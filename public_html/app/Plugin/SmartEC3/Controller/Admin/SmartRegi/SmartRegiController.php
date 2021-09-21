<?php

namespace Plugin\SmartEC3\Controller\Admin\SmartRegi;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;
use Plugin\SmartEC3\Event\SmartRegiEvents;

use Eccube\Entity\Product;

use Eccube\Controller\AbstractController;
use Plugin\SmartEC3\Entity\Config;
use Plugin\SmartEC3\Entity\SmartRegi;
use Plugin\SmartEC3\Entity\SmartRegiImage;
use Plugin\SmartEC3\Entity\Master\SmartRegiStore;
use Plugin\SmartEC3\Form\Type\Admin\SmartRegi\SmartRegiType;
use Plugin\SmartEC3\Repository\ConfigRepository;
use Plugin\SmartEC3\Repository\SmartRegiRepository;
use Plugin\SmartEC3\Repository\SmartRegiImageRepository;

use Eccube\Util\CacheUtil;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\CategoryRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class SmartRegiController extends AbstractController
{
    /**
     * @var SmartRegiRepository
     */
    protected $smartRegiRepository;

    /**
     * @var SmartRegiImageRepository
     */
    protected $smartRegiImageRepository;

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SmartRegiController constructor.
     *
     * @param SmartRegiRepository $smartRegiRepository
     * @param ConfigRepository $configRepository
     * @param SmartRegiImageRepository $smartRegiImageRepository
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        SmartRegiRepository $smartRegiRepository,
        ConfigRepository $configRepository,
        SmartRegiImageRepository $smartRegiImageRepository,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ){
        $this->smartRegiRepository = $smartRegiRepository;
        $this->configRepository = $configRepository;
        $this->smartRegiImageRepository = $smartRegiImageRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/smart_ec3/callback", name="admin_product_smartregi_callback")
     */
    public function apiCallback(Request $request)
    {
        return true;
    }

    /**
     * @Route("/%eccube_admin_route%/smart_ec3/{id}/load", name="admin_product_smartregi_load", methods={"GET"}, requirements={"id" = "\d+"})
     * @Template("@SmartEC3/admin/Product/smart_popup.twig")
     * @ParamConverter("Product")
     */
    public function loadProductSmartRegi(Request $request, Product $Product)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        /** @var $Product ProductRepository */
        if (!$Product) {
            throw new NotFoundHttpException();
        }

        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);

        return [
            'SmartRegi' => $SmartRegi,
        ];
    }


    /**
     * @Route("/%eccube_admin_route%/smart_ec3/{id}/new", requirements={"id" = "\d+"}, name="smart_ec3_admin_smartregi_new")
     * @Route("/%eccube_admin_route%/smart_ec3/{id}/edit", requirements={"id" = "\d+"}, name="smart_ec3_admin_smartregi_edit")
     * @Template("@SmartEC3/admin/SmartRegi/smart_edit.twig")
     */
    public function edit(Request $request, $id = null, RouterInterface $router, CacheUtil $cacheUtil){

        //$Product = $this->productRepository->find($id);
        $Product = $this->fetchFullProduct($id);

        if (!$Product) {
            throw new NotFoundHttpException();
        }

        // Check if config complete
        $Config = $this->configRepository->find(1);
        if ('POST' != $request->getMethod()){
            if($Config) {
                if(!$Config->checkConfigComplete()){
                    $this->addError('plg.smartec3.config.not_complete', 'admin');
                }
            }else{
                $this->addError('plg.smartec3.config.not_complete', 'admin');
            }
        }

        $oldStoreType = null;
        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);
        if (!$SmartRegi) {
            $SmartRegi = new SmartRegi();
        }else{
            $oldStoreType = $SmartRegi->getStoreType();
        } 

        $builder = $this->formFactory->createBuilder(SmartRegiType::class, $SmartRegi);
        // Event goes here in between
        $form = $builder->getForm();

        // ファイルの登録
        $images = [];
        $SmartImages = $SmartRegi->getSmartRegiImage();
        foreach ($SmartImages as $SmartImage) {
            $images[] = $SmartImage->getFileName();
        }
        $form['images']->setData($images);

        // 消費税率
        $Tax = $SmartRegi->getTax();
        $form['tax']->setData($Tax);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {    

                $SmartRegi = $form->getData();

                // 画像の登録
                $add_images = $form->get('add_images')->getData();
                foreach ($add_images as $add_image) {
                    $SmartRegiImage = new \Plugin\SmartEC3\Entity\SmartRegiImage();
                    $SmartRegiImage
                        ->setFileName($add_image)
                        ->setSmartRegi($SmartRegi)
                        ->setSortNo(1);
                    $SmartRegi->addSmartRegiImage($SmartRegiImage);
                    $this->entityManager->persist($SmartRegiImage);

                    // 移動
                    $file = new File($this->eccubeConfig['eccube_temp_image_dir'].'/'.$add_image);
                    $file->move($this->eccubeConfig['eccube_save_image_dir']);
                }

                // 画像の削除
                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $SmartRegiImage = $this->smartRegiImageRepository
                        ->findOneBy(['file_name' => $delete_image]);

                    // 追加してすぐに削除した画像は、Entityに追加されない
                    if ($SmartRegiImage instanceof SmartRegiImage) {
                        $SmartRegi->removeSmartRegiImage($SmartRegiImage);
                        $this->entityManager->remove($SmartRegiImage);
                    }
                    $this->entityManager->persist($SmartRegi);

                    // 削除
                    $fs = new Filesystem();
                    $fs->remove($this->eccubeConfig['eccube_save_image_dir'].'/'.$delete_image);
                }
                $this->entityManager->persist($SmartRegi);
                $this->entityManager->flush($SmartRegi);

                // Sorting
                $sortNos = $request->get('sort_no_images');
                if ($sortNos) {
                    foreach ($sortNos as $sortNo) {
                        list($filename, $sortNo_val) = explode('//', $sortNo);
                        $SmartRegiImage = $this->smartRegiImageRepository
                            ->findOneBy([
                                'file_name' => $filename,
                                'SmartRegi' => $SmartRegi,
                            ]);
                        $SmartRegiImage->setSortNo($sortNo_val);
                        $this->entityManager->persist($SmartRegiImage);
                    }
                }
                $this->entityManager->flush($SmartRegi);

                // 商品登録と更新日
                $SmartRegi->setProduct($Product);
                $SmartRegi->setUpdateDate(new \DateTime());
                
                $this->entityManager->persist($SmartRegi);
                $this->entityManager->flush($SmartRegi);


                $newStoreType = $form->get('store_type')->getData();
                // Change from both systems to EC only
                if ($oldStoreType != null && $newStoreType != $oldStoreType && $newStoreType->getId() == SmartRegiStore::EC_ONLY){
                
                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'id' => $id,
                            'ProductClass' => $Product->getProductClasses()
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(SmartRegiEvents::PLG_SMARTREGI_ADMIM_SMART_EDIT_STORE_CHANGE, $event);

                }else{

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'Product' => $Product
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(SmartRegiEvents::PLG_SMARTREGI_ADMIM_SMART_EDIT_COMPLETE, $event);
                }

                $this->addSuccess('登録しました。', 'admin');

                if ($returnLink = $form->get('return_link')->getData()) {
                    try {
                        // $returnLinkはpathの形式で渡される. pathが存在するかをルータでチェックする.
                        $pattern = '/^'.preg_quote($request->getBasePath(), '/').'/';
                        $returnLink = preg_replace($pattern, '', $returnLink);
                        $result = $router->match($returnLink);
                        // パラメータのみ抽出
                        $params = array_filter($result, function ($key) {
                            return 0 !== \strpos($key, '_');
                        }, ARRAY_FILTER_USE_KEY);

                        // pathからurlを再構築してリダイレクト.
                        return $this->redirectToRoute($result['_route'], $params);
                    } catch (\Exception $e) {
                        // マッチしない場合はログ出力してスキップ.
                        log_warning('URLの形式が不正です。');
                    }
                }

                $cacheUtil->clearDoctrineCache();

                return $this->redirectToRoute('smart_ec3_admin_smartregi_edit', ['id' => $Product->getId()]);

            }
        }

        //------------------------------------------------------------------------------------------
        
        // Use any function inside the Service like this instead of throwing events if desired :
        // $SmartRegiService = $this->get('smartec3.smart_regi_service');
        // $testMsg = $SmartRegiService->helloWorld('Hello people');
        // $this->addWarning($testMsg, 'admin');

        //------------------------------------------------------------------------------------------

        return [
            'form' => $form->createView(),
            'Product' => $Product,
        ];
    }

    public function fetchFullProduct(int $productId){

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p') 
            ->from('Eccube\\Entity\\Product', 'p')
            ->addSelect(['pct','pc', 'ps', 'cc1', 'cc2', 'pi', 'pt'])
            ->innerJoin('p.ProductClasses', 'pc')
            ->innerJoin('pc.ProductStock', 'ps')
            ->leftJoin('p.ProductCategories', 'pct')
            ->leftJoin('pc.ClassCategory1', 'cc1')
            ->leftJoin('pc.ClassCategory2', 'cc2')
            ->leftJoin('p.ProductImage', 'pi')
            ->leftJoin('p.ProductTag', 'pt')
            ->where('p.id = :id')
            ->andWhere('pc.visible = :visible')
            ->setParameter('id', $productId)
            ->setParameter('visible', true);

        $product = $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $product;

    }

    /**
     * @Route("/%eccube_admin_route%/smart_ec3/image/add", name="admin_smart_image_add", methods={"POST"})
     */
    public function addImage(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $images = $request->files->get('admin_smartregi');

        $allowExtensions = ['gif', 'jpg', 'jpeg', 'png'];
        $maxHeight = 260;
        $maxWidth = 260;
        $files = [];
        if (count($images) > 0) {
            foreach ($images as $img) {
                foreach ($img as $image) {

                    // File Size
                    list($height, $width) = getimagesize($image);
                    if($height > $maxHeight || $width > $maxWidth)
                        return $this->json(['smartimage_upload' => 'File dimensions too big'], 500);

                    //ファイルフォーマット検証
                    $mimeType = $image->getMimeType();
                    if (0 !== strpos($mimeType, 'image')) {
                        throw new UnsupportedMediaTypeHttpException();
                    }

                    // 拡張子
                    $extension = $image->getClientOriginalExtension();
                    if (!in_array(strtolower($extension), $allowExtensions)) {
                        throw new UnsupportedMediaTypeHttpException();
                    }

                    $filename = date('mdHis').uniqid('_').'.'.$extension;
                    $image->move($this->eccubeConfig['eccube_temp_image_dir'], $filename);
                    $files[] = $filename;
                }
            }
        }

        return $this->json(['files' => $files], 200);
    }

    /**
     * @Route("/%eccube_admin_route%/smart_ec3/product/all", name="admin_smart_product_all")
     */
    public function productAll(Request $request)
    {

        $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin(array());
        $AuxProducts = $qb->getQuery()->getResult();

        foreach($AuxProducts as $AuxProduct){

            $SmartRegi = $this->smartRegiRepository->getFromProduct($AuxProduct);
            
            if($SmartRegi != null){


                $id = $AuxProduct->getId();
                $Product = $this->fetchFullProduct($id);

                $event = new EventArgs(
                    [
                        'Product' => $Product,
                    ],
                    $request
                );

                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE, $event);
            }
        }

        return $this->redirectToRoute('admin_product');
    }

}
