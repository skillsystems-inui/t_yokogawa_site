<?php

namespace Plugin\SmartEC3\Service;

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Customer;
use Eccube\Entity\Category;
use Eccube\Entity\Order;
use Plugin\SmartEC3\Entity\SmartRegi;

use Symfony\Component\Routing\RouterInterface;


class SmartRegiServiceHelper
{
    // Constant values
    const DEFAULT_SHOP_ID = 1;

    // SmartRegi tables
    const CATEGORY_TABLE = 'Category';
    const USER_TABLE = 'Customer';
    const PRODUCT_TABLE = 'Product';
    const PRODUCT_IMAGE_TABLE = 'ProductImage';
    const PRODUCT_STOCK_TABLE = 'Stock';
    const PRODUCT_PRICE_TABLE = 'ProductPrice';
    const PRODUCT_STORE_TABLE = 'ProductStore';
    const TRANSACTION_HEAD = 'TransactionHead';
    const CALLBACK_URL_REGIST_PRODUCT_IMAGE = '/admin/smareji/callback.php';
    const IMAGE_DIR = '/html/upload/save_image/';
    
    //---------------------------------------------------------------------------------------
    // カテゴリー
    //---------------------------------------------------------------------------------------

    public function setCategoryUpdate(Category $category, int $offset){

        $arrData = array();
        $arrData['proc_info']['proc_division'] = "U"; // Command settings
        $arrData['data'][0]['table_name'] = self::CATEGORY_TABLE;
        $arrData['data'][0]['rows'][0]['categoryId'] = $category->getId() + $offset;
        $arrData['data'][0]['rows'][0]['categoryName'] = $category->getName();
        $parentCat = $category->getParent();
        if ( $parentCat != null)
            $arrData['data'][0]['rows'][0]['parentCategoryId'] = $parentCat->getId() + $offset;
        $arrData['data'][0]['rows'][0]['displayFlag'] = 1;
        $arrData['data'][0]['rows'][0]['pointNotApplicable'] = 0;
        $arrData['data'][0]['rows'][0]['taxFreeDivision'] = 0;
        $arrData['data'][0]['rows'][0]['reduceTaxId'] = null;

        return $arrData;

    }

    public function setCategoryDelete(int $id, int $offset ){
     
        $arrData = array();
        $arrData['proc_info']['proc_division'] = "D"; // Command settings
        $arrData['data'][0]['table_name'] = self::CATEGORY_TABLE;
        $arrData['data'][0]['rows'][0]['categoryId'] = $id + $offset;

        return $arrData;

    }
    
    //---------------------------------------------------------------------------------------
    // 会員
    //---------------------------------------------------------------------------------------

    public function setUserUpdate(Customer $customer, int $offset){

        $arrData = array();
        $arrData['proc_info']['proc_division'] = "U"; // Command settings
        $arrData['data'][0]['table_name'] = self::USER_TABLE;
        $arrData['data'][0]['rows'][0]['customerId'] = $customer->getId() + $offset;
        $arrData['data'][0]['rows'][0]['customerCode'] = $arrData['data'][0]['rows'][0]['customerId'];
        $arrData['data'][0]['rows'][0]['lastName'] = $customer->getName01();
        $arrData['data'][0]['rows'][0]['firstName'] = $customer->getName02();
        $arrData['data'][0]['rows'][0]['lastKana'] = $customer->getKana01();
        $arrData['data'][0]['rows'][0]['firstKana'] = $customer->getKana02();
        $arrData['data'][0]['rows'][0]['postCode'] = $customer->getPostalCode();
        
        $Pref= $customer->getPref();
        $PrefName= $Pref->getName();
        $arrData['data'][0]['rows'][0]['address'] = $PrefName . $customer->getAddr01() . $customer->getAddr02();
        
        $arrData['data'][0]['rows'][0]['phoneNumber'] = $customer->getPhoneNumber();
        $arrData['data'][0]['rows'][0]['faxNumber'] = "";
        $arrData['data'][0]['rows'][0]['mailAddress'] = $customer->getEmail();
        $arrData['data'][0]['rows'][0]['mailAddress2'] = "";
        $arrData['data'][0]['rows'][0]['note'] = $customer->getNote();
        
        // No Mail magazine in ECCube 4
        //$arrData['data'][0]['rows'][0]['mailReceiveFlag'] = 1;
        
        // Optional parameters -----------------------------------------------------------------
        
        $arrData['data'][0]['rows'][0]['companyName'] = $customer->getCompanyName();
        $arrData['data'][0]['rows'][0]['point'] = $customer->getPoint();
        
        $Sex = $customer->getSex();
        $arrData['data'][0]['rows'][0]['sex'] = isset($Sex) ? $Sex->getId() : 0;
        
        $Birth = $customer->getBirth();
        if (isset($Birth))
            $arrData['data'][0]['rows'][0]['birthDate'] = date_format($Birth, 'Y-m-d');
        
        // -------------------------------------------------------------------------------------
        
        $arrData['data'][0]['rows'][0]['entryDate'] = date_format($customer->getCreateDate(), 'Y-m-d');

        $Status = $customer->getStatus();
        $arrData['data'][0]['rows'][0]['status'] = $Status->getId() == 2? 0 : $Status->getId();

        return $arrData;

    }

