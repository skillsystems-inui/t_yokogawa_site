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


    //??????????????????
    public function createProductInfo($value){
    	
    	$this->debugLog("\n".'----- START -----');
    	$this->debugLog(date("Y-m-d H:i:s"));//??????????????????
    	
    	
    	//??????????????????ID??????????????????????????????
    	$metadata = $this->entityManager->getClassMetaData(\Eccube\Entity\ProductClass::class);
    	$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
    	$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    	
    	//????????????????????????????????????????????????
    	$Config = $this->configRepository->find(1);
        $p_offset = $Config->getProductOffset();
        $c_offset = $Config->getCategoryOffset();
    	
    	//------------------------------------------------------------
    	//?????????????????????????????????????????????????????????
    	//??????ID??????
    	$smaregi_product_id = 0;
    	//?????????????????????
    	$smaregi_product_code = '';
    	//?????????
    	$smaregi_product_name = '';
    	//????????????????????????ID
    	$smaregi_category_id = '';
    	//?????????????????????????????????
    	$smaregi_group_code = '';
    	//??????????????????
    	$smaregi_attribute = '';
    	//?????????????????????
    	$smaregi_size = '';
    	//??????
    	$smaregi_product_class_price = 0;
    	
    	//------------------------------------------------------------
    	//---??????????????????????????????????????????---
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
	            
    	//???????????????ID??????
    	$product_class_id = $smaregi_product_id - $p_offset;
    	//???????????????????????????
    	$existProducClass = $this->productClassRepository->find($product_class_id);
    	
    	//???????????????
    	$product_code = $smaregi_product_code;
    	//?????????
    	$product_name = $smaregi_product_name;
    	//??????
    	$product_class_price = $smaregi_product_class_price;
    	
    	//????????????????????????ID
    	$smart_category_id = $smaregi_category_id - $c_offset;
    	//?????????????????????????????????
    	//$smart_group_code = $smaregi_group_code; //ToDo???????????????????????????????????????????????????????????????????????????????????????
    	//??????????????????
    	$smart_group_code = $smaregi_attribute;
    	
    	//?????????????????????
    	$smart_size = $smaregi_size;
    	
    	//????????????(?????????????????????????????????)
    	$this->debugLog('--    ????????????????????????????????????    ---');
    	$this->debugLog('product_class_id: '.$product_class_id);
    	$this->debugLog('product_code: '.$product_code);
    	$this->debugLog('product_name: '.$product_name);
    	$this->debugLog('product_class_price: '.$product_class_price);
    	$this->debugLog('smart_category_id: '.$smart_category_id);
    	$this->debugLog('smart_group_code: '.$smart_group_code);
    	$this->debugLog('smart_size: '.$smart_size);
    	$this->debugLog('--    ????????????????????????????????????    ---');
    	
    	//????????????(????????????1??????)
    	$SalesType = $this->salesTypeRepository->find(1);
    	//????????????ID(????????????1??????)
        $SaleType = $this->saleTypeRepository->find(1);
        
        
        //??????????????????
        $isKikakuProductFlg = false;
        if($smart_group_code != null){
           //?????????????????????????????????????????????????????????
           $isKikakuProductFlg = true;
        }
        $this->debugLog('?????????????????????: '.$isKikakuProductFlg);
        
        //?????????????????????
        $kikakuParentProduct = $this->productRepository->getProductByGroupCode($smart_group_code);
        if($smart_group_code == null){
        	//?????????????????????????????????????????????????????????null?????????
        	$kikakuParentProduct = null;
        }
        
        //?????????????????????????????????
        $isKikakuParentFlg = false;
        if($kikakuParentProduct == null && $isKikakuProductFlg == true){
        	 $isKikakuParentFlg = true;
        }
        $this->debugLog('????????????????????????????????????: '.$isKikakuParentFlg);
        $this->debugLog('???????????????????????? '.$kikakuParentProduct);
        
        //???????????????ID
        $class_category_id = null;
        //?????????????????????
        if($smart_size == '4???'){
        	$class_category_id = 3;
        }else if($smart_size == '5???'){
        	$class_category_id = 4;
        }else if($smart_size == '6???'){
        	$class_category_id = 5;
        }
        $this->debugLog('???????????????????????????????????? '.$smart_size);
        $this->debugLog('EC??????????????????ID??? '.$class_category_id);
        
    	//???????????????
    	if (!$existProducClass) {
    	    $this->debugLog('--    MODE???????????????    ---');
    	
            //?????????????????????????????????
            if($kikakuParentProduct == null){
                $Product = new Product();
	            //------DB??????------
	            $this->entityManager->persist($Product);
	            
	            //[Product]
	            //??????????????????????????????
	            $ProductStatus = $this->productStatusRepository->find(ProductStatus::DISPLAY_HIDE);//???????????????????????????(DISPLAY_HIDE)
	            $Product->setStatus($ProductStatus);
            }else{
                //???????????????????????????????????????????????????????????????
            }
            
            //????????????????????????(??????????????????????????????)-----------------------------------------------------
            //[ProductClass]
            $ProductClass = new ProductClass();
            //????????????????????????ID??????????????????(????????????????????????ID-1)
            $ProductClass->setId($product_class_id);
            
            //??????????????????????????????
                //?????????????????????????????????
            if($kikakuParentProduct == null){
            	$ProductClass->setProduct($Product);
            }else{
                //???????????????????????????????????????????????????????????????
                $ProductClass->setProduct($kikakuParentProduct);
            }
            
            //??????
            if($isKikakuParentFlg == true){
            	//????????????????????????????????????
            	$ProductClass->setVisible(false);
            }else{
            	$ProductClass->setVisible(true);
            }
            
            //?????????
            $ProductClass->setStockUnlimited(true);
            //????????????????????????-----------------------------------------------------
            
            //?????????????????????-------------------------------------------------------
            //[ProductStock]
            $ProductStock = new ProductStock();
            $ProductStock->setProductClass($ProductClass);
            
            //?????????????????????????????????
            if($kikakuParentProduct == null){
                //[Product]
	            //??????????????????
	            $Product->setName($product_name);
	            //?????????????????????????????????????????????
	            $Product->setSmartGroupCode($smart_group_code);
	            //???????????????????????????ID?????????
	            $Product->setSmartCategoryId($smart_category_id);
	            //????????????ID?????????(????????????1??????)
	            $Product->setSalesType($SalesType);
	            //??????????????????????????????
	            $Product->setCreateDate(new \DateTime());
	            $Product->setUpdateDate(new \DateTime());
             }else{
                //???????????????????????????????????????????????????????????????
            }
            //------DB??????------
            $this->entityManager->flush();//DB??????
            //?????????????????????-------------------------------------------------------
            
            //????????????????????????(?????????????????????)-----------------------------------------------------
            //[ProductClass]
            //????????????ID?????????(????????????1??????)
            $ProductClass->setSaleType($SaleType);
            //???????????????
            $ProductClass->setPrice02($product_class_price);
            //????????????????????????
            $ProductClass->setCode($product_code);
            //?????????????????????
            $ProductClass->setDeliveryDateDays(0);
            //??????????????????????????????
            $ProductClass->setCreateDate(new \DateTime());
            $ProductClass->setUpdateDate(new \DateTime());
            
            if($class_category_id != null){
	            //???????????????ID????????????
	            $ClassCategory = $this->classCategoryRepository->find($class_category_id);
	            if ($ClassCategory) {
	                $ProductClass->setClassCategory1($ClassCategory);
	            }
            }
            
            //------DB??????------
            $this->entityManager->flush();//DB??????
            //????????????????????????(?????????????????????)-----------------------------------------------------
            
            //[ProductStock]
            //??????????????????????????????
            $ProductStock->setCreateDate(new \DateTime());
            $ProductStock->setUpdateDate(new \DateTime());
            
            //------DB??????------
            $this->entityManager->persist($ProductClass);
            $this->entityManager->flush();//DB??????
            //------DB??????------
            $this->entityManager->persist($ProductStock);
            $this->entityManager->flush();//DB??????
            
        } else {
        //?????????
            $this->debugLog('--    MODE?????????    ---');
            
            //???????????????????????????
            $ProductClass = $this->productClassRepository->find($product_class_id);
	        
	        //????????????????????????-----------------------------------------------------
	        
	        $this->debugLog('--    ?????????????????????    ---');
	        $this->debugLog('--    [???????????????]    ---');
	        $this->debugLog('?????????product_class_id: '.$product_class_id);
	    	$this->debugLog('product_code: '.$ProductClass->getCode().' ??? '.$product_code);
	    	$this->debugLog('product_class_price: '.$ProductClass->getPrice02().' ??? '.$product_class_price);
	    	$this->debugLog('smart_group_code: '.$ProductClass->getSmartGroupCode().' ??? '.$smart_group_code);
	        $this->debugLog('--    [???????????????]    ---');
	        
	        
	        //[ProductClass]
            //???????????????
            $ProductClass->setPrice02($product_class_price);
            //????????????????????????
            $ProductClass->setCode($product_code);
            //??????????????????
            $ProductClass->setUpdateDate(new \DateTime());
            
            if($class_category_id != null){
	            //???????????????ID????????????
	            $ClassCategory = $this->classCategoryRepository->find($class_category_id);
	            if ($ClassCategory) {
	                $ProductClass->setClassCategory1($ClassCategory);
	            }
            }
            
            //------DB??????------
            $this->entityManager->persist($ProductClass);//????????????
            $this->entityManager->flush();//DB??????
            //????????????????????????-----------------------------------------------------
            
            
            //???????????????-----------------------------------------------------
	        //?????????????????????????????????
            if($kikakuParentProduct == null){
		        $Product = $ProductClass->getProduct();
		        if($Product != null){
		        	
		        	$this->debugLog('--    ????????????    ---');
			        $this->debugLog('--    [???????????????]    ---');
			        $this->debugLog('product_name: '.$Product->getName().' ??? '.$product_name);
			        $this->debugLog('smart_group_code: '.$Product->getSmartGroupCode().' ??? '.$smart_group_code);
			        $this->debugLog('smart_category_id: '.$Product->getSmartCategoryId().' ??? '.$smart_category_id);
			        $this->debugLog('--    [???????????????]    ---');
		        	
		        	//[Product]
		            //??????????????????
		            $Product->setName($product_name);
		            //?????????????????????????????????????????????
	                $Product->setSmartGroupCode($smart_group_code);
		            //????????????ID?????????
		            $Product->setSmartCategoryId($smart_category_id);
		            //??????????????????
		            $Product->setUpdateDate(new \DateTime());
		            //------DB??????------
		            $this->entityManager->persist($Product);//????????????
	                $this->entityManager->flush();//DB??????
		        }
	        }
	        //???????????????-----------------------------------------------------
	        
        }
        
        $this->debugLog(date("Y-m-d H:i:s"));//??????????????????
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
                    $header_val .= "??????????????????\n";
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
                $string_val .= "??????????????????\n";
                $string_val .= "=====================================================================================\n";
            }
		}
        
        file_put_contents($dir . '/'. $logfile, array('Request data' => $string_val), FILE_APPEND);
    }
    
    
    public function debugLog($word){

        $logfile = 'smartregi_debug.log';
        $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
        
        //$string_val .= "\n=====================================================================================\n";
        $string_val .= $word;//?????????????????????
        $string_val .= "\n";
        
        file_put_contents($dir . '/'. $logfile, array('Request data' => $string_val), FILE_APPEND);
    }

}
