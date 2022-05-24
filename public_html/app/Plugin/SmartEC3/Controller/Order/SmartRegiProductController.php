<?php

namespace Plugin\SmartEC3\Controller\Order;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;

use Plugin\SmartEC3\Repository\ConfigRepository;
use Plugin\SmartEC3\Repository\SmartRegiRepository;

use Eccube\Controller\AbstractController;

use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Shipping;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderItemType;

use Eccube\Repository\CustomerRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\OrderRepository;

use Eccube\Repository\OrderItemRepository;

use Eccube\Repository\PaymentRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductStockRepository;
use Eccube\Repository\PointHistoryRepository;

use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\TaxTypeRepository;
use Eccube\Repository\Master\PrefRepository;

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
     * SmartRegiController constructor.
     *
     * @param ConfigRepository $configRepository
     * @param SmartRegiRepository $smartRegiRepository
     * @param CustomerRepository $customerRepository
     * @param DeliveryRepository $deliveryRepository
     * @param PrefRepository $prefRepository
     * @param ProductStockRepository $productStockRepository
     * @param ProductClassRepository $productClassRepository
     * @param PointHistoryRepository $pointHistoryRepository
     * @param OrderRepository $orderRepository
     * @param OrderItemRepository $orderItemRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param TaxTypeRepository $taxTypeRepository
     * @param PaymentRepository $paymentRepository
     * @param TaxRuleService $taxRuleService
     */
    public function __construct(
        ConfigRepository $configRepository,
        SmartRegiRepository $smartRegiRepository,
        CustomerRepository $customerRepository,
        DeliveryRepository $deliveryRepository,
        PrefRepository $prefRepository,
        ProductStockRepository $productStockRepository,
        ProductClassRepository $productClassRepository,
        PointHistoryRepository $pointHistoryRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderItemTypeRepository $orderItemTypeRepository,
        TaxTypeRepository $taxTypeRepository,
        PaymentRepository $paymentRepository,
        TaxRuleService $taxRuleService
    ){
        $this->configRepository = $configRepository;
        $this->smartRegiRepository = $smartRegiRepository;
        $this->customerRepository = $customerRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->prefRepository = $prefRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productClassRepository = $productClassRepository;
        $this->pointHistoryRepository = $pointHistoryRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->taxTypeRepository = $taxTypeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->taxRuleService = $taxRuleService;
    }

    /**
     * @Route("/smart_ec3/order/connect2", name="smart_product_connect")
     */
    public function productConnect(Request $request)
    {

//        $Config = $this->configRepository->find(1);
//        if (!$Config->getOrderUpdate()){ // Product and customer update?
//            $logfile = 'smartregi_error.log';
//            $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
//            file_put_contents($dir.'/'.$logfile, array('Received' => "Order update received. Make sure to turn on order synchronization to update database data\n\n"), FILE_APPEND);
//            return;
//        } 

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
            $this->debugLog('test03');
            $this->debugLog($value->table_name);
            
                switch ($value->table_name) {
                    case 'Product':
                        return $this->json(['Result2' => true], 200);
                        break;
                    default:
                        break;
                }
            }
        }
        
        //test
        $this->debugLog('test99');

        return $this->json(['Result9' => true], 200);

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