    public function setUserDelete(int $id, int $offset ){
     
        $arrData = array();
        $arrData['proc_info']['proc_division'] = "D"; // Command settings
        $arrData['data'][0]['table_name'] = self::USER_TABLE;
        $arrData['data'][0]['rows'][0]['customerId'] = $id + $offset;

        return $arrData;

    }

    //---------------------------------------------------------------------------------------
    // 商品
    //---------------------------------------------------------------------------------------

    public function setProductUpdate(Product $product, ProductClass $productClass, SmartRegi $smartRegi, int $offset){


        $arrData = array();
        $arrData['proc_info']['proc_division'] = "U"; // Command settings
        // Set Product data
        $arrData['data'][0]['table_name'] = self::PRODUCT_TABLE;
        $arrData['data'][0]['rows'] = $this->setProductData($product, $productClass, $smartRegi, $offset);

        // Set Product price
        $arrData['data'][1]['table_name'] = self::PRODUCT_PRICE_TABLE;
        $arrData['data'][1]['rows'] = $this->setProductPriceData($productClass, $offset);

        // Set Product price
        $arrData['data'][2]['table_name'] = self::PRODUCT_STORE_TABLE;
        $arrData['data'][2]['rows'] = $this->setProductStoreData($product, $productClass, $offset);

        return $arrData;

    }
    
    public function setProductDelete(ProductClass $productClass, int $offset){


        $arrData = array();
        $arrData['proc_info']['proc_division'] = "D"; // Command settings
        $arrData['data'][0]['table_name'] = self::PRODUCT_TABLE;
        $arrData['data'][0]['rows'][0]['productId'] = $productClass->getId() + $offset;
      
        return $arrData;

    }

    public function setProductData(Product $product, ProductClass $productClass, SmartRegi $smartRegi, int $offset){

        $productData = array();

    	//$productData[0]['productId'] = $productClass->getProduct()->getId() + $offset;
    	$productData[0]['productId'] = $productClass->getId() + $offset;
    	$productData[0]['productCode'] = $productClass->getCode();
        
        $productCategories = $product->getProductCategories();
        if(count($productCategories) > 0){
            // Problem with offset, it sometimes starts at 1, to avoid this we use first. 
            // Collections are not behaving as expected
    	    $productData[0]['categoryId'] = $productCategories->first()->getCategoryId() + $offset;
        }

        $productData[0]['productName'] = $productClass->formattedProductName();
    	$productData[0]['printReceiptProductName'] = $productData[0]['productName'];
    	$productData[0]['groupCode'] = $productClass->getProduct()->getId() . $productClass->getProduct()->getCreateDate()->format('Ymd');;
    	
    	$productData[0]['productKana'] = '';
    	$productData[0]['taxDivision'] = 1;
    	$productData[0]['productPriceDivision'] = $smartRegi->getPriceType()->getId();

    	$productData[0]['price'] = (int) $productClass->getPrice02();
    	$productData[0]['cost'] = $productClass->getPrice01();

    	$productData[0]['description'] = $product->getDescriptionDetail();
    	$productData[0]['catchCopy'] = $product->getDescriptionList();
    	$productData[0]['size'] = $smartRegi->getSize();
    	$productData[0]['color'] = $smartRegi->getColor();
    	
    	if($productClass->getClassCategory1() != null || $productClass->getClassCategory2() != null){
    		
            $productData[0]['size'] = $productClass->getClassCategory1() != null ? $productClass->getClassCategory1()->getName() : $productData[0]['size'] ;
    		$productData[0]['color'] =  $productClass->getClassCategory2() != null ?  $productClass->getClassCategory2()->getName() : $productData[0]['color'];
    	}
    	
    	$productData[0]['salesDivision'] = 0;

    	$productData[0]['stockControlDivision'] = $productClass->isStockUnlimited() ? 1: 0;
    	
        $Status = $product->getStatus();
    	$productData[0]['displayFlag'] = $Status->getId() == 2? 0 : $Status->getId();

    	$productData[0]['division'] = 0;
    	$productData[0]['pointNotApplicable'] = 0;
    	$productData[0]['taxFreeDivision'] = 0;
    	$productData[0]['supplierProductNo'] = $productClass->getCode();

    	//$productData[0]['reduceTaxPrice'] = $smartRegi->getTax()->getTaxRate();
    	// $productData[0]['reduceTaxId'] = trim($arrData['plg_tax_rate_smareji']); -> hay que aanadir a la pagina
        if ($smartRegi->getTax()->getTaxRate() == 8 )
    	    $productData[0]['reduceTaxId'] = 10000000+$smartRegi->getTaxType()->getId();
    	//$productData[0]['reduceTaxCustomerPrice'] = $productData[0]['reduceTaxPrice'];

        return $productData;
    }

