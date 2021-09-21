<?php

namespace Plugin\SmartEC3\Form\Type\Admin\SmartRegi;

use Eccube\Common\EccubeConfig;

use Plugin\SmartEC3\Entity\SmartRegi;
use Plugin\SmartEC3\Form\Type\Master\SmartRegiStoreType;
use Plugin\SmartEC3\Form\Type\Master\SmartRegiPriceType;
use Plugin\SmartEC3\Form\Type\Master\SmartRegiTaxType;
use Plugin\SmartEC3\Form\Type\Master\TaxRuleType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SmartRegiType extends AbstractType
{

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(
        EccubeConfig $eccubeConfig
    ){
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('smart_image', FileType::class, [
                // This one directly affects the number of loops in the controller
                'multiple' => true, 
                'required' => false,
                'mapped' => false,
            ])
            ->add('store_type', SmartRegiStoreType::class, [
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('price_type', SmartRegiPriceType::class, [
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('tax', TaxRuleType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('tax_type', SmartRegiTaxType::class, [
            ])
            ->add('size', TextType::class, [
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
            ->add('color', TextType::class, [
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
             // 画像
             ->add('images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('add_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('delete_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('return_link', HiddenType::class, [
                'mapped' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var FormInterface $form */
            $form = $event->getForm();
            $saveImgDir = $this->eccubeConfig['eccube_save_image_dir'];
            $tempImgDir = $this->eccubeConfig['eccube_temp_image_dir'];
            $this->validateFilePath($form->get('delete_images'), [$saveImgDir, $tempImgDir]);
            $this->validateFilePath($form->get('add_images'), [$tempImgDir]);
        });
    }

    private function validateFilePath($form, $dirs)
    {
        foreach ($form->getData() as $fileName) {
            $fileInDir = array_filter($dirs, function ($dir) use ($fileName) {
                $filePath = realpath($dir.'/'.$fileName);
                $topDirPath = realpath($dir);
                return strpos($filePath, $topDirPath) === 0 && $filePath !== $topDirPath;
            });
            if (!$fileInDir) {
                $form->getRoot()['smart_image']->addError(new FormError(trans('admin.product.image__invalid_path')));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SmartRegi::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_smartregi';
    }
}
