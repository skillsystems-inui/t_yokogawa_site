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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * SalesType
 *
 * @ORM\Table(name="mtb_sales_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\SalesTypeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class SalesType extends \Eccube\Entity\Master\AbstractMasterEntity
{
    
    /**
     * 店頭予約.
     */
    const ATSHOP = 1;

    /**
     * 通信販売
     */
    const NETSHOP = 2;

    /**
     * 店舗と通信販売の両方
     */
    const BOTH = 3;
}
