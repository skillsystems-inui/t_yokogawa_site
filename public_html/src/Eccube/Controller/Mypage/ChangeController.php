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

namespace Eccube\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Entity\PointHistory;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\PointHistoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ChangeController extends AbstractController
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var PointHistoryRepository
     */
    protected $pointHistoryRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    public function __construct(
        CustomerRepository $customerRepository,
        PointHistoryRepository $pointHistoryRepository,
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->customerRepository = $customerRepository;
        $this->pointHistoryRepository = $pointHistoryRepository;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * 会員情報編集画面.
     *
     * @Route("/mypage/change", name="mypage_change")
     * @Template("Mypage/change.twig")
     */
    public function index(Request $request)
    {
        $Customer = $this->getUser();
        $LoginCustomer = clone $Customer;
        $this->entityManager->detach($LoginCustomer);
        
        $session = $this->session;

        $previous_password = $Customer->getPassword();
        $Customer->setPassword($this->eccubeConfig['eccube_default_password']);

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createBuilder(EntryType::class, $Customer);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        $form->handleRequest($request);
        
        //アプリ判定
        $is_application = false;
        $org_url = $session->get('eccube.mypagechange_url');
        
        log_info(
			            '__testLogB',
			            [
			                'org_url' => $org_url,
			            ]
			        );
	        	
    	//遷移前のページ判定
    	if(strlen($org_url) > 0){
		    if(strpos($org_url,'app_')){
	        	//アプリの場合
	        	$is_application = true;
	        }
    	}
	    
        if ($form->isSubmitted() && $form->isValid()) {
            log_info('会員編集開始');

            if ($Customer->getPassword() === $this->eccubeConfig['eccube_default_password']) {
                $Customer->setPassword($previous_password);
            } else {
                $encoder = $this->encoderFactory->getEncoder($Customer);
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($encoder->createSalt());
                }
                $Customer->setPassword(
                    $encoder->encodePassword($Customer->getPassword(), $Customer->getSalt())
                );
            }
            $this->entityManager->flush();

            log_info('会員編集完了');
            
            //誕生日ポイントをセットする
            // ポイント履歴取得
            $targetPointHistory = $this->pointHistoryRepository->findOneBy(
                [
                    'Customer' => $Customer,
                ]
            );
            
            $birth_point = 200;//誕生日ポイント
            $now = new \DateTime("now");//現在日時
            if($targetPointHistory != null){
            	// 誕生日がnullではなく、ポイント履歴の誕生日ポイントが0の場合
            	if($Customer->getBirth() != null && $targetPointHistory->getAppBirth() < 1){
					$targetPointHistory->setAppBirth(intval($birth_point));
            	}
            	$targetPointHistory->setUpdateDate($now);
            	$this->entityManager->persist($targetPointHistory);
            	$this->entityManager->flush();
            	
            }else{
            	//データ未作成なら新規作成
            	
            	/* @var PointHistory $PointHistory */
	            $PointHistory = new PointHistory();
		        $PointHistory->setCustomer($Customer);
		        
		        $PointHistory->setSum(0);
		        
		        // 誕生日がnullではなく、ポイント履歴の誕生日ポイントが0の場合
		        if($Customer->getBirth() != null){
            		$PointHistory->setAppBirth(intval($birth_point));
            	}
		        
		        $PointHistory->setCreateDate($now);
		        $PointHistory->setUpdateDate($now);
		        $PointHistory->setAvailable(1);
		        
		        $this->entityManager->persist($PointHistory);
		        $this->entityManager->flush();
            }
            

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE, $event);
            
            //画面遷移
            //PC
    		if($is_application == false){
            	return $this->redirect($this->generateUrl('mypage_change_complete'));
        	}else{
        		//アプリ
        		
        		$to_url = "http://t-yokogawa-com.check-xserver.jp/user_data/app_change_complete";
        		header("Location:".$to_url);
    			exit();
        	}
        	
        }else{
        	//画面表示時にURL保持
        	$cur_url = $_SERVER['REQUEST_URI'];
        	$session->set('eccube.mypagechange_url', $cur_url);
        	
        	        
			log_info(
			            '__testLogA',
			            [
			                'cur_url' => $cur_url,
			            ]
			        );
        }

        $this->tokenStorage->getToken()->setUser($LoginCustomer);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 会員情報編集完了画面.
     *
     * @Route("/mypage/change_complete", name="mypage_change_complete")
     * @Template("Mypage/change_complete.twig")
     */
    public function complete(Request $request)
    {
        return [];
    }
    
}
