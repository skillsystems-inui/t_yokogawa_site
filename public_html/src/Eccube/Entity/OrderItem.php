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

            return $this->price + $this->tax;
        }

        /**
         * @return integer
         */
        public function getTotalPrice()
        {
            return ($this->getPriceIncTax() + $this->getAdditionalPrice()) * $this->getQuantity();
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
         * @ORM\Column(name="option_name11", type="string", length=255, nullable=true)
         */
        private $option_name11;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name12", type="string", length=255, nullable=true)
         */
        private $option_name12;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name13", type="string", length=255, nullable=true)
         */
        private $option_name13;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name14", type="string", length=255, nullable=true)
         */
        private $option_name14;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name15", type="string", length=255, nullable=true)
         */
        private $option_name15;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name16", type="string", length=255, nullable=true)
         */
        private $option_name16;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name17", type="string", length=255, nullable=true)
         */
        private $option_name17;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name18", type="string", length=255, nullable=true)
         */
        private $option_name18;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name19", type="string", length=255, nullable=true)
         */
        private $option_name19;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_name20", type="string", length=255, nullable=true)
         */
        private $option_name20;
        
        
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
         * @var string|null
         *
         * @ORM\Column(name="option_category_name11", type="string", length=255, nullable=true)
         */
        private $option_category_name11;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name12", type="string", length=255, nullable=true)
         */
        private $option_category_name12;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name13", type="string", length=255, nullable=true)
         */
        private $option_category_name13;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name14", type="string", length=255, nullable=true)
         */
        private $option_category_name14;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name15", type="string", length=255, nullable=true)
         */
        private $option_category_name15;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name16", type="string", length=255, nullable=true)
         */
        private $option_category_name16;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name17", type="string", length=255, nullable=true)
         */
        private $option_category_name17;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name18", type="string", length=255, nullable=true)
         */
        private $option_category_name18;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name19", type="string", length=255, nullable=true)
         */
        private $option_category_name19;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="option_category_name20", type="string", length=255, nullable=true)
         */
        private $option_category_name20;
        
        
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
         * Set optionName11.
         *
         * @param string|null $optionName11
         *
         * @return OrderItem
         */
        public function setOptionName11($optionName11 = null)
        {
            $this->option_name11 = $optionName11;

            return $this;
        }
        
        /**
         * Set optionName12.
         *
         * @param string|null $optionName12
         *
         * @return OrderItem
         */
        public function setOptionName12($optionName12 = null)
        {
            $this->option_name12 = $optionName12;

            return $this;
        }
        
        /**
         * Set optionName13.
         *
         * @param string|null $optionName13
         *
         * @return OrderItem
         */
        public function setOptionName13($optionName13 = null)
        {
            $this->option_name13 = $optionName13;

            return $this;
        }
        
        /**
         * Set optionName14.
         *
         * @param string|null $optionName14
         *
         * @return OrderItem
         */
        public function setOptionName14($optionName14 = null)
        {
            $this->option_name14 = $optionName14;

            return $this;
        }
        
        /**
         * Set optionName15.
         *
         * @param string|null $optionName15
         *
         * @return OrderItem
         */
        public function setOptionName15($optionName15 = null)
        {
            $this->option_name15 = $optionName15;

            return $this;
        }
        
        /**
         * Set optionName16.
         *
         * @param string|null $optionName16
         *
         * @return OrderItem
         */
        public function setOptionName16($optionName16 = null)
        {
            $this->option_name16 = $optionName16;

            return $this;
        }
        
        /**
         * Set optionName17.
         *
         * @param string|null $optionName17
         *
         * @return OrderItem
         */
        public function setOptionName17($optionName17 = null)
        {
            $this->option_name17 = $optionName17;

            return $this;
        }
        
        /**
         * Set optionName18.
         *
         * @param string|null $optionName18
         *
         * @return OrderItem
         */
        public function setOptionName18($optionName18 = null)
        {
            $this->option_name18 = $optionName18;

            return $this;
        }
        
        /**
         * Set optionName19.
         *
         * @param string|null $optionName19
         *
         * @return OrderItem
         */
        public function setOptionName19($optionName19 = null)
        {
            $this->option_name19 = $optionName19;

            return $this;
        }
        
        /**
         * Set optionName20.
         *
         * @param string|null $optionName20
         *
         * @return OrderItem
         */
        public function setOptionName20($optionName20 = null)
        {
            $this->option_name20 = $optionName20;

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
         * Get optionName11.
         *
         * @return string|null
         */
        public function getOptionName11()
        {
            return $this->option_name11;
        }

        /**
         * Get optionName12.
         *
         * @return string|null
         */
        public function getOptionName12()
        {
            return $this->option_name12;
        }
        
        /**
         * Get optionName13.
         *
         * @return string|null
         */
        public function getOptionName13()
        {
            return $this->option_name13;
        }
        
        /**
         * Get optionName14.
         *
         * @return string|null
         */
        public function getOptionName14()
        {
            return $this->option_name14;
        }
        
        /**
         * Get optionName15.
         *
         * @return string|null
         */
        public function getOptionName15()
        {
            return $this->option_name15;
        }
        
        /**
         * Get optionName16.
         *
         * @return string|null
         */
        public function getOptionName16()
        {
            return $this->option_name16;
        }
        
        /**
         * Get optionName17.
         *
         * @return string|null
         */
        public function getOptionName17()
        {
            return $this->option_name17;
        }
        
        /**
         * Get optionName18.
         *
         * @return string|null
         */
        public function getOptionName18()
        {
            return $this->option_name18;
        }
        /**
         * Get optionName19.
         *
         * @return string|null
         */
        public function getOptionName19()
        {
            return $this->option_name19;
        }
        
        /**
         * Get optionName20.
         *
         * @return string|null
         */
        public function getOptionName20()
        {
            return $this->option_name20;
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
         * Set optionCategoryName11.
         *
         * @param string|null $optionCategoryName11
         *
         * @return OrderItem
         */
        public function setOptionCategoryName11($optionCategoryName11 = null)
        {
            $this->option_category_name11 = $optionCategoryName11;

            return $this;
        }
        
        /**
         * Set optionCategoryName12.
         *
         * @param string|null $optionCategoryName12
         *
         * @return OrderItem
         */
        public function setOptionCategoryName12($optionCategoryName12 = null)
        {
            $this->option_category_name12 = $optionCategoryName12;

            return $this;
        }
        
        /**
         * Set optionCategoryName13.
         *
         * @param string|null $optionCategoryName13
         *
         * @return OrderItem
         */
        public function setOptionCategoryName13($optionCategoryName13 = null)
        {
            $this->option_category_name13 = $optionCategoryName13;

            return $this;
        }
        
        /**
         * Set optionCategoryName14.
         *
         * @param string|null $optionCategoryName14
         *
         * @return OrderItem
         */
        public function setOptionCategoryName14($optionCategoryName14 = null)
        {
            $this->option_category_name14 = $optionCategoryName14;

            return $this;
        }
        
        /**
         * Set optionCategoryName15.
         *
         * @param string|null $optionCategoryName15
         *
         * @return OrderItem
         */
        public function setOptionCategoryName15($optionCategoryName15 = null)
        {
            $this->option_category_name15 = $optionCategoryName15;

            return $this;
        }
        
        /**
         * Set optionCategoryName16.
         *
         * @param string|null $optionCategoryName16
         *
         * @return OrderItem
         */
        public function setOptionCategoryName16($optionCategoryName16 = null)
        {
            $this->option_category_name16 = $optionCategoryName16;

            return $this;
        }
        /**
         * Set optionCategoryName17.
         *
         * @param string|null $optionCategoryName17
         *
         * @return OrderItem
         */
        public function setOptionCategoryName17($optionCategoryName17 = null)
        {
            $this->option_category_name17 = $optionCategoryName17;

            return $this;
        }
        /**
         * Set optionCategoryName18.
         *
         * @param string|null $optionCategoryName18
         *
         * @return OrderItem
         */
        public function setOptionCategoryName18($optionCategoryName18 = null)
        {
            $this->option_category_name18 = $optionCategoryName18;

            return $this;
        }
        /**
         * Set optionCategoryName19.
         *
         * @param string|null $optionCategoryName19
         *
         * @return OrderItem
         */
        public function setOptionCategoryName19($optionCategoryName19 = null)
        {
            $this->option_category_name19 = $optionCategoryName19;

            return $this;
        }
        
        /**
         * Set optionCategoryName20.
         *
         * @param string|null $optionCategoryName20
         *
         * @return OrderItem
         */
        public function setOptionCategoryName20($optionCategoryName20 = null)
        {
            $this->option_category_name20 = $optionCategoryName20;

            return $this;
        }

        /**
         * Get optionCategoryName11.
         *
         * @return string|null
         */
        public function getOptionCategoryName11()
        {
            return $this->option_category_name11;
        }
        
        /**
         * Get optionCategoryName12.
         *
         * @return string|null
         */
        public function getOptionCategoryName12()
        {
            return $this->option_category_name12;
        }
        

        /**
         * Get optionCategoryName13.
         *
         * @return string|null
         */
        public function getOptionCategoryName13()
        {
            return $this->option_category_name13;
        }

        /**
         * Get optionCategoryName14.
         *
         * @return string|null
         */
        public function getOptionCategoryName14()
        {
            return $this->option_category_name14;
        }

        /**
         * Get optionCategoryName15.
         *
         * @return string|null
         */
        public function getOptionCategoryName15()
        {
            return $this->option_category_name15;
        }

        /**
         * Get optionCategoryName16.
         *
         * @return string|null
         */
        public function getOptionCategoryName16()
        {
            return $this->option_category_name16;
        }

        /**
         * Get optionCategoryName17.
         *
         * @return string|null
         */
        public function getOptionCategoryName17()
        {
            return $this->option_category_name17;
        }

        /**
         * Get optionCategoryName18.
         *
         * @return string|null
         */
        public function getOptionCategoryName18()
        {
            return $this->option_category_name18;
        }

        /**
         * Get optionCategoryName19.
         *
         * @return string|null
         */
        public function getOptionCategoryName19()
        {
            return $this->option_category_name19;
        }
        
        /**
         * Get optionCategoryName20.
         *
         * @return string|null
         */
        public function getOptionCategoryName20()
        {
            return $this->option_category_name20;
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
        
        
    }
}
