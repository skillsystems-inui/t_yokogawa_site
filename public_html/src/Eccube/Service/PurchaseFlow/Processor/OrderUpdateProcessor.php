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

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 受注情報更新処理.
 */
class OrderUpdateProcessor extends AbstractPurchaseProcessor
{
    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * OrderUpdateProcessor constructor.
     *
     * @param OrderStatusRepository $orderStatusRepository
     */
    public function __construct(OrderStatusRepository $orderStatusRepository)
    {
        $this->orderStatusRepository = $orderStatusRepository;
    }

    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        if (!$target instanceof Order) {
            return;
        }
        
        //注文ステータス　デフォルトは「新規受付」
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        
        //配送セット(店舗)
    	//　店舗：和泉中央本店、店舗：岸和田店
		//配送セット(取り寄せ)
        //　取り寄せ：ヤマト
        
        $Shipping = $target->getShippings() == null ? null : $target->getShippings()[0];
        $ShippingDelivery = null;
        
        log_info(
            '[受注情報更新処理]commit[ステータス判定]Shipping',
            [
                'Shipping' => $Shipping,
            ]
        );
        
        if($Shipping != null)
        {
        	$_Delivery = $Shipping->getDelivery();
        	
	        log_info(
	            '[受注情報更新処理]commit[ステータス判定]_Delivery',
	            [
	                '_Delivery' => $_Delivery,
	            ]
	        );
	        
        	if($_Delivery != null){
        		$ShippingDelivery = $_Delivery->getName();
        		
		        log_info(
		            '[受注情報更新処理]commit[ステータス判定]ShippingDelivery',
		            [
		                'ShippingDelivery' => $ShippingDelivery,
		            ]
		        );
		                		
        		//取り置きするかの判定　配送方法に「店」が含まれるかどうか
        		if ( strpos( $ShippingDelivery, '店' ) === false ) {
        			//含まれないため新規受付
        			log_info(
			            '[受注情報更新処理]commit[ステータス判定]配送方法に「店」が含まれない',
			            [
			                'ShippingDelivery' => $ShippingDelivery,
			            ]
			        );
        		}else{        			
			        log_info(
			            '[受注情報更新処理]commit[ステータス判定]配送方法に「店」が含まれる',
			            [
			                'ShippingDelivery' => $ShippingDelivery,
			            ]
			        );
			                		
        			//取り置き(店舗で受け取り)判定のため、ステータスは取り置きをセットする(KEEPED)
        			$OrderStatus = $this->orderStatusRepository->find(OrderStatus::KEEPED);
        		}
        	}
        }
        
        log_info(
            '[受注情報更新処理] commit()',
            [
                'ShippingDelivery' => $ShippingDelivery,
                'Shipping' => $Shipping,
                'OrderStatus' => $OrderStatus,
            ]
        );
        
        $target->setOrderStatus($OrderStatus);
        $target->setOrderDate(new \DateTime());
    }
}
