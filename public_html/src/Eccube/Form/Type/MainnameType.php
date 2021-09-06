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

namespace Eccube\Form\Type;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MainnameType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * MainnameType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EccubeConfig $eccubeConfig
    ) {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['mainname_options']['required'] = $options['required'];

        // required の場合は NotBlank も追加する
        if ($options['required']) {

            $options['mainname_options']['constraints'] = array_merge([
                new Assert\NotBlank(),
            ], $options['mainname_options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }
        
        if (empty($options['mainname_name'])) {
            //$options['mainname_name'] = 'main'.$builder->getName();
            $options['mainname_name'] = $builder->getName();
        }
        
        //ラベル非表示
        $options['mainname_options']['label'] = false;
        //プレースホルダー
        $options['mainname_options']['attr']['placeholder'] = '家族代表をボタンより選択してください';
        
        $builder
            ->add($options['mainname_name'], TextType::class, array_merge_recursive($options['options'], $options['mainname_options']))
        ;

        $builder->setAttribute('mainname_name', $options['mainname_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['mainname_name'] = $builder->getAttribute('mainname_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => [],
            'mainname_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
            'label'=>false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        //return 'mainname';
        return 'name';
    }
}
