<?php

namespace Plugin\SmartEC3\Service;


use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Customer;
use Eccube\Entity\Category;
use Eccube\Entity\Order;
use Plugin\SmartEC3\Entity\Config;
use Plugin\SmartEC3\Entity\SmartRegi;
use Plugin\SmartEC3\Entity\SmartRegiImage;
use Plugin\SmartEC3\Entity\Master\SmartRegiStore;
use Plugin\SmartEC3\Service\SmartRegiServiceHelper;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eccube\Repository\ProductRepository;
use Plugin\SmartEC3\Repository\ConfigRepository;
use Plugin\SmartEC3\Repository\SmartRegiRepository;
use Plugin\SmartEC3\Repository\SmartRegiImageRepository;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;


class SmartRegiService
{

    // Params
    const CATEGORY_PARAM = 'category_upd';
    const USER_PARAM = 'customer_upd';
    const PRODUCT_PARAM = 'product_upd';
    const PRODUCT_IMAGE_PARAM = 'product_image_upd';
    const PRODUCT_STOCK_PARAM = 'stock_upd';
    const TRANSACTION_PARAM = 'transaction_upd';

    // Actions
    const CATEGORY_UPDATE_ACTION = 'CATEGORY_UPDATE';
    const CATEGORY_DELETE_ACTION = 'CATEGORY_DELETE';
    const USER_UPDATE_ACTION = 'USER_UPDATE';
    const USER_DELETE_ACTION = 'USER_DELETE';
    const PRODUCT_UPDATE_ACTION = 'PRODUCT_UPDATE';
    const PRODUCT_IMAGE_ACTION = 'PRODUCT_IMAGE';
    const PRODUCT_STOCK_ACTION = 'PRODUCT_STOCK';
    const PRODUCT_DELETE_ACTION = 'PRODUCT_DELETE';
    const TRANSACTION_UPDATE_ACTION = 'TRANSACTION_UPDATE';
    const TRANSACTION_DETAIL_UPDATE_ACTION = 'TRANSACTION_DETAIL_UPDATE';
    
    // LOGS
    const CATEGORY_LOG = 'smartregi_category.log';
    const USER_LOG = 'smartregi_user.log';
    const PRODUCT_LOG = 'smartregi_product.log';
    const PRODUCT_STOCK_LOG = 'smartregi_product_stock.log';
    const TRANSACTION_LOG = 'smartregi_transaction.log';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
    */
    protected $entityManager;
    
    /**
     * @var ContainerInterface
    */
    private $container;

    /**
     * @var SmartRegiRepository
     */
    protected $smartRegiRepository;

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SmartRegiServiceHelper
     */
    protected $smartHelper;


     /**
     * SmartRegiService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param SmartRegiRepository $smartRegiRepository
     * @param ConfigRepository $configRepository
     * @param SmartRegiImageRepository $smartRegiImageRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        SmartRegiRepository $smartRegiRepository,
        ConfigRepository $configRepository,
        SmartRegiImageRepository $smartRegiImageRepository,
        ProductRepository $productRepository
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->smartRegiRepository = $smartRegiRepository;
        $this->configRepository = $configRepository;
        $this->smartRegiImageRepository = $smartRegiImageRepository;
        $this->productRepository = $productRepository;
        $this->smartHelper = new SmartRegiServiceHelper();
    }

    /**
    * アクセスログの記録
    * @param FilterControllerEvent $event
    * @return void
    */
    public function route_listener(FilterControllerEvent $event) : void
    {
        $route = $event->getRequest()->attributes->get('_route');

        switch ($route) {
            case 'admin_product_product_delete':
                $this->deleteProductRoute($event);
                break;
            // case 'admin_product_product_class':
            //     $this->updateMultiPriceRoute($event);
            //     break;
            case 'admin_product_product_class_clear':
                $this->clearMultiPriceRoute($event);
                break;
            default:
                break;
        }
    }

