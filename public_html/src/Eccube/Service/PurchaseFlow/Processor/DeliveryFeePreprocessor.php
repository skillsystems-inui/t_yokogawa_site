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

            /*koko 配送がヤマトの場合、合計金額によって配送タイプを選択する
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
            
            //ヤマトかどうか
            if($Shipping->getShippingDeliveryName() == '配送:ヤマト運輸'){
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
	            
	            //配送方法セット
	            /** @var DeliveryFee $DeliveryFee */
	            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
	                'Delivery' => $TargetDelivery,
	                'Pref' => $Shipping->getPref(),
	            ]);
            }else{
	            /** @var DeliveryFee $DeliveryFee */
	            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
	                'Delivery' => $Shipping->getDelivery(),
	                'Pref' => $Shipping->getPref(),
	            ]);
            }

            $OrderItem = new OrderItem();
            $OrderItem->setProductName($DeliveryFeeType->getName())
                ->setPrice($DeliveryFee->getFee() + $deliveryFeeProduct)
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
