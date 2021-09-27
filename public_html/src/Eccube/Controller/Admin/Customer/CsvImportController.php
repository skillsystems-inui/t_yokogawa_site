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

namespace Eccube\Controller\Admin\Customer;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Common\Constant;
use Eccube\Controller\Admin\AbstractCsvImportController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Category;
use Eccube\Entity\Customer;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\ProductTag;
use Eccube\Form\Type\Admin\CsvImportType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Repository\Master\FamilymainRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TagRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\CsvImportService;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;


class CsvImportController extends AbstractCsvImportController
{
    /**
     * @var DeliveryDurationRepository
     */
    protected $deliveryDurationRepository;

    /**
     * @var SaleTypeRepository
     */
    protected $saleTypeRepository;
    
    /**
     * @var SexRepository
     */
    protected $sexRepository;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    
    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;
    
    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var CustomerStatusRepository
     */
    protected $customerStatusRepository;
    
    /**
     * @var FamilymainRepository
     */
    protected $familymainRepository;
    
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    private $errors = [];

    /**
     * CsvImportController constructor.
     *
     * @param DeliveryDurationRepository $deliveryDurationRepository
     * @param SaleTypeRepository $saleTypeRepository
     * @param SexRepository $sexRepository
     * @param TagRepository $tagRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param CustomerAddressRepository $customerAddressRepository
     * @param CustomerStatusRepository $customerStatusRepository
     * @param FamilymainRepository $familymainRepository
     * @param ClassCategoryRepository $classCategoryRepository
     * @param ProductStatusRepository $productStatusRepository
     * @param ProductRepository $productRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param EncoderFactoryInterface $encoderFactory
     * @param BaseInfoRepository $baseInfoRepository
     * @param ValidatorInterface $validator
     * @throws \Exception
     */
    public function __construct(
        DeliveryDurationRepository $deliveryDurationRepository,
        SaleTypeRepository $saleTypeRepository,
        SexRepository $sexRepository,
        TagRepository $tagRepository,
        CategoryRepository $categoryRepository,
        CustomerRepository $customerRepository,
        CustomerAddressRepository $customerAddressRepository,
        CustomerStatusRepository $customerStatusRepository,
        FamilymainRepository $familymainRepository,
        ClassCategoryRepository $classCategoryRepository,
        ProductStatusRepository $productStatusRepository,
        ProductRepository $productRepository,
        TaxRuleRepository $taxRuleRepository,
        EncoderFactoryInterface $encoderFactory,
        BaseInfoRepository $baseInfoRepository,
        ValidatorInterface $validator
    ) {
        $this->deliveryDurationRepository = $deliveryDurationRepository;
        $this->saleTypeRepository = $saleTypeRepository;
        $this->sexRepository = $sexRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->customerRepository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->customerStatusRepository = $customerStatusRepository;
        $this->familymainRepository = $familymainRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->productStatusRepository = $productStatusRepository;
        $this->productRepository = $productRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->encoderFactory = $encoderFactory;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->validator = $validator;
    }

