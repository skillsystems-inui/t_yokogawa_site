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

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料明細追加.
 */
class DeliveryFeePreprocessor implements ItemHolderPreprocessor
{
    /** @var BaseInfo */
    protected $BaseInfo;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;
    
    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * DeliveryFeePreprocessor constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     * @param EntityManagerInterface $entityManager
     * @param TaxRuleRepository $taxRuleRepository
     * @param DeliveryRepository $deliveryRepository
     * @param DeliveryFeeRepository $deliveryFeeRepository
     */
    public function __construct(
        BaseInfoRepository $baseInfoRepository,
        EntityManagerInterface $entityManager,
        TaxRuleRepository $taxRuleRepository,
        DeliveryRepository $deliveryRepository,
        DeliveryFeeRepository $deliveryFeeRepository
    ) {
        $this->BaseInfo = $baseInfoRepository->get();
        $this->entityManager = $entityManager;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $this->removeDeliveryFeeItem($itemHolder);
        $this->saveDeliveryFeeItem($itemHolder);
    }

    private function removeDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        foreach ($itemHolder->getShippings() as $Shipping) {
            foreach ($Shipping->getOrderItems() as $item) {
                if ($item->getProcessorName() == DeliveryFeePreprocessor::class) {
                    $Shipping->removeOrderItem($item);
                    $itemHolder->removeOrderItem($item);
                    $this->entityManager->remove($item);
                }
            }
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    private function saveDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        $DeliveryFeeType = $this->entityManager
            ->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);

        /** @var Order $Order */
        $Order = $itemHolder;
        /* @var Shipping $Shipping */
        foreach ($Order->getShippings() as $Shipping) {
            // 送料の計算
            $deliveryFeeProduct = 0;
            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
                /** @var OrderItem $item */
                foreach ($Shipping->getOrderItems() as $item) {
                    if (!$item->isProduct()) {
                        continue;
                    }
                    $deliveryFeeProduct += $item->getProductClass()->getDeliveryFee() * $item->getQuantity();
                }
            }
            
            //送料
            $souryo = 0;

            /* 配送がヤマトの場合、合計金額によって配送タイプを選択する
            * 
            * 2000円未満             ： 配送:ヤマト運輸
            * 2000円以上、5000円未満 ： 配送:ヤマト運輸2
            * 5000円以上、10000円未満： 配送:ヤマト運輸3
            * 10000円以上            ： 配送:ヤマト運輸4
            */
            $deliv_id1 = 1;//ヤマト運輸
            $deliv_id2 = 5;//ヤマト運輸2
            $deliv_id3 = 6;//ヤマト運輸3
            $deliv_id4 = 7;//ヤマト運輸4
            
            $uketori_type = 1;// 2:お取り寄せ(ヤマト運輸)、2以外:店頭受取
            //注文情報の受け取り方法を取得 //test
            $ShipOrder = $Shipping->getOrder();
            if (!(is_null($ShipOrder))) {
                $uketori_type = $ShipOrder->getUketoriType();// 2:お取り寄せ(ヤマト運輸)、2以外:店頭受取
            }
            
            log_info(
	            '送料計算　開始',
	            [
	                '送料' => $souryo,
	                'is_null_ShipOrder' => is_null($ShipOrder),
	                'ShipOrder_getId' => $ShipOrder->getId(),
	                '受け取りタイプ(uketori_type)' => $uketori_type,
	                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
	            ]
	        );
            
