<?php

namespace Plugin\SmartEC3\Form\Type\Admin;

use Plugin\SmartEC3\Entity\Config;
use Eccube\Form\Type\ToggleSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use \Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contract_id', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'plg.smartec3.config.contract_id',
                    'maxlength' => 100
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('access_token', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'plg.smartec3.config.access_token',
                    'maxlength' => 100
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('api_url', UrlType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'plg.smartec3.config.api_url',
                    'maxlength' => 100
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('user_update', ToggleSwitchType::class)
            ->add('user_offset', NumberType::class, [
                'required' => false,
                'empty_data' => 0,
                'attr' => array('style' => 'display:none;'),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ],
            ])
            ->add('product_update', ToggleSwitchType::class)
            ->add('product_offset', NumberType::class, [
                'required' => false,
                'empty_data' => 0,
                'attr' => array('style' => 'display:none;'),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ],
            ])
            ->add('category_update', ToggleSwitchType::class)
            ->add('category_offset', NumberType::class, [
                'required' => false,
                'empty_data' => 0,
                'attr' => array('style' => 'display:none;'),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ],
            ])
            ->add('order_update', ToggleSwitchType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}