    /**
     * 会員登録CSVアップロード
     *
     * @Route("/%eccube_admin_route%/customer/customer_csv_upload", name="admin_customer_csv_import")
     * @Template("@admin/Customer/csv_customer.twig")
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function csvCustomer(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();
        $headers = $this->getCustomerCsvHeader();
        
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formFile = $form['import_file']->getData();
                if (!empty($formFile)) {
                    log_info('会員CSV登録開始');
                    $data = $this->getImportData($formFile);
                    if ($data === false) {
                        $this->addErrors(trans('admin.common.csv_invalid_format'));

                        return $this->renderWithError($form, $headers, false);
                    }
                    $getId = function ($item) {
                        return $item['id'];
                    };
                    $requireHeader = array_keys(array_map($getId, array_filter($headers, function ($value) {
                        return $value['required'];
                    })));

                    $columnHeaders = $data->getColumnHeaders();

                    if (count(array_diff($requireHeader, $columnHeaders)) > 0) {
                        $this->addErrors(trans('admin.common.csv_invalid_format'));

                        return $this->renderWithError($form, $headers, false);
                    }

                    $size = count($data);

                    if ($size < 1) {
                        $this->addErrors(trans('admin.common.csv_invalid_no_data'));

                        return $this->renderWithError($form, $headers, false);
                    }

                    $headerSize = count($columnHeaders);
                    $headerByKey = array_flip(array_map($getId, $headers));

                    $this->entityManager->getConfiguration()->setSQLLogger(null);
                    $this->entityManager->getConnection()->beginTransaction();
                    
                    
                    
                    // CSVファイルの登録処理
                    foreach ($data as $row) {
                        $line = $data->key() + 1;
                        if ($headerSize != count($row)) {
                            $message = trans('admin.common.csv_invalid_format_line', ['%line%' => $line]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        }

                        if (!isset($row[$headerByKey['id']]) || StringUtil::isBlank($row[$headerByKey['id']])) {
                            $Customer = new Customer();
                            $this->entityManager->persist($Customer);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['id']])) {
                                $Customer = $this->customerRepository->find($row[$headerByKey['id']]);
                                if (!$Customer) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['id']]);
                                    $this->addErrors($message);

                                    return $this->renderWithError($form, $headers);
                                }
                                
                                // 編集用にデフォルトパスワードをセット
                                $previous_password = $Customer->getPassword();
				            	$Customer->setPassword($this->eccubeConfig['eccube_default_password']);
				            
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['id']]);
                                $this->addErrors($message);

                                return $this->renderWithError($form, $headers);
                            }
                        }

                        if (StringUtil::isBlank($row[$headerByKey['status']])) {
                            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                            $this->addErrors($message);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['status']])) {
                                $CustomerStatus = $this->customerStatusRepository->find($row[$headerByKey['status']]);
                                if (!$CustomerStatus) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                                    $this->addErrors($message);
                                } else {
                                    $Customer->setStatus($CustomerStatus);
                                }
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                                $this->addErrors($message);
                            }
                        }

                        //名前01
                        if (StringUtil::isBlank($row[$headerByKey['name01']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['name01']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setName01(StringUtil::trimAll($row[$headerByKey['name01']]));
                        }
                        
                        //名前02
                        if (StringUtil::isBlank($row[$headerByKey['name02']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['name02']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setName02(StringUtil::trimAll($row[$headerByKey['name02']]));
                        }
                        
                        //名前01(カナ)
                        if (StringUtil::isBlank($row[$headerByKey['name01_kana']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['name01_kana']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setKana01(StringUtil::trimAll($row[$headerByKey['name01_kana']]));
                        }
                        
                        //名前02(カナ)
                        if (StringUtil::isBlank($row[$headerByKey['name02_kana']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['name02_kana']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setKana02(StringUtil::trimAll($row[$headerByKey['name02_kana']]));
                        }
                        
                        //郵便番号
                        if (StringUtil::isBlank($row[$headerByKey['postal_code']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['postal_code']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setPostalcode(StringUtil::trimAll($row[$headerByKey['postal_code']]));
                        }
                        
                        //住所1
                        if (StringUtil::isBlank($row[$headerByKey['addr01']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['addr01']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setAddr01(StringUtil::trimAll($row[$headerByKey['addr01']]));
                        }
                        //住所2
                        if (StringUtil::isBlank($row[$headerByKey['addr02']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['addr02']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setAddr02(StringUtil::trimAll($row[$headerByKey['addr02']]));
                        }
                        
                        //電話番号
                        if (StringUtil::isBlank($row[$headerByKey['phone_number']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['phone_number']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setPhoneNumber(StringUtil::trimAll($row[$headerByKey['phone_number']]));
                        }
                        
                        //性別
                        if (StringUtil::isBlank($row[$headerByKey['sex']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sex']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
							$Sex = $this->sexRepository->find($row[$headerByKey['sex']]);
			                if (!$Sex) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sex']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setSex($Sex);
			                }
                        }
                        
                        //誕生日
                        /*
                        if (StringUtil::isBlank($row[$headerByKey['birth']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['birth']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setBirth(strtotime(StringUtil::trimAll($row[$headerByKey['birth']])));
                        }
                        */
                        
