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

if (!class_exists('\Eccube\Entity\ProductClassCategory')) {
    /**
     * ProductClassCategory
     *
     * @ORM\Table(name="dtb_product_class_category")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ProductClassCategoryRepository")
     */
    class ProductClassCategory extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="product_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $product_id;

        /**
         * @var int
         *
         * @ORM\Column(name="class_category_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $class_category_id;

        /**
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="ProductClassCategories")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

        /**
         * @var \Eccube\Entity\ClassName
         */
        private $ClassName;
        

        /**
         * @var \Eccube\Entity\ClassCategory
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory", inversedBy="ProductClassCategories")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="class_category_id", referencedColumnName="id")
         * })
         */
        private $ClassCategory;
        
        /**
         * Set productId.
         *
         * @param int $productId
         *
         * @return ProductClassCategory
         */
        public function setProductId($productId)
        {
            $this->product_id = $productId;

            return $this;
        }

        /**
         * Get productId.
         *
         * @return int
         */
        public function getProductId()
        {
            return $this->product_id;
        }

        /**
         * Set classCategoryId.
         *
         * @param int $classCategoryId
         *
         * @return ProductClassCategory
         */
        public function setClassCategoryId($classCategoryId)
        {
            $this->class_category_id = $classCategoryId;

            return $this;
        }

        /**
         * Get classCategoryId.
         *
         * @return int
         */
        public function getClassCategoryId()
        {        
            return $this->class_category_id;
        }

        /**
         * Set product.
         *
         * @param \Eccube\Entity\Product|null $product
         *
         * @return ProductClassCategory
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
         * Set className.
         *
         * @param \Eccube\Entity\ClassName|null $className
         *
         * @return ProductClassCategory
         */
        public function setClassName(\Eccube\Entity\ClassName $className = null)
        {
            $this->ClassName = $className;

            return $this;
        }

        /**
         * Get className.
         *
         * @return \Eccube\Entity\ClassName|null
         */
        public function getClassName()
        {
            return $this->ClassName;
        }

        /**
         * Set classCategory.
         *
         * @param \Eccube\Entity\ClassCategory|null $classCategory
         *
         * @return ProductClassCategory
         */
        public function setClassCategory(\Eccube\Entity\ClassCategory $classCategory = null)
        {
            $this->ClassCategory = $classCategory;

            return $this;
        }

        /**
         * Get classCategory.
         *
         * @return \Eccube\Entity\ClassCategory|null
         */
        public function getClassCategory()
        {
            return $this->ClassCategory;
        }
        
    }
}
