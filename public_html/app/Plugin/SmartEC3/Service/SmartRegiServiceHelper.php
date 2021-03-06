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
    const TRANSACTION_DETAIL = 'TransactionDetail';
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
        
        //会員コードが入っていれば処理実行
        $customer_code = $customer->getCustomerCode();
        if($customer_code != null){
	        $arrData['proc_info']['proc_division'] = "U"; // Command settings
	        $arrData['data'][0]['table_name'] = self::USER_TABLE;
	        //会員コードで管理する(もともとは$customer->getId() + $offset)
	        $arrData['data'][0]['rows'][0]['customerId'] = $customer->getId();
	        $arrData['data'][0]['rows'][0]['customerCode'] = $customer->getCustomerCode();
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
	        //20220401 その他はスマレジの不明を登録する
	        if(isset($Sex)){
	        	//性別「その他(3)」ならスマレジの「不明(0)」を指定する
	        	if($Sex->getId() < 3){
	        		$arrData['data'][0]['rows'][0]['sex'] =  $Sex->getId();
	        	}else{
	        		$arrData['data'][0]['rows'][0]['sex'] =  0;
	        	}
	        }else{
	        	$arrData['data'][0]['rows'][0]['sex'] =  0;
	        }
	        
	        
	        $Birth = $customer->getBirth();
	        if (isset($Birth))
	            $arrData['data'][0]['rows'][0]['birthDate'] = date_format($Birth, 'Y-m-d');
	        
	        // -------------------------------------------------------------------------------------
	        
	        $arrData['data'][0]['rows'][0]['entryDate'] = date_format($customer->getCreateDate(), 'Y-m-d');

	        $Status = $customer->getStatus();
	        $arrData['data'][0]['rows'][0]['status'] = $Status->getId() == 2? 0 : $Status->getId();
        }
        
        //20220331会員コードで判定
        log_info(
            '会員更新　arrData',
            [
                'arrData' => $arrData,
            ]
        );

        return $arrData;

    }

    public function setUserDelete(string $customer_id, int $offset ){
     
        $arrData = array();
        $arrData['proc_info']['proc_division'] = "D"; // Command settings
        $arrData['data'][0]['table_name'] = self::USER_TABLE;
        $arrData['data'][0]['rows'][0]['customerId'] = $customer_id;//スマレジの会員IDをそのまま使うので$offsetは考慮しない
		
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
    	    //カテゴリの1番目ではなくスマレジ用カテゴリを登録する
    	    //$productData[0]['categoryId'] = $productCategories->first()->getCategoryId() + $offset;
    	    $smart_category_id = $product->getSmartCategoryId();
    	    if($smart_category_id != null && is_numeric($smart_category_id)){
    	    	$productData[0]['categoryId'] = intval($smart_category_id) + $offset;
    	    	
    	    	log_info(
		            '__testLog0',
		            [
		                'smart_category_id' => $smart_category_id,
		            ]
		        );
    	    }
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

    public function setTransactionUpdate(Order $order, int $p_offset, int $u_offset){

        $arrData = array();
        
        //端末取引日時(日時)
        $terminalTranDateTime = $order->getOrderDate()->format('Y-m-d H:i:s');
        //端末取引日時(日)
        $terminalTranDate = $order->getOrderDate()->format('Y-m-d');
        
        $arrData['proc_info']['proc_division'] = "U"; // Command settings
        $arrData['data'][0]['table_name'] = self::TRANSACTION_HEAD;
        
        //取引区分(1：通常、2：入金、3：出金、4：預かり金、 5：預かり金返金、6：ポイント加算、　7：ポイント減算、8：ポイント失効、10:取置き、13：マイル加算、14：マイル減算、15：バリューカード入金、16：領収証)
        $arrData['data'][0]['rows'][0]['transactionHeadDivision'] = 10; //取引区分★　「10:取置き」
        $arrData['data'][0]['rows'][0]['cancelDivision']          = 0;  //取消区分★　取引の取消を識別する区分(0:通常、1：取消）
        $arrData['data'][0]['rows'][0]['subtotal']                = intval($order->getSubtotal());//小計★ 
        $arrData['data'][0]['rows'][0]['subtotalDiscountPrice']   = intval($order->getDiscount());//小計値引き
        $arrData['data'][0]['rows'][0]['subtotalDiscountRate']    = 0;//小計割引率
        $arrData['data'][0]['rows'][0]['pointDiscount']           = 0;//ポイント値引き 
        $arrData['data'][0]['rows'][0]['total']                   = intval($order->getTotalPrice());//合計★
        
        $arrData['data'][0]['rows'][0]['carriage']                = intval($order->getDeliveryFeeTotal());//EC連携用送料
        $arrData['data'][0]['rows'][0]['commission']              = 0;//EC連携用手数料
        
        $arrData['data'][0]['rows'][0]['sumDivision']             = 2;//締め区分
        $arrData['data'][0]['rows'][0]['sumDateTime']             = $terminalTranDate;//締め日
        
        $arrData['data'][0]['rows'][0]['storeId']                 = self::DEFAULT_SHOP_ID;;//店舗ID★
        $arrData['data'][0]['rows'][0]['terminalId']              = 10;//端末ID★
        $arrData['data'][0]['rows'][0]['terminalTranId']          = $order->getId();//端末取引ID★
        $arrData['data'][0]['rows'][0]['terminalTranDateTime']    = $terminalTranDateTime;//端末取引日時★
        
        //英数字を全角に変換する 20220426
        $_memo = mb_convert_kana($order->getNote(), 'A', 'utf-8');
        $arrData['data'][0]['rows'][0]['memo']    = $_memo;//メモをセット
        
        //会員情報取得
        $CustomerInfo = $order->getCustomer();
        if($CustomerInfo != null){
        	$arrData['data'][0]['rows'][0]['customerId'] = $CustomerInfo->getId();//会員ID
        }
        
        //配送情報取得
        $ShipInfo = $order->getShippings()[0];
        //受取予定日 
        $arrData['data'][0]['rows'][0]['pickUpDate']              = $ShipInfo->getShippingDeliveryDate() == null ? null : $ShipInfo->getShippingDeliveryDate()->format('Y-m-d');
        
        //前受金 20220420
        $arrData['data'][0]['rows'][0]['partPayment']                   = intval($order->getTotalPrice());//合計をセット
        //受領金額 20220420
        $arrData['data'][0]['rows'][0]['deposit']                   = intval($order->getTotalPrice());//合計をセット
        //おつり 20220420
        $arrData['data'][0]['rows'][0]['charge']                   = 0;//おつりなし
        
        //受注詳細をセット
        $meisaiNo = 1;
        
        /* @var $detail OrderItem */
        foreach ($order->getProductOrderItems() as $detail) {
            
            //オプションの追加料金
            $optional_sum_addprice = 0;
            if($detail->getAdditionalPrice() != null){
            	$optional_sum_addprice = $detail->getAdditionalPrice();
            }
            
            $arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;
	        $arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）
	        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = intval($detail->getTotalPrice()) - intval($optional_sum_addprice);//販売単価(オプション追加料金は含まない)
	        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = intval($detail->getQuantity());//数量
	        
	        //商品規格データ
	        $productClass = $detail->getProductClass();
	        //商品ID
	        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $productClass->getId() + $p_offset;
	        
	        log_info(
	            'スマレジへ送る受注詳細(商品)セット',
	            [
	                'arrData' => $arrData['data'][$meisaiNo]['rows'][0],
	            ]
	        );
	        
            $meisaiNo++;
            
            
            //【オプション追加料金もセットする】
            
            //<   商品ID   >
            // ----- Noキャンドル ID-----
            // Noキャンドル・０ :
            $_no_can_0 = 10368; 
            // Noキャンドル・１ :
            $_no_can_1 = 10369;
            // Noキャンドル・２ : 
            $_no_can_2 = 10370;
            // Noキャンドル・３ : 
            $_no_can_3 = 10371;
            // Noキャンドル・４ : 
            $_no_can_4 = 10372;
            // Noキャンドル・５ : 
            $_no_can_5 = 10373;
            // Noキャンドル・６ : 
            $_no_can_6 = 10374;
            // Noキャンドル・７ : 
            $_no_can_7 = 10375;
            // Noキャンドル・８ : 
            $_no_can_8 = 10376;
            // Noキャンドル・９ : 
            $_no_can_9 = 10377;
            //<   追加料金   >
            // ----- Noキャンドル 単価-----
            // Noキャンドル 販売単価
            $_no_can_price = 132;
            
            //<   商品ID   >
            // ----- 追加プレート ID-----
            $_add_plate_id = 10357;
            //<   追加料金   >
            // ----- 追加プレート 単価-----
            $_add_plate_price = 65;
            
            //<   商品ID   >
            // ----- デコレーション追加 ID-----
            // いちごUP
            $_deco_add_ichigo_up_id = 10351; 
            // フルーツUP
            $_deco_add_fruit_up_id = 10349;
            // 生チョコUP 
            $_deco_add_namachoco_up_id = 10350;
            // 絵チョコ(4号) 
            $_deco_add_echoco_4_id = 10340;
            //<   追加料金   >
            // ----- デコレーション追加 単価-----
            // いちごUP
            $_deco_add_ichigo_up_price = 378; 
            // フルーツUP
            $_deco_add_fruit_up_price = 378;
            // 生チョコUP 
            $_deco_add_namachoco_up_price = 378;
            // 絵チョコ(4号) 
            $_deco_add_echoco_4_price = 756;
            
            //<   商品ID   >
            // ----- ポリ袋 ID-----
            // ポリ中
            $_poli_cyu_id = 10237; 
            // ポリ大
            $_poli_dai_id = 10238;
            // ポリ特大 
            $_poli_tokudai_id = 10239;
            //<   追加料金   >
            // ----- ポリ袋 単価-----
            // ポリ中
            $_poli_cyu_price = 22; 
            // ポリ大
            $_poli_dai_price = 22;
            // ポリ特大 
            $_poli_tokudai_price = 110;
            
            
            //ToDo  No.キャンドル(0,2～9)、追加プレート(2～5)、デコレーション追加(いちごUP、フルーツUP、生チョコUP、絵チョコ(4号))、ポリ袋(中、大、特大)　　もNo.キャンドル1同様に記載すること！！
            
            
            // [ケーキ]
            // A.) No.キャンドル(0～9)
            // 　No.1
            if($detail->getOptionCandleNo1Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo1Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_1 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.2
            if($detail->getOptionCandleNo2Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo2Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_2 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.3
            if($detail->getOptionCandleNo3Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo3Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_3 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.4
            if($detail->getOptionCandleNo4Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo4Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_4 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.5
            if($detail->getOptionCandleNo5Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo5Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_5 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.6
            if($detail->getOptionCandleNo6Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo6Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_6 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.7
            if($detail->getOptionCandleNo7Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo7Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_7 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.8
            if($detail->getOptionCandleNo8Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo8Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_8 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.9
            if($detail->getOptionCandleNo9Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo9Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_9 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　No.0
            if($detail->getOptionCandleNo0Num() != null){
            	$numstr = str_replace('本', '', $detail->getOptionCandleNo0Num() );
            	$num = intval($numstr);
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_no_can_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_no_can_0 + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            
            
            
            // B.) 追加プレート(2～5)
            // 　2～5枚目
            $addPlateNum = 0;
            if($detail->getOptionPrintnamePlate2() != null){
            	$addPlateNum = $addPlateNum + 1;
            }
            if($detail->getOptionPrintnamePlate3() != null){
            	$addPlateNum = $addPlateNum + 1;
            }
            if($detail->getOptionPrintnamePlate4() != null){
            	$addPlateNum = $addPlateNum + 1;
            }
            if($detail->getOptionPrintnamePlate5() != null){
            	$addPlateNum = $addPlateNum + 1;
            }
            if($addPlateNum > 0){
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_add_plate_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $addPlateNum;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_add_plate_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            
            // C.) デコレーション追加(いちごUP、フルーツUP、生チョコUP、絵チョコ(4号))
            // 　いちごUP
            if($detail->getOptionDecoIchigoChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_deco_add_ichigo_up_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_deco_add_ichigo_up_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　フルーツUP
            if($detail->getOptionDecoFruitChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_deco_add_fruit_up_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_deco_add_fruit_up_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　生チョコUP
            if($detail->getOptionDecoNamachocoChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_deco_add_namachoco_up_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_deco_add_namachoco_up_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　絵チョコ(4号)
            if($detail->getOptionDecoEchocoChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_deco_add_echoco_4_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_deco_add_echoco_4_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            
            // [袋]
            // D.) ポリ袋(中、大、特大)
            // 　ポリ袋(中)
            if($detail->getOptionPoriCyuChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_poli_cyu_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_poli_cyu_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　ポリ袋(大)
            if($detail->getOptionPoriDaiChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_poli_dai_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_poli_dai_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
            // 　ポリ袋(特大)
            if($detail->getOptionPoriTokudaiChk() != null){
            	$num = 1;
            	
            	//共通
            	$arrData['data'][$meisaiNo]['table_name'] = self::TRANSACTION_DETAIL;　固定;
            	$arrData['data'][$meisaiNo]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）　固定
		        $arrData['data'][$meisaiNo]['rows'][0]['salesPrice'] = $_poli_tokudai_price;//販売単価　ToDo直値指定じゃなく動的にしたい
		        $arrData['data'][$meisaiNo]['rows'][0]['quantity']   = $num;//数量
		        $arrData['data'][$meisaiNo]['rows'][0]['productId'] = $_poli_tokudai_id + $p_offset;//商品ID　ToDo直値指定じゃなく動的にしたい
		        
            	$meisaiNo++;
            }
        }
        
        /*
        log_info(
            'スマレジへ受注データ登録　setTransactionUpdate',
            [
                'arrData' => $arrData,
            ]
        );
        */
        
        return $arrData;
    }
    
    public function setTransactionDetailUpdate(Order $order){

        $arrData = array();
        
        //端末取引日時
        $terminalTranDateTime = $order->getOrderDate()->format('Y-m-d H:i:s');
        
        $arrData['proc_info']['proc_division'] = "U"; // Command settings
        $arrData['data'][0]['table_name'] = self::TRANSACTION_DETAIL;
        $arrData['data'][0]['rows'][0]['transactionDetailDivision'] = 1;//取引明細を識別する区分。（1：通常、2：返品、3：部門売り）
        $arrData['data'][0]['rows'][0]['salesPrice'] = $order->getTotalPrice();//販売単価
        $arrData['data'][0]['rows'][0]['quantity'] = $order->getQuantity();//数量
        
        log_info(
            'スマレジへ受注データ登録　setTransactionDetailUpdate',
            [
                'arrData' => $arrData,
            ]
        );
        
        return $arrData;
    }
    
    public function getTransaction(Order $order, int $p_offset, int $u_offset){

        $arrData = array();
        $arrData['conditions'][0]['terminalTranDateTime'] = $order->getOrderDate()->format('Y-m-d H:i:s');
        $arrData['order'][0] = "transactionHeadId";
        $arrData['table_name'] = self::TRANSACTION_HEAD;
        
        return $arrData;
    }

}