                        //ポイント
                        if (StringUtil::isBlank($row[$headerByKey['point']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['point']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setPoint(intval(StringUtil::trimAll($row[$headerByKey['point']])));
                        }
                        
                        //家族代表フラグ
                        $Familymain = $this->familymainRepository->find($row[$headerByKey['is_family_main']]);
		                if (!$Familymain) {
		                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['is_family_main']]);
		                    $this->addErrors($message);
		                } else {
		                    $Customer->setFamilymain($Familymain);
		                }
                        
                        /*
                        //家族代表会員ID
                        if (StringUtil::isBlank($row[$headerByKey['family_main_customer_id']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_main_customer_id']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setMaincustomer(StringUtil::trimAll($row[$headerByKey['family_main_customer_id']]));
                        }
                        */
                        
                        
                        //メール
                        if (StringUtil::isBlank($row[$headerByKey['email']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['email']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setEmail(StringUtil::trimAll($row[$headerByKey['email']]));
                        }
                        
                        //パスワード
                        $encoder = $this->encoderFactory->getEncoder($Customer);

			            if ($Customer->getPassword() === $this->eccubeConfig['eccube_default_password']) {
			                $Customer->setPassword($previous_password);
			                
			            } else {
			                
			                //新規登録時はパスワードないのでこちら
			                
			                if ($Customer->getSalt() === null) {
			                    $Customer->setSalt($encoder->createSalt());
			                    $Customer->setSecretKey($this->customerRepository->getUniqueSecretKey());
			                    //saltもないのでこちら
			                }
			                //$raw_pass='test1234';
			                $raw_pass = ''.StringUtil::trimAll($row[$headerByKey['password']]).'';
			                
			                $Customer->setPassword(  $encoder->encodePassword($raw_pass , $Customer->getSalt())  );

			            }

                        $this->entityManager->flush();
                    }

                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();

                    log_info('会員CSV登録完了');
                    $message = 'admin.common.csv_upload_complete';
                    $this->session->getFlashBag()->add('eccube.admin.success', $message);

                    $cacheUtil->clearDoctrineCache();
                }
            }
        }
        

        return $this->renderWithError($form, $headers);
    }


    /**
     * アップロード用CSV雛形ファイルダウンロード
     *
     * @Route("/%eccube_admin_route%/customer/csv_template/{type}", requirements={"type" = "\w+"}, name="admin_customer_csv_template")
     *
     * @param $type
     *
     * @return StreamedResponse
     */
    public function csvTemplate(Request $request, $type)
    {
        if ($type == 'customer') {
            $headers = $this->getCustomerCsvHeader();
            $filename = 'customer.csv';
        } else {
            throw new NotFoundHttpException();
        }

        return $this->sendTemplateResponse($request, array_keys($headers), $filename);
    }

    /**
     * 登録、更新時のエラー画面表示
     *
     * @param FormInterface $form
     * @param array $headers
     * @param bool $rollback
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function renderWithError($form, $headers, $rollback = true)
    {
        if ($this->hasErrors()) {
            if ($rollback) {
                $this->entityManager->getConnection()->rollback();
            }
        }

        $this->removeUploadedFile();

        return [
            'form' => $form->createView(),
            'headers' => $headers,
            'errors' => $this->errors,
        ];
    }

    /**
     * 登録、更新時のエラー画面表示
     */
    protected function addErrors($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return array
     */
    protected function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return boolean
     */
    protected function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * 商品登録CSVヘッダー定義
     *
     * @return array
     */
    protected function getCustomerCsvHeader()
    {
        return [
            //会員ID
            trans('admin.customer.customer_csv.customer_id_col') => [
                'id' => 'id',
                'description' => 'admin.customer.customer_csv.customer_id_description',
                'required' => false,
            ],
            //会員ステータス
            trans('admin.customer.customer_csv.display_status_col') => [
                'id' => 'status',
                'description' => 'admin.customer.customer_csv.display_status_description',
                'required' => true,
            ],
            //会員名1
            trans('admin.customer.customer_csv.customer_name01_col') => [
                'id' => 'name01',
                'description' => 'admin.customer.customer_csv.customer_name01_description',
                'required' => true,
            ],
            //会員名2
            trans('admin.customer.customer_csv.customer_name02_col') => [
                'id' => 'name02',
                'description' => 'admin.customer.customer_csv.customer_name02_description',
                'required' => true,
            ],
            //会員名1(カナ)
            trans('admin.customer.customer_csv.customer_name01_kana_col') => [
                'id' => 'name01_kana',
                'description' => 'admin.customer.customer_csv.customer_name01_kana_description',
                'required' => true,
            ],
            //会員名2(カナ)
            trans('admin.customer.customer_csv.customer_name02_kana_col') => [
                'id' => 'name02_kana',
                'description' => 'admin.customer.customer_csv.customer_name02_kana_description',
                'required' => true,
            ],
            
            //メールアドレス
            trans('admin.customer.customer_csv.email_col') => [
                'id' => 'email',
                'description' => 'admin.customer.customer_csv.email_description',
                'required' => false,
            ],
            //パスワード
            trans('admin.customer.customer_csv.password_col') => [
                'id' => 'password',
                'description' => 'admin.customer.customer_csv.password_description',
                'required' => true,
            ],
            
            //住所(郵便番号)
            trans('admin.customer.customer_csv.customer_postal_code_col') => [
                'id' => 'postal_code',
                'description' => 'admin.customer.customer_csv.customer_postal_code_description',
                'required' => true,
            ],
            //住所(住所1)
            trans('admin.customer.customer_csv.customer_addr01_col') => [
                'id' => 'addr01',
                'description' => 'admin.customer.customer_csv.customer_addr01_description',
                'required' => true,
            ],
            //住所(住所2)
            trans('admin.customer.customer_csv.customer_addr02_col') => [
                'id' => 'addr02',
                'description' => 'admin.customer.customer_csv.customer_addr02_description',
                'required' => true,
            ],
            //電話番号
            trans('admin.customer.customer_csv.customer_phone_number_col') => [
                'id' => 'phone_number',
                'description' => 'admin.customer.customer_csv.customer_phone_number_description',
                'required' => true,
            ],
            //性別
            trans('admin.customer.customer_csv.customer_sex_col') => [
                'id' => 'sex',
                'description' => 'admin.customer.customer_csv.customer_sex_description',
                'required' => true,
            ],
            //誕生日
            trans('admin.customer.customer_csv.customer_birth_col') => [
                'id' => 'birth',
                'description' => 'admin.customer.customer_csv.customer_birth_description',
                'required' => true,
            ],
            //ポイント
            trans('admin.customer.customer_csv.customer_point_col') => [
                'id' => 'point',
                'description' => 'admin.customer.customer_csv.customer_point_description',
                'required' => true,
            ],
            /*
            //家族代表フラグ
            trans('admin.customer.customer_csv.customer_is_family_main_col') => [
                'id' => 'is_family_main',
                'description' => 'admin.customer.customer_csv.customer_is_family_main_description',
                'required' => true,
            ],
            //家族代表会員ID
            trans('admin.customer.customer_csv.customer_family_main_customer_id_col') => [
                'id' => 'family_main_customer_id',
                'description' => 'admin.customer.customer_csv.customer_family_main_customer_id_description',
                'required' => false,
            ],
            */
        ];
    }
}
