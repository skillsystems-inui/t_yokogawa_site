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

namespace Plugin\SmartEC3\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * SmartRegiPrice
 *
 * @ORM\Table(name="plg_smart_ec3_price_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\SmartEC3\Repository\Master\SmartRegiPriceRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class SmartRegiPrice extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * EC/スマレジ
     *
     * @var integer
     */
    const NORMAL_PRICE = 1;

    /**
     * ECのみ
     *
     * @var integer
     */
    const OPEN_PRICE = 2;

}
