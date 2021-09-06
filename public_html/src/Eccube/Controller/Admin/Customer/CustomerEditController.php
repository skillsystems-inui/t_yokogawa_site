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

namespace Eccube\Controller\Admin\Customer;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CustomerType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Repository\CustomerRepository;
use Eccube\Util\StringUtil;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CustomerEditController extends AbstractController
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    public function __construct(
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @Route("/%eccube_admin_route%/customer/new", name="admin_customer_new")
     * @Route("/%eccube_admin_route%/customer/{id}/edit", requirements={"id" = "\d+"}, name="admin_customer_edit")
     * @Template("@admin/Customer/edit.twig")
     */
    public function index(Request $request, $id = null)
    {
        $this->entityManager->getFilters()->enable('incomplete_order_status_hidden');
        // 編集
        if ($id) {
            $Customer = $this->customerRepository
                ->find($id);

            if (is_null($Customer)) {
                throw new NotFoundHttpException();
            }

            $oldStatusId = $Customer->getStatus()->getId();
            // 編集用にデフォルトパスワードをセット
            $previous_password = $Customer->getPassword();
            $Customer->setPassword($this->eccubeConfig['eccube_default_password']);
        // 新規登録
        } else {
            $Customer = $this->customerRepository->newCustomer();

            $oldStatusId = null;
        }

        // 会員登録フォーム
        $builder = $this->formFactory
            ->createBuilder(CustomerType::class, $Customer);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('会員登録開始', [$Customer->getId()]);

            $encoder = $this->encoderFactory->getEncoder($Customer);

            if ($Customer->getPassword() === $this->eccubeConfig['eccube_default_password']) {
                $Customer->setPassword($previous_password);
            } else {
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($encoder->createSalt());
                    $Customer->setSecretKey($this->customerRepository->getUniqueSecretKey());
                }
                $Customer->setPassword($encoder->encodePassword($Customer->getPassword(), $Customer->getSalt()));
            }

            // 退会ステータスに更新の場合、ダミーのアドレスに更新
            $newStatusId = $Customer->getStatus()->getId();
            if ($oldStatusId != $newStatusId && $newStatusId == CustomerStatus::WITHDRAWING) {
                $Customer->setEmail(StringUtil::random(60).'@dummy.dummy');
            }
            
            //会員名、カナ名、住所の全てをセット
            $_NameAll = $Customer->getName01().' '.$Customer->getName02();
	        $Customer->setNameAll($_NameAll);
	        
	        $_KanaAll = $Customer->getKana01().' '.$Customer->getKana02();
	        $Customer->setKanaAll($_KanaAll);
	        
	        $_AddrAll = $Customer->getAddr01().' '.$Customer->getAddr02();
	        $Customer->setAddrAll($_AddrAll);
            //.会員名、カナ名、アドレスの全てをセット
            
            $this->entityManager->persist($Customer);
            $this->entityManager->flush();

            log_info('会員登録完了', [$Customer->getId()]);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE, $event);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_customer_edit', [
                'id' => $Customer->getId(),
            ]);
        }
        
        
        //----- 家族紐付け用会員検索 -----
        // 会員検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE, $event);

        $searchCustomerModalForm = $builder->getForm();
        //----- .家族紐付け用会員検索 -----

        return [
            'form' => $form->createView(),
            'Customer' => $Customer,
            'searchCustomerModalForm' => $searchCustomerModalForm->createView(),
        ];
    }
    
    
    
    /**
     * 顧客情報を検索する.
     *
     * @Route("/%eccube_admin_route%/customer/search/maincustomer/html", name="admin_customer_search_maincustomer_html")
     * @Route("/%eccube_admin_route%/customer/search/maincustomer/html/page/{page_no}", requirements={"page_No" = "\d+"}, name="admin_customer_search_maincustomer_html_page")
     * @Template("@admin/Customer/search_maincustomer.twig")
     *
     * @param Request $request
     * @param integer $page_no
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchMaincustomerHtml(Request $request, $page_no = null, Paginator $paginator)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            log_debug('search customer start.');
            $page_count = $this->eccubeConfig['eccube_default_page_count'];
            $session = $this->session;

            if ('POST' === $request->getMethod()) {
                $page_no = 1;

                $searchData = [
                    'multi' => $request->get('search_word'),
                    'customer_status' => [
                        CustomerStatus::REGULAR,
                    ],
                    // 自身が家族代表であるか　代表である:1 代表でない:1以外
                    'customer_familymain' => [
                        1,
                    ],
                ];

                $session->set('eccube.admin.customer.maincustomer.search', $searchData);
                $session->set('eccube.admin.customer.maincustomer.search.page_no', $page_no);
            } else {
                $searchData = (array) $session->get('eccube.admin.customer.maincustomer.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.customer.maincustomer.search.page_no'));
                } else {
                    $session->set('eccube.admin.customer.maincustomer.search.page_no', $page_no);
                }
            }

            $qb = $this->customerRepository->getQueryBuilderBySearchData($searchData);

            $event = new EventArgs(
                [
                    'qb' => $qb,
                    'data' => $searchData,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_SEARCH, $event);

            /** @var \Knp\Component\Pager\Pagination\SlidingPagination $pagination */
            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $page_count,
                ['wrap-queries' => true]
            );

            /** @var $Customers \Eccube\Entity\Customer[] */
            $Customers = $pagination->getItems();

            if (empty($Customers)) {
                log_debug('search customer not found.');
            }

            $data = [];
            $formatName = '%s%s(%s%s)';
            foreach ($Customers as $Customer) {
                $data[] = [
                    'id' => $Customer->getId(),
                    'name' => sprintf($formatName, $Customer->getName01(), $Customer->getName02(),
                        $Customer->getKana01(),
                        $Customer->getKana02()),
                    'phone_number' => $Customer->getPhoneNumber(),
                    'email' => $Customer->getEmail(),
                ];
            }

            $event = new EventArgs(
                [
                    'data' => $data,
                    'Customers' => $pagination,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_COMPLETE, $event);
            $data = $event->getArgument('data');

            return [
                'data' => $data,
                'pagination' => $pagination,
            ];
        }
    }

    /**
     * 顧客情報を検索する.
     *
     * @Route("/%eccube_admin_route%/customer/search/maincustomer/id", name="admin_customer_search_maincustomer_by_id", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchMaiancustomerById(Request $request)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            log_debug('search customer by id start.');

            /** @var $Customer \Eccube\Entity\Customer */
            $Customer = $this->customerRepository
                ->find($request->get('id'));

            $event = new EventArgs(
                [
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_INITIALIZE, $event);

            if (is_null($Customer)) {
                log_debug('search customer by id not found.');

                return $this->json([], 404);
            }

            log_debug('search customer by id found.');

            $data = [
                'id' => $Customer->getId(),
                'name01' => $Customer->getName01(),
                'name02' => $Customer->getName02(),
                'kana01' => $Customer->getKana01(),
                'kana02' => $Customer->getKana02(),
                'postal_code' => $Customer->getPostalCode(),
                'pref' => is_null($Customer->getPref()) ? null : $Customer->getPref()->getId(),
                'addr01' => $Customer->getAddr01(),
                'addr02' => $Customer->getAddr02(),
                'email' => $Customer->getEmail(),
                'phone_number' => $Customer->getPhoneNumber(),
                'company_name' => $Customer->getCompanyName(),
            ];

            $event = new EventArgs(
                [
                    'data' => $data,
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_COMPLETE, $event);
            $data = $event->getArgument('data');

            return $this->json($data);
        }
    }
    
    
    
}
