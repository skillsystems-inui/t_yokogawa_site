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

namespace Eccube\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


    /**
     * Product
     *
     * @ORM\Table(name="dtb_product")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ProductRepository")
     */
    class Product extends \Eccube\Entity\AbstractEntity
    {
    use \Plugin\RelatedProduct4\Entity\ProductTrait;

        private $_calc = false;
        private $stockFinds = [];
        private $stocks = [];
        private $stockUnlimiteds = [];
        private $price01 = [];
        private $price02 = [];
        private $price01IncTaxs = [];
        private $price02IncTaxs = [];
        private $codes = [];
        private $classCategories1 = [];
        private $classCategories2 = [];
        private $className1;
        private $className2;
        
        //options
        private $optionCategories1 = [];
        private $optionCategories2 = [];
        private $optionCategories3 = [];
        private $optionCategories4 = [];
        private $optionCategories5 = [];
        private $optionCategories6 = [];
        private $optionCategories7 = [];
        private $optionCategories8 = [];
        private $optionCategories9 = [];
        private $optionCategories10 = [];
        private $optionCategories11 = [];
        private $optionCategories12 = [];
        private $optionCategories13 = [];
        private $optionCategories14 = [];
        private $optionCategories15 = [];
        private $optionCategories16 = [];
        private $optionCategories17 = [];
        private $optionCategories18 = [];
        private $optionCategories19 = [];
        private $optionCategories20 = [];
        private $optionName1;
        private $optionName2;
        private $optionName3;
        private $optionName4;
        private $optionName5;
        private $optionName6;
        private $optionName7;
        private $optionName8;
        private $optionName9;
        private $optionName10;
        private $optionName11;
        private $optionName12;
        private $optionName13;
        private $optionName14;
        private $optionName15;
        private $optionName16;
        private $optionName17;
        private $optionName18;
        private $optionName19;
        private $optionName20;

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getName();
        }

        public function _calc()
        {
            if (!$this->_calc) {
                $i = 0;
                foreach ($this->getProductClasses() as $ProductClass) {
                    /* @var $ProductClass \Eccube\Entity\ProductClass */
                    // stock_find
                    if ($ProductClass->isVisible() == false) {
                        continue;
                    }
                    $ClassCategory1 = $ProductClass->getClassCategory1();
                    $ClassCategory2 = $ProductClass->getClassCategory2();
                    if ($ClassCategory1 && !$ClassCategory1->isVisible()) {
                        continue;
                    }
                    if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
                        continue;
                    }

                    // stock_find
                    $this->stockFinds[] = $ProductClass->getStockFind();

                    // stock
                    $this->stocks[] = $ProductClass->getStock();

                    // stock_unlimited
                    $this->stockUnlimiteds[] = $ProductClass->isStockUnlimited();

                    // price01
                    if (!is_null($ProductClass->getPrice01())) {
                        $this->price01[] = $ProductClass->getPrice01();
                        // price01IncTax
                        $this->price01IncTaxs[] = $ProductClass->getPrice01IncTax();
                    }

                    // price02
                    $this->price02[] = $ProductClass->getPrice02();

                    // price02IncTax
                    $this->price02IncTaxs[] = $ProductClass->getPrice02IncTax();

                    // product_code
                    $this->codes[] = $ProductClass->getCode();

                    if ($i === 0) {
                        if ($ProductClass->getClassCategory1() && $ProductClass->getClassCategory1()->getId()) {
                            $this->className1 = $ProductClass->getClassCategory1()->getClassName()->getName();
                        }
                        if ($ProductClass->getClassCategory2() && $ProductClass->getClassCategory2()->getId()) {
                            $this->className2 = $ProductClass->getClassCategory2()->getClassName()->getName();
                        }
                    }
                    if ($ProductClass->getClassCategory1()) {
                        $classCategoryId1 = $ProductClass->getClassCategory1()->getId();
                        if (!empty($classCategoryId1)) {
                            if ($ProductClass->getClassCategory2()) {
                                $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName();
                                $this->classCategories2[$ProductClass->getClassCategory1()->getId()][$ProductClass->getClassCategory2()->getId()] = $ProductClass->getClassCategory2()->getName();
                            } else {
                                $visual_name =' '.$ProductClass->getVisualname();
                                //$this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName().($ProductClass->getStockFind() ? '' : trans('front.product.out_of_stock_label'));
                                $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName().($ProductClass->getStockFind() ? '' : trans('front.product.out_of_stock_label')).$visual_name;
                            }
                        }
                    }
                    $i++;
                }
                $this->_calc = true;
            }
        }
        
        
        public function set_options()
        {
        	// オプション内容セット用メソッド
            //登録されたオプション数
            $option_cnt = 0;
            //同一オプションかどうか判定用変数
            $bak_option_name = null;
            foreach ($this->getProductClassCategories() as $ProductClassCategory) {
            	/* @var $ProductClassCategory \Eccube\Entity\ProductClassCategory */
                
                //オプションID
				$ClassNameId = $ProductClassCategory->getClassCategoryId();
				$ClassName = $ProductClassCategory->getClassCategory()->getClassName();
				$OptionName = $ClassName->getName();
				
				//オプション別カウンタ
				if(!empty($OptionName) && ($bak_option_name != $OptionName)){
					$option_cnt++;
					$bak_option_name = $OptionName;
                }
				
				//オプション名とオプション選択肢セット
				if ($ClassNameId) {
                    $this->set_option_name_category($ProductClassCategory, $ClassNameId, $OptionName, $option_cnt);
                }
            }
        }
        
        
        public function set_option_name_category($ProductClassCategory, $ClassNameId, $OptionName, $option_no)
        {
        	// オプション名と選択肢セット用メソッド
        	switch ($option_no) {
                case 1:
                    $this->optionName1 = $OptionName;
                    $this->optionCategories1[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 2:
                    $this->optionName2 = $OptionName;
                    $this->optionCategories2[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 3:
                    $this->optionName3 = $OptionName;
                    $this->optionCategories3[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 4:
                    $this->optionName4 = $OptionName;
                    $this->optionCategories4[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 5:
                    $this->optionName5 = $OptionName;
                    $this->optionCategories5[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 6:
                    $this->optionName6 = $OptionName;
                    $this->optionCategories6[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 7:
                    $this->optionName7 = $OptionName;
                    $this->optionCategories7[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 8:
                    $this->optionName8 = $OptionName;
                    $this->optionCategories8[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 9:
                    $this->optionName9 = $OptionName;
                    $this->optionCategories9[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 10:
                    $this->optionName10 = $OptionName;
                    $this->optionCategories10[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 11:
                    $this->optionName11 = $OptionName;
                    $this->optionCategories11[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 12:
                    $this->optionName12 = $OptionName;
                    $this->optionCategories12[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 13:
                    $this->optionName13 = $OptionName;
                    $this->optionCategories13[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 14:
                    $this->optionName14 = $OptionName;
                    $this->optionCategories14[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 15:
                    $this->optionName15 = $OptionName;
                    $this->optionCategories15[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 16:
                    $this->optionName16 = $OptionName;
                    $this->optionCategories16[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 17:
                    $this->optionName17 = $OptionName;
                    $this->optionCategories17[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 18:
                    $this->optionName18 = $OptionName;
                    $this->optionCategories18[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 19:
                    $this->optionName19 = $OptionName;
                    $this->optionCategories19[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                case 20:
                    $this->optionName20 = $OptionName;
                    $this->optionCategories20[$ClassNameId] = $ProductClassCategory->getClassCategory()->getName();
                    break;
                default:
                    break;
            }
        }

        /**
         * Is Enable
         *
         * @return bool
         *
         * @deprecated
         */
        public function isEnable()
        {
            return $this->getStatus()->getId() === \Eccube\Entity\Master\ProductStatus::DISPLAY_SHOW ? true : false;
        }

        /**
         * Get ClassName1
         *
         * @return string
         */
        public function getClassName1()
        {
            $this->_calc();

            return $this->className1;
        }

        /**
         * Get ClassName2
         *
         * @return string
         */
        public function getClassName2()
        {
            $this->_calc();

            return $this->className2;
        }

        /**
         * Get getClassCategories1
         *
         * @return array
         */
        public function getClassCategories1()
        {
            $this->_calc();

            return $this->classCategories1;
        }

        public function getClassCategories1AsFlip()
        {
            return array_flip($this->getClassCategories1());
        }

        /**
         * Get getClassCategories2
         *
         * @return array
         */
        public function getClassCategories2($class_category1)
        {
            $this->_calc();

            return isset($this->classCategories2[$class_category1]) ? $this->classCategories2[$class_category1] : [];
        }

        public function getClassCategories2AsFlip($class_category1)
        {
            return array_flip($this->getClassCategories2($class_category1));
        }
        
        
        /**
         * Get OptionName1
         *
         * @return string
         */
        public function getOptionName1()
        {
            return $this->optionName1;
        }
        public function getOptionCategories1AsFlip()
        {
            return array_flip($this->optionCategories1);
        }

        /**
         * Get OptionName2
         *
         * @return string
         */
        public function getOptionName2()
        {
            return $this->optionName2;
        }
        public function getOptionCategories2AsFlip()
        {
            return array_flip($this->optionCategories2);
        }
        
        /**
         * Get OptionName3
         *
         * @return string
         */
        public function getOptionName3()
        {
            return $this->optionName3;
        }
        public function getOptionCategories3AsFlip()
        {
            return array_flip($this->optionCategories3);
        }
                
        
        /**
         * Get OptionName4
         *
         * @return string
         */
        public function getOptionName4()
        {
            return $this->optionName4;
        }
        public function getOptionCategories4AsFlip()
        {
            return array_flip($this->optionCategories4);
        }
 
        /**
         * Get OptionName5
         *
         * @return string
         */
        public function getOptionName5()
        {
            return $this->optionName5;
        }
        public function getOptionCategories5AsFlip()
        {
            return array_flip($this->optionCategories5);
        }

        /**
         * Get OptionName6
         *
         * @return string
         */
        public function getOptionName6()
        {
            return $this->optionName6;
        }
        public function getOptionCategories6AsFlip()
        {
            return array_flip($this->optionCategories6);
        }
        
        /**
         * Get OptionName7
         *
         * @return string
         */
        public function getOptionName7()
        {
            return $this->optionName7;
        }
        public function getOptionCategories7AsFlip()
        {
            return array_flip($this->optionCategories7);
        }
        
        
        /**
         * Get OptionName8
         *
         * @return string
         */
        public function getOptionName8()
        {
            return $this->optionName8;
        }
        public function getOptionCategories8AsFlip()
        {
            return array_flip($this->optionCategories8);
        }
        
        
        /**
         * Get OptionName9
         *
         * @return string
         */
        public function getOptionName9()
        {
            return $this->optionName9;
        }
        public function getOptionCategories9AsFlip()
        {
            return array_flip($this->optionCategories9);
        }
        
        
        /**
         * Get OptionName10
         *
         * @return string
         */
        public function getOptionName10()
        {
            return $this->optionName10;
        }
        public function getOptionCategories10AsFlip()
        {
            return array_flip($this->optionCategories10);
        }
        
        
        /**
         * Get OptionName11
         *
         * @return string
         */
        public function getOptionName11()
        {
            return $this->OptionName11;
        }
        public function getOptionCategories11AsFlip()
        {
            return array_flip($this->optionCategories11);
        }

        /**
         * Get OptionName12
         *
         * @return string
         */
        public function getOptionName12()
        {
            return $this->OptionName12;
        }
        public function getOptionCategories12AsFlip()
        {
            return array_flip($this->optionCategories12);
        }
        
        /**
         * Get OptionName13
         *
         * @return string
         */
        public function getOptionName13()
        {
            return $this->OptionName13;
        }
        public function getOptionCategories13AsFlip()
        {
            return array_flip($this->optionCategories13);
        }
                
        
        /**
         * Get OptionName14
         *
         * @return string
         */
        public function getOptionName14()
        {
            return $this->OptionName14;
        }
        public function getOptionCategories14AsFlip()
        {
            return array_flip($this->optionCategories14);
        }
 
        /**
         * Get OptionName15
         *
         * @return string
         */
        public function getOptionName15()
        {
            return $this->OptionName15;
        }
        public function getOptionCategories15AsFlip()
        {
            return array_flip($this->optionCategories15);
        }

        /**
         * Get OptionName16
         *
         * @return string
         */
        public function getOptionName16()
        {
            return $this->OptionName16;
        }
        public function getOptionCategories16AsFlip()
        {
            return array_flip($this->optionCategories16);
        }
        
        /**
         * Get OptionName17
         *
         * @return string
         */
        public function getOptionName17()
        {
            return $this->OptionName17;
        }
        public function getOptionCategories17AsFlip()
        {
            return array_flip($this->optionCategories17);
        }
        
        
        /**
         * Get OptionName18
         *
         * @return string
         */
        public function getOptionName18()
        {
            return $this->OptionName18;
        }
        public function getOptionCategories18AsFlip()
        {
            return array_flip($this->optionCategories18);
        }
        
        
        /**
         * Get OptionName19
         *
         * @return string
         */
        public function getOptionName19()
        {
            return $this->OptionName19;
        }
        public function getOptionCategories19AsFlip()
        {
            return array_flip($this->optionCategories19);
        }
        
        
        /**
         * Get OptionName20
         *
         * @return string
         */
        public function getOptionName20()
        {
            return $this->optionName20;
        }
        public function getOptionCategories20AsFlip()
        {
            return array_flip($this->optionCategories20);
        }
        
        
        /**
         * Get StockFind
         *
         * @return bool
         */
        public function getStockFind()
        {
            $this->_calc();

            return max($this->stockFinds);
        }

        /**
         * Get Stock min
         *
         * @return integer
         */
        public function getStockMin()
        {
            $this->_calc();

            return min($this->stocks);
        }

        /**
         * Get Stock max
         *
         * @return integer
         */
        public function getStockMax()
        {
            $this->_calc();

            return max($this->stocks);
        }

        /**
         * Get StockUnlimited min
         *
         * @return integer
         */
        public function getStockUnlimitedMin()
        {
            $this->_calc();

            return min($this->stockUnlimiteds);
        }

        /**
         * Get StockUnlimited max
         *
         * @return integer
         */
        public function getStockUnlimitedMax()
        {
            $this->_calc();

            return max($this->stockUnlimiteds);
        }

        /**
         * Get Price01 min
         *
         * @return integer
         */
        public function getPrice01Min()
        {
            $this->_calc();

            if (count($this->price01) == 0) {
                return null;
            }

            return min($this->price01);
        }

        /**
         * Get Price01 max
         *
         * @return integer
         */
        public function getPrice01Max()
        {
            $this->_calc();

            if (count($this->price01) == 0) {
                return null;
            }

            return max($this->price01);
        }

        /**
         * Get Price02 min
         *
         * @return integer
         */
        public function getPrice02Min()
        {
            $this->_calc();

            return min($this->price02);
        }

        /**
         * Get Price02 max
         *
         * @return integer
         */
        public function getPrice02Max()
        {
            $this->_calc();

            return max($this->price02);
        }

        /**
         * Get Price01IncTax min
         *
         * @return integer
         */
        public function getPrice01IncTaxMin()
        {
            $this->_calc();

            return min($this->price01IncTaxs);
        }

        /**
         * Get Price01IncTax max
         *
         * @return integer
         */
        public function getPrice01IncTaxMax()
        {
            $this->_calc();

            return max($this->price01IncTaxs);
        }

        /**
         * Get Price02IncTax min
         *
         * @return integer
         */
        public function getPrice02IncTaxMin()
        {
            $this->_calc();

            return min($this->price02IncTaxs);
        }

        /**
         * Get Price02IncTax max
         *
         * @return integer
         */
        public function getPrice02IncTaxMax()
        {
            $this->_calc();

            return max($this->price02IncTaxs);
        }

        /**
         * Get Product_code min
         *
         * @return integer
         */
        public function getCodeMin()
        {
            $this->_calc();

            $codes = [];
            foreach ($this->codes as $code) {
                if (!is_null($code)) {
                    $codes[] = $code;
                }
            }

            return count($codes) ? min($codes) : null;
        }

        /**
         * Get Product_code max
         *
         * @return integer
         */
        public function getCodeMax()
        {
            $this->_calc();

            $codes = [];
            foreach ($this->codes as $code) {
                if (!is_null($code)) {
                    $codes[] = $code;
                }
            }

            return count($codes) ? max($codes) : null;
        }

        public function getMainListImage()
        {
            $ProductImages = $this->getProductImage();

            return empty($ProductImages) ? null : $ProductImages[0];
        }

        public function getMainFileName()
        {
            if (count($this->ProductImage) > 0) {
                return $this->ProductImage[0];
            } else {
                return null;
            }
        }

        public function hasProductClass()
        
        {
            foreach ($this->ProductClasses as $ProductClass) {
                if (!$ProductClass->isVisible()) {
                    continue;
                }
                if (!is_null($ProductClass->getClassCategory1())) {
                    return true;
                }
            }

            return false;
        }
        
        
        public function hasClassCategory()
        {
            //var_dump($this->ClassCategories);
            //var_dump($this->ProductClasses);
            
            /*
            if (!empty($this->ClassCategories)) {
	            foreach ($this->ClassCategories as $ClassCategory) {
	                if (!is_null($ClassCategory->getId())) {
	                    return true;
	                }
	                return true;
	            }
            }
            
            return false;
            
            */
            
            //$this->ClassCategoriesがループできないのかエラーになる　とりあえず全部trueにする
            return true;
        }
        

        //オプション取得
        public function hasProductClassSub()
        {
            foreach ($this->ProductClassSubs as $ProductClassSub) {
                if (!$ProductClassSub->isVisible()) {
                    continue;
                }
                if (!is_null($ProductClassSub->getClassSubCategory())) {
                    return true;
                }
            }

            return false;
        }

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         */
        private $name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="note", type="string", length=4000, nullable=true)
         */
        private $note;

        /**
         * @var string|null
         *
         * @ORM\Column(name="description_list", type="string", length=4000, nullable=true)
         */
        private $description_list;

        /**
         * @var string|null
         *
         * @ORM\Column(name="description_detail", type="string", length=4000, nullable=true)
         */
        private $description_detail;

        /**
         * @var string|null
         *
         * @ORM\Column(name="search_word", type="string", length=4000, nullable=true)
         */
        private $search_word;

        /**
         * @var string|null
         *
         * @ORM\Column(name="free_area", type="text", nullable=true)
         */
        private $free_area;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductCategory", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductCategories;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductClassCategory", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductClassCategories;
        
        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductClassName", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductClassNames;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductClass", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductClasses;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductClassSub", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductClassSubs;
        
        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ClassCategory", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ClassCategories;
        
        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductImage", mappedBy="Product", cascade={"remove"})
         * @ORM\OrderBy({
         *     "sort_no"="ASC"
         * })
         */
        private $ProductImage;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductTag", mappedBy="Product", cascade={"remove"})
         */
        private $ProductTag;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\CustomerFavoriteProduct", mappedBy="Product")
         */
        private $CustomerFavoriteProducts;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;

        /**
         * @var \Eccube\Entity\Master\ProductStatus
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\ProductStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_status_id", referencedColumnName="id")
         * })
         */
        private $Status;
        
        
        /**
         * @var integer|null
         *
         * @ORM\Column(name="option_id1", type="integer", options={"unsigned":true}, nullable=true)
         */
        private $option_id1;
        
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="sales_period_from", type="datetimetz", nullable=true)
         */
        private $sales_period_from;
        
        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="sales_period_to", type="datetimetz", nullable=true)
         */
        private $sales_period_to;
        
        /**
         * @var \Eccube\Entity\Master\SalesType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\SalesType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="sales_type", referencedColumnName="id")
         * })
         */
        private $Salestype;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="expiry_date", type="string", length=510, nullable=true)
         */
        private $expiry_date;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="expiration_date", type="string", length=510, nullable=true)
         */
        private $expiration_date;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="please_read", type="text", nullable=true)
         */
        private $please_read;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="capacity", type="string", length=510, nullable=true)
         */
        private $capacity;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="material", type="string", length=510, nullable=true)
         */
        private $material;
        
        
        
        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ProductCategories = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductClassCategories = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductClassNames = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductClasses = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductClassSubs = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductImage = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductTag = new \Doctrine\Common\Collections\ArrayCollection();
            $this->CustomerFavoriteProducts = new \Doctrine\Common\Collections\ArrayCollection();
            
            $this->ClassCategories = new \Doctrine\Common\Collections\ArrayCollection();
            
        }

        public function __clone()
        {
            $this->id = null;
        }

        public function copy()
        {
            // コピー対象外
            $this->CustomerFavoriteProducts = new ArrayCollection();

            $Categories = $this->getProductCategories();
            $this->ProductCategories = new ArrayCollection();
            foreach ($Categories as $Category) {
                $CopyCategory = clone $Category;
                $this->addProductCategory($CopyCategory);
                $CopyCategory->setProduct($this);
            }

            $Classes = $this->getProductClasses();
            $this->ProductClasses = new ArrayCollection();
            foreach ($Classes as $Class) {
                $CopyClass = clone $Class;
                $this->addProductClass($CopyClass);
                $CopyClass->setProduct($this);
            }

            $Images = $this->getProductImage();
            $this->ProductImage = new ArrayCollection();
            foreach ($Images as $Image) {
                $CloneImage = clone $Image;
                $this->addProductImage($CloneImage);
                $CloneImage->setProduct($this);
            }

            $Tags = $this->getProductTag();
            $this->ProductTag = new ArrayCollection();
            foreach ($Tags as $Tag) {
                $CloneTag = clone $Tag;
                $this->addProductTag($CloneTag);
                $CloneTag->setProduct($this);
            }

            return $this;
        }

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set name.
         *
         * @param string $name
         *
         * @return Product
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set note.
         *
         * @param string|null $note
         *
         * @return Product
         */
        public function setNote($note = null)
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote()
        {
            return $this->note;
        }

        /**
         * Set descriptionList.
         *
         * @param string|null $descriptionList
         *
         * @return Product
         */
        public function setDescriptionList($descriptionList = null)
        {
            $this->description_list = $descriptionList;

            return $this;
        }

        /**
         * Get descriptionList.
         *
         * @return string|null
         */
        public function getDescriptionList()
        {
            return $this->description_list;
        }

        /**
         * Set descriptionDetail.
         *
         * @param string|null $descriptionDetail
         *
         * @return Product
         */
        public function setDescriptionDetail($descriptionDetail = null)
        {
            $this->description_detail = $descriptionDetail;

            return $this;
        }

        /**
         * Get descriptionDetail.
         *
         * @return string|null
         */
        public function getDescriptionDetail()
        {
            return $this->description_detail;
        }

        /**
         * Set searchWord.
         *
         * @param string|null $searchWord
         *
         * @return Product
         */
        public function setSearchWord($searchWord = null)
        {
            $this->search_word = $searchWord;

            return $this;
        }

        /**
         * Get searchWord.
         *
         * @return string|null
         */
        public function getSearchWord()
        {
            return $this->search_word;
        }

        /**
         * Set freeArea.
         *
         * @param string|null $freeArea
         *
         * @return Product
         */
        public function setFreeArea($freeArea = null)
        {
            $this->free_area = $freeArea;

            return $this;
        }

        /**
         * Get freeArea.
         *
         * @return string|null
         */
        public function getFreeArea()
        {
            return $this->free_area;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Product
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return Product
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Add productCategory.
         *
         * @param \Eccube\Entity\ProductCategory $productCategory
         *
         * @return Product
         */
        public function addProductCategory(\Eccube\Entity\ProductCategory $productCategory)
        {
            $this->ProductCategories[] = $productCategory;

            return $this;
        }

        /**
         * Remove productCategory.
         *
         * @param \Eccube\Entity\ProductCategory $productCategory
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductCategory(\Eccube\Entity\ProductCategory $productCategory)
        {
            return $this->ProductCategories->removeElement($productCategory);
        }
        
        /**
         * Add productClassCategory.
         *
         * @param \Eccube\Entity\ProductClassCategory $productClassCategory
         *
         * @return Product
         */
        public function addProductClassCategory(\Eccube\Entity\ProductClassCategory $productClassCategory)
        {
            $this->ProductClassCategories[] = $productClassCategory;

            return $this;
        }

        /**
         * Remove productClassCategory.
         *
         * @param \Eccube\Entity\ProductClassCategory $productClassCategory
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductClassCategory(\Eccube\Entity\ProductClassCategory $productClassCategory)
        {
            return $this->ProductClassCategories->removeElement($productClassCategory);
        }
        
        
        /**
         * Add productClassName.
         *
         * @param \Eccube\Entity\ProductClassName $productClassName
         *
         * @return Product
         */
        public function addProductClassName(\Eccube\Entity\ProductClassName $productClassName)
        {
            $this->ProductClassNames[] = $productClassName;

            return $this;
        }

        /**
         * Remove productClassName.
         *
         * @param \Eccube\Entity\ProductClassName $productClassName
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductClassName(\Eccube\Entity\ProductClassName $productClassName)
        {
            return $this->ProductClassNames->removeElement($productClassName);
        }
        
        
        /**
         * Get productCategories.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductCategories()
        {
            return $this->ProductCategories;
        }

        /**
         * Get productClassCategories.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductClassCategories()
        {
            return $this->ProductClassCategories;
        }
        
        /**
         * Get productClassNames.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductClassNames()
        {
            return $this->ProductClassNames;
        }

        /**
         * Add productClass.
         *
         * @param \Eccube\Entity\ProductClass $productClass
         *
         * @return Product
         */
        public function addProductClass(\Eccube\Entity\ProductClass $productClass)
        {
            $this->ProductClasses[] = $productClass;

            return $this;
        }

        /**
         * Add productClassSub.
         *
         * @param \Eccube\Entity\ProductClassSub $productClassSub
         *
         * @return Product
         */
        public function addProductClassSub(\Eccube\Entity\ProductClassSub $productClassSub)
        {
            $this->ProductClassSubs[] = $productClassSub;

            return $this;
        }

        /**
         * Remove productClass.
         *
         * @param \Eccube\Entity\ProductClass $productClass
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductClass(\Eccube\Entity\ProductClass $productClass)
        {
            return $this->ProductClasses->removeElement($productClass);
        }
        
        /**
         * Remove productClassSub.
         *
         * @param \Eccube\Entity\ProductClassSub $productClassSub
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductClassSub(\Eccube\Entity\ProductClassSub $productClassSub)
        {
            return $this->ProductClassSubs->removeElement($productClassSub);
        }

        /**
         * Get productClasses.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductClasses()
        {
            return $this->ProductClasses;
        }

        /**
         * Get productClassSubs.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductClassSubs()
        {
            return $this->ProductClassSubs;
        }
        
        
        /**
         * Get classCategories.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getClassCategories()
        {
            return $this->ClassCategories;
        }
        
        
        /**
         * Add productImage.
         *
         * @param \Eccube\Entity\ProductImage $productImage
         *
         * @return Product
         */
        public function addProductImage(\Eccube\Entity\ProductImage $productImage)
        {
            $this->ProductImage[] = $productImage;

            return $this;
        }

        /**
         * Remove productImage.
         *
         * @param \Eccube\Entity\ProductImage $productImage
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductImage(\Eccube\Entity\ProductImage $productImage)
        {
            return $this->ProductImage->removeElement($productImage);
        }

        /**
         * Get productImage.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductImage()
        {
            return $this->ProductImage;
        }

        /**
         * Add productTag.
         *
         * @param \Eccube\Entity\ProductTag $productTag
         *
         * @return Product
         */
        public function addProductTag(\Eccube\Entity\ProductTag $productTag)
        {
            $this->ProductTag[] = $productTag;

            return $this;
        }

        /**
         * Remove productTag.
         *
         * @param \Eccube\Entity\ProductTag $productTag
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductTag(\Eccube\Entity\ProductTag $productTag)
        {
            return $this->ProductTag->removeElement($productTag);
        }

        /**
         * Get productTag.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductTag()
        {
            return $this->ProductTag;
        }

        /**
         * Get Tag
         * フロント側タグsort_no順の配列を作成する
         *
         * @return []Tag
         */
        public function getTags()
        {
            $tags = [];

            foreach ($this->getProductTag() as $productTag) {
                $tags[] = $productTag->getTag();
            }

            usort($tags, function (Tag $tag1, Tag $tag2) {
                return $tag1->getSortNo() < $tag2->getSortNo();
            });

            return $tags;
        }

        /**
         * Add customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return Product
         */
        public function addCustomerFavoriteProduct(\Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct)
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerFavoriteProduct(\Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct)
        {
            return $this->CustomerFavoriteProducts->removeElement($customerFavoriteProduct);
        }

        /**
         * Get customerFavoriteProducts.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCustomerFavoriteProducts()
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return Product
         */
        public function setCreator(\Eccube\Entity\Member $creator = null)
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return \Eccube\Entity\Member|null
         */
        public function getCreator()
        {
            return $this->Creator;
        }

        /**
         * Set status.
         *
         * @param \Eccube\Entity\Master\ProductStatus|null $status
         *
         * @return Product
         */
        public function setStatus(\Eccube\Entity\Master\ProductStatus $status = null)
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         *
         * @return \Eccube\Entity\Master\ProductStatus|null
         */
        public function getStatus()
        {
            return $this->Status;
        }
        
        
        /**
         * Set optionId1.
         *
         * @param integer|null $optionId1
         *
         * @return Product
         */
        public function setOptionId1($optionId1 = null)
        {
            $this->option_id1 = $optionId1;

            return $this;
        }

        /**
         * Get optionId1.
         *
         * @return integer|null
         */
        public function getOptionId1()
        {
            return $this->option_id1;
        }
        
        
        /**
         * Set sales_period_from.
         *
         * @param \DateTime|null $sales_period_from
         *
         * @return Product
         */
        public function setSalesPeriodFrom($sales_period_from = null)
        {
            $this->sales_period_from = $sales_period_from;

            return $this;
        }

        /**
         * Get sales_period_from.
         *
         * @return \DateTime|null
         */
        public function getSalesPeriodFrom()
        {
            return $this->sales_period_from;
        }
        
        /**
         * Set sales_period_to.
         *
         * @param \DateTime|null $sales_period_to
         *
         * @return Product
         */
        public function setSalesPeriodTo($sales_period_to = null)
        {
            $this->sales_period_to = $sales_period_to;

            return $this;
        }

        /**
         * Get sales_period_to.
         *
         * @return \DateTime|null
         */
        public function getSalesPeriodTo()
        {
            return $this->sales_period_to;
        }
        
        /**
         * Set salestype.
         *
         * @param \Eccube\Entity\Master\SalesType|null $salestype
         *
         * @return Product
         */
        public function setSalestype(\Eccube\Entity\Master\SalesType $salestype = null)
        {
            $this->Salestype = $salestype;

            return $this;
        }

        /**
         * Get salestype.
         *
         * @return \Eccube\Entity\Master\SalesType|null
         */
        public function getSalestype()
        {
            return $this->Salestype;
        }
        
        /**
         * Set expiry_date.
         *
         * @param string|null $expiry_date
         *
         * @return Product
         */
        public function setExpiryDate($expiry_date = null)
        {
            $this->expiry_date = $expiry_date;

            return $this;
        }

        /**
         * Get expiry_date.
         *
         * @return string|null
         */
        public function getExpiryDate()
        {
            return $this->expiry_date;
        }
        
        /**
         * Set expiration_date.
         *
         * @param string|null $expiration_date
         *
         * @return Product
         */
        public function setExpirationDate($expiration_date = null)
        {
            $this->expiration_date = $expiration_date;

            return $this;
        }

        /**
         * Get expiration_date.
         *
         * @return string|null
         */
        public function getExpirationDate()
        {
            return $this->expiration_date;
        }
        
        /**
         * Set please_read.
         *
         * @param string|null $please_read
         *
         * @return News
         */
        public function setPleaseRead($please_read = null)
        {
            $this->please_read = $please_read;

            return $this;
        }

        /**
         * Get please_read.
         *
         * @return string|null
         */
        public function getPleaseRead()
        {
            return $this->please_read;
        }
        
        /**
         * Set capacity.
         *
         * @param string|null $capacity
         *
         * @return Product
         */
        public function setCapacity($capacity = null)
        {
            $this->capacity = $capacity;

            return $this;
        }

        /**
         * Get capacity.
         *
         * @return string|null
         */
        public function getCapacity()
        {
            return $this->capacity;
        }
        
        /**
         * Set material.
         *
         * @param string|null $material
         *
         * @return Product
         */
        public function setMaterial($material = null)
        {
            $this->material = $material;

            return $this;
        }

        /**
         * Get material.
         *
         * @return string|null
         */
        public function getMaterial()
        {
            return $this->material;
        }
        

    }
