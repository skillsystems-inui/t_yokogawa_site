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

        //受注親情報
    	$headOrder = null;
        
        foreach ($arrData as $info) {
        	switch ($info->table_name) {
                case 'TransactionHead':
                	$arrOrder = json_decode(json_encode($info->rows[0]), true);
                	
                	log_info(
			            '受注データ作成(TransactionHead)',
			            [
			                'arrOrder' => $arrOrder
			            ]
			        );
			        
                    $Order = $this->fillOrder($Order, $arrOrder);
                    //受注親情報セット
        			$headOrder = $arrOrder;
                    break;
                case 'TransactionDetail':
                    $arrOrderItems = json_decode(json_encode($info->rows), true);
                    
                	log_info(
			            '受注データ作成(TransactionDetail)',
			            [
			                'headOrder' => $headOrder
			            ]
			        );
			        
                    $Order = $this->setOrderItems($Order, $arrOrderItems, $headOrder);
                    break;
                default:
                    break;
            }
        }

        $this->entityManager->persist($Order);
        $this->entityManager->flush();

    }

    public function fillOrder($_Order, $arrOrder){

		log_info(
	            '受注情報登録開始　スマレジからのパラメータ取得(arrOrder)',
	            [
	                'arrOrder' => $arrOrder
	            ]
	        );

        $Config = $this->configRepository->find(1);
        $customerOffset = $Config->getUserOffset();
        
        //--- 登録か更新か判定する ---
        // 【取り置きキャンセルの場合】disposeServerTransactionHeadIdに取引IDが指定されている
        if ( (isset($arrOrder['disposeServerTransactionHeadId']) && ($arrOrder['disposeServerTransactionHeadId'] != '0')) ||
             (isset($arrOrder['cancelDivision']) && ($arrOrder['cancelDivision'] == 1)) 
           ){
		    
        	//対象の取引IDが指定されている場合、更新モード
        	//仮に今ある受注IDを指定する
        	$orderId = $arrOrder['disposeServerTransactionHeadId'];
        	$TargetOrder = $this->orderRepository->getRegularCustomerByEmail($orderId);
        	
        	$Order = $TargetOrder;
        	//--- ステータスのみを更新する ---
        	//「新規受付」→「注文取消し」に変更
        	//「取り置き」→「注文取消し」に変更
        	
        	//ステータスを「注文取消し」に変更する
			$OrderStatus = $this->orderStatusRepository->find(OrderStatus::CANCEL);
			log_info(
	            '受注情報　取り置きキャンセル　ステータス更新',
	            [
	                'orderId' => $orderId,
	                'TargetOrder' => $TargetOrder,
	                'OrderStatus' => $OrderStatus,
	            ]
	        );
			//ステータスをセット
        	$Order->setOrderStatus($OrderStatus);
        	
        	
        	log_info(
	            '受注情報登録　取り置きキャンセル　紐づく商品データ取得開始',
	            [
	                'Order' => $Order
	            ]
	        );
        	$OrderItems = $this->orderItemRepository->findBy(['Order' => $Order]);
            foreach ($OrderItems as $OrderItem) {
            
	            log_info(
		            '受注情報登録　取り置きキャンセル　紐づく商品データ取得して削除実行',
		            [
		                'OrderItem' => $OrderItem
		            ]
		        );
            
                $Order->removeOrderItem($OrderItem);
                $this->entityManager->remove($OrderItem);
                $this->entityManager->flush($OrderItem);
                
                log_info(
		            '受注情報登録　取り置きキャンセル　紐づく商品データ取得して削除完了',
		            [
		                'Order' => $Order
		            ]
		        );
            }
        	log_info(
	            '受注情報登録　取り置きキャンセル　紐づく商品データ取得終了',
	            [
	                'Order' => $Order
	            ]
	        );
        	
        	
        	log_info(
	            '受注情報登録終了　取り置きキャンセル',
	            [
	                'Order' => $Order
	            ]
	        );
        	
        	return $Order;
        }else if ( isset($arrOrder['pickUpTransactionHeadId']) && ($arrOrder['pickUpTransactionHeadId'] > 0) ){
		    // 【取り置き商品購入の場合】pickUpTransactionHeadIdに対象の取引IDが指定されている
		    //  2件のデータが来る
		    //  　1件目　pickUpTransactionHeadId:transactionHeadIdと同じ、     layawayServerTransactionHeadId:取り置き実施したtransactionHeadId、transactionHeadId:オリジナル
		    //  　2件目　pickUpTransactionHeadId:上記1件目のtransactionHeadId、layawayServerTransactionHeadId:なし、                             transactionHeadId:取り置き実施したtransactionHeadId
		    
        	//対象の取引IDが指定されている場合、更新モード
        	$torioki_orderId = $arrOrder['layawayServerTransactionHeadId'];
        	
        	log_info(
	            '受注情報　取り置き商品購入判定　処理前',
	            [
	                'layawayServerTransactionHeadId' => $torioki_orderId,
	            ]
	        );
        	
        	if($torioki_orderId > 0){
        		//ステータスセット
        		$TargetOrder = $this->orderRepository->getRegularCustomerByEmail($torioki_orderId);
        	
	        	$Order = $TargetOrder;
	        	//--- ステータスのみを更新する ---
	        	//「取り置き」→「発送済み」に変更
	        	
	        	//ステータスをセット
	        	$OrderStatus = $this->orderStatusRepository->find(OrderStatus::DELIVERED);
	        	log_info(
		            '受注情報　取り置き商品購入判定　ステータスセット',
		            [
		                'OrderStatus' => $OrderStatus,
		            ]
		        );
				$Order->setOrderStatus($OrderStatus);
				
				//20220317
				//↓金額・ポイントも更新する
				$Order->setSubtotal($arrOrder['subtotalForDiscount']);
		        $Order->setCharge(0);

		        $discount = $arrOrder['subtotalDiscountPrice'] + $arrOrder['couponDiscount'];
		        $Order->setDiscount($discount);

		        $Order->setTax($arrOrder['taxInclude']);
		        $Order->setTotal($arrOrder['total']);
		        $Order->setPaymentTotal($arrOrder['total']);

		        $Order->setUsePoint($arrOrder['spendPoint']);
		        $Order->setAddPoint($arrOrder['newPoint']);

		        // Payment type and method
		        $payment_id = $arrOrder['paymentMethodId1'] ? $arrOrder['paymentMethodId1'] : 0;
		        if($payment_id != 0){
		            $Payment = $this->paymentRepository->find($payment_id);
		            $Order->setPayment($Payment);
		        }
		        //skill custom スマレジで購入したデータは支払方法がnullで送られてくるので「スマレジ」ではなく「店舗支払い」とする 
		        //$method = $arrOrder['paymentMethodName1'] ? $arrOrder['paymentMethodName1'] :"スマレジ";
		        $method = $arrOrder['paymentMethodName1'] ? $arrOrder['paymentMethodName1'] :"店舗支払い";
		        $Order->setPaymentMethod($method);
		        log_info(
		            '受注情報　取り置き商品購入判定　金額・ポイントセット',
		            [
		                'subtotalForDiscount' => $arrOrder['subtotalForDiscount'],
		                'discount' => $discount,
		                'taxInclude' => $arrOrder['taxInclude'],
		                'total' => $arrOrder['total'],
		                'PaymentTotal' => $arrOrder['total'],
		                'UsePoint' => $arrOrder['spendPoint'],
		                'AddPoint' => $arrOrder['newPoint'],
		                'method' => $method,
		            ]
		        );
		        //↑金額・ポイントも更新する
				
				
				//20220204
	            //レシート番号を登録しておく
	            $transactionUuid = $arrOrder['transactionUuid'];
	            log_info(
		            '受注情報　取り置き商品購入判定　レシート番号セット',
		            [
		                'transactionUuid' => $transactionUuid,
		            ]
		        );
	            $Order->setTransactionUuid($transactionUuid);
	            //端末IDを登録しておく
	            $terminalId = $arrOrder['terminalId'];
	            log_info(
		            '受注情報　取り置き商品購入判定　端末IDセット',
		            [
		                'terminalId' => $terminalId,
		            ]
		        );
	            $Order->setTerminalId($terminalId);
	            //端末取引IDを登録しておく
	            $terminalTranId = $arrOrder['terminalTranId'];
	            log_info(
		            '受注情報　取り置き商品購入判定　端末取引IDセット',
		            [
		                'terminalTranId' => $terminalTranId,
		            ]
		        );
	            $Order->setTerminalTranId($terminalTranId);
				
				
				$OrderItems = $this->orderItemRepository->findBy(['Order' => $Order]);
	            foreach ($OrderItems as $OrderItem) {
	            
		            log_info(
			            '受注情報登録　取り置き商品購入判定　紐づく商品データ取得して削除実行',
			            [
			                'OrderItem' => $OrderItem
			            ]
			        );
	            
	                $Order->removeOrderItem($OrderItem);
	                $this->entityManager->remove($OrderItem);
	                $this->entityManager->flush($OrderItem);
	                
	                log_info(
			            '受注情報登録　取り置き商品購入判定　紐づく商品データ取得して削除完了',
			            [
			                'Order' => $Order
			            ]
			        );
	            }
				
				
        	}else{
        		$torioki_orderId = $arrOrder['transactionHeadId'];
        		$TargetOrder = $this->orderRepository->getRegularCustomerByEmail($torioki_orderId);
        		log_info(
		            '受注情報　取り置き商品購入判定　対象の取引IDが指定されていないため受注情報を返すだけ',
		            [
		                'torioki_orderId' => $torioki_orderId,
		                'TargetOrder' => $TargetOrder,
		            ]
		        );
        		$Order = $TargetOrder;
        	}
        	
        	log_info(
	            '受注情報登録　取り置き商品購入判定　紐づく商品データ取得開始',
	            [
	                'Order' => $Order
	            ]
	        );
        	
        	log_info(
	            '受注情報登録　取り置き商品購入判定　紐づく商品データ取得終了',
	            [
	                'Order' => $Order
	            ]
	        );
        	
        	
        	log_info(
	            '受注情報登録終了　取り置き商品購入判定',
	            [
	                'Order' => $Order
	            ]
	        );
        	
        	return $Order;
        }else{

	        log_info(
		            '受注情報　取り置きの場合以外　登録モード',
		            [
		                '_Order' => $_Order,
		            ]
		        );
        
        	//対象の取引IDが指定されていない場合、登録モード
        	$Order = $_Order;
        }
        //--- .登録か更新か判定する ---
        
        // Set Customer Data and Shipping
        //-------------------------------------------------------------------------
        if (isset($arrOrder['customerCode'])){
            $customerCode = $arrOrder['customerCode'];//会員コード指定　$customerOffsetは考慮しない
            $Customer = $this->customerRepository->getRegularCustomerByCustomerCode($customerCode);
            $Order->setCustomer($Customer);
            $Order->setName01($Customer->getName01());
            $Order->setName02($Customer->getName02());
            $Order->setKana01($Customer->getKana01());
            $Order->setKana02($Customer->getKana02());
            
            //名前(全)セット 20220426
            $_NameAll = $Customer->getName01().' '.$Customer->getName02();
            $_KanaAll = $Customer->getKana01().' '.$Customer->getKana02();
            $_AddrAll = $Customer->getAddr01().' '.$Customer->getAddr02();
            $Order->setNameAll($_NameAll);
            $Order->setKanaAll($_KanaAll);
            $Order->setAddrAll($_AddrAll);

            //電話。住所、メールを登録
            /*
            $Order->setPostalCode('5300001');
            $Order->setPref(27);
            $Order->setAddr01('大阪府大阪市');
            $Order->setAddr02('123');
            $Order->setPhoneNumber('09011112222');
            $Order->setEmail('test@gmail.com');
            */
            $Order->setPostalCode($Customer->getPostalCode());
            $Order->setPref($Customer->getPref());
            $Order->setAddr01($Customer->getAddr01());
            $Order->setAddr02($Customer->getAddr02());
            $Order->setPhoneNumber($Customer->getPhoneNumber());
            $Order->setEmail($Customer->getEmail());
            
            //スマレジの取引IDを登録しておく
            $smaregiId = $arrOrder['transactionHeadId'];
            $Order->setSmaregiId($smaregiId);
            
            //20220204
            //レシート番号を登録しておく
            $transactionUuid = $arrOrder['transactionUuid'];
            $Order->setTransactionUuid($transactionUuid);
            //端末IDを登録しておく
            $terminalId = $arrOrder['terminalId'];
            $Order->setTerminalId($terminalId);
            //端末取引IDを登録しておく
            $terminalTranId = $arrOrder['terminalTranId'];
            $Order->setTerminalTranId($terminalTranId);
            
            //メモをセット
            $Order->setNote($arrOrder['memo']);
            
            $Shipping = new Shipping();
            $Shipping->setOrder($Order);
            $Shipping->setName01($Customer->getName01());
            $Shipping->setName02($Customer->getName02());
            //カナ登録
            $Shipping->setKana01($Customer->getKana01());
            $Shipping->setKana02($Customer->getKana02());
            
            //出荷用メモ欄にスマレジからのレシートメモをセットする 20220325
            $Shipping->setNote($arrOrder['receiptMemo']);
            
            $Shipping->setPref($Customer->getPref());
            $Shipping->setPostalCode('5300001');
            $Shipping->setPhoneNumber('09011112222');
            $Shipping->setAddr01($Customer->getAddr01());
            $Shipping->setAddr02($Customer->getAddr02());
            
            //名前(全)セット 20220426
            $Shipping->setNameAll($_NameAll);
            $Shipping->setKanaAll($_KanaAll);
            $Shipping->setAddrAll($_AddrAll);
            
            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDeliveryName("スマレジ店内購入");
            
            //配送セット
            $deliv_id_honten = 3;//和泉中央本店
            $deliv_id_kishiwada = 4;//岸和田店
            
            if($arrOrder['storeId'] == 2){
            	//岸和田店の場合
            	$TargetDelivery = $this->deliveryRepository->find($deliv_id_kishiwada);
            }else{
            	//和泉中央本店の場合
            	$TargetDelivery = $this->deliveryRepository->find($deliv_id_honten);
            }
            $Shipping->setDelivery($TargetDelivery);
            
            //スマレジからの受取日をECCUBEのお届け予定日/お届け希望日にセットする
            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['pickUpDate']));

            $Order->addShipping($Shipping);
            
        }else{
            $Order->setName01("ゲスト");
            $Order->setName02("ゲスト");
            $Order->setKana01("ゲスト");
            $Order->setKana02("ゲスト");
            
            //名前(全)セット 20220426
            $_NameAll = 'ゲスト ゲスト';
            $_KanaAll = 'ゲスト ゲスト';
            $_AddrAll = '大阪市北区梅田 123';
            $Order->setNameAll($_NameAll);
            $Order->setKanaAll($_KanaAll);
            $Order->setAddrAll($_AddrAll);
            
            $Order->setPostalCode('5300001');
            $Order->setPhoneNumber('09011112222');
            $Order->setAddr01('大阪市北区梅田');
            $Order->setAddr02('123');
            $Order->setEmail('test@gmail.com');
            
            //スマレジの取引IDを登録しておく
            $smaregiId = $arrOrder['transactionHeadId'];
            $Order->setSmaregiId($smaregiId);
            
            //20220204
            //レシート番号を登録しておく
            $transactionUuid = $arrOrder['transactionUuid'];
            $Order->setTransactionUuid($transactionUuid);
            //端末IDを登録しておく
            $terminalId = $arrOrder['terminalId'];
            $Order->setTerminalId($terminalId);
            //端末取引IDを登録しておく
            $terminalTranId = $arrOrder['terminalTranId'];
            $Order->setTerminalTranId($terminalTranId);
            
            //メモをセット
            $Order->setNote($arrOrder['memo']);
            
            $Shipping = new Shipping();
            $Shipping->setOrder($Order);
            $Shipping->setName01("ゲスト");
            $Shipping->setName02("ゲスト");
            //カナ登録
            $Shipping->setKana01("ゲスト");
            $Shipping->setKana02("ゲスト");
            
            //名前(全)セット 20220426
            $Shipping->setNameAll($_NameAll);
            $Shipping->setKanaAll($_KanaAll);
            $Shipping->setAddrAll($_AddrAll);
            
            //出荷用メモ欄にスマレジからのレシートメモをセットする 20220325
            $Shipping->setNote($arrOrder['receiptMemo']);
            
            //都道府県コードも登録する(大阪府)
            $zip_code = 27;
            $Pref = $this->prefRepository->find($zip_code);
            //　該当会員がいるならそれをセット
            if($Pref != null){
            	$Order->setPref($Pref);
            	$Shipping->setPref($Pref);
            }else{
            	//いないならnullセット
            	$Order->setPref(null);
            	$Shipping->setPref(null);
            }
            
            $Shipping->setPostalCode('5300001');
            $Shipping->setPhoneNumber('09011112222');
            $Shipping->setAddr01('大阪市北区梅田');
            $Shipping->setAddr02('123');
            
            $Shipping->setShippingDeliveryDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDate(new \DateTime($arrOrder['transactionDateTime']));
            $Shipping->setShippingDeliveryName("スマレジ店内購入");
            
            //配送セット
            $deliv_id_honten = 3;//和泉中央本店
            $deliv_id_kishiwada = 4;//岸和田店
            
            if($arrOrder['storeId'] == 2){
            	//岸和田店の場合
            	$TargetDelivery = $this->deliveryRepository->find($deliv_id_kishiwada);
            }else{
            	//和泉中央本店の場合
            	$TargetDelivery = $this->deliveryRepository->find($deliv_id_honten);
            }
            $Shipping->setDelivery($TargetDelivery);
            
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
        //skill custom スマレジで購入したデータは支払方法がnullで送られてくるので「スマレジ」ではなく「店舗支払い」とする 
        //$method = $arrOrder['paymentMethodName1'] ? $arrOrder['paymentMethodName1'] :"スマレジ";
        $method = $arrOrder['paymentMethodName1'] ? $arrOrder['paymentMethodName1'] :"店舗支払い";
        $Order->setPaymentMethod($method);
        
        //[transactionHeadDivision]
        //スマレジから通常購入：1
        //スマレジから取り置き：10
        //
        //ECCUBE OrderStatus
        //新規受付   NEW
        //注文取消し CANCEL
        //発送済み   DELIVERED
        //返品       RETURNED
        //取り置き   KEEPED

        //----- 取り置き以外は発送済みにする -----
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::DELIVERED);
        
        //〇取り置きの場合
        if($arrOrder['transactionHeadDivision'] == 10){
        	$OrderStatus = $this->orderStatusRepository->find(OrderStatus::KEEPED);
        }
        
        //ステータスをセット
        $Order->setOrderStatus($OrderStatus);

        $Order->setCreateDate(new \DateTime($arrOrder['transactionDateTime']));
        $Order->setUpdateDate(new \DateTime($arrOrder['updDateTime']));
        $Order->setOrderDate(new \DateTime($arrOrder['transactionDateTime']));
        $Order->setPaymentDate(new \DateTime($arrOrder['transactionDateTime']));

        return $Order;
    }

    public function setOrderItems($Order, $arrOrderItems, $headOrder){

			log_info(
	            '受注詳細情報登録開始　スマレジからのパラメータ取得(headOrder)',
	            [
	                'headOrder' => $headOrder,
	            ]
	        );
	        
	        if ( isset($headOrder['pickUpTransactionHeadId']) && ($headOrder['pickUpTransactionHeadId'] > 0) ){
		    // 【取り置き商品購入の場合】pickUpTransactionHeadIdに対象の取引IDが指定されている
		    //  2件のデータが来る
		    //  　1件目　pickUpTransactionHeadId:transactionHeadIdと同じ、     layawayServerTransactionHeadId:取り置き実施したtransactionHeadId、transactionHeadId:オリジナル
		    //  　2件目　pickUpTransactionHeadId:上記1件目のtransactionHeadId、layawayServerTransactionHeadId:なし、                             transactionHeadId:取り置き実施したtransactionHeadId
		    	
	        	//「pickUpTransactionHeadId」は2件とも値が入っている
	        	//「layawayServerTransactionHeadId」は取り置き購入時(購入時内容)のみ値が入っている　取り置き時内容のデータには入っていない
	        	if( isset($headOrder['layawayServerTransactionHeadId']) && ($headOrder['layawayServerTransactionHeadId'] > 0) ){
	        		
	        		log_info(
			            '受注詳細情報登録　取り置き商品購入判定　必要なデータのため、処理続行',
			            [
			                'headOrder' => $headOrder,
			                'pickUpTransactionHeadId' => $headOrder['pickUpTransactionHeadId'],
			                'layawayServerTransactionHeadId' => $headOrder['layawayServerTransactionHeadId'],
			                'transactionHeadId' => $headOrder['transactionHeadId'],
			            ]
			        );
			        

	        	}else{
	        		
	        		log_info(
			            '受注詳細情報登録　取り置き商品購入判定　不要なデータのため、購入商品の追加はしない',
			            [
			                'headOrder' => $headOrder,
			                'pickUpTransactionHeadId' => $headOrder['pickUpTransactionHeadId'],
			                'layawayServerTransactionHeadId' => $headOrder['layawayServerTransactionHeadId'],
			                'transactionHeadId' => $headOrder['transactionHeadId'],
			            ]
			        );
			        
			        return $Order;
	        	}
	        	
	        	
	        }else{
	        	log_info(
	            '受注詳細情報登録　取り置き商品購入以外',
		            [
		                'pickUpTransactionHeadId' => $headOrder['pickUpTransactionHeadId'],
		                'layawayServerTransactionHeadId' => $headOrder['layawayServerTransactionHeadId'],
		                'transactionHeadId' => $headOrder['transactionHeadId'],
		            ]
		        );
	        }
	        
	        
	        


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
                
                log_info(
		            '受注詳細情報登録　商品情報',
			            [
			                'Product' => $Product,
			                'ProductClass' => $ProductClass,
			                'TaxType' => $TaxType,
			                'setTaxRate' => $TaxRuleVal,
			                'setTax' => $arrOrderItem['taxExcludeProportional'],
			                'setPrice' => $ProductClass->getPrice02(),
			                'OrderItemType' => $OrderItemType,
			            ]
			        );

            }else{

                $TaxType = $this->taxTypeRepository->find(1);
                $OrderItem->setTaxType($TaxType);
                
                $TaxRuleVal = $arrOrderItem['taxRate'];
                //$OrderItem->setTaxRuleId($TaxRule->getId());
                $OrderItem->setTaxRate($TaxRuleVal);
                $OrderItem->setTax($arrOrderItem['taxExcludeProportional']);
                $OrderItem->setPrice($arrOrderItem['price']);
                
                log_info(
		            '受注詳細情報登録　商品情報以外',
			            [
			                'TaxType' => $TaxType,
			                'setTaxRate' => $TaxRuleVal,
			                'setTax' => $arrOrderItem['taxExcludeProportional'],
			                'setPrice' => $arrOrderItem['price'],
			            ]
			        );
            }


            $OrderItem->setQuantity($arrOrderItem['quantity']);
            $OrderItem->setProductName($arrOrderItem['productName']);
            //20220414 プロダクトコード追加
            $OrderItem->setProductCode($arrOrderItem['productCode']);
            
            $OrderItem->setShipping($Order->getShippings()[0]);
            $OrderItem->setOrder($Order);

            $Order->addOrderItem($OrderItem);
            
            
            log_info(
		            '受注詳細情報登録　配送情報など',
			            [
			                'setQuantity' => $arrOrderItem['quantity'],
			                'setProductName' => $arrOrderItem['productName'],
			                'setShipping' => $Order->getShippings()[0],
			                'setOrder' => $Order,
			            ]
			        );

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
            
            $customer_code = $customerData->customerCode;//会員コード　$offsetは考慮しない
            $Customer = $this->customerRepository->getRegularCustomerByCustomerCode($customer_code);
            
            //店舗ID取得(1:和泉中央本店,2:岸和田店)
	        $store_id = 1;
            //最終更新日
		    $updDateTime = date('Y-m-d H:i:s', strtotime($customerData->updDateTime));
		    
		    //☆最終更新日だけ先に更新する
		    /*
		    $Customer->setLastBuyDate(new \DateTime($customerData->updDateTime));
		    $this->entityManager->persist($Customer);
            $this->entityManager->flush();
            */
		    
		    //配送
            $deliv_id_honten = 3;//和泉中央本店
            $deliv_id_kishiwada = 4;//岸和田店
		    
		    $haisoOrderObj = $this->orderRepository->getOrderByOrderdate(new \DateTime($customerData->updDateTime));
		    if($haisoOrderObj != null){
		    	$_Shipping = $haisoOrderObj->getShippings()[0];
		    	$ship_store_name = $_Shipping->getDelivery()->getName();
		    	if(strpos($ship_store_name,'岸和田') !== false){
		    		//店舗ID取得(1:和泉中央本店,2:岸和田店)
	        		$store_id = 2;
		    	}
		    }
		    
	        log_info(
		            'ポイント履歴に反映　店舗ID取得',
		            [
		                'store_id' => $store_id,
		            ]
		        );
            
            //ポイント反映前
            $org_customer_point = $Customer->getPoint();
            //ポイント差分
            $dif_customer_point = ($customerData->point) - $org_customer_point;
            
            //ポイント履歴に反映
            $this->updatePointHistory($Customer, $dif_customer_point, $store_id);
            
            $Customer->setPoint($customerData->point);
            $this->entityManager->persist($Customer);
            $this->entityManager->flush();
            
            
        }
        
    }
    
    public function updatePointHistory($Customer, $AddPoint, $StoreId){
        
        //----- ポイント履歴の更新を行う -----
        // ポイント履歴取得
        $targetPointHistory = $this->pointHistoryRepository->findOneBy(
            [
                'Customer' => $Customer,
            ]
        );
        
        //店舗判別(和泉中央本店かどうか)
        $IsHoten = $StoreId != "2" ? true : false;
        
        $now = new \DateTime("now");//現在日時
        if($targetPointHistory != null){
        	//データがあればポイント更新
        	if($IsHoten == true){
        		//和泉中央本店の場合
        		$current_point = intval($targetPointHistory->getShopHonten());
		        $targetPointHistory->setShopHonten($current_point + intval($AddPoint));
		        $targetPointHistory->setShopHontenDate($now);
        	}else{
        		//岸和田店の場合
        		$current_point = intval($targetPointHistory->getShopKishiwada());
        		$targetPointHistory->setShopKishiwada($current_point + intval($AddPoint));
        		$targetPointHistory->setShopKishiwadaDate($now);
        	}
        	
        	$targetPointHistory->setUpdateDate($now);
        	
        	$this->entityManager->persist($targetPointHistory);
        	
        }else{
        	//データ未作成なら新規作成
        	
        	/* @var PointHistory $PointHistory */
            $PointHistory = new PointHistory();
	        $PointHistory->setCustomer($Customer);
	        
	        $PointHistory->setSum(0);
	        $PointHistory->setAppBirth(0);
	        
	        if($IsHoten == true){
        		//和泉中央本店の場合
        		$PointHistory->setShopKishiwada(0);
		        $PointHistory->setShopHonten(intval($AddPoint));
		        $PointHistory->setShopHontenDate($now);
        	}else{
        		//岸和田店の場合
        		$PointHistory->setShopKishiwada(intval($AddPoint));
		        $PointHistory->setShopHonten(0);
		        $PointHistory->setShopKishiwadaDate($now);
        	}
	        
	        $PointHistory->setCreateDate($now);
	        $PointHistory->setUpdateDate($now);
	        $PointHistory->setAvailable(1);
	        
	        $this->entityManager->persist($PointHistory);
        }
        
        $this->entityManager->flush();
        
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