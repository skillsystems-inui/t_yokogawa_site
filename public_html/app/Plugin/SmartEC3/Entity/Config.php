<?php

namespace Plugin\SmartEC3\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table(name="plg_smart_ec3_config")
 * @ORM\Entity(repositoryClass="Plugin\SmartEC3\Repository\ConfigRepository")
 */
class Config
{

    /// スマレジ会員連携
    // スマレジ商品情報連携（商品情報、在庫、スマレジ画像）
    // スマレジカテゴリ連携
    // スマレジ注文連携（スマレジ→ECCUBE）


    // スマレジ会員開始番号
    // スマレジ商品開始番号
    // スマレジカテゴリー開始番号

    public function __construct(){
        $this->user_update = false;
        $this->product_update = false;
        $this->category_update = false;
        $this->order_update = false;
    }

    /**
     * @return boolean
     */
    public function checkConfigComplete()
    {
        return $this->contract_id != null && $this->access_token != null && $this->api_url != null;
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_id", type="string", length=255, nullable=true)
     */
    private $contract_id;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    private $access_token;

    /**
     * @var string
     *
     * @ORM\Column(name="api_url", type="string", length=255, nullable=true)
     */
    private $api_url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="user_update", type="boolean", options={"default":"0"})
     */
    private $user_update;

    /**
     * @var int
     *
     * @ORM\Column(name="user_offset", type="integer", options={"unsigned":true}, nullable=true)
     */
    private $user_offset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="product_update", type="boolean", options={"default":"0"})
     */
    private $product_update;

    /**
     * @var int
     *
     * @ORM\Column(name="product_offset", type="integer", options={"unsigned":true}, nullable=true)
     */
    private $product_offset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="category_update", type="boolean", options={"default":"0"})
     */
    private $category_update;

    /**
     * @var int
     *
     * @ORM\Column(name="category_offset", type="integer", options={"unsigned":true}, nullable=true)
     */
    private $category_offset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="order_update", type="boolean", options={"default":"0"})
     */
    private $order_update;

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
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
        private $Creator;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this;
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContractId()
    {
        return $this->contract_id;
    }

    /**
     * @param string $contract_id
     *
     * @return $this;
     */
    public function setContractId($contract_id)
    {
        $this->contract_id = $contract_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @param string $access_token
     *
     * @return $this;
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiURL()
    {
        return $this->api_url;
    }

    /**
     * @param string $api_url
     *
     * @return $this;
     */
    public function setApiURL($api_url)
    {
        $this->api_url = $api_url;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUserUpdate()
    {
        return $this->user_update;
    }

    /**
     * @param string $user_update
     *
     * @return $this;
     */
    public function setUserUpdate($user_update)
    {
        $this->user_update = $user_update;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserOffset()
    {
        return $this->user_offset;
    }

    /**
     * @param string $user_offset
     *
     * @return $this;
     */
    public function setUserOffset($user_offset)
    {
        $this->user_offset = $user_offset;

        return $this;
    }

    /**
     * @return bool
     */
    public function getProductUpdate()
    {
        return $this->product_update;
    }

    /**
     * @param string $product_update
     *
     * @return $this;
     */
    public function setProductUpdate($product_update)
    {
        $this->product_update = $product_update;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductOffset()
    {
        return $this->product_offset;
    }

    /**
     * @param string $product_offset
     *
     * @return $this;
     */
    public function setProductOffset($product_offset)
    {
        $this->product_offset = $product_offset;

        return $this;
    }
    
    /**
     * @return bool
     */
    public function getCategoryUpdate()
    {
        return $this->category_update;
    }

    /**
     * @param string $category_update
     *
     * @return $this;
     */
    public function setCategoryUpdate($category_update)
    {
        $this->category_update = $category_update;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryOffset()
    {
        return $this->category_offset;
    }

    /**
     * @param string $category_offset
     *
     * @return $this;
     */
    public function setCategoryOffset($category_offset)
    {
        $this->category_offset = $category_offset;

        return $this;
    }

    /**
     * @return bool
     */
    public function getOrderUpdate()
    {
        return $this->order_update;
    }

    /**
     * @param string $order_update
     *
     * @return $this;
     */
    public function setOrderUpdate($order_update)
    {
        $this->order_update = $order_update;

        return $this;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Config
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
     * @return Config
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
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return Config
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
