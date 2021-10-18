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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;


    /**
     * Customer
     *
     * @ORM\Table(name="dtb_customer", uniqueConstraints={@ORM\UniqueConstraint(name="secret_key", columns={"secret_key"})}, indexes={@ORM\Index(name="dtb_customer_buy_times_idx", columns={"buy_times"}), @ORM\Index(name="dtb_customer_buy_total_idx", columns={"buy_total"}), @ORM\Index(name="dtb_customer_create_date_idx", columns={"create_date"}), @ORM\Index(name="dtb_customer_update_date_idx", columns={"update_date"}), @ORM\Index(name="dtb_customer_last_buy_date_idx", columns={"last_buy_date"}), @ORM\Index(name="dtb_customer_email_idx", columns={"email"})})
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\CustomerRepository")
     */
    class Customer extends \Eccube\Entity\AbstractEntity implements UserInterface
    {
    use \Plugin\GmoPaymentGateway4\Entity\CustomerTrait;

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
         * @ORM\Column(name="name01", type="string", length=255)
         */
        private $name01;

        /**
         * @var string
         *
         * @ORM\Column(name="name02", type="string", length=255)
         */
        private $name02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="kana01", type="string", length=255, nullable=true)
         */
        private $kana01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="kana02", type="string", length=255, nullable=true)
         */
        private $kana02;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="name_all", type="string", length=510, nullable=true)
         */
        private $name_all;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="kana_all", type="string", length=510, nullable=true)
         */
        private $kana_all;
        
         /**
         * @var string
         *
         * @ORM\Column(name="mainname", type="string", length=510)
         */
        private $mainname;

        /**
         * @var string|null
         *
         * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
         */
        private $company_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="postal_code", type="string", length=8, nullable=true)
         */
        private $postal_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr01", type="string", length=255, nullable=true)
         */
        private $addr01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr02", type="string", length=255, nullable=true)
         */
        private $addr02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr_all", type="string", length=510, nullable=true)
         */
        private $addr_all;

        /**
         * @var string
         *
         * @ORM\Column(name="email", type="string", length=255)
         */
        private $email;

        /**
         * @var string|null
         *
         * @ORM\Column(name="phone_number", type="string", length=14, nullable=true)
         */
        private $phone_number;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="birth", type="datetimetz", nullable=true)
         */
        private $birth;

        /**
         * @var string|null
         *
         * @ORM\Column(name="password", type="string", length=255)
         */
        private $password;

        /**
         * @var string|null
         *
         * @ORM\Column(name="salt", type="string", length=255, nullable=true)
         */
        private $salt;

        /**
         * @var string
         *
         * @ORM\Column(name="secret_key", type="string", length=255)
         */
        private $secret_key;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="first_buy_date", type="datetimetz", nullable=true)
         */
        private $first_buy_date;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="last_buy_date", type="datetimetz", nullable=true)
         */
        private $last_buy_date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="buy_times", type="decimal", precision=10, scale=0, nullable=true, options={"unsigned":true,"default":0})
         */
        private $buy_times = 0;

        /**
         * @var string|null
         *
         * @ORM\Column(name="buy_total", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true,"default":0})
         */
        private $buy_total = 0;

        /**
         * @var string|null
         *
         * @ORM\Column(name="note", type="string", length=4000, nullable=true)
         */
        private $note;

        /**
         * @var string|null
         *
         * @ORM\Column(name="reset_key", type="string", length=255, nullable=true)
         */
        private $reset_key;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="reset_expire", type="datetimetz", nullable=true)
         */
        private $reset_expire;

        /**
         * @var string
         *
         * @ORM\Column(name="point", type="decimal", precision=12, scale=0, options={"unsigned":false,"default":0})
         */
        private $point = '0';

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
         * @ORM\Column(name="customer_code", type="string", length=255, nullable=true)
         */
        private $customer_code;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="device_token1", type="string", length=200, nullable=true)
         */
        private $device_token1;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name01", type="string", length=50)
         */
        private $family_name01;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex01_id", referencedColumnName="id")
         * })
         */
        private $family_sex01;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation01", type="string", length=50)
         */
        private $family_relation01;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth01", type="datetimetz", nullable=true)
         */
        private $family_birth01;
		
        /**
         * @var string
         *
         * @ORM\Column(name="family_name02", type="string", length=50)
         */
        private $family_name02;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex02_id", referencedColumnName="id")
         * })
         */
        private $family_sex02;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation02", type="string", length=50)
         */
        private $family_relation02;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth02", type="datetimetz", nullable=true)
         */
        private $family_birth02;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name03", type="string", length=50)
         */
        private $family_name03;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex03_id", referencedColumnName="id")
         * })
         */
        private $family_sex03;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation03", type="string", length=50)
         */
        private $family_relation03;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth03", type="datetimetz", nullable=true)
         */
        private $family_birth03;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name04", type="string", length=50)
         */
        private $family_name04;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex04_id", referencedColumnName="id")
         * })
         */
        private $family_sex04;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation04", type="string", length=50)
         */
        private $family_relation04;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth04", type="datetimetz", nullable=true)
         */
        private $family_birth04;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name05", type="string", length=50)
         */
        private $family_name05;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex05_id", referencedColumnName="id")
         * })
         */
        private $family_sex05;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation05", type="string", length=50)
         */
        private $family_relation05;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth05", type="datetimetz", nullable=true)
         */
        private $family_birth05;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name06", type="string", length=50)
         */
        private $family_name06;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex06_id", referencedColumnName="id")
         * })
         */
        private $family_sex06;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation06", type="string", length=50)
         */
        private $family_relation06;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth06", type="datetimetz", nullable=true)
         */
        private $family_birth06;
		
        /**
         * @var string
         *
         * @ORM\Column(name="family_name07", type="string", length=50)
         */
        private $family_name07;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex07_id", referencedColumnName="id")
         * })
         */
        private $family_sex07;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation07", type="string", length=50)
         */
        private $family_relation07;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth07", type="datetimetz", nullable=true)
         */
        private $family_birth07;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name08", type="string", length=50)
         */
        private $family_name08;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex08_id", referencedColumnName="id")
         * })
         */
        private $family_sex08;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation08", type="string", length=50)
         */
        private $family_relation08;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth08", type="datetimetz", nullable=true)
         */
        private $family_birth08;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name09", type="string", length=50)
         */
        private $family_name09;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex09_id", referencedColumnName="id")
         * })
         */
        private $family_sex09;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation09", type="string", length=50)
         */
        private $family_relation09;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth09", type="datetimetz", nullable=true)
         */
        private $family_birth09;
        
        /**
         * @var string
         *
         * @ORM\Column(name="family_name10", type="string", length=50)
         */
        private $family_name10;
        
        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_sex10_id", referencedColumnName="id")
         * })
         */
        private $family_sex10;

        /**
         * @var string
         *
         * @ORM\Column(name="family_relation10", type="string", length=50)
         */
        private $family_relation10;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="family_birth10", type="datetimetz", nullable=true)
         */
        private $family_birth10;
        
        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\CustomerFavoriteProduct", mappedBy="Customer", cascade={"remove"})
         */
        private $CustomerFavoriteProducts;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\CustomerAddress", mappedBy="Customer", cascade={"remove"})
         * @ORM\OrderBy({
         *     "id"="ASC"
         * })
         */
        private $CustomerAddresses;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\Order", mappedBy="Customer")
         */
        private $Orders;

        /**
         * @var \Eccube\Entity\Master\CustomerStatus
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\CustomerStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="customer_status_id", referencedColumnName="id")
         * })
         */
        private $Status;

        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="sex_id", referencedColumnName="id")
         * })
         */
        private $Sex;

        /**
         * @var \Eccube\Entity\Master\Job
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Job")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="job_id", referencedColumnName="id")
         * })
         */
        private $Job;

        /**
         * @var \Eccube\Entity\Master\Country
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
         * })
         */
        private $Country;

        /**
         * @var \Eccube\Entity\Master\Pref
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         */
        private $Pref;

        /**
         * @var \Eccube\Entity\Master\Familymain
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Familymain")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="is_family_main", referencedColumnName="id")
         * })
         */
        private $Familymain;
        
        /**
         * @var \Eccube\Entity\Customer
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Customer")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="family_main_customer_id", referencedColumnName="id")
         * })
         */
        private $Maincustomer;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->CustomerFavoriteProducts = new \Doctrine\Common\Collections\ArrayCollection();
            $this->CustomerAddresses = new \Doctrine\Common\Collections\ArrayCollection();
            $this->Orders = new \Doctrine\Common\Collections\ArrayCollection();

            $this->setBuyTimes(0);
            $this->setBuyTotal(0);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) ($this->getName01().' '.$this->getName02());
        }

        /**
         * {@inheritdoc}
         */
        public function getRoles()
        {
            return ['ROLE_USER'];
        }

        /**
         * {@inheritdoc}
         */
        public function getUsername()
        {
            return $this->email;
        }

        /**
         * {@inheritdoc}
         */
        public function eraseCredentials()
        {
        }

        // TODO: できればFormTypeで行いたい
        public static function loadValidatorMetadata(ClassMetadata $metadata)
        {
            $metadata->addConstraint(new UniqueEntity([
                'fields' => 'email',
                'message' => 'form_error.customer_already_exists',
                'repositoryMethod' => 'getNonWithdrawingCustomers',
            ]));
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
         * Set name01.
         *
         * @param string $name01
         *
         * @return Customer
         */
        public function setName01($name01)
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string
         */
        public function getName01()
        {
            return $this->name01;
        }

        /**
         * Set name02.
         *
         * @param string $name02
         *
         * @return Customer
         */
        public function setName02($name02)
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string
         */
        public function getName02()
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         *
         * @param string|null $kana01
         *
         * @return Customer
         */
        public function setKana01($kana01 = null)
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string|null
         */
        public function getKana01()
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         *
         * @param string|null $kana02
         *
         * @return Customer
         */
        public function setKana02($kana02 = null)
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string|null
         */
        public function getKana02()
        {
            return $this->kana02;
        }


        /**
         * Set name_all.
         *
         * @param string $name_all
         *
         * @return Customer
         */
        public function setNameAll($name_all)
        {
            $this->name_all = $name_all;

            return $this;
        }

        /**
         * Get name_all.
         *
         * @return string
         */
        public function getNameAll()
        {
            return $this->name_all;
        }

        /**
         * Set kana_all.
         *
         * @param string|null $kana_all
         *
         * @return Customer
         */
        public function setKanaAll($kana_all = null)
        {
            $this->kana_all = $kana_all;

            return $this;
        }

        /**
         * Get kana_all.
         *
         * @return string|null
         */
        public function getKanaAll()
        {
            return $this->kana_all;
        }
        
        /**
         * Set companyName.
         *
         * @param string|null $companyName
         *
         * @return Customer
         */
        public function setCompanyName($companyName = null)
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName()
        {
            return $this->company_name;
        }

        /**
         * Set postal_code.
         *
         * @param string|null $postal_code
         *
         * @return Customer
         */
        public function setPostalCode($postal_code = null)
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode()
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         *
         * @param string|null $addr01
         *
         * @return Customer
         */
        public function setAddr01($addr01 = null)
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01()
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         *
         * @param string|null $addr02
         *
         * @return Customer
         */
        public function setAddr02($addr02 = null)
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02()
        {
            return $this->addr02;
        }
        
        /**
         * Set addr_all.
         *
         * @param string|null $addr_all
         *
         * @return Customer
         */
        public function setAddrAll($addr_all = null)
        {
            $this->addr_all = $addr_all;

            return $this;
        }

        /**
         * Get addr_all.
         *
         * @return string|null
         */
        public function getAddrAll()
        {
            return $this->addr_all;
        }

        /**
         * Set email.
         *
         * @param string $email
         *
         * @return Customer
         */
        public function setEmail($email)
        {
            $this->email = $email;

            return $this;
        }

        /**
         * Get email.
         *
         * @return string
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * Set phone_number.
         *
         * @param string|null $phone_number
         *
         * @return Customer
         */
        public function setPhoneNumber($phone_number = null)
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber()
        {
            return $this->phone_number;
        }

        /**
         * Set birth.
         *
         * @param \DateTime|null $birth
         *
         * @return Customer
         */
        public function setBirth($birth = null)
        {
            $this->birth = $birth;

            return $this;
        }

        /**
         * Get birth.
         *
         * @return \DateTime|null
         */
        public function getBirth()
        {
            return $this->birth;
        }

        /**
         * Set password.
         *
         * @param string|null $password
         *
         * @return Customer
         */
        public function setPassword($password = null)
        {
            $this->password = $password;

            return $this;
        }

        /**
         * Get password.
         *
         * @return string|null
         */
        public function getPassword()
        {
            return $this->password;
        }

        /**
         * Set salt.
         *
         * @param string|null $salt
         *
         * @return Customer
         */
        public function setSalt($salt = null)
        {
            $this->salt = $salt;

            return $this;
        }

        /**
         * Get salt.
         *
         * @return string|null
         */
        public function getSalt()
        {
            return $this->salt;
        }

        /**
         * Set secretKey.
         *
         * @param string $secretKey
         *
         * @return Customer
         */
        public function setSecretKey($secretKey)
        {
            $this->secret_key = $secretKey;

            return $this;
        }

        /**
         * Get secretKey.
         *
         * @return string
         */
        public function getSecretKey()
        {
            return $this->secret_key;
        }

        /**
         * Set firstBuyDate.
         *
         * @param \DateTime|null $firstBuyDate
         *
         * @return Customer
         */
        public function setFirstBuyDate($firstBuyDate = null)
        {
            $this->first_buy_date = $firstBuyDate;

            return $this;
        }

        /**
         * Get firstBuyDate.
         *
         * @return \DateTime|null
         */
        public function getFirstBuyDate()
        {
            return $this->first_buy_date;
        }

        /**
         * Set lastBuyDate.
         *
         * @param \DateTime|null $lastBuyDate
         *
         * @return Customer
         */
        public function setLastBuyDate($lastBuyDate = null)
        {
            $this->last_buy_date = $lastBuyDate;

            return $this;
        }

        /**
         * Get lastBuyDate.
         *
         * @return \DateTime|null
         */
        public function getLastBuyDate()
        {
            return $this->last_buy_date;
        }

        /**
         * Set buyTimes.
         *
         * @param string|null $buyTimes
         *
         * @return Customer
         */
        public function setBuyTimes($buyTimes = null)
        {
            $this->buy_times = $buyTimes;

            return $this;
        }

        /**
         * Get buyTimes.
         *
         * @return string|null
         */
        public function getBuyTimes()
        {
            return $this->buy_times;
        }

        /**
         * Set buyTotal.
         *
         * @param string|null $buyTotal
         *
         * @return Customer
         */
        public function setBuyTotal($buyTotal = null)
        {
            $this->buy_total = $buyTotal;

            return $this;
        }

        /**
         * Get buyTotal.
         *
         * @return string|null
         */
        public function getBuyTotal()
        {
            return $this->buy_total;
        }

        /**
         * Set note.
         *
         * @param string|null $note
         *
         * @return Customer
         */
        public function setNote($note = null)
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote()
        {
            return $this->note;
        }

        /**
         * Set resetKey.
         *
         * @param string|null $resetKey
         *
         * @return Customer
         */
        public function setResetKey($resetKey = null)
        {
            $this->reset_key = $resetKey;

            return $this;
        }

        /**
         * Get resetKey.
         *
         * @return string|null
         */
        public function getResetKey()
        {
            return $this->reset_key;
        }

        /**
         * Set resetExpire.
         *
         * @param \DateTime|null $resetExpire
         *
         * @return Customer
         */
        public function setResetExpire($resetExpire = null)
        {
            $this->reset_expire = $resetExpire;

            return $this;
        }

        /**
         * Get resetExpire.
         *
         * @return \DateTime|null
         */
        public function getResetExpire()
        {
            return $this->reset_expire;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Customer
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
         * @return Customer
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
         * Add customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return Customer
         */
        public function addCustomerFavoriteProduct(\Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct)
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerFavoriteProduct(\Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct)
        {
            return $this->CustomerFavoriteProducts->removeElement($customerFavoriteProduct);
        }

        /**
         * Get customerFavoriteProducts.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCustomerFavoriteProducts()
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Add customerAddress.
         *
         * @param \Eccube\Entity\CustomerAddress $customerAddress
         *
         * @return Customer
         */
        public function addCustomerAddress(\Eccube\Entity\CustomerAddress $customerAddress)
        {
            $this->CustomerAddresses[] = $customerAddress;

            return $this;
        }

        /**
         * Remove customerAddress.
         *
         * @param \Eccube\Entity\CustomerAddress $customerAddress
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerAddress(\Eccube\Entity\CustomerAddress $customerAddress)
        {
            return $this->CustomerAddresses->removeElement($customerAddress);
        }

        /**
         * Get customerAddresses.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCustomerAddresses()
        {
            return $this->CustomerAddresses;
        }

        /**
         * Add order.
         *
         * @param \Eccube\Entity\Order $order
         *
         * @return Customer
         */
        public function addOrder(\Eccube\Entity\Order $order)
        {
            $this->Orders[] = $order;

            return $this;
        }

        /**
         * Remove order.
         *
         * @param \Eccube\Entity\Order $order
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrder(\Eccube\Entity\Order $order)
        {
            return $this->Orders->removeElement($order);
        }

        /**
         * Get orders.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getOrders()
        {
            return $this->Orders;
        }

        /**
         * Set status.
         *
         * @param \Eccube\Entity\Master\CustomerStatus|null $status
         *
         * @return Customer
         */
        public function setStatus(\Eccube\Entity\Master\CustomerStatus $status = null)
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         *
         * @return \Eccube\Entity\Master\CustomerStatus|null
         */
        public function getStatus()
        {
            return $this->Status;
        }

        /**
         * Set sex.
         *
         * @param \Eccube\Entity\Master\Sex|null $sex
         *
         * @return Customer
         */
        public function setSex(\Eccube\Entity\Master\Sex $sex = null)
        {
            $this->Sex = $sex;

            return $this;
        }

        /**
         * Get sex.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getSex()
        {
            return $this->Sex;
        }

        /**
         * Set job.
         *
         * @param \Eccube\Entity\Master\Job|null $job
         *
         * @return Customer
         */
        public function setJob(\Eccube\Entity\Master\Job $job = null)
        {
            $this->Job = $job;

            return $this;
        }

        /**
         * Get job.
         *
         * @return \Eccube\Entity\Master\Job|null
         */
        public function getJob()
        {
            return $this->Job;
        }

        /**
         * Set country.
         *
         * @param \Eccube\Entity\Master\Country|null $country
         *
         * @return Customer
         */
        public function setCountry(\Eccube\Entity\Master\Country $country = null)
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return \Eccube\Entity\Master\Country|null
         */
        public function getCountry()
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param \Eccube\Entity\Master\Pref|null $pref
         *
         * @return Customer
         */
        public function setPref(\Eccube\Entity\Master\Pref $pref = null)
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return \Eccube\Entity\Master\Pref|null
         */
        public function getPref()
        {
            return $this->Pref;
        }

        /**
         * Set point
         *
         * @param string $point
         *
         * @return Customer
         */
        public function setPoint($point)
        {
            $this->point = $point;

            return $this;
        }

        /**
         * Get point
         *
         * @return string
         */
        public function getPoint()
        {
            return $this->point;
        }
        
        
        /**
         * Set familymain.
         *
         * @param \Eccube\Entity\Master\Familymain|null $familymain
         *
         * @return Customer
         */
        public function setFamilymain(\Eccube\Entity\Master\Familymain $familymain = null)
        {
            $this->Familymain = $familymain;

            return $this;
        }

        /**
         * Get familymain.
         *
         * @return \Eccube\Entity\Master\Familymain|null
         */
        public function getFamilymain()
        {
            return $this->Familymain;
        }
        
        /**
         * Set maincustomer.
         *
         * @param \Eccube\Entity\Customer|null $maincustomer
         *
         * @return Customer
         */
        public function setMaincustomer(\Eccube\Entity\Customer $maincustomer = null)
        {
            $this->Maincustomer = $maincustomer;

            return $this;
        }

        /**
         * Get maincustomer.
         *
         * @return \Eccube\Entity\Customer|null
         */
        public function getMaincustomer()
        {
            return $this->Maincustomer;
        }
        
        
        /**
         * Set mainname.
         *
         * @param string $mainname
         *
         * @return Customer
         */
        public function setMainname($mainname)
        {
            $this->mainname = $mainname;

            return $this;
        }

        /**
         * Get mainname.
         *
         * @return string
         */
        public function getMainname()
        {
            return $this->mainname;
        }
                
        /**
         * Set family_name01.
         *
         * @param string $family_name01
         *
         * @return Customer
         */
        public function setFamilyName01($family_name01)
        {
            $this->family_name01 = $family_name01;

            return $this;
        }

        /**
         * Get family_name01.
         *
         * @return string
         */
        public function getFamilyName01()
        {
            return $this->family_name01;
        }
        
        /**
         * Set family_relation01.
         *
         * @param string $family_relation01
         *
         * @return Customer
         */
        public function setFamilyRelation01($family_relation01)
        {
            $this->family_relation01 = $family_relation01;

            return $this;
        }

        /**
         * Get family_relation01.
         *
         * @return string
         */
        public function getFamilyRelation01()
        {
            return $this->family_relation01;
        }
        
        
        /**
         * Set family_birth01.
         *
         * @param string $family_birth01
         *
         * @return Customer
         */
        public function setFamilyBirth01($family_birth01)
        {
            $this->family_birth01 = $family_birth01;

            return $this;
        }

        /**
         * Get family_birth01.
         *
         * @return string
         */
        public function getFamilyBirth01()
        {
            return $this->family_birth01;
        }
        
        /**
         * Set family_name02.
         *
         * @param string $family_name02
         *
         * @return Customer
         */
        public function setFamilyName02($family_name02)
        {
            $this->family_name02 = $family_name02;

            return $this;
        }

        /**
         * Get family_name02.
         *
         * @return string
         */
        public function getFamilyName02()
        {
            return $this->family_name02;
        }
        
        /**
         * Set family_relation02.
         *
         * @param string $family_relation02
         *
         * @return Customer
         */
        public function setFamilyRelation02($family_relation02)
        {
            $this->family_relation02 = $family_relation02;

            return $this;
        }

        /**
         * Get family_relation02.
         *
         * @return string
         */
        public function getFamilyRelation02()
        {
            return $this->family_relation02;
        }
        
        
        /**
         * Set family_birth02.
         *
         * @param string $family_birth02
         *
         * @return Customer
         */
        public function setFamilyBirth02($family_birth02)
        {
            $this->family_birth02 = $family_birth02;

            return $this;
        }

        /**
         * Get family_birth02.
         *
         * @return string
         */
        public function getFamilyBirth02()
        {
            return $this->family_birth02;
        }
        
        /**
         * Set family_name03.
         *
         * @param string $family_name03
         *
         * @return Customer
         */
        public function setFamilyName03($family_name03)
        {
            $this->family_name03 = $family_name03;

            return $this;
        }

        /**
         * Get family_name03.
         *
         * @return string
         */
        public function getFamilyName03()
        {
            return $this->family_name03;
        }
        
        /**
         * Set family_relation03.
         *
         * @param string $family_relation03
         *
         * @return Customer
         */
        public function setFamilyRelation03($family_relation03)
        {
            $this->family_relation03 = $family_relation03;

            return $this;
        }

        /**
         * Get family_relation03.
         *
         * @return string
         */
        public function getFamilyRelation03()
        {
            return $this->family_relation03;
        }
        
        /**
         * Set family_birth03.
         *
         * @param string $family_birth03
         *
         * @return Customer
         */
        public function setFamilyBirth03($family_birth03)
        {
            $this->family_birth03 = $family_birth03;

            return $this;
        }

        /**
         * Get family_birth03.
         *
         * @return string
         */
        public function getFamilyBirth03()
        {
            return $this->family_birth03;
        }
        
        /**
         * Set family_name04.
         *
         * @param string $family_name04
         *
         * @return Customer
         */
        public function setFamilyName04($family_name04)
        {
            $this->family_name04 = $family_name04;

            return $this;
        }

        /**
         * Get family_name04.
         *
         * @return string
         */
        public function getFamilyName04()
        {
            return $this->family_name04;
        }
        
        /**
         * Set family_relation04.
         *
         * @param string $family_relation04
         *
         * @return Customer
         */
        public function setFamilyRelation04($family_relation04)
        {
            $this->family_relation04 = $family_relation04;

            return $this;
        }

        /**
         * Get family_relation04.
         *
         * @return string
         */
        public function getFamilyRelation04()
        {
            return $this->family_relation04;
        }
        
        
        /**
         * Set family_birth04.
         *
         * @param string $family_birth04
         *
         * @return Customer
         */
        public function setFamilyBirth04($family_birth04)
        {
            $this->family_birth04 = $family_birth04;

            return $this;
        }

        /**
         * Get family_birth04.
         *
         * @return string
         */
        public function getFamilyBirth04()
        {
            return $this->family_birth04;
        }
        
        /**
         * Set family_name05.
         *
         * @param string $family_name05
         *
         * @return Customer
         */
        public function setFamilyName05($family_name05)
        {
            $this->family_name05 = $family_name05;

            return $this;
        }

        /**
         * Get family_name05.
         *
         * @return string
         */
        public function getFamilyName05()
        {
            return $this->family_name05;
        }
        
        /**
         * Set family_relation05.
         *
         * @param string $family_relation05
         *
         * @return Customer
         */
        public function setFamilyRelation05($family_relation05)
        {
            $this->family_relation05 = $family_relation05;

            return $this;
        }

        /**
         * Get family_relation05.
         *
         * @return string
         */
        public function getFamilyRelation05()
        {
            return $this->family_relation05;
        }
        
        
        /**
         * Set family_birth05.
         *
         * @param string $family_birth05
         *
         * @return Customer
         */
        public function setFamilyBirth05($family_birth05)
        {
            $this->family_birth05 = $family_birth05;

            return $this;
        }

        /**
         * Get family_birth05.
         *
         * @return string
         */
        public function getFamilyBirth05()
        {
            return $this->family_birth05;
        }
        
        /**
         * Set family_name06.
         *
         * @param string $family_name06
         *
         * @return Customer
         */
        public function setFamilyName06($family_name06)
        {
            $this->family_name06 = $family_name06;

            return $this;
        }

        /**
         * Get family_name06.
         *
         * @return string
         */
        public function getFamilyName06()
        {
            return $this->family_name06;
        }
        
        /**
         * Set family_relation06.
         *
         * @param string $family_relation06
         *
         * @return Customer
         */
        public function setFamilyRelation06($family_relation06)
        {
            $this->family_relation06 = $family_relation06;

            return $this;
        }

        /**
         * Get family_relation06.
         *
         * @return string
         */
        public function getFamilyRelation06()
        {
            return $this->family_relation06;
        }
        
        
        /**
         * Set family_birth06.
         *
         * @param string $family_birth06
         *
         * @return Customer
         */
        public function setFamilyBirth06($family_birth06)
        {
            $this->family_birth06 = $family_birth06;

            return $this;
        }

        /**
         * Get family_birth06.
         *
         * @return string
         */
        public function getFamilyBirth06()
        {
            return $this->family_birth06;
        }
        
        /**
         * Set family_name07.
         *
         * @param string $family_name07
         *
         * @return Customer
         */
        public function setFamilyName07($family_name07)
        {
            $this->family_name07 = $family_name07;

            return $this;
        }

        /**
         * Get family_name07.
         *
         * @return string
         */
        public function getFamilyName07()
        {
            return $this->family_name07;
        }
        
        /**
         * Set family_relation07.
         *
         * @param string $family_relation07
         *
         * @return Customer
         */
        public function setFamilyRelation07($family_relation07)
        {
            $this->family_relation07 = $family_relation07;

            return $this;
        }

        /**
         * Get family_relation07.
         *
         * @return string
         */
        public function getFamilyRelation07()
        {
            return $this->family_relation07;
        }
        
        
        /**
         * Set family_birth07.
         *
         * @param string $family_birth07
         *
         * @return Customer
         */
        public function setFamilyBirth07($family_birth07)
        {
            $this->family_birth07 = $family_birth07;

            return $this;
        }

        /**
         * Get family_birth07.
         *
         * @return string
         */
        public function getFamilyBirth07()
        {
            return $this->family_birth07;
        }
        
        /**
         * Set family_name08.
         *
         * @param string $family_name08
         *
         * @return Customer
         */
        public function setFamilyName08($family_name08)
        {
            $this->family_name08 = $family_name08;

            return $this;
        }

        /**
         * Get family_name08.
         *
         * @return string
         */
        public function getFamilyName08()
        {
            return $this->family_name08;
        }
        
        /**
         * Set family_relation08.
         *
         * @param string $family_relation08
         *
         * @return Customer
         */
        public function setFamilyRelation08($family_relation08)
        {
            $this->family_relation08 = $family_relation08;

            return $this;
        }

        /**
         * Get family_relation08.
         *
         * @return string
         */
        public function getFamilyRelation08()
        {
            return $this->family_relation08;
        }
        
        /**
         * Set family_birth08.
         *
         * @param string $family_birth08
         *
         * @return Customer
         */
        public function setFamilyBirth08($family_birth08)
        {
            $this->family_birth08 = $family_birth08;

            return $this;
        }

        /**
         * Get family_birth08.
         *
         * @return string
         */
        public function getFamilyBirth08()
        {
            return $this->family_birth08;
        }
        
        /**
         * Set family_name09.
         *
         * @param string $family_name09
         *
         * @return Customer
         */
        public function setFamilyName09($family_name09)
        {
            $this->family_name09 = $family_name09;

            return $this;
        }

        /**
         * Get family_name09.
         *
         * @return string
         */
        public function getFamilyName09()
        {
            return $this->family_name09;
        }
        
        /**
         * Set family_relation09.
         *
         * @param string $family_relation09
         *
         * @return Customer
         */
        public function setFamilyRelation09($family_relation09)
        {
            $this->family_relation09 = $family_relation09;

            return $this;
        }

        /**
         * Get family_relation09.
         *
         * @return string
         */
        public function getFamilyRelation09()
        {
            return $this->family_relation09;
        }
        
        
        /**
         * Set family_birth09.
         *
         * @param string $family_birth09
         *
         * @return Customer
         */
        public function setFamilyBirth09($family_birth09)
        {
            $this->family_birth09 = $family_birth09;

            return $this;
        }

        /**
         * Get family_birth09.
         *
         * @return string
         */
        public function getFamilyBirth09()
        {
            return $this->family_birth09;
        }
        
        /**
         * Set family_name10.
         *
         * @param string $family_name10
         *
         * @return Customer
         */
        public function setFamilyName10($family_name10)
        {
            $this->family_name10 = $family_name10;

            return $this;
        }

        /**
         * Get family_name10.
         *
         * @return string
         */
        public function getFamilyName10()
        {
            return $this->family_name10;
        }
        
        /**
         * Set family_relation10.
         *
         * @param string $family_relation10
         *
         * @return Customer
         */
        public function setFamilyRelation10($family_relation10)
        {
            $this->family_relation10 = $family_relation10;

            return $this;
        }

        /**
         * Get family_relation10.
         *
         * @return string
         */
        public function getFamilyRelation10()
        {
            return $this->family_relation10;
        }
        
        
        /**
         * Set family_birth10.
         *
         * @param string $family_birth10
         *
         * @return Customer
         */
        public function setFamilyBirth10($family_birth10)
        {
            $this->family_birth10 = $family_birth10;

            return $this;
        }

        /**
         * Get family_birth10.
         *
         * @return string
         */
        public function getFamilyBirth10()
        {
            return $this->family_birth10;
        }
        
        /**
         * Set family_sex01.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex01
         *
         * @return Customer
         */
        public function setFamilySex01(\Eccube\Entity\Master\Sex $family_sex01 = null)
        {
            $this->family_sex01 = $family_sex01;

            return $this;
        }

        /**
         * Get family_sex01.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex01()
        {
            return $this->family_sex01;
        }
        
        /**
         * Set family_sex02.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex02
         *
         * @return Customer
         */
        public function setFamilySex02(\Eccube\Entity\Master\Sex $family_sex02 = null)
        {
            $this->family_sex02 = $family_sex02;

            return $this;
        }

        /**
         * Get family_sex02.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex02()
        {
            return $this->family_sex02;
        }
        
        /**
         * Set family_sex03.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex03
         *
         * @return Customer
         */
        public function setFamilySex03(\Eccube\Entity\Master\Sex $family_sex03 = null)
        {
            $this->family_sex03 = $family_sex03;

            return $this;
        }

        /**
         * Get family_sex03.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex03()
        {
            return $this->family_sex03;
        }
        
        /**
         * Set family_sex04.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex04
         *
         * @return Customer
         */
        public function setFamilySex04(\Eccube\Entity\Master\Sex $family_sex04 = null)
        {
            $this->family_sex04 = $family_sex04;

            return $this;
        }

        /**
         * Get family_sex04.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex04()
        {
            return $this->family_sex04;
        }
         /**
         * Set family_sex05.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex05
         *
         * @return Customer
         */
        public function setFamilySex05(\Eccube\Entity\Master\Sex $family_sex05 = null)
        {
            $this->family_sex05 = $family_sex05;

            return $this;
        }

        /**
         * Get family_sex05.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex05()
        {
            return $this->family_sex05;
        }
        
                     
        /**
         * Set family_sex06.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex06
         *
         * @return Customer
         */
        public function setFamilySex06(\Eccube\Entity\Master\Sex $family_sex06 = null)
        {
            $this->family_sex06 = $family_sex06;

            return $this;
        }

        /**
         * Get family_sex06.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex06()
        {
            return $this->family_sex06;
        }
        
         /**
         * Set family_sex07.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex07
         *
         * @return Customer
         */
        public function setFamilySex07(\Eccube\Entity\Master\Sex $family_sex07 = null)
        {
            $this->family_sex07 = $family_sex07;

            return $this;
        }

        /**
         * Get family_sex07.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex07()
        {
            return $this->family_sex07;
        }
        
                       
        /**
         * Set family_sex08.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex08
         *
         * @return Customer
         */
        public function setFamilySex08(\Eccube\Entity\Master\Sex $family_sex08 = null)
        {
            $this->family_sex08 = $family_sex08;

            return $this;
        }

        /**
         * Get family_sex08.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex08()
        {
            return $this->family_sex08;
        }
        
                       
        /**
         * Set family_sex09.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex09
         *
         * @return Customer
         */
        public function setFamilySex09(\Eccube\Entity\Master\Sex $family_sex09 = null)
        {
            $this->family_sex09 = $family_sex09;

            return $this;
        }

        /**
         * Get family_sex09.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex09()
        {
            return $this->family_sex09;
        }
        
                       
        /**
         * Set family_sex10.
         *
         * @param \Eccube\Entity\Master\Sex|null $family_sex10
         *
         * @return Customer
         */
        public function setFamilySex10(\Eccube\Entity\Master\Sex $family_sex10 = null)
        {
            $this->family_sex10 = $family_sex10;

            return $this;
        }

        /**
         * Get family_sex10.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getFamilySex10()
        {
            return $this->family_sex10;
        }
        
        
        /**
         * Set customer_code.
         *
         * @param string|null $customer_code
         *
         * @return Customer
         */
        public function setCustomerCode($customer_code = null)
        {
            $this->customer_code = $customer_code;

            return $this;
        }

        /**
         * Get customer_code.
         *
         * @return string|null
         */
        public function getCustomerCode()
        {
            return $this->customer_code;
        }
        
        
        /**
         * Set device_token1.
         *
         * @param string|null $device_token1
         *
         * @return Customer
         */
        public function setDeviceToken1($device_token1 = null)
        {
            $this->device_token1 = $device_token1;

            return $this;
        }

        /**
         * Get device_token1.
         *
         * @return string|null
         */
        public function getDeviceToken1()
        {
            return $this->device_token1;
        }
        
    }
