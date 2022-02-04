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

namespace Eccube\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\EventListener\SecurityListener;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Util\StringUtil;
use SunCat\MobileDetectBundle\DeviceDetector\MobileDetector;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderHelper
{
    // FIXME 必要なメソッドのみ移植する
    use ControllerTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string 非会員情報を保持するセッションのキー
     */
    const SESSION_NON_MEMBER = 'eccube.front.shopping.nonmember';

    /**
     * @var string 非会員の住所情報を保持するセッションのキー
     */
    const SESSION_NON_MEMBER_ADDRESSES = 'eccube.front.shopping.nonmember.customeraddress';

    /**
     * @var string 受注IDを保持するセッションのキー
     */
    const SESSION_ORDER_ID = 'eccube.front.shopping.order.id';

    /**
     * @var string カートが分割されているかどうかのフラグ. 購入フローからのログイン時にカートが分割された場合にtrueがセットされる.
     *
     * @see SecurityListener
     */
    const SESSION_CART_DIVIDE_FLAG = 'eccube.front.cart.divide';

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        OrderRepository $orderRepository,
        OrderItemTypeRepository $orderItemTypeRepository,
        OrderStatusRepository $orderStatusRepository,
        DeliveryRepository $deliveryRepository,
        PaymentRepository $paymentRepository,
        DeviceTypeRepository $deviceTypeRepository,
        PrefRepository $prefRepository,
        MobileDetector $mobileDetector,
        SessionInterface $session
    ) {
        $this->container = $container;
        $this->orderRepository = $orderRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->paymentRepository = $paymentRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->entityManager = $entityManager;
        $this->prefRepository = $prefRepository;
        $this->mobileDetector = $mobileDetector;
        $this->session = $session;
    }

    /**
     * 購入処理中の受注を生成する.
     *
     * @param Customer $Customer
     * @param $CartItems
     *
     * @return Order
     */
    public function createPurchaseProcessingOrder(Cart $Cart, Customer $Customer)
    {
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PROCESSING);
        $Order = new Order($OrderStatus);

        $preOrderId = $this->createPreOrderId();
        $Order->setPreOrderId($preOrderId);

        // 顧客情報の設定
        $this->setCustomer($Order, $Customer);

        $DeviceType = $this->deviceTypeRepository->find($this->mobileDetector->isMobile() ? DeviceType::DEVICE_TYPE_MB : DeviceType::DEVICE_TYPE_PC);
        $Order->setDeviceType($DeviceType);

        // 明細情報の設定
        $OrderItems = $this->createOrderItemsFromCartItems($Cart->getCartItems());
        $OrderItemsGroupBySaleType = array_reduce($OrderItems, function ($result, $item) {
            /* @var OrderItem $item */
            $saleTypeId = $item->getProductClass()->getSaleType()->getId();
            $result[$saleTypeId][] = $item;

            return $result;
        }, []);

        foreach ($OrderItemsGroupBySaleType as $OrderItems) {
            $Shipping = $this->createShippingFromCustomer($Customer);
            $Shipping->setOrder($Order);
            $this->addOrderItems($Order, $Shipping, $OrderItems);
            $this->setDefaultDelivery($Shipping);
            $this->entityManager->persist($Shipping);
            $Order->addShipping($Shipping);
        }

        $this->setDefaultPayment($Order);
        
        $OptionMemo = $this->createOrderMemoFromOrderItems($Order);
        $UketoriType = $Cart->getUketoriType();
        
        //オプション指定情報をメモに登録する
        $Order->setNote($OptionMemo);
        //受け取り方法を注文に反映する
        $Order->setUketoriType($UketoriType);

        $this->entityManager->persist($Order);

        return $Order;
    }

    /**
     * @param Cart $Cart
     *
     * @return bool
     */
    public function verifyCart(Cart $Cart)
    {
        if (count($Cart->getCartItems()) > 0) {
            $divide = $this->session->get(self::SESSION_CART_DIVIDE_FLAG);
            if ($divide) {
                log_info('ログイン時に販売種別が異なる商品がカートと結合されました。');

                return false;
            }

            return true;
        }

        log_info('カートに商品が入っていません。');

        return false;
    }

    /**
     * 注文手続き画面でログインが必要かどうかの判定
     *
     * @return bool
     */
    public function isLoginRequired()
    {
        // フォームログイン済はログイン不要
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        // Remember Meログイン済の場合はフォームからのログインが必要
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return true;
        }

        // 未ログインだがお客様情報を入力している場合はログイン不要
        if (!$this->getUser() && $this->getNonMember()) {
            return false;
        }

        return true;
    }

    /**
     * 購入処理中の受注を取得する.
     *
     * @param null|string $preOrderId
     *
     * @return null|Order
     */
    public function getPurchaseProcessingOrder($preOrderId = null)
    {
        if (null === $preOrderId) {
            return null;
        }

        return $this->orderRepository->findOneBy([
            'pre_order_id' => $preOrderId,
            'OrderStatus' => OrderStatus::PROCESSING,
        ]);
    }

    /**
     * セッションに保持されている非会員情報を取得する.
     * 非会員購入時に入力されたお客様情報を返す.
     *
     * @return Customer
     */
    public function getNonMember()
    {
        $NonMember = $this->session->get(self::SESSION_NON_MEMBER);
        if ($NonMember && $NonMember->getPref()) {
            $Pref = $this->prefRepository->find($NonMember->getPref()->getId());
            $NonMember->setPref($Pref);
        }

        return $NonMember;
    }

    /**
     * @param Cart $Cart
     * @param Customer $Customer
     *
     * @return Order|null
     */
    public function initializeOrder(Cart $Cart, Customer $Customer)
    {
        // 購入処理中の受注情報を取得
        if ($Order = $this->getPurchaseProcessingOrder($Cart->getPreOrderId())) {
        	//カート商品の受け取り情報を反映する
        	$Order->setUketoriType($Cart->getUketoriType());
        	
            return $Order;
        }

        // 受注情報を作成
        $Order = $this->createPurchaseProcessingOrder($Cart, $Customer);
        $Cart->setPreOrderId($Order->getPreOrderId());

        return $Order;
    }

    public function removeSession()
    {
        $this->session->remove(self::SESSION_ORDER_ID);
        $this->session->remove(self::SESSION_ORDER_ID);
        $this->session->remove(self::SESSION_NON_MEMBER);
        $this->session->remove(self::SESSION_NON_MEMBER_ADDRESSES);
    }

    /**
     * 会員情報の更新日時が受注の作成日時よりも新しければ, 受注の注文者情報を更新する.
     *
     * @param Order $Order
     * @param Customer $Customer
     */
    public function updateCustomerInfo(Order $Order, Customer $Customer)
    {
        if ($Order->getCreateDate() < $Customer->getUpdateDate()) {
            $this->setCustomer($Order, $Customer);
        }
    }

    public function createPreOrderId()
    {
        // ランダムなpre_order_idを作成
        do {
            $preOrderId = sha1(StringUtil::random(32));

            $Order = $this->orderRepository->findOneBy(
                [
                    'pre_order_id' => $preOrderId,
                ]
            );
        } while ($Order);

        return $preOrderId;
    }

    protected function setCustomer(Order $Order, Customer $Customer)
    {
        if ($Customer->getId()) {
            $Order->setCustomer($Customer);
        }

        $Order->copyProperties(
            $Customer,
            [
                'id',
                'create_date',
                'update_date',
                'del_flg',
            ]
        );
    }

    /**
     * @param Collection|ArrayCollection|CartItem[] $CartItems
     *
     * @return OrderItem[]
     */
    protected function createOrderItemsFromCartItems($CartItems)
    {
        $ProductItemType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCT);

        return array_map(function ($item) use ($ProductItemType) {
            /* @var $item CartItem */
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getProductClass();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();
            
            //オプション選択情報
            $OptionDetail     = $item->getOptionDetail();
            
            
            //-------------------------------------------------------
            //↓[Option]オプション別
            $OptionCandleDaiNum  = $item->getOptionCandleDaiNum();
            $OptionCandleSyoNum  = $item->getOptionCandleSyoNum();
            $OptionCandleNo1Num  = $item->getOptionCandleNo1Num();
            $OptionCandleNo2Num  = $item->getOptionCandleNo2Num();
            $OptionCandleNo3Num  = $item->getOptionCandleNo3Num();
            $OptionCandleNo4Num  = $item->getOptionCandleNo4Num();
            $OptionCandleNo5Num  = $item->getOptionCandleNo5Num();
            $OptionCandleNo6Num  = $item->getOptionCandleNo6Num();
            $OptionCandleNo7Num  = $item->getOptionCandleNo7Num();
            $OptionCandleNo8Num  = $item->getOptionCandleNo8Num();
            $OptionCandleNo9Num  = $item->getOptionCandleNo9Num();
            $OptionCandleNo0Num  = $item->getOptionCandleNo0Num();
            $OptionPrintnamePlate1  = $item->getOptionPrintnamePlate1();
            $OptionPrintnamePlate2  = $item->getOptionPrintnamePlate2();
            $OptionPrintnamePlate3  = $item->getOptionPrintnamePlate3();
            $OptionPrintnamePlate4  = $item->getOptionPrintnamePlate4();
            $OptionPrintnamePlate5  = $item->getOptionPrintnamePlate5();
            $OptionDecoIchigoChk  = $item->getOptionDecoIchigoChk();
            $OptionDecoFruitChk  = $item->getOptionDecoFruitChk();
            $OptionDecoNamachocoChk  = $item->getOptionDecoNamachocoChk();
            $OptionDecoEchocoChk  = $item->getOptionDecoEchocoChk();
            $OptionPoriCyuChk  = $item->getOptionPoriCyuChk();
            $OptionPoriDaiChk  = $item->getOptionPoriDaiChk();
            $OptionPoriTokudaiChk  = $item->getOptionPoriTokudaiChk();
            $OptionHousouSentaku  = $item->getOptionHousouSentaku();
            $OptionNoshiKakekata  = $item->getOptionNoshiKakekata();
            $OptionKakehousouSyurui  = $item->getOptionKakehousouSyurui();
            $OptionUwagakiSentaku  = $item->getOptionUwagakiSentaku();
            $OptionPrintnameNosina  = $item->getOptionPrintnameNosina();
            $OptionPlateSentaku  = $item->getOptionPlateSentaku();
	        //↑[Option]オプション別
            //-------------------------------------------------------
            
            
            //オプションによる追加価格
            $AdditionalPrice = $item->getAdditionalPrice();
            

            $OrderItem = new OrderItem();
            $OrderItem
                ->setProduct($Product)
                ->setProductClass($ProductClass)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($item->getQuantity())
                ->setOrderItemType($ProductItemType);

            $ClassCategory1 = $ProductClass->getClassCategory1();
            if (!is_null($ClassCategory1)) {
                $OrderItem->setClasscategoryName1($ClassCategory1->getName());
                $OrderItem->setClassName1($ClassCategory1->getClassName()->getName());
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if (!is_null($ClassCategory2)) {
                $OrderItem->setClasscategoryName2($ClassCategory2->getName());
                $OrderItem->setClassName2($ClassCategory2->getClassName()->getName());
            }
            
            //オプション選択情報
            if (!is_null($OptionDetail)) {
                $OrderItem->setOptionDetail($OptionDetail);
            }
            
            //-------------------------------------------------------
            //↓[Option]オプション別
            if (!is_null($OptionCandleDaiNum)) {
                $OrderItem->setOptionCandleDaiNum($OptionCandleDaiNum);
            }
            if (!is_null($OptionCandleSyoNum)) {
                $OrderItem->setOptionCandleSyoNum($OptionCandleSyoNum);
            }
            if (!is_null($OptionCandleNo1Num)) {
                $OrderItem->setOptionCandleNo1Num($OptionCandleNo1Num);
            }
            if (!is_null($OptionCandleNo2Num)) {
                $OrderItem->setOptionCandleNo2Num($OptionCandleNo2Num);
            }
            if (!is_null($OptionCandleNo3Num)) {
                $OrderItem->setOptionCandleNo3Num($OptionCandleNo3Num);
            }
            if (!is_null($OptionCandleNo4Num)) {
                $OrderItem->setOptionCandleNo4Num($OptionCandleNo4Num);
            }
            if (!is_null($OptionCandleNo5Num)) {
                $OrderItem->setOptionCandleNo5Num($OptionCandleNo5Num);
            }
            if (!is_null($OptionCandleNo6Num)) {
                $OrderItem->setOptionCandleNo6Num($OptionCandleNo6Num);
            }
            if (!is_null($OptionCandleNo7Num)) {
                $OrderItem->setOptionCandleNo7Num($OptionCandleNo7Num);
            }
            if (!is_null($OptionCandleNo8Num)) {
                $OrderItem->setOptionCandleNo8Num($OptionCandleNo8Num);
            }
            if (!is_null($OptionCandleNo9Num)) {
                $OrderItem->setOptionCandleNo9Num($OptionCandleNo9Num);
            }
            if (!is_null($OptionCandleNo0Num)) {
                $OrderItem->setOptionCandleNo0Num($OptionCandleNo0Num);
            }
            if (!is_null($OptionPrintnamePlate1)) {
                $OrderItem->setOptionPrintnamePlate1($OptionPrintnamePlate1);
            }
            if (!is_null($OptionPrintnamePlate2)) {
                $OrderItem->setOptionPrintnamePlate2($OptionPrintnamePlate2);
            }
            if (!is_null($OptionPrintnamePlate3)) {
                $OrderItem->setOptionPrintnamePlate3($OptionPrintnamePlate3);
            }
            if (!is_null($OptionPrintnamePlate4)) {
                $OrderItem->setOptionPrintnamePlate4($OptionPrintnamePlate4);
            }
            if (!is_null($OptionPrintnamePlate5)) {
                $OrderItem->setOptionPrintnamePlate5($OptionPrintnamePlate5);
            }
            if (!is_null($OptionDecoIchigoChk)) {
                $OrderItem->setOptionDecoIchigoChk($OptionDecoIchigoChk);
            }
            if (!is_null($OptionDecoFruitChk)) {
                $OrderItem->setOptionDecoFruitChk($OptionDecoFruitChk);
            }
            if (!is_null($OptionDecoNamachocoChk)) {
                $OrderItem->setOptionDecoNamachocoChk($OptionDecoNamachocoChk);
            }
            if (!is_null($OptionDecoEchocoChk)) {
                $OrderItem->setOptionDecoEchocoChk($OptionDecoEchocoChk);
            }
            if (!is_null($OptionPoriCyuChk)) {
                $OrderItem->setOptionPoriCyuChk($OptionPoriCyuChk);
            }
            if (!is_null($OptionPoriDaiChk)) {
                $OrderItem->setOptionPoriDaiChk($OptionPoriDaiChk);
            }
            if (!is_null($OptionPoriTokudaiChk)) {
                $OrderItem->setOptionPoriTokudaiChk($OptionPoriTokudaiChk);
            }
            if (!is_null($OptionHousouSentaku)) {
                $OrderItem->setOptionHousouSentaku($OptionHousouSentaku);
            }
            if (!is_null($OptionNoshiKakekata)) {
                $OrderItem->setOptionNoshiKakekata($OptionNoshiKakekata);
            }
            if (!is_null($OptionKakehousouSyurui)) {
                $OrderItem->setOptionKakehousouSyurui($OptionKakehousouSyurui);
            }
            if (!is_null($OptionUwagakiSentaku)) {
                $OrderItem->setOptionUwagakiSentaku($OptionUwagakiSentaku);
            }
            if (!is_null($OptionPrintnameNosina)) {
                $OrderItem->setOptionPrintnameNosina($OptionPrintnameNosina);
            }
            if (!is_null($OptionPlateSentaku)) {
                $OrderItem->setOptionPlateSentaku($OptionPlateSentaku);
            }
	        //↑[Option]オプション別
	        //-------------------------------------------------------
	        
	        
            //オプションによる追加価格
            if (!is_null($AdditionalPrice)) {
                $OrderItem->setAdditionalPrice($AdditionalPrice);
            }

            return $OrderItem;
        }, $CartItems instanceof Collection ? $CartItems->toArray() : $CartItems);
    }

    /**
     * @param Customer $Customer
     *
     * @return Shipping
     */
    protected function createShippingFromCustomer(Customer $Customer)
    {
        $Shipping = new Shipping();
        $Shipping
            ->setName01($Customer->getName01())
            ->setName02($Customer->getName02())
            ->setKana01($Customer->getKana01())
            ->setKana02($Customer->getKana02())
            ->setCompanyName($Customer->getCompanyName())
            ->setPhoneNumber($Customer->getPhoneNumber())
            ->setPostalCode($Customer->getPostalCode())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02())
            ->setNameAll($Customer->getNameAll())
            ->setKanaAll($Customer->getKanaAll())
            ->setAddrAll($Customer->getAddrAll())
            ;

        return $Shipping;
    }

    /**
     * @param Shipping $Shipping
     */
    protected function setDefaultDelivery(Shipping $Shipping)
    {
        // 配送商品に含まれる販売種別を抽出.
        $OrderItems = $Shipping->getOrderItems();
        $SaleTypes = [];
        /** @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $ProductClass = $OrderItem->getProductClass();
            $SaleType = $ProductClass->getSaleType();
            $SaleTypes[$SaleType->getId()] = $SaleType;
        }

        // 販売種別に紐づく配送業者を取得.
        $Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);

        // 初期の配送業者を設定
        $Delivery = current($Deliveries);
        $Shipping->setDelivery($Delivery);
        $Shipping->setShippingDeliveryName($Delivery->getName());
    }

    /**
     * @param Order $Order
     */
    protected function setDefaultPayment(Order $Order)
    {
        $OrderItems = $Order->getOrderItems();

        // 受注明細に含まれる販売種別を抽出.
        $SaleTypes = [];
        /** @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $ProductClass = $OrderItem->getProductClass();
            if (is_null($ProductClass)) {
                // 商品明細のみ対象とする. 送料明細等はスキップする.
                continue;
            }
            $SaleType = $ProductClass->getSaleType();
            $SaleTypes[$SaleType->getId()] = $SaleType;
        }

        // 販売種別に紐づく配送業者を抽出
        $Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);

        // 利用可能な支払い方法を抽出.
        $Payments = $this->paymentRepository->findAllowedPayments($Deliveries, true);

        // 初期の支払い方法を設定.
        $Payment = current($Payments);
        if ($Payment) {
            $Order->setPayment($Payment);
            $Order->setPaymentMethod($Payment->getMethod());
        }
    }

    /**
     * @param Order $Order
     * @param Shipping $Shipping
     * @param array $OrderItems
     */
    protected function addOrderItems(Order $Order, Shipping $Shipping, array $OrderItems)
    {
        foreach ($OrderItems as $OrderItem) {
            $Shipping->addOrderItem($OrderItem);
            $Order->addOrderItem($OrderItem);
            $OrderItem->setOrder($Order);
            $OrderItem->setShipping($Shipping);
        }
    }
    
    /**
     * @param Order $Order
     *
     * @return string
     */
    protected function createOrderMemoFromOrderItems($Order)
    {
        $oprion_memo = '';
        
        $OrderItems = $Order->getOrderItems();
        /** @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $per_product_name = $OrderItem->getProductName()."\n";
            $per_option_detail = $OrderItem->getOptionDetail()."\n";
            $oprion_memo = $oprion_memo.$per_product_name.$per_option_detail;
        }

        return $oprion_memo;
    }
    
}