    public function setProductPriceData(ProductClass $productClass, int $offset){

        $priceData = array();
        
    	//$priceData[0]['productId'] = $productClass->getProduct()->getId() + $offset;
    	$priceData[0]['productId'] = $productClass->getId() + $offset;
    	$priceData[0]['storeId'] = self::DEFAULT_SHOP_ID;
    	$priceData[0]['priceDivision'] = 1;
    	$priceData[0]['startDate'] = date("Y-m-d");
    	$priceData[0]['endDate'] = "";
    	$priceData[0]['price'] = (int) $productClass->getPrice02();


        return $priceData;
    }

    public function setProductStoreData(Product $product, ProductClass $productClass, int $offset){

        $storeData = array();

    	//$storeData[0]['productId'] = $productClass->getProduct()->getId() + $offset;
    	$storeData[0]['productId'] = $productClass->getId() + $offset;
    	$storeData[0]['storeId'] = self::DEFAULT_SHOP_ID;
        $Status = $product->getStatus();
        $storeData[0]['assignDivision'] = $Status->getId() == 2? 1 : 0;

        return $storeData;
    }

    public function setProductImage(ProductClass $productClass, SmartRegi $smartRegi, int $offset){

        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");

        $arrImage = array();    	
        $arrImage['proc_info']['proc_division'] = "U";
    	$arrImage['data'][0]['table_name'] = self::PRODUCT_IMAGE_TABLE;

    	//$arrImage['data'][0]['callbackUrl'] = url('smart_ec3_admin_smartregi_edit');
    	//$arrImage['data'][0]['callbackUrl'] = 'http://54.150.26.92/admin/smart_ec3/callback';
    	$arrImage['data'][0]['callbackUrl'] = $protocol . $_SERVER["HTTP_HOST"] . self::CALLBACK_URL_REGIST_PRODUCT_IMAGE;

    	$arrImage['data'][0]['state'] = 'test';
        //$arrImage['data'][0]['rows'][0]['productId'] = $productClass->getProduct()->getId() + $offset;
        $arrImage['data'][0]['rows'][0]['productId'] = $productClass->getId() + $offset;

        $image = $smartRegi->getSmartRegiImage()[0];
        //$arrImage['data'][0]['rows'][0]['imageUrl'] = $this->eccubeConfig['eccube_save_image_dir'] . $image->getFileName();
        $arrImage['data'][0]['rows'][0]['imageUrl'] =  $protocol . $_SERVER["HTTP_HOST"] . self::IMAGE_DIR . $image->getFileName();
    	
    	return $arrImage;    	
    }

    public function setProductStock(ProductClass $productClass, int $offset){

        // TODO: IMPORT the eccubeConfig parameter to get the proper paths for image upload

        $arrStock =array();
    	
        $arrStock['proc_info']['proc_division'] = "U";
        $arrStock['proc_info']['proc_detail_division'] = 1;
    	$arrStock['data'][0]['table_name'] = self::PRODUCT_STOCK_TABLE;
        //$arrStock['data'][0]['rows'][0]['productId'] = $productClass->getProduct()->getId() + $offset;
        $arrStock['data'][0]['rows'][0]['productId'] = $productClass->getId() + $offset;
        $arrStock['data'][0]['rows'][0]['storeId'] = self::DEFAULT_SHOP_ID;
        $arrStock['data'][0]['rows'][0]['stockAmount'] = $productClass->getStock();
        $arrStock['data'][0]['rows'][0]['stockDivision'] = "11";
    	
    	return $arrStock;    	
    }
    
    
    //---------------------------------------------------------------------------------------
    // 受注
    //---------------------------------------------------------------------------------------

