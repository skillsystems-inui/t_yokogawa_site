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
use Eccube\Entity\Category;
use Eccube\Entity\ClassName;
use Eccube\Form\Type\Master\ProductStatusType;
use Eccube\Form\Type\Master\SalesTypeType;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProductType.
 */
class ProductType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;
    
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ProductType constructor.
     *
     * @param CategoryRepository $categoryRepository
     * @param ClassNameRepository $classNameRepository
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ClassNameRepository $classNameRepository,
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->classNameRepository = $classNameRepository;
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // 商品規格情報
            ->add('class', ProductClassType::class, [
                'mapped' => false,
            ])
            // 基本情報
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('product_image', FileType::class, [
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ])
            ->add('product_subimage', FileType::class, [
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ])
            ->add('description_detail', TextareaType::class, [
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            ->add('description_list', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            ->add('Category', ChoiceType::class, [
                'choice_label' => 'Name',
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $this->categoryRepository->getList(null, true),
                'choice_value' => function (Category $Category = null) {
                    return $Category ? $Category->getId() : null;
                },
            ])
            
            //商品オプション
            ->add('ClassName', ChoiceType::class, [
                'choice_label' => 'Name',
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $this->classNameRepository->getList(),
                'choice_value' => function (ClassName $ClassName = null) {
                    return $ClassName ? $ClassName->getId() : null;
                },
            ])
            /*
            //商品オプション ToDo20210721Aオプションの再現上手くいかない
            ->add('ClassName', ChoiceType::class, [
                'choice_label' => 'Name',
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $this->classNameRepository->getList(),
                'choice_value' => function (ClassName $ClassName = null) {
                    return $ClassName ? $ClassName->getId() : null;
                },
            ])
            */
            

            // 詳細な説明
            ->add('Tag', EntityType::class, [
                'class' => 'Eccube\Entity\Tag',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('t')
                    ->orderBy('t.sort_no', 'DESC');
                },
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
            ])
            ->add('search_word', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            // サブ情報
            ->add('free_area', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])

            // 右ブロック
            ->add('Status', ProductStatusType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])

            // タグ
            ->add('tags', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
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
            // サブ画像
            ->add('subimages', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('add_subimages', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('delete_subimages', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            //販売期間(開始)
            ->add('sales_period_from', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            //販売期間(終了)
            ->add('sales_period_to', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
            ])
            //配送種類
            ->add('sales_type', SalesTypeType::class, [
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            //内容量
            ->add('capacity', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
            //スマレジカテゴリID
            ->add('smart_category_id', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
            //賞味期限
            ->add('expiry_date', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
            //消費期限
            ->add('expiration_date', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
            //原材料
            ->add('material', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
            //お読みください
            ->add('please_read', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var FormInterface $form */
            $form = $event->getForm();
	        
	        //商品コードの重複チェック(規格選択なし)
	        // 商品クラスを持たない場合のみチェック
	        $_data = $form->getData();
	        
	        if($form->has('class')){
	        	/** @var Product $Product */
                $Product = $event->getData();
                //登録更新対象の商品ID
                $product_id = $Product->getId();
                
                //商品クラスフォーム
                $class_form = $form->get('class');
                $this->validateProductCode($class_form, $product_id);
            }
            
            //メイン画像と同様にサブ画像も同じ場所に格納する
            $saveImgDir = $this->eccubeConfig['eccube_save_image_dir'];
            $tempImgDir = $this->eccubeConfig['eccube_temp_image_dir'];
            $this->validateFilePath($form->get('delete_images'), [$saveImgDir, $tempImgDir]);
            $this->validateFilePath($form->get('add_images'), [$tempImgDir]);
        });
    }

    /**
     * 指定された複数ディレクトリのうち、いずれかのディレクトリ以下にファイルが存在するかを確認。
     *
     * @param $form FormInterface
     * @param $dirs array
     */
    private function validateFilePath($form, $dirs)
    {
        foreach ($form->getData() as $fileName) {
            $fileInDir = array_filter($dirs, function ($dir) use ($fileName) {
                $filePath = realpath($dir.'/'.$fileName);
                $topDirPath = realpath($dir);
                return strpos($filePath, $topDirPath) === 0 && $filePath !== $topDirPath;
            });
            if (!$fileInDir) {
                $form->getRoot()['product_image']->addError(new FormError(trans('admin.product.image__invalid_path')));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_product';
    }
    
    /**
     * 商品コードの重複チェック
     *
     * @param $class_form FormInterface
     * @param $product_id int
     */
    private function validateProductCode($class_form, $product_id)
    {
    	//入力された商品コードを取得
    	$_code = $class_form->get('code');
    	$input_product_code = $_code->getData();
    	
    	$qb = $this->entityManager->createQueryBuilder();
            $qb->select('count(p)')
                ->from('Eccube\\Entity\\ProductClass', 'p')
                ->leftJoin(
		            'Eccube\\Entity\\Product',
		            'r',
		            \Doctrine\ORM\Query\Expr\Join::WITH,
		            'p.Product = r.id'
		        )
                ->where('p.code = :product_code')
                ->setParameter('product_code', $input_product_code);

            // 更新の場合は自身のデータを重複チェックから除外する
            if (!is_null($product_id)) {
                $qb
                    ->andWhere('r.id <> :product_id')
                    ->setParameter('product_id', $product_id);
            }
            //重複している商品コード数
            $count = $qb->getQuery()->getSingleScalarResult();
            
            //重複している商品コードがあればエラーセット
            if ($count > 0) {
                $class_form['code']->addError(new FormError(trans('admin.content.product_code_exists')));
                //$this->addErrors('code', $class_form, $errors);
            }
    }
    
    protected function addValidations(FormBuilderInterface $builder)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $form->getData();
            
            //商品コード
            // 重複チェック

            log_info(
	            '__testLog0818X',
	            [
	                'data_code' => $data['code'],
	                '_data_' => $data,
	            ]
	        );
            /*
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('count(p)')
                ->from('Eccube\\Entity\\ProductClass', 'p')
                ->where('p.product_code = :product_code')
                ->setParameter('product_code', $data['code']);

            // 更新の場合は自身のデータを重複チェックから除外する
            if (!is_null($Page->getId())) {
                $qb
                    ->andWhere('p.id <> :page_id')
                    ->setParameter('page_id', $Page->getId());
            }
            $count = $qb->getQuery()->getSingleScalarResult();
            if ($count > 0) {
                $form['code']->addError(new FormError(trans('admin.content.page_url_exists')));
                //$this->addErrors('code', $form, $errors);
            }
            */
            
        });
    }
    
}
