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
        
        //test
        $this->debugLog('test01');
        
        foreach($info->data as $key => $value){
            
            //test
            $this->debugLog('test02');
            
            // Only this info is processed
            if($value->table_name == "Product"){
            

	            
		            //test
		            //$this->debugLog('test03');
		            //$this->debugLog($value->table_name);
		            //$this->debugLog($row->productId);
		            
		            
		            switch ($value->table_name) {
	                    case 'Product':
	                        $this->createProductInfo($value);
	                        //return $this->json(['Result2' => true], 200);
	                        break;
	                    default:
	                        break;
	                }
	                /**/
	                
            }
        }
        
        //test
        $this->debugLog('test99');

        return $this->json(['Result9' => true], 200);

    }


    //商品情報登録
    public function createProductInfo($value){
    	
    	// -----必須情報-----
    	//[dtb_product]
    	// id : ユニークなID
    	//creator_id : 1固定
    	//product_status_id : DISPLAY_HIDE
    	// name : 任意の商品名
    	// create_date : 作成日時
    	// update_date : 更新日時
    	// discriminator_type : 「product」固定
    	// smart_category_id : スマレジのカテゴリID+1
    	// 
    	//[dtb_product_class]
    	// id : ユニークなID
    	// stock_unlimited : 1(無制限)固定
    	// price02 : 任意の価格
    	// visible : 1(有効)固定
    	// create_date : 作成日時
    	// update_date : 更新日時
    	// discriminator_type : 「productclass」固定
    	// delivery_date_days : 0固定
    	// 
    	// [dtb_product_stock]
    	// id : ユニークなID
    	// create_date : 作成日時
    	// update_date : 更新日時
    	// discriminator_type : 「productstock」固定
    	// -------------------
    	
    	$this->debugLog('start');
    	
    	$metadata = $this->entityManager->getClassMetaData(\Eccube\Entity\ProductClass::class);
    	$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
    	$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    	
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
    	
    	//スマレジカテゴリID
    	$smart_category_id = $smaregi_category_id - $c_offset;
    	
    	//スマレジグループコード
    	$smart_group_code = $smaregi_group_code;
    	
    	//価格
    	$product_class_price = $smaregi_product_class_price;
	
    	//販売種類(意図的に1固定)
    	$SalesType = $this->salesTypeRepository->find(1);
    	//商品種類ID(意図的に1固定)
        $SaleType = $this->saleTypeRepository->find(1);
          
        $this->debugLog('testVal');

$this->debugLog($smaregi_product_id);
$this->debugLog($smaregi_product_code);
$this->debugLog($smaregi_product_name);
$this->debugLog($smaregi_category_id);
$this->debugLog($smaregi_product_class_price);
/**/
        
        
        
        
    	//新規登録時
    	if (!$existProducClass) {
            
            $this->debugLog('testB');
            
            $Product = new Product();
            $this->entityManager->persist($Product);
            
            
            //[Product]
            //商品ステータスを登録
            $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);//新規登録時はステータスは非公開(DISPLAY_HIDE)
            $Product->setStatus($ProductStatus);
            
            $this->debugLog('testB1');
            
            //[ProductClass]
            $ProductClass = new ProductClass();
            //指定の商品クラスIDをセットする(スマレジ側の商品ID-1)
            $ProductClass->setId($product_class_id);
            //商品テーブルと紐づけ
            $ProductClass->setProduct($Product);
            //有効
            $ProductClass->setVisible(true);
            //無制限
            $ProductClass->setStockUnlimited(true);
            //スマレジ用グループコードを登録
            $ProductClass->setSmartGroupCode($smart_group_code);
            
            //[ProductStock]
            $ProductStock = new ProductStock();
            $ProductStock->setProductClass($ProductClass);
            
            //[Product]
            //商品名を登録
            $Product->setName($product_name);
            //スマレジ用カテゴリIDを登録
            $Product->setSmartCategoryId($smart_category_id);
            //販売種類IDを登録(意図的に1固定)
            $Product->setSalesType($SalesType);
            //登録日、更新日を登録
            $Product->setCreateDate(new \DateTime());
            $Product->setUpdateDate(new \DateTime());
            
            $this->entityManager->flush();//DB反映
            
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
            $this->debugLog('testB4');
            
            $this->entityManager->flush();//DB反映
            $this->debugLog('testB5');
            //[ProductStock]
            //登録日、更新日を登録
            $ProductStock->setCreateDate(new \DateTime());
            $ProductStock->setUpdateDate(new \DateTime());
            
           
            $this->entityManager->persist($ProductClass);
            $this->entityManager->flush();//DB反映
            
            $this->entityManager->persist($ProductStock);
            $this->entityManager->flush();//DB反映
            
        } else {
        //更新時
            $this->debugLog('testC');
        
            //商品クラス情報取得
            $ProductClass = $this->productClassRepository->find($product_class_id);
	        
	        /*
            //[Product]
            //商品名を更新
            $Product->setName($product_name);
            //カテゴリIDを更新
            $Product->setSmartCategoryId($smart_category_id);
            //更新日を更新
            $Product->setUpdateDate(new \DateTime());
            */
        }
        
        $this->debugLog('testD');
    	$this->debugLog('end');
    }
    
    
    public function logProductInfo($request){

        $logfile = 'smartregi_oroductg.log';
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
        
        $string_val .= "\n=====================================================================================\n";
        $string_val .= $word;//キーワード出力
        $string_val .= "\n=====================================================================================\n";
        
        file_put_contents($dir . '/'. $logfile, array('Request data' => $string_val), FILE_APPEND);
    }

}