    public function setTransactionUpdate(Order $order){

        $arrData = array();
        
        log_info(
            '__testLog',
            [
                'test_api' => 'jyutyu_api',
            ]
        );
        
        /*
        
        $arrData['proc_info']['proc_division'] = "U"; // Command settings
        $arrData['data'][0]['table_name'] = self::TRANSACTION_HEAD;
        
        //$arrData['data'][0]['rows'][0]['transactionHeadDivision'] = $order->getId();
        //$arrData['data'][0]['rows'][0]['transactionDateTime'] = $order->getDateTime();
        //$arrData['data'][0]['rows'][0][''] = ;//
        
        //取引区分(1：通常、2：入金、3：出金、4：預かり金、 5：預かり金返金、6：ポイント加算、　7：ポイント減算、8：ポイント失効、10:取置き、13：マイル加算、14：マイル減算、15：バリューカード入金、16：領収証)
        $arrData['data'][0]['rows'][0]['transactionHeadDivision'] = 1;//取引区分★
        $arrData['data'][0]['rows'][0]['cancelDivision']          = 0;//取消区分★　取引の取消を識別する区分(0:通常、1：取消）
        $arrData['data'][0]['rows'][0]['subtotal']                = 500;//小計★ 
        $arrData['data'][0]['rows'][0]['subtotalDiscountPrice']   = 0;//小計値引き
        $arrData['data'][0]['rows'][0]['subtotalDiscountRate']    = 0;//小計割引率
        $arrData['data'][0]['rows'][0]['pointDiscount']           = 0;//ポイント値引き 
        $arrData['data'][0]['rows'][0]['total']                   = 600;//合計★
        //$arrData['data'][0]['rows'][0]['taxExclude']              = 0;//外税額
        //$arrData['data'][0]['rows'][0]['taxInclude']              = 0;//内税額
        //$arrData['data'][0]['rows'][0]['deposit']                 = 0;//預かり金
        //$arrData['data'][0]['rows'][0]['depositCash']             = 0;//預かり金現金
        //$arrData['data'][0]['rows'][0]['depositCredit']           = 0;//預かり金クレジット
        //$arrData['data'][0]['rows'][0]['charge']                  = 0;//釣銭
        //$arrData['data'][0]['rows'][0]['newPoint']                = 0;//付与ポイント 
        //$arrData['data'][0]['rows'][0]['spendPoint']              = 0;//使用ポイント
        //$arrData['data'][0]['rows'][0]['point']                   = 0;//現在ポイント★
        //$arrData['data'][0]['rows'][0]['totalPoint']              = 0;//合計ポイント 
        //$arrData['data'][0]['rows'][0]['earnMile']                = 0;//獲得マイル 
        //$arrData['data'][0]['rows'][0]['totalMile']               = 0;//合計マイル
        //$arrData['data'][0]['rows'][0]['adjustmentMile']          = 0;//調整マイル
        //調整マイル区分  
        //　1:0マイル　（マイルを付与しない。「調整マイル = -1 * 獲得マイル」）2:マイル指定（指定したマイルにする。「調整マイル = 入力値 - 獲得マイル」）
        //　3:マイル加算（マイルを加算/減算する。「調整マイル =入力値」）4:マイルｎ倍（指定倍数にする。「調整マイル = 獲得マイル * (n - 1)」
        //$arrData['data'][0]['rows'][0]['adjustmentMileDivision']  = '1';//調整マイル区分 
        $arrData['data'][0]['rows'][0]['storeId']                 = 1;//店舗ID★
        $arrData['data'][0]['rows'][0]['terminalId']              = 1;//端末ID★
        //$arrData['data'][0]['rows'][0]['customerId']            = 2;//会員ID
        $arrData['data'][0]['rows'][0]['terminalTranId']          = ;//端末取引ID★
        $arrData['data'][0]['rows'][0]['terminalTranDateTime']    = '';//端末取引日時★
        
        */
        
        return $arrData;
    }


}