    // Opposed to the ideal method, product deletion has to be approached in a different way
    // This is due to the lack of event in the default controller when calling the delete process, which, for others actually
    // does exists.
    // Therefore, since we need to delete the SmartRegi data and also need the different ProductClasses' ids, we must process
    // the deletion before actually deleting from the EC3 database all product related data, resulting in a state in which
    // the SmartRegi data might be deleted but not the real product data, making both DBs out of sync and in need of manual operation.
    public function deleteProductRoute(FilterControllerEvent $event){

        $route_params = $event->getRequest()->attributes->get('_route_params');
        $id = $route_params["id"];

        $Product = $this->productRepository->find($id);
        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);

        if ($SmartRegi){
    
            $ProductClasses = $Product->getProductClasses();
            $this->deleteSmartRegiProduct($ProductClasses, $id);

            $this->smartRegiRepository->delete($SmartRegi);
            $this->entityManager->flush();

        }
    }

    public function updateMultiPriceRoute(FilterControllerEvent $event){
        
        $route_params = $event->getRequest()->attributes->get('_route_params');
        $id = $route_params["id"];
        
        $Product = $this->productRepository->find($id);
        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);
        
        if ($SmartRegi){
            $ProductClasses = $Product->getProductClasses();
            
            // Set the toDelete and toUpdate/Register product classes
            
            $post = $event->getRequest()->request->all();

            if ($post !=null && count($post) > 0){
                
                $postMatrix = $post['product_class_matrix'];
                if ($postMatrix != null && isset($postMatrix['product_classes']) ) {

                    // $postClasses = $postMatrix['product_classes'];
    
                    // $toDelete = array();
                    // $toUpdate = array();
    
                    // foreach ($postClasses as $postClass) {
                    //     if(array_key_exists("checked", $postClass)){
                    //         $toUpdate[] = $postClass;
                    //     }else{
                    //         $toDelete[] = $postClass;
                    //     }
                    // }
    
                    $msg = '';

                    //$msg .= $this->updateSmartRegiProduct($Product, $toUpdate);
                    // $msg .= $this->updateSmartRegiProduct($Product);

                    //$msg .= $this->deleteSmartRegiProduct($ProductClasses, $id, $toDelete);
                    $msg .= $this->deleteSmartRegiProduct($ProductClasses, $id);
    
                    $flashbag = $this->container->get('session')->getFlashBag();
                    $flashbag->add('eccube.'.'admin'.'.warning', $msg);
                }
                
            }
        }
    }

    public function clearMultiPriceRoute(FilterControllerEvent $event){

        $route_params = $event->getRequest()->attributes->get('_route_params');
        $id = $route_params["id"];

        $Product = $this->productRepository->find($id);
        $SmartRegi = $this->smartRegiRepository->getFromProduct($Product);

        if ($SmartRegi){
            $ProductClasses = $Product->getProductClasses();
            $this->deleteSmartRegiProduct($ProductClasses, $id);
        }
    }

    //---------------------------------------------------------------------------------------
    // カテゴリー
    //---------------------------------------------------------------------------------------

    public function updateSmartRegiCategory(Category $category){
        
        $Config = $this->configRepository->find(1);
        if ($Config->getCategoryUpdate()){
            $offset = $Config->getCategoryOffset();
                    
            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::CATEGORY_PARAM;
            $api_url = $Config->getApiURL();

            // Query settings
            $arrData = $this->smartHelper->setCategoryUpdate($category, $offset);

            // Log message and file
            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::CATEGORY_UPDATE_ACTION, self::CATEGORY_LOG);

            $msg = $arrRet == "ok\n" ? "Category Updated" : $arrRet;
        }else{
            $msg = "Category update is off";
        }
        
        return $msg;
    }

    public function deleteSmartRegiCategory(int $id){

        $Config = $this->configRepository->find(1);
        if ($Config->getCategoryUpdate()){
            $offset = $Config->getCategoryOffset();
                    
            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::CATEGORY_PARAM;
            $api_url = $Config->getApiURL();

            // Query settings
            $arrData = $this->smartHelper->setCategoryDelete($id, $offset);
            
            // Log message and file
            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::CATEGORY_DELETE_ACTION, self::CATEGORY_LOG);
            $msg = $arrRet == "ok\n" ? "Category Deleted" : $arrRet;
        }else{
            $msg = "Category update is off";
        }
        
        return $msg;
    }

    //---------------------------------------------------------------------------------------
    // 会員
    //---------------------------------------------------------------------------------------

    public function updateSmartRegiUser(Customer $customer){
        

        $Config = $this->configRepository->find(1);
        if ($Config->getUserUpdate()){
            $offset = $Config->getUserOffset();

            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::USER_PARAM;
            $api_url = $Config->getApiURL();

            // Query settings
            $arrData = $this->smartHelper->setUserUpdate($customer, $offset);

            // Log message and file
            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::USER_UPDATE_ACTION, self::USER_LOG);

            $msg = $arrRet == "ok\n" ? "User Updated" : $arrRet;

        }else{
            $msg = "User update is off";
        }
        
        return $msg;
    }

    public function deleteSmartRegiUser(string $customer_id){
        
        $Config = $this->configRepository->find(1);
        if ($Config->getUserUpdate()){
            $offset = $Config->getUserOffset();

            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::USER_PARAM;
            $api_url = $Config->getApiURL();

            // Query settings
            $arrData = $this->smartHelper->setUserDelete($customer_id, $offset);

            // Log message and file
            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::USER_DELETE_ACTION, self::USER_LOG);

            $msg = $arrRet == "ok\n" ? "User Deleted" : $arrRet;

        }else{
            $msg = "User update is off";
        }
        
        return $msg;
    }

    //---------------------------------------------------------------------------------------
    // 商品
    //---------------------------------------------------------------------------------------

    public function updateSmartRegiProduct(Product $product, Array $classFilter = null){

        $msg = null;
        $Config = $this->configRepository->find(1);
        if ($Config->getProductUpdate()){
            $offset = $Config->getProductOffset();

            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::PRODUCT_PARAM;
            $paramImage = self::PRODUCT_IMAGE_PARAM;
            $paramStock = self::PRODUCT_STOCK_PARAM;
            $api_url = $Config->getApiURL();

            $productClasses = $product->getProductClasses();
            $SmartRegi = $this->smartRegiRepository->getFromProduct($product);

            if (!$SmartRegi){
                $msg = "Please set the SmartRegi info for the product";
            }else{

                if ($SmartRegi->getStoreType()->getId() != SmartRegiStore::EC_ONLY) {

                    $msg = "UPDATED INFO\n";
                    
                    foreach ($productClasses as $productClass){

                        if ($productClass->isVisible()){
                            // Query settings
                            $arrData = $this->smartHelper->setProductUpdate($product, $productClass, $SmartRegi, $offset);
                            $arrImage = $this->smartHelper->setProductImage($productClass, $SmartRegi, $offset);                        
                            
                            // Log message and file
                            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData, self::PRODUCT_UPDATE_ACTION, self::PRODUCT_LOG);
                            $msg .= $arrRet == "ok\n" ? "Product Updated: " . $arrData['data'][0]['rows'][0]['productId'] ."\n" : $arrRet;
                            
                            $arrRet2 = $this->doRequest($arrConnect,$paramImage,$api_url,$arrImage,self::PRODUCT_IMAGE_ACTION, self::PRODUCT_LOG);
                            $msg .= "\n";
                            $msg .= $arrRet2 == "ok\n" ? "Product Image Updated: " . $arrData['data'][0]['rows'][0]['productId'] ."\n" : $arrRet2;

                            if(!$productClass->isStockUnlimited()){
                                $arrStock = $this->smartHelper->setProductStock($productClass, $offset);       
                                $arrRet = $this->doRequest($arrConnect,$paramStock,$api_url,$arrStock, self::PRODUCT_STOCK_ACTION, self::PRODUCT_STOCK_LOG);
                                $msg .= $arrRet == "ok\n" ? "Product Stock Updated: " . $arrData['data'][0]['rows'][0]['productId'] ."\n" : $arrRet;
                            }
                        }else{
                            $arrData = $this->smartHelper->setProductDelete($productClass, $offset);

                            // Log message and file
                            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::PRODUCT_DELETE_ACTION, self::PRODUCT_LOG);
        
                            $msg .= $arrRet == "ok\n" ? "Product Deleted: " . $arrData['data'][0]['rows'][0]['productId'] ."\n" : $arrRet;
                        }
                    }
                }
            }
        }else{
            $msg = "Product update is off";
        }
        
        return $msg;
    }

    public function deleteSmartRegiProduct($productClasses, int $id){
       
        $Config = $this->configRepository->find(1);
        if ($Config->getProductUpdate()){
            $offset = $Config->getProductOffset();

            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::PRODUCT_PARAM;
            $api_url = $Config->getApiURL();

            $msg = "DELETED INFO\n";

            foreach ($productClasses as $productClass){
                //if ($productClass->isVisible()){

                    // Query settings
                    $arrData = $this->smartHelper->setProductDelete($productClass, $offset);

                    // Log message and file
                    $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::PRODUCT_DELETE_ACTION, self::PRODUCT_LOG);

                    $msg .= $arrRet == "ok\n" ? "Product Deleted: " . $arrData['data'][0]['rows'][0]['productId'] ."\n" : $arrRet;
                //}
            }

        }else{
            $msg = "Product update is off";
        }
        
        return $msg;
    }
    
    //---------------------------------------------------------------------------------------
    // 受注
    //---------------------------------------------------------------------------------------
    public function updateSmartRegiTransaction(Order $order){
        
        $Config = $this->configRepository->find(1);
        if ($Config->getOrderUpdate()){
            $p_offset = $Config->getProductOffset();
            $u_offset = $Config->getUserOffset();
            
            // Connection settings
            $arrConnect = array();
            $arrConnect['contract_id'] = $Config->getContractId();
            $arrConnect['access_token'] = $Config->getAccessToken();

            // Api Settings
            $param = self::TRANSACTION_PARAM;
            $api_url = $Config->getApiURL();
          
            // Query settings 受注データ
            $arrData = $this->smartHelper->setTransactionUpdate($order, $p_offset, $u_offset);
            // Log message and file
            $arrRet = $this->doRequest($arrConnect,$param,$api_url,$arrData,self::TRANSACTION_UPDATE_ACTION, self::TRANSACTION_LOG);

            //$msg = $arrRet == "ok\n" ? ($arrRet2 == "ok\n" ? "Transaction Updated" : $arrRet2) : $arrRet;
            $msg = $arrRet == "ok\n" ? "Transaction Updated" : $arrRet;
            
            //登録が成功したらスマレジの取引IDをECの受注に登録しておく
            $gparam = 'transaction_ref';
            $gaData = $this->smartHelper->getTransaction($order, $p_offset, $u_offset);
            
            $gaRet = $this->getResult($arrConnect,$gparam,$api_url,$gaData,'TRANSACTION_GET', self::TRANSACTION_LOG);
            
            //スマレジ取引ID取得
            $smartTranHeadId = null;
            $smartTranHeadId = $gaRet['result'][0]['transactionHeadId'];
            //スマレジ取引IDをECの受注に登録
            $order->setSmaregiId($smartTranHeadId);
            $this->entityManager->flush();
            
            //20220204
            //レシート番号を登録しておく
            $transactionUuid = $gaRet['result'][0]['transactionUuid'];
            $order->setTransactionUuid($transactionUuid);
            //端末IDを登録しておく
            $terminalId = $gaRet['result'][0]['terminalId'];
            $order->setTerminalId($terminalId);
            //端末取引IDを登録しておく
            $terminalTranId = $gaRet['result'][0]['terminalTranId'];
            $order->setTerminalTranId($terminalTranId);
            $this->entityManager->flush();
            
        }else{
            $msg = "Transaction update is off";
        }
        
        return $msg;
    }
    
    
    //---------------------------------------------------------------------------------------
    // Requests
    //---------------------------------------------------------------------------------------

    // Requests

    public function doRequest($arrConnect,$param,$url,$SendData,$action,$logfile){

		$send_data = json_encode($SendData,JSON_UNESCAPED_UNICODE);

		$param_string = "";
		$param_string .= "proc_name=".$param."&params=".$send_data;
		
		$headers = array(
					'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
					'X_contract_id: '.$arrConnect['contract_id'].'',
					'X_access_token:'.$arrConnect['access_token'].'',
					);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param_string);
		$response = curl_exec($ch);

		$arrRet = json_decode($response, true);
		curl_close($ch);

		$result = "";
		//if(count($arrRet) == 0){
		if($arrRet == null || ( $arrRet != null && count($arrRet) == 0) ){
			$result = "no response";
            $arrRet = $result;
		}elseif(!isset($arrRet["error_code"])){
			$result = "ok\n";
            $arrRet = $result;
			//$this->updateError($result,$action,$SendData,true);
		}elseif(isset($arrRet["error_code"])){
			$result = $this->getError($arrRet);
            $arrRet = $result;
			//$this->updateError($result,$action,$SendData);
		}

        // LOGGING
	    $req = $this->array2String($SendData,$action,$arrConnect);

		$this->logHistory($url,$req,$result,$action,$logfile);
		
		return $arrRet;
		
	}

    //---------------------------------------------------------------------------------------
    // Logging
    //---------------------------------------------------------------------------------------

    public function array2String($arrData,$action,$arrConnect){
		
		$string_val = "";
		$string_val .= "contract_id: ".$arrConnect['contract_id']."\n";
		$string_val .= "access_token: ".$arrConnect['access_token']."\n";
		$string_val .= "proc_division: ".$arrData['proc_info']['proc_division']."\n";
		//$string_val .= "proc_detail_division: ".$arrData['proc_info']['proc_detail_division']."\n";
		
		foreach($arrData['data'] as $key => $value){
			$string_val .= "[".$key."]\n";
			$string_val .= "table_name: ".$value['table_name']."\n";
			foreach($value['rows'] as $id => $info){
				$params = "";
				foreach($info as $field => $data){
					$params .= $field.":".$data."\n";
				}
			}
			$string_val .= $params;
		}
		
		return $string_val;
	}

    public function logHistory($url,$req,$result,$action,$logfile){
		$arrData = array();
        $arrData['start'] = "\n-----------------------------------------------------------------------------------------\n";
        $arrData['date'] = "Date: " .date('Y/m/d H:i:s') . "\n";
		$arrData['action'] = "Action: ". $action. "\n";
		$arrData['url'] = "リクエストURL: ".$url. "\n";
		$arrData['req'] = $req;
		$arrData['result'] = "\nステータス:".$result."\n";
        $arrData['end'] = "-----------------------------------------------------------------------------------------\n";
		
		//$this->printActionLog($arrData,$logfile);

        $dir = $this->container->getParameter('plugin_realdir').'/SmartEC3/logs';
        if(!is_dir($dir)){
            mkdir($dir, 0777);
        }

        file_put_contents($dir.'/'.$logfile, $arrData, FILE_APPEND);
	}

    public function getError($arrRet){

		$error_string = "";
		$error_string .= "エラー：";
		$error_string .= "error_code:".$arrRet["error_code"]." ";
		$error_string .= "error:".$arrRet["error"]." ";
		$error_string .= "error_description:".$arrRet["error_description"]." ";
		
		return $error_string;
	}
	
	public function getResult($arrConnect,$param,$url,$SendData,$action,$logfile){

		$send_data = json_encode($SendData,JSON_UNESCAPED_UNICODE);

		$param_string = "";
		$param_string .= "proc_name=".$param."&params=".$send_data;
		
		$headers = array(
					'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
					'X_contract_id: '.$arrConnect['contract_id'].'',
					'X_access_token:'.$arrConnect['access_token'].'',
					);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param_string);
		$response = curl_exec($ch);

		$arrRet = json_decode($response, true);
		curl_close($ch);

        // LOGGING
	    $req = $this->array2String($SendData,$action,$arrConnect);

		$this->logHistory($url,$req,$result,$action,$logfile);
		
		return $arrRet;
	}

}