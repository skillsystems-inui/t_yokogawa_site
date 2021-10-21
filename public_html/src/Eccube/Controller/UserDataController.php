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

namespace Eccube\Controller;

use Eccube\Entity\Page;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PointHistoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Knp\Component\Pager\Paginator;

class UserDataController extends AbstractController
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;
    
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var PointHistoryRepository
     */
    protected $pointHistoryRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * UserDataController constructor.
     *
     * @param PageRepository $pageRepository
     * @param OrderRepository $orderRepository
     * @param PointHistoryRepository $pointHistoryRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     */
    public function __construct(
        PageRepository $pageRepository,
        OrderRepository $orderRepository,
        PointHistoryRepository $pointHistoryRepository,
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->orderRepository = $orderRepository;
        $this->pointHistoryRepository = $pointHistoryRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%eccube_user_data_route%/{route}", name="user_data", requirements={"route": "([0-9a-zA-Z_\-]+\/?)+(?<!\/)"})
     */
    public function index(Request $request, $route)
    {
        $session = $this->session;
        
        //会員情報
        $Customer = $this->getUser();
        
        $Page = $this->pageRepository->findOneBy(
            [
                'url' => $route,
                'edit_type' => Page::EDIT_TYPE_USER,
            ]
        );

        if (null === $Page) {
            throw new NotFoundHttpException();
        }

        $file = sprintf('@user_data/%s.twig', $Page->getFileName());

        //アプリ判定
        $is_application = false;
        $current_url = $_SERVER['REQUEST_URI'];
        if(strpos($current_url,'app_')){
        	//アプリの場合
        	$is_application = true;
        	if (!($this->isGranted('IS_AUTHENTICATED_FULLY'))) {
	            log_info('アプリから未ログインのためログイン画面へ遷移させる');
	            //ログイン画面へ遷移(アプリ)
        		header("Location:http://t-yokogawa-com.check-xserver.jp/mypage/login?app_mode");
    			exit();
	        }
        }
        
        //注文一覧取得
        $orders = array();
        if($Customer != null){
        	$orders = $this->orderRepository->getOrdersByCustomer($Customer);
        }
        
        //ポイント一覧取得
        $points = array();
        if($Customer != null){
        	// ポイント履歴取得
            $targetPointHistory = $this->pointHistoryRepository->findOneBy(
                [
                    'Customer' => $Customer,
                ]
            );
            
            //初期値セット
            $points['ec_online'] = 0;
            $points['ec_yoyaku'] = 0;
            $points['app_birth'] = 0;
            $points['shop_honten'] = 0;
            $points['shop_kishiwada'] = 0;
            
            //データがあるならセット
            if($targetPointHistory != null){
            	$points['ec_online'] = $targetPointHistory->getEcOnline();
	            $points['ec_yoyaku'] = $targetPointHistory->getEcYoyaku();
	            $points['app_birth'] = $targetPointHistory->getAppBirth();
	            $points['shop_honten'] = $targetPointHistory->getShopHonten();
	            $points['shop_kishiwada'] = $targetPointHistory->getShopKishiwada();
	            $points['update_date'] = $targetPointHistory->getUpdateDate();
            }
        }
        
        $event = new EventArgs(
            [
                'Page' => $Page,
                'file' => $file,
            ],
            $request
        );
        
        //アプリトップ画面の場合
        if($Page->getFileName() == 'app_top'){
        	//アプリログイン時であればデバイストークンを更新する
	        if($is_application == true){
	        	$deviceToken = $session->get('eccube.device_token');
	        	
	        	//デバイストークン更新
	        	if(strlen($deviceToken) > 0){
				    //通知設定情報更新 デバイストークンをセットする
			        $Customer->setDeviceToken1($deviceToken);
			        $this->entityManager->flush();
			        log_info('デバイストークンをセット完了');
	        	}
	        }
        }
        //.アプリトップ画面の場合
        
        //通知設定画面の場合
        if($Page->getFileName() == 'app_notice'){
        	//ユーザーがログイン時の場合通知設定情報の更新を実行する
            log_info('通知設定開始');
            
            //通知設定タイプ(する/しない)
            $notice_type = 'none';
            if(strpos($current_url,'exec_on')){
            	$notice_type = 'on';//通知設定する
            }else if(strpos($current_url,'exec_off')){
            	$notice_type = 'off';//通知設定しない
            }
            
            //デバイストークン
            $device_token = '123456789';//test
            
			log_info(
	            'customer_notice_set',
	            [
	                'notice_type' => $notice_type,
	                'device_token' => $device_token,
	            ]
	        );
	        
	        //通知設定情報更新
            if($notice_type == 'on'){
            	//通知設定ありのためデバイストークンをセットする
            	$Customer->setDeviceToken1($device_token);
            	$this->entityManager->flush();
            	log_info('通知設定ON終了');
            }else if ($notice_type == 'off'){
            	//通知設定なしのためデバイストークンをセットしない
            	$Customer->setDeviceToken1(null);
            	$this->entityManager->flush();
            	log_info('通知設定OFF終了');
            }else{
            	//更新処理はしない
            	log_info('通知設定表示時');
            }
            
        }
        //.通知設定画面の場合
        
        
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_USER_DATA_INDEX_INITIALIZE, $event);

        return $this->render($file, ['orders' => $orders, 
                                     'order_count' => count($orders),
                                     'points' => $points, 
                                    ]);
    }
}
