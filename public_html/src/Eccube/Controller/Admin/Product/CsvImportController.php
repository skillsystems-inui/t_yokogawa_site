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

namespace Eccube\Controller\Admin\Product;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Common\Constant;
use Eccube\Controller\Admin\AbstractCsvImportController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Category;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductClassCategory;
use Eccube\Entity\ProductClassName;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\ProductTag;
use Eccube\Form\Type\Admin\CsvImportType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\Master\SalesTypeRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TagRepository;
use Eccube\Repository\TaxRuleRepository;
use Plugin\RelatedProduct4\Entity\RelatedProduct;
use Plugin\RelatedProduct4\Repository\RelatedProductRepository;
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
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var SalesTypeRepository
     */
    protected $salesTypeRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * @var RelatedProductRepository
     */
    protected $relatedProductRepository;

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
     * @param TagRepository $tagRepository
     * @param CategoryRepository $categoryRepository
     * @param ClassCategoryRepository $classCategoryRepository
     * @param ClassNameRepository $classNameRepository
     * @param ProductStatusRepository $productStatusRepository
     * @param ProductStatusRepository $salesTypeRepository
     * @param ProductRepository $productRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param RelatedProductRepository $relatedProductRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param ValidatorInterface $validator
     * @throws \Exception
     */
    public function __construct(
        DeliveryDurationRepository $deliveryDurationRepository,
        SaleTypeRepository $saleTypeRepository,
        TagRepository $tagRepository,
        CategoryRepository $categoryRepository,
        ClassCategoryRepository $classCategoryRepository,
        ClassNameRepository $classNameRepository,
        ProductStatusRepository $productStatusRepository,
        SalesTypeRepository $salesTypeRepository,
        ProductRepository $productRepository,
        TaxRuleRepository $taxRuleRepository,
        RelatedProductRepository $relatedProductRepository,
        BaseInfoRepository $baseInfoRepository,
        ValidatorInterface $validator
    ) {
        $this->deliveryDurationRepository = $deliveryDurationRepository;
        $this->saleTypeRepository = $saleTypeRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->classNameRepository = $classNameRepository;
        $this->productStatusRepository = $productStatusRepository;
        $this->salesTypeRepository = $salesTypeRepository;
        $this->productRepository = $productRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->relatedProductRepository = $relatedProductRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->validator = $validator;
    }

    /**
     * ????????????CSV??????????????????
     *
     * @Route("/%eccube_admin_route%/product/product_csv_upload", name="admin_product_csv_import")
     * @Template("@admin/Product/csv_product.twig")
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function csvProduct(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();
        $headers = $this->getProductCsvHeader();
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
                    $deleteImages = [];

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
                            $Product = new Product();
                            $this->entityManager->persist($Product);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['id']])) {
                                $Product = $this->productRepository->find($row[$headerByKey['id']]);
                                if (!$Product) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['id']]);
                                    $this->addErrors($message);

                                    return $this->renderWithError($form, $headers);
                                }
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['id']]);
                                $this->addErrors($message);

                                return $this->renderWithError($form, $headers);
                            }

                            if (isset($row[$headerByKey['product_del_flg']])) {
                                if (StringUtil::isNotBlank($row[$headerByKey['product_del_flg']]) && $row[$headerByKey['product_del_flg']] == (string) Constant::ENABLED) {
                                    // ?????????????????????
                                    $deleteImages[] = $Product->getProductImage();

                                    try {
                                        $this->productRepository->delete($Product);
                                        $this->entityManager->flush();

                                        continue;
                                    } catch (ForeignKeyConstraintViolationException $e) {
                                        $message = trans('admin.common.csv_invalid_foreign_key', ['%line%' => $line, '%name%' => $Product->getName()]);
                                        $this->addErrors($message);

                                        return $this->renderWithError($form, $headers);
                                    }
                                }
                            }
                        }

                        if (StringUtil::isBlank($row[$headerByKey['status']])) {
                            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                            $this->addErrors($message);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['status']])) {
                                $ProductStatus = $this->productStatusRepository->find($row[$headerByKey['status']]);
                                if (!$ProductStatus) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                                    $this->addErrors($message);
                                } else {
                                    $Product->setStatus($ProductStatus);
                                }
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['status']]);
                                $this->addErrors($message);
                            }
                        }

                        if (StringUtil::isBlank($row[$headerByKey['name']])) {
                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['name']]);
                            $this->addErrors($message);

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Product->setName(StringUtil::trimAll($row[$headerByKey['name']]));
                        }

                        if (isset($row[$headerByKey['note']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['note']])) {
                                $Product->setNote(StringUtil::trimAll($row[$headerByKey['note']]));
                            } else {
                                $Product->setNote(null);
                            }
                        }

                        if (isset($row[$headerByKey['description_list']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['description_list']])) {
                                $Product->setDescriptionList(StringUtil::trimAll($row[$headerByKey['description_list']]));
                            } else {
                                $Product->setDescriptionList(null);
                            }
                        }

                        if (isset($row[$headerByKey['description_detail']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['description_detail']])) {
                                if (mb_strlen($row[$headerByKey['description_detail']]) > $this->eccubeConfig['eccube_ltext_len']) {
                                    $message = trans('admin.common.csv_invalid_description_detail_upper_limit', [
                                        '%line%' => $line,
                                        '%name%' => $headerByKey['description_detail'],
                                        '%max%' => $this->eccubeConfig['eccube_ltext_len'],
                                    ]);
                                    $this->addErrors($message);

                                    return $this->renderWithError($form, $headers);
                                } else {
                                    $Product->setDescriptionDetail(StringUtil::trimAll($row[$headerByKey['description_detail']]));
                                }
                            } else {
                                $Product->setDescriptionDetail(null);
                            }
                        }

                        if (isset($row[$headerByKey['search_word']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['search_word']])) {
                                $Product->setSearchWord(StringUtil::trimAll($row[$headerByKey['search_word']]));
                            } else {
                                $Product->setSearchWord(null);
                            }
                        }

                        if (isset($row[$headerByKey['free_area']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['free_area']])) {
                                $Product->setFreeArea(StringUtil::trimAll($row[$headerByKey['free_area']]));
                            } else {
                                $Product->setFreeArea(null);
                            }
                        }
                        
                        //???????????????????????????(??????)
                        if (isset($row[$headerByKey['sales_period_from']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['sales_period_from']])) {
                                
                                $salesPeriodFromDate = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['sales_period_from']]);
				                if ($salesPeriodFromDate == true) {
				                    //????????????(??????)???????????????
				                    $salesPeriodFromDate->setTime(0, 0, 0);
                                	$Product->setSalesPeriodFrom($salesPeriodFromDate);
				                }else{
				                	// ??????????????????????????????????????????????????????
				                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['sales_period_from']]);
                                    $this->addErrors($message);
				                }
                            } else {
                                //????????????????????????null
                                $Product->setSalesPeriodFrom(null);
                            }
                        }
                        //???????????????????????????(??????)
                        if (isset($row[$headerByKey['sales_period_to']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['sales_period_to']])) {
                                
                                $salesPeriodToDate = \DateTime::createFromFormat('Y-m-d', $row[$headerByKey['sales_period_to']]);
				                if ($salesPeriodToDate == true) {
				                    //????????????(??????)???????????????
				                    $salesPeriodToDate->setTime(0, 0, 0);
                                	$Product->setSalesPeriodTo($salesPeriodToDate);
				                }else{
				                	// ??????????????????????????????????????????????????????
				                	$message = trans('admin.common.csv_invalid_date_format', ['%line%' => $line, '%name%' => $headerByKey['sales_period_to']]);
                                    $this->addErrors($message);
				                }
                            } else {
                                //????????????????????????null
                                $Product->setSalesPeriodTo(null);
                            }
                        }
                        
                        //????????????????????????
                        if (isset($row[$headerByKey['capacity']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['capacity']])) {
                                $Product->setCapacity(StringUtil::trimAll($row[$headerByKey['capacity']]));
                            } else {
                                $Product->setCapacity(null);
                            }
                        }
                        
                        //???????????????????????????
                        if (isset($row[$headerByKey['expiry_date']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['expiry_date']])) {
                                $Product->setExpiryDate(StringUtil::trimAll($row[$headerByKey['expiry_date']]));
                            } else {
                                $Product->setExpiryDate(null);
                            }
                        }
                        
                        //???????????????????????????
                        if (isset($row[$headerByKey['expiration_date']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['expiration_date']])) {
                                $Product->setExpirationDate(StringUtil::trimAll($row[$headerByKey['expiration_date']]));
                            } else {
                                $Product->setExpirationDate(null);
                            }
                        }
                        
                        //????????????????????????
                        if (isset($row[$headerByKey['material']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['material']])) {
                                $Product->setMaterial(StringUtil::trimAll($row[$headerByKey['material']]));
                            } else {
                                $Product->setMaterial(null);
                            }
                        }
                        
                        //????????????????????????????????????
                        if (isset($row[$headerByKey['please_read']])) {
                            if (StringUtil::isNotBlank($row[$headerByKey['please_read']])) {
                                $Product->setPleaseRead(StringUtil::trimAll($row[$headerByKey['please_read']]));
                            } else {
                                $Product->setPleaseRead(null);
                            }
                        }
                        
                        //???????????????????????????(ID) 
                        if (StringUtil::isBlank($row[$headerByKey['sales_type']])) {
                            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['sales_type']]);
                            $this->addErrors($message);
                        } else {
                            if (preg_match('/^\d+$/', $row[$headerByKey['sales_type']])) {
                                $ProductSalesType = $this->salesTypeRepository->find($row[$headerByKey['sales_type']]);
                                if (!$ProductSalesType) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sales_type']]);
                                    $this->addErrors($message);
                                } else {
                                    $Product->setSalestype($ProductSalesType);
                                }
                            } else {
                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sales_type']]);
                                $this->addErrors($message);
                            }
                        }
                        
                        // ??????????????????
                        $this->createProductImage($row, $Product, $data, $headerByKey);

                        $this->entityManager->flush();
                        
                        //??????????????????????????????(ID)
                        $this->createProductOption($row, $Product, $data, $headerByKey);
                        $this->entityManager->flush();
                        
                        //???????????????????????????(ID)
                        $this->createRelatedProduct($row, $Product, $data, $headerByKey);
                        $this->entityManager->flush();

                        // ????????????????????????
                        $this->createProductCategory($row, $Product, $data, $headerByKey);

                        //????????????
                        $this->createProductTag($row, $Product, $data, $headerByKey);

                        // ????????????????????????????????????????????????
                        /** @var ProductClass[] $ProductClasses */
                        $ProductClasses = $Product->getProductClasses();
                        if ($ProductClasses->count() < 1) {
                            // ????????????1(ID)??????????????????????????????????????????????????????????????????????????????
                            $ProductClassOrg = $this->createProductClass($row, $Product, $data, $headerByKey);
                            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                                    $deliveryFee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                                    $errors = $this->validator->validate($deliveryFee, new GreaterThanOrEqual(['value' => 0]));
                                    if ($errors->count() === 0) {
                                        $ProductClassOrg->setDeliveryFee($deliveryFee);
                                    } else {
                                        $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                                        $this->addErrors($message);
                                    }
                                }
                            }

                            // ?????????????????????????????????????????????????????????
                            if ($this->BaseInfo->isOptionProductTaxRule()) {
                                if (isset($row[$headerByKey['tax_rate']]) && StringUtil::isNotBlank($row[$headerByKey['tax_rate']])) {
                                    $taxRate = $row[$headerByKey['tax_rate']];
                                    $errors = $this->validator->validate($taxRate, new GreaterThanOrEqual(['value' => 0]));
                                    if ($errors->count() === 0) {
                                        if ($ProductClassOrg->getTaxRule()) {
                                            // ???????????????????????????????????????????????????
                                            $ProductClassOrg->getTaxRule()->setTaxRate($taxRate);
                                        } else {
                                            // ???????????????????????????????????????????????????
                                            $TaxRule = $this->taxRuleRepository->newTaxRule();
                                            $TaxRule->setTaxRate($taxRate);
                                            $TaxRule->setApplyDate(new \DateTime());
                                            $TaxRule->setProduct($Product);
                                            $TaxRule->setProductClass($ProductClassOrg);
                                            $ProductClassOrg->setTaxRule($TaxRule);
                                        }
                                    } else {
                                        $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['tax_rate']]);
                                        $this->addErrors($message);
                                    }
                                } else {
                                    // ??????????????????????????????????????????????????????
                                    if ($ProductClassOrg->getTaxRule()) {
                                        $this->taxRuleRepository->delete($ProductClassOrg->getTaxRule());
                                        $ProductClassOrg->setTaxRule(null);
                                    }
                                }
                            }

                            if (isset($row[$headerByKey['class_category1']]) && StringUtil::isNotBlank($row[$headerByKey['class_category1']])) {
                                /*??????2????????????
                                if (isset($row[$headerByKey['class_category2']]) && $row[$headerByKey['class_category1']] == $row[$headerByKey['class_category2']]) {
                                    $message = trans('admin.common.csv_invalid_not_same', [
                                        '%line%' => $line,
                                        '%name1%' => $headerByKey['class_category1'],
                                        '%name2%' => $headerByKey['class_category2'],
                                    ]);
                                    $this->addErrors($message);
                                } else {
                                */
                                    // ??????????????????
                                    // ?????????????????????????????????
                                    $ProductClass = clone $ProductClassOrg;
                                    $ProductStock = clone $ProductClassOrg->getProductStock();

                                    // ????????????1???????????????2???null??????????????????????????????
                                    $ProductClassOrg->setVisible(false);

                                    // ????????????1???2?????????????????????????????????
                                    $ClassCategory1 = null;
                                    if (preg_match('/^\d+$/', $row[$headerByKey['class_category1']])) {
                                        $ClassCategory1 = $this->classCategoryRepository->find($row[$headerByKey['class_category1']]);
                                        if (!$ClassCategory1) {
                                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                            $this->addErrors($message);
                                        } else {
                                            $ProductClass->setClassCategory1($ClassCategory1);
                                        }
                                    } else {
                                        $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                        $this->addErrors($message);
                                    }

                                    /*??????2????????????
                                    if (isset($row[$headerByKey['class_category2']]) && StringUtil::isNotBlank($row[$headerByKey['class_category2']])) {
                                        if (preg_match('/^\d+$/', $row[$headerByKey['class_category2']])) {
                                            $ClassCategory2 = $this->classCategoryRepository->find($row[$headerByKey['class_category2']]);
                                            if (!$ClassCategory2) {
                                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                                $this->addErrors($message);
                                            } else {
                                                if ($ClassCategory1 &&
                                                    ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                ) {
                                                    $message = trans('admin.common.csv_invalid_not_same', ['%line%' => $line, '%name1%' => $headerByKey['class_category1'], '%name2%' => $headerByKey['class_category2']]);
                                                    $this->addErrors($message);
                                                } else {
                                                    $ProductClass->setClassCategory2($ClassCategory2);
                                                }
                                            }
                                        } else {
                                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                            $this->addErrors($message);
                                        }
                                    }
                                    */
                                    
                                    $ProductClass->setProductStock($ProductStock);
                                    $ProductStock->setProductClass($ProductClass);

                                    $this->entityManager->persist($ProductClass);
                                    $this->entityManager->persist($ProductStock);
                                //} //??????2????????????
                            } else {
                                /*??????2????????????
                                if (isset($row[$headerByKey['class_category2']]) && StringUtil::isNotBlank($row[$headerByKey['class_category2']])) {
                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                    $this->addErrors($message);
                                }
                                */
                            }
                        } else {
                            // ?????????????????????
                            $flag = false;
                            $classCategoryId1 = StringUtil::isBlank($row[$headerByKey['class_category1']]) ? null : $row[$headerByKey['class_category1']];
                            //??????2????????????
                            //$classCategoryId2 = StringUtil::isBlank($row[$headerByKey['class_category2']]) ? null : $row[$headerByKey['class_category2']];
                            $classCategoryId2 = null;

                            foreach ($ProductClasses as $pc) {
                                $classCategory1 = is_null($pc->getClassCategory1()) ? null : $pc->getClassCategory1()->getId();
                                $classCategory2 = is_null($pc->getClassCategory2()) ? null : $pc->getClassCategory2()->getId();

                                // ??????????????????????????????????????????
                                if ($classCategory1 == $classCategoryId1 &&
                                    $classCategory2 == $classCategoryId2
                                ) {
                                    $this->updateProductClass($row, $Product, $pc, $data, $headerByKey);

                                    if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                        if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                                            $deliveryFee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                                            $errors = $this->validator->validate($deliveryFee, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                $pc->setDeliveryFee($deliveryFee);
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    }

                                    // ?????????????????????????????????????????????????????????
                                    if ($this->BaseInfo->isOptionProductTaxRule()) {
                                        if (isset($row[$headerByKey['tax_rate']]) && StringUtil::isNotBlank($row[$headerByKey['tax_rate']])) {
                                            $taxRate = $row[$headerByKey['tax_rate']];
                                            $errors = $this->validator->validate($taxRate, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                if ($pc->getTaxRule()) {
                                                    // ???????????????????????????????????????????????????
                                                    $pc->getTaxRule()->setTaxRate($taxRate);
                                                } else {
                                                    // ???????????????????????????????????????????????????
                                                    $TaxRule = $this->taxRuleRepository->newTaxRule();
                                                    $TaxRule->setTaxRate($taxRate);
                                                    $TaxRule->setApplyDate(new \DateTime());
                                                    $TaxRule->setProduct($Product);
                                                    $TaxRule->setProductClass($pc);
                                                    $pc->setTaxRule($TaxRule);
                                                }
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['tax_rate']]);
                                                $this->addErrors($message);
                                            }
                                        } else {
                                            // ??????????????????????????????????????????????????????
                                            if ($pc->getTaxRule()) {
                                                $this->taxRuleRepository->delete($pc->getTaxRule());
                                                $pc->setTaxRule(null);
                                            }
                                        }
                                    }

                                    $flag = true;
                                    break;
                                }
                            }

                            // ?????????????????????
                            if (!$flag) {
                                $pc = $ProductClasses[0];
                                if ($pc->getClassCategory1() == null &&
                                    $pc->getClassCategory2() == null
                                ) {
                                    // ????????????1???????????????2???null??????????????????????????????
                                    $pc->setVisible(false);
                                }

                                /*??????2????????????
                                if (isset($row[$headerByKey['class_category1']]) && isset($row[$headerByKey['class_category2']])
                                    && $row[$headerByKey['class_category1']] == $row[$headerByKey['class_category2']]) {
                                    $message = trans('admin.common.csv_invalid_not_same', [
                                        '%line%' => $line,
                                        '%name1%' => $headerByKey['class_category1'],
                                        '%name2%' => $headerByKey['class_category2'],
                                    ]);
                                    $this->addErrors($message);
                                } else {
                                */
                                    // ??????????????????1???????????????????????????
                                    // ????????????1???2?????????????????????????????????
                                    $ClassCategory1 = null;
                                    if (preg_match('/^\d+$/', $classCategoryId1)) {
                                        $ClassCategory1 = $this->classCategoryRepository->find($classCategoryId1);
                                        if (!$ClassCategory1) {
                                            $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                            $this->addErrors($message);
                                        }
                                    } else {
                                        $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                                        $this->addErrors($message);
                                    }

                                    $ClassCategory2 = null;
                                    /*??????2????????????
                                    if (isset($row[$headerByKey['class_category2']]) && StringUtil::isNotBlank($row[$headerByKey['class_category2']])) {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() == null) {
                                            $message = trans('admin.common.csv_invalid_can_not', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                            $this->addErrors($message);
                                        } else {
                                            if (preg_match('/^\d+$/', $classCategoryId2)) {
                                                $ClassCategory2 = $this->classCategoryRepository->find($classCategoryId2);
                                                if (!$ClassCategory2) {
                                                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                                    $this->addErrors($message);
                                                } else {
                                                    if ($ClassCategory1 &&
                                                        ($ClassCategory1->getClassName()->getId() == $ClassCategory2->getClassName()->getId())
                                                    ) {
                                                        $message = trans('admin.common.csv_invalid_not_same', [
                                                            '%line%' => $line,
                                                            '%name1%' => $headerByKey['class_category1'],
                                                            '%name2%' => $headerByKey['class_category2'],
                                                        ]);
                                                        $this->addErrors($message);
                                                    }
                                                }
                                            } else {
                                                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    } else {
                                        if ($pc->getClassCategory1() != null && $pc->getClassCategory2() != null) {
                                            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                                            $this->addErrors($message);
                                        }
                                    }
                                    */
                                    
                                    $ProductClass = $this->createProductClass($row, $Product, $data, $headerByKey, $ClassCategory1, $ClassCategory2);

                                    if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                                        if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                                            $deliveryFee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                                            $errors = $this->validator->validate($deliveryFee, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                $ProductClass->setDeliveryFee($deliveryFee);
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    }

                                    // ?????????????????????????????????????????????????????????
                                    if ($this->BaseInfo->isOptionProductTaxRule()) {
                                        if (isset($row[$headerByKey['tax_rate']]) && StringUtil::isNotBlank($row[$headerByKey['tax_rate']])) {
                                            $taxRate = $row[$headerByKey['tax_rate']];
                                            $errors = $this->validator->validate($taxRate, new GreaterThanOrEqual(['value' => 0]));
                                            if ($errors->count() === 0) {
                                                $TaxRule = $this->taxRuleRepository->newTaxRule();
                                                $TaxRule->setTaxRate($taxRate);
                                                $TaxRule->setApplyDate(new \DateTime());
                                                $TaxRule->setProduct($Product);
                                                $TaxRule->setProductClass($ProductClass);
                                                $ProductClass->setTaxRule($TaxRule);
                                            } else {
                                                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['tax_rate']]);
                                                $this->addErrors($message);
                                            }
                                        }
                                    }

                                    $Product->addProductClass($ProductClass);
                                //} //??????2????????????
                            }
                        }
                        if ($this->hasErrors()) {
                            return $this->renderWithError($form, $headers);
                        }
                        $this->entityManager->persist($Product);
                    }
                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();

                    // ???????????????????????????(commit?????????????????????)
                    foreach ($deleteImages as $images) {
                        foreach ($images as $image) {
                            try {
                                $fs = new Filesystem();
                                $fs->remove($this->eccubeConfig['eccube_save_image_dir'].'/'.$image);
                            } catch (\Exception $e) {
                                // ???????????????????????????????????????
                            }
                        }
                    }

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
     * ??????????????????CSV??????????????????
     *
     * @Route("/%eccube_admin_route%/product/category_csv_upload", name="admin_product_category_csv_import")
     * @Template("@admin/Product/csv_category.twig")
     */
    public function csvCategory(Request $request, CacheUtil $cacheUtil)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();

        $headers = $this->getCategoryCsvHeader();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formFile = $form['import_file']->getData();
                if (!empty($formFile)) {
                    log_info('????????????CSV????????????');
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

                    $headerByKey = array_flip(array_map($getId, $headers));

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
                    $this->entityManager->getConfiguration()->setSQLLogger(null);
                    $this->entityManager->getConnection()->beginTransaction();
                    // CSV???????????????????????????
                    foreach ($data as $row) {
                        /** @var $Category Category */
                        $Category = new Category();
                        if (isset($row[$headerByKey['id']]) && strlen($row[$headerByKey['id']]) > 0) {
                            if (!preg_match('/^\d+$/', $row[$headerByKey['id']])) {
                                $this->addErrors(($data->key() + 1).'?????????????????????ID????????????????????????');

                                return $this->renderWithError($form, $headers);
                            }
                            $Category = $this->categoryRepository->find($row[$headerByKey['id']]);
                            if (!$Category) {
                                $this->addErrors(($data->key() + 1).'????????????????????????????????????ID???????????????????????????????????????????????????????????????ID??????????????????????????????????????????');

                                return $this->renderWithError($form, $headers);
                            }
                            if ($row[$headerByKey['id']] == $row[$headerByKey['parent_category_id']]) {
                                $this->addErrors(($data->key() + 1).'?????????????????????ID??????????????????ID??????????????????');

                                return $this->renderWithError($form, $headers);
                            }
                        }

                        if (isset($row[$headerByKey['category_del_flg']]) && StringUtil::isNotBlank($row[$headerByKey['category_del_flg']])) {
                            if (StringUtil::trimAll($row[$headerByKey['category_del_flg']]) == 1) {
                                if ($Category->getId()) {
                                    log_info('????????????????????????', [$Category->getId()]);
                                    try {
                                        $this->categoryRepository->delete($Category);
                                        log_info('????????????????????????', [$Category->getId()]);
                                    } catch (ForeignKeyConstraintViolationException $e) {
                                        log_info('???????????????????????????', [$Category->getId(), $e]);
                                        $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Category->getName()]);
                                        $this->addError($message, 'admin');

                                        return $this->renderWithError($form, $headers);
                                    }
                                }

                                continue;
                            }
                        }

                        if (!isset($row[$headerByKey['category_name']]) || StringUtil::isBlank($row[$headerByKey['category_name']])) {
                            $this->addErrors(($data->key() + 1).'?????????????????????????????????????????????????????????');

                            return $this->renderWithError($form, $headers);
                        } else {
                            $Category->setName(StringUtil::trimAll($row[$headerByKey['category_name']]));
                        }

                        $ParentCategory = null;
                        if (isset($row[$headerByKey['parent_category_id']]) && StringUtil::isNotBlank($row[$headerByKey['parent_category_id']])) {
                            if (!preg_match('/^\d+$/', $row[$headerByKey['parent_category_id']])) {
                                $this->addErrors(($data->key() + 1).'????????????????????????ID????????????????????????');

                                return $this->renderWithError($form, $headers);
                            }

                            /** @var $ParentCategory Category */
                            $ParentCategory = $this->categoryRepository->find($row[$headerByKey['parent_category_id']]);
                            if (!$ParentCategory) {
                                $this->addErrors(($data->key() + 1).'????????????????????????ID????????????????????????');

                                return $this->renderWithError($form, $headers);
                            }
                        }
                        $Category->setParent($ParentCategory);

                        // Level
                        if (isset($row['??????']) && StringUtil::isNotBlank($row['??????'])) {
                            if ($ParentCategory == null && $row['??????'] != 1) {
                                $this->addErrors(($data->key() + 1).'????????????????????????ID????????????????????????');

                                return $this->renderWithError($form, $headers);
                            }
                            $level = StringUtil::trimAll($row['??????']);
                        } else {
                            $level = 1;
                            if ($ParentCategory) {
                                $level = $ParentCategory->getHierarchy() + 1;
                            }
                        }

                        $Category->setHierarchy($level);

                        if ($this->eccubeConfig['eccube_category_nest_level'] < $Category->getHierarchy()) {
                            $this->addErrors(($data->key() + 1).'???????????????????????????????????????????????????????????????????????????????????????');

                            return $this->renderWithError($form, $headers);
                        }

                        if ($this->hasErrors()) {
                            return $this->renderWithError($form, $headers);
                        }
                        $this->entityManager->persist($Category);
                        $this->categoryRepository->save($Category);
                    }

                    $this->entityManager->getConnection()->commit();
                    log_info('????????????CSV????????????');
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
     * @Route("/%eccube_admin_route%/product/csv_template/{type}", requirements={"type" = "\w+"}, name="admin_product_csv_template")
     *
     * @param $type
     *
     * @return StreamedResponse
     */
    public function csvTemplate(Request $request, $type)
    {
        if ($type == 'product') {
            $headers = $this->getProductCsvHeader();
            $filename = 'product.csv';
        } elseif ($type == 'category') {
            $headers = $this->getCategoryCsvHeader();
            $filename = 'category.csv';
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
     * ??????????????????????????????
     *
     * @param $row
     * @param Product $Product
     * @param CsvImportService $data
     * @param $headerByKey
     */
    protected function createProductImage($row, Product $Product, $data, $headerByKey)
    {
        if (!isset($row[$headerByKey['product_image']])) {
            return;
        }
        if (StringUtil::isNotBlank($row[$headerByKey['product_image']])) {
            // ???????????????
            $ProductImages = $Product->getProductImage();
            foreach ($ProductImages as $ProductImage) {
                $Product->removeProductImage($ProductImage);
                $this->entityManager->remove($ProductImage);
            }

            // ???????????????
            $images = explode(',', $row[$headerByKey['product_image']]);

            $sortNo = 1;

            $pattern = "/\\$|^.*.\.\\\.*|\/$|^.*.\.\/\.*/";
            foreach ($images as $image) {
                $fileName = StringUtil::trimAll($image);

                // ????????????????????????????????????????????????
                if (strlen($fileName) > 0 && preg_match($pattern, $fileName)) {
                    $message = trans('admin.common.csv_invalid_image', ['%line%' => $data->key() + 1, '%name%' => $headerByKey['product_image']]);
                    $this->addErrors($message);
                } else {
                    // ???????????????????????????
                    if (!empty($fileName)) {
                        $ProductImage = new ProductImage();
                        $ProductImage->setFileName($fileName);
                        $ProductImage->setProduct($Product);
                        $ProductImage->setSortNo($sortNo);

                        $Product->addProductImage($ProductImage);
                        $sortNo++;
                        $this->entityManager->persist($ProductImage);
                    }
                }
            }
        }
    }

    /**
     * ????????????????????????????????????
     *
     * @param $row
     * @param Product $Product
     * @param CsvImportService $data
     * @param $headerByKey
     */
    protected function createProductCategory($row, Product $Product, $data, $headerByKey)
    {
        if (!isset($row[$headerByKey['product_category']])) {
            return;
        }
        // ?????????????????????
        $ProductCategories = $Product->getProductCategories();
        foreach ($ProductCategories as $ProductCategory) {
            $Product->removeProductCategory($ProductCategory);
            $this->entityManager->remove($ProductCategory);
            $this->entityManager->flush();
        }

        if (StringUtil::isNotBlank($row[$headerByKey['product_category']])) {
            // ?????????????????????
            $categories = explode(',', $row[$headerByKey['product_category']]);
            $sortNo = 1;
            $categoriesIdList = [];
            foreach ($categories as $category) {
                $line = $data->key() + 1;
                if (preg_match('/^\d+$/', $category)) {
                    $Category = $this->categoryRepository->find($category);
                    if (!$Category) {
                        $message = trans('admin.common.csv_invalid_not_found_target', [
                            '%line%' => $line,
                            '%name%' => $headerByKey['product_category'],
                            '%target_name%' => $category,
                        ]);
                        $this->addErrors($message);
                    } else {
                        foreach ($Category->getPath() as $ParentCategory) {
                            if (!isset($categoriesIdList[$ParentCategory->getId()])) {
                                $ProductCategory = $this->makeProductCategory($Product, $ParentCategory, $sortNo);
                                $this->entityManager->persist($ProductCategory);
                                $sortNo++;

                                $Product->addProductCategory($ProductCategory);
                                $categoriesIdList[$ParentCategory->getId()] = true;
                            }
                        }
                        if (!isset($categoriesIdList[$Category->getId()])) {
                            $ProductCategory = $this->makeProductCategory($Product, $Category, $sortNo);
                            $sortNo++;
                            $this->entityManager->persist($ProductCategory);
                            $Product->addProductCategory($ProductCategory);
                            $categoriesIdList[$Category->getId()] = true;
                        }
                    }
                } else {
                    $message = trans('admin.common.csv_invalid_not_found_target', [
                        '%line%' => $line,
                        '%name%' => $headerByKey['product_category'],
                        '%target_name%' => $category,
                    ]);
                    $this->addErrors($message);
                }
            }
        }
    }

    /**
     * ???????????????
     *
     * @param array $row
     * @param Product $Product
     * @param CsvImportService $data
     */
    protected function createProductTag($row, Product $Product, $data, $headerByKey)
    {
        if (!isset($row[$headerByKey['product_tag']])) {
            return;
        }
        // ???????????????
        $ProductTags = $Product->getProductTag();
        foreach ($ProductTags as $ProductTag) {
            $Product->removeProductTag($ProductTag);
            $this->entityManager->remove($ProductTag);
        }

        if (StringUtil::isNotBlank($row[$headerByKey['product_tag']])) {
            // ???????????????
            $tags = explode(',', $row[$headerByKey['product_tag']]);
            foreach ($tags as $tag_id) {
                $Tag = null;
                if (preg_match('/^\d+$/', $tag_id)) {
                    $Tag = $this->tagRepository->find($tag_id);

                    if ($Tag) {
                        $ProductTags = new ProductTag();
                        $ProductTags
                            ->setProduct($Product)
                            ->setTag($Tag);

                        $Product->addProductTag($ProductTags);

                        $this->entityManager->persist($ProductTags);
                    }
                }
                if (!$Tag) {
                    $message = trans('admin.common.csv_invalid_not_found_target', [
                        '%line%' => $data->key() + 1,
                        '%name%' => $headerByKey['product_tag'],
                        '%target_name%' => $tag_id,
                    ]);
                    $this->addErrors($message);
                }
            }
        }
    }

    /**
     * ??????????????????1?????????????????????2???null????????????????????????????????????
     *
     * @param $row
     * @param Product $Product
     * @param CsvImportService $data
     * @param $headerByKey
     * @param null $ClassCategory1
     * @param null $ClassCategory2
     *
     * @return ProductClass
     */
    protected function createProductClass($row, Product $Product, $data, $headerByKey, $ClassCategory1 = null, $ClassCategory2 = null)
    {
        // ????????????1???????????????2???null????????????????????????
        $ProductClass = new ProductClass();
        $ProductClass->setProduct($Product);
        $ProductClass->setVisible(true);

        $line = $data->key() + 1;
        //???????????????????????????ID????????????????????????(1)
        $row_const_sale_type = 1;
        $header_by_key_sale_type = '';
        $SaleType = $this->saleTypeRepository->find($row_const_sale_type);
        $ProductClass->setSaleType($SaleType);
        //.???????????????????????????ID????????????????????????(1)
        
        /* ???????????????????????????ID????????????????????????(?????????????????????)
        if (isset($row[$headerByKey['sale_type']]) && StringUtil::isNotBlank($row[$headerByKey['sale_type']])) {
            if (preg_match('/^\d+$/', $row[$headerByKey['sale_type']])) {
                $SaleType = $this->saleTypeRepository->find($row[$headerByKey['sale_type']]);
                if (!$SaleType) {
                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sale_type']]);
                    $this->addErrors($message);
                } else {
                    $ProductClass->setSaleType($SaleType);
                }
            } else {
                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sale_type']]);
                $this->addErrors($message);
            }
        } else {
            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['sale_type']]);
            $this->addErrors($message);
        }
        */
        
        $ProductClass->setClassCategory1($ClassCategory1);
        $ProductClass->setClassCategory2($ClassCategory2);

        if (isset($row[$headerByKey['delivery_date']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_date']])) {
            if (preg_match('/^\d+$/', $row[$headerByKey['delivery_date']])) {
                $DeliveryDuration = $this->deliveryDurationRepository->find($row[$headerByKey['delivery_date']]);
                if (!$DeliveryDuration) {
                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['delivery_date']]);
                    $this->addErrors($message);
                } else {
                    $ProductClass->setDeliveryDuration($DeliveryDuration);
                }
            } else {
                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['delivery_date']]);
                $this->addErrors($message);
            }
        }

        if (isset($row[$headerByKey['product_code']]) && StringUtil::isNotBlank($row[$headerByKey['product_code']])) {
            $ProductClass->setCode(StringUtil::trimAll($row[$headerByKey['product_code']]));
        } else {
            $ProductClass->setCode(null);
        }

        if (!isset($row[$headerByKey['stock_unlimited']])
            || StringUtil::isBlank($row[$headerByKey['stock_unlimited']])
            || $row[$headerByKey['stock_unlimited']] == (string) Constant::DISABLED
        ) {
            $ProductClass->setStockUnlimited(false);
            // ???????????????????????????????????????????????????
            if (isset($row[$headerByKey['stock']]) && StringUtil::isNotBlank($row[$headerByKey['stock']])) {
                $stock = str_replace(',', '', $row[$headerByKey['stock']]);
                if (preg_match('/^\d+$/', $stock) && $stock >= 0) {
                    $ProductClass->setStock($stock);
                } else {
                    $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['stock']]);
                    $this->addErrors($message);
                }
            } else {
                $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['stock']]);
                $this->addErrors($message);
            }
        } elseif ($row[$headerByKey['stock_unlimited']] == (string) Constant::ENABLED) {
            $ProductClass->setStockUnlimited(true);
            $ProductClass->setStock(null);
        } else {
            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['stock_unlimited']]);
            $this->addErrors($message);
        }

        if (isset($row[$headerByKey['sale_limit']]) && StringUtil::isNotBlank($row[$headerByKey['sale_limit']])) {
            $saleLimit = str_replace(',', '', $row[$headerByKey['sale_limit']]);
            if (preg_match('/^\d+$/', $saleLimit) && $saleLimit >= 0) {
                $ProductClass->setSaleLimit($saleLimit);
            } else {
                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['sale_limit']]);
                $this->addErrors($message);
            }
        }

        if (isset($row[$headerByKey['price01']]) && StringUtil::isNotBlank($row[$headerByKey['price01']])) {
            $price01 = str_replace(',', '', $row[$headerByKey['price01']]);
            $errors = $this->validator->validate($price01, new GreaterThanOrEqual(['value' => 0]));
            if ($errors->count() === 0) {
                $ProductClass->setPrice01($price01);
            } else {
                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['price01']]);
                $this->addErrors($message);
            }
        }

        if (isset($row[$headerByKey['price02']]) && StringUtil::isNotBlank($row[$headerByKey['price02']])) {
            $price02 = str_replace(',', '', $row[$headerByKey['price02']]);
            $errors = $this->validator->validate($price02, new GreaterThanOrEqual(['value' => 0]));
            if ($errors->count() === 0) {
                $ProductClass->setPrice02($price02);
            } else {
                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['price02']]);
                $this->addErrors($message);
            }
        } else {
            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['price02']]);
            $this->addErrors($message);
        }

        if ($this->BaseInfo->isOptionProductDeliveryFee()) {
            if (isset($row[$headerByKey['delivery_fee']]) && StringUtil::isNotBlank($row[$headerByKey['delivery_fee']])) {
                $delivery_fee = str_replace(',', '', $row[$headerByKey['delivery_fee']]);
                $errors = $this->validator->validate($delivery_fee, new GreaterThanOrEqual(['value' => 0]));
                if ($errors->count() === 0) {
                    $ProductClass->setDeliveryFee($delivery_fee);
                } else {
                    $message = trans('admin.common.csv_invalid_greater_than_zero',
                        ['%line%' => $line, '%name%' => $headerByKey['delivery_fee']]);
                    $this->addErrors($message);
                }
            }
        }
        
        //??????????????????????????????0?????????
        $ProductClass->setDeliveryDateDays(0);

        $Product->addProductClass($ProductClass);
        $ProductStock = new ProductStock();
        $ProductClass->setProductStock($ProductStock);
        $ProductStock->setProductClass($ProductClass);

        if (!$ProductClass->isStockUnlimited()) {
            $ProductStock->setStock($ProductClass->getStock());
        } else {
            // ?????????????????????null?????????
            $ProductStock->setStock(null);
        }

        $this->entityManager->persist($ProductClass);
        $this->entityManager->persist($ProductStock);

        return $ProductClass;
    }

    /**
     * ???????????????????????????
     *
     * @param $row
     * @param Product $Product
     * @param ProductClass $ProductClass
     * @param CsvImportService $data
     *
     * @return ProductClass
     */
    protected function updateProductClass($row, Product $Product, ProductClass $ProductClass, $data, $headerByKey)
    {
        $ProductClass->setProduct($Product);

        $line = $data->key() + 1;
        $row[$headerByKey['sale_type']] = 1;//????????????1
        if ($row[$headerByKey['sale_type']] == '') {
            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['sale_type']]);
            $this->addErrors($message);
        } else {
            if (preg_match('/^\d+$/', $row[$headerByKey['sale_type']])) {
                $SaleType = $this->saleTypeRepository->find($row[$headerByKey['sale_type']]);
                if (!$SaleType) {
                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['sale_type']]);
                    $this->addErrors($message);
                } else {
                    $ProductClass->setSaleType($SaleType);
                }
            } else {
                $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['sale_type']]);
                $this->addErrors($message);
            }
        }

        // ????????????1???2?????????????????????????????????
        if ($row[$headerByKey['class_category1']] != '') {
            if (preg_match('/^\d+$/', $row[$headerByKey['class_category1']])) {
                $ClassCategory = $this->classCategoryRepository->find($row[$headerByKey['class_category1']]);
                if (!$ClassCategory) {
                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                    $this->addErrors($message);
                } else {
                    $ProductClass->setClassCategory1($ClassCategory);
                }
            } else {
                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category1']]);
                $this->addErrors($message);
            }
        }

        /*??????2????????????
        if ($row[$headerByKey['class_category2']] != '') {
            if (preg_match('/^\d+$/', $row[$headerByKey['class_category2']])) {
                $ClassCategory = $this->classCategoryRepository->find($row[$headerByKey['class_category2']]);
                if (!$ClassCategory) {
                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                    $this->addErrors($message);
                } else {
                    $ProductClass->setClassCategory2($ClassCategory);
                }
            } else {
                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['class_category2']]);
                $this->addErrors($message);
            }
        }
        */

        if ($row[$headerByKey['delivery_date']] != '') {
            if (preg_match('/^\d+$/', $row[$headerByKey['delivery_date']])) {
                $DeliveryDuration = $this->deliveryDurationRepository->find($row[$headerByKey['delivery_date']]);
                if (!$DeliveryDuration) {
                    $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['delivery_date']]);
                    $this->addErrors($message);
                } else {
                    $ProductClass->setDeliveryDuration($DeliveryDuration);
                }
            } else {
                $message = trans('admin.common.csv_invalid_not_found', ['%line%' => $line, '%name%' => $headerByKey['delivery_date']]);
                $this->addErrors($message);
            }
        }

        if (StringUtil::isNotBlank($row[$headerByKey['product_code']])) {
            $ProductClass->setCode(StringUtil::trimAll($row[$headerByKey['product_code']]));
        } else {
            $ProductClass->setCode(null);
        }

        if (!isset($row[$headerByKey['stock_unlimited']])
            || StringUtil::isBlank($row[$headerByKey['stock_unlimited']])
            || $row[$headerByKey['stock_unlimited']] == (string) Constant::DISABLED
        ) {
            $ProductClass->setStockUnlimited(false);
            // ???????????????????????????????????????????????????
            if ($row[$headerByKey['stock']] == '') {
                $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['stock']]);
                $this->addErrors($message);
            } else {
                $stock = str_replace(',', '', $row[$headerByKey['stock']]);
                if (preg_match('/^\d+$/', $stock) && $stock >= 0) {
                    $ProductClass->setStock($row[$headerByKey['stock']]);
                } else {
                    $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['stock']]);
                    $this->addErrors($message);
                }
            }
        } elseif ($row[$headerByKey['stock_unlimited']] == (string) Constant::ENABLED) {
            $ProductClass->setStockUnlimited(true);
            $ProductClass->setStock(null);
        } else {
            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['stock_unlimited']]);
            $this->addErrors($message);
        }

        if ($row[$headerByKey['sale_limit']] != '') {
            $saleLimit = str_replace(',', '', $row[$headerByKey['sale_limit']]);
            if (preg_match('/^\d+$/', $saleLimit) && $saleLimit >= 0) {
                $ProductClass->setSaleLimit($saleLimit);
            } else {
                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['sale_limit']]);
                $this->addErrors($message);
            }
        }

        if ($row[$headerByKey['price01']] != '') {
            $price01 = str_replace(',', '', $row[$headerByKey['price01']]);
            $errors = $this->validator->validate($price01, new GreaterThanOrEqual(['value' => 0]));
            if ($errors->count() === 0) {
                $ProductClass->setPrice01($price01);
            } else {
                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['price01']]);
                $this->addErrors($message);
            }
        }

        if ($row[$headerByKey['price02']] == '') {
            $message = trans('admin.common.csv_invalid_required', ['%line%' => $line, '%name%' => $headerByKey['price02']]);
            $this->addErrors($message);
        } else {
            $price02 = str_replace(',', '', $row[$headerByKey['price02']]);
            $errors = $this->validator->validate($price02, new GreaterThanOrEqual(['value' => 0]));
            if ($errors->count() === 0) {
                $ProductClass->setPrice02($price02);
            } else {
                $message = trans('admin.common.csv_invalid_greater_than_zero', ['%line%' => $line, '%name%' => $headerByKey['price02']]);
                $this->addErrors($message);
            }
        }

        $ProductStock = $ProductClass->getProductStock();

        if (!$ProductClass->isStockUnlimited()) {
            $ProductStock->setStock($ProductClass->getStock());
        } else {
            // ?????????????????????null?????????
            $ProductStock->setStock(null);
        }

        return $ProductClass;
    }

    /**
     * ????????????
     * ???????????????????????????????????????
     *
     * @param $row
     * @param Product $Product
     * @param CsvImportService $data
     * @param $headerByKey
     */
    protected function createProductOption($row, Product $Product, $data, $headerByKey)
    {
        if (!isset($row[$headerByKey['product_option']])) {
            return;
        }
        // ????????????????????????(ClassName)
        $ProductOptions = $Product->getProductClassNames();
        foreach ($ProductOptions as $ProductOption) {
            $Product->removeProductClassName($ProductOption);
            $this->entityManager->remove($ProductOption);
            $this->entityManager->flush();
        }
        // ???????????????(???)?????????(ClassCategory)
        $ProductChildOptions = $Product->getProductClassCategories();
        foreach ($ProductChildOptions as $ProductChildOption) {
            $Product->removeProductClassCategory($ProductChildOption);
            $this->entityManager->remove($ProductChildOption);
            $this->entityManager->flush();
        }

        if (StringUtil::isNotBlank($row[$headerByKey['product_option']])) {
            // ????????????????????????
            $options = explode(',', $row[$headerByKey['product_option']]);
            $sortNo = 1;
            $optionsIdList = [];
            foreach ($options as $option) {
                $line = $data->key() + 1;
                if (preg_match('/^\d+$/', $option)) {
                    $Option = $this->classNameRepository->find($option);
                    if (!$Option) {
                        $message = trans('admin.common.csv_invalid_not_found_target', [
                            '%line%' => $line,
                            '%name%' => $headerByKey['product_option'],
                            '%target_name%' => $option,
                        ]);
                        $this->addErrors($message);
                    } else {
                        /* ??????????????????????????????
                        foreach ($Option->getPath() as $ParentOption) {
                            if (!isset($optionsIdList[$ParentOption->getId()])) {
                                $ProductOption = $this->makeProductClassName($Product, $ParentOption, $sortNo);
                                $this->entityManager->persist($ProductOption);
                                $sortNo++;

                                $Product->addProductClassName($ProductOption);
                                $optionsIdList[$ParentOption->getId()] = true;
                            }
                        }
                        */
                        //??????????????????????????????(ClassName)
                        if (!isset($optionsIdList[$Option->getId()])) {
                            $ProductOption = $this->makeProductClassName($Product, $Option, $sortNo);
                            $sortNo++;
                            $this->entityManager->persist($ProductOption);
                            $Product->addProductClassName($ProductOption);
                            $optionsIdList[$Option->getId()] = true;
                            
                            //???????????????(???)???????????????(ClassCategory)
							$ChildOptions = $Option->getClassCategories();
							// ???????????????(???)???????????????????????????(???)???????????????
							foreach ($ChildOptions as $ChildOption) {
								$ProductChildOption = $this->makeProductClassCategory($Product, $ChildOption);
							    $this->entityManager->persist($ProductChildOption);
							    $Product->addProductClassCategory($ProductChildOption);
							}
                        }
                    }
                } else {
                    $message = trans('admin.common.csv_invalid_not_found_target', [
                        '%line%' => $line,
                        '%name%' => $headerByKey['product_option'],
                        '%target_name%' => $option,
                    ]);
                    $this->addErrors($message);
                }
            }
        }
    }
    
    /**
     * ????????????
     * ??????????????????????????????
     *
     * @param $row
     * @param Product $Product
     * @param CsvImportService $data
     * @param $headerByKey
     */
    protected function createRelatedProduct($row, Product $Product, $data, $headerByKey)
    {
        if (!isset($row[$headerByKey['related_product']])) {
            return;
        }
        // ?????????????????????
        $RelatedProducts = $Product->getRelatedProducts();
        foreach ($RelatedProducts as $RelatedProduct) {
            $Product->removeRelatedProduct($RelatedProduct);
            $this->entityManager->remove($RelatedProduct);
            $this->entityManager->flush();
        }

        if (StringUtil::isNotBlank($row[$headerByKey['related_product']])) {
            // ?????????????????????
            $relateds = explode(',', $row[$headerByKey['related_product']]);
            $sortNo = 1;
            $relatedsIdList = [];
            foreach ($relateds as $related) {
                $line = $data->key() + 1;
                if (preg_match('/^\d+$/', $related)) {
                    $Related = $this->productRepository->find($related);
                    if (!$Related) {
                        $message = trans('admin.common.csv_invalid_not_found_target', [
                            '%line%' => $line,
                            '%name%' => $headerByKey['related_product'],
                            '%target_name%' => $related,
                        ]);
                        $this->addErrors($message);
                    } else {
                        if (!isset($relatedsIdList[$Related->getId()])) {
                            $RelatedProduct = $this->makeRelatedProduct($Product, $Related, $sortNo);
                            $sortNo++;
                            $this->entityManager->persist($RelatedProduct);
                            $Product->addRelatedProduct($RelatedProduct);
                            $relatedsIdList[$Related->getId()] = true;
                        }
                    }
                } else {
                    $message = trans('admin.common.csv_invalid_not_found_target', [
                        '%line%' => $line,
                        '%name%' => $headerByKey['related_product'],
                        '%target_name%' => $related,
                    ]);
                    $this->addErrors($message);
                }
            }
        }
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
    protected function getProductCsvHeader()
    {
        return [
            trans('admin.product.product_csv.product_id_col') => [
                'id' => 'id',
                'description' => 'admin.product.product_csv.product_id_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.display_status_col') => [
                'id' => 'status',
                'description' => 'admin.product.product_csv.display_status_description',
                'required' => true,
            ],
            trans('admin.product.product_csv.product_name_col') => [
                'id' => 'name',
                'description' => 'admin.product.product_csv.product_name_description',
                'required' => true,
            ],
            trans('admin.product.product_csv.shop_memo_col') => [
                'id' => 'note',
                'description' => 'admin.product.product_csv.shop_memo_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.description_list_col') => [
                'id' => 'description_list',
                'description' => 'admin.product.product_csv.description_list_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.description_detail_col') => [
                'id' => 'description_detail',
                'description' => 'admin.product.product_csv.description_detail_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.keyword_col') => [
                'id' => 'search_word',
                'description' => 'admin.product.product_csv.keyword_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.free_area_col') => [
                'id' => 'free_area',
                'description' => 'admin.product.product_csv.free_area_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.delete_flag_col') => [
                'id' => 'product_del_flg',
                'description' => 'admin.product.product_csv.delete_flag_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.product_image_col') => [
                'id' => 'product_image',
                'description' => 'admin.product.product_csv.product_image_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.category_col') => [
                'id' => 'product_category',
                'description' => 'admin.product.product_csv.category_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.tag_col') => [
                'id' => 'product_tag',
                'description' => 'admin.product.product_csv.tag_description',
                'required' => false,
            ],
            /* ????????????????????????
            trans('admin.product.product_csv.sale_type_col') => [
                'id' => 'sale_type',
                'description' => 'admin.product.product_csv.sale_type_description',
                'required' => true,
            ],
            */
            trans('admin.product.product_csv.class_category1_col') => [
                'id' => 'class_category1',
                'description' => 'admin.product.product_csv.class_category1_description',
                'required' => false,
            ],
            /* ????????????2(ID)????????????
            trans('admin.product.product_csv.class_category2_col') => [
                'id' => 'class_category2',
                'description' => 'admin.product.product_csv.class_category2_description',
                'required' => false,
            ],
            */
            trans('admin.product.product_csv.delivery_duration_col') => [
                'id' => 'delivery_date',
                'description' => 'admin.product.product_csv.delivery_duration_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.product_code_col') => [
                'id' => 'product_code',
                'description' => 'admin.product.product_csv.product_code_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.stock_col') => [
                'id' => 'stock',
                'description' => 'admin.product.product_csv.stock_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.stock_unlimited_col') => [
                'id' => 'stock_unlimited',
                'description' => 'admin.product.product_csv.stock_unlimited_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.sale_limit_col') => [
                'id' => 'sale_limit',
                'description' => 'admin.product.product_csv.sale_limit_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.normal_price_col') => [
                'id' => 'price01',
                'description' => 'admin.product.product_csv.normal_price_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.sale_price_col') => [
                'id' => 'price02',
                'description' => 'admin.product.product_csv.sale_price_description',
                'required' => true,
            ],
            trans('admin.product.product_csv.delivery_fee_col') => [
                'id' => 'delivery_fee',
                'description' => 'admin.product.product_csv.delivery_fee_description',
                'required' => false,
            ],
            trans('admin.product.product_csv.tax_rate_col') => [
                'id' => 'tax_rate',
                'description' => 'admin.product.product_csv.tax_rate_description',
                'required' => false,
            ],
            //???????????????????????????(???????????????)
            trans('admin.product.product_csv.sales_period_from_col') => [
                'id' => 'sales_period_from',
                'description' => 'admin.product.product_csv.sales_period_from_description',
                'required' => false,
            ],  
            trans('admin.product.product_csv.sales_period_to_col') => [
                'id' => 'sales_period_to',
                'description' => 'admin.product.product_csv.sales_period_to_description',
                'required' => false,
            ],
            //???????????????????????????(ID)  ??????
            trans('admin.product.product_csv.sales_type_col') => [
                'id' => 'sales_type',
                'description' => 'admin.product.product_csv.sales_type__description',
                'required' => true,
            ],
            //??????????????????????????????(ID)
            /*
            trans('admin.product.product_csv.product_option_col') => [
                'id' => 'product_option',
                'description' => 'admin.product.product_csv.product_option_description',
                'required' => false,
            ],
            */
            //????????????????????????
            trans('admin.product.product_csv.capacity_col') => [
                'id' => 'capacity',
                'description' => 'admin.product.product_csv.capacity_description',
                'required' => false,
            ],
            //???????????????????????????
            trans('admin.product.product_csv.expiry_date_col') => [
                'id' => 'expiry_date',
                'description' => 'admin.product.product_csv.expiry_date_description',
                'required' => false,
            ],
            //???????????????????????????
            trans('admin.product.product_csv.expiration_date_col') => [
                'id' => 'expiration_date',
                'description' => 'admin.product.product_csv.expiration_date_description',
                'required' => false,
            ],
            //????????????????????????
            trans('admin.product.product_csv.material_col') => [
                'id' => 'material',
                'description' => 'admin.product.product_csv.material_description',
                'required' => false,
            ],
            //????????????????????????????????????
            trans('admin.product.product_csv.please_read_col') => [
                'id' => 'please_read',
                'description' => 'admin.product.product_csv.please_read_description',
                'required' => false,
            ],
            //???????????????????????????(ID)
            trans('admin.product.product_csv.related_product_col') => [
                'id' => 'related_product',
                'description' => 'admin.product.product_csv.related_product_description',
                'required' => false,
            ],
            /* ????????????
            */
        ];
    }

    /**
     * ????????????CSV??????????????????
     */
    protected function getCategoryCsvHeader()
    {
        return [
            trans('admin.product.category_csv.category_id_col') => [
                'id' => 'id',
                'description' => 'admin.product.category_csv.category_id_description',
                'required' => false,
            ],
            trans('admin.product.category_csv.category_name_col') => [
                'id' => 'category_name',
                'description' => 'admin.product.category_csv.category_name_description',
                'required' => true,
            ],
            trans('admin.product.category_csv.parent_category_id_col') => [
                'id' => 'parent_category_id',
                'description' => 'admin.product.category_csv.parent_category_id_description',
                'required' => false,
            ],
            trans('admin.product.category_csv.delete_flag_col') => [
                'id' => 'category_del_flg',
                'description' => 'admin.product.category_csv.delete_flag_description',
                'required' => false,
            ],
        ];
    }

    /**
     * ProductCategory??????
     *
     * @param \Eccube\Entity\Product $Product
     * @param \Eccube\Entity\Category $Category
     * @param int $sortNo
     *
     * @return ProductCategory
     */
    private function makeProductCategory($Product, $Category, $sortNo)
    {
        $ProductCategory = new ProductCategory();
        $ProductCategory->setProduct($Product);
        $ProductCategory->setProductId($Product->getId());
        $ProductCategory->setCategory($Category);
        $ProductCategory->setCategoryId($Category->getId());

        return $ProductCategory;
    }
    
    
    /**
     * ??????????????????????????????
     * ProductClassName??????
     *
     * @param \Eccube\Entity\Product $Product
     * @param \Eccube\Entity\ClassName $ClassName
     * @param int $sortNo
     *
     * @return ProductClassName
     */
    private function makeProductClassName($Product, $ClassName, $sortNo)
    {
        $ProductClassName = new ProductClassName();
        $ProductClassName->setProduct($Product);
        $ProductClassName->setProductId($Product->getId());
        $ProductClassName->setClassName($ClassName);
        $ProductClassName->setClassNameId($ClassName->getId());

        return $ProductClassName;
    }
    
    /**
     * ??????????????????????????????(???)(???????????????)
     * ProductClassCategory??????
     *
     * @param \Eccube\Entity\Product $Product
     * @param \Eccube\Entity\ClassCategory $ClassCategory
     * @param int $sortNo
     *
     * @return ProductClassCategory
     */
    private function makeProductClassCategory($Product, $ClassCategory)
    {
        $ProductClassCategory = new ProductClassCategory();
        $ProductClassCategory->setProduct($Product);
        $ProductClassCategory->setProductId($Product->getId());
        $ProductClassCategory->setClassCategory($ClassCategory);
        $ProductClassCategory->setClassCategoryId($ClassCategory->getId());

        return $ProductClassCategory;
    }
    
    /**
     * ???????????????????????????
     * RelatedProduct??????
     *
     * @param \Eccube\Entity\Product $Product
     * @param \Eccube\Entity\Product $ChildProduct
     * @param int $sortNo
     *
     * @return RelatedProduct
     */
    private function makeRelatedProduct($Product, $ChildProduct, $sortNo)
    {
        $RelatedProduct = new RelatedProduct();
        $RelatedProduct->setProduct($Product);
        $RelatedProduct->setChildProduct($ChildProduct);

        return $RelatedProduct;
    }
}
