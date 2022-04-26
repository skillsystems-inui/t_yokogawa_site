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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ClassCategory;
use Eccube\Repository\CartRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Service\Cart\CartItemAllocator;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Util\StringUtil;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CartService
{
    /**
     * @var Cart[]
     */
    protected $carts;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ItemHolderInterface
     *
     * @deprecated
     */
    protected $cart;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;


    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * @var CartItemComparator
     */
    protected $cartItemComparator;

    /**
     * @var CartItemAllocator
     */
    protected $cartItemAllocator;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * CartService constructor.
     *
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     * @param ProductClassRepository $productClassRepository
     * @param ClassCategoryRepository $classCategoryRepository
     * @param CartItemComparator $cartItemComparator
     * @param CartItemAllocator $cartItemAllocator
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        ProductClassRepository $productClassRepository,
        ClassCategoryRepository $classCategoryRepository,
        CartRepository $cartRepository,
        CartItemComparator $cartItemComparator,
        CartItemAllocator $cartItemAllocator,
        OrderRepository $orderRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->productClassRepository = $productClassRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->cartRepository = $cartRepository;
        $this->cartItemComparator = $cartItemComparator;
        $this->cartItemAllocator = $cartItemAllocator;
        $this->orderRepository = $orderRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * 現在のカートの配列を取得する.
     *
     * 本サービスのインスタンスのメンバーが空の場合は、DBまたはセッションからカートを取得する
     *
     * @param bool $empty_delete true の場合、商品明細が空のカートが存在した場合は削除する
     *
     * @return Cart[]
     */
    public function getCarts($empty_delete = false)
    {
        if (null !== $this->carts) {
            if ($empty_delete) {
                $cartKeys = [];
                foreach (array_keys($this->carts) as $index) {
                    $Cart = $this->carts[$index];
                    if ($Cart->getItems()->count() > 0) {
                        $cartKeys[] = $Cart->getCartKey();
                    } else {
                        $this->entityManager->remove($this->carts[$index]);
                        $this->entityManager->flush($this->carts[$index]);
                        unset($this->carts[$index]);
                    }
                }

                $this->session->set('cart_keys', $cartKeys);
            }

            return $this->carts;
        }

        if ($this->getUser()) {
            $this->carts = $this->getPersistedCarts();
        } else {
            $this->carts = $this->getSessionCarts();
        }

        return $this->carts;
    }

    /**
     * 永続化されたカートを返す
     *
     * @return Cart[]
     */
    public function getPersistedCarts()
    {
        return $this->cartRepository->findBy(['Customer' => $this->getUser()]);
    }

    /**
     * セッションにあるカートを返す
     *
     * @return Cart[]
     */
    public function getSessionCarts()
    {
        $cartKeys = $this->session->get('cart_keys', []);

        if (empty($cartKeys)) {
            return [];
        }

        return $this->cartRepository->findBy(['cart_key' => $cartKeys], ['id' => 'DESC']);
    }

    /**
     * 会員が保持する永続化されたカートと、非会員時のカートをマージする.
     */
    public function mergeFromPersistedCart()
    {
        $CartItems = [];
        foreach ($this->getPersistedCarts() as $Cart) {
            $CartItems = $this->mergeCartItems($Cart->getCartItems(), $CartItems);
        }

        // セッションにある非会員カートとDBから取得した会員カートをマージする.
        foreach ($this->getSessionCarts() as $Cart) {
            $CartItems = $this->mergeCartItems($Cart->getCartItems(), $CartItems);
        }

        $this->restoreCarts($CartItems);
    }

    /**
     * @return Cart|null
     */
    public function getCart()
    {
        $Carts = $this->getCarts();

        if (empty($Carts)) {
            return null;
        }

        $cartKeys = $this->session->get('cart_keys', []);
        $Cart = null;
        if (count($cartKeys) > 0) {
            foreach ($Carts as $cart) {
                if ($cart->getCartKey() === current($cartKeys)) {
                    $Cart = $cart;
                    break;
                }
            }
        } else {
            $Cart = $Carts[0];
        }

        return $Cart;
    }

    /**
     * @param CartItem[] $cartItems
     *
     * @return CartItem[]
     */
    protected function mergeAllCartItems($cartItems = [])
    {
        /** @var CartItem[] $allCartItems */
        $allCartItems = [];

        foreach ($this->getCarts() as $Cart) {
            $allCartItems = $this->mergeCartItems($Cart->getCartItems(), $allCartItems);
        }

        return $this->mergeCartItems($cartItems, $allCartItems);
    }

    /**
     * @param $cartItems
     * @param $allCartItems
     *
     * @return array
     */
    protected function mergeCartItems($cartItems, $allCartItems)
    {
        foreach ($cartItems as $item) {
            $itemExists = false;
            foreach ($allCartItems as $itemInArray) {
                // 同じ明細があればマージする
                if ($this->cartItemComparator->compare($item, $itemInArray)) {
                    $itemInArray->setQuantity($itemInArray->getQuantity() + $item->getQuantity());
                    $itemExists = true;
                    break;
                }
            }
            if (!$itemExists) {
                $allCartItems[] = $item;
            }
        }

        return $allCartItems;
    }

    protected function restoreCarts($cartItems)
    {
        foreach ($this->getCarts() as $Cart) {
            foreach ($Cart->getCartItems() as $i) {
                $this->entityManager->remove($i);
                $this->entityManager->flush($i);
            }
            $this->entityManager->remove($Cart);
            $this->entityManager->flush($Cart);
        }
        $this->carts = [];

        /** @var Cart[] $Carts */
        $Carts = [];

        foreach ($cartItems as $item) {
            $allocatedId = $this->cartItemAllocator->allocate($item);
            $cartKey = $this->createCartKey($allocatedId, $this->getUser());

            if (isset($Carts[$cartKey])) {
                $Cart = $Carts[$cartKey];
                
                
                log_info(
		            'カートに追加',
		            [
		                'item' => $item,
		            ]
		        );
                
                
                $Cart->addCartItem($item);
                $item->setCart($Cart);
            } else {
                /** @var Cart $Cart */
                $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
                if ($Cart) {
                    foreach ($Cart->getCartItems() as $i) {
                        
                        
                        log_info(
				            'カートから外す',
				            [
				                'item' => $i,
				            ]
				        );
                        
                        $this->entityManager->remove($i);
                        $this->entityManager->flush($i);
                    }
                    $this->entityManager->remove($Cart);
                    $this->entityManager->flush($Cart);
                }
                $Cart = new Cart();
                $Cart->setCartKey($cartKey);
                $Cart->addCartItem($item);
                $item->setCart($Cart);
                $Carts[$cartKey] = $Cart;
            }
        }

        $this->carts = array_values($Carts);
    }

    /**
     * カートに商品を追加します.
     *
     * @param $ProductClass ProductClass 商品規格
     * @param $quantity int 数量
     *
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProduct($ProductClass, $quantity = 1)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->entityManager
                ->getRepository(ProductClass::class)
                ->find($ProductClassId);
            if (is_null($ProductClass)) {
                return false;
            }
        }

        $ClassCategory1 = $ProductClass->getClassCategory1();
        
        if ($ClassCategory1 && !$ClassCategory1->isVisible()) {
            return false;
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
            return false;
        }

        $newItem = new CartItem();
        $newItem->setQuantity($quantity);
        $newItem->setPrice($ProductClass->getPrice02IncTax());
        $newItem->setProductClass($ProductClass);

        $allCartItems = $this->mergeAllCartItems([$newItem]);
        $this->restoreCarts($allCartItems);

        return true;
    }


    /**
     * カートに商品とそのオプションを追加します.
     *
     * @param $ProductClass ProductClass 商品規格
     * @param $OptionDetail オプション選択情報
     * @param $option_candle_dai_num オプション別
     * @param $option_candle_syo_num オプション別
     * @param $option_candle_no1_num オプション別
     * @param $option_candle_no2_num オプション別
     * @param $option_candle_no3_num オプション別
     * @param $option_candle_no4_num オプション別
     * @param $option_candle_no5_num オプション別
     * @param $option_candle_no6_num オプション別
     * @param $option_candle_no7_num オプション別
     * @param $option_candle_no8_num オプション別
     * @param $option_candle_no9_num オプション別
     * @param $option_candle_no0_num オプション別
     * @param $option_printname_plate1 オプション別
     * @param $option_printname_plate2 オプション別
     * @param $option_printname_plate3 オプション別
     * @param $option_printname_plate4 オプション別
     * @param $option_printname_plate5 オプション別
     * @param $option_deco_ichigo_chk    オプション別
     * @param $option_deco_fruit_chk     オプション別
     * @param $option_deco_namachoco_chk オプション別
     * @param $option_deco_echoco_chk    オプション別
     * @param $option_pori_cyu_chk      オプション別
     * @param $option_pori_dai_chk      オプション別
     * @param $option_pori_tokudai_chk  オプション別
     * @param $option_housou_sentaku    オプション別
     * @param $option_noshi_kakekata    オプション別
     * @param $option_kakehousou_syurui オプション別
     * @param $option_uwagaki_sentaku   オプション別
     * @param $option_printname_nosina  オプション別
     * @param $option_plate_sentaku  オプション別
     * @param $additional_option_price 追加料金
     * @param $quantity int 数量
     *
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProductOption($ProductClass, 
                                     $OptionDetail,
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
                                     $quantity = 1)
    {
        
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->entityManager
                ->getRepository(ProductClass::class)
                ->find($ProductClassId);
            if (is_null($ProductClass)) {
                return false;
            }
        }
        
        $ClassCategory1 = $ProductClass->getClassCategory1();
        
        if ($ClassCategory1 && !$ClassCategory1->isVisible()) {
            return false;
        }
        
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
            return false;
        }

        $newItem = new CartItem();
        $newItem->setQuantity($quantity);
        $newItem->setPrice($ProductClass->getPrice02IncTax());
        $newItem->setProductClass($ProductClass);
		
        //[Option]オプション情報を登録する
        $newItem->setOptionDetail($OptionDetail);
        
        //↓[Option]オプション別
        $newItem->setOptionCandleDaiNum($option_candle_dai_num);
        $newItem->setOptionCandleSyoNum($option_candle_syo_num);
        $newItem->setOptionCandleNo1Num($option_candle_no1_num);
        $newItem->setOptionCandleNo2Num($option_candle_no2_num);
        $newItem->setOptionCandleNo3Num($option_candle_no3_num);
        $newItem->setOptionCandleNo4Num($option_candle_no4_num);
        $newItem->setOptionCandleNo5Num($option_candle_no5_num);
        $newItem->setOptionCandleNo6Num($option_candle_no6_num);
        $newItem->setOptionCandleNo7Num($option_candle_no7_num);
        $newItem->setOptionCandleNo8Num($option_candle_no8_num);
        $newItem->setOptionCandleNo9Num($option_candle_no9_num);
        $newItem->setOptionCandleNo0Num($option_candle_no0_num);
        $newItem->setOptionPrintnamePlate1($option_printname_plate1);
        $newItem->setOptionPrintnamePlate2($option_printname_plate2);
        $newItem->setOptionPrintnamePlate3($option_printname_plate3);
        $newItem->setOptionPrintnamePlate4($option_printname_plate4);
        $newItem->setOptionPrintnamePlate5($option_printname_plate5);
        $newItem->setOptionDecoIchigoChk($option_deco_ichigo_chk);
        $newItem->setOptionDecoFruitChk($option_deco_fruit_chk);
        $newItem->setOptionDecoNamachocoChk($option_deco_namachoco_chk);
        $newItem->setOptionDecoEchocoChk($option_deco_echoco_chk);
        $newItem->setOptionPoriCyuChk($option_pori_cyu_chk);
        $newItem->setOptionPoriDaiChk($option_pori_dai_chk);
        $newItem->setOptionPoriTokudaiChk($option_pori_tokudai_chk);
        $newItem->setOptionHousouSentaku($option_housou_sentaku);
        $newItem->setOptionNoshiKakekata($option_noshi_kakekata);
        $newItem->setOptionKakehousouSyurui($option_kakehousou_syurui);
        $newItem->setOptionUwagakiSentaku($option_uwagaki_sentaku);
        $newItem->setOptionPrintnameNosina($option_printname_nosina);
        $newItem->setOptionPlateSentaku($option_plate_sentaku);
        //↑[Option]オプション別

        
		//[Option]追加料金を登録する
		$newItem->setAdditionalPrice($additional_option_price);
		
        $allCartItems = $this->mergeAllCartItems([$newItem]);
        $this->restoreCarts($allCartItems);

        return true;
    }

    public function removeProduct($ProductClass)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->entityManager
                ->getRepository(ProductClass::class)
                ->find($ProductClassId);
            if (is_null($ProductClass)) {
                return false;
            }
        }

        $removeItem = new CartItem();
        $removeItem->setPrice($ProductClass->getPrice02IncTax());
        $removeItem->setProductClass($ProductClass);

        $allCartItems = $this->mergeAllCartItems();
        $foundIndex = -1;
        foreach ($allCartItems as $index => $itemInCart) {
            if ($this->cartItemComparator->compare($itemInCart, $removeItem)) {
                $foundIndex = $index;
                break;
            }
        }

        array_splice($allCartItems, $foundIndex, 1);
        $this->restoreCarts($allCartItems);

        return true;
    }

    public function save()
    {
        $cartKeys = [];
        foreach ($this->carts as $Cart) {
            $Cart->setCustomer($this->getUser());
            $this->entityManager->persist($Cart);
            foreach ($Cart->getCartItems() as $item) {
                $this->entityManager->persist($item);
                $this->entityManager->flush($item);
            }
            $this->entityManager->flush($Cart);
            $cartKeys[] = $Cart->getCartKey();
        }

        $this->session->set('cart_keys', $cartKeys);

        return;
    }

    /**
     * @param  string $pre_order_id
     *
     * @return \Eccube\Service\CartService
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->getCart()->setPreOrderId($pre_order_id);

        return $this;
    }
    
    /**
     * @param  string $uketori_type
     *
     * @return \Eccube\Service\CartService
     */
    public function setUketoriType($uketori_type)
    {
        $this->getCart()->setUketoriType($uketori_type);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPreOrderId()
    {
        $Cart = $this->getCart();
        if (!empty($Cart)) {
            return $Cart->getPreOrderId();
        }

        return null;
    }

    /**
     * @return \Eccube\Service\CartService
     */
    public function clear()
    {
        $Carts = $this->getCarts();
        if (!empty($Carts)) {
            $removed = $this->getCart();
            if ($removed && UnitOfWork::STATE_MANAGED === $this->entityManager->getUnitOfWork()->getEntityState($removed)) {
                $this->entityManager->remove($removed);
                $this->entityManager->flush($removed);

                $cartKeys = [];
                foreach ($Carts as $key => $Cart) {
                    // テーブルから削除されたカートを除外する
                    if ($Cart == $removed) {
                        unset($Carts[$key]);
                    }
                    $cartKeys[] = $Cart->getCartKey();
                }
                $this->session->set('cart_keys', $cartKeys);
                // 注文完了のカートキーをセッションから削除する
                $this->session->remove('cart_key');
                $this->carts = $this->cartRepository->findBy(['cart_key' => $cartKeys], ['id' => 'DESC']);
            }
        }

        return $this;
    }

    /**
     * @param CartItemComparator $cartItemComparator
     */
    public function setCartItemComparator($cartItemComparator)
    {
        $this->cartItemComparator = $cartItemComparator;
    }

    /**
     * カートキーで指定したインデックスにあるカートを優先にする
     *
     * @param string $cartKey カートキー
     */
    public function setPrimary($cartKey)
    {
        $Carts = $this->getCarts();
        $primary = $Carts[0];
        $index = 0;
        foreach ($Carts as $key => $Cart) {
            if ($Cart->getCartKey() === $cartKey) {
                $index = $key;
                $primary = $Carts[$index];
                break;
            }
        }
        $prev = $Carts[0];
        array_splice($Carts, 0, 1, [$primary]);
        array_splice($Carts, $index, 1, [$prev]);
        $this->carts = $Carts;
        $this->save();
    }

    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    /**
     * @param string $allocatedId
     */
    protected function createCartKey($allocatedId, Customer $Customer = null)
    {
        if ($Customer instanceof Customer) {
            return $Customer->getId().'_'.$allocatedId;
        }

        if ($this->session->has('cart_key_prefix')) {
            return $this->session->get('cart_key_prefix').'_'.$allocatedId;
        }

        do {
            $random = StringUtil::random(32);
            $cartKey = $random.'_'.$allocatedId;
            $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
        } while ($Cart);

        $this->session->set('cart_key_prefix', $random);

        return $cartKey;
    }
}
