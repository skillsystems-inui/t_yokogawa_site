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

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\CartItem')) {
    /**
     * CartItem
     *
     * @ORM\Table(name="dtb_cart_item")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\CartItemRepository")
     */
    class CartItem extends \Eccube\Entity\AbstractEntity implements ItemInterface
    {
        use PointRateTrait;

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="price", type="decimal", precision=12, scale=2, options={"default":0})
         */
        private $price = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="quantity", type="decimal", precision=10, scale=0, options={"default":0})
         */
        private $quantity = 0;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="printname_plate", type="string", length=255, nullable=true)
         */
        private $printname_plate;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="printname_noshi", type="string", length=255, nullable=true)
         */
        private $printname_noshi;
        
        /**
         * @var \Eccube\Entity\ProductClass
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ProductClass")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_class_id", referencedColumnName="id")
         * })
         */
        private $ProductClass;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id1", referencedColumnName="id")
         * })
         */
        private $ClassCategory1;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id2", referencedColumnName="id")
         * })
         */
        private $ClassCategory2;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id3", referencedColumnName="id")
         * })
         */
        private $ClassCategory3;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id4", referencedColumnName="id")
         * })
         */
        private $ClassCategory4;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id5", referencedColumnName="id")
         * })
         */
        private $ClassCategory5;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id6", referencedColumnName="id")
         * })
         */
        private $ClassCategory6;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id7", referencedColumnName="id")
         * })
         */
        private $ClassCategory7;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id8", referencedColumnName="id")
         * })
         */
        private $ClassCategory8;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id9", referencedColumnName="id")
         * })
         */
        private $ClassCategory9;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="option_class_category_id10", referencedColumnName="id")
         * })
         */
        private $ClassCategory10;
        
        /**
         * @var string
         *
         * @ORM\Column(name="additional_price", type="decimal", precision=12, scale=2, options={"default":0})
         */
        private $additional_price = 0;

        /**
         * @var \Eccube\Entity\Cart
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Cart", inversedBy="CartItems")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="cart_id", referencedColumnName="id", onDelete="CASCADE")
         * })
         */
        private $Cart;

        /**
         * sessionのシリアライズのために使われる
         *
         * @var int
         */
        private $product_class_id;
        
        /**
         * オプション選択用
         *
         * @var int
         */
        private $option_class_category_id1;
        
        public function __sleep()
        {
            return ['product_class_id', 'option_class_category_id1', 'price', 'quantity' ];
        }

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }
        
        /**
         * @param  integer  $price
         *
         * @return CartItem
         */
        public function setPrice($price)
        {
            $this->price = $price;

            return $this;
        }

        /**
         * @return string
         */
        public function getPrice()
        {
            return $this->price;
        }

        /**
         * @param  integer  $quantity
         *
         * @return CartItem
         */
        public function setQuantity($quantity)
        {
            $this->quantity = $quantity;

            return $this;
        }

        /**
         * @return string
         */
        public function getQuantity()
        {
            return $this->quantity;
        }

        /**
         * @return integer
         */
        public function getTotalPrice()
        {
            return ($this->getPrice() + $this->getAdditionalPrice()) * $this->getQuantity();
        }

        /**
         * 商品明細かどうか.
         *
         * @return boolean 商品明細の場合 true
         */
        public function isProduct()
        {
            return true;
        }

        /**
         * 送料明細かどうか.
         *
         * @return boolean 送料明細の場合 true
         */
        public function isDeliveryFee()
        {
            return false;
        }

        /**
         * 手数料明細かどうか.
         *
         * @return boolean 手数料明細の場合 true
         */
        public function isCharge()
        {
            return false;
        }

        /**
         * 値引き明細かどうか.
         *
         * @return boolean 値引き明細の場合 true
         */
        public function isDiscount()
        {
            return false;
        }

        /**
         * 税額明細かどうか.
         *
         * @return boolean 税額明細の場合 true
         */
        public function isTax()
        {
            return false;
        }

        /**
         * ポイント明細かどうか.
         *
         * @return boolean ポイント明細の場合 true
         */
        public function isPoint()
        {
            return false;
        }

        public function getOrderItemType()
        {
            // TODO OrderItemType::PRODUCT
            $ItemType = new \Eccube\Entity\Master\OrderItemType();

            return $ItemType;
        }

        /**
         * @param ProductClass $ProductClass
         *
         * @return $this
         */
        public function setProductClass(ProductClass $ProductClass)
        {
            $this->ProductClass = $ProductClass;

            $this->product_class_id = is_object($ProductClass) ?
            $ProductClass->getId() : null;
            
            return $this;
        }

        /**
         * @return ProductClass
         */
        public function getProductClass()
        {
            return $this->ProductClass;
        }

        /**
         * @return int|null
         */
        public function getProductClassId()
        {
            return $this->product_class_id;
        }



        /**
         * @param ClassCategory $ClassCategory1
         *
         * @return $this
         */
        public function setClassCategory1(ClassCategory $ClassCategory1)
        {
            $this->ClassCategory1 = $ClassCategory1;

            $this->option_class_category_id1 = is_object($ClassCategory1) ?
            $ClassCategory1->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory1()
        {
            return $this->ClassCategory1;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId1()
        {
            return $this->option_class_category_id1;
        }

        /**
         * @param ClassCategory $ClassCategory2
         *
         * @return $this
         */
        public function setClassCategory2(ClassCategory $ClassCategory2)
        {
            $this->ClassCategory2 = $ClassCategory2;

            $this->option_class_category_id2 = is_object($ClassCategory2) ?
            $ClassCategory2->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory2()
        {
            return $this->ClassCategory2;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId2()
        {
            return $this->option_class_category_id2;
        }

        /**
         * @param ClassCategory $ClassCategory3
         *
         * @return $this
         */
        public function setClassCategory3(ClassCategory $ClassCategory3)
        {
            $this->ClassCategory3 = $ClassCategory3;

            $this->option_class_category_id3 = is_object($ClassCategory3) ?
            $ClassCategory3->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory3()
        {
            return $this->ClassCategory3;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId3()
        {
            return $this->option_class_category_id3;
        }

        /**
         * @param ClassCategory $ClassCategory4
         *
         * @return $this
         */
        public function setClassCategory4(ClassCategory $ClassCategory4)
        {
            $this->ClassCategory4 = $ClassCategory4;

            $this->option_class_category_id4 = is_object($ClassCategory4) ?
            $ClassCategory4->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory4()
        {
            return $this->ClassCategory4;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId4()
        {
            return $this->option_class_category_id4;
        }

        /**
         * @param ClassCategory $ClassCategory5
         *
         * @return $this
         */
        public function setClassCategory5(ClassCategory $ClassCategory5)
        {
            $this->ClassCategory5 = $ClassCategory5;

            $this->option_class_category_id5 = is_object($ClassCategory5) ?
            $ClassCategory5->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory5()
        {
            return $this->ClassCategory5;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId5()
        {
            return $this->option_class_category_id5;
        }

        /**
         * @param ClassCategory $ClassCategory6
         *
         * @return $this
         */
        public function setClassCategory6(ClassCategory $ClassCategory6)
        {
            $this->ClassCategory6 = $ClassCategory6;

            $this->option_class_category_id6 = is_object($ClassCategory6) ?
            $ClassCategory6->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory6()
        {
            return $this->ClassCategory6;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId6()
        {
            return $this->option_class_category_id6;
        }

        /**
         * @param ClassCategory $ClassCategory7
         *
         * @return $this
         */
        public function setClassCategory7(ClassCategory $ClassCategory7)
        {
            $this->ClassCategory7 = $ClassCategory7;

            $this->option_class_category_id7 = is_object($ClassCategory7) ?
            $ClassCategory7->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory7()
        {
            return $this->ClassCategory7;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId7()
        {
            return $this->option_class_category_id7;
        }

        /**
         * @param ClassCategory $ClassCategory8
         *
         * @return $this
         */
        public function setClassCategory8(ClassCategory $ClassCategory8)
        {
            $this->ClassCategory8 = $ClassCategory8;

            $this->option_class_category_id8 = is_object($ClassCategory8) ?
            $ClassCategory8->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory8()
        {
            return $this->ClassCategory8;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId8()
        {
            return $this->option_class_category_id8;
        }

        /**
         * @param ClassCategory $ClassCategory9
         *
         * @return $this
         */
        public function setClassCategory9(ClassCategory $ClassCategory9)
        {
            $this->ClassCategory9 = $ClassCategory9;

            $this->option_class_category_id9 = is_object($ClassCategory9) ?
            $ClassCategory9->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory9()
        {
            return $this->ClassCategory9;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId9()
        {
            return $this->option_class_category_id9;
        }

        /**
         * @param ClassCategory $ClassCategory10
         *
         * @return $this
         */
        public function setClassCategory10(ClassCategory $ClassCategory10)
        {
            $this->ClassCategory10 = $ClassCategory10;

            $this->option_class_category_id10 = is_object($ClassCategory10) ?
            $ClassCategory10->getId() : null;

            return $this;
        }

        /**
         * @return ClassCategory
         */
        public function getClassCategory10()
        {
            return $this->ClassCategory10;
        }

        /**
         * @return int|null
         */
        public function getClassCategoryId10()
        {
            return $this->option_class_category_id10;
        }

        /**
         * Set printname_plate.
         *
         * @param string|null $printname_plate
         *
         * @return CartItem
         */
        public function setPrintnamePlate($printname_plate = null)
        {
            $this->printname_plate = $printname_plate;

            return $this;
        }

        /**
         * Get printname_plate.
         *
         * @return string|null
         */
        public function getPrintnamePlate()
        {
            return $this->printname_plate;
        }

        /**
         * Set printname_noshi.
         *
         * @param string|null $printname_noshi
         *
         * @return CartItem
         */
        public function setPrintnameNoshi($printname_noshi = null)
        {
            $this->printname_noshi = $printname_noshi;

            return $this;
        }

        /**
         * Get printname_noshi.
         *
         * @return string|null
         */
        public function getPrintnameNoshi()
        {
            return $this->printname_noshi;
        }
        
        /**
         * @param  integer  $additional_price
         *
         * @return CartItem
         */
        public function setAdditionalPrice($additional_price)
        {
            $this->additional_price = $additional_price;

            return $this;
        }

        /**
         * @return string
         */
        public function getAdditionalPrice()
        {
            return $this->additional_price;
        }
        
        public function getPriceIncTax()
        {
            // TODO ItemInterfaceに追加, Cart::priceは税込み金額が入っているので,フィールドを分ける必要がある
            return $this->price;
        }

        /**
         * @return Cart
         */
        public function getCart()
        {
            return $this->Cart;
        }

        /**
         * @param Cart $Cart
         */
        public function setCart(Cart $Cart)
        {
            $this->Cart = $Cart;
        }
        
        
        
    }
}
