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

namespace Eccube\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\PointHistory;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\PointHistoryRepository;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;
use Eccube\Service\PurchaseFlow\Processor\StockReduceProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\StateMachine;

class OrderStateMachine implements EventSubscriberInterface
{
    /**
     * @var StateMachine
     */
    private $machine;

    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var PointHistoryRepository
     */
    private $pointHistoryRepository;

    /**
     * @var PointProcessor
     */
    private $pointProcessor;
    /**
     * @var StockReduceProcessor
     */
    private $stockReduceProcessor;

    public function __construct(StateMachine $_orderStateMachine, OrderStatusRepository $orderStatusRepository, PointProcessor $pointProcessor, StockReduceProcessor $stockReduceProcessor, PointHistoryRepository $pointHistoryRepository, EntityManagerInterface $entityManager)
    {
        $this->machine = $_orderStateMachine;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->pointProcessor = $pointProcessor;
        $this->stockReduceProcessor = $stockReduceProcessor;
        $this->pointHistoryRepository = $pointHistoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * 指定ステータスに遷移.
     *
     * @param Order $Order 受注
     * @param OrderStatus $OrderStatus 遷移先ステータス
     */
    public function apply(Order $Order, OrderStatus $OrderStatus)
    {
        $context = $this->newContext($Order);
        $transition = $this->getTransition($context, $OrderStatus);
        if ($transition) {
            $this->machine->apply($context, $transition->getName());
        } else {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * 指定ステータスに遷移できるかどうかを判定.
     *
     * @param Order $Order 受注
     * @param OrderStatus $OrderStatus 遷移先ステータス
     *
     * @return boolean 指定ステータスに遷移できる場合はtrue
     */
    public function can(Order $Order, OrderStatus $OrderStatus)
    {
        return !is_null($this->getTransition($this->newContext($Order), $OrderStatus));
    }

    private function getTransition(OrderStateMachineContext $context, OrderStatus $OrderStatus)
    {
        $transitions = $this->machine->getEnabledTransitions($context);
        foreach ($transitions as $t) {
            if (in_array($OrderStatus->getId(), $t->getTos())) {
                return $t;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.order.completed' => ['onCompleted'],
            'workflow.order.transition.pay' => ['updatePaymentDate'],
            'workflow.order.transition.cancel' => [['rollbackStock'], ['rollbackUsePoint']],
            'workflow.order.transition.back_to_in_progress' => [['commitStock'], ['commitUsePoint']],
            'workflow.order.transition.ship' => [['commitAddPoint']],
            'workflow.order.transition.return' => [['rollbackUsePoint'], ['rollbackAddPoint']],
            'workflow.order.transition.cancel_return' => [['commitUsePoint'], ['commitAddPoint']],
        ];
    }

    /*
     * Event handlers.
     */

    /**
     * 入金日を更新する.
     *
     * @param Event $event
     */
    public function updatePaymentDate(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Order->setPaymentDate(new \DateTime());
    }

    /**
     * 会員の保有ポイントを減らす.
     *
     * @param Event $event
     *
     * @throws PurchaseFlow\PurchaseException
     */
    public function commitUsePoint(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->pointProcessor->prepare($Order, new PurchaseContext());
    }

    /**
     * 利用ポイントを会員に戻す.
     *
     * @param Event $event
     */
    public function rollbackUsePoint(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->pointProcessor->rollback($Order, new PurchaseContext());
    }

    /**
     * 在庫を減らす.
     *
     * @param Event $event
     *
     * @throws PurchaseFlow\PurchaseException
     */
    public function commitStock(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->stockReduceProcessor->prepare($Order, new PurchaseContext());
    }

    /**
     * 在庫を戻す.
     *
     * @param Event $event
     */
    public function rollbackStock(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->stockReduceProcessor->rollback($Order, new PurchaseContext());
    }

    /**
     * 会員に加算ポイントを付与する.
     *
     * @param Event $event
     */
    public function commitAddPoint(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Customer = $Order->getCustomer();
        if ($Customer) {
        
        log_info(
            '__testLog0poooooooooooooooooooooooooooooooooooooooo!',
            [
                'uketoriType!' => $Order->getUketoriType(),
                'point!' => intval($Order->getAddPoint()),
            ]
        );
        
            $Customer->setPoint(intval($Customer->getPoint()) + intval($Order->getAddPoint()));
            
            //----- ポイント履歴にも反映する -----
            // 受け取り方法(店舗受け取りかどうか)(getUketoriType 1:店舗受取 2:取り寄せ) 
            $UketoriYoyaku = $Order->getUketoriType() == 2 ? false : true;
            
            // ポイント履歴取得
            $targetPointHistory = $this->pointHistoryRepository->findOneBy(
                [
                    'Customer' => $Customer,
                ]
            );
            
            $now = new \DateTime("now");//現在日時
            if($targetPointHistory != null){
            	//データがあればポイント更新
            	if($UketoriYoyaku == true){
            		//店舗予約の場合
            		$current_point = intval($targetPointHistory->getEcYoyaku());
			        $targetPointHistory->setEcYoyaku($current_point + intval($Order->getAddPoint()));
            	}else{
            		//オンライン購入の場合
            		$current_point = intval($targetPointHistory->getEcOnline());
            		$targetPointHistory->setEcOnline($current_point + intval($Order->getAddPoint()));
            	}
            	
            	$targetPointHistory->setUpdateDate($now);
            	
            	$this->entityManager->persist($targetPointHistory);
            	
            }else{
            	//データ未作成なら新規作成
            	
            	/* @var PointHistory $PointHistory */
	            $PointHistory = new PointHistory();
		        $PointHistory->setCustomer($Customer);
		        
		        $PointHistory->setSum(0);
		        $PointHistory->setAppBirth(0);
		        $PointHistory->setShopHonten(0);
		        $PointHistory->setShopKishiwada(0);
		        
		        if($UketoriYoyaku == true){
            		//店舗予約の場合
            		$PointHistory->setEcOnline(0);
			        $PointHistory->setEcYoyaku(intval($Order->getAddPoint()));
            	}else{
            		//オンライン購入の場合
            		$PointHistory->setEcOnline(intval($Order->getAddPoint()));
			        $PointHistory->setEcYoyaku(0);
            	}
		        
		        $PointHistory->setCreateDate($now);
		        $PointHistory->setUpdateDate($now);
		        $PointHistory->setAvailable(1);
		        
		        $this->entityManager->persist($PointHistory);
            }
            
            
        }
    }

    /**
     * 会員に付与した加算ポイントを取り消す.
     *
     * @param Event $event
     */
    public function rollbackAddPoint(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Customer = $Order->getCustomer();
        if ($Customer) {
            $Customer->setPoint(intval($Customer->getPoint()) - intval($Order->getAddPoint()));
        }
    }

    /**
     * 受注ステータスを再設定.
     * {@link StateMachine}によって遷移が終了したときには{@link Order#OrderStatus}のidが変更されるだけなのでOrderStatusを設定し直す.
     *
     * @param Event $event
     */
    public function onCompleted(Event $event)
    {
        /** @var $context OrderStateMachineContext */
        $context = $event->getSubject();
        $Order = $context->getOrder();
        $CompletedOrderStatus = $this->orderStatusRepository->find($context->getStatus());
        $Order->setOrderStatus($CompletedOrderStatus);
    }

    private function newContext(Order $Order)
    {
        return new OrderStateMachineContext((string) $Order->getOrderStatus()->getId(), $Order);
    }
}

class OrderStateMachineContext
{
    /** @var string */
    private $status;

    /** @var Order */
    private $Order;

    /**
     * OrderStateMachineContext constructor.
     *
     * @param string $status
     * @param Order $Order
     */
    public function __construct($status, Order $Order)
    {
        $this->status = $status;
        $this->Order = $Order;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->Order;
    }
}
