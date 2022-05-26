<?php

namespace Plugin\SmartEC3\Controller\Order;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;

use Plugin\SmartEC3\Repository\ConfigRepository;
use Plugin\SmartEC3\Repository\SmartRegiRepository;

use Eccube\Controller\AbstractController;

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\ProductStatus;

use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductStockRepository;
use Eccube\Repository\PointHistoryRepository;

use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\TaxTypeRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\Master\SalesTypeRepository;
use Eccube\Repository\Master\ProductStatusRepository;

use Eccube\Service\TaxRuleService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class SmartRegiProductController extends AbstractController
{

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var SmartRegiRepository
     */
    protected $smartRegiRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * @var PointHistoryRepository
     */
    protected $pointHistoryRepository;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;
    
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var SaleTypeRepository
     */
    protected $saleTypeRepository;

    /**
     * @var SalesTypeRepository
     */
    protected $salesTypeRepository;


    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    
    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;
    
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    /**
     * @var TaxTypeRepository
     */
    private $taxTypeRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var TaxRuleService
     */
    private $taxRuleService;
    
    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;
    

    /**
     * SmartRegiController constructor.
     *
     * @param ConfigRepository $configRepository
     * @param SmartRegiRepository $smartRegiRepository
     * @param ClassCategoryRepository $classCategoryRepository
     * @param CustomerRepository $customerRepository
     * @param DeliveryRepository $deliveryRepository
     * @param PrefRepository $prefRepository
     * @param ProductStockRepository $productStockRepository
     * @param ProductClassRepository $productClassRepository
     * @param ProductRepository $productRepository
     * @param SaleTypeRepository $saleTypeRepository
     * @param SalesTypeRepository $salesTypeRepository
     * @param PointHistoryRepository $pointHistoryRepository
     * @param OrderRepository $orderRepository
     * @param OrderItemRepository $orderItemRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param TaxTypeRepository $taxTypeRepository
     * @param PaymentRepository $paymentRepository
     * @param TaxRuleService $taxRuleService
     * @param ProductStatusRepository $productStatusRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        SmartRegiRepository $smartRegiRepository,
        ClassCategoryRepository $classCategoryRepository,
        CustomerRepository $customerRepository,
        DeliveryRepository $deliveryRepository,
        PrefRepository $prefRepository,
        ProductStockRepository $productStockRepository,
        ProductClassRepository $productClassRepository,
        ProductRepository $productRepository,
        SaleTypeRepository $saleTypeRepository,
        SalesTypeRepository $salesTypeRepository,
        PointHistoryRepository $pointHistoryRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderItemTypeRepository $orderItemTypeRepository,
        TaxTypeRepository $taxTypeRepository,
        PaymentRepository $paymentRepository,
        TaxRuleService $taxRuleService,
        ProductStatusRepository $productStatusRepository
    ){
        $this->configRepository = $configRepository;
        $this->smartRegiRepository = $smartRegiRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->customerRepository = $customerRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->prefRepository = $prefRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productClassRepository = $productClassRepository;
        $this->productRepository = $productRepository;
        $this->saleTypeRepository = $saleTypeRepository;
        $this->salesTypeRepository = $salesTypeRepository;
        $this->pointHistoryRepository = $pointHistoryRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->taxTypeRepository = $taxTypeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->taxRuleService = $taxRuleService;
        $this->productStatusRepository = $productStatusRepository;
    }

    /**
     * @Route("/smart_ec3/order/connect2", name="smart_product_connect")
     */
    public function productConnect(Request $request)
    {
        $this->logProductInfo($request);
        $post = $request->request->all();
        $info = json_decode($post["params"]);
        
        foreach($info->data as $key => $value){
            
            if($value->table_name == "Product"){
                switch ($value->table_name) {
                    case 'Product':
                        $this->createProductInfo($value);
                        break;
                    default:
                        break;
                }
            }
        }
        
        return $this->json(['Result9' => true], 200);

    }


    //商品情報登録
    public function createProductInfo($value){
    	
    	$this->debugLog("\n".'----- START -----');
    	$this->debugLog(date("Y-m-d H:i:s"));//処理日時出力
    	
    	
    	//商品クラスのIDを指定するための設定
    	$metadata = $this->entityManager->getClassMetaData(\Eccube\Entity\ProductClass::class);
    	$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
    	$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    	
    	//スマレジ項目との調整用プロパティ
    	$Config = $this->configRepository->find(1);
        $p_offset = $Config->getProductOffset();
        $c_offset = $Config->getCategoryOffset();
    	
    	//------------------------------------------------------------
    	//スマレジからの値をセットするプロパティ
    	//商品ID取得
    	$smaregi_product_id = 0;
    	//商品コード取得
    	$smaregi_product_code = '';
    	//商品名
    	$smaregi_product_name = '';
    	//スマレジカテゴリID
    	$smaregi_category_id = '';
    	//スマレジグループコード
    	$smaregi_group_code = '';
    	//スマレジ規格
    	$smaregi_attribute = '';
    	//スマレジサイズ
    	$smaregi_size = '';
    	//価格
    	$smaregi_product_class_price = 0;
    	
    	//------------------------------------------------------------
    	//---スマレジからの値をセットする---
    	foreach($value->rows as $key => $row){
			$smaregi_product_id = $row->productId;
			$smaregi_product_code = $row->productCode;
			$smaregi_product_name = $row->productName;
			$smaregi_category_id = $row->categoryId;
			$smaregi_group_code = $row->groupCode;
			$smaregi_attribute = $row->attribute;
			$smaregi_size = $row->size;
			$smaregi_product_class_price = $row->price;
    	}
	            
    	//商品クラスID取得
    	$product_class_id = $smaregi_product_id - $p_offset;
    	//商品クラス存在確認
    	$existProducClass = $this->productClassRepository->find($product_class_id);
    	
    	//商品コード
    	$product_code = $smaregi_product_code;
    	//商品名
    	$product_name = $smaregi_product_name;
    	//価格
    	$product_class_price = $smaregi_product_class_price;
    	
    	//スマレジカテゴリID
    	$smart_category_id = $smaregi_category_id - $c_offset;
    	//スマレジグループコード
    	//$smart_group_code = $smaregi_group_code; //ToDo規格商品以外の情報も入っているので、ひとまず「規格」を使う
    	//スマレジ規格
    	$smart_group_code = $smaregi_attribute;
    	
    	//スマレジサイズ
    	$smart_size = $smaregi_size;
    	
    	//ログ出力(スマレジから送られる値)
    	$this->debugLog('--    ▽スマレジから送られる値    ---');
    	$this->debugLog('product_class_id: '.$product_class_id);
    	$this->debugLog('product_code: '.$product_code);
    	$this->debugLog('product_name: '.$product_name);
    	$this->debugLog('product_class_price: '.$product_class_price);
    	$this->debugLog('smart_category_id: '.$smart_category_id);
    	$this->debugLog('smart_group_code: '.$smart_group_code);
    	$this->debugLog('smart_size: '.$smart_size);
    	$this->debugLog('--    △スマレジから送られる値    ---');
    	
    	//販売種類(意図的に1固定)
    	$SalesType = $this->salesTypeRepository->find(1);
    	//商品種類ID(意図的に1固定)
        $SaleType = $this->saleTypeRepository->find(1);
        
        
        //規格あり商品
        $isKikakuProductFlg = false;
        if($smart_group_code != null){
           //規格が入っていれば規格あり商品とみなす
           $isKikakuProductFlg = true;
        }
        $this->debugLog('規格あり商品か: '.$isKikakuProductFlg);
        
        //規格親商品情報
        $kikakuParentProduct = $this->productRepository->getProductByGroupCode($smart_group_code);
        if($smart_group_code == null){
        	//スマレジ側の規格が指定されていなければnullとする
        	$kikakuParentProduct = null;
        }
        
        //規格あり親データフラグ
        $isKikakuParentFlg = false;
        if($kikakuParentProduct == null && $isKikakuProductFlg == true){
        	 $isKikakuParentFlg = true;
        }
        $this->debugLog('規格あり商品の親データか: '.$isKikakuParentFlg);
        $this->debugLog('親とする商品名： '.$kikakuParentProduct);
        
        //クラス規格ID
        $class_category_id = null;
        //クラス規格判定
        if($smart_size == '4号'){
        	$class_category_id = 3;
        }else if($smart_size == '5号'){
        	$class_category_id = 4;
        }else if($smart_size == '6号'){
        	$class_category_id = 5;
        }
        $this->debugLog('スマレジ側の指定サイズ： '.$smart_size);
        $this->debugLog('EC側の適応規格ID： '.$class_category_id);
        
    	//新規登録時
    	if (!$existProducClass) {
    	    $this->debugLog('--    MODE：新規登録    ---');
    	
            //規格あり商品でない場合
            if($kikakuParentProduct == null){
                $Product = new Product();
	            //------DB処理------
	            $this->entityManager->persist($Product);
	            
	            //[Product]
	            //商品ステータスを登録
	            $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);//新規登録時は非公開(DISPLAY_HIDE)
	            $Product->setStatus($ProductStatus);
            }else{
                //規格あり商品である場合、商品情報は作らない
            }
            
            //▽商品クラス登録(必須項目を最初に登録)-----------------------------------------------------
            //[ProductClass]
            $ProductClass = new ProductClass();
            //指定の商品クラスIDをセットする(スマレジ側の商品ID-1)
            $ProductClass->setId($product_class_id);
            
            //商品テーブルと紐づけ
                //規格あり商品でない場合
            if($kikakuParentProduct == null){
            	$ProductClass->setProduct($Product);
            }else{
                //規格あり商品である場合、親商品情報と紐づけ
                $ProductClass->setProduct($kikakuParentProduct);
            }
            
            //有効
            if($isKikakuParentFlg == true){
            	//規格あり親データなら無効
            	$ProductClass->setVisible(false);
            }else{
            	$ProductClass->setVisible(true);
            }
            
            //無制限
            $ProductClass->setStockUnlimited(true);
            //△商品クラス登録-----------------------------------------------------
            
            //▽商品在庫登録-------------------------------------------------------
            //[ProductStock]
            $ProductStock = new ProductStock();
            $ProductStock->setProductClass($ProductClass);
            
            //規格あり商品でない場合
            if($kikakuParentProduct == null){
                //[Product]
	            //商品名を登録
	            $Product->setName($product_name);
	            //スマレジ用グループコードを登録
	            $Product->setSmartGroupCode($smart_group_code);
	            //スマレジ用カテゴリIDを登録
	            $Product->setSmartCategoryId($smart_category_id);
	            //販売種類IDを登録(意図的に1固定)
	            $Product->setSalesType($SalesType);
	            //登録日、更新日を登録
	            $Product->setCreateDate(new \DateTime());
	            $Product->setUpdateDate(new \DateTime());
             }else{
                //規格あり商品である場合、商品情報は作らない
            }
            //------DB処理------
            $this->entityManager->flush();//DB反映
            //△商品在庫登録-------------------------------------------------------
            
            //▽商品クラス登録(必須以外を登録)-----------------------------------------------------
            //[ProductClass]
            //商品種類IDを登録(意図的に1固定)
            $ProductClass->setSaleType($SaleType);
            //価格を登録
            $ProductClass->setPrice02($product_class_price);
            //商品コードを登録
            $ProductClass->setCode($product_code);
            //配送日数を登録
            $ProductClass->setDeliveryDateDays(0);
            //登録日、更新日を登録
            $ProductClass->setCreateDate(new \DateTime());
            $ProductClass->setUpdateDate(new \DateTime());
            
            if($class_category_id != null){
	            //クラス規格IDをセット
	            $ClassCategory = $this->classCategoryRepository->find($class_category_id);
	            if ($ClassCategory) {
	                $ProductClass->setClassCategory1($ClassCategory);
	            }
            }
            
            //------DB処理------
            $this->entityManager->flush();//DB反映
            //△商品クラス登録(必須以外を登録)-----------------------------------------------------
            
            //[ProductStock]
            //登録日、更新日を登録
            $ProductStock->setCreateDate(new \DateTime());
            $ProductStock->setUpdateDate(new \DateTime());
            
            //------DB処理------
            $this->entityManager->persist($ProductClass);
            $this->entityManager->flush();//DB反映
            //------DB処理------
            $this->entityManager->persist($ProductStock);
            $this->entityManager->flush();//DB反映
            
        } else {
        //更新時
            $this->debugLog('--    MODE：更新    ---');
            
            //商品クラス情報取得
            $ProductClass = $this->productClassRepository->find($product_class_id);
	        
	        //▽商品クラス更新-----------------------------------------------------
	        
	        $this->debugLog('--    商品クラス更新    ---');
	        $this->debugLog('--    [▽更新情報]    ---');
	        $this->debugLog('対象のproduct_class_id: '.$product_class_id);
	    	$this->debugLog('product_code: '.$ProductClass->getCode().' → '.$product_code);
	    	$this->debugLog('product_class_price: '.$ProductClass->getPrice02().' → '.$product_class_price);
	    	$this->debugLog('smart_group_code: '.$ProductClass->getSmartGroupCode().' → '.$smart_group_code);
	        $this->debugLog('--    [△更新情報]    ---');
	        
	        
	        //[ProductClass]
            //価格を更新
            $ProductClass->setPrice02($product_class_price);
            //商品コードを更新
            $ProductClass->setCode($product_code);
            //更新日を更新
            $ProductClass->setUpdateDate(new \DateTime());
            
            if($class_category_id != null){
	            //クラス規格IDをセット
	            $ClassCategory = $this->classCategoryRepository->find($class_category_id);
	            if ($ClassCategory) {
	                $ProductClass->setClassCategory1($ClassCategory);
	            }
            }
            
            //------DB処理------
            $this->entityManager->persist($ProductClass);//登録実行
            $this->entityManager->flush();//DB反映
            //△商品クラス更新-----------------------------------------------------
            
            
            //▽商品更新-----------------------------------------------------
	        //規格あり商品でない場合
            if($kikakuParentProduct == null){
		        $Product = $ProductClass->getProduct();
		        if($Product != null){
		        	
		        	$this->debugLog('--    商品更新    ---');
			        $this->debugLog('--    [▽更新情報]    ---');
			        $this->debugLog('product_name: '.$Product->getName().' → '.$product_name);
			        $this->debugLog('smart_group_code: '.$Product->getSmartGroupCode().' → '.$smart_group_code);
			        $this->debugLog('smart_category_id: '.$Product->getSmartCategoryId().' → '.$smart_category_id);
			        $this->debugLog('--    [△更新情報]    ---');
		        	
		        	//[Product]
		            //商品名を更新
		            $Product->setName($product_name);
		            //スマレジ用グループコードを更新
	                $Product->setSmartGroupCode($smart_group_code);
		            //カテゴリIDを更新
		            $Product->setSmartCategoryId($smart_category_id);
		            //更新日を更新
		            $Product->setUpdateDate(new \DateTime());
		            //------DB処理------
		            $this->entityManager->persist($Product);//登録実行
	                $this->entityManager->flush();//DB反映
		        }
	        }
	        //△商品更新-----------------------------------------------------
	        
        }
        
        $this->debugLog(date("Y-m-d H:i:s"));//処理日時出力
    	$this->debugLog('----- END -----');
    }
    
    public function logProductInfo($request){

        $logfile = 'smartregi_receive_product.log';
        $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
        
        $post = $request->request->all();
        
        $info = json_decode($post["params"]);

        $string_val = "";

        foreach($info->data as $key => $value){
            if($value->table_name == "Product"){
                
                //$string_val .= "[".$key."]\n";
                //$string_val .= "table_name: ".$value->table_name."\n";

                if ($value->table_name == "Product"){
                    $header_val = "=====================================================================================\n";
                    $header_val .= "商品登録開始\n";
                    $header_val .= "=====================================================================================\n";
                    file_put_contents($dir.'/'.$logfile, array('Header' => $header_val), FILE_APPEND);
                }

                $string_val .= $value->table_name."\n";
                $string_val .= "-------------------------------------------------------------------------------------\n";
                $params = "";
                foreach($value->rows as $id => $info){
                    $params .= "[".$id."]\n";
                    foreach($info as $field => $data){
                        $params .= $field.":".$data."\n";
                    }
                }

                $string_val .= $params;
                $string_val .= "-------------------------------------------------------------------------------------\n";
                $string_val .= "=====================================================================================\n";
                $string_val .= "商品登録終了\n";
                $string_val .= "=====================================================================================\n";
            }
		}
        
        file_put_contents($dir . '/'. $logfile, array('Request data' => $string_val), FILE_APPEND);
    }
    
    
    public function debugLog($word){

        $logfile = 'smartregi_debug.log';
        $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
        
        //$string_val .= "\n=====================================================================================\n";
        $string_val .= $word;//キーワード出力
        $string_val .= "\n";
        
        file_put_contents($dir . '/'. $logfile, array('Request data' => $string_val), FILE_APPEND);
    }

}
