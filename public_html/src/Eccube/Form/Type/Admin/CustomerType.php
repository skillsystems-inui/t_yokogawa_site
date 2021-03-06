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

namespace Eccube\Form\Type\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Form\Type\Master\FamilymainType;
use Eccube\Form\Type\Master\JobType;
use Eccube\Form\Type\Master\SexType;
use Eccube\Form\Type\MainnameType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\RepeatedPasswordType;
use Eccube\Form\Type\PhoneNumberType;
use Eccube\Form\Type\PostalType;
use Eccube\Form\Validator\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * CustomerType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig
    )
    {
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, [
                'required' => true,
            ])
            ->add('kana', KanaType::class, [
                'required' => true,
            ])
            ->add('customer_code', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('company_name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('postal_code', PostalType::class, [
                'required' => true,
            ])
            ->add('address', AddressType::class, [
                'required' => true,
            ])
            ->add('phone_number', PhoneNumberType::class, [
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(['strict' => $this->eccubeConfig['eccube_rfc_email_check']]),
                ],
                'attr' => [
                    'placeholder' => 'common.mail_address_sample',
                ],
            ])
            ->add('sex', SexType::class, [
                'required' => false,
            ])
            ->add('job', JobType::class, [
                'required' => false,
            ])
            ->add('birth', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('password', RepeatedPasswordType::class, [
                // 'type' => 'password',
                'first_options' => [
                    'label' => 'member.label.pass',
                ],
                'second_options' => [
                    'label' => 'member.label.verify_pass',
                ],
            ])
            ->add('status', CustomerStatusType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add(
                'point',
                NumberType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => "/^\d+$/u",
                            'message' => 'form_error.numeric_only',
                        ]),
                    ],
                ]
            )
            ->add('familymain', FamilymainType::class, [
                'required' => true,
            ])
            ->add('mainname', MainnameType::class, [
                'required' => false,
            ])
            
            //????????????
            //  1???10???
            ->add('family_birth01', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name01', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation01', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex01', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth02', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name02', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation02', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex02', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth03', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name03', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation03', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex03', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth04', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name04', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation04', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex04', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth05', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name05', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation05', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex05', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth06', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name06', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation06', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex06', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth07', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name07', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation07', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex07', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth08', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name08', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation08', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex08', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth09', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name09', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation09', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex09', SexType::class, [
                'required' => false,
            ])
            ->add('family_birth10', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('family_name10', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_relation10', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('family_sex10', SexType::class, [
                'required' => false,
            ])
            
            ->add('note', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ]);

        $builder
            ->add($builder->create('maincustomer', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->entityManager,
                    '\Eccube\Entity\Customer'
                )));
                
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Customer $Customer */
            $Customer = $event->getData();
            if ($Customer->getPassword() != '' && $Customer->getPassword() == $Customer->getEmail()) {
                $form['password']['first']->addError(new FormError(trans('common.password_eq_email')));
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $Customer = $event->getData();

            // ????????????????????????????????????????????????0?????????
            if (is_null($Customer->getPoint())) {
                $Customer->setPoint(0);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Customer',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_customer';
    }
    
}
