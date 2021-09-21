<?php

namespace Plugin\SmartEC3\Controller\Admin\Customer;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;

use Plugin\SmartEC3\Repository\ConfigRepository;

use Eccube\Controller\AbstractController;

use Eccube\Repository\CustomerRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


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
     * SmartRegiController constructor.
     *
     * @param ConfigRepository $configRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        CustomerRepository $customerRepository
    ){
        $this->configRepository = $configRepository;
        $this->customerRepository = $customerRepository;
    }


    /**
     * @Route("/%eccube_admin_route%/smart_ec3/customer/all", name="admin_smart_customer_all")
     */
    public function customerAll(Request $request)
    {

        $qb = $this->customerRepository->getQueryBuilderBySearchData(array());
        $Customers =$qb->getQuery()->getResult();

        foreach($Customers as $TargetCustomer){
            $event = new EventArgs(
                [
                    'Customer' => $TargetCustomer,
                ],
                $request
            );

            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE, $event);
        }

        return $this->redirectToRoute('admin_customer_page');
    }

}