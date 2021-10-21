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

if (!class_exists('\Eccube\Entity\PointHistory')) {
    /**
     * PointHistory
     *
     * @ORM\Table(name="dtb_point_history")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\PointHistoryRepository")
     */
    class PointHistory extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

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
         * @var \Eccube\Entity\Customer
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Customer", inversedBy="PointHistorys")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
         * })
         */
        private $Customer;
        
        
        /**
         * @var string
         *
         * @ORM\Column(name="sum", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $sum = '0';

        /**
         * @var string
         *
         * @ORM\Column(name="ec_online", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $ec_online = '0';

        /**
         * @var string
         *
         * @ORM\Column(name="ec_yoyaku", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $ec_yoyaku = '0';

        /**
         * @var string
         *
         * @ORM\Column(name="app_birth", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $app_birth = '0';

        /**
         * @var string
         *
         * @ORM\Column(name="shop_honten", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $shop_honten = '0';

        /**
         * @var string
         *
         * @ORM\Column(name="shop_kishiwada", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $shop_kishiwada = '0';
        
        /**
         * @var boolean
         *
         * @ORM\Column(name="available", type="boolean", options={"default":true})
         */
        private $available = true;

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
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return PointHistory
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
         * @return PointHistory
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
         * Set customer.
         *
         * @param \Eccube\Entity\Customer|null $customer
         *
         * @return PointHistory
         */
        public function setCustomer(\Eccube\Entity\Customer $customer = null)
        {
            $this->Customer = $customer;

            return $this;
        }

        /**
         * Get customer.
         *
         * @return \Eccube\Entity\Customer|null
         */
        public function getCustomer()
        {
            return $this->Customer;
        }
        
        
        /**
         * Set sum
         *
         * @param string $sum
         *
         * @return Customer
         */
        public function setSum($sum)
        {
            $this->sum = $sum;

            return $this;
        }

        /**
         * Get sum
         *
         * @return string
         */
        public function getSum()
        {
            return $this->sum;
        }
        
        /**
         * Set ec_online
         *
         * @param string $ec_online
         *
         * @return Customer
         */
        public function setEcOnline($ec_online)
        {
            $this->ec_online = $ec_online;

            return $this;
        }

        /**
         * Get ec_online
         *
         * @return string
         */
        public function getEcOnline()
        {
            return $this->ec_online;
        }
        
        /**
         * Set ec_yoyaku
         *
         * @param string $ec_yoyaku
         *
         * @return Customer
         */
        public function setEcYoyaku($ec_yoyaku)
        {
            $this->ec_yoyaku = $ec_yoyaku;

            return $this;
        }

        /**
         * Get ec_yoyaku
         *
         * @return string
         */
        public function getEcYoyaku()
        {
            return $this->ec_yoyaku;
        }
        
        /**
         * Set app_birth
         *
         * @param string $app_birth
         *
         * @return Customer
         */
        public function setAppBirth($app_birth)
        {
            $this->app_birth = $app_birth;

            return $this;
        }

        /**
         * Get app_birth
         *
         * @return string
         */
        public function getAppBirth()
        {
            return $this->app_birth;
        }
        
        /**
         * Set shop_honten
         *
         * @param string $shop_honten
         *
         * @return Customer
         */
        public function setShopHonten($shop_honten)
        {
            $this->shop_honten = $shop_honten;

            return $this;
        }

        /**
         * Get shop_honten
         *
         * @return string
         */
        public function getShopHonten()
        {
            return $this->shop_honten;
        }
        
        /**
         * Set shop_kishiwada
         *
         * @param string $shop_kishiwada
         *
         * @return Customer
         */
        public function setShopKishiwada($shop_kishiwada)
        {
            $this->shop_kishiwada = $shop_kishiwada;

            return $this;
        }

        /**
         * Get shop_kishiwada
         *
         * @return string
         */
        public function getShopKishiwada()
        {
            return $this->shop_kishiwada;
        }
        
        
        /**
         * Set available
         *
         * @param boolean $available
         *
         * @return Delivery
         */
        public function setAvailable($available)
        {
            $this->available = $available;

            return $this;
        }

        /**
         * Is the available?
         *
         * @return boolean
         */
        public function isAvailable()
        {
            return $this->available;
        }
        
    }
}
