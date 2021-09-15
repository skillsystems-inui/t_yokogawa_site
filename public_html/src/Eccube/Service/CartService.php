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
                $Cart->addCartItem($item);
                $item->setCart($Cart);
            } else {
                /** @var Cart $Cart */
                $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
                if ($Cart) {
                    foreach ($Cart->getCartItems() as $i) {
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
     * @param $ClassCategoryOption1  ClassCategory 商品オプション1
     * @param $ClassCategoryOption2  ClassCategory 商品オプション2
     * @param $ClassCategoryOption3  ClassCategory 商品オプション3
     * @param $ClassCategoryOption4  ClassCategory 商品オプション4
     * @param $ClassCategoryOption5  ClassCategory 商品オプション5
     * @param $ClassCategoryOption6  ClassCategory 商品オプション6
     * @param $ClassCategoryOption7  ClassCategory 商品オプション7
     * @param $ClassCategoryOption8  ClassCategory 商品オプション8
     * @param $ClassCategoryOption9  ClassCategory 商品オプション9
     * @param $ClassCategoryOption10 ClassCategory 商品オプション10
     * @param $PrintnamePlate 名付け(プレート)
     * @param $PrintnameNoshi 名付け(熨斗)
     * @param $AdditionalOptionPrice オプションによる追加価格　additional_option_price
     * @param $quantity int 数量
     *
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProductOption($ProductClass, 
                                     $ClassCategoryOption1, 
                                     $ClassCategoryOption2, 
                                     $ClassCategoryOption3, 
                                     $ClassCategoryOption4, 
                                     $ClassCategoryOption5, 
                                     $ClassCategoryOption6, 
                                     $ClassCategoryOption7, 
                                     $ClassCategoryOption8, 
                                     $ClassCategoryOption9, 
                                     $ClassCategoryOption10, 
                                     $PrintnamePlate,
                                     $PrintnameNoshi,
                                     $AdditionalOptionPrice,
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
        
        
        //オプション1用オブジェクト取得
        if (!is_null($ClassCategoryOption1)) {
	        if (!$ClassCategoryOption1 instanceof ClassCategory) {
	            
	            $ClassCategoryOption1Id = $ClassCategoryOption1;
	            
	            $ClassCategoryOption1 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption1Id);
	        }
        }
        //オプション2用オブジェクト取得
        if (!is_null($ClassCategoryOption2)) {
	        if (!$ClassCategoryOption2 instanceof ClassCategory) {
	            
	            $ClassCategoryOption2Id = $ClassCategoryOption2;
	            
	            $ClassCategoryOption2 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption2Id);
	        }
        }
        //オプション3用オブジェクト取得
        if (!is_null($ClassCategoryOption3)) {
	        if (!$ClassCategoryOption3 instanceof ClassCategory) {
	            
	            $ClassCategoryOption3Id = $ClassCategoryOption3;
	            
	            $ClassCategoryOption3 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption3Id);
	        }
        }
        //オプション4用オブジェクト取得
        if (!is_null($ClassCategoryOption4)) {
	        if (!$ClassCategoryOption4 instanceof ClassCategory) {
	            
	            $ClassCategoryOption4Id = $ClassCategoryOption4;
	            
	            $ClassCategoryOption4 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption4Id);
	        }
        }
        //オプション5用オブジェクト取得
        if (!is_null($ClassCategoryOption5)) {
	        if (!$ClassCategoryOption5 instanceof ClassCategory) {
	            
	            $ClassCategoryOption5Id = $ClassCategoryOption5;
	            
	            $ClassCategoryOption5 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption5Id);
	        }
        }
        //オプション6用オブジェクト取得
        if (!is_null($ClassCategoryOption6)) {
	        if (!$ClassCategoryOption6 instanceof ClassCategory) {
	            
	            $ClassCategoryOption6Id = $ClassCategoryOption6;
	            
	            $ClassCategoryOption6 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption6Id);
	        }
        }
        //オプション7用オブジェクト取得
        if (!is_null($ClassCategoryOption7)) {
	        if (!$ClassCategoryOption7 instanceof ClassCategory) {
	            
	            $ClassCategoryOption7Id = $ClassCategoryOption7;
	            
	            $ClassCategoryOption7 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption7Id);
	        }
        }
        //オプション8用オブジェクト取得
        if (!is_null($ClassCategoryOption8)) {
	        if (!$ClassCategoryOption8 instanceof ClassCategory) {
	            
	            $ClassCategoryOption8Id = $ClassCategoryOption8;
	            
	            $ClassCategoryOption8 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption8Id);
	        }
        }
        //オプション9用オブジェクト取得
        if (!is_null($ClassCategoryOption9)) {
	        if (!$ClassCategoryOption9 instanceof ClassCategory) {
	            
	            $ClassCategoryOption9Id = $ClassCategoryOption9;
	            
	            $ClassCategoryOption9 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption9Id);
	        }
        }
        //オプション10用オブジェクト取得
        if (!is_null($ClassCategoryOption10)) {
	        if (!$ClassCategoryOption10 instanceof ClassCategory) {
	            
	            $ClassCategoryOption10Id = $ClassCategoryOption10;
	            
	            $ClassCategoryOption10 = $this->entityManager
	                ->getRepository(ClassCategory::class)
	                ->find($ClassCategoryOption10Id);
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
		
		//[Option]オプション1をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption1)) {
            $newItem->setClassCategory1($ClassCategoryOption1);
        }
        //[Option]オプション2をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption2)) {
            $newItem->setClassCategory2($ClassCategoryOption2);
        }
        //[Option]オプション3をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption3)) {
            $newItem->setClassCategory3($ClassCategoryOption3);
        }
        //[Option]オプション4をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption4)) {
            $newItem->setClassCategory4($ClassCategoryOption4);
        }
        //[Option]オプション5をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption5)) {
            $newItem->setClassCategory5($ClassCategoryOption5);
        }
        //[Option]オプション6をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption6)) {
            $newItem->setClassCategory6($ClassCategoryOption6);
        }
        //[Option]オプション7をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption7)) {
            $newItem->setClassCategory7($ClassCategoryOption7);
        }
        //[Option]オプション8をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption8)) {
            $newItem->setClassCategory8($ClassCategoryOption8);
        }
        //[Option]オプション9をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption9)) {
            $newItem->setClassCategory9($ClassCategoryOption9);
        }
        //[Option]オプション10をCartItemに登録する(未指定(null)でなければ実施する)
        if (!is_null($ClassCategoryOption10)) {
            $newItem->setClassCategory10($ClassCategoryOption10);
        }
        
        //[Option]名付け(プレート)をCartItemに登録する
        $newItem->setPrintnamePlate($PrintnamePlate);
        
        //[Option]名付け(熨斗)をCartItemに登録する
        $newItem->setPrintnameNoshi($PrintnameNoshi);
        
        //[Option]オプションによる追加価格をCartItemに登録する
        $newItem->setAdditionalPrice($AdditionalOptionPrice);
        
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
