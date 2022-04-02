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

namespace Eccube\Repository;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Eccube\Doctrine\Query\Queries;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Util\StringUtil;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * OrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderRepository extends AbstractRepository
{
    /**
     * @var Queries
     */
    protected $queries;

    /**
     * OrderRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param Queries $queries
     */
    public function __construct(RegistryInterface $registry, Queries $queries)
    {
        parent::__construct($registry, Order::class);
        $this->queries = $queries;
    }

    /**
     * @param int $orderId
     * @param OrderStatus $Status
     */
    public function changeStatus($orderId, \Eccube\Entity\Master\OrderStatus $Status)
    {
        $Order = $this
            ->find($orderId)
            ->setOrderStatus($Status)
        ;

        switch ($Status->getId()) {
            case '6': // 入金済へ
                $Order->setPaymentDate(new \DateTime());
                break;
        }

        $em = $this->getEntityManager();
        $em->persist($Order);
        $em->flush();
    }

    /**
     * @param  array        $searchData
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderBySearchDataForAdmin($searchData)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o, s')
            ->addSelect('oi', 'pref')
            ->leftJoin('o.OrderItems', 'oi')
            ->leftJoin('o.Pref', 'pref')
            ->innerJoin('o.Shippings', 's')
            ->leftJoin('s.Delivery', 'dv');


        // order_id_start
        if (isset($searchData['order_id']) && StringUtil::isNotBlank($searchData['order_id'])) {
            $qb
                ->andWhere('o.id = :order_id')
                ->setParameter('order_id', $searchData['order_id']);
        }

        // order_no
        if (isset($searchData['order_no']) && StringUtil::isNotBlank($searchData['order_no'])) {
            $qb
                ->andWhere('o.order_no = :order_no')
                ->setParameter('order_no', $searchData['order_no']);
        }

        // order_id_start
        if (isset($searchData['order_id_start']) && StringUtil::isNotBlank($searchData['order_id_start'])) {
            $qb
                ->andWhere('o.id >= :order_id_start')
                ->setParameter('order_id_start', $searchData['order_id_start']);
        }
        // multi
        if (isset($searchData['multi']) && StringUtil::isNotBlank($searchData['multi'])) {
            
            
            log_info(
	            '受注検索　注文番号・お名前・会社名・メールアドレス・電話番号・店舗A',
	            [
	                'searchData_multi' => $searchData['multi'],
	            ]
	        );
            
            
            $multi = preg_match('/^\d{0,10}$/', $searchData['multi']) ? $searchData['multi'] : null;
            
            
            
            log_info(
	            '受注検索　注文番号・お名前・会社名・メールアドレス・電話番号・店舗X',
	            [
	                'searchData_multi' => $searchData['multi'],
	            ]
	        );
            
            
            if ($multi && $multi > '2147483647' && $this->isPostgreSQL()) {
                $multi = null;
            }
            
            log_info(
	            '受注検索　注文番号・お名前・会社名・メールアドレス・電話番号・店舗',
	            [
	                'searchData_multi' => $searchData['multi'],
	            ]
	        );
            
            //20220327店舗名を検索条件指定する↓
            // $searchData['multi'] が「本店」または「岸和田店」のとき、店舗指定する
            // 対象 dtb_shipping のdelivery_id
            //     <dtb_delivery>
            //     idが3　店舗受取：和泉中央本店
            //     idが4　店舗受取：岸和田店
            if ($searchData['multi'] == '本店' || $searchData['multi'] == '岸和田店') {
                
                //id指定
                if ($searchData['multi'] == '岸和田店'){
                	$multi = 4;//岸和田店
                }else{
                	$multi = 3;//本店
                }
                
                $qb
	                ->andWhere('dv.id = :multi ')
	                ->setParameter('multi', $multi);
            } else {
            //20220327店舗名を検索条件指定する↑
	            $qb
	                ->andWhere('o.id = :multi OR o.name01 LIKE :likemulti OR o.name02 LIKE :likemulti OR '.
	                            'o.kana01 LIKE :likemulti OR o.kana02 LIKE :likemulti OR o.company_name LIKE :likemulti OR '.
	                            'o.order_no LIKE :likemulti OR o.email LIKE :likemulti OR o.phone_number LIKE :likemulti')
	                ->setParameter('multi', $multi)
	                ->setParameter('likemulti', '%'.$searchData['multi'].'%');
            }//20220327店舗名を検索条件指定する用
        }

        // order_id_end
        if (isset($searchData['order_id_end']) && StringUtil::isNotBlank($searchData['order_id_end'])) {
            $qb
                ->andWhere('o.id <= :order_id_end')
                ->setParameter('order_id_end', $searchData['order_id_end']);
        }

        // status
        $filterStatus = false;
        if (!empty($searchData['status']) && count($searchData['status'])) {
            $qb
                ->andWhere($qb->expr()->in('o.OrderStatus', ':status'))
                ->setParameter('status', $searchData['status']);
            $filterStatus = true;
        }

        if (!$filterStatus) {
            // 購入処理中, 決済処理中は検索対象から除外
            $qb->andWhere($qb->expr()->notIn('o.OrderStatus', ':status'))
                ->setParameter('status', [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        }

        // company_name
        if (isset($searchData['company_name']) && StringUtil::isNotBlank($searchData['company_name'])) {
            $qb
                ->andWhere('o.company_name LIKE :company_name')
                ->setParameter('company_name', '%'.$searchData['company_name'].'%');
        }

        // name
        if (isset($searchData['name']) && StringUtil::isNotBlank($searchData['name'])) {
            $qb
                ->andWhere('CONCAT(o.name01, o.name02) LIKE :name')
                ->setParameter('name', '%'.$searchData['name'].'%');
        }

        // kana
        if (isset($searchData['kana']) && StringUtil::isNotBlank($searchData['kana'])) {
            $qb
                ->andWhere('CONCAT(o.kana01, o.kana02) LIKE :kana')
                ->setParameter('kana', '%'.$searchData['kana'].'%');
        }

        // email
        if (isset($searchData['email']) && StringUtil::isNotBlank($searchData['email'])) {
            $qb
                ->andWhere('o.email like :email')
                ->setParameter('email', '%'.$searchData['email'].'%');
        }

        // tel
        if (isset($searchData['phone_number']) && StringUtil::isNotBlank($searchData['phone_number'])) {
            $tel = preg_replace('/[^0-9]/ ', '', $searchData['phone_number']);
            $qb
                ->andWhere('o.phone_number LIKE :phone_number')
                ->setParameter('phone_number', '%'.$tel.'%');
        }

        // sex
        if (!empty($searchData['sex']) && count($searchData['sex']) > 0) {
            $qb
                ->andWhere($qb->expr()->in('o.Sex', ':sex'))
                ->setParameter('sex', $searchData['sex']->toArray());
        }

        // payment
        if (!empty($searchData['payment']) && count($searchData['payment'])) {
            $payments = [];
            foreach ($searchData['payment'] as $payment) {
                $payments[] = $payment->getId();
            }
            $qb
                ->leftJoin('o.Payment', 'p')
                ->andWhere($qb->expr()->in('p.id', ':payments'))
                ->setParameter('payments', $payments);
        }

        // oreder_date
        if (!empty($searchData['order_datetime_start']) && $searchData['order_datetime_start']) {
            $date = $searchData['order_datetime_start'];
            $qb
                ->andWhere('o.order_date >= :order_date_start')
                ->setParameter('order_date_start', $date);
        } elseif (!empty($searchData['order_date_start']) && $searchData['order_date_start']) {
            $date = $searchData['order_date_start'];
            $qb
                ->andWhere('o.order_date >= :order_date_start')
                ->setParameter('order_date_start', $date);
        }

        if (!empty($searchData['order_datetime_end']) && $searchData['order_datetime_end']) {
            $date = $searchData['order_datetime_end'];
            $qb
                ->andWhere('o.order_date < :order_date_end')
                ->setParameter('order_date_end', $date);
        } elseif (!empty($searchData['order_date_end']) && $searchData['order_date_end']) {
            $date = clone $searchData['order_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('o.order_date < :order_date_end')
                ->setParameter('order_date_end', $date);
        }

        // payment_date
        if (!empty($searchData['payment_datetime_start']) && $searchData['payment_datetime_start']) {
            $date = $searchData['payment_datetime_start'];
            $qb
                ->andWhere('o.payment_date >= :payment_date_start')
                ->setParameter('payment_date_start', $date);
        } elseif (!empty($searchData['payment_date_start']) && $searchData['payment_date_start']) {
            $date = $searchData['payment_date_start'];
            $qb
                ->andWhere('o.payment_date >= :payment_date_start')
                ->setParameter('payment_date_start', $date);
        }

        if (!empty($searchData['payment_datetime_end']) && $searchData['payment_datetime_end']) {
            $date = $searchData['payment_datetime_end'];
            $qb
                ->andWhere('o.payment_date < :payment_date_end')
                ->setParameter('payment_date_end', $date);
        } elseif (!empty($searchData['payment_date_end']) && $searchData['payment_date_end']) {
            $date = clone $searchData['payment_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('o.payment_date < :payment_date_end')
                ->setParameter('payment_date_end', $date);
        }

        // update_date
        if (!empty($searchData['update_datetime_start']) && $searchData['update_datetime_start']) {
            $date = $searchData['update_datetime_start'];
            $qb
                ->andWhere('s.shipping_date >= :update_date_start')
                ->setParameter('update_date_start', $date);
        } elseif (!empty($searchData['update_date_start']) && $searchData['update_date_start']) {
            $date = $searchData['update_date_start'];
            $qb
                ->andWhere('s.shipping_date >= :update_date_start')
                ->setParameter('update_date_start', $date);
        }

        if (!empty($searchData['update_datetime_end']) && $searchData['update_datetime_end']) {
            $date = $searchData['update_datetime_end'];
            $qb
                ->andWhere('s.shipping_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        } elseif (!empty($searchData['update_date_end']) && $searchData['update_date_end']) {
            $date = clone $searchData['update_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('s.shipping_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        }

        // payment_total
        if (isset($searchData['payment_total_start']) && StringUtil::isNotBlank($searchData['payment_total_start'])) {
            $qb
                ->andWhere('o.payment_total >= :payment_total_start')
                ->setParameter('payment_total_start', $searchData['payment_total_start']);
        }
        if (isset($searchData['payment_total_end']) && StringUtil::isNotBlank($searchData['payment_total_end'])) {
            $qb
                ->andWhere('o.payment_total <= :payment_total_end')
                ->setParameter('payment_total_end', $searchData['payment_total_end']);
        }

        // buy_product_name
        if (isset($searchData['buy_product_name']) && StringUtil::isNotBlank($searchData['buy_product_name'])) {
            $qb
                ->andWhere('oi.product_name LIKE :buy_product_name')
                ->setParameter('buy_product_name', '%'.$searchData['buy_product_name'].'%');
        }

        // 発送メール送信/未送信.
        if (isset($searchData['shipping_mail']) && $count = count($searchData['shipping_mail'])) {
            // 送信済/未送信両方にチェックされている場合は検索条件に追加しない
            if ($count < 2) {
                $checked = current($searchData['shipping_mail']);
                if ($checked == Shipping::SHIPPING_MAIL_UNSENT) {
                    // 未送信
                    $qb
                        ->andWhere('s.mail_send_date IS NULL');
                } elseif ($checked == Shipping::SHIPPING_MAIL_SENT) {
                    // 送信
                    $qb
                        ->andWhere('s.mail_send_date IS NOT NULL');
                }
            }
        }
        
        // 20220403注文場所指定
        if (isset($searchData['family_main']) && $count = count($searchData['family_main'])) {
            // ECで注文/店舗で注文両方にチェックされている場合は検索条件に追加しない
            if ($count < 2) {
                $checked = current($searchData['family_main']);
                if ($checked == 0) {
                    // ECで注文
                    $qb
                        ->andWhere('o.uketori_type IS NOT NULL');
                } elseif ($checked == 1) {
                    // 店舗で注文
                    $qb
                        ->andWhere('o.uketori_type IS NULL');                   
                }
            }
        }

        // 送り状番号.
        if (!empty($searchData['tracking_number'])) {
            $qb
                ->andWhere('s.tracking_number = :tracking_number')
                ->setParameter('tracking_number', $searchData['tracking_number']);
        }

        // お届け予定日(Shipping.delivery_date)
        if (!empty($searchData['shipping_delivery_datetime_start']) && $searchData['shipping_delivery_datetime_start']) {
            $date = $searchData['shipping_delivery_datetime_start'];
            $qb
                ->andWhere('s.shipping_delivery_date >= :shipping_delivery_date_start')
                ->setParameter('shipping_delivery_date_start', $date);
        } elseif (!empty($searchData['shipping_delivery_date_start']) && $searchData['shipping_delivery_date_start']) {
            $date = $searchData['shipping_delivery_date_start'];
            $qb
                ->andWhere('s.shipping_delivery_date >= :shipping_delivery_date_start')
                ->setParameter('shipping_delivery_date_start', $date);
        }

        if (!empty($searchData['shipping_delivery_datetime_end']) && $searchData['shipping_delivery_datetime_end']) {
            $date = $searchData['shipping_delivery_datetime_end'];
            $qb
                ->andWhere('s.shipping_delivery_date < :shipping_delivery_date_end')
                ->setParameter('shipping_delivery_date_end', $date);
        } elseif (!empty($searchData['shipping_delivery_date_end']) && $searchData['shipping_delivery_date_end']) {
            $date = clone $searchData['shipping_delivery_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('s.shipping_delivery_date < :shipping_delivery_date_end')
                ->setParameter('shipping_delivery_date_end', $date);
        }

        // Order By
        $qb->orderBy('o.update_date', 'DESC');
        $qb->addorderBy('o.id', 'DESC');

        return $this->queries->customize(QueryKey::ORDER_SEARCH_ADMIN, $qb, $searchData);
    }

    /**
     * @param  \Eccube\Entity\Customer $Customer
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderByCustomer(\Eccube\Entity\Customer $Customer)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.Customer = :Customer')
            ->setParameter('Customer', $Customer);

        // Order By
        $qb->addOrderBy('o.id', 'DESC');

        return $this->queries->customize(QueryKey::ORDER_SEARCH_BY_CUSTOMER, $qb, ['customer' => $Customer]);
    }
    
    /**
     * @param  \Eccube\Entity\Customer $Customer
     *
     * @return array
     */
    public function getOrdersByCustomer(\Eccube\Entity\Customer $Customer)
    {
        $orders = $this->createQueryBuilder('o')
		          ->where('o.Customer = :Customer')
		          ->setParameter('Customer', $Customer)
		          ->addOrderBy('o.id', 'DESC')
		          ->getQuery()
                  ->getResult();
            
        return $orders;
    }

