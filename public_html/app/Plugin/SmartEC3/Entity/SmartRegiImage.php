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

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('Plugin\SmartEC3\Entity\SmartRegiImage')) {
    /**
     * SmartRegiImage
     *
     * @ORM\Table(name="plg_smart_ec3_image")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Plugin\SmartEC3\Repository\SmartRegiImageRepository")
     */
    class SmartRegiImage extends \Eccube\Entity\AbstractEntity
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
         * @var string
         *
         * @ORM\Column(name="file_name", type="string", length=255)
         */
        private $file_name;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
         */
        private $sort_no;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \Plugin\SmartEC3\Entity\SmartRegi
         *
         * @ORM\ManyToOne(targetEntity="Plugin\SmartEC3\Entity\SmartRegi", inversedBy="SmartRegiImage")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="smart_id", referencedColumnName="id")
         * })
         */
        private $SmartRegi;

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
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set fileName.
         *
         * @param string $fileName
         *
         * @return SmartRegiImage
         */
        public function setFileName($fileName)
        {
            $this->file_name = $fileName;

            return $this;
        }

        /**
         * Get fileName.
         *
         * @return string
         */
        public function getFileName()
        {
            return $this->file_name;
        }

        /**
         * Set sortNo.
         *
         * @param int $sortNo
         *
         * @return SmartRegiImage
         */
        public function setSortNo($sortNo)
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return SmartRegiImage
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
         * Set SmartRegi.
         *
         * @param \Plugin\SmartEC3\Entity\SmartRegi|null $smart_regi
         *
         * @return SmartRegiImage
         */
        public function setSmartRegi(\Plugin\SmartEC3\Entity\SmartRegi $smart_regi = null)
        {
            $this->SmartRegi = $smart_regi;

            return $this;
        }

        /**
         * Get SmartRegi.
         *
         * @return \Plugin\SmartEC3\Entity\SmartRegi|null
         */
        public function getSmartRegi()
        {
            return $this->SmartRegi;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return SmartRegiImage
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
