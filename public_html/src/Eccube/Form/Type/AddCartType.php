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
                        'style' => 'width: 70px;',
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
                    $select_text =  '--- '.$Product->getClassName1().'(??????)'.' ---';
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

		            // ??????3??????????????????
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
            
            //????????????????????????
            /*
            	???????????????
            	[?????????]?????????[??????????????????]
            	????????????????????????????????????(???????????????)????????????????????????
            	????????????????????????????????????(???????????????)??????????????????????????????
            	???????????????
            	[?????????]?????????[??????????????????]
            	?????????????????????????????????????????????????????????????????????(?????????)
            	???????????????????????????
            */
            if ($Product && $Product->getProductClassCategories()) {
                $Product->set_options();//??????????????????????????????
                if (!is_null($Product->getOptionName1())) {
                    $builder->add('optioncategory_id1', ChoiceType::class, [
                        'label' => $Product->getOptionName1(),
                        'choices' => $Product->getOptionCategories1AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id1')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id1', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName2())) {
                    $builder->add('optioncategory_id2', ChoiceType::class, [
                        'label' => $Product->getOptionName2(),
                        'choices' => $Product->getOptionCategories2AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id2')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id2', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName3())) {
                    $builder->add('optioncategory_id3', ChoiceType::class, [
                        'label' => $Product->getOptionName3(),
                        'choices' => $Product->getOptionCategories3AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id3')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id3', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName4())) {
                    $builder->add('optioncategory_id4', ChoiceType::class, [
                        'label' => $Product->getOptionName4(),
                        'choices' => $Product->getOptionCategories4AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id4')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id4', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName5())) {
                    $builder->add('optioncategory_id5', ChoiceType::class, [
                        'label' => $Product->getOptionName5(),
                        'choices' => $Product->getOptionCategories5AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id5')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id5', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName6())) {
                    $builder->add('optioncategory_id6', ChoiceType::class, [
                        'label' => $Product->getOptionName6(),
                        'choices' => $Product->getOptionCategories6AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id6')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id6', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName7())) {
                    $builder->add('optioncategory_id7', ChoiceType::class, [
                        'label' => $Product->getOptionName7(),
                        'choices' => $Product->getOptionCategories7AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id7')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id7', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName8())) {
                    $builder->add('optioncategory_id8', ChoiceType::class, [
                        'label' => $Product->getOptionName8(),
                        'choices' => $Product->getOptionCategories8AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id8')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id8', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName9())) {
                    $builder->add('optioncategory_id9', ChoiceType::class, [
                        'label' => $Product->getOptionName9(),
                        'choices' => $Product->getOptionCategories9AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id9')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id9', ChoiceType::class, $options);
                }
                
                if (!is_null($Product->getOptionName10())) {
                    $builder->add('optioncategory_id10', ChoiceType::class, [
                        'label' => $Product->getOptionName10(),
                        'choices' => $Product->getOptionCategories10AsFlip(),
                        'mapped' => false,
                    ]);
                    $options = $builder->get('optioncategory_id10')->getOptions();

		            //????????????????????????
		            $options['compound'] = true;
		            $options['expanded'] = true;
		            unset($options['choices']['common.select']);

		            $builder->add('optioncategory_id10', ChoiceType::class, $options);
                }
            }
            
            //???????????????????????????
            $builder->add('option_detail', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 1024]),
                ],
            ]);
            
            
            //-----?????????????????????-----
            $builder->add('option_candle_dai_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 1024]),
                ],
            ]);
            $builder->add('option_candle_syo_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 1024]),
                ],
            ]);
            
            $builder->add('option_candle_no1_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no2_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no3_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no4_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no5_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no6_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no7_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no8_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no9_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_candle_no0_num', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            
            $builder->add('option_printname_plate1', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_printname_plate2', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_printname_plate3', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_printname_plate4', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_printname_plate5', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            
            $builder->add('option_deco_ichigo_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_deco_fruit_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_deco_namachoco_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_deco_echoco_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_pori_cyu_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_pori_dai_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_pori_tokudai_chk', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_housou_sentaku', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_noshi_kakekata', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_kakehousou_syurui', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_uwagaki_sentaku', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_printname_nosina', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            $builder->add('option_plate_sentaku', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'style' => 'min-height : 20px',
                ],
                'constraints' => [
                    new Length(['min' => 0, 'max' => 100]),
                ],
            ]);
            
            //-----?????????????????????-----
            
            
            
            
            
            
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
                // FIXME ?????????????????????????????????????????????
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
            'allow_extra_fields' => true,
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
        //????????????2????????????(?????????)???????????????????????????NULL?????????__unselected???????????????
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