    /**
     * ステータスごとの受注件数を取得する.
     *
     * @param integer $OrderStatusOrId
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByOrderStatus($OrderStatusOrId)
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COALESCE(COUNT(o.id), 0)')
            ->where('o.OrderStatus = :OrderStatus')
            ->setParameter('OrderStatus', $OrderStatusOrId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 会員の購入金額, 購入回数, 初回購入日, 最終購入費を更新する
     *
     * @param Customer $Customer
     * @param array $OrderStatuses
     */
    public function updateOrderSummary(Customer $Customer, array $OrderStatuses = [OrderStatus::NEW, OrderStatus::PAID, OrderStatus::DELIVERED, OrderStatus::IN_PROGRESS])
    {
        try {
            $result = $this->createQueryBuilder('o')
                ->select('COUNT(o.id) AS buy_times, SUM(o.total) AS buy_total, MIN(o.id) AS first_order_id, MAX(o.id) AS last_order_id')
                ->where('o.Customer = :Customer')
                ->andWhere('o.OrderStatus in (:OrderStatuses)')
                ->setParameter('Customer', $Customer)
                ->setParameter('OrderStatuses', $OrderStatuses)
                ->groupBy('o.Customer')
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            // 受注データが存在しなければ初期化
            $Customer->setFirstBuyDate(null);
            $Customer->setLastBuyDate(null);
            $Customer->setBuyTimes(0);
            $Customer->setBuyTotal(0);

            return;
        }

        $FirstOrder = $this->find(['id' => $result['first_order_id']]);
        $LastOrder = $this->find(['id' => $result['last_order_id']]);

        $Customer->setBuyTimes($result['buy_times']);
        $Customer->setBuyTotal($result['buy_total']);
        $Customer->setFirstBuyDate($FirstOrder->getOrderDate());
        $Customer->setLastBuyDate($LastOrder->getOrderDate());
    }
    
    /**
     * 注文をスマレジIDで検索する.
     *
     * @param $smaregi_id
     *
     * @return null|Customer 見つからない場合はnullを返す.
     */
    public function getRegularCustomerByEmail($smaregi_id)
    {
        return $this->findOneBy([
            'smaregi_id' => $smaregi_id,
        ]);
    }
    
    
    /**
     * 注文を注文日で検索する.
     *
     * @param $order_date
     *
     * @return null|Order 見つからない場合はnullを返す.
     */
    public function getOrderByOrderdate($order_date)
    {
        return $this->findOneBy([
            'order_date' => $order_date,
        ]);
    }
}
