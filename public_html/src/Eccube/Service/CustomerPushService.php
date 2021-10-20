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

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Util\EntityUtil;
use Eccube\Util\FormUtil;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerPushService
{
    /**
     * @var resource
     */
    protected $fp;

    /**
     * @var boolean
     */
    protected $closed = false;

    /**
     * @var \Closure
     */
    protected $convertEncodingCallBack;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var QueryBuilder;
     */
    protected $qb;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var CsvType
     */
    protected $CsvType;

    /**
     * @var Csv[]
     */
    protected $Csvs;

    /**
     * @var CsvRepository
     */
    protected $csvRepository;

    /**
     * @var CsvTypeRepository
     */
    protected $csvTypeRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * CustomerPushService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CsvRepository $csvRepository
     * @param CsvTypeRepository $csvTypeRepository
     * @param OrderRepository $orderRepository
     * @param CustomerRepository $customerRepository
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CsvRepository $csvRepository,
        CsvTypeRepository $csvTypeRepository,
        OrderRepository $orderRepository,
        ShippingRepository $shippingRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository,
        EccubeConfig $eccubeConfig,
        FormFactoryInterface $formFactory
    ) {
        $this->entityManager = $entityManager;
        $this->csvRepository = $csvRepository;
        $this->csvTypeRepository = $csvTypeRepository;
        $this->orderRepository = $orderRepository;
        $this->shippingRepository = $shippingRepository;
        $this->customerRepository = $customerRepository;
        $this->eccubeConfig = $eccubeConfig;
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->eccubeConfig = $config;
    }

    /**
     * @param CsvRepository $csvRepository
     */
    public function setCsvRepository(CsvRepository $csvRepository)
    {
        $this->csvRepository = $csvRepository;
    }

    /**
     * @param CsvTypeRepository $csvTypeRepository
     */
    public function setCsvTypeRepository(CsvTypeRepository $csvTypeRepository)
    {
        $this->csvTypeRepository = $csvTypeRepository;
    }

    /**
     * @param OrderRepository $orderRepository
     */
    public function setOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param CustomerRepository $customerRepository
     */
    public function setCustomerRepository(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param ProductRepository $productRepository
     */
    public function setProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param QueryBuilder $qb
     */
    public function setExportQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * Csv種別からServiceの初期化を行う.
     *
     * @param $CsvType|integer
     */
    public function initCsvType($CsvType)
    {
        if ($CsvType instanceof CsvType) {
            $this->CsvType = $CsvType;
        } else {
            $this->CsvType = $this->csvTypeRepository->find($CsvType);
        }

        $criteria = [
            'CsvType' => $CsvType,
            'enabled' => true,
        ];
        $orderBy = [
            'sort_no' => 'ASC',
        ];
        $this->Csvs = $this->csvRepository->findBy($criteria, $orderBy);
    }

    /**
     * @return Csv[]
     */
    public function getCsvs()
    {
        return $this->Csvs;
    }

    /**
     * ヘッダ行を出力する.
     * このメソッドを使う場合は, 事前にinitCsvType($CsvType)で初期化しておく必要がある.
     */
    public function exportHeader()
    {
        if (is_null($this->CsvType) || is_null($this->Csvs)) {
            throw new \LogicException('init csv type incomplete.');
        }

        $row = [];
        foreach ($this->Csvs as $Csv) {
            $row[] = $Csv->getDispName();
        }

        $this->fopen();
        $this->fputcsv($row);
        $this->fclose();
    }

    /**
     * クエリビルダにもとづいた会員データに対してプッシュ通知を行う.
     * このメソッドを使う場合は, 事前にsetExportQueryBuilder($qb)で出力対象のクエリビルダをわたしておく必要がある.
     *
     * @param \Closure $closure
     */
    public function pushData(\Closure $closure)
    {
        if (is_null($this->qb) || is_null($this->entityManager)) {
            throw new \LogicException('query builder not set.');
        }

        $this->fopen();

        $query = $this->qb->getQuery();
        foreach ($query->getResult() as $perCustomer) {
            
            
            if($perCustomer != null){
	            log_info(
		            'test_PUSH_YOURER',
		            [
		                'perCustomer_id' => $perCustomer->getId(),
		                'perCustomer_name01' => $perCustomer->getName01(),
		                'perCustomer_name02' => $perCustomer->getName02(),
		                'perDevice_Token1' => $perCustomer->getDeviceToken1(),
		            ]
		        );
		        
		        //デバイストークンがある場合のみプッシュ通知を実行する
		        if($perCustomer->getDeviceToken1() != null){
		        	$decvice_token = $perCustomer->getDeviceToken1();
		        	$this->pushNotification($decvice_token);
		        }
	            
	            /*
	            $closure($perCustomer, $this);
	            $this->entityManager->detach($perCustomer);
	            $query->free();
	            flush();
	            */
            }
        }

        $this->fclose();
    }
    
    function pushNotification($decvice_token) {
	  $url = 'https://fcm.googleapis.com/fcm/send';
	  $headers = array(
	    'Content-Type: application/json',
	    'Authorization: key='."AAAANGv8iJI:APA91bFw4naNvCmBXUJE2tz6HnDedxyDnMtwlGFsH0rp3QVXByUl7AyW9OMKjKrTb5CHAHPd-GLUlckbJwIKrJeQQB6HStuomFJ1QhFm8I_MKFwWZnptqphmrkoTUy27Fa-ZLzVhyBqd",
	  );
	 
	  $posts = array(
	    'registration_ids' => [$decvice_token],
	    'data' => array(
	      'message' => "Message本文",
	    ),
	    "notification" => [
	      "title" => "Messageタイトル",
	      "body" => "Message本文",
	      "badge" => 1, // 通知を送った際に、アプリのアイコンに表示するバッチ
	      "sound" => "default", // 通知を送った際に、音を出すかの設定
	    ],
	  );
	 
	  // curl処理
	  $ch = curl_init($url);                                     // 初期化
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);               // サーバー証明書の検証を行わないようにする
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);            // ヘッダー定義
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);            // 返り値を文字列で返すように
	  curl_setopt($ch, CURLOPT_POST, TRUE);                      // POSTで送信する & データあり
	  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($posts)); // ＰＯＳＴするデータ(JSON形式で渡す必要あり)
	 
	  $result = curl_exec($ch); // 送信 & 帰って来た値はよしなに
	  
	  log_info(
            '__testLogYYYYYYYYYYY',
            [
                'result' => $result,
            ]
        );
	  
	  curl_close($ch);        // セッションを閉じる 
	}

    /**
     * CSV出力項目と比較し, 合致するデータを返す.
     *
     * @param \Eccube\Entity\Csv $Csv
     * @param $entity
     *
     * @return string|null
     */
    public function getData(Csv $Csv, $entity)
    {
        // エンティティ名が一致するかどうかチェック.
        $csvEntityName = str_replace('\\\\', '\\', $Csv->getEntityName());
        $entityName = ClassUtils::getClass($entity);
        if ($csvEntityName !== $entityName) {
            return null;
        }

        // カラム名がエンティティに存在するかどうかをチェック.
        if (!$entity->offsetExists($Csv->getFieldName())) {
            return null;
        }

        // データを取得.
        $data = $entity->offsetGet($Csv->getFieldName());

        // one to one の場合は, dtb_csv.reference_field_name, 合致する結果を取得する.
        if ($data instanceof \Eccube\Entity\AbstractEntity) {
            if (EntityUtil::isNotEmpty($data)) {
                return $data->offsetGet($Csv->getReferenceFieldName());
            }
        } elseif ($data instanceof \Doctrine\Common\Collections\Collection) {
            // one to manyの場合は, カンマ区切りに変換する.
            $array = [];
            foreach ($data as $elem) {
                if (EntityUtil::isNotEmpty($elem)) {
                    $array[] = $elem->offsetGet($Csv->getReferenceFieldName());
                }
            }

            return implode($this->eccubeConfig['eccube_csv_export_multidata_separator'], $array);
        } elseif ($data instanceof \DateTime) {
            // datetimeの場合は文字列に変換する.
            return $data->format($this->eccubeConfig['eccube_csv_export_date_format']);
        } else {
            // スカラ値の場合はそのまま.
            return $data;
        }

        return null;
    }

    /**
     * 文字エンコーディングの変換を行うコールバック関数を返す.
     *
     * @return \Closure
     */
    public function getConvertEncodingCallback()
    {
        $config = $this->eccubeConfig;

        return function ($value) use ($config) {
            return mb_convert_encoding(
                (string) $value, $config['eccube_csv_export_encoding'], 'UTF-8'
            );
        };
    }

    public function fopen()
    {
        if (is_null($this->fp) || $this->closed) {
            $this->fp = fopen('php://output', 'w');
        }
    }

    /**
     * @param $row
     */
    public function fputcsv($row)
    {
        if (is_null($this->convertEncodingCallBack)) {
            $this->convertEncodingCallBack = $this->getConvertEncodingCallback();
        }

        fputcsv($this->fp, array_map($this->convertEncodingCallBack, $row), $this->eccubeConfig['eccube_csv_export_separator']);
    }

    public function fclose()
    {
        if (!$this->closed) {
            fclose($this->fp);
            $this->closed = true;
        }
    }

    /**
     * 受注検索用のクエリビルダを返す.
     *
     * @param Request $request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderQueryBuilder(Request $request)
    {
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchOrderType::class);
        $searchForm = $builder->getForm();

        $viewData = $session->get('eccube.admin.order.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

        // 受注データのクエリビルダを構築.
        $qb = $this->orderRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }

    /**
     * 会員検索用のクエリビルダを返す.
     *
     * @param Request $request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCustomerQueryBuilder(Request $request)
    {
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);
        $searchForm = $builder->getForm();

        $viewData = $session->get('eccube.admin.customer.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

        // 会員データのクエリビルダを構築.
        $qb = $this->customerRepository
            ->getQueryBuilderBySearchData($searchData);

        return $qb;
    }

    /**
     * 商品検索用のクエリビルダを返す.
     *
     * @param Request $request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductQueryBuilder(Request $request)
    {
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);
        $searchForm = $builder->getForm();

        $viewData = $session->get('eccube.admin.product.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

        // 商品データのクエリビルダを構築.
        $qb = $this->productRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }
}
