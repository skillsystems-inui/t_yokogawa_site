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
        
        //������
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

        //�A�v������
        $is_application = false;
        $current_url = $_SERVER['REQUEST_URI'];
        if(strpos($current_url,'app_')){
        	//�A�v���̏ꍇ
        	$is_application = true;
        	if (!($this->isGranted('IS_AUTHENTICATED_FULLY'))) {
	            log_info('�A�v�����疢���O�C���̂��߃��O�C����ʂ֑J�ڂ�����');
	            //���O�C����ʂ֑J��(�A�v��)
        		header("Location:http://t-yokogawa-com.check-xserver.jp/mypage/login?app_mode");
    			exit();
	        }
        }
        
        //�����ꗗ�擾
        $orders = array();
        if($Customer != null){
        	$orders = $this->orderRepository->getOrdersByCustomer($Customer);
        }
        
        //�|�C���g�ꗗ�擾
        $points = array();
        if($Customer != null){
        	// �|�C���g�����擾
            $targetPointHistory = $this->pointHistoryRepository->findOneBy(
                [
                    'Customer' => $Customer,
                ]
            );
            
            //�����l�Z�b�g
            $points['ec_online'] = 0;
            $points['ec_yoyaku'] = 0;
            $points['app_birth'] = 0;
            $points['shop_honten'] = 0;
            $points['shop_kishiwada'] = 0;
            
            //�f�[�^������Ȃ�Z�b�g
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
        
        //�A�v���g�b�v��ʂ̏ꍇ
        if($Page->getFileName() == 'app_top'){
        	//�A�v�����O�C�����ł���΃f�o�C�X�g�[�N�����X�V����
	        if($is_application == true){
	        	$deviceToken = $session->get('eccube.device_token');
	        	
	        	//�f�o�C�X�g�[�N���X�V
	        	if(strlen($deviceToken) > 0){
				    //�ʒm�ݒ���X�V �f�o�C�X�g�[�N�����Z�b�g����
			        $Customer->setDeviceToken1($deviceToken);
			        $this->entityManager->flush();
			        log_info('�f�o�C�X�g�[�N�����Z�b�g����');
	        	}
	        }
        }
        //.�A�v���g�b�v��ʂ̏ꍇ
        
        //�ʒm�ݒ��ʂ̏ꍇ
        if($Page->getFileName() == 'app_notice'){
        	//���[�U�[�����O�C�����̏ꍇ�ʒm�ݒ���̍X�V�����s����
            log_info('�ʒm�ݒ�J�n');
            
            //�ʒm�ݒ�^�C�v(����/���Ȃ�)
            $notice_type = 'none';
            if(strpos($current_url,'exec_on')){
            	$notice_type = 'on';//�ʒm�ݒ肷��
            }else if(strpos($current_url,'exec_off')){
            	$notice_type = 'off';//�ʒm�ݒ肵�Ȃ�
            }
            
            //�f�o�C�X�g�[�N��
            $device_token = '123456789';//test
            
			log_info(
	            'customer_notice_set',
	            [
	                'notice_type' => $notice_type,
	                'device_token' => $device_token,
	            ]
	        );
	        
	        //�ʒm�ݒ���X�V
            if($notice_type == 'on'){
            	//�ʒm�ݒ肠��̂��߃f�o�C�X�g�[�N�����Z�b�g����
            	$Customer->setDeviceToken1($device_token);
            	$this->entityManager->flush();
            	log_info('�ʒm�ݒ�ON�I��');
            }else if ($notice_type == 'off'){
            	//�ʒm�ݒ�Ȃ��̂��߃f�o�C�X�g�[�N�����Z�b�g���Ȃ�
            	$Customer->setDeviceToken1(null);
            	$this->entityManager->flush();
            	log_info('�ʒm�ݒ�OFF�I��');
            }else{
            	//�X�V�����͂��Ȃ�
            	log_info('�ʒm�ݒ�\����');
            }
            
        }
        //.�ʒm�ݒ��ʂ̏ꍇ
        
        
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_USER_DATA_INDEX_INITIALIZE, $event);

        return $this->render($file, ['orders' => $orders, 
                                     'order_count' => count($orders),
                                     'points' => $points, 
                                    ]);
    }
}
