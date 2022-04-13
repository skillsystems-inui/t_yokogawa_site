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

namespace Eccube\Form\Type\Shopping;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\Shipping;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Plugin\DeliveryDate4\Repository\DeliveryDateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ShippingType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;
    
    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @var DeliveryDateRepository
     */
    protected $deliveryDateRepository;

    /**
     * ShippingType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param DeliveryRepository $deliveryRepository
     * @param DeliveryFeeRepository $deliveryFeeRepository
     * @param DeliveryDateRepository $deliveryDateRepository
     */
    public function __construct(EccubeConfig $eccubeConfig, DeliveryRepository $deliveryRepository, DeliveryFeeRepository $deliveryFeeRepository, DeliveryDateRepository $deliveryDateRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
        $this->deliveryDateRepository = $deliveryDateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'OrderItems',
                CollectionType::class,
                [
                    'entry_type' => OrderItemType::class,
                ]
            );

        // 配送業者のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /* @var Shipping $Shipping */
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }
                
                //注文情報の受け取り方法を取得
                $ShipOrder = $Shipping->getOrder();
                if (is_null($ShipOrder) || !$ShipOrder->getId()) {
                    return;
                }
                $uketori_type = $ShipOrder->getUketoriType();

                // 配送商品に含まれる販売種別を抽出.
                $OrderItems = $Shipping->getProductOrderItems();
                $SaleTypes = [];
                foreach ($OrderItems as $OrderItem) {
                    $ProductClass = $OrderItem->getProductClass();
                    $SaleType = $ProductClass->getSaleType();
                    $SaleTypes[$SaleType->getId()] = $SaleType;
                }
                
                log_info(
		            '配送選択画面　配送セット',
		            [
		                'getDelivery()' => $Shipping->getDelivery(),
		            ]
		        );

                // 販売種別に紐づく配送業者を取得.
                //$Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);
                if($uketori_type != 2){
                	//ヤマト以外
                	$Deliveries = $this->deliveryRepository->getDeliveriesTentou();
                	
                	//配送セット(店舗)
                	$deliv_id_honten = 3;//店舗：和泉中央本店
            		$deliv_id_kishiwada = 4;//店舗：岸和田店
            		
            		//取り置きするかの判定　配送方法に「本店」が含まれるかどうか
            		$_Delivery = $Shipping->getDelivery();
        			$_shippingDelivery = $_Delivery->getName();
        			if ( strpos( $_shippingDelivery, '岸和田' ) === false ) {
        				//配送方法に「岸和田」が含まれないから「本店」
        				$TargetDelivery = $this->deliveryRepository->find($deliv_id_honten);
            			$Shipping->setDelivery($TargetDelivery);        				
            		}else{
            			//配送方法に「岸和田」が含まれるから「岸和田店」
            			$TargetDelivery = $this->deliveryRepository->find($deliv_id_kishiwada);
            			$Shipping->setDelivery($TargetDelivery);
            		}
            		
	            	
                }else{
                	//ヤマト指定
                	$Deliveries = $this->deliveryRepository->getDeliveriesYamato();
                	
                	//配送セット(取り寄せ)
            		$deliv_id_yamato = 1;//取り寄せ：ヤマト
	            	$TargetDelivery = $this->deliveryRepository->find($deliv_id_yamato);
	            	$Shipping->setDelivery($TargetDelivery);
                }
                
                log_info(
			            '販売種別に紐づく配送業者を取得　uketori_type',
			            [
			                'uketori_type' => $uketori_type,
			            ]
			        );

                // 配送業者のプルダウンにセット.
                $form = $event->getForm();
                $form->add(
                    'Delivery',
                    EntityType::class,
                    [
                        'required' => false,
                        'label' => 'shipping.label.delivery_hour',
                        'class' => 'Eccube\Entity\Delivery',
                        'choice_label' => 'name',
                        'choices' => $Deliveries,
                        'placeholder' => false,
                        'constraints' => [
                            new NotBlank(),
                        ],
                    ]
                );
            }
        );

        // お届け日のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }

                // お届け日の設定
                $minDate = 0;
                $deliveryDurationFlag = false;

                // 配送時に最大となる商品日数を取得
                foreach ($Shipping->getOrderItems() as $detail) {
                    $ProductClass = $detail->getProductClass();
                    if (is_null($ProductClass)) {
                        continue;
                    }
                    $deliveryDuration = $ProductClass->getDeliveryDuration();
                    if (is_null($deliveryDuration)) {
                        continue;
                    }
                    if ($deliveryDuration->getDuration() < 0) {
                        // 配送日数がマイナスの場合はお取り寄せなのでスキップする
                        $deliveryDurationFlag = false;
                        break;
                    }

                    if ($minDate < $deliveryDuration->getDuration()) {
                        $minDate = $deliveryDuration->getDuration();
                    }
                    // 配送日数が設定されている
                    $deliveryDurationFlag = true;
                }

                // 配達最大日数期間を設定
                $deliveryDurations = [];

                // 配送日数が設定されている
                if ($deliveryDurationFlag) {
                    $period = new \DatePeriod(
                        new \DateTime($minDate.' day'),
                        new \DateInterval('P1D'),
                        new \DateTime($minDate + $this->eccubeConfig['eccube_deliv_date_end_max'].' day')
                    );

                    // 曜日設定用
                    $dateFormatter = \IntlDateFormatter::create(
                        'ja_JP@calendar=japanese',
                        \IntlDateFormatter::FULL,
                        \IntlDateFormatter::FULL,
                        'Asia/Tokyo',
                        \IntlDateFormatter::TRADITIONAL,
                        'E'
                    );

                    foreach ($period as $day) {
                        $deliveryDurations[$day->format('Y/m/d')] = $day->format('Y/m/d').'('.$dateFormatter->format($day).')';
                    }
                }

                $form = $event->getForm();
                $form
                    ->add(
                        'shipping_delivery_date',
                        ChoiceType::class,
                        [
                            'choices' => array_flip($deliveryDurations),
                            'required' => false,
                            'placeholder' => 'common.select__unspecified',
                            'mapped' => false,
                            'data' => $Shipping->getShippingDeliveryDate() ? $Shipping->getShippingDeliveryDate()->format('Y/m/d') : null,
                        ]
                    );
            }
        );
        // お届け時間のプルダウンを生成
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Shipping $Shipping */
                $Shipping = $event->getData();
                if (is_null($Shipping) || !$Shipping->getId()) {
                    return;
                }

                $ShippingDeliveryTime = null;
                $DeliveryTimes = [];
                $Delivery = $Shipping->getDelivery();
                
                log_info(
		            'お取り寄せ方法　Shipping',
		            [
		                'Shipping' => $Shipping->getId(),
		            ]
		        );
                
                
                if ($Delivery) {
                    $DeliveryTimes = $Delivery->getDeliveryTimes();
                    $DeliveryTimes = $DeliveryTimes->filter(function (DeliveryTime $DeliveryTime) {
                        return $DeliveryTime->isVisible();
                    });

                    foreach ($DeliveryTimes as $deliveryTime) {
                        if ($deliveryTime->getId() == $Shipping->getTimeId()) {
                            $ShippingDeliveryTime = $deliveryTime;
                            break;
                        }
                    }
                }

                $form = $event->getForm();
                $form->add(
                    'DeliveryTime',
                    EntityType::class,
                    [
                        'label' => 'front.shopping.delivery_time',
                        'class' => 'Eccube\Entity\DeliveryTime',
                        'choice_label' => 'deliveryTime',
                        'choices' => $DeliveryTimes,
                        'required' => false,
                        'placeholder' => 'common.select__unspecified',
                        'mapped' => false,
                        'data' => $ShippingDeliveryTime,
                    ]
                );
            }
        );

        // POSTされないデータをエンティティにセットする.
        // TODO PurchaseFlow で行うのが適切.
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Shipping $Shipping */
            $Shipping = $event->getData();
            $form = $event->getForm();
            /** @var Delivery $Delivery */
            $Delivery = $form['Delivery']->getData();
            if ($Delivery) {
                $Shipping->setShippingDeliveryName($Delivery->getName());
            } else {
                $Shipping->setShippingDeliveryName(null);
            }
            
            $DeliveryDate = $form['shipping_delivery_date']->getData();
            log_info(
	            '配達日セット　開始',
	            [
	                'DeliveryDate' => $DeliveryDate,
	            ]
	        );
            if ($DeliveryDate) {
                $Shipping->setShippingDeliveryDate(new \DateTime($DeliveryDate));
                
                //出荷予定日にデフォルトセット(配送日) 20220409
                $ShukkayoteiDate = $DeliveryDate;
                
                //配送日から配送先の配送日数を引いた日付を出荷予定日とする
                // 都道府県ごとの配送日数を引く
	            $PrefDate = $this->deliveryDateRepository->findOneBy([
	                'Delivery' => $Delivery,
	                'Pref' => $Shipping->getPref(),
	            ]);
	            
	            
                log_info(
		            '出荷予定日セット　配送先の都道府県に要する日数を取得',
		            [
		                'PrefDate' => $PrefDate,
		            ]
		        );
	            
	            if($PrefDate){
	                $pDate = $PrefDate->getDates();
	                
	                log_info(
			            '出荷予定日セット　日数取得',
			            [
			                'pDate' => $pDate,
			            ]
			        );
	                
	                $strDeliveryDate = date('Y-m-d H:i:s', strtotime($DeliveryDate));
	                
	                if(!is_null($pDate)){
	                    $minus = " -".$pDate." day";
	                    
	                    log_info(
			            '出荷予定日セット　マイナス日数',
				            [
				                'minus' => $minus,
				                'DeliveryDate' => $strDeliveryDate,
				            ]
				        );
			        
	                    //出荷予定日をセット
	                    $ShukkayoteiDate = date("Y-m-d H:i:s", strtotime($strDeliveryDate.$minus));
	                    
	                    log_info(
				            '出荷予定日セット　日付取得',
				            [
				                'ShukkayoteiDate' => $ShukkayoteiDate,
				            ]
				        );
	                }
	            }
                
                //出荷予定日もあわせて登録する 20220409
                $Shipping->setShippingShukkayoteiDate(new \DateTime($ShukkayoteiDate));
                
                log_info(
		            '出荷予定日セット　完了',
		            [
		                'ShukkayoteiDate' => $ShukkayoteiDate,
		            ]
		        );
                
            } else {
                $Shipping->setShippingDeliveryDate(null);
            }
            log_info(
	            '配達日セット　終了',
	            [
	                'DeliveryDate' => $DeliveryDate,
	            ]
	        );

            $DeliveryTime = $form['DeliveryTime']->getData();
            if ($DeliveryTime) {
                $Shipping->setShippingDeliveryTime($DeliveryTime->getDeliveryTime());
                $Shipping->setTimeId($DeliveryTime->getId());
            } else {
                $Shipping->setShippingDeliveryTime(null);
                $Shipping->setTimeId(null);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Eccube\Entity\Shipping',
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '_shopping_shipping';
    }
}
