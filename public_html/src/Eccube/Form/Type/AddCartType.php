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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ClassCategory;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Repository\ProductClassRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Constraints\Length;

class AddCartType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $config;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Eccube\Entity\Product
     */
    protected $Product = null;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    protected $doctrine;

    public function __construct(ManagerRegistry $doctrine, EccubeConfig $config)
    {
        $this->doctrine = $doctrine;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $Product \Eccube\Entity\Product */
        $Product = $options['product'];
        $this->Product = $Product;
        $ProductClasses = $Product->getProductClasses();
        
        $ClassCategoryies = $Product->getClassCategories1();

        $builder
            ->add('product_id', HiddenType::class, [
                'data' => $Product->getId(),
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^\d+$/']),
                ], ])
            ->add(
                $builder
                    ->create('ProductClass', HiddenType::class, [
                        'data_class' => null,
                        'data' => $Product->hasProductClass() ? null : $ProductClasses->first(),
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ])
                    ->addModelTransformer(new EntityToIdTransformer($this->doctrine->getManager(), ProductClass::class)))
             
             ->add('additional_price', HiddenType::class, [
                'data' => 0,
                 ])
                 ;
                    

        if ($Product->getStockFind()) {
            $builder
                ->add('quantity', IntegerType::class, [
                    'data' => 1,
                    'attr' => [
                        'min' => 1,
                        'maxlength' => $this->config['eccube_int_len'],
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\GreaterThanOrEqual([
                            'value' => 1,
                        ]),
                        new Assert\Regex(['pattern' => '/^\d+$/']),
                    ],
                ]);
            if ($Product && $Product->getProductClasses()) {
                if (!is_null($Product->getClassName1())) {
                    /*
                    $select_text =  '--- '.$Product->getClassName1().'(必須)'.' ---';
                    $builder->add('classcategory_id1', ChoiceType::class, [
                        'label' => $Product->getClassName1(),
                        'choices' => [$select_text => '__unselected'] + $Product->getClassCategories1AsFlip(),
                        'mapped' => false,
                    ]);
                    */
                    
                    $builder->add('classcategory_id1', ChoiceType::class, [
                        'label' => $Product->getClassName1(),
                        'choices' => $Product->getClassCategories1AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('classcategory_id1')->getOptions();

		            // 以下3行を追加する
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('classcategory_id1', ChoiceType::class, $options);
		            
                }
                if (!is_null($Product->getClassName2())) {
                    $builder->add('classcategory_id2', ChoiceType::class, [
                        'label' => $Product->getClassName2(),
                        'choices' => ['common.select' => '__unselected'],
                        'mapped' => false,
                    ]);
                }
            }
            
            //オプションセット
            if ($Product && $Product->getProductClassCategories()) {
                $Product->set_options();//オプション内容を取得
                if (!is_null($Product->getOptionName1())) {
                    $select_text =  '--- '.$Product->getOptionName1().'(選択)'.' ---';
                    $builder->add('optioncategory_id1', ChoiceType::class, [
                        'label' => $Product->getOptionName1(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories1AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName2())) {
                    $select_text =  '--- '.$Product->getOptionName2().'(選択)'.' ---';
                    $builder->add('optioncategory_id2', ChoiceType::class, [
                        'label' => $Product->getOptionName2(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories2AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName3())) {
                    $select_text =  '--- '.$Product->getOptionName3().'(選択)'.' ---';
                    $builder->add('optioncategory_id3', ChoiceType::class, [
                        'label' => $Product->getOptionName3(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories3AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName4())) {
                    $select_text =  '--- '.$Product->getOptionName4().'(選択)'.' ---';
                    $builder->add('optioncategory_id4', ChoiceType::class, [
                        'label' => $Product->getOptionName4(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories4AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName5())) {
                    $select_text =  '--- '.$Product->getOptionName5().'(選択)'.' ---';
                    $builder->add('optioncategory_id5', ChoiceType::class, [
                        'label' => $Product->getOptionName5(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories5AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName6())) {
                    $select_text =  '--- '.$Product->getOptionName6().'(選択)'.' ---';
                    $builder->add('optioncategory_id6', ChoiceType::class, [
                        'label' => $Product->getOptionName6(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories6AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName7())) {
                    $select_text =  '--- '.$Product->getOptionName7().'(選択)'.' ---';
                    $builder->add('optioncategory_id7', ChoiceType::class, [
                        'label' => $Product->getOptionName7(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories7AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName8())) {
                    $select_text =  '--- '.$Product->getOptionName8().'(選択)'.' ---';
                    $builder->add('optioncategory_id8', ChoiceType::class, [
                        'label' => $Product->getOptionName8(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories8AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName9())) {
                    $select_text =  '--- '.$Product->getOptionName9().'(選択)'.' ---';
                    $builder->add('optioncategory_id9', ChoiceType::class, [
                        'label' => $Product->getOptionName9(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories9AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                if (!is_null($Product->getOptionName10())) {
                    $select_text =  '--- '.$Product->getOptionName10().'(選択)'.' ---';
                    $builder->add('optioncategory_id10', ChoiceType::class, [
                        'label' => $Product->getOptionName10(),
                        'choices' => [$select_text => '__unselected'] + $Product->getOptionCategories10AsFlip(),
                        'mapped' => false,
                    ]);
                }
                
                //名入れ(プレート)
                $builder->add('printname_plate', TextareaType::class, [
		            'required' => false,
		            'attr' => [
	                    'rows' => 2,
	                    'style' => 'min-height : 20px',
	                ],
		            'constraints' => [
		                new Length(['min' => 0, 'max' => 252]),
		            ],
		        ]);
		        
		        //名入れ(熨斗)
                $builder->add('printname_noshi', TextareaType::class, [
		            'required' => false,
		            'attr' => [
	                    'rows' => 2,
	                    'style' => 'min-height : 20px',
	                ],
		            'constraints' => [
		                new Length(['min' => 0, 'max' => 252]),
		            ],
		        ]);
            }
            

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {
                $data = $event->getData();
                $form = $event->getForm();
                if (isset($data['classcategory_id1']) && !is_null($Product->getClassName2())) {
                    if ($data['classcategory_id1']) {
                        $form->add('classcategory_id2', ChoiceType::class, [
                            'label' => $Product->getClassName2(),
                            'choices' => ['common.select' => '__unselected'] + $Product->getClassCategories2AsFlip($data['classcategory_id1']),
                            'mapped' => false,
                        ]);
                    }
                }
            });

            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                /** @var CartItem $CartItem */
                $CartItem = $event->getData();
                $ProductClass = $CartItem->getProductClass();
                // FIXME 価格の設定箇所、ここでいいのか
                if ($ProductClass) {
                    $CartItem
                        ->setProductClass($ProductClass)
                        ->setPrice($ProductClass->getPrice02IncTax());
                }
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product');
        $resolver->setDefaults([
            'data_class' => CartItem::class,
            'id_add_product_id' => true,
            'constraints' => [
                // FIXME new Assert\Callback(array($this, 'validate')),
            ],
        ]);
    }

    /*
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['id_add_product_id']) {
            foreach ($view->vars['form']->children as $child) {
                $child->vars['id'] .= $options['product']->getId();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'add_cart';
    }

    /**
     * validate
     *
     * @param type $data
     * @param ExecutionContext $context
     */
    public function validate($data, ExecutionContext $context)
    {
        $context->getValidator()->validate($data['product_class_id'], [
            new Assert\NotBlank(),
        ], '[product_class_id]');
        if ($this->Product->getClassName1()) {
            $context->validateValue($data['classcategory_id1'], [
                new Assert\NotBlank(),
                new Assert\NotEqualTo([
                    'value' => '__unselected',
                    'message' => 'form_error.not_selected',
                ]),
            ], '[classcategory_id1]');
        }
        //商品規格2初期状態(未選択)の場合の返却値は「NULL」で「__unselected」ではない
        if ($this->Product->getClassName2()) {
            $context->getValidator()->validate($data['classcategory_id2'], [
                new Assert\NotBlank(),
                new Assert\NotEqualTo([
                    'value' => '__unselected',
                    'message' => 'form_error.not_selected',
                ]),
            ], '[classcategory_id2]');
        }
    }
}
