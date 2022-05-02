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
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxDisplayType;

if (!class_exists('\Eccube\Entity\OrderItem')) {
    /**
     * OrderItem
     *
     * @ORM\Table(name="dtb_order_item")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\OrderItemRepository")
     */
    class OrderItem extends \Eccube\Entity\AbstractEntity implements ItemInterface
    {
        use PointRateTrait;

        /**
         * Get price IncTax
         *
         * @return string
         */
        public function getPriceIncTax()
        {
            // 税表示区分が税込の場合は, priceに税込金額が入っている.
            if ($this->TaxDisplayType && $this->TaxDisplayType->getId() == TaxDisplayType::INCLUDED) {
                return $this->price;
            }

            //20220422 税金は税率と商品金額から算出する(スマレジから送られる税金は商品の注文数量分まとめてくるためEC(商品1つあたり)と整合性が合わないため)
            //return $this->price + $this->tax;
            //$_tax = $this->calcTax($this->getPrice(), $this->getTaxRate(), $this->getRoundingType()->getId(),$this->getTaxAdjust());
            $_tax = $this->calcTax($this->getPrice(), $this->getTaxRate(), 1,$this->getTaxAdjust());
            
            return $this->price + $_tax;
        }

        /**
         * @return integer
         */
        public function getTotalPrice()
        {
            $_price = $this->getPrice();
            $_quantity = $this->getQuantity();
            
            //20220422　個数が1つの場合は既存の処理( (税金+追加料金)×個数(1)  )
            if($_quantity < 2){
            	return ($this->getPriceIncTax() + $this->getAdditionalPrice()) * $this->getQuantity();
            }
            
            //20220422　個数が複数の場合は計算方法変更
            //税抜き合計
            $_p_q = $_price * $_quantity;
            
            //税率
            $_taxRate = $this->getTaxRate() == 0 ? 0 : ($this->getTaxRate()/100) + 1;
            
            //合計
            $_sum = $_p_q * $_taxRate;
            
            //最終合計
            $last_sum = $_sum == 0 ? 0 : floor($_sum);
            
            return $last_sum;
        }

        /**
         * @return integer
         */
        public function getOrderItemTypeId()
        {
            if (is_object($this->getOrderItemType())) {
                return $this->getOrderItemType()->getId();
            }

            return null;
        }

        /**
         * 商品明細かどうか.
         *
         * @return boolean 商品明細の場合 true
         */
        public function isProduct()
        {
            return $this->getOrderItemTypeId() === OrderItemType::PRODUCT;
        }

        /**
         * 送料明細かどうか.
         *
         * @return boolean 送料明細の場合 true
         */
        public function isDeliveryFee()
        {
            return $this->getOrderItemTypeId() === OrderItemType::DELIVERY_FEE;
        }

        /**
         * 手数料明細かどうか.
         *
         * @return boolean 手数料明細の場合 true
         */
        public function isCharge()
        {
            return $this->getOrderItemTypeId() === OrderItemType::CHARGE;
        }

        /**
         * 値引き明細かどうか.
         *
         * @return boolean 値引き明細の場合 true
         */
        public function isDiscount()
        {
            return $this->getOrderItemTypeId() === OrderItemType::DISCOUNT;
        }

        /**
         * 税額明細かどうか.
         *
         * @return boolean 税額明細の場合 true
         */
        public function isTax()
        {
            return $this->getOrderItemTypeId() === OrderItemType::TAX;
        }

        /**
         * ポイント明細かどうか.
         *
         * @return boolean ポイント明細の場合 true
         */
        public function isPoint()
        {
            return $this->getOrderItemTypeId() === OrderItemType::POINT;
        }

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
         * @ORM\Column(name="product_name", type="string", length=255)
         */
        private $product_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="product_code", type="string", length=255, nullable=true)
         */
        private $product_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="class_name1", type="string", length=255, nullable=true)
         */
        private $class_name1;

        /**
         * @var string|null
         *
         * @ORM\Column(name="class_name2", type="string", length=255, nullable=true)
         */
        private $class_name2;

        /**
         * @var string|null
         *
         * @ORM\Column(name="class_category_name1", type="string", length=255, nullable=true)
         */
        private $class_category_name1;

        /**
         * @var string|null
         *
         * @ORM\Column(name="class_category_name2", type="string", length=255, nullable=true)
         */
        private $class_category_name2;

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
         * @var string
         *
         * @ORM\Column(name="tax", type="decimal", precision=10, scale=0, options={"default":0})
         */
        private $tax = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="tax_rate", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
         */
        private $tax_rate = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="tax_adjust", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
         */
        private $tax_adjust = 0;

        /**
         * @var int|null
         *
         * @ORM\Column(name="tax_rule_id", type="smallint", nullable=true, options={"unsigned":true})
         */
        private $tax_rule_id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="currency_code", type="string", nullable=true)
         */
        private $currency_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="processor_name", type="string", nullable=true)
         */
        private $processor_name;


        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name1", type="string", length=255, nullable=true)
         */
        private $option_name1;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name2", type="string", length=255, nullable=true)
         */
        private $option_name2;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name3", type="string", length=255, nullable=true)
         */
        private $option_name3;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name4", type="string", length=255, nullable=true)
         */
        private $option_name4;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name5", type="string", length=255, nullable=true)
         */
        private $option_name5;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name6", type="string", length=255, nullable=true)
         */
        private $option_name6;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name7", type="string", length=255, nullable=true)
         */
        private $option_name7;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name8", type="string", length=255, nullable=true)
         */
        private $option_name8;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name9", type="string", length=255, nullable=true)
         */
        private $option_name9;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name10", type="string", length=255, nullable=true)
         */
        private $option_name10;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_plate", type="string", length=255, nullable=true)
         */
        private $option_printname_plate;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_printname_noshi", type="string", length=255, nullable=true)
         */
        private $option_printname_noshi;
        
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
         * @var string|null
         *
         * @ORM\Column(name="option_plate_sentaku", type="string", length=100, nullable=true)
         */
        private $option_plate_sentaku;
        
        
        
        
        /**
         * @var string
         *
         * @ORM\Column(name="additional_price", type="decimal", precision=12, scale=2, options={"default":0})
         */
        private $additional_price = 0;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name1", type="string", length=255, nullable=true)
         */
        private $option_category_name1;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name2", type="string", length=255, nullable=true)
         */
        private $option_category_name2;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name3", type="string", length=255, nullable=true)
         */
        private $option_category_name3;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name4", type="string", length=255, nullable=true)
         */
        private $option_category_name4;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name5", type="string", length=255, nullable=true)
         */
        private $option_category_name5;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name6", type="string", length=255, nullable=true)
         */
        private $option_category_name6;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name7", type="string", length=255, nullable=true)
         */
        private $option_category_name7;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name8", type="string", length=255, nullable=true)
         */
        private $option_category_name8;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name9", type="string", length=255, nullable=true)
         */
        private $option_category_name9;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name10", type="string", length=255, nullable=true)
         */
        private $option_category_name10;
        
        
        
        /**
         * @var string
         *
         * @ORM\Column(name="order_sub_no", type="string", length=255)
         */
        private $order_sub_no;

        
        
        /**
         * @var \Eccube\Entity\Order
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order", inversedBy="OrderItems")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
         * })
         */
        private $Order;

        /**
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

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
         * @var \Eccube\Entity\Shipping
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Shipping", inversedBy="OrderItems")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="shipping_id", referencedColumnName="id")
         * })
         */
        private $Shipping;

        /**
         * @var \Eccube\Entity\Master\RoundingType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\RoundingType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="rounding_type_id", referencedColumnName="id")
         * })
         */
        private $RoundingType;

        /**
         * @var \Eccube\Entity\Master\TaxType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\TaxType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="tax_type_id", referencedColumnName="id")
         * })
         */
        private $TaxType;

        /**
         * @var \Eccube\Entity\Master\TaxDisplayType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\TaxDisplayType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="tax_display_type_id", referencedColumnName="id")
         * })
         */
        private $TaxDisplayType;

        /**
         * @var \Eccube\Entity\Master\OrderItemType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\OrderItemType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_item_type_id", referencedColumnName="id")
         * })
         */
        private $OrderItemType;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="class_category_id1", referencedColumnName="id")
         * })
         */
        private $ClassCategory;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set productName.
         *
         * @param string $productName
         *
         * @return OrderItem
         */
        public function setProductName($productName)
        {
            $this->product_name = $productName;

            return $this;
        }

        /**
         * Get productName.
         *
         * @return string
         */
        public function getProductName()
        {
            return $this->product_name;
        }

        /**
         * Set productCode.
         *
         * @param string|null $productCode
         *
         * @return OrderItem
         */
        public function setProductCode($productCode = null)
        {
            $this->product_code = $productCode;

            return $this;
        }

        /**
         * Get productCode.
         *
         * @return string|null
         */
        public function getProductCode()
        {
            return $this->product_code;
        }

        /**
         * Set className1.
         *
         * @param string|null $className1
         *
         * @return OrderItem
         */
        public function setClassName1($className1 = null)
        {
            $this->class_name1 = $className1;

            return $this;
        }

        /**
         * Get className1.
         *
         * @return string|null
         */
        public function getClassName1()
        {
            return $this->class_name1;
        }

        /**
         * Set className2.
         *
         * @param string|null $className2
         *
         * @return OrderItem
         */
        public function setClassName2($className2 = null)
        {
            $this->class_name2 = $className2;

            return $this;
        }

        /**
         * Get className2.
         *
         * @return string|null
         */
        public function getClassName2()
        {
            return $this->class_name2;
        }

        /**
         * Set classCategoryName1.
         *
         * @param string|null $classCategoryName1
         *
         * @return OrderItem
         */
        public function setClassCategoryName1($classCategoryName1 = null)
        {
            $this->class_category_name1 = $classCategoryName1;

            return $this;
        }

        /**
         * Get classCategoryName1.
         *
         * @return string|null
         */
        public function getClassCategoryName1()
        {
            return $this->class_category_name1;
        }

        /**
         * Set classCategoryName2.
         *
         * @param string|null $classCategoryName2
         *
         * @return OrderItem
         */
        public function setClassCategoryName2($classCategoryName2 = null)
        {
            $this->class_category_name2 = $classCategoryName2;

            return $this;
        }

        /**
         * Get classCategoryName2.
         *
         * @return string|null
         */
        public function getClassCategoryName2()
        {
            return $this->class_category_name2;
        }

        /**
         * Set price.
         *
         * @param string $price
         *
         * @return OrderItem
         */
        public function setPrice($price)
        {
            $this->price = $price;

            return $this;
        }

        /**
         * Get price.
         *
         * @return string
         */
        public function getPrice()
        {
            return $this->price;
        }

        /**
         * Set quantity.
         *
         * @param string $quantity
         *
         * @return OrderItem
         */
        public function setQuantity($quantity)
        {
            $this->quantity = $quantity;

            return $this;
        }

        /**
         * Get quantity.
         *
         * @return string
         */
        public function getQuantity()
        {
            return $this->quantity;
        }

        /**
         * @return string
         */
        public function getTax()
        {
            return $this->tax;
        }

        /**
         * @param string $tax
         *
         * @return $this
         */
        public function setTax($tax)
        {
            $this->tax = $tax;

            return $this;
        }

        /**
         * Set taxRate.
         *
         * @param string $taxRate
         *
         * @return OrderItem
         */
        public function setTaxRate($taxRate)
        {
            $this->tax_rate = $taxRate;

            return $this;
        }

        /**
         * Get taxRate.
         *
         * @return string
         */
        public function getTaxRate()
        {
            return $this->tax_rate;
        }

        /**
         * Set taxAdjust.
         *
         * @param string $tax_adjust
         *
         * @return OrderItem
         */
        public function setTaxAdjust($tax_adjust)
        {
            $this->tax_adjust = $tax_adjust;

            return $this;
        }

        /**
         * Get taxAdjust.
         *
         * @return string
         */
        public function getTaxAdjust()
        {
            return $this->tax_adjust;
        }

        /**
         * Set taxRuleId.
         * @deprecated 税率設定は受注作成時に決定するため廃止予定
         *
         * @param int|null $taxRuleId
         *
         * @return OrderItem
         */
        public function setTaxRuleId($taxRuleId = null)
        {
            $this->tax_rule_id = $taxRuleId;

            return $this;
        }

        /**
         * Get taxRuleId.
         * @deprecated 税率設定は受注作成時に決定するため廃止予定
         *
         * @return int|null
         */
        public function getTaxRuleId()
        {
            return $this->tax_rule_id;
        }

        /**
         * Get currencyCode.
         *
         * @return string
         */
        public function getCurrencyCode()
        {
            return $this->currency_code;
        }

        /**
         * Set currencyCode.
         *
         * @param string|null $currencyCode
         *
         * @return OrderItem
         */
        public function setCurrencyCode($currencyCode = null)
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * Get processorName.
         *
         * @return string
         */
        public function getProcessorName()
        {
            return $this->processor_name;
        }

        /**
         * Set processorName.
         *
         * @param string|null $processorName
         *
         * @return $this
         */
        public function setProcessorName($processorName = null)
        {
            $this->processor_name = $processorName;

            return $this;
        }

        /**
         * Set order.
         *
         * @param \Eccube\Entity\Order|null $order
         *
         * @return OrderItem
         */
        public function setOrder(\Eccube\Entity\Order $order = null)
        {
            $this->Order = $order;

            return $this;
        }

        /**
         * Get order.
         *
         * @return \Eccube\Entity\Order|null
         */
        public function getOrder()
        {
            return $this->Order;
        }

        public function getOrderId()
        {
            if (is_object($this->getOrder())) {
                return $this->getOrder()->getId();
            }

            return null;
        }

        /**
         * Set product.
         *
         * @param \Eccube\Entity\Product|null $product
         *
         * @return OrderItem
         */
        public function setProduct(\Eccube\Entity\Product $product = null)
        {
            $this->Product = $product;

            return $this;
        }

        /**
         * Get product.
         *
         * @return \Eccube\Entity\Product|null
         */
        public function getProduct()
        {
            return $this->Product;
        }

        /**
         * Set productClass.
         *
         * @param \Eccube\Entity\ProductClass|null $productClass
         *
         * @return OrderItem
         */
        public function setProductClass(\Eccube\Entity\ProductClass $productClass = null)
        {
            $this->ProductClass = $productClass;

            return $this;
        }

        /**
         * Get productClass.
         *
         * @return \Eccube\Entity\ProductClass|null
         */
        public function getProductClass()
        {
            return $this->ProductClass;
        }

        /**
         * Set shipping.
         *
         * @param \Eccube\Entity\Shipping|null $shipping
         *
         * @return OrderItem
         */
        public function setShipping(\Eccube\Entity\Shipping $shipping = null)
        {
            $this->Shipping = $shipping;

            return $this;
        }

        /**
         * Get shipping.
         *
         * @return \Eccube\Entity\Shipping|null
         */
        public function getShipping()
        {
            return $this->Shipping;
        }

        /**
         * @return RoundingType
         */
        public function getRoundingType()
        {
            return $this->RoundingType;
        }

        /**
         * @param RoundingType $RoundingType
         */
        public function setRoundingType(RoundingType $RoundingType = null)
        {
            $this->RoundingType = $RoundingType;

            return $this;
        }

        /**
         * Set taxType
         *
         * @param \Eccube\Entity\Master\TaxType $taxType
         *
         * @return OrderItem
         */
        public function setTaxType(\Eccube\Entity\Master\TaxType $taxType = null)
        {
            $this->TaxType = $taxType;

            return $this;
        }

        /**
         * Get taxType
         *
         * @return \Eccube\Entity\Master\TaxType
         */
        public function getTaxType()
        {
            return $this->TaxType;
        }

        /**
         * Set taxDisplayType
         *
         * @param \Eccube\Entity\Master\TaxDisplayType $taxDisplayType
         *
         * @return OrderItem
         */
        public function setTaxDisplayType(\Eccube\Entity\Master\TaxDisplayType $taxDisplayType = null)
        {
            $this->TaxDisplayType = $taxDisplayType;

            return $this;
        }

        /**
         * Get taxDisplayType
         *
         * @return \Eccube\Entity\Master\TaxDisplayType
         */
        public function getTaxDisplayType()
        {
            return $this->TaxDisplayType;
        }

        /**
         * Set orderItemType
         *
         * @param \Eccube\Entity\Master\OrderItemType $orderItemType
         *
         * @return OrderItem
         */
        public function setOrderItemType(\Eccube\Entity\Master\OrderItemType $orderItemType = null)
        {
            $this->OrderItemType = $orderItemType;

            return $this;
        }

        /**
         * Get orderItemType
         *
         * @return \Eccube\Entity\Master\OrderItemType
         */
        public function getOrderItemType()
        {
            return $this->OrderItemType;
        }
        
        
        
        /**
         * Set optionName1.
         *
         * @param string|null $optionName1
         *
         * @return OrderItem
         */
        public function setOptionName1($optionName1 = null)
        {
            $this->option_name1 = $optionName1;

            return $this;
        }
        
        /**
         * Set optionName2.
         *
         * @param string|null $optionName2
         *
         * @return OrderItem
         */
        public function setOptionName2($optionName2 = null)
        {
            $this->option_name2 = $optionName2;

            return $this;
        }
        
        /**
         * Set optionName3.
         *
         * @param string|null $optionName3
         *
         * @return OrderItem
         */
        public function setOptionName3($optionName3 = null)
        {
            $this->option_name3 = $optionName3;

            return $this;
        }
        
        /**
         * Set optionName4.
         *
         * @param string|null $optionName4
         *
         * @return OrderItem
         */
        public function setOptionName4($optionName4 = null)
        {
            $this->option_name4 = $optionName4;

            return $this;
        }
        
        /**
         * Set optionName5.
         *
         * @param string|null $optionName5
         *
         * @return OrderItem
         */
        public function setOptionName5($optionName5 = null)
        {
            $this->option_name5 = $optionName5;

            return $this;
        }
        
        /**
         * Set optionName6.
         *
         * @param string|null $optionName6
         *
         * @return OrderItem
         */
        public function setOptionName6($optionName6 = null)
        {
            $this->option_name6 = $optionName6;

            return $this;
        }
        
        /**
         * Set optionName7.
         *
         * @param string|null $optionName7
         *
         * @return OrderItem
         */
        public function setOptionName7($optionName7 = null)
        {
            $this->option_name7 = $optionName7;

            return $this;
        }
        
        /**
         * Set optionName8.
         *
         * @param string|null $optionName8
         *
         * @return OrderItem
         */
        public function setOptionName8($optionName8 = null)
        {
            $this->option_name8 = $optionName8;

            return $this;
        }
        
        /**
         * Set optionName9.
         *
         * @param string|null $optionName9
         *
         * @return OrderItem
         */
        public function setOptionName9($optionName9 = null)
        {
            $this->option_name9 = $optionName9;

            return $this;
        }
        
        /**
         * Set optionName10.
         *
         * @param string|null $optionName10
         *
         * @return OrderItem
         */
        public function setOptionName10($optionName10 = null)
        {
            $this->option_name10 = $optionName10;

            return $this;
        }
        
        /**
         * Set optionPrintnamePlate.
         *
         * @param string|null $optionPrintnamePlate
         *
         * @return OrderItem
         */
        public function setOptionPrintnamePlate($optionPrintnamePlate = null)
        {
            $this->option_printname_plate = $optionPrintnamePlate;

            return $this;
        }
        
        /**
         * Set optionPrintnameNoshi.
         *
         * @param string|null $optionPrintnameNoshi
         *
         * @return OrderItem
         */
        public function setOptionPrintnameNoshi($optionPrintnameNoshi = null)
        {
            $this->option_printname_noshi = $optionPrintnameNoshi;

            return $this;
        }
        
        
        /**
         * Set optionDetail.
         *
         * @param string|null $optionDetail
         *
         * @return OrderItem
         */
        public function setOptionDetail($optionDetail = null)
        {
            $this->option_detail = $optionDetail;

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
         * Set option_plate_sentaku.
         *
         * @param string|null $option_plate_sentaku
         *
         * @return CartItem
         */
        public function setOptionPlateSentaku($option_plate_sentaku = null)
        {
            $this->option_plate_sentaku = $option_plate_sentaku;

            return $this;
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
        
        /**
         * Get optionName1.
         *
         * @return string|null
         */
        public function getOptionName1()
        {
            return $this->option_name1;
        }

        /**
         * Get optionName2.
         *
         * @return string|null
         */
        public function getOptionName2()
        {
            return $this->option_name2;
        }
        
        /**
         * Get optionName3.
         *
         * @return string|null
         */
        public function getOptionName3()
        {
            return $this->option_name3;
        }
        
        /**
         * Get optionName4.
         *
         * @return string|null
         */
        public function getOptionName4()
        {
            return $this->option_name4;
        }
        
        /**
         * Get optionName5.
         *
         * @return string|null
         */
        public function getOptionName5()
        {
            return $this->option_name5;
        }
        
        /**
         * Get optionName6.
         *
         * @return string|null
         */
        public function getOptionName6()
        {
            return $this->option_name6;
        }
        
        /**
         * Get optionName7.
         *
         * @return string|null
         */
        public function getOptionName7()
        {
            return $this->option_name7;
        }
        
        /**
         * Get optionName8.
         *
         * @return string|null
         */
        public function getOptionName8()
        {
            return $this->option_name8;
        }
        /**
         * Get optionName9.
         *
         * @return string|null
         */
        public function getOptionName9()
        {
            return $this->option_name9;
        }
        
        /**
         * Get optionName10.
         *
         * @return string|null
         */
        public function getOptionName10()
        {
            return $this->option_name10;
        }
        
        /**
         * Get optionPrintnameNoshi.
         *
         * @return string|null
         */
        public function getOptionPrintnameNoshi()
        {
            return $this->option_printname_noshi;
        }
        
        /**
         * Get optionPrintnamePlate.
         *
         * @return string|null
         */
        public function getOptionPrintnamePlate()
        {
            return $this->option_printname_plate;
        }

        /**
         * Get optionDetail.
         *
         * @return string|null
         */
        public function getOptionDetail()
        {
            return $this->option_detail;
        }

        /**
         * Set optionCategoryName1.
         *
         * @param string|null $optionCategoryName1
         *
         * @return OrderItem
         */
        public function setOptionCategoryName1($optionCategoryName1 = null)
        {
            $this->option_category_name1 = $optionCategoryName1;

            return $this;
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
         * Get option_plate_sentaku.
         *
         * @return string|null
         */
        public function getOptionPlateSentaku()
        {
            return $this->option_plate_sentaku;
        }
        
        
        
        
        /**
         * Set optionCategoryName2.
         *
         * @param string|null $optionCategoryName2
         *
         * @return OrderItem
         */
        public function setOptionCategoryName2($optionCategoryName2 = null)
        {
            $this->option_category_name2 = $optionCategoryName2;

            return $this;
        }
        
        /**
         * Set optionCategoryName3.
         *
         * @param string|null $optionCategoryName3
         *
         * @return OrderItem
         */
        public function setOptionCategoryName3($optionCategoryName3 = null)
        {
            $this->option_category_name3 = $optionCategoryName3;

            return $this;
        }
        
        /**
         * Set optionCategoryName4.
         *
         * @param string|null $optionCategoryName4
         *
         * @return OrderItem
         */
        public function setOptionCategoryName4($optionCategoryName4 = null)
        {
            $this->option_category_name4 = $optionCategoryName4;

            return $this;
        }
        
        /**
         * Set optionCategoryName5.
         *
         * @param string|null $optionCategoryName5
         *
         * @return OrderItem
         */
        public function setOptionCategoryName5($optionCategoryName5 = null)
        {
            $this->option_category_name5 = $optionCategoryName5;

            return $this;
        }
        
        /**
         * Set optionCategoryName6.
         *
         * @param string|null $optionCategoryName6
         *
         * @return OrderItem
         */
        public function setOptionCategoryName6($optionCategoryName6 = null)
        {
            $this->option_category_name6 = $optionCategoryName6;

            return $this;
        }
        /**
         * Set optionCategoryName7.
         *
         * @param string|null $optionCategoryName7
         *
         * @return OrderItem
         */
        public function setOptionCategoryName7($optionCategoryName7 = null)
        {
            $this->option_category_name7 = $optionCategoryName7;

            return $this;
        }
        /**
         * Set optionCategoryName8.
         *
         * @param string|null $optionCategoryName8
         *
         * @return OrderItem
         */
        public function setOptionCategoryName8($optionCategoryName8 = null)
        {
            $this->option_category_name8 = $optionCategoryName8;

            return $this;
        }
        /**
         * Set optionCategoryName9.
         *
         * @param string|null $optionCategoryName9
         *
         * @return OrderItem
         */
        public function setOptionCategoryName9($optionCategoryName9 = null)
        {
            $this->option_category_name9 = $optionCategoryName9;

            return $this;
        }
        
        /**
         * Set optionCategoryName10.
         *
         * @param string|null $optionCategoryName10
         *
         * @return OrderItem
         */
        public function setOptionCategoryName10($optionCategoryName10 = null)
        {
            $this->option_category_name10 = $optionCategoryName10;

            return $this;
        }

        /**
         * Get optionCategoryName1.
         *
         * @return string|null
         */
        public function getOptionCategoryName1()
        {
            return $this->option_category_name1;
        }
        
        /**
         * Get optionCategoryName2.
         *
         * @return string|null
         */
        public function getOptionCategoryName2()
        {
            return $this->option_category_name2;
        }
        

        /**
         * Get optionCategoryName3.
         *
         * @return string|null
         */
        public function getOptionCategoryName3()
        {
            return $this->option_category_name3;
        }

        /**
         * Get optionCategoryName4.
         *
         * @return string|null
         */
        public function getOptionCategoryName4()
        {
            return $this->option_category_name4;
        }

        /**
         * Get optionCategoryName5.
         *
         * @return string|null
         */
        public function getOptionCategoryName5()
        {
            return $this->option_category_name5;
        }

        /**
         * Get optionCategoryName6.
         *
         * @return string|null
         */
        public function getOptionCategoryName6()
        {
            return $this->option_category_name6;
        }

        /**
         * Get optionCategoryName7.
         *
         * @return string|null
         */
        public function getOptionCategoryName7()
        {
            return $this->option_category_name7;
        }

        /**
         * Get optionCategoryName8.
         *
         * @return string|null
         */
        public function getOptionCategoryName8()
        {
            return $this->option_category_name8;
        }

        /**
         * Get optionCategoryName9.
         *
         * @return string|null
         */
        public function getOptionCategoryName9()
        {
            return $this->option_category_name9;
        }
        
        /**
         * Get optionCategoryName10.
         *
         * @return string|null
         */
        public function getOptionCategoryName10()
        {
            return $this->option_category_name10;
        }
        
        
        
        /**
         * Set orderSubNo.
         *
         * @param string $orderSubNo
         *
         * @return OrderItem
         */
        public function setOrderSubNo($orderSubNo)
        {
            $this->order_sub_no = $orderSubNo;

            return $this;
        }

        /**
         * Get orderSubNo.
         *
         * @return string
         */
        public function getOrderSubNo()
        {
            return $this->order_sub_no;
        }
        
        
        /**
	     * 税金額を計算する
	     *
	     * @param  int    $price     計算対象の金額
	     * @param  int    $taxRate   税率(%単位)
	     * @param  int    $RoundingType  端数処理
	     * @param  int    $taxAdjust 調整額
	     *
	     * @return double 税金額
	     */
	    public function calcTax($price, $taxRate, $RoundingType, $taxAdjust = 0)
	    {
	        $tax = $price * $taxRate / 100;
	        $roundTax = $this->roundByRoundingType($tax, $RoundingType);

	        return $roundTax + $taxAdjust;
	    }
	    
	    /**
	     * 課税規則に応じて端数処理を行う
	     *
	     * @param  integer $value    端数処理を行う数値
	     * @param integer $RoundingType
	     *
	     * @return double        端数処理後の数値
	     */
	    public function roundByRoundingType($value, $RoundingType)
	    {
	        /*
	        switch ($RoundingType) {
	            // 四捨五入
	            case \Eccube\Entity\Master\RoundingType::ROUND:
	                $ret = round($value);
	                break;
	            // 切り捨て
	            case \Eccube\Entity\Master\RoundingType::FLOOR:
	                $ret = floor($value);
	                break;
	            // 切り上げ
	            case \Eccube\Entity\Master\RoundingType::CEIL:
	                $ret = ceil($value);
	                break;
	            // デフォルト:切り上げ
	            default:
	                $ret = ceil($value);
	                break;
	        }
	        */
	        //切り捨て固定
	        $ret = floor($value);

	        return $ret;
	    }
        
    }
}
