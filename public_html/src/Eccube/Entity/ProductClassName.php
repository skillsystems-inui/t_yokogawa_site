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

if (!class_exists('\Eccube\Entity\ProductClassName')) {
    /**
     * ProductClassName
     *
     * @ORM\Table(name="dtb_product_class_name")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ProductClassNameRepository")
     */
    class ProductClassName extends \Eccube\Entity\AbstractEntity
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
         * @ORM\Column(name="class_name_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $class_name_id;

        /**
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="ProductClassNames")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

        /**
         * @var \Eccube\Entity\ClassName
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassName", inversedBy="ProductClassNames")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="class_name_id", referencedColumnName="id")
         * })
         */
        private $ClassName;
        
        /**
         * Set productId.
         *
         * @param int $productId
         *
         * @return ProductClassName
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
         * Set classNameId.
         *
         * @param int $classNameId
         *
         * @return ProductClassName
         */
        public function setClassNameId($classNameId)
        {
            $this->class_name_id = $classNameId;

            return $this;
        }

        /**
         * Get classNameId.
         *
         * @return int
         */
        public function getClassNameId()
        {        
            return $this->class_name_id;
        }

        /**
         * Set product.
         *
         * @param \Eccube\Entity\Product|null $product
         *
         * @return ProductClassName
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
         * @return ProductClassName
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
        
    }
}
