<?php

namespace Plugin\SmartEC3;

use Eccube\Event\EventArgs;
use Eccube\Event\EccubeEvents;
use Eccube\Event\TemplateEvent;
use Eccube\Common\EccubeConfig;
use Doctrine\ORM\Events;
use Plugin\SmartEC3\Event\SmartRegiEvents;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Eccube\Entity\Master\Pref;
use Plugin\SmartEC3\Entity\Config;
use Plugin\SmartEC3\Service\SmartRegiService;
use Plugin\SmartEC3\Repository\ConfigRepository;
use Plugin\SmartEC3\Repository\SmartRegiRepository;

class Event implements EventSubscriberInterface
{

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var SmartRegiService
     */
    protected $smartRegiService;
    
    /**
     * @var SmartRegiRepository
     */
    protected $smartRegiRepository;
    
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Event constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param SmartRegiRepository $smartRegiRepository
     * @param ConfigRepository $configRepository
     * @param SessionInterface $session
     */

    public function __construct(
        EccubeConfig $eccubeConfig,
        SmartRegiService $smartRegiService,
        SmartRegiRepository $smartRegiRepository,
        ConfigRepository $configRepository,
        SessionInterface $session
    ){
        $this->eccubeConfig = $eccubeConfig;
        $this->smartRegiService = $smartRegiService;
        $this->smartRegiRepository = $smartRegiRepository;
        $this->configRepository = $configRepository;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            '@admin/Product/index.twig' => 'SmartEC3ProductIndexTwig',
            '@admin/Product/product.twig' => 'SmartEC3ProductEditTwig',

            '@admin/Product/product_class.twig' => 'SmartEC3ProductClassEditTwig',

            '@admin/Product/category.twig' => 'SmartEC3CategoryIndexTwig',

            '@admin/Customer/index.twig' => 'SmartEC3CustomerIndexTwig',

            '@admin/Order/index.twig' => 'SmartEC3OrderIndexTwig',
            '@admin/Order/edit.twig' => 'SmartEC3OrderEditTwig',
            
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE => 'SmartEC3CategoryUpdate',
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE => 'SmartEC3CategoryDelete',

            EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE => 'SmartEC3UserRegister',
            EccubeEvents::ADMIN_CUSTOMER_DELETE_COMPLETE => 'SmartEC3UserDelete',

            EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE => 'SmartEC3UserRegister',
            EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE => 'SmartEC3UserRegisterFront',
            EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE => 'SmartEC3UserRegisterFront',
            //EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE => 'SmartEC3UserDeleteFront',

            EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE => 'SmartEC3ProductRegister',
            SmartRegiEvents::PLG_SMARTREGI_ADMIM_SMART_EDIT_COMPLETE => 'SmartEC3ProductRegister',
            SmartRegiEvents::PLG_SMARTREGI_ADMIM_SMART_EDIT_STORE_CHANGE => 'SmartEC3ProductDelete',
            //EccubeEvents::ADMIN_PRODUCT_DELETE_COMPLETE => 'SmartEC3ProductDelete',
            
            //購入完了した時
            EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE => 'SmartEC3TransactionUpdate',
            //最終確認　購入完了した時
            //EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE => 'SmartEC3TransactionUpdate',
            
        ];
    }

    //---------------------------------------------------------------------------------------
    // Template Events
    //---------------------------------------------------------------------------------------

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3ProductIndexTwig(TemplateEvent $event){

        $this->checkWarning();

        $twig = '@SmartEC3/admin/Product/smart_index.twig';
        $event->addSnippet($twig);

        $parameters = $event->getParameters();
        $pagination = $parameters["pagination"];
        $Products =  $pagination->getItems();
        $SmartRegis = $this->smartRegiRepository->getSmartDataForSearchedProductsAdmin($Products);

        $parameters['SmartRegis'] = $SmartRegis;
        $event->setParameters($parameters);
    }

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3ProductEditTwig(TemplateEvent $event){

        $this->checkWarning();

        $twig = '@SmartEC3/admin/Product/smart_settings.twig';
        $event->addSnippet($twig);

        $parameters = $event->getParameters();
        $id = $parameters["id"];
        $Product = $id != null ? $parameters["Product"] : null;
        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);

        $parameters['SmartRegi'] = $SmartRegi;
        $event->setParameters($parameters);
    }

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3ProductClassEditTwig(TemplateEvent $event){

        $this->checkWarning();

        $twig = '@SmartEC3/admin/Product/smart_multiprice_warning.twig';
        $event->addSnippet($twig);

        $parameters = $event->getParameters();
        $Product = $parameters["Product"] ?? null;
        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);

        $parameters['SmartRegi'] = $SmartRegi;
        $event->setParameters($parameters);
    }

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3CategoryIndexTwig(TemplateEvent $event){

        $this->checkWarning();

        $twig = '@SmartEC3/admin/Category/smart_category.twig';
        $event->addSnippet($twig);
        
    }

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3CustomerIndexTwig(TemplateEvent $event){

        $this->checkWarning();

        $twig = '@SmartEC3/admin/Customer/smart_customer.twig';
        $event->addSnippet($twig);
        
    }

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3OrderIndexTwig(TemplateEvent $event){

        $this->checkWarning();

        $parameters = $event->getParameters();

        $pagination = $parameters["pagination"];
        
        $dummyPref = new Pref();
        $dummyPref->setName('スマレジ購入');
        
        foreach ($pagination as $Order) {
            if($Order->getPaymentMethod() == "スマレジ"){
                $Shipping = $Order->getShippings()[0];
                $Shipping->setPref($dummyPref);
            }
        }
    }

    /**
     * @param TemplateEvent $event
     */
    public function SmartEC3OrderEditTwig(TemplateEvent $event){

        $this->checkWarning();

        $parameters = $event->getParameters();
        $Order = $parameters["Order"];

        if($Order->getPaymentMethod() == "スマレジ"){

            $twig = $this->eccubeConfig['plugin_realdir'] . '/SmartEC3/Resource/template/admin/Order/smart_order.twig';
            $twigString = file_get_contents($twig);

            $event->setSource($twigString);
        }
        
    }

    public function checkWarning(){

        // Check if config complete

        $flashbag = $this->session->getFlashBag();

        $Config = $this->configRepository->find(1);
        if($Config) {
            if(!$Config->checkConfigComplete()){
                $flashbag->add('eccube.'.'admin'.'.danger', 'plg.smartec3.config.not_complete');
            }
        }else{
            $flashbag->add('eccube.'.'admin'.'.danger', 'plg.smartec3.config.not_complete');
        }

    }

    //---------------------------------------------------------------------------------------
    // Service calls
    //---------------------------------------------------------------------------------------

    public function SmartEC3CategoryUpdate(EventArgs $event){

        $arguments = $event->getArguments();
        $category = $arguments["TargetCategory"];

        $msg = $this->smartRegiService->updateSmartRegiCategory($category);
        $msg = "スマレジ： " . $msg;
        $flashbag = $this->session->getFlashBag();
        $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }

    public function SmartEC3CategoryDelete(EventArgs $event){

        $request = $event->getRequest();
        $id = (int) $request->get("id");

        $msg = $this->smartRegiService->deleteSmartRegiCategory($id);
        $msg = "スマレジ： " . $msg;
        $flashbag = $this->session->getFlashBag();
        $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }



    public function SmartEC3UserRegister(EventArgs $event){

        $arguments = $event->getArguments();
        $customer = $arguments["Customer"];

        $msg = $this->smartRegiService->updateSmartRegiUser($customer);
        $msg = "スマレジ： " . $msg;
        $flashbag = $this->session->getFlashBag();
        $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }
    
    public function SmartEC3UserDelete(EventArgs $event){

        $arguments = $event->getArguments();
        $customer_id = $arguments["customer_id"];
        log_info('SmartEC3UserDelete_CODE_log', [$customer_id]);
        
		//customer_idからスマレジ側の該当会員データを削除する
        $msg = $this->smartRegiService->deleteSmartRegiUser($customer_id);
        $msg = "スマレジ： " . $msg;
        $flashbag = $this->session->getFlashBag();
        $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }



    public function SmartEC3UserRegisterFront(EventArgs $event){

        
        $arguments = $event->getArguments();
        $customer = $arguments["Customer"];
        
        $msg = $this->smartRegiService->updateSmartRegiUser($customer);
        
        // User front has a different page for confirmation, can7t use flash bags
        // $msg = "スマレジ： " . $msg;
        //$flashbag = $this->session->getFlashBag();
        // $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }

    public function SmartEC3UserDeleteFront(EventArgs $event){

        $request = $event->getRequest();
        $id = (int) $request->get("id");

        $msg = $this->smartRegiService->deleteSmartRegiUser($id);

        // User front has a different page for confirmation, can7t use flash bags
        // $msg = "スマレジ： " . $msg;
        //$flashbag = $this->session->getFlashBag();
        // $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }



    public function SmartEC3ProductRegister(EventArgs $event){

        $arguments = $event->getArguments();
        $product = $arguments["Product"];

        $msg = $this->smartRegiService->updateSmartRegiProduct($product);
        if( $msg != null){
            $msg = "スマレジ： " . $msg;
            $flashbag = $this->session->getFlashBag();
            $flashbag->add('eccube.'.'admin'.'.warning', $msg);
        }

    }

    public function SmartEC3ProductDelete(EventArgs $event){

        $request = $event->getRequest();
        $arguments = $event->getArguments();

        $id = (int) $request->get("id");
        $ProductClasses = $arguments["ProductClass"];

        $msg = $this->smartRegiService->deleteSmartRegiProduct($ProductClasses, $id);
        $msg = "スマレジ： " . $msg;
        $flashbag = $this->session->getFlashBag();
        $flashbag->add('eccube.'.'admin'.'.warning', $msg);

    }
    
    public function SmartEC3TransactionUpdate(EventArgs $event){
log_info(
            '__testLog1',
            [
                'test_val' => 'test1',
            ]
        );

        $arguments = $event->getArguments();
        $transaction = $arguments["Order"];


log_info(
            '__testLog2',
            [
                'test2a' => $arguments,
                'test2b' => $transaction,
            ]
        );


        $msg = $this->smartRegiService->updateSmartRegiTransaction($transaction);
        $msg = "スマレジ： " . $msg;
        $flashbag = $this->session->getFlashBag();
        $flashbag->add('eccube.'.'admin'.'.warning', $msg);

log_info(
            '__testLog3',
            [
                'test_val' => 'test',
            ]
        );

    }

}
