<?php

namespace Plugin\SmartEC3\Controller\Order;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;

use Plugin\SmartEC3\Repository\ConfigRepository;
use Plugin\SmartEC3\Repository\SmartRegiRepository;

use Eccube\Controller\AbstractController;

use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderItemType;

use Eccube\Repository\CustomerRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductStockRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\TaxTypeRepository;

use Eccube\Service\TaxRuleService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class SmartRegiOrderController extends AbstractController
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
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    
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
     * @param ProductStockRepository $productStockRepository
     * @param ProductClassRepository $productClassRepository
     * @param OrderRepository $orderRepository
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
        ProductStockRepository $productStockRepository,
        ProductClassRepository $productClassRepository,
        OrderRepository $orderRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderItemTypeRepository $orderItemTypeRepository,
        TaxTypeRepository $taxTypeRepository,
        PaymentRepository $paymentRepository,
        TaxRuleService $taxRuleService
    ){
        $this->configRepository = $configRepository;
        $this->smartRegiRepository = $smartRegiRepository;
        $this->customerRepository = $customerRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productClassRepository = $productClassRepository;
        $this->orderRepository = $orderRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->taxTypeRepository = $taxTypeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->taxRuleService = $taxRuleService;
    }


    /**
     * @Route("/smart_ec3/order/connect", name="smart_order_connect")
     */
    public function orderConnect(Request $request)
    {
        $Config = $this->configRepository->find(1);
        if (!$Config->getOrderUpdate()){ // Product and customer update?
            $logfile = 'smartregi_error.log';
            $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
            file_put_contents($dir.'/'.$logfile, array('Received' => "Order update received. Make sure to turn on order synchronization to update database data\n\n"), FILE_APPEND);
            return;
        } 

        /// Order processing flow
        /// ---------------------------------------------------
        /// 
        /// First Post
        ///      "TransactionHead" -> Order creation
        ///      "TransactionDetail" -> Order Items
        ///
        /// Second Post
        ///      "Stock" -> product stock update
        ///
        /// Third Post (optional)
        ///     "Customer" -> customer point update
        ///
        /// ---------------------------------------------------

        $this->logOrderInfo($request);
        $post = $request->request->all();
        $info = json_decode($post["params"]);

        foreach($info->data as $key => $value){
            // Only this info is processed
            if($value->table_name == "TransactionHead" || $value->table_name == "TransactionDetail" || $value->table_name == "Stock" || $value->table_name == "Customer"){
                switch ($value->table_name) {
                    case 'TransactionHead':
                        $this->registerOrderData($info->data);
                        break;
                    case 'Stock':
                        $this->updateStockData($value->rows);
                        break;
                    case 'Customer':
                        $this->updateCustomerData($value->rows);
                        break;
                    default:
                        break;
                }
            }
        }

        return $this->json(['Result' => true], 200);

    }

    public function registerOrderData($arrData){

        $Config = $this->configRepository->find(1);

        $Order = new Order();

        foreach ($arrData as $info) {
            switch ($info->table_name) {
                case 'TransactionHead':
                    $arrOrder = json_decode(json_encode($info->rows[0]), true);
                    $Order = $this->fillOrder($Order, $arrOrder);
                    break;
                case 'TransactionDetail':
                    $arrOrderItems = json_decode(json_encode($info->rows), true);
                    $Order = $this->setOrderItems($Order, $arrOrderItems);
                    break;
                default:
                    break;
            }
        }

        $this->entityManager->persist($Order);
        $this->entityManager->flush();

    }

    public function fillOrder($Order, $arrOrder){

        $Config = $this->configRepository->find(1);
        $customerOffset = $Config->getUserOffset();
        
        // Set Customer Data and Shipping
        //-------------------------------------------------------------------------
        if (isset($arrOrder['customerId'])){
            $customerId = $arrOrder['customerId'] - $customerOffset;
            $Customer = $this->customerRepository->find($customerId);
            $Order->setCustomer($Customer);
            $Order->setName01($Customer->getName01());
            $Order->setName02($Customer->getName02());
            $Order->setKana01($Customer->getKana01());
            $Order->setKana02($Customer->getKana02());
            
            $Shipping = new Shipping();
            $Shipping->setOrder($Order);
            $Shipping->setName01($Customer->getName01());
            $Shipping->setName02($Customer->getName02());
            $Shipping->setPref($Customer->getPref());

            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDeliveryName("スマレジ店内購入");
            
            //スマレジからの受取日をECCUBEのお届け予定日/お届け希望日にセットする
            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['pickUpDate']));

            $Order->addShipping($Shipping);
            
        }else{
            $Order->setName01("ゲスト");
            $Order->setName02("ゲスト");
            $Order->setKana01("ゲスト");
            $Order->setKana02("ゲスト");

            $Shipping = new Shipping();
            $Shipping->setOrder($Order);
            $Shipping->setName01("ゲスト");
            $Shipping->setName02("ゲスト");
            //$Shipping->setPref($Customer->getPref());

            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDeliveryName("スマレジ店内購入");
            
            //スマレジからの受取日をECCUBEのお届け予定日/お届け希望日にセットする
            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['pickUpDate']));

            $Order->addShipping($Shipping);
        }
        
        // Set the proper order Data
        //-------------------------------------------------------------------------

        $Order->setSubtotal($arrOrder['subtotalForDiscount']);
        $Order->setCharge(0);

        $discount = $arrOrder['subtotalDiscountPrice'] + $arrOrder['couponDiscount'];
        $Order->setDiscount($discount);

        $Order->setTax($arrOrder['taxInclude']);
        $Order->setTotal($arrOrder['total']);
        $Order->setPaymentTotal($arrOrder['total']);

        $Order->setUsePoint($arrOrder['spendPoint']);
        $Order->setAddPoint($arrOrder['newPoint']);

        // Point ? -> No setter method in the Order entity :\
    	//$arrOrder['use_point'] = $arrData['spendPoint'];
    	//$arrOrder['add_point'] = $arrData['newPoint'];

        // Payment type and method
        $payment_id = $arrOrder['paymentMethodId1'] ? $arrOrder['paymentMethodId1'] : 0;
        if($payment_id != 0){
            $Payment = $this->paymentRepository->find($payment_id);
            $Order->setPayment($Payment);
        }
        //skill custom スマレジで購入したデータは支払方法がnullで送られてくるみたいなので「スマレジ」ではなく「店舗支払い」とする 
        //$method = $arrOrder['paymentMethodName1'] ? $arrOrder['paymentMethodName1'] :"スマレジ";
        $method = $arrOrder['paymentMethodName1'] ? $arrOrder['paymentMethodName1'] :"店舗支払い";
        $Order->setPaymentMethod($method);

        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::DELIVERED);
        if($arrOrder['transactionHeadDivision'] == 10){
        	$OrderStatus = $this->orderStatusRepository->find(OrderStatus::KEEPED);
        }
        $Order->setOrderStatus($OrderStatus);

        $Order->setCreateDate(new \DateTime($arrOrder['transactionDateTime']));
        $Order->setUpdateDate(new \DateTime($arrOrder['updDateTime']));
        $Order->setOrderDate(new \DateTime($arrOrder['transactionDateTime']));
        $Order->setPaymentDate(new \DateTime($arrOrder['transactionDateTime']));

        return $Order;
    }

    public function setOrderItems($Order, $arrOrderItems){


        $Config = $this->configRepository->find(1);
        $productOffset = $Config->getProductOffset();

        foreach ($arrOrderItems as $arrOrderItem) {

            $OrderItem = new OrderItem();

            $ProductClass = $this->productClassRepository->find($arrOrderItem["productId"] - $productOffset);

            if ($ProductClass != null){

                $Product = $ProductClass->getProduct();
                $OrderItem->setProduct($Product);
                $OrderItem->setProductClass($ProductClass);

                $TaxType = $this->taxTypeRepository->find(1);
                $OrderItem->setTaxType($TaxType);

                $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);

                // $TaxRule = $SmartRegi->getTax();
                // $OrderItem->setTaxRuleId($TaxRule->getId());
                // $OrderItem->setTaxRate($TaxRule->getTaxRate()); //TaxRule -> smartregi config
                
                $TaxRuleVal = $arrOrderItem['taxRate'];
                $OrderItem->setTaxRate($TaxRuleVal);
                $OrderItem->setTax($arrOrderItem['taxExcludeProportional']);                           
                $OrderItem->setPrice($ProductClass->getPrice02());

                $OrderItemType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCT);
                $OrderItem->setOrderItemType($OrderItemType);

            }else{

                $TaxType = $this->taxTypeRepository->find(1);
                $OrderItem->setTaxType($TaxType);
                
                $TaxRuleVal = $arrOrderItem['taxRate'];
                //$OrderItem->setTaxRuleId($TaxRule->getId());
                $OrderItem->setTaxRate($TaxRuleVal);
                $OrderItem->setTax($arrOrderItem['taxExcludeProportional']);
                $OrderItem->setPrice($arrOrderItem['price']);
            }


            $OrderItem->setQuantity($arrOrderItem['quantity']);
            $OrderItem->setProductName($arrOrderItem['productName']);
            
            $OrderItem->setShipping($Order->getShippings()[0]);
            $OrderItem->setOrder($Order);

            $Order->addOrderItem($OrderItem);

        }

        return $Order;

    }


    public function updateStockData($rows){

        $Config = $this->configRepository->find(1);
        $offset = $Config->getProductOffset();
        if(!$Config->getProductUpdate())return;

        foreach ($rows as $stockData){

            $classId = $stockData->productId - $offset;
            $consumed = $stockData->amount; // Always a negative number

            $ProductClass = $this->productClassRepository->find($classId);
            $ProductStock = $this->productStockRepository->findOneBy(['ProductClass' => $ProductClass]);

            // 無制限チェック
            if (!$ProductClass->isStockUnlimited()){

                $new_stock = $ProductStock->getStock() + $consumed;
                $ProductStock->setStock($new_stock);

                $ProductClass->setProductStock($ProductStock);
                $ProductStock->setProductClass($ProductClass);
                $ProductClass->setStock($new_stock);

                $this->entityManager->persist($ProductStock);
                $this->entityManager->persist($ProductClass);
                $this->entityManager->flush();
            }
        }

    }

    public function updateCustomerData($rows){

        $Config = $this->configRepository->find(1);
        $offset = $Config->getUserOffset();
        if(!$Config->getUserUpdate())return;

        foreach ($rows as $customerData) {
            
            $id = $customerData->customerId - $offset;
            $Customer = $this->customerRepository->find($id);

            $Customer->setPoint($customerData->point);
            $this->entityManager->persist($Customer);
            $this->entityManager->flush();

        }
        
    }

    public function logOrderInfo($request){

        $logfile = 'smartregi_order.log';
        $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
        
        $post = $request->request->all();
        
        $info = json_decode($post["params"]);

        $string_val = "";

        foreach($info->data as $key => $value){
            if($value->table_name == "TransactionHead" || $value->table_name == "TransactionDetail" || $value->table_name == "Stock" || $value->table_name == "Customer"){
                
                //$string_val .= "[".$key."]\n";
                //$string_val .= "table_name: ".$value->table_name."\n";

                if ($value->table_name == "TransactionHead"){
                    $header_val = "=====================================================================================\n";
                    $header_val .= "受注登録開始\n";
                    $header_val .= "=====================================================================================\n";
                    file_put_contents($dir.'/'.$logfile, array('Header' => $header_val), FILE_APPEND);
                }
                if ($value->table_name != "TransactionDetail"){
                    $divider = "*************************************************************************************\n";
                    file_put_contents($dir.'/'.$logfile, array('Divider' => $divider), FILE_APPEND);
                    file_put_contents($dir.'/'.$logfile, array('Received' => print_r($post,true)), FILE_APPEND);
                    file_put_contents($dir.'/'.$logfile, array('Divider' => $divider), FILE_APPEND);
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
                if($value->table_name == "Customer"){
                    $string_val .= "=====================================================================================\n";
                    $string_val .= "受注登録終了\n";
                    $string_val .= "=====================================================================================\n";
                }
            }
		}
        
        file_put_contents($dir . '/'. $logfile, array('Request data' => $string_val), FILE_APPEND);
    }

}