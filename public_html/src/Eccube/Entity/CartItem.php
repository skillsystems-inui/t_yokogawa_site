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
         * @var string|null
         *
         * @ORM\Column(name="option_detail", type="string", length=1024, nullable=true)
         */
        private $option_detail;
        
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_dai_num", type="string", length=100, nullable=true)
         */
        private $option_candle_dai_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_syo_num", type="string", length=100, nullable=true)
         */
        private $option_candle_syo_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no1_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no1_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no2_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no2_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no3_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no3_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no4_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no4_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no5_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no5_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no6_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no6_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no7_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no7_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no8_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no8_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no9_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no9_num;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_candle_no0_num", type="string", length=100, nullable=true)
         */
        private $option_candle_no0_num;
        
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_plate1", type="string", length=100, nullable=true)
         */
        private $option_printname_plate1;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_plate2", type="string", length=100, nullable=true)
         */
        private $option_printname_plate2;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_plate3", type="string", length=100, nullable=true)
         */
        private $option_printname_plate3;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_plate4", type="string", length=100, nullable=true)
         */
        private $option_printname_plate4;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_plate5", type="string", length=100, nullable=true)
         */
        private $option_printname_plate5;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_deco_ichigo_chk", type="string", length=100, nullable=true)
         */
        private $option_deco_ichigo_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_deco_fruit_chk", type="string", length=100, nullable=true)
         */
        private $option_deco_fruit_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_deco_namachoco_chk", type="string", length=100, nullable=true)
         */
        private $option_deco_namachoco_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_deco_echoco_chk", type="string", length=100, nullable=true)
         */
        private $option_deco_echoco_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_pori_cyu_chk", type="string", length=100, nullable=true)
         */
        private $option_pori_cyu_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_pori_dai_chk", type="string", length=100, nullable=true)
         */
        private $option_pori_dai_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_pori_tokudai_chk", type="string", length=100, nullable=true)
         */
        private $option_pori_tokudai_chk;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_housou_sentaku", type="string", length=100, nullable=true)
         */
        private $option_housou_sentaku;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_noshi_kakekata", type="string", length=100, nullable=true)
         */
        private $option_noshi_kakekata;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_kakehousou_syurui", type="string", length=100, nullable=true)
         */
        private $option_kakehousou_syurui;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_uwagaki_sentaku", type="string", length=100, nullable=true)
         */
        private $option_uwagaki_sentaku;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_nosina", type="string", length=100, nullable=true)
         */
        private $option_printname_nosina;
        
        
        
        
        
        
        
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
         * Set option_detail.
         *
         * @param string|null $option_detail
         *
         * @return CartItem
         */
        public function setOptionDetail($option_detail = null)
        {
            $this->option_detail = $option_detail;

            return $this;
        }
        
        
        
        
        
        /**
         * Set option_candle_dai_num.
         *
         * @param string|null $option_candle_dai_num
         *
         * @return CartItem
         */
        public function setOptionCandleDaiNum($option_candle_dai_num = null)
        {
            $this->option_candle_dai_num = $option_candle_dai_num;

            return $this;
        }
        
        /**
         * Set option_candle_syo_num.
         *
         * @param string|null $option_candle_syo_num
         *
         * @return CartItem
         */
        public function setOptionCandleSyoNum($option_candle_syo_num = null)
        {
            $this->option_candle_syo_num = $option_candle_syo_num;

            return $this;
        }
        
        /**
         * Set option_candle_no1_num.
         *
         * @param string|null $option_candle_no1_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo1Num($option_candle_no1_num = null)
        {
            $this->option_candle_no1_num = $option_candle_no1_num;

            return $this;
        }
        
        /**
         * Set option_candle_no2_num.
         *
         * @param string|null $option_candle_no2_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo2Num($option_candle_no2_num = null)
        {
            $this->option_candle_no2_num = $option_candle_no2_num;

            return $this;
        }
        
        /**
         * Set option_candle_no3_num.
         *
         * @param string|null $option_candle_no3_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo3Num($option_candle_no3_num = null)
        {
            $this->option_candle_no3_num = $option_candle_no3_num;

            return $this;
        }
        
        /**
         * Set option_candle_no4_num.
         *
         * @param string|null $option_candle_no4_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo4Num($option_candle_no4_num = null)
        {
            $this->option_candle_no4_num = $option_candle_no4_num;

            return $this;
        }
        
        /**
         * Set option_candle_no5_num.
         *
         * @param string|null $option_candle_no5_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo5Num($option_candle_no5_num = null)
        {
            $this->option_candle_no5_num = $option_candle_no5_num;

            return $this;
        }
        
        
        /**
         * Set option_candle_no6_num.
         *
         * @param string|null $option_candle_no6_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo6Num($option_candle_no6_num = null)
        {
            $this->option_candle_no6_num = $option_candle_no6_num;

            return $this;
        }
        
        /**
         * Set option_candle_no7_num.
         *
         * @param string|null $option_candle_no7_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo7Num($option_candle_no7_num = null)
        {
            $this->option_candle_no7_num = $option_candle_no7_num;

            return $this;
        }
        
        /**
         * Set option_candle_no8_num.
         *
         * @param string|null $option_candle_no8_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo8Num($option_candle_no8_num = null)
        {
            $this->option_candle_no8_num = $option_candle_no8_num;

            return $this;
        }
        
        /**
         * Set option_candle_no9_num.
         *
         * @param string|null $option_candle_no9_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo9Num($option_candle_no9_num = null)
        {
            $this->option_candle_no9_num = $option_candle_no9_num;

            return $this;
        }
        
        /**
         * Set option_candle_no0_num.
         *
         * @param string|null $option_candle_no0_num
         *
         * @return CartItem
         */
        public function setOptionCandleNo0Num($option_candle_no0_num = null)
        {
            $this->option_candle_no0_num = $option_candle_no0_num;

            return $this;
        }
        
        /**
         * Set option_printname_plate1.
         *
         * @param string|null $option_printname_plate1
         *
         * @return CartItem
         */
        public function setOptionPrintnamePlate1($option_printname_plate1 = null)
        {
            $this->option_printname_plate1 = $option_printname_plate1;

            return $this;
        }
        
        /**
         * Set option_printname_plate2.
         *
         * @param string|null $option_printname_plate2
         *
         * @return CartItem
         */
        public function setOptionPrintnamePlate2($option_printname_plate2 = null)
        {
            $this->option_printname_plate2 = $option_printname_plate2;

            return $this;
        }
        
        /**
         * Set option_printname_plate3.
         *
         * @param string|null $option_printname_plate3
         *
         * @return CartItem
         */
        public function setOptionPrintnamePlate3($option_printname_plate3 = null)
        {
            $this->option_printname_plate3 = $option_printname_plate3;

            return $this;
        }
        
        /**
         * Set option_printname_plate4.
         *
         * @param string|null $option_printname_plate4
         *
         * @return CartItem
         */
        public function setOptionPrintnamePlate4($option_printname_plate4 = null)
        {
            $this->option_printname_plate4 = $option_printname_plate4;

            return $this;
        }
        
        /**
         * Set option_printname_plate5.
         *
         * @param string|null $option_printname_plate5
         *
         * @return CartItem
         */
        public function setOptionPrintnamePlate5($option_printname_plate5 = null)
        {
            $this->option_printname_plate5 = $option_printname_plate5;

            return $this;
        }
        
        /**
         * Set option_deco_ichigo_chk.
         *
         * @param string|null $option_deco_ichigo_chk
         *
         * @return CartItem
         */
        public function setOptionDecoIchigoChk($option_deco_ichigo_chk = null)
        {
            $this->option_deco_ichigo_chk = $option_deco_ichigo_chk;

            return $this;
        }
        
        /**
         * Set option_deco_fruit_chk.
         *
         * @param string|null $option_deco_fruit_chk
         *
         * @return CartItem
         */
        public function setOptionDecoFruitChk($option_deco_fruit_chk = null)
        {
            $this->option_deco_fruit_chk = $option_deco_fruit_chk;

            return $this;
        }
        
        
        /**
         * Set option_deco_namachoco_chk.
         *
         * @param string|null $option_deco_namachoco_chk
         *
         * @return CartItem
         */
        public function setOptionDecoNamachocoChk($option_deco_namachoco_chk = null)
        {
            $this->option_deco_namachoco_chk = $option_deco_namachoco_chk;

            return $this;
        }
        
        /**
         * Set option_deco_echoco_chk.
         *
         * @param string|null $option_deco_echoco_chk
         *
         * @return CartItem
         */
        public function setOptionDecoEchocoChk($option_deco_echoco_chk = null)
        {
            $this->option_deco_echoco_chk = $option_deco_echoco_chk;

            return $this;
        }
        
        /**
         * Set option_pori_cyu_chk.
         *
         * @param string|null $option_pori_cyu_chk
         *
         * @return CartItem
         */
        public function setOptionPoriCyuChk($option_pori_cyu_chk = null)
        {
            $this->option_pori_cyu_chk = $option_pori_cyu_chk;

            return $this;
        }
        
        /**
         * Set option_pori_dai_chk.
         *
         * @param string|null $option_pori_dai_chk
         *
         * @return CartItem
         */
        public function setOptionPoriDaiChk($option_pori_dai_chk = null)
        {
            $this->option_pori_dai_chk = $option_pori_dai_chk;

            return $this;
        }
        
        /**
         * Set option_pori_tokudai_chk.
         *
         * @param string|null $option_pori_tokudai_chk
         *
         * @return CartItem
         */
        public function setOptionPoriTokudaiChk($option_pori_tokudai_chk = null)
        {
            $this->option_pori_tokudai_chk = $option_pori_tokudai_chk;

            return $this;
        }
        
        /**
         * Set option_housou_sentaku.
         *
         * @param string|null $option_housou_sentaku
         *
         * @return CartItem
         */
        public function setOptionHousouSentaku($option_housou_sentaku = null)
        {
            $this->option_housou_sentaku = $option_housou_sentaku;

            return $this;
        }
        
        /**
         * Set option_noshi_kakekata.
         *
         * @param string|null $option_noshi_kakekata
         *
         * @return CartItem
         */
        public function setOptionNoshiKakekata($option_noshi_kakekata = null)
        {
            $this->option_noshi_kakekata = $option_noshi_kakekata;

            return $this;
        }
        
        /**
         * Set option_kakehousou_syurui.
         *
         * @param string|null $option_kakehousou_syurui
         *
         * @return CartItem
         */
        public function setOptionKakehousouSyurui($option_kakehousou_syurui = null)
        {
            $this->option_kakehousou_syurui = $option_kakehousou_syurui;

            return $this;
        }
        
        /**
         * Set option_uwagaki_sentaku.
         *
         * @param string|null $option_uwagaki_sentaku
         *
         * @return CartItem
         */
        public function setOptionUwagakiSentaku($option_uwagaki_sentaku = null)
        {
            $this->option_uwagaki_sentaku = $option_uwagaki_sentaku;

            return $this;
        }
        
        /**
         * Set option_printname_nosina.
         *
         * @param string|null $option_printname_nosina
         *
         * @return CartItem
         */
        public function setOptionPrintnameNosina($option_printname_nosina = null)
        {
            $this->option_printname_nosina = $option_printname_nosina;

            return $this;
        }
        
        /**
         * Get option_detail.
         *
         * @return string|null
         */
        public function getOptionDetail()
        {
            return $this->option_detail;
        }
        
        
        /**
         * Get option_candle_dai_num.
         *
         * @return string|null
         */
        public function getOptionCandleDaiNum()
        {
            return $this->option_candle_dai_num;
        }
        
        /**
         * Get option_candle_syo_num.
         *
         * @return string|null
         */
        public function getOptionCandleSyoNum()
        {
            return $this->option_candle_syo_num;
        }
        
        /**
         * Get option_candle_no1_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo1Num()
        {
            return $this->option_candle_no1_num;
        }
        
        /**
         * Get option_candle_no2_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo2Num()
        {
            return $this->option_candle_no2_num;
        }
        
        
        /**
         * Get option_candle_no3_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo3Num()
        {
            return $this->option_candle_no3_num;
        }
        
        /**
         * Get option_candle_no4_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo4Num()
        {
            return $this->option_candle_no4_num;
        }
        
        
        /**
         * Get option_candle_no5_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo5Num()
        {
            return $this->option_candle_no5_num;
        }
        
        /**
         * Get option_candle_no6_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo6Num()
        {
            return $this->option_candle_no6_num;
        }
        
        /**
         * Get option_candle_no7_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo7Num()
        {
            return $this->option_candle_no7_num;
        }
        
        /**
         * Get option_candle_no8_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo8Num()
        {
            return $this->option_candle_no8_num;
        }
        
        /**
         * Get option_candle_no9_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo9Num()
        {
            return $this->option_candle_no9_num;
        }
        
        /**
         * Get option_candle_no0_num.
         *
         * @return string|null
         */
        public function getOptionCandleNo0Num()
        {
            return $this->option_candle_no0_num;
        }
        
		
        /**
         * Get option_printname_plate1.
         *
         * @return string|null
         */
        public function getOptionPrintnamePlate1()
        {
            return $this->option_printname_plate1;
        }
        
        /**
         * Get option_printname_plate2.
         *
         * @return string|null
         */
        public function getOptionPrintnamePlate2()
        {
            return $this->option_printname_plate2;
        }
        
        /**
         * Get option_printname_plate3.
         *
         * @return string|null
         */
        public function getOptionPrintnamePlate3()
        {
            return $this->option_printname_plate3;
        }
        
        /**
         * Get option_printname_plate4.
         *
         * @return string|null
         */
        public function getOptionPrintnamePlate4()
        {
            return $this->option_printname_plate4;
        }
        
        
        /**
         * Get option_printname_plate5.
         *
         * @return string|null
         */
        public function getOptionPrintnamePlate5()
        {
            return $this->option_printname_plate5;
        }
        
        /**
         * Get option_deco_ichigo_chk.
         *
         * @return string|null
         */
        public function getOptionDecoIchigoChk()
        {
            return $this->option_deco_ichigo_chk;
        }
        
        
        /**
         * Get option_deco_fruit_chk.
         *
         * @return string|null
         */
        public function getOptionDecoFruitChk()
        {
            return $this->option_deco_fruit_chk;
        }
        
        /**
         * Get option_deco_namachoco_chk.
         *
         * @return string|null
         */
        public function getOptionDecoNamachocoChk()
        {
            return $this->option_deco_namachoco_chk;
        }
        
        /**
         * Get option_deco_echoco_chk.
         *
         * @return string|null
         */
        public function getOptionDecoEchocoChk()
        {
            return $this->option_deco_echoco_chk;
        }
        
        /**
         * Get option_pori_cyu_chk.
         *
         * @return string|null
         */
        public function getOptionPoriCyuChk()
        {
            return $this->option_pori_cyu_chk;
        }
        
        /**
         * Get option_pori_dai_chk.
         *
         * @return string|null
         */
        public function getOptionPoriDaiChk()
        {
            return $this->option_pori_dai_chk;
        }
        
        /**
         * Get option_pori_tokudai_chk.
         *
         * @return string|null
         */
        public function getOptionPoriTokudaiChk()
        {
            return $this->option_pori_tokudai_chk;
        }
        
		
        /**
         * Get option_housou_sentaku.
         *
         * @return string|null
         */
        public function getOptionHousouSentaku()
        {
            return $this->option_housou_sentaku;
        }
        
        /**
         * Get option_noshi_kakekata.
         *
         * @return string|null
         */
        public function getOptionNoshiKakekata()
        {
            return $this->option_noshi_kakekata;
        }
        
        /**
         * Get option_kakehousou_syurui.
         *
         * @return string|null
         */
        public function getOptionKakehousouSyurui()
        {
            return $this->option_kakehousou_syurui;
        }
        
        /**
         * Get option_uwagaki_sentaku.
         *
         * @return string|null
         */
        public function getOptionUwagakiSentaku()
        {
            return $this->option_uwagaki_sentaku;
        }
        
        
        /**
         * Get option_printname_nosina.
         *
         * @return string|null
         */
        public function getOptionPrintnameNosina()
        {
            return $this->option_printname_nosina;
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
