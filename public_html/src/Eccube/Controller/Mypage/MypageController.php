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
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Product;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MypageController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CustomerFavoriteProductRepository
     */
    protected $customerFavoriteProductRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * MypageController constructor.
     *
     * @param OrderRepository $orderRepository
     * @param CustomerFavoriteProductRepository $customerFavoriteProductRepository
     * @param CartService $cartService
     * @param BaseInfoRepository $baseInfoRepository
     * @param PurchaseFlow $purchaseFlow
     */
    public function __construct(
        OrderRepository $orderRepository,
        CustomerFavoriteProductRepository $customerFavoriteProductRepository,
        CartService $cartService,
        BaseInfoRepository $baseInfoRepository,
        PurchaseFlow $purchaseFlow
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerFavoriteProductRepository = $customerFavoriteProductRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->cartService = $cartService;
        $this->purchaseFlow = $purchaseFlow;
    }

    /**
     * ??????????????????.
     *
     * @Route("/mypage/login", name="mypage_login")
     * @Template("Mypage/login.twig")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        $session = $this->session;
        
        //???????????????
        $to_url = "http://t-yokogawa.com/";
        
        //????????????????????????
        $_device_token = null;
        
        //???????????????
        $is_application = false;
        $current_url = $_SERVER['REQUEST_URI'];
        if(strpos($current_url,'app_')){
        	//??????????????????
        	//?????????????????????????????????
        	$to_url = "http://t-yokogawa.com/user_data/app_top";
        	$is_application = true;
        }
        
        //???????????????????????????????????????????????????????????????????????????
        if($is_application == true){
        	//URL???????????????????????????????????????(?token=??????)
        	$dvt_idx = strpos($current_url, '?token='); 
        	if($dvt_idx != false){
        		$token_idx = $dvt_idx + 7;//offset7
	        	$device_token = substr($current_url, $token_idx);
	        	$session->set('eccube.device_token', $device_token);
		    }
        }
        
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
	            log_info('???????????????????????????????????????????????????');
				
				//??????????????????(PC)
        		if($is_application == false){
	            	return $this->redirectToRoute('mypage');
	        	}else{
	        		//??????????????????(?????????)
	        		header("Location:".$to_url);
        			exit();
	        	}
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory
            ->createNamedBuilder('', CustomerLoginType::class);

        $builder->get('login_memory')->setData((bool) $request->getSession()->get('_security.login_memory'));

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $Customer = $this->getUser();
            if ($Customer instanceof Customer) {
                $builder->get('login_email')
                    ->setData($Customer->getEmail());
            }
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();
        
        //????????????(????????????????????????)
        if($is_application == true){
        	$this->setLoginTargetPath($to_url);
        }
        
        return [
            'error' => $utils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }

    /**
     * ???????????????.
     *
     * @Route("/mypage/", name="mypage")
     * @Template("Mypage/index.twig")
     */
    public function index(Request $request, Paginator $paginator)
    {
        $Customer = $this->getUser();

        // ???????????????/????????????????????????????????????????????????????????????.
        $this->entityManager
            ->getFilters()
            ->enable('incomplete_order_status_hidden');

        // paginator
        $qb = $this->orderRepository->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_INDEX_SEARCH, $event);

        $pagination = $paginator->paginate(
            $qb,
            $request->get('pageno', 1),
            $this->eccubeConfig['eccube_search_pmax']
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * ?????????????????????????????????.
     *
     * @Route("/mypage/history/{order_no}", name="mypage_history")
     * @Template("Mypage/history.twig")
     */
    public function history(Request $request, $order_no)
    {
        $this->entityManager->getFilters()
            ->enable('incomplete_order_status_hidden');
        $Order = $this->orderRepository->findOneBy(
            [
                'order_no' => $order_no,
                'Customer' => $this->getUser(),
            ]
        );

        $event = new EventArgs(
            [
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE, $event);

        /** @var Order $Order */
        $Order = $event->getArgument('Order');

        if (!$Order) {
            throw new NotFoundHttpException();
        }

        $stockOrder = true;
        foreach ($Order->getOrderItems() as $orderItem) {
            if ($orderItem->isProduct() && $orderItem->getQuantity() < 0) {
                $stockOrder = false;
                break;
            }
        }

        return [
            'Order' => $Order,
            'stockOrder' => $stockOrder,
        ];
    }

    /**
     * ??????????????????.
     *
     * @Route("/mypage/order/{order_no}", name="mypage_order", methods={"PUT"})
     */
    public function order(Request $request, $order_no)
    {
        $this->isTokenValid();

        log_info('???????????????', [$order_no]);

        $Customer = $this->getUser();

        /* @var $Order \Eccube\Entity\Order */
        $Order = $this->orderRepository->findOneBy(
            [
                'order_no' => $order_no,
                'Customer' => $Customer,
            ]
        );

        $event = new EventArgs(
            [
                'Order' => $Order,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE, $event);

        if (!$Order) {
            log_info('???????????????????????????????????????', [$order_no]);
            throw new NotFoundHttpException();
        }

        // ?????????????????????????????????
        $errorMessages = [];

        foreach ($Order->getOrderItems() as $OrderItem) {
            try {
                if ($OrderItem->getProduct() && $OrderItem->getProductClass()) {
                    $this->cartService->addProduct($OrderItem->getProductClass(), $OrderItem->getQuantity());

                    // ??????????????????
                    $Carts = $this->cartService->getCarts();
                    foreach ($Carts as $Cart) {
                        $result = $this->purchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
                        // ???????????????????????????????????????????????????????????????????????????.
                        if ($result->hasError()) {
                            $this->cartService->removeProduct($OrderItem->getProductClass());
                            foreach ($result->getErrors() as $error) {
                                $errorMessages[] = $error->getMessage();
                            }
                        }
                        foreach ($result->getWarning() as $warning) {
                            $errorMessages[] = $warning->getMessage();
                        }
                    }

                    $this->cartService->save();
                }
            } catch (CartException $e) {
                log_info($e->getMessage(), [$order_no]);
                $this->addRequestError($e->getMessage());
            }
        }

        foreach ($errorMessages as $errorMessage) {
            $this->addRequestError($errorMessage);
        }

        $event = new EventArgs(
            [
                'Order' => $Order,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        log_info('???????????????', [$order_no]);

        return $this->redirect($this->generateUrl('cart'));
    }

    /**
     * ????????????????????????????????????.
     *
     * @Route("/mypage/favorite", name="mypage_favorite")
     * @Template("Mypage/favorite.twig")
     */
    public function favorite(Request $request, Paginator $paginator)
    {
        if (!$this->BaseInfo->isOptionFavoriteProduct()) {
            throw new NotFoundHttpException();
        }
        $Customer = $this->getUser();

        // paginator
        $qb = $this->customerFavoriteProductRepository->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH, $event);

        $pagination = $paginator->paginate(
            $qb,
            $request->get('pageno', 1),
            $this->eccubeConfig['eccube_search_pmax'],
            ['wrap-queries' => true]
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * ????????????????????????????????????.
     *
     * @Route("/mypage/favorite/{id}/delete", name="mypage_favorite_delete", methods={"DELETE"}, requirements={"id" = "\d+"})
     */
    public function delete(Request $request, Product $Product)
    {
        $this->isTokenValid();

        $Customer = $this->getUser();

        log_info('?????????????????????????????????', [$Customer->getId(), $Product->getId()]);

        $CustomerFavoriteProduct = $this->customerFavoriteProductRepository->findOneBy(['Customer' => $Customer, 'Product' => $Product]);

        if ($CustomerFavoriteProduct) {
            $this->customerFavoriteProductRepository->delete($CustomerFavoriteProduct);
        } else {
            throw new BadRequestHttpException();
        }

        $event = new EventArgs(
            [
                'Customer' => $Customer,
                'CustomerFavoriteProduct' => $CustomerFavoriteProduct,
            ], $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE, $event);

        log_info('?????????????????????????????????', [$Customer->getId(), $CustomerFavoriteProduct->getId()]);

        return $this->redirect($this->generateUrl('mypage_favorite'));
    }
}
