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
            
            //ヤマトかどうか
            if($uketori_type == 2){
	            //--ヤマトの場合--
	            
	            //----- タイプ別金額 -----
				$baseprice_Jouon  = 0;//常温
				$baseprice_reiTou = 0;//冷凍
				$baseprice_reiZou = 0;//冷蔵
	            
	            //----- 配送タイプ判定(常温・冷蔵・冷凍) -----
	            // 常温：追加料金 0円
	            // 冷蔵：追加料金 220円
	            // 冷凍：追加料金 220円
	            //----------------------
	            //注文に含まれる商品から追加料金を取得する
				$include_reiZou = false;//冷蔵が含まれる
				$include_reiTou = false;//冷凍が含まれる
				$include_Jouon = false;//常温が含まれる
				
				// 注文に含まれる商品単位↓
				foreach ($Shipping->getOrderItems() as $p_item) {
	                
	                //リセット
	                $chk_reiZou = false;//冷蔵が含まれる(常温判定時に使用)
					$chk_reiTou = false;//冷凍が含まれる(常温判定時に使用)
					
	                if (!$p_item->isProduct()) {
	                    continue;
	                }
	                
	                // 商品に含まれるカテゴリ単位↓
	                //商品が持つカテゴリから判断する
	                foreach ($p_item->getProduct()->getProductCategories() as $p_category) {
	                	//冷蔵配送の場合　カテゴリ：「ゼリーソルベ」id:97 //冷蔵商品：132
	                	if($p_category->getCategoryId() == 132){
	                		$include_reiZou = true;
	                		$chk_reiZou = true;//冷蔵が含まれる(常温判定時に使用)
							
							//冷蔵商品の金額を加算
                			$baseprice_reiZou = $baseprice_reiZou + $p_item->getPrice() + $p_item->getAdditionalPrice();
	                		
				            log_info( '送料計算　タイプ：ヤマト　配送タイプ：冷蔵配送', [ '送料' => $souryo,'カテゴリID(CategoryId)' => $p_category->getCategoryId(), ]);
					        
					        //商品単位でカテゴリ判定できたらループから抜ける
	                		break;
	                		
					        
	                	}else if($p_category->getCategoryId() == 74){
	                		//冷凍配送の場合　カテゴリ：「冷凍商品」id:74
	                		$include_reiTou = true;
	                		$chk_reiTou = true;//冷凍が含まれる(常温判定時に使用)
	                		
	                		//冷凍商品の金額を加算
                			$baseprice_reiTou = $baseprice_reiTou + $p_item->getPrice() + $p_item->getAdditionalPrice();
	                		
				            log_info('送料計算　タイプ：ヤマト　配送タイプ：冷凍配送', [ '送料' => $souryo,'カテゴリID(CategoryId)' => $p_category->getCategoryId(),]);
					        
					        //商品単位でカテゴリ判定できたらループから抜ける
	                		break;
	                	}
	                }
	                // 商品に含まれるカテゴリ単位↑
	                
	                //log_info( '送料計算　「常温」と判断する前', ['chk_reiTou' => $chk_reiTou, 'chk_reiZou' => $chk_reiZou,] );
	                
	                //冷凍でも冷蔵でもなければ「常温」と判断する
	                if($chk_reiTou == false && $chk_reiZou == false){
	                
						$_today = date("Y/m/d"); //今日の日付
						$_startDate = "2022/04/20"; //開始日
						$_endDate = "2022/11/30"; //終了日
						if(strtotime($_today) >= strtotime($_startDate) && strtotime($_endDate) >= strtotime($_today)){
							//範囲内
							log_info('範囲内', ['送料' => $_today, ] );
						}else{
							//範囲外
							log_info('範囲外', ['送料' => $_today, ] );
						}
						
						//常温判定する
                		$include_Jouon = true;
                		
                		//常温商品の金額を加算
                		$baseprice_Jouon = $baseprice_Jouon + $p_item->getPrice() + $p_item->getAdditionalPrice();
                		
                		log_info( '送料計算　タイプ：ヤマト　配送タイプ：それ以外(常温)', [ '送料' => $souryo, ]);
                	}
	            }
	            // 注文に含まれる商品単位↑
	            
	            //-----【タイプ別の基本料金取得】-----
	            //----- 基本料金(タイプ別) -----
				$basefee_Jouon  = 0;//常温
				$basefee_reiTou = 0;//冷凍
				$basefee_reiZou = 0;//冷蔵
				$basefee_JZ     = 0;//常温と冷蔵のまとめ
				
				//---タイプ別追加料金---
				$plus_reitou = 220;//冷凍なら追加料金
				$plus_reizou = 220;//冷蔵なら追加料金
	            
	            //---------- 冷凍 ----------
	            //冷凍商品があるときのみ基本料金をセット
	            if($baseprice_reiTou > 0){
		            //どの区画に入るか
		            if($baseprice_reiTou < 2000){
		            	$reitouDelivery = $this->deliveryRepository->find($deliv_id1);
		            }else if(2000 <= $baseprice_reiTou && $baseprice_reiTou < 5000){
		            	$reitouDelivery = $this->deliveryRepository->find($deliv_id2);
		            }else if(5000 <= $baseprice_reiTou && $baseprice_reiTou < 10000){
		            	$reitouDelivery = $this->deliveryRepository->find($deliv_id3);
		            }else{
		            	$reitouDelivery = $this->deliveryRepository->find($deliv_id4);
		            }
		            //配送方法セット
		            /** @var DeliveryFee $reitouDeliveryFee */
		            $reitouDeliveryFee = $this->deliveryFeeRepository->findOneBy([
		                'Delivery' => $reitouDelivery,
		                'Pref' => $Shipping->getPref(),
		            ]);
		            
		            //基本料金セット
		            $basefee_reiTou = $reitouDeliveryFee->getFee() + $plus_reitou;
	            }
	            //---------- .冷凍 ----------
	            
	            //---------- 冷蔵 ----------
	            //冷蔵商品があるときのみ基本料金をセット
	            if($baseprice_reiZou > 0){
		            //どの区画に入るか
		            if($baseprice_reiZou < 2000){
		            	$reizouDelivery = $this->deliveryRepository->find($deliv_id1);
		            }else if(2000 <= $baseprice_reiZou && $baseprice_reiZou < 5000){
		            	$reizouDelivery = $this->deliveryRepository->find($deliv_id2);
		            }else if(5000 <= $baseprice_reiZou && $baseprice_reiZou < 10000){
		            	$reizouDelivery = $this->deliveryRepository->find($deliv_id3);
		            }else{
		            	$reizouDelivery = $this->deliveryRepository->find($deliv_id4);
		            }
		            //配送方法セット
		            /** @var DeliveryFee $reizouDeliveryFee */
		            $reizouDeliveryFee = $this->deliveryFeeRepository->findOneBy([
		                'Delivery' => $reizouDelivery,
		                'Pref' => $Shipping->getPref(),
		            ]);
		            
		            //基本料金セット
		            $basefee_reiZou = $reizouDeliveryFee->getFee() + $plus_reizou;
	            }
	            //---------- .冷蔵 ----------
	            
	            //---------- 常温 ----------
	            //常温商品があるときのみ基本料金をセット
	            if($baseprice_Jouon > 0){
		            //どの区画に入るか
		            if($baseprice_Jouon < 2000){
		            	$jouonDelivery = $this->deliveryRepository->find($deliv_id1);
		            }else if(2000 <= $baseprice_Jouon && $baseprice_Jouon < 5000){
		            	$jouonDelivery = $this->deliveryRepository->find($deliv_id2);
		            }else if(5000 <= $baseprice_Jouon && $baseprice_Jouon < 10000){
		            	$jouonDelivery = $this->deliveryRepository->find($deliv_id3);
		            }else{
		            	$jouonDelivery = $this->deliveryRepository->find($deliv_id4);
		            }
		            //配送方法セット
		            /** @var DeliveryFee $jouonDeliveryFee */
		            $jouonDeliveryFee = $this->deliveryFeeRepository->findOneBy([
		                'Delivery' => $jouonDelivery,
		                'Pref' => $Shipping->getPref(),
		            ]);
		            
		            //基本料金セット
		            $basefee_Jouon = $jouonDeliveryFee->getFee();
	            }
	            //---------- .常温 ----------
	            
	            //---------- (常温と冷蔵のまとめ) ----------
	            //(常温と冷蔵)商品があるときのみ基本料金をセット
	            $baseprice_JZ = $baseprice_Jouon + $baseprice_reiZou;
	            if($baseprice_JZ > 0){
		            //どの区画に入るか
		            if($baseprice_JZ < 2000){
		            	$jzDelivery = $this->deliveryRepository->find($deliv_id1);
		            }else if(2000 <= $baseprice_JZ && $baseprice_JZ < 5000){
		            	$jzDelivery = $this->deliveryRepository->find($deliv_id2);
		            }else if(5000 <= $baseprice_JZ && $baseprice_JZ < 10000){
		            	$jzDelivery = $this->deliveryRepository->find($deliv_id3);
		            }else{
		            	$jzDelivery = $this->deliveryRepository->find($deliv_id4);
		            }
		            //配送方法セット
		            /** @var DeliveryFee $jzDeliveryFee */
		            $jzDeliveryFee = $this->deliveryFeeRepository->findOneBy([
		                'Delivery' => $jzDelivery,
		                'Pref' => $Shipping->getPref(),
		            ]);
		            
		            //基本料金セット(冷蔵なので手数料も加算)
		            $basefee_JZ = $jzDeliveryFee->getFee() + $plus_reizou;
	            }
	            //---------- .(常温と冷蔵のまとめ) ----------
	            
	            //-----.【タイプ別の基本料金取得】-----
	            
	            
	            
	            //-----配送タイプがバラバラになったときの合計送料-----
	            $total_fee = 0;
	            
	            if($include_reiZou == true && $include_reiTou == true && $include_Jouon == true){
	            	//①常温、②冷凍、③冷蔵なので「冷凍と冷蔵」分の送料　※常温は冷蔵にまとめることができる
	            	$total_fee = $basefee_reiTou + $basefee_JZ;
	            	
	            }else if($include_reiZou == true && $include_reiTou == true && $include_Jouon == false){
	            	//②冷凍、③冷蔵なので「冷凍と冷蔵」分の送料
	            	$total_fee = $basefee_reiTou + $basefee_reiZou;
	            	
	            }else if($include_reiZou == true && $include_reiTou == false && $include_Jouon == true){
	            	//①常温、③冷蔵なので「冷蔵」分の送料　※常温は冷蔵にまとめることができる
	            	$total_fee = $basefee_JZ;
	                
	            }else if($include_reiZou == false && $include_reiTou == true && $include_Jouon == true){
	            	//①常温、②冷凍なので「常温と冷凍」分の送料
	            	$total_fee = $basefee_reiTou + $basefee_Jouon;
	            	
	            }else if($include_reiZou == false && $include_reiTou == false && $include_Jouon == true){
	            	//①常温なので「常温」分の送料
	            	$total_fee = $basefee_Jouon;
	            	
	            }else if($include_reiZou == false && $include_reiTou == true && $include_Jouon == false){
	            	//②冷凍なので「冷凍」分の送料
	            	$total_fee = $basefee_reiTou;
	            	
	            }else if($include_reiZou == true && $include_reiTou == false && $include_Jouon == false){
	            	//③冷蔵なので「冷蔵」分の送料
	            	$total_fee = $basefee_reiZou;
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
	            $souryo = $total_fee - $user_sub_price;
	            
	            log_info(
		            '送料計算　タイプ：ヤマト　　最終送料',
			            [
			                '送料(最終)' => $souryo,
			                '会員割引(user_sub_price)' => $user_sub_price,
			            ]
		        );
		        
            }else{
	            //配送以外(店頭受取)の場合
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
