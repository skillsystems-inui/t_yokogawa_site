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
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * UserDataController constructor.
     *
     * @param PageRepository $pageRepository
     * @param OrderRepository $orderRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     */
    public function __construct(
        PageRepository $pageRepository,
        OrderRepository $orderRepository,
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->orderRepository = $orderRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * @Route("/%eccube_user_data_route%/{route}", name="user_data", requirements={"route": "([0-9a-zA-Z_\-]+\/?)+(?<!\/)"})
     */
    public function index(Request $request, $route)
    {
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
        $current_url = $_SERVER['REQUEST_URI'];
        if(strpos($current_url,'app_')){
        	//アプリの場合
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
        
        $event = new EventArgs(
            [
                'Page' => $Page,
                'file' => $file,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_USER_DATA_INDEX_INITIALIZE, $event);

        return $this->render($file, ['orders' => $orders, 
                                     'order_count' => count($orders),
                                    ]);
    }
}
