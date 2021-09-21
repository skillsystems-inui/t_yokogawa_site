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

namespace Plugin\SmartEC3\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Plugin\SmartEC3\Entity\SmartRegi')) {
    /**
     * SmartRegi
     *
     * @ORM\Table(name="plg_smart_ec3_data")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Plugin\SmartEC3\Repository\SmartRegiRepository")
     */
    
    class SmartRegi extends \Eccube\Entity\AbstractEntity
    {
        
        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getFileName();
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
         * @var \Plugin\SmartEC3\Entity\Master\SmartRegiStore
         *
         * @ORM\ManyToOne(targetEntity="Plugin\SmartEC3\Entity\Master\SmartRegiStore")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="store_type", referencedColumnName="id")
         * })
         */
        private $store_type;

        /**
         * @var \Plugin\SmartEC3\Entity\Master\SmartRegiPrice
         *
         * @ORM\ManyToOne(targetEntity="Plugin\SmartEC3\Entity\Master\SmartRegiPrice")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="price_type", referencedColumnName="id")
         * })
         */
        private $price_type;
        
        /**
         * @var \Eccube\Entity\TaxRule
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\TaxRule")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="tax", referencedColumnName="id")
         * })
         */
        private $tax;

        /**
         * @var \Plugin\SmartEC3\Entity\Master\SmartRegiTax
         *
         * @ORM\ManyToOne(targetEntity="Plugin\SmartEC3\Entity\Master\SmartRegiTax")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="tax_type", referencedColumnName="id")
         * })
         */
        private $tax_type;

        /**
         * @var string
         *
         * @ORM\Column(name="size", type="string", length=255)
         */
        private $size;

        /**
         * @var string
         *
         * @ORM\Column(name="color", type="string", length=255)
         */
        private $color;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Plugin\SmartEC3\Entity\SmartRegiImage", mappedBy="SmartRegi", cascade={"remove"})
         * @ORM\OrderBy({
         *     "sort_no"="ASC"
         * })
         */
        private $SmartRegiImage;

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
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;


        /**
         * Constructor
         */
        public function __construct()
        {
            $this->SmartRegiImage = new \Doctrine\Common\Collections\ArrayCollection();
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
         * Set store_type.
         *
         * @param \Plugin\SmartEC3\Entity\Master\SmartRegiStore|null $store_type
         *
         * @return SmartRegi
         */
        public function setStoreType(\Plugin\SmartEC3\Entity\Master\SmartRegiStore $store_type = null)
        {
            $this->store_type = $store_type;

            return $this;
        }

        /**
         * Get store_type.
         *
         * @return \Plugin\SmartEC3\Entity\Master\SmartRegiStore|null
         */
        public function getStoreType()
        {
            return $this->store_type;
        }

        /**
         * Set price_type.
         *
         * @param \Plugin\SmartEC3\Entity\Master\SmartRegiPrice|null $price_type
         *
         * @return SmartRegi
         */
        public function setPriceType(\Plugin\SmartEC3\Entity\Master\SmartRegiPrice $price_type=null)
        {
            $this->price_type = $price_type;

            return $this;
        }

        /**
         * Get price_type.
         *
         * @return \Plugin\SmartEC3\Entity\Master\SmartRegiPrice|null
         */
        public function getPriceType()
        {
            return $this->price_type;
        }

        /**
         * Set tax.
         *
         * @param \Eccube\Entity\TaxRule|null $tax
         *
         * @return SmartRegi
         */
        public function setTax(\Eccube\Entity\TaxRule $tax=null)
        {
            $this->tax = $tax;

            return $this;
        }

        /**
         * Get tax.
         *
         * @return \Eccube\Entity\TaxRule
         */
        public function getTax()
        {
            return $this->tax;
        }

        /**
         * Set tax_type.
         *
         * @param \Plugin\SmartEC3\Entity\Master\SmartRegiTax|null $tax_type
         *
         * @return SmartRegi
         */
        public function setTaxType(\Plugin\SmartEC3\Entity\Master\SmartRegiTax $tax_type = null)
        {
            $this->tax_type = $tax_type;

            return $this;
        }

        /**
         * Get tax_type.
         *
         * @return \Plugin\SmartEC3\Entity\Master\SmartRegiTax|null
         */
        public function getTaxType()
        {
            return $this->tax_type;
        }

        /**
         * @param string $size
         *
         * @return SmartRegi;
         */
        public function setSize($size)
        {
            $this->size = $size;

            return $this;
        }

        /**
         * Get size.
         * 
         * @return string
         */
        public function getSize()
        {
            return $this->size;
        }
        
        /**
         * @param string $color
         *
         * @return SmartRegi;
         */
        public function setColor($color)
        {
            $this->color = $color;

            return $this;
        }

        /**
         * Get color.
         * 
         * @return string
         */
        public function getColor()
        {
            return $this->color;
        }

        /**
         * Add smartImage.
         *
         * @param \Plugin\SmartEC3\Entity\SmartRegiImage $smartImage
         *
         * @return SmartRegi
         */
        public function addSmartRegiImage(\Plugin\SmartEC3\Entity\SmartRegiImage $smartImage)
        {
            $this->SmartRegiImage[] = $smartImage;

            return $this;
        }

        /**
         * Remove smartImage.
         *
         * @param \Plugin\SmartEC3\Entity\SmartRegiImage $smartImage
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeSmartRegiImage(\Plugin\SmartEC3\Entity\SmartRegiImage $smartImage)
        {
            return $this->SmartRegiImage->removeElement($smartImage);
        }

        /**
         * Get smartImage.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getSmartRegiImage()
        {
            return $this->SmartRegiImage;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return SmartRegi
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
         * @return Product
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
         * Set product.
         *
         * @param \Eccube\Entity\Product|null $product
         *
         * @return SmartRegi
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
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return SmartRegi
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