            //ヤマトかどうか
            if($uketori_type == 2){
	            //--ヤマトの場合--
	            
	            //合計金額
	            $targetSubTotal = $Shipping->getOrder()->getSubtotal();
	            //どの区画に入るか
	            if($targetSubTotal < 2000){
	            	$TargetDelivery = $this->deliveryRepository->find($deliv_id1);
	            }else if(2000 <= $targetSubTotal && $targetSubTotal < 5000){
	            	$TargetDelivery = $this->deliveryRepository->find($deliv_id2);
	            }else if(5000 <= $targetSubTotal && $targetSubTotal < 10000){
	            	$TargetDelivery = $this->deliveryRepository->find($deliv_id3);
	            }else{
	            	$TargetDelivery = $this->deliveryRepository->find($deliv_id4);
	            }
	            
	            log_info(
		            '送料計算　タイプ：ヤマト　区画決定',
			            [
			                '送料' => $souryo,
			                '商品の合計金額' => $targetSubTotal,
			                '区画(TargetDelivery)' => $TargetDelivery,
			                '受け取りタイプ(uketori_type)' => $uketori_type,
			                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
			            ]
		        );
	            
	            //配送方法セット
	            /** @var DeliveryFee $DeliveryFee */
	            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
	                'Delivery' => $TargetDelivery,
	                'Pref' => $Shipping->getPref(),
	            ]);
	            
	            
	            log_info(
		            '送料計算　タイプ：ヤマト　基本送料決定',
			            [
			                '送料' => $souryo,
			                '基本送料(DeliveryFee)' => $DeliveryFee,
			                '区画(TargetDelivery)' => $TargetDelivery,
			                '都道府県コード(Pref)' => $Shipping->getPref(),
			                '受け取りタイプ(uketori_type)' => $uketori_type,
			                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
			            ]
		        );
	            
	            
	            //----- 配送タイプ判定(常温・冷蔵・冷凍) -----
	            // 常温：追加料金 0円
	            // 冷蔵：追加料金 220円
	            // 冷凍：追加料金 220円
	            //----------------------
	            //注文に含まれる商品から追加料金を取得する
				$include_reiZou = false;//冷蔵が含まれる
				$include_reiTou = false;//冷凍が含まれる
				$include_Jouon = false;//常温が含まれる
				foreach ($Shipping->getOrderItems() as $p_item) {
	                if (!$p_item->isProduct()) {
	                    continue;
	                }
	                //商品が持つカテゴリから判断する
	                foreach ($p_item->getProduct()->getProductCategories() as $p_category) {
	                	//冷蔵配送の場合　カテゴリ：「ゼリーソルベ」id:97
	                	if($p_category->getCategoryId() == 97){
	                		$include_reiZou = true;
	                		
				            log_info(
					            '送料計算　タイプ：ヤマト　配送タイプ：冷蔵配送',
						            [
						                '送料' => $souryo,
						                '基本送料(DeliveryFee)' => $DeliveryFee,
						                'カテゴリID(CategoryId)' => $p_category->getCategoryId(),
						                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
						            ]
					        );
					        
					        //商品単位でカテゴリ判定できたらループから抜ける
	                		break;
	                		
					        
	                	}else if($p_category->getCategoryId() == 74){
	                		//冷凍配送の場合　カテゴリ：「冷凍商品」id:74
	                		$include_reiTou = true;
	                		
				            log_info(
					            '送料計算　タイプ：ヤマト　配送タイプ：冷凍配送',
						            [
						                '送料' => $souryo,
						                '基本送料(DeliveryFee)' => $DeliveryFee,
						                'カテゴリID(CategoryId)' => $p_category->getCategoryId(),
						                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
						            ]
					        );
					        
					        //商品単位でカテゴリ判定できたらループから抜ける
	                		break;
	                	}
	                }
	                
	                
	                //冷凍でも冷蔵でもなければ「常温」と判断する
	                if($include_reiTou != true && $include_reiZou != true){
                		$include_Jouon = true;
                		
			            log_info(
				            '送料計算　タイプ：ヤマト　配送タイプ：それ以外(常温)',
					            [
					                '送料' => $souryo,
					                '基本送料(DeliveryFee)' => $DeliveryFee,
					                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
					            ]
				        );
                	}
                	
	            }
	            
	            //-----配送タイプがバラバラになったときの追加の送料-----
	            $include_type_addition_fee = 0;
	            $plus_reizou = 220;
	            $plus_reitou = 220;
	            if($include_reiZou == true && $include_reiTou == true && $include_Jouon == true){
	            	//+2送料分+冷凍追加分+冷蔵追加分
	            	$include_type_addition_fee = $include_type_addition_fee + $DeliveryFee->getFee() + $DeliveryFee->getFee() + $plus_reizou + $plus_reitou;
	            	
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+2送料分+冷凍追加分+冷蔵追加分',
				            [
				                '送料' => $souryo,
				                'include_type_addition_fee' => $include_type_addition_fee,
				                'DeliveryFee->getFee()' => $DeliveryFee->getFee(),
				                'DeliveryFee->getFee()' => $DeliveryFee->getFee(),
				                'plus_reizou' => $plus_reizou,
				                'plus_reitou' => $plus_reitou,
				                '送料追加分(include_type_addition_fee)' => $include_type_addition_fee,
				            ]
			        );
	            	
	            }else if($include_reiZou == true && $include_reiTou == true && $include_Jouon == false){
	            	//+1送料分+冷凍追加分+冷蔵追加分
	            	$include_type_addition_fee = $include_type_addition_fee + $DeliveryFee->getFee() + $plus_reizou + $plus_reitou;
	            	
	            	
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+1送料分+冷凍追加分+冷蔵追加分',
				            [
				                '送料' => $souryo,
				                'include_type_addition_fee' => $include_type_addition_fee,
				                'DeliveryFee->getFee()' => $DeliveryFee->getFee(),
				                'plus_reizou' => $plus_reizou,
				                'plus_reitou' => $plus_reitou,
				                '送料追加分(include_type_addition_fee)' => $include_type_addition_fee,
				            ]
			        );
	            	
	            }else if($include_reiZou == true && $include_reiTou == false && $include_Jouon == true){
	            	//+1送料分+冷蔵追加分
	            	//冷蔵のみと同じ処理
	            	//$include_type_addition_fee = $include_type_addition_fee + $DeliveryFee->getFee() + $plus_reizou;
	            	$include_type_addition_fee = $include_type_addition_fee + $plus_reizou;
	                
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+1送料分+冷蔵追加分',
				            [
				                '送料' => $souryo,
				                'include_type_addition_fee' => $include_type_addition_fee,
				                //'DeliveryFee->getFee()' => $DeliveryFee->getFee(),
				                'plus_reizou' => $plus_reizou,
				                '送料追加分(include_type_addition_fee)' => $include_type_addition_fee,
				            ]
			        );
	            	
	            }else if($include_reiZou == false && $include_reiTou == true && $include_Jouon == true){
	            	//+1送料分+冷凍追加分
	            	$include_type_addition_fee = $include_type_addition_fee + $DeliveryFee->getFee()  + $plus_reitou;
	            	
	            	
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+1送料分+冷凍追加分',
				            [
				                '送料' => $souryo,
				                'include_type_addition_fee' => $include_type_addition_fee,
				                'DeliveryFee->getFee()' => $DeliveryFee->getFee(),
				                'plus_reitou' => $plus_reitou,
				                '送料追加分(include_type_addition_fee)' => $include_type_addition_fee,
				            ]
			        );
	            	
	            }else if($include_reiZou == false && $include_reiTou == false && $include_Jouon == true){
	            	//+なし
	            	
	            	
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+なし',
				            [
				                '送料' => $souryo,
				            ]
			        );
	            	
	            }else if($include_reiZou == false && $include_reiTou == true && $include_Jouon == false){
	            	//+冷凍追加分
	            	$include_type_addition_fee = $include_type_addition_fee + $plus_reitou;
	            	
	            	
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+冷凍追加分',
				            [
				                '送料' => $souryo,
				                'include_type_addition_fee' => $include_type_addition_fee,
				                'plus_reitou' => $plus_reitou,
				                '送料追加分(include_type_addition_fee)' => $include_type_addition_fee,
				            ]
			        );
	            	
	            }else if($include_reiZou == true && $include_reiTou == false && $include_Jouon == false){
	            	//+冷蔵追加分
	            	$include_type_addition_fee = $include_type_addition_fee + $plus_reizou;
	            	
	            	
	                
		            log_info(
			            '送料計算　タイプ：ヤマト　配送タイプがバラバラで追加時：+2送料分+冷凍追加分+冷蔵追加分',
				            [
				                '送料' => $souryo,
				                'include_type_addition_fee' => $include_type_addition_fee,
				                'plus_reizou' => $plus_reizou,
				                '送料追加分(include_type_addition_fee)' => $include_type_addition_fee,
				            ]
			        );
	            	
	            }
	            
	            //-----会員の場合は割引を行う(ゲストは対象外)-----
	            //割引額
	            $user_sub_price = 0;
	            // user_appを取得
				$user_app = \Eccube\Application::getInstance();
				// user情報を取得
				$user_info = $user_app['user'];
				// ゲストユーザは"anon."が格納される
				if ($user_info != "anon.") {
				  //会員の場合割引する　100円
				  $user_sub_price = 100;
				}
	            
	            //送料
	            $souryo = $DeliveryFee->getFee() + $include_type_addition_fee - $user_sub_price;
	            
	            log_info(
		            '送料計算　タイプ：ヤマト　　最終送料',
			            [
			                '送料(最終)' => $souryo,
			                '基本送料(DeliveryFee->getFee())' => $DeliveryFee->getFee(),
			                '追加送料(include_type_addition_fee)' => $include_type_addition_fee,
			                '会員割引(user_sub_price)' => $user_sub_price,
			            ]
		        );
		        
            }else{
	            //配送以外(店頭受取)の場合
	            
	            /** @var DeliveryFee $DeliveryFee */
	            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
	                'Delivery' => $Shipping->getDelivery(),
	                'Pref' => $Shipping->getPref(),
	            ]);
	            //送料なし
	            $souryo = 0;
	            
	            log_info(
	            '送料計算　タイプ：ヤマト以外',
		            [
		                '送料(最終)' => $souryo,
		                'is_null_ShipOrder' => is_null($ShipOrder),
		                'ShipOrder_getId' => $ShipOrder->getId(),
		                '受け取りタイプ(uketori_type)' => $uketori_type,
		                '配送名(getShippingDeliveryName)' => $Shipping->getShippingDeliveryName(),
		            ]
		        );
		        
            }
            
            $OrderItem = new OrderItem();
            $OrderItem->setProductName($DeliveryFeeType->getName())
                ->setPrice($souryo + $deliveryFeeProduct)
                ->setQuantity(1)
                ->setOrderItemType($DeliveryFeeType)
                ->setShipping($Shipping)
                ->setOrder($itemHolder)
                ->setTaxDisplayType($TaxInclude)
                ->setTaxType($Taxation)
                ->setProcessorName(DeliveryFeePreprocessor::class);

            $itemHolder->addItem($OrderItem);
            $Shipping->addOrderItem($OrderItem);
        }
    }
}
