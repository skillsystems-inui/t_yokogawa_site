<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Master\ProductListMaxType;
use Eccube\Form\Type\Master\ProductListOrderByType;
use Eccube\Form\Type\SearchProductType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\Master\ProductListMaxRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ProductController extends AbstractController
{
    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @var CustomerFavoriteProductRepository
     */
    protected $customerFavoriteProductRepository;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var AuthenticationUtils
     */
    protected $helper;

    /**
     * @var ProductListMaxRepository
     */
    protected $productListMaxRepository;

    private $title = '';

    /**
     * ProductController constructor.
     *
     * @param PurchaseFlow $cartPurchaseFlow
     * @param CustomerFavoriteProductRepository $customerFavoriteProductRepository
     * @param CartService $cartService
     * @param ProductRepository $productRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param AuthenticationUtils $helper
     * @param ProductListMaxRepository $productListMaxRepository
     */
    public function __construct(
        PurchaseFlow $cartPurchaseFlow,
        CustomerFavoriteProductRepository $customerFavoriteProductRepository,
        CartService $cartService,
        ProductRepository $productRepository,
        BaseInfoRepository $baseInfoRepository,
        AuthenticationUtils $helper,
        ProductListMaxRepository $productListMaxRepository
    ) {
        $this->purchaseFlow = $cartPurchaseFlow;
        $this->customerFavoriteProductRepository = $customerFavoriteProductRepository;
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->helper = $helper;
        $this->productListMaxRepository = $productListMaxRepository;
    }

    /**
     * 商品一覧画面.
     *
     * @Route("/products/list", name="product_list")
     * @Template("Product/list.twig")
     */
    public function index(Request $request, Paginator $paginator)
    {
        // Doctrine SQLFilter
        if ($this->BaseInfo->isOptionNostockHidden()) {
            $this->entityManager->getFilters()->enable('option_nostock_hidden');
        }

        // handleRequestは空のqueryの場合は無視するため
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', ''));
        }

        // searchForm
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createNamedBuilder('', SearchProductType::class);

        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE, $event);

        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();

        $searchForm->handleRequest($request);

        // paginator
        $searchData = $searchForm->getData();
        $qb = $this->productRepository->getQueryBuilderBySearchData($searchData);

        $event = new EventArgs(
            [
                'searchData' => $searchData,
                'qb' => $qb,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH, $event);
        $searchData = $event->getArgument('searchData');

        $query = $qb->getQuery()
            ->useResultCache(true, $this->eccubeConfig['eccube_result_cache_lifetime_short']);

        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $query,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            !empty($searchData['disp_number']) ? $searchData['disp_number']->getId() : $this->productListMaxRepository->findOneBy([], ['sort_no' => 'ASC'])->getId()
        );

        $ids = [];
        foreach ($pagination as $Product) {
            $ids[] = $Product->getId();
        }
        $ProductsAndClassCategories = $this->productRepository->findProductsWithSortedClassCategories($ids, 'p.id');

        // addCart form
        $forms = [];
        foreach ($pagination as $Product) {
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $this->formFactory->createNamedBuilder(
                '',
                AddCartType::class,
                null,
                [
                    'product' => $ProductsAndClassCategories[$Product->getId()],
                    'allow_extra_fields' => true,
                ]
            );
            $addCartForm = $builder->getForm();

            $forms[$Product->getId()] = $addCartForm->createView();
        }

        // 表示件数
        $builder = $this->formFactory->createNamedBuilder(
            'disp_number',
            ProductListMaxType::class,
            null,
            [
                'required' => false,
                'allow_extra_fields' => true,
            ]
        );
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_DISP, $event);

        $dispNumberForm = $builder->getForm();

        $dispNumberForm->handleRequest($request);

        // ソート順
        $builder = $this->formFactory->createNamedBuilder(
            'orderby',
            ProductListOrderByType::class,
            null,
            [
                'required' => false,
                'allow_extra_fields' => true,
            ]
        );
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_ORDER, $event);

        $orderByForm = $builder->getForm();

        $orderByForm->handleRequest($request);

        $Category = $searchForm->get('category_id')->getData();

        return [
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
            'disp_number_form' => $dispNumberForm->createView(),
            'order_by_form' => $orderByForm->createView(),
            'forms' => $forms,
            'Category' => $Category,
        ];
    }

    /**
     * 商品詳細画面.
     *
     * @Route("/products/detail/{id}", name="product_detail", methods={"GET"}, requirements={"id" = "\d+"})
     * @Template("Product/detail.twig")
     * @ParamConverter("Product", options={"repository_method" = "findWithSortedClassCategories"})
     *
     * @param Request $request
     * @param Product $Product
     *
     * @return array
     */
    public function detail(Request $request, Product $Product)
    {
        if (!$this->checkVisibility($Product)) {
            throw new NotFoundHttpException();
        }

        $builder = $this->formFactory->createNamedBuilder(
            '',
            AddCartType::class,
            null,
            [
                'product' => $Product,
                'id_add_product_id' => false,
            ]
        );

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE, $event);

        $is_favorite = false;
        if ($this->isGranted('ROLE_USER')) {
            $Customer = $this->getUser();
            $is_favorite = $this->customerFavoriteProductRepository->isFavorite($Customer, $Product);
        }

        return [
            'title' => $this->title,
            'subtitle' => $Product->getName(),
            'form' => $builder->getForm()->createView(),
            'Product' => $Product,
            'is_favorite' => $is_favorite,
        ];
    }

    /**
     * お気に入り追加.
     *
     * @Route("/products/add_favorite/{id}", name="product_add_favorite", requirements={"id" = "\d+"})
     */
    public function addFavorite(Request $request, Product $Product)
    {
        $this->checkVisibility($Product);

        $event = new EventArgs(
            [
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_FAVORITE_ADD_INITIALIZE, $event);

        if ($this->isGranted('ROLE_USER')) {
            $Customer = $this->getUser();
            $this->customerFavoriteProductRepository->addFavorite($Customer, $Product);
            $this->session->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());

            $event = new EventArgs(
                [
                    'Product' => $Product,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_FAVORITE_ADD_COMPLETE, $event);

            return $this->redirectToRoute('product_detail', ['id' => $Product->getId()]);
        } else {
            // 非会員の場合、ログイン画面を表示
            //  ログイン後の画面遷移先を設定
            $this->setLoginTargetPath($this->generateUrl('product_add_favorite', ['id' => $Product->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
            $this->session->getFlashBag()->set('eccube.add.favorite', true);

            $event = new EventArgs(
                [
                    'Product' => $Product,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_FAVORITE_ADD_COMPLETE, $event);

            return $this->redirectToRoute('mypage_login');
        }
    }

    /**
     * カートに追加.
     *
     * @Route("/products/add_cart/{id}", name="product_add_cart", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function addCart(Request $request, Product $Product)
    {
        
        // エラーメッセージの配列
        $errorMessages = [];
        if (!$this->checkVisibility($Product)) {
            throw new NotFoundHttpException();
        }
        
        $builder = $this->formFactory->createNamedBuilder(
            '',
            AddCartType::class,
            null,
            [
                'product' => $Product,
                'id_add_product_id' => false,
            ]
        );
        
        $event = new EventArgs(
            [
                'builder' => $builder,
                'Product' => $Product,
            ],
            $request
        );
        
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_CART_ADD_INITIALIZE, $event);
        
        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        $form->handleRequest($request);

        if (!$form->isValid()) {
            throw new NotFoundHttpException();
        }
        
        $addCartData = $form->getData();
        
        log_info(
            'カート追加処理開始',
            [
                'product_id' => $Product->getId(),
                'product_class_id' => $addCartData['product_class_id'],
                'quantity' => $addCartData['quantity'],
            ]
        );
        
        
        //選択オプション1
        $optionClassCategoryId1 = null;
        if (!is_null($Product->getOptionName1())) {
	        $optionClassCategoryId1 = $form->get('optioncategory_id1')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId1 == '__unselected'){
	        	$optionClassCategoryId1 = null;
	        }
        }

        //選択オプション2
        $optionClassCategoryId2 = null;
        if (!is_null($Product->getOptionName2())) {
	        $optionClassCategoryId2 = $form->get('optioncategory_id2')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId2 == '__unselected'){
	        	$optionClassCategoryId2 = null;
	        }
        }
        //選択オプション3
        $optionClassCategoryId3 = null;
        if (!is_null($Product->getOptionName3())) {
	        $optionClassCategoryId3 = $form->get('optioncategory_id3')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId3 == '__unselected'){
	        	$optionClassCategoryId3 = null;
	        }
        }
        //選択オプション4
        $optionClassCategoryId4 = null;
        if (!is_null($Product->getOptionName4())) {
	        $optionClassCategoryId4 = $form->get('optioncategory_id4')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId4 == '__unselected'){
	        	$optionClassCategoryId4 = null;
	        }
        }
        //選択オプション5
        $optionClassCategoryId5 = null;
        if (!is_null($Product->getOptionName5())) {
	        $optionClassCategoryId5 = $form->get('optioncategory_id5')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId5 == '__unselected'){
	        	$optionClassCategoryId5 = null;
	        }
        }
        //選択オプション6
        $optionClassCategoryId6 = null;
        if (!is_null($Product->getOptionName6())) {
	        $optionClassCategoryId6 = $form->get('optioncategory_id6')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId6 == '__unselected'){
	        	$optionClassCategoryId6 = null;
	        }
        }
        //選択オプション7
        $optionClassCategoryId7 = null;
        if (!is_null($Product->getOptionName7())) {
	        $optionClassCategoryId7 = $form->get('optioncategory_id7')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId7 == '__unselected'){
	        	$optionClassCategoryId7 = null;
	        }
        }
        //選択オプション8
        $optionClassCategoryId8 = null;
        if (!is_null($Product->getOptionName8())) {
	        $optionClassCategoryId8 = $form->get('optioncategory_id8')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId8 == '__unselected'){
	        	$optionClassCategoryId8 = null;
	        }
        }
        //選択オプション9
        $optionClassCategoryId9 = null;
        if (!is_null($Product->getOptionName9())) {
	        $optionClassCategoryId9 = $form->get('optioncategory_id9')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId9 == '__unselected'){
	        	$optionClassCategoryId9 = null;
	        }
        }
        //選択オプション10
        $optionClassCategoryId10 = null;
        if (!is_null($Product->getOptionName10())) {
	        $optionClassCategoryId10 = $form->get('optioncategory_id10')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId10 == '__unselected'){
	        	$optionClassCategoryId10 = null;
	        }
        }
        
        //選択オプション11
        $optionClassCategoryId11 = null;
        if (!is_null($Product->getOptionName11())) {
	        $optionClassCategoryId11 = $form->get('optioncategory_id11')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId11 == '__unselected'){
	        	$optionClassCategoryId11 = null;
	        }
        }

        //選択オプション12
        $optionClassCategoryId12 = null;
        if (!is_null($Product->getOptionName12())) {
	        $optionClassCategoryId12 = $form->get('optioncategory_id12')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId12 == '__unselected'){
	        	$optionClassCategoryId12 = null;
	        }
        }
        //選択オプション13
        $optionClassCategoryId13 = null;
        if (!is_null($Product->getOptionName13())) {
	        $optionClassCategoryId13 = $form->get('optioncategory_id13')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId13 == '__unselected'){
	        	$optionClassCategoryId13 = null;
	        }
        }
        //選択オプション14
        $optionClassCategoryId14 = null;
        if (!is_null($Product->getOptionName14())) {
	        $optionClassCategoryId14 = $form->get('optioncategory_id14')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId14 == '__unselected'){
	        	$optionClassCategoryId14 = null;
	        }
        }
        //選択オプション15
        $optionClassCategoryId15 = null;
        if (!is_null($Product->getOptionName15())) {
	        $optionClassCategoryId15 = $form->get('optioncategory_id15')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId15 == '__unselected'){
	        	$optionClassCategoryId15 = null;
	        }
        }
        //選択オプション16
        $optionClassCategoryId16 = null;
        if (!is_null($Product->getOptionName16())) {
	        $optionClassCategoryId16 = $form->get('optioncategory_id16')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId16 == '__unselected'){
	        	$optionClassCategoryId16 = null;
	        }
        }
        //選択オプション17
        $optionClassCategoryId17 = null;
        if (!is_null($Product->getOptionName17())) {
	        $optionClassCategoryId17 = $form->get('optioncategory_id17')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId17 == '__unselected'){
	        	$optionClassCategoryId17 = null;
	        }
        }
        //選択オプション18
        $optionClassCategoryId18 = null;
        if (!is_null($Product->getOptionName18())) {
	        $optionClassCategoryId18 = $form->get('optioncategory_id18')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId18 == '__unselected'){
	        	$optionClassCategoryId18 = null;
	        }
        }
        //選択オプション19
        $optionClassCategoryId19 = null;
        if (!is_null($Product->getOptionName19())) {
	        $optionClassCategoryId19 = $form->get('optioncategory_id19')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId19 == '__unselected'){
	        	$optionClassCategoryId19 = null;
	        }
        }
        //選択オプション20
        $optionClassCategoryId20 = null;
        if (!is_null($Product->getOptionName20())) {
	        $optionClassCategoryId20 = $form->get('optioncategory_id20')->getData();
	        // 未選択の場合nullセット
	        if($optionClassCategoryId20 == '__unselected'){
	        	$optionClassCategoryId20 = null;
	        }
        }
        
        
        //名付け(プレート)
        $printname_plate = $form->get('printname_plate')->getData();
        
        //名付け(熨斗)
        $printname_noshi = $form->get('printname_noshi')->getData();
        
        //オプションによる追加価格
        $additional_option_price = $form->get('additional_price')->getData();
        
        //オプション選択されていればカートに反映する
        $this->cartService->addProductOption($addCartData['product_class_id'], 
                                             $optionClassCategoryId1, 
                                             $optionClassCategoryId2, 
                                             $optionClassCategoryId3, 
                                             $optionClassCategoryId4, 
                                             $optionClassCategoryId5, 
                                             $optionClassCategoryId6, 
                                             $optionClassCategoryId7, 
                                             $optionClassCategoryId8, 
                                             $optionClassCategoryId9, 
                                             $optionClassCategoryId10, 
                                             $optionClassCategoryId11, 
                                             $optionClassCategoryId12, 
                                             $optionClassCategoryId13, 
                                             $optionClassCategoryId14, 
                                             $optionClassCategoryId15, 
                                             $optionClassCategoryId16, 
                                             $optionClassCategoryId17, 
                                             $optionClassCategoryId18, 
                                             $optionClassCategoryId19, 
                                             $optionClassCategoryId20, 
                                             $printname_plate,
                                             $printname_noshi,
                                             $additional_option_price,
                                             $addCartData['quantity']);
        //$this->cartService->addProduct($addCartData['product_class_id'], $addCartData['quantity']);

        // 明細の正規化
        $Carts = $this->cartService->getCarts();
        foreach ($Carts as $Cart) {
            $result = $this->purchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
            // 復旧不可のエラーが発生した場合は追加した明細を削除.
            if ($result->hasError()) {
                $this->cartService->removeProduct($addCartData['product_class_id']);
                foreach ($result->getErrors() as $error) {
                    $errorMessages[] = $error->getMessage();
                }
            }
            foreach ($result->getWarning() as $warning) {
                $errorMessages[] = $warning->getMessage();
            }
        }

        $this->cartService->save();

        log_info(
            'カート追加処理完了',
            [
                'product_id' => $Product->getId(),
                'product_class_id' => $addCartData['product_class_id'],
                'quantity' => $addCartData['quantity'],
            ]
        );

        $event = new EventArgs(
            [
                'form' => $form,
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_PRODUCT_CART_ADD_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        if ($request->isXmlHttpRequest()) {
            // ajaxでのリクエストの場合は結果をjson形式で返す。

            // 初期化
            $done = null;
            $messages = [];

            if (empty($errorMessages)) {
                // エラーが発生していない場合
                $done = true;
                array_push($messages, trans('front.product.add_cart_complete'));
            } else {
                // エラーが発生している場合
                $done = false;
                $messages = $errorMessages;
            }

            return $this->json(['done' => $done, 'messages' => $messages]);
        } else {
            // ajax以外でのリクエストの場合はカート画面へリダイレクト
            foreach ($errorMessages as $errorMessage) {
                $this->addRequestError($errorMessage);
            }

            return $this->redirectToRoute('cart');
        }
    }

    /**
     * ページタイトルの設定
     *
     * @param  null|array $searchData
     *
     * @return str
     */
    protected function getPageTitle($searchData)
    {
        if (isset($searchData['name']) && !empty($searchData['name'])) {
            return trans('front.product.search_result');
        } elseif (isset($searchData['category_id']) && $searchData['category_id']) {
            return $searchData['category_id']->getName();
        } else {
            return trans('front.product.all_products');
        }
    }

    /**
     * 閲覧可能な商品かどうかを判定
     *
     * @param Product $Product
     *
     * @return boolean 閲覧可能な場合はtrue
     */
    protected function checkVisibility(Product $Product)
    {
        $is_admin = $this->session->has('_security_admin');

        // 管理ユーザの場合はステータスやオプションにかかわらず閲覧可能.
        if (!$is_admin) {
            // 在庫なし商品の非表示オプションが有効な場合.
            // if ($this->BaseInfo->isOptionNostockHidden()) {
            //     if (!$Product->getStockFind()) {
            //         return false;
            //     }
            // }
            // 公開ステータスでない商品は表示しない.
            if ($Product->getStatus()->getId() !== ProductStatus::DISPLAY_SHOW) {
                return false;
            }
        }

        return true;
    }
}
