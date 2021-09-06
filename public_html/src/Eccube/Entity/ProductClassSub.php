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

if (!class_exists('\Eccube\Entity\ProductClassSub')) {
    /**
     * ProductClassSub
     *
     * @ORM\Table(name="dtb_product_class_sub")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ProductClassSubRepository")
     */
    class ProductClassSub extends \Eccube\Entity\AbstractEntity
    {
        private $tax_rate = false;

        /**
         * 商品規格名を含めた商品名を返す.
         *
         * @return string
         */
        public function formattedProductName()
        {
            $productName = $this->getProduct()->getName();
            if ($this->hasClassSubCategory()) {
                $productName .= ' - '.$this->getClassSubCategory()->getName();
            }

            return $productName;
        }

        /**
         * Is Enable
         *
         * @return bool
         *
         * @deprecated
         */
        public function isEnable()
        {
            return $this->getProduct()->isEnable();
        }

        /**
         * Set tax_rate
         *
         * @param  string $tax_rate
         *
         * @return ProductClassSub
         */
        public function setTaxRate($tax_rate)
        {
            $this->tax_rate = $tax_rate;

            return $this;
        }

        /**
         * Get tax_rate
         *
         * @return boolean
         */
        public function getTaxRate()
        {
            return $this->tax_rate;
        }

        /**
         * Has ClassSubCategory
         *
         * @return boolean
         */
        public function hasClassSubCategory()
        {
            return isset($this->ClassSubCategory);
        }

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="product_code", type="string", length=255, nullable=true)
         */
        private $code;

        /**
         * @var boolean
         *
         * @ORM\Column(name="visible", type="boolean", options={"default":true})
         */
        private $visible;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="currency_code", type="string", nullable=true)
         */
        private $currency_code;

        /**
         * @var \Eccube\Entity\TaxRule
         *
         * @ORM\OneToOne(targetEntity="Eccube\Entity\TaxRule", mappedBy="ProductClassSub", cascade={"persist","remove"})
         */
        private $TaxRule;

        /**
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="ProductClassSubes")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

        /**
         * @var \Eccube\Entity\Master\SaleType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\SaleType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="sale_type_id", referencedColumnName="id")
         * })
         */
        private $SaleType;

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="class_sub_category_id", referencedColumnName="id", nullable=true)
         * })
         */
        private $ClassSubCategory;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;

        public function __clone()
        {
            $this->id = null;
        }

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
         * Set code.
         *
         * @param string|null $code
         *
         * @return ProductClassSub
         */
        public function setCode($code = null)
        {
            $this->code = $code;

            return $this;
        }

        /**
         * Get code.
         *
         * @return string|null
         */
        public function getCode()
        {
            return $this->code;
        }

        /**
         * @return boolean
         */
        public function isVisible()
        {
            return $this->visible;
        }

        /**
         * @param boolean $visible
         *
         * @return ProductClassSub
         */
        public function setVisible($visible)
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return ProductClassSub
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return ProductClassSub
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
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
         * @return $this
         */
        public function setCurrencyCode($currencyCode = null)
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * Set taxRule.
         *
         * @param \Eccube\Entity\TaxRule|null $taxRule
         *
         * @return ProductClassSub
         */
        public function setTaxRule(\Eccube\Entity\TaxRule $taxRule = null)
        {
            $this->TaxRule = $taxRule;

            return $this;
        }

        /**
         * Get taxRule.
         *
         * @return \Eccube\Entity\TaxRule|null
         */
        public function getTaxRule()
        {
            return $this->TaxRule;
        }

        /**
         * Set product.
         *
         * @param \Eccube\Entity\Product|null $product
         *
         * @return ProductClassSub
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
         * Set saleType.
         *
         * @param \Eccube\Entity\Master\SaleType|null $saleType
         *
         * @return ProductClassSub
         */
        public function setSaleType(\Eccube\Entity\Master\SaleType $saleType = null)
        {
            $this->SaleType = $saleType;

            return $this;
        }

        /**
         * Get saleType.
         *
         * @return \Eccube\Entity\Master\SaleType|null
         */
        public function getSaleType()
        {
            return $this->SaleType;
        }

        /**
         * Set classSubCategory.
         *
         * @param \Eccube\Entity\ClassCategory|null $classSubCategory
         *
         * @return ProductClassSub
         */
        public function setClassSubCategory(\Eccube\Entity\ClassCategory $classSubCategory = null)
        {
            $this->ClassSubCategory = $classSubCategory;

            return $this;
        }

        /**
         * Get classSubCategory.
         *
         * @return \Eccube\Entity\ClassCategory|null
         */
        public function getClassSubCategory()
        {
            return $this->ClassSubCategory;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return ProductClassSub
         */
        public function setCreator(\Eccube\Entity\Member $creator = null)
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return \Eccube\Entity\Member|null
         */
        public function getCreator()
        {
            return $this->Creator;
        }
    }
}
