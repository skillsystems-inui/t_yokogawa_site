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
            
            /* @var $OptionCategory1 \Eccube\Entity\ClassCategory */
            $OptionCategory1 = $item->getClassCategory1();
            
            /* @var $OptionCategory2 \Eccube\Entity\ClassCategory */
            $OptionCategory2 = $item->getClassCategory2();
            
            /* @var $OptionCategory3 \Eccube\Entity\ClassCategory */
            $OptionCategory3 = $item->getClassCategory3();
            
            /* @var $OptionCategory4 \Eccube\Entity\ClassCategory */
            $OptionCategory4 = $item->getClassCategory4();
            
            /* @var $OptionCategory5 \Eccube\Entity\ClassCategory */
            $OptionCategory5 = $item->getClassCategory5();
            
            /* @var $OptionCategory6 \Eccube\Entity\ClassCategory */
            $OptionCategory6 = $item->getClassCategory6();
            
            /* @var $OptionCategory7 \Eccube\Entity\ClassCategory */
            $OptionCategory7 = $item->getClassCategory7();
            
            /* @var $OptionCategory8 \Eccube\Entity\ClassCategory */
            $OptionCategory8 = $item->getClassCategory8();
            
            /* @var $OptionCategory9 \Eccube\Entity\ClassCategory */
            $OptionCategory9 = $item->getClassCategory9();
            
            /* @var $OptionCategory10 \Eccube\Entity\ClassCategory */
            $OptionCategory10 = $item->getClassCategory10();
            
            //名入れ(プレート)
            $OptionPrintPlate = $item->getPrintnamePlate();
            
            //名入れ(熨斗)
            $OptionPrintNoshi = $item->getPrintnameNoshi();
            
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
            
            //注文に選択したオプションを追加
            if (!is_null($OptionCategory1)) {
                $OrderItem->setOptionCategoryName1($OptionCategory1->getName());
                $OrderItem->setOptionName1($OptionCategory1->getClassName()->getName());
            }
            if (!is_null($OptionCategory2)) {
                $OrderItem->setOptionCategoryName2($OptionCategory2->getName());
                $OrderItem->setOptionName2($OptionCategory2->getClassName()->getName());
            }
            if (!is_null($OptionCategory3)) {
                $OrderItem->setOptionCategoryName3($OptionCategory3->getName());
                $OrderItem->setOptionName3($OptionCategory3->getClassName()->getName());
            }
            if (!is_null($OptionCategory4)) {
                $OrderItem->setOptionCategoryName4($OptionCategory4->getName());
                $OrderItem->setOptionName4($OptionCategory4->getClassName()->getName());
            }
            if (!is_null($OptionCategory5)) {
                $OrderItem->setOptionCategoryName5($OptionCategory5->getName());
                $OrderItem->setOptionName5($OptionCategory5->getClassName()->getName());
            }
            if (!is_null($OptionCategory6)) {
                $OrderItem->setOptionCategoryName6($OptionCategory6->getName());
                $OrderItem->setOptionName6($OptionCategory6->getClassName()->getName());
            }
            if (!is_null($OptionCategory7)) {
                $OrderItem->setOptionCategoryName7($OptionCategory7->getName());
                $OrderItem->setOptionName7($OptionCategory7->getClassName()->getName());
            }
            if (!is_null($OptionCategory8)) {
                $OrderItem->setOptionCategoryName8($OptionCategory8->getName());
                $OrderItem->setOptionName8($OptionCategory8->getClassName()->getName());
            }
            if (!is_null($OptionCategory9)) {
                $OrderItem->setOptionCategoryName9($OptionCategory9->getName());
                $OrderItem->setOptionName9($OptionCategory9->getClassName()->getName());
            }
            if (!is_null($OptionCategory10)) {
                $OrderItem->setOptionCategoryName10($OptionCategory10->getName());
                $OrderItem->setOptionName10($OptionCategory10->getClassName()->getName());
            }
            
            //名入れ(プレート)
            if (!is_null($OptionPrintPlate)) {
                $OrderItem->setOptionPrintnamePlate($OptionPrintPlate);
            }
            //名入れ(熨斗)
            if (!is_null($OptionPrintNoshi)) {
                $OrderItem->setOptionPrintnameNoshi($OptionPrintNoshi);
            }
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
}
