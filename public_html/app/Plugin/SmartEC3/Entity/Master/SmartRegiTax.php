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
 * SmartRegiTax
 *
 * @ORM\Table(name="plg_smart_ec3_tax_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\SmartEC3\Repository\Master\SmartRegiTaxRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class SmartRegiTax extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * 軽減(特定商品の軽減税率適用）
     *
     * @var integer
     */
    const TAX_TYPE1 = 10000001;
    
    /**
     * 選択[標準] (状態による適用[適用しない])
     *
     * @var integer
     */
    const TAX_TYPE2 = 10000002;
    
    /**
     * 選択[軽減] (状態による適用[適用する])
     *
     * @var integer
     */
    const TAX_TYPE3 = 10000003;
    
    /**
     * 選択[選択](状態による適用[都度選択する])
     *
     * @var integer
     */
    const TAX_TYPE4 = 10000004;
}
