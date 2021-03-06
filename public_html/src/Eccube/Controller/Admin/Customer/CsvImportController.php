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
use Eccube\Repository\Master\PrefRepository;
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
     * @var PrefRepository
     */
    protected $prefRepository;

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
     * @param PrefRepository $prefRepository
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
        PrefRepository $prefRepository,
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
        $this->prefRepository = $prefRepository;
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
     * ????????????CSV??????????????????
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
                    log_info('??????CSV????????????');
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
                    
                    
                    
                    // CSV???????????????????????????
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
                                
                                // ??????????????????????????????????????????????????????
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

                        //??????????????????
                        if (StringUtil::isBlank($row[$headerByKey['customer_code']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['customer_code']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                        	//[?????????]
                        	if (!isset($row[$headerByKey['id']]) || StringUtil::isBlank($row[$headerByKey['id']])) {
	                        	//??????????????????
	                        	$SameCustomer = $this->customerRepository->getRegularCustomerByCustomerCode(StringUtil::trimAll($row[$headerByKey['customer_code']]));
	                            if ($SameCustomer != null) {
	                                $message = trans('admin.common.csv_invalid_already_exist_target', [
			                            '%line%' => $line,
			                            '%name%' => $headerByKey['customer_code'],
			                            '%target_name%' => $row[$headerByKey['customer_code']],
			                        ]);
	                                
	                                $this->addErrors($message);
	                            }else{
	                            	$Customer->setCustomerCode(StringUtil::trimAll($row[$headerByKey['customer_code']]));
	                            }
                            }else{
                            	//[?????????]
                            	//?????????????????????
                            	$CurrentCustomerCode = $Customer->getCustomerCode();
                            	//?????????????????????
                            	$ImportCustomerCode = StringUtil::trimAll($row[$headerByKey['customer_code']]);
                            	//????????????????????????????????????????????????????????????
                            	if($CurrentCustomerCode != $ImportCustomerCode){
                            		//??????????????????????????????
                            		$message = trans('admin.common.csv_invalid_cannot_change', ['%line%' => $line, '%name%' => $headerByKey['customer_code']]);
			                    	$this->addErrors($message);
                            	}else{
                            		//??????????????????????????????????????????????????????????????????
                            		$Customer->setCustomerCode($CurrentCustomerCode);
                            	}
                            }
                        }
                        
                        //??????01
                        if (StringUtil::isBlank($row[$headerByKey['name01']])) {
                            //??????????????????????????????
                            $noname = '?????????';
                            $Customer->setName01($noname);
                        } else {
                            $Customer->setName01(StringUtil::trimAll($row[$headerByKey['name01']]));
                        }
                        
                        //??????02
                        if (StringUtil::isBlank($row[$headerByKey['name02']])) {
                            //???????????????????????????
                            $noname = '??????';
                            $Customer->setName02($noname);
                        } else {
                            $Customer->setName02(StringUtil::trimAll($row[$headerByKey['name02']]));
                        }
                        
                        //??????01(??????)
                        if (StringUtil::isBlank($row[$headerByKey['name01_kana']])) {
                            //????????????????????????????????????
                            $noname = '???????????????';
                            $Customer->setKana01($noname);
                        } else {
                            $Customer->setKana01(StringUtil::trimAll($row[$headerByKey['name01_kana']]));
                        }
                        
                        //??????02(??????)
                        if (StringUtil::isBlank($row[$headerByKey['name02_kana']])) {
                            //?????????????????????????????????
                            $noname = '????????????';
                            $Customer->setKana02($noname);
                        } else {
                            $Customer->setKana02(StringUtil::trimAll($row[$headerByKey['name02_kana']]));
                        }
                        
                        //????????????
                        if (StringUtil::isBlank($row[$headerByKey['postal_code']])) {
                            //??????????????????0000000???
                            $noname = '0000000';
                            $Customer->setPostalcode($noname);
                            
                            //????????????????????????????????????(?????????)
                            $zip_code = 27;
                            $Pref = $this->prefRepository->find($zip_code);
			                if (!$Pref) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['postal_code']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setPref($Pref);
			                }
			                
                        } else {
                            $Customer->setPostalcode(StringUtil::trimAll($row[$headerByKey['postal_code']]));
                            
                            //????????????????????????????????????
                            $zip_code = $this->prefecture_from_zip($row[$headerByKey['postal_code']]) != null ? $this->prefecture_from_zip($row[$headerByKey['postal_code']]) : 27;
                            
                            $Pref = $this->prefRepository->find($zip_code);
			                if (!$Pref) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['postal_code']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setPref($Pref);
			                }
                        }
                        
                        //??????1
                        if (StringUtil::isBlank($row[$headerByKey['addr01']])) {
                            //??????????????????????????????
                            $noname = '?????????';
                            $Customer->setAddr01($noname);
                        } else {
                            $Customer->setAddr01(StringUtil::trimAll($row[$headerByKey['addr01']]));
                        }
                        //??????2
                        if (StringUtil::isBlank($row[$headerByKey['addr02']])) {
                            //??????????????????????????????
                            $noname = '?????????';
                            $Customer->setAddr02($noname);
                        } else {
                            $Customer->setAddr02(StringUtil::trimAll($row[$headerByKey['addr02']]));
                        }
                        
                        //????????????
                        if (StringUtil::isBlank($row[$headerByKey['phone_number']])) {
                            //??????????????????0600000000???
                            $noname = '0600000000';
                            $Customer->setPhoneNumber($noname);
                        } else {
                            $Customer->setPhoneNumber(StringUtil::trimAll($row[$headerByKey['phone_number']]));
                        }
                        
                        //??????
                        if (StringUtil::isBlank($row[$headerByKey['sex']])) {
                            //???????????????null
                            $noname = null;
                            $Customer->setSex($noname);
                        } else {
							$Sex = $this->sexRepository->find($row[$headerByKey['sex']]);
			                if (!$Sex) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sex']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setSex($Sex);
			                }
                        }
                        
                        //?????????
                        if (StringUtil::isNotBlank($row[$headerByKey['birth']])) {
	                        $BirthDay = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['birth']]);
			                if ($BirthDay == true) {
			                    $BirthDay->setTime(0, 0, 0);
	                        	$Customer->setBirth($BirthDay);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['birth']]);
	                            $this->addErrors($message);
			                }
		                }else{
		                	$Customer->setBirth(null);
		                }
                        
                        //????????????
                        if (StringUtil::isBlank($row[$headerByKey['point']])) {
                            //???????????????0????????????????????????
                            $Customer->setPoint(0);
                        } else {
                            $Customer->setPoint(intval(StringUtil::trimAll($row[$headerByKey['point']])));
                        }
                        
                        /*
                        //?????????????????????
                        $Familymain = $this->familymainRepository->find($row[$headerByKey['is_family_main']]);
		                if (!$Familymain) {
		                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['is_family_main']]);
		                    $this->addErrors($message);
		                } else {
		                    $Customer->setFamilymain($Familymain);
		                }
                        
                        
                        //??????????????????ID
                        if (StringUtil::isBlank($row[$headerByKey['family_main_customer_id']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_main_customer_id']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Customer->setMaincustomer(StringUtil::trimAll($row[$headerByKey['family_main_customer_id']]));
                        }
                        */
                        
                        //-------------------- [????????????] --------------------
                        //????????????01?????????
                        $Customer->setFamilyName01(StringUtil::trimAll($row[$headerByKey['family_name01']]));
                        //????????????01?????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_sex01_id']])) {
	                    	$FamilySex01 = $this->sexRepository->find($row[$headerByKey['family_sex01_id']]);
			                if (!$FamilySex01) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex01_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex01($FamilySex01);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex01($noname);
                        }
                        
                        //????????????01?????????
                        $Customer->setFamilyRelation01(StringUtil::trimAll($row[$headerByKey['family_relation01']]));
                        //????????????01????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth01']])) {
	                        $familyBirthDay01 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth01']]);
			                if ($familyBirthDay01 == true) {
			                    $familyBirthDay01->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth01($familyBirthDay01);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth01']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth01($noname);
                        }
		                
						//????????????02?????????
                        $Customer->setFamilyName02(StringUtil::trimAll($row[$headerByKey['family_name02']]));
                        //????????????02?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex02_id']])) {
	                    	$FamilySex02 = $this->sexRepository->find($row[$headerByKey['family_sex02_id']]);
			                if (!$FamilySex02) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex02_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex02($FamilySex02);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex02($noname);
                        }
                        
                        //????????????02?????????
                        $Customer->setFamilyRelation02(StringUtil::trimAll($row[$headerByKey['family_relation02']]));
                        //????????????02????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth02']])) {
	                        $familyBirthDay02 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth02']]);
			                if ($familyBirthDay02 == true) {
			                    $familyBirthDay02->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth02($familyBirthDay02);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth02']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth02($noname);
                        }
		                
						//????????????03?????????
                        $Customer->setFamilyName03(StringUtil::trimAll($row[$headerByKey['family_name03']]));
                        //????????????03?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex03_id']])) {
	                    	$FamilySex03 = $this->sexRepository->find($row[$headerByKey['family_sex03_id']]);
			                if (!$FamilySex03) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex03_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex03($FamilySex03);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex03($noname);
                        }
                        
                        //????????????03?????????
                        $Customer->setFamilyRelation03(StringUtil::trimAll($row[$headerByKey['family_relation03']]));
                        //????????????03????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth03']])) {
	                        $familyBirthDay03 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth03']]);
			                if ($familyBirthDay03 == true) {
			                    $familyBirthDay03->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth03($familyBirthDay03);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth03']]);
	                            $this->addErrors($message);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth03($noname);
                        }
		                
						//????????????04?????????
                        $Customer->setFamilyName04(StringUtil::trimAll($row[$headerByKey['family_name04']]));
                        //????????????04?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex04_id']])) {
	                    	$FamilySex04 = $this->sexRepository->find($row[$headerByKey['family_sex04_id']]);
			                if (!$FamilySex04) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex04_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex04($FamilySex04);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex04($noname);
                        }
                        
                        //????????????04?????????
                        $Customer->setFamilyRelation04(StringUtil::trimAll($row[$headerByKey['family_relation04']]));
                        //????????????04????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth04']])) {
	                        $familyBirthDay04 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth04']]);
			                if ($familyBirthDay04 == true) {
			                    $familyBirthDay04->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth04($familyBirthDay04);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth04']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth04($noname);
                        }
		                
						//????????????05?????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_sex05_id']])) {
                        $Customer->setFamilyName05(StringUtil::trimAll($row[$headerByKey['family_name05']]));
                        //????????????05?????????
                    	$FamilySex05 = $this->sexRepository->find($row[$headerByKey['family_sex05_id']]);
			                if (!$FamilySex05) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex05_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex05($FamilySex05);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex05($noname);
                        }
                        
                        //????????????05?????????
                        $Customer->setFamilyRelation05(StringUtil::trimAll($row[$headerByKey['family_relation05']]));
                        //????????????05????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth05']])) {
	                        $familyBirthDay05 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth05']]);
			                if ($familyBirthDay05 == true) {
			                    $familyBirthDay05->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth05($familyBirthDay05);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth05']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth05($noname);
                        }
		                
						//????????????06?????????
                        $Customer->setFamilyName06(StringUtil::trimAll($row[$headerByKey['family_name06']]));
                        //????????????06?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex06_id']])) {
	                    	$FamilySex06 = $this->sexRepository->find($row[$headerByKey['family_sex06_id']]);
			                if (!$FamilySex06) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex06_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex06($FamilySex06);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex06($noname);
                        }
                        
                        //????????????06?????????
                        $Customer->setFamilyRelation06(StringUtil::trimAll($row[$headerByKey['family_relation06']]));
                        //????????????06????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth06']])) {
	                        $familyBirthDay06 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth06']]);
			                if ($familyBirthDay06 == true) {
			                    $familyBirthDay06->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth06($familyBirthDay06);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth06']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth06($noname);
                        }
		                
						//????????????07?????????
                        $Customer->setFamilyName07(StringUtil::trimAll($row[$headerByKey['family_name07']]));
                        //????????????07?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex07_id']])) {
	                    	$FamilySex07 = $this->sexRepository->find($row[$headerByKey['family_sex07_id']]);
			                if (!$FamilySex07) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex07_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex07($FamilySex07);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex07($noname);
                        }
                        
                        //????????????07?????????
                        $Customer->setFamilyRelation07(StringUtil::trimAll($row[$headerByKey['family_relation07']]));
                        //????????????07????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth07']])) {
	                        $familyBirthDay07 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth07']]);
			                if ($familyBirthDay07 == true) {
			                    $familyBirthDay07->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth07($familyBirthDay07);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth07']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth07($noname);
                        }
		                
						//????????????08?????????
                        $Customer->setFamilyName08(StringUtil::trimAll($row[$headerByKey['family_name08']]));
                        //????????????08?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex08_id']])) {
	                    	$FamilySex08 = $this->sexRepository->find($row[$headerByKey['family_sex08_id']]);
			                if (!$FamilySex08) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex08_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex08($FamilySex08);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex08($noname);
                        }
                        
                        //????????????08?????????
                        $Customer->setFamilyRelation08(StringUtil::trimAll($row[$headerByKey['family_relation08']]));
                        //????????????08????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth08']])) {
	                        $familyBirthDay08 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth08']]);
			                if ($familyBirthDay08 == true) {
			                    $familyBirthDay08->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth08($familyBirthDay08);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth08']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth08($noname);
                        }
		                
						//????????????09?????????
                        $Customer->setFamilyName09(StringUtil::trimAll($row[$headerByKey['family_name09']]));
                        //????????????09?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex09_id']])) {
	                    	$FamilySex09 = $this->sexRepository->find($row[$headerByKey['family_sex09_id']]);
			                if (!$FamilySex09) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex09_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex09($FamilySex09);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex09($noname);
                        }
                        
                        //????????????09?????????
                        $Customer->setFamilyRelation09(StringUtil::trimAll($row[$headerByKey['family_relation09']]));
                        //????????????09????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth09']])) {
	                        $familyBirthDay09 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth09']]);
			                if ($familyBirthDay09 == true) {
			                    $familyBirthDay09->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth09($familyBirthDay09);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth09']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth09($noname);
                        }
		                
						//????????????10?????????
                        $Customer->setFamilyName10(StringUtil::trimAll($row[$headerByKey['family_name10']]));
                        //????????????10?????????
                    	if (StringUtil::isNotBlank($row[$headerByKey['family_sex10_id']])) {
	                    	$FamilySex10 = $this->sexRepository->find($row[$headerByKey['family_sex10_id']]);
			                if (!$FamilySex10) {
			                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['family_sex10_id']]);
			                    $this->addErrors($message);
			                } else {
			                    $Customer->setFamilySex10($FamilySex10);
			                }
			            }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilySex10($noname);
                        }
                        
                        //????????????10?????????
                        $Customer->setFamilyRelation10(StringUtil::trimAll($row[$headerByKey['family_relation10']]));
                        //????????????10????????????
                        if (StringUtil::isNotBlank($row[$headerByKey['family_birth10']])) {
	                        $familyBirthDay10 = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['family_birth10']]);
			                if ($familyBirthDay10 == true) {
			                    $familyBirthDay10->setTime(0, 0, 0);
	                        	$Customer->setFamilyBirth10($familyBirthDay10);
			                }else{
			                	// ??????????????????????????????????????????????????????
			                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['family_birth10']]);
	                            $this->addErrors($message);
			                }
		                }else{
                            //???????????????null
                            $noname = null;
                            $Customer->setFamilyBirth10($noname);
                        }
		                
		                //-------------------- .[????????????] --------------------
                        
                        
                        //?????????
                        if (StringUtil::isBlank($row[$headerByKey['email']])) {
                            //??????????????????a@a.a(????????????)???
                            $noname = 'a@a.a'.(StringUtil::trimAll($row[$headerByKey['customer_code']]));
                            $Customer->setEmail($noname);
                        } else {
                            $Customer->setEmail(StringUtil::trimAll($row[$headerByKey['email']]));
                        }
                        
                        //???????????????
                        $encoder = $this->encoderFactory->getEncoder($Customer);

			            if ($Customer->getPassword() === $this->eccubeConfig['eccube_default_password']) {
			                $Customer->setPassword($previous_password);
			                
			            } else {
			                
			                //??????????????????????????????????????????????????????
			                
			                if ($Customer->getSalt() === null) {
			                    $Customer->setSalt($encoder->createSalt());
			                    $Customer->setSecretKey($this->customerRepository->getUniqueSecretKey());
			                    //salt????????????????????????
			                }
			                //$raw_pass='test1234';
			                $raw_pass = ''.StringUtil::trimAll($row[$headerByKey['password']]).'';
			                
			                $Customer->setPassword(  $encoder->encodePassword($raw_pass , $Customer->getSalt())  );

			            }

                        $this->entityManager->flush();
                    }

                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();

                    log_info('??????CSV????????????');
                    $message = 'admin.common.csv_upload_complete';
                    $this->session->getFlashBag()->add('eccube.admin.success', $message);

                    $cacheUtil->clearDoctrineCache();
                }
            }
        }
        

        return $this->renderWithError($form, $headers);
    }


    /**
     * ?????????????????????CSV????????????????????????????????????
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
     * ??????????????????????????????????????????
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
     * ??????????????????????????????????????????
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
     * ????????????CSV??????????????????
     *
     * @return array
     */
    protected function getCustomerCsvHeader()
    {
        return [
            //??????ID
            trans('admin.customer.customer_csv.customer_id_col') => [
                'id' => 'id',
                'description' => 'admin.customer.customer_csv.customer_id_description',
                'required' => false,
            ],
            //????????????
            trans('admin.customer.customer_csv.customer_code_col') => [
                'id' => 'customer_code',
                'description' => 'admin.customer.customer_csv.customer_code_description',
                'required' => true,
            ],
            //?????????????????????
            trans('admin.customer.customer_csv.display_status_col') => [
                'id' => 'status',
                'description' => 'admin.customer.customer_csv.display_status_description',
                'required' => true,
            ],
            //?????????1
            trans('admin.customer.customer_csv.customer_name01_col') => [
                'id' => 'name01',
                'description' => 'admin.customer.customer_csv.customer_name01_description',
                'required' => false,
            ],
            //?????????2
            trans('admin.customer.customer_csv.customer_name02_col') => [
                'id' => 'name02',
                'description' => 'admin.customer.customer_csv.customer_name02_description',
                'required' => false,
            ],
            //?????????1(??????)
            trans('admin.customer.customer_csv.customer_name01_kana_col') => [
                'id' => 'name01_kana',
                'description' => 'admin.customer.customer_csv.customer_name01_kana_description',
                'required' => false,
            ],
            //?????????2(??????)
            trans('admin.customer.customer_csv.customer_name02_kana_col') => [
                'id' => 'name02_kana',
                'description' => 'admin.customer.customer_csv.customer_name02_kana_description',
                'required' => false,
            ],
            
            //?????????????????????
            trans('admin.customer.customer_csv.email_col') => [
                'id' => 'email',
                'description' => 'admin.customer.customer_csv.email_description',
                'required' => false,
            ],
            //???????????????
            trans('admin.customer.customer_csv.password_col') => [
                'id' => 'password',
                'description' => 'admin.customer.customer_csv.password_description',
                'required' => true,
            ],
            
            //??????(????????????)
            trans('admin.customer.customer_csv.customer_postal_code_col') => [
                'id' => 'postal_code',
                'description' => 'admin.customer.customer_csv.customer_postal_code_description',
                'required' => false,
            ],
            //??????(??????1)
            trans('admin.customer.customer_csv.customer_addr01_col') => [
                'id' => 'addr01',
                'description' => 'admin.customer.customer_csv.customer_addr01_description',
                'required' => false,
            ],
            //??????(??????2)
            trans('admin.customer.customer_csv.customer_addr02_col') => [
                'id' => 'addr02',
                'description' => 'admin.customer.customer_csv.customer_addr02_description',
                'required' => false,
            ],
            //????????????
            trans('admin.customer.customer_csv.customer_phone_number_col') => [
                'id' => 'phone_number',
                'description' => 'admin.customer.customer_csv.customer_phone_number_description',
                'required' => false,
            ],
            //??????
            trans('admin.customer.customer_csv.customer_sex_col') => [
                'id' => 'sex',
                'description' => 'admin.customer.customer_csv.customer_sex_description',
                'required' => false,
            ],
            //?????????
            trans('admin.customer.customer_csv.customer_birth_col') => [
                'id' => 'birth',
                'description' => 'admin.customer.customer_csv.customer_birth_description',
                'required' => false,
            ],
            //????????????
            trans('admin.customer.customer_csv.customer_point_col') => [
                'id' => 'point',
                'description' => 'admin.customer.customer_csv.customer_point_description',
                'required' => false,
            ],
            /*
            //?????????????????????
            trans('admin.customer.customer_csv.customer_is_family_main_col') => [
                'id' => 'is_family_main',
                'description' => 'admin.customer.customer_csv.customer_is_family_main_description',
                'required' => true,
            ],
            //??????????????????ID
            trans('admin.customer.customer_csv.customer_family_main_customer_id_col') => [
                'id' => 'family_main_customer_id',
                'description' => 'admin.customer.customer_csv.customer_family_main_customer_id_description',
                'required' => false,
            ],
            */
            
            
            //????????????(????????????????????????????????????)1???10
            //01
            // ??????
            trans('admin.customer.customer_csv.customer_family_name01_col') => [
                'id' => 'family_name01',
                'description' => 'admin.customer.customer_csv.customer_family_name01_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex01_id_col') => [
                'id' => 'family_sex01_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex01_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation01_col') => [
                'id' => 'family_relation01',
                'description' => 'admin.customer.customer_csv.customer_family_relation01_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth01_col') => [
                'id' => 'family_birth01',
                'description' => 'admin.customer.customer_csv.customer_family_birth01_description',
                'required' => false,
            ],
            //02
            // ??????
            trans('admin.customer.customer_csv.customer_family_name02_col') => [
                'id' => 'family_name02',
                'description' => 'admin.customer.customer_csv.customer_family_name02_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex02_id_col') => [
                'id' => 'family_sex02_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex02_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation02_col') => [
                'id' => 'family_relation02',
                'description' => 'admin.customer.customer_csv.customer_family_relation02_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth02_col') => [
                'id' => 'family_birth02',
                'description' => 'admin.customer.customer_csv.customer_family_birth02_description',
                'required' => false,
            ],
            //03
            // ??????
            trans('admin.customer.customer_csv.customer_family_name03_col') => [
                'id' => 'family_name03',
                'description' => 'admin.customer.customer_csv.customer_family_name03_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex03_id_col') => [
                'id' => 'family_sex03_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex03_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation03_col') => [
                'id' => 'family_relation03',
                'description' => 'admin.customer.customer_csv.customer_family_relation03_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth03_col') => [
                'id' => 'family_birth03',
                'description' => 'admin.customer.customer_csv.customer_family_birth03_description',
                'required' => false,
            ],
            //04
            // ??????
            trans('admin.customer.customer_csv.customer_family_name04_col') => [
                'id' => 'family_name04',
                'description' => 'admin.customer.customer_csv.customer_family_name04_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex04_id_col') => [
                'id' => 'family_sex04_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex04_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation04_col') => [
                'id' => 'family_relation04',
                'description' => 'admin.customer.customer_csv.customer_family_relation04_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth04_col') => [
                'id' => 'family_birth04',
                'description' => 'admin.customer.customer_csv.customer_family_birth04_description',
                'required' => false,
            ],
            //05
            // ??????
            trans('admin.customer.customer_csv.customer_family_name05_col') => [
                'id' => 'family_name05',
                'description' => 'admin.customer.customer_csv.customer_family_name05_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex05_id_col') => [
                'id' => 'family_sex05_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex05_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation05_col') => [
                'id' => 'family_relation05',
                'description' => 'admin.customer.customer_csv.customer_family_relation05_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth05_col') => [
                'id' => 'family_birth05',
                'description' => 'admin.customer.customer_csv.customer_family_birth05_description',
                'required' => false,
            ],
            //06
            // ??????
            trans('admin.customer.customer_csv.customer_family_name06_col') => [
                'id' => 'family_name06',
                'description' => 'admin.customer.customer_csv.customer_family_name06_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex06_id_col') => [
                'id' => 'family_sex06_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex06_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation06_col') => [
                'id' => 'family_relation06',
                'description' => 'admin.customer.customer_csv.customer_family_relation06_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth06_col') => [
                'id' => 'family_birth06',
                'description' => 'admin.customer.customer_csv.customer_family_birth06_description',
                'required' => false,
            ],
            //07
            // ??????
            trans('admin.customer.customer_csv.customer_family_name07_col') => [
                'id' => 'family_name07',
                'description' => 'admin.customer.customer_csv.customer_family_name07_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex07_id_col') => [
                'id' => 'family_sex07_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex07_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation07_col') => [
                'id' => 'family_relation07',
                'description' => 'admin.customer.customer_csv.customer_family_relation07_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth07_col') => [
                'id' => 'family_birth07',
                'description' => 'admin.customer.customer_csv.customer_family_birth07_description',
                'required' => false,
            ],
            //08
            // ??????
            trans('admin.customer.customer_csv.customer_family_name08_col') => [
                'id' => 'family_name08',
                'description' => 'admin.customer.customer_csv.customer_family_name08_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex08_id_col') => [
                'id' => 'family_sex08_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex08_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation08_col') => [
                'id' => 'family_relation08',
                'description' => 'admin.customer.customer_csv.customer_family_relation08_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth08_col') => [
                'id' => 'family_birth08',
                'description' => 'admin.customer.customer_csv.customer_family_birth08_description',
                'required' => false,
            ],
            //09
            // ??????
            trans('admin.customer.customer_csv.customer_family_name09_col') => [
                'id' => 'family_name09',
                'description' => 'admin.customer.customer_csv.customer_family_name09_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex09_id_col') => [
                'id' => 'family_sex09_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex09_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation09_col') => [
                'id' => 'family_relation09',
                'description' => 'admin.customer.customer_csv.customer_family_relation09_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth09_col') => [
                'id' => 'family_birth09',
                'description' => 'admin.customer.customer_csv.customer_family_birth09_description',
                'required' => false,
            ],
            //10
            // ??????
            trans('admin.customer.customer_csv.customer_family_name10_col') => [
                'id' => 'family_name10',
                'description' => 'admin.customer.customer_csv.customer_family_name10_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_sex10_id_col') => [
                'id' => 'family_sex10_id',
                'description' => 'admin.customer.customer_csv.customer_family_sex10_id_description',
                'required' => false,
            ],
            // ??????
            trans('admin.customer.customer_csv.customer_family_relation10_col') => [
                'id' => 'family_relation10',
                'description' => 'admin.customer.customer_csv.customer_family_relation10_description',
                'required' => false,
            ],
            // ?????????
            trans('admin.customer.customer_csv.customer_family_birth10_col') => [
                'id' => 'family_birth10',
                'description' => 'admin.customer.customer_csv.customer_family_birth10_description',
                'required' => false,
            ],
        ];
    }
    
    /**
     * ??????????????????????????????????????????????????????
     */
    protected function prefecture_from_zip( $zip ){
		     if( preg_match( '/^01/',     $zip ) ){ return  5; } // "?????????";   }
		else if( preg_match( '/^02/',     $zip ) ){ return  3; } // "?????????";   }
		else if( preg_match( '/^03/',     $zip ) ){ return  2; } // "?????????";   }
		else if( preg_match( '/^0[4-9]/', $zip ) ){ return  1; } // "?????????";   }
		else if( preg_match( '/^1[0-9]/', $zip ) ){ return 13; } // "?????????";   }
		// ^20?????????
		else if( preg_match( '/^2[1-5]/', $zip ) ){ return 14; } // "????????????"; }
		else if( preg_match( '/^2[679]/', $zip ) ){ return 12; } // "?????????";   }
		// ^28?????????
		else if( preg_match( '/^3[01]/',  $zip ) ){ return  8; } // "?????????";   }
		else if( preg_match( '/^32/',     $zip ) ){ return  9; } // "?????????";   }
		else if( preg_match( '/^3[3-6]/', $zip ) ){ return 11; } // "?????????";   }
		else if( preg_match( '/^37/',     $zip ) ){ return 10; } // "?????????";   }
		else if( preg_match( '/^3[89]/',  $zip ) ){ return 20; } // "?????????";   }
		else if( preg_match( '/^40/',     $zip ) ){ return 19; } // "?????????";   }
		else if( preg_match( '/^4[1-3]/', $zip ) ){ return 22; } // "?????????";   }
		else if( preg_match( '/^4[4-9]/', $zip ) ){ return 23; } // "?????????";   }
		else if( preg_match( '/^50/',     $zip ) ){ return 21; } // "?????????";   }
		else if( preg_match( '/^51/',     $zip ) ){ return 24; } // "?????????";   }
		else if( preg_match( '/^520\-?046[1-5]$/', $zip ) ){ return 26; } // "?????????";   } // ??????
		else if( preg_match( '/^52/',     $zip ) ){ return 25; } // "?????????";   }
		else if( preg_match( '/^5[3-9]/', $zip ) ){ return 27; } // "?????????";   }
		else if( preg_match( '/^6[0-2]/', $zip ) ){ return 26; } // "?????????"; }
		else if( preg_match( '/^630\-?027[12]$/',  $zip ) ){ return 27; } // "?????????"; } // ??????
		else if( preg_match( '/^63/',     $zip ) ){ return 29; } // "?????????"; }
		else if( preg_match( '/^64/',     $zip ) ){ return 30; } // "????????????"; }
		else if( preg_match( '/^6[5-7]/', $zip ) ){ return 28; } // "?????????"; }
		else if( preg_match( '/^68/',     $zip ) ){ return 31; } // "?????????"; }
		else if( preg_match( '/^69/',     $zip ) ){ return 32; } // "?????????"; }
		else if( preg_match( '/^7[01]/',  $zip ) ){ return 33; } // "?????????"; }
		else if( preg_match( '/^7[23]/',  $zip ) ){ return 34; } // "?????????"; }
		else if( preg_match( '/^7[45]/',  $zip ) ){ return 35; } // "?????????"; }
		else if( preg_match( '/^76/',     $zip ) ){ return 37; } // "?????????"; }
		else if( preg_match( '/^77/',     $zip ) ){ return 36; } // "?????????"; }
		else if( preg_match( '/^78/',     $zip ) ){ return 39; } // "?????????"; }
		else if( preg_match( '/^79/',     $zip ) ){ return 38; } //"?????????"; }
		else if( preg_match( '/^8[0-3]/', $zip ) ){ return 40; } // "?????????"; }
		else if( preg_match( '/^84/',     $zip ) ){ return 41; } // "?????????"; }
		else if( preg_match( '/^85/',     $zip ) ){ return 42; } // "?????????"; }
		else if( preg_match( '/^86/',     $zip ) ){ return 43; } // "?????????"; }
		else if( preg_match( '/^87/',     $zip ) ){ return 44; } //"?????????"; }
		else if( preg_match( '/^88/',     $zip ) ){ return 45; } // "?????????"; }
		else if( preg_match( '/^89/',     $zip ) ){ return 46; } // "????????????"; }
		else if( preg_match( '/^90/',     $zip ) ){ return 47; } // "?????????"; }
		else if( preg_match( '/^91/',     $zip ) ){ return 18; } // "?????????"; }
		else if( preg_match( '/^92/',     $zip ) ){ return 17; } // "?????????"; }
		else if( preg_match( '/^93/',     $zip ) ){ return 16; } // "?????????"; }
		else if( preg_match( '/^9[45]/',  $zip ) ){ return 15; } // "?????????"; }
		else if( preg_match( '/^9[67]/',  $zip ) ){ return  7; } // "?????????"; }
		else if( preg_match( '/^98/',     $zip ) ){ return  4; } // "?????????"; }
		else if( preg_match( '/^99/',     $zip ) ){ return  6; } // "?????????"; }
		
		return null;
	}
}
