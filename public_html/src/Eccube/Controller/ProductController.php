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
     * ??????????????????.
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

        // handleRequest?????????query??????????????????????????????
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

        // ????????????
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

        // ????????????
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
     * ??????????????????.
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
     * ?????????????????????.
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
            // ????????????????????????????????????????????????
            //  ??????????????????????????????????????????
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
     * ??????????????????.
     *
     * @Route("/products/add_cart/{id}", name="product_add_cart", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function addCart(Request $request, Product $Product)
    {
        
        // ?????????????????????????????????
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
            '???????????????????????????',
            [
                'product_id' => $Product->getId(),
                'product_class_id' => $addCartData['product_class_id'],
                'quantity' => $addCartData['quantity'],
            ]
        );
        
        //?????????????????????
        $option_detail = $form->get('option_detail')->getData();
        //?????????????????? 0204???
        $option_candle_dai_num = $form->get('option_candle_dai_num')->getData();
        $option_candle_syo_num = $form->get('option_candle_syo_num')->getData();
        $option_candle_no1_num = $form->get('option_candle_no1_num')->getData();
        $option_candle_no2_num = $form->get('option_candle_no2_num')->getData();
        $option_candle_no3_num = $form->get('option_candle_no3_num')->getData();
        $option_candle_no4_num = $form->get('option_candle_no4_num')->getData();
        $option_candle_no5_num = $form->get('option_candle_no5_num')->getData();
        $option_candle_no6_num = $form->get('option_candle_no6_num')->getData();
        $option_candle_no7_num = $form->get('option_candle_no7_num')->getData();
        $option_candle_no8_num = $form->get('option_candle_no8_num')->getData();
        $option_candle_no9_num = $form->get('option_candle_no9_num')->getData();
        $option_candle_no0_num = $form->get('option_candle_no0_num')->getData();
        $option_printname_plate1 = $form->get('option_printname_plate1')->getData();
        $option_printname_plate2 = $form->get('option_printname_plate2')->getData();
        $option_printname_plate3 = $form->get('option_printname_plate3')->getData();
        $option_printname_plate4 = $form->get('option_printname_plate4')->getData();
        $option_printname_plate5 = $form->get('option_printname_plate5')->getData();
        $option_deco_ichigo_chk     = $form->get('option_deco_ichigo_chk')->getData();
        $option_deco_fruit_chk      = $form->get('option_deco_fruit_chk')->getData();
        $option_deco_namachoco_chk  = $form->get('option_deco_namachoco_chk')->getData();
        $option_deco_echoco_chk     = $form->get('option_deco_echoco_chk')->getData();
        $option_pori_cyu_chk      = $form->get('option_pori_cyu_chk')->getData();
        $option_pori_dai_chk      = $form->get('option_pori_dai_chk')->getData();
        $option_pori_tokudai_chk  = $form->get('option_pori_tokudai_chk')->getData();
        $option_housou_sentaku    = $form->get('option_housou_sentaku')->getData();
        $option_noshi_kakekata    = $form->get('option_noshi_kakekata')->getData();
        $option_kakehousou_syurui = $form->get('option_kakehousou_syurui')->getData();
        $option_uwagaki_sentaku   = $form->get('option_uwagaki_sentaku')->getData();
        $option_printname_nosina  = $form->get('option_printname_nosina')->getData();
        $option_plate_sentaku  = $form->get('option_plate_sentaku')->getData();
        //?????????????????? 0204???
        
        //????????????????????????????????????
        $additional_option_price = $form->get('additional_price')->getData();
        
        //ToDo 01 ?????????????????????(?????????)???????????????????????????(???????????????)
        //ToDo 02 ?????????????????????????????????????????????(??????????????????????????????????????????)
        //        ???No???????????????
        //        ??????????????????????????????(2????????????)
        //        ??????????????????????????????(?????????UP)
        //        ??????????????????????????????(????????????UP)
        //        ??????????????????????????????(????????????UP)
        //        ??????????????????????????????(????????????(4???))
        //        ???????????????
        //        ???????????????
        //        ??????????????????
        //???????????????????????????????????????????????????????????????
        $this->cartService->addProductOption($addCartData['product_class_id'], 
                                             $option_detail,
                                             $option_candle_dai_num,
                                             $option_candle_syo_num,
                                             $option_candle_no1_num,
                                             $option_candle_no2_num,
                                             $option_candle_no3_num,
                                             $option_candle_no4_num,
                                             $option_candle_no5_num,
                                             $option_candle_no6_num,
                                             $option_candle_no7_num,
                                             $option_candle_no8_num,
                                             $option_candle_no9_num,
                                             $option_candle_no0_num,
                                             $option_printname_plate1,
                                             $option_printname_plate2,
                                             $option_printname_plate3,
                                             $option_printname_plate4,
                                             $option_printname_plate5,
                                             $option_deco_ichigo_chk,
                                             $option_deco_fruit_chk,
                                             $option_deco_namachoco_chk,
                                             $option_deco_echoco_chk,
                                             $option_pori_cyu_chk,
                                             $option_pori_dai_chk,
                                             $option_pori_tokudai_chk,
                                             $option_housou_sentaku,
                                             $option_noshi_kakekata,
                                             $option_kakehousou_syurui,
                                             $option_uwagaki_sentaku,
                                             $option_printname_nosina,
                                             $option_plate_sentaku,
                                             $additional_option_price,
                                             $addCartData['quantity']);
        //$this->cartService->addProduct($addCartData['product_class_id'], $addCartData['quantity']);

        // ??????????????????
        $Carts = $this->cartService->getCarts();
        foreach ($Carts as $Cart) {
            $result = $this->purchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
            // ???????????????????????????????????????????????????????????????????????????.
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
            '???????????????????????????',
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
            // ajax??????????????????????????????????????????json??????????????????

            // ?????????
            $done = null;
            $messages = [];

            if (empty($errorMessages)) {
                // ???????????????????????????????????????
                $done = true;
                array_push($messages, trans('front.product.add_cart_complete'));
            } else {
                // ????????????????????????????????????
                $done = false;
                $messages = $errorMessages;
            }

            return $this->json(['done' => $done, 'messages' => $messages]);
        } else {
            // ajax???????????????????????????????????????????????????????????????????????????
            foreach ($errorMessages as $errorMessage) {
                $this->addRequestError($errorMessage);
            }

            return $this->redirectToRoute('cart');
        }
    }

    /**
     * ??????????????????????????????
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
     * ??????????????????????????????????????????
     *
     * @param Product $Product
     *
     * @return boolean ????????????????????????true
     */
    protected function checkVisibility(Product $Product)
    {
        $is_admin = $this->session->has('_security_admin');

        // ??????????????????????????????????????????????????????????????????????????????????????????.
        if (!$is_admin) {
            // ???????????????????????????????????????????????????????????????.
            // if ($this->BaseInfo->isOptionNostockHidden()) {
            //     if (!$Product->getStockFind()) {
            //         return false;
            //     }
            // }
            // ??????????????????????????????????????????????????????.
            if ($Product->getStatus()->getId() !== ProductStatus::DISPLAY_SHOW) {
                return false;
            }
        }

        return true;
    }
}
