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
 * InfoType
 *
 * @ORM\Table(name="mtb_info_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\InfoTypeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class InfoType extends \Eccube\Entity\Master\AbstractMasterEntity
{
    
    /**
     * NEWS.
     */
    const NEWS = 1;

    /**
     * イベント
     */
    const EVENT = 2;

    /**
     * お知らせ
     */
    const INFOMATION = 3;
}
