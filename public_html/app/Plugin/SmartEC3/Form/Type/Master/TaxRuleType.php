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

namespace Plugin\SmartEC3\Form\Type\Master;

use Eccube\Form\Type\MasterType;
use Eccube\Entity\TaxRule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxRuleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => TaxRule::class,
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            'choice_label' => function (TaxRule $TaxRule = null) {
                return $TaxRule ? $TaxRule->getTaxRate() : null;
            },
            'query_builder' => function ($er) {
                return $er->createQueryBuilder('t')
                    ->orderBy('t.tax_rate', 'ASC');
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MasterType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tax';
    }
}
