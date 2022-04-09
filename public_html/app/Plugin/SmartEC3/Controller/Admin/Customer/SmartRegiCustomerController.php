<?php

namespace Plugin\SmartEC3\Controller\Admin\Customer;

use Eccube\Common\Constant;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;

use Plugin\SmartEC3\Repository\ConfigRepository;

use Eccube\Controller\AbstractController;

use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Util\FormUtil;

use Eccube\Repository\CustomerRepository;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Form\FormFactoryInterface;


class SmartRegiCustomerController extends AbstractController
{

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;


    /**
     * SmartRegiController constructor.
     *
     * @param ConfigRepository $configRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        CustomerRepository $customerRepository,
        FormFactoryInterface $formFactory
    ){
        $this->configRepository = $configRepository;
        $this->customerRepository = $customerRepository;
        $this->formFactory = $formFactory;
    }


    /**
     * @Route("/%eccube_admin_route%/smart_ec3/customer/all", name="admin_smart_customer_all")
     */
    public function customerAll(Request $request)
    {

        //【注意】実行する対象上限数
        $max_limit_cnt = 500;//ToDo この数字を変更する場合は表示側も変更すること！(件数が直書きされているところ)(ソース：public_html\src\Eccube\Resource\template\admin\Customer\index.twig)
        
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);
        $searchForm = $builder->getForm();
        $viewData = $session->get('eccube.admin.customer.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
        
        //$qb = $this->customerRepository->getQueryBuilderBySearchData(array());//全件
        $qb = $this->customerRepository->getQueryBuilderBySearchData($searchData);//指定条件
        $Customers =$qb->getQuery()->getResult();
        $TargetCount = 0;
        if($Customers != null){
        	$TargetCount = count($Customers);
        }
        
        // タイムアウトを無効にする.
        set_time_limit(0);
        
        log_info(
            'スマレジに会員一括登録実行　開始',
            [
                'max_limit_cnt' => $max_limit_cnt,
                'TargetCount' => $TargetCount,
                'Customers' => $Customers,
                'searchData' => $searchData,
            ]
        );
		
		if($TargetCount <= $max_limit_cnt){
	        log_info(
	            'スマレジに会員一括登録実行　対象件数が上限以下なので実行する',
	            [
	                'searchData' => $searchData,
                    'TargetCount' => $TargetCount,
                    'Customers' => $Customers,
	            ]
	        );
	        
	        //該当する会員情報をスマレジに登録する
	        foreach($Customers as $TargetCustomer){
	            $event = new EventArgs(
	                [
	                    'Customer' => $TargetCustomer,
	                ],
	                $request
	            );

	            //実行
	            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE, $event);
	        }
        }else{
        	log_info(
	            'スマレジに会員一括登録実行　対象件数が上限を超えているので実行しない',
	            [
	                'searchData' => $searchData,
                    'TargetCount' => $TargetCount,
                    'Customers' => $Customers,
	            ]
	        );
        }
        
        
        log_info(
            'スマレジに会員一括登録実行　終了',
            [
                'searchData' => $searchData,
                'TargetCount' => $TargetCount,
                'Customers' => $Customers,
            ]
        );

        //画面再表示20220330
        $page_no = intval($this->session->get('eccube.admin.customer.search.page_no'));
        $page_no = $page_no ? $page_no : Constant::ENABLED;
        return $this->redirect($this->generateUrl('admin_customer_page',
                ['page_no' => $page_no]).'?resume='.Constant::ENABLED);
        //return $this->redirectToRoute('admin_customer_page');
    }

}