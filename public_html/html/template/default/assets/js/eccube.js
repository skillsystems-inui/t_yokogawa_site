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

(function(window, undefined) {

    // 名前空間の重複を防ぐ
    if (window.eccube === undefined) {
        window.eccube = {};
    }

    var eccube = window.eccube;

    // グローバルに使用できるようにする
    window.eccube = eccube;

    /**
     * 規格2のプルダウンを設定する.
     */
    eccube.setClassCategories = function($form, product_id, $sele1, $sele2, selected_id2) {
        if ($sele1 && $sele1.length) {
            var classcat_id1 = $sele1.val() ? $sele1.val() : '';
            if ($sele2 && $sele2.length) {
                // 規格2の選択肢をクリア
                $sele2.children().remove();

                var classcat2;

                if (eccube.hasOwnProperty('productsClassCategories')) {
                    // 商品一覧時
                    classcat2 = eccube.productsClassCategories[product_id][classcat_id1];
                } else {
                    // 詳細表示時
                    classcat2 = eccube.classCategories[classcat_id1];
                }

                // 規格2の要素を設定
                for (var key in classcat2) {
                    if (classcat2.hasOwnProperty(key)) {
                        var id = classcat2[key].classcategory_id2;
                        var name = classcat2[key].name;
                        var option = $('<option />').val(id ? id : '').text(name);
                        if (id === selected_id2) {
                            option.attr('selected', true);
                        }
                        $sele2.append(option);
                    }
                }
                eccube.checkStock($form, product_id, $sele1.val() ? $sele1.val() : '__unselected2',
                    $sele2.val() ? $sele2.val() : '');
            }
        }
    };

    /**
     * 規格の選択状態に応じて, フィールドを設定する.
     */
    var price02_origin = [];
    var price02_orgkikaku = [];
    eccube.checkStock = function($form, product_id, classcat_id1, classcat_id2, option_id1 = null, option_id2 = null, option_id3 = null, option_id4 = null, option_id5 = null, option_id6 = null, option_id7 = null, option_id8 = null, option_id9 = null, option_id10 = null) {

        classcat_id2 = classcat_id2 ? classcat_id2 : '';
        var classcat2;

        if (eccube.hasOwnProperty('productsClassCategories')) {
            // 商品一覧時
            classcat2 = eccube.productsClassCategories[product_id][classcat_id1]['#' + classcat_id2];
        } else {
            // 詳細表示時
            if (typeof eccube.classCategories[classcat_id1] !== 'undefined') {
                classcat2 = eccube.classCategories[classcat_id1]['#' + classcat_id2];
            }
        }
        
        //----- 追加価格(オプション) -----
        var $addprice = 0;//初期値0円
        
        //オプション選択による追加価格
        var $additional_price_dynamic = $form.find('[id^=additional_price]');
        $additional_price_dynamic.val($addprice);
        
        if (typeof classcat2 === 'undefined') {
            // 商品コード
            var $product_code = $('.product-code-default');
            if (typeof this.product_code_origin === 'undefined') {
                // 初期値を保持しておく
                this.product_code_origin = $product_code.text();
            }
            $product_code.text(this.product_code_origin);

            // 在庫(品切れ)
            var $cartbtn = $form.parent().find('.add-cart').first();
            if (typeof this.product_cart_origin === 'undefined') {
                // 初期値を保持しておく
                this.product_cart_origin = $cartbtn.text();
            }
            $cartbtn.prop('disabled', false);
            $cartbtn.text(this.product_cart_origin);

            // 通常価格
            var $price01 = $form.parent().find('.price01-default').first();
            if (typeof this.price01_origin === 'undefined') {
                // 初期値を保持しておく
                this.price01_origin = $price01.text();
            }
            $price01.text(this.price01_origin);

            // 販売価格
            var $price02 = $form.parent().find('.price02-default').first();
            if (typeof price02_origin[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_origin[product_id] = $price02.text();
            }
            $price02.text(price02_origin[product_id]);
            
            // 販売価格(規格のみ)
            var $price02_kikaku = $form.parent().find('.price02-view-default').first();
            if (typeof price02_orgkikaku[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_orgkikaku[product_id] = $price02_kikaku.text();
            }
            $price02_kikaku.text(price02_orgkikaku[product_id]);

            // 商品規格
            var $product_class_id_dynamic = $form.find('[id^=ProductClass]');
            $product_class_id_dynamic.val('');

        } else {
            // 商品コード
            var $product_code = $('.product-code-default');
            if (classcat2 && typeof classcat2.product_code !== 'undefined') {
                $product_code.text(classcat2.product_code);
            } else {
                $product_code.text(this.product_code_origin);
            }
            
            // 在庫(品切れ)
            var $cartbtn = $form.parent().find('.add-cart').first();
            if (typeof this.product_cart_origin === 'undefined') {
                // 初期値を保持しておく
                this.product_cart_origin = $cartbtn.text();
            }
            if (classcat2 && classcat2.stock_find === false) {
                $cartbtn.prop('disabled', true);
                $cartbtn.text('ただいま品切れ中です');
            } else {
                $cartbtn.prop('disabled', false);
                $cartbtn.text(this.product_cart_origin);
            }

            //オプション選択による追加価格を反映
            poribag_price = eccube.getOptionPrice($form);
            
            // 通常
            //通常価格を数値変換する(ポリ袋の価格を加算するため)
            const str_price1 = classcat2.price01_inc_tax;
            const rep_str_price1 = str_price1.replace(/,/g, '');
            //数値かつポリ袋選択ならポリ袋代金をする
        	//※数値しか来ない前提
        	num_price1 = parseInt(rep_str_price1) + poribag_price;
            
            // 販売
            //販売価格を数値変換する(ポリ袋の価格を加算するため)
            const str_price2 = classcat2.price02_inc_tax;
            const rep_str_price2 = str_price2.replace(/,/g, '');
            //数値かつポリ袋選択ならポリ袋代金をする
        	//※数値しか来ない前提
        	num_price2 = parseInt(rep_str_price2) + poribag_price;
            
            
            // 通常価格
            var $price01 = $form.parent().find('.price01-default').first();
            if (typeof this.price01_origin === 'undefined') {
                // 初期値を保持しておく
                this.price01_origin = $price01.text();
            }
            if (classcat2 && typeof classcat2.price01_inc_tax !== 'undefined' && String(classcat2.price01_inc_tax).length >= 1) {
                //規格の販売価格(1,000など)
                //$price01.text('￥' + classcat2.price01_inc_tax);
                s_price1 = String(num_price1);
                $price01.text('￥' + s_price1);
            } else {
                //画面表示時の価格(1,000～3,000など)
                $price01.text(this.price01_origin);
            }
            
            // 販売価格
            var $price02 = $form.parent().find('.price02-default').first();
            if (typeof price02_origin[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_origin[product_id] = $price02.text();
            }
            if (classcat2 && typeof classcat2.price02_inc_tax !== 'undefined' && String(classcat2.price02_inc_tax).length >= 1) {                
                //規格の販売価格(1,000など)
                //$price02.text('￥' + classcat2.price02_inc_tax);
                s_price2 = String(num_price2);
                $price02.text('￥' + s_price2);
            } else {
            	//画面表示時の価格(1,000～3,000など)
                $price02.text(price02_origin[product_id]);
            }
            
            // 販売価格
            var $price02_kikaku = $form.parent().find('.price02-view-default').first();
            if (typeof price02_orgkikaku[product_id] === 'undefined') {
                // 初期値を保持しておく
                price02_orgkikaku[product_id] = $price02_kikaku.text();
            }
            if (classcat2 && typeof classcat2.price02_inc_tax !== 'undefined' && String(classcat2.price02_inc_tax).length >= 1) {                
                //規格の販売価格(1,000など)
                //$price02_kikaku.text('￥' + classcat2.price02_inc_tax);
                s_price2 = String(num_price2);
                $price02_kikaku.text('￥' + s_price2);
            } else {
            	//画面表示時の価格(1,000～3,000など)
                $price02_kikaku.text(price02_orgkikaku[product_id]);
            }

            // ポイント
            var $point_default = $form.find('[id^=point_default]');
            var $point_dynamic = $form.find('[id^=point_dynamic]');
            if (classcat2 && typeof classcat2.point !== 'undefined' && String(classcat2.point).length >= 1) {

                $point_dynamic.text(classcat2.point).show();
                $point_default.hide();
            } else {
                $point_dynamic.hide();
                $point_default.show();
            }

            // 商品規格
            var $product_class_id_dynamic = $form.find('[id^=ProductClass]');
            if (classcat2 && typeof classcat2.product_class_id !== 'undefined' && String(classcat2.product_class_id).length >= 1) {
                $product_class_id_dynamic.val(classcat2.product_class_id);
            } else {
                $product_class_id_dynamic.val('');
            }
        }
    };
    
    /**
     * 追加価格のオプションがあれば追加価格を取得する.
     */
    eccube.checkOptionPrice = function($form) {
        
        //----- 追加価格(オプション) -----
        var $addprice = 0;//初期値0円
        
        //オプション1
        var $optn1 = $form.find('input[name=optioncategory_id1] option:selected');
        var yen1 = eccube.getPerOptionPrice($form, $optn1);
        $addprice = $addprice +  yen1;
        
        //オプション2
        var $optn2 = $form.find('input[name=optioncategory_id2] option:selected');
        var yen2 = eccube.getPerOptionPrice($form, $optn2);
        $addprice = $addprice +  yen2;

        //オプション3
        var $optn3 = $form.find('input[name=optioncategory_id3] option:selected');
        var yen3 = eccube.getPerOptionPrice($form, $optn3);
        $addprice = $addprice +  yen3;

        //オプション4
        var $optn4 = $form.find('input[name=optioncategory_id4] option:selected');
        var yen4 = eccube.getPerOptionPrice($form, $optn4);
        $addprice = $addprice +  yen4;

        //オプション5
        var $optn5 = $form.find('input[name=optioncategory_id5] option:selected');
        var yen5 = eccube.getPerOptionPrice($form, $optn5);
        $addprice = $addprice +  yen5;
        
        //オプション6
        var $optn6 = $form.find('input[name=optioncategory_id6] option:selected');
        var yen6 = eccube.getPerOptionPrice($form, $optn6);
        $addprice = $addprice +  yen6;
        
        //オプション7
        var $optn7 = $form.find('input[name=optioncategory_id7] option:selected');
        var yen7 = eccube.getPerOptionPrice($form, $optn7);
        $addprice = $addprice +  yen7;

        //オプション8
        var $optn8 = $form.find('input[name=optioncategory_id8] option:selected');
        var yen8 = eccube.getPerOptionPrice($form, $optn8);
        $addprice = $addprice +  yen8;

        //オプション9
        var $optn9 = $form.find('input[name=optioncategory_id9] option:selected');
        var yen9 = eccube.getPerOptionPrice($form, $optn9);
        $addprice = $addprice +  yen9;
        
        //オプション10
        var $optn10 = $form.find('input[name=optioncategory_id10] option:selected');
        var yen10 = eccube.getPerOptionPrice($form, $optn10);
        $addprice = $addprice +  yen10;
        
        //オプション選択による追加価格セット
        var $additional_price_dynamic = $form.find('[id^=additional_price]');
        $additional_price_dynamic.val($addprice);
        
    };
    
    /**
     * 追加価格のオプションがあれば追加価格を取得する.
     */
    eccube.getOptionPrice = function($form) {
        
        //----- 追加価格(オプション) -----
        var $addprice = 0;//初期値0円
        
        //オプション1
        var $optn1 = $form.find('input[name=optioncategory_id1] option:selected');
        var yen1 = eccube.getPerOptionPrice($form, $optn1);
        $addprice = $addprice +  yen1;
        
        //オプション2
        var $optn2 = $form.find('input[name=optioncategory_id2] option:selected');
        var yen2 = eccube.getPerOptionPrice($form, $optn2);
        $addprice = $addprice +  yen2;

        //オプション3
        var $optn3 = $form.find('input[name=optioncategory_id3] option:selected');
        var yen3 = eccube.getPerOptionPrice($form, $optn3);
        $addprice = $addprice +  yen3;

        //オプション4
        var $optn4 = $form.find('input[name=optioncategory_id4] option:selected');
        var yen4 = eccube.getPerOptionPrice($form, $optn4);
        $addprice = $addprice +  yen4;

        //オプション5
        var $optn5 = $form.find('input[name=optioncategory_id5] option:selected');
        var yen5 = eccube.getPerOptionPrice($form, $optn5);
        $addprice = $addprice +  yen5;
        
        //オプション6
        var $optn6 = $form.find('input[name=optioncategory_id6] option:selected');
        var yen6 = eccube.getPerOptionPrice($form, $optn6);
        $addprice = $addprice +  yen6;
        
        //オプション7
        var $optn7 = $form.find('input[name=optioncategory_id7] option:selected');
        var yen7 = eccube.getPerOptionPrice($form, $optn7);
        $addprice = $addprice +  yen7;

        //オプション8
        var $optn8 = $form.find('input[name=optioncategory_id8] option:selected');
        var yen8 = eccube.getPerOptionPrice($form, $optn8);
        $addprice = $addprice +  yen8;

        //オプション9
        var $optn9 = $form.find('input[name=optioncategory_id9] option:selected');
        var yen9 = eccube.getPerOptionPrice($form, $optn9);
        $addprice = $addprice +  yen9;
        
        //オプション10
        var $optn10 = $form.find('input[name=optioncategory_id10] option:selected');
        var yen10 = eccube.getPerOptionPrice($form, $optn10);
        $addprice = $addprice +  yen10;
        
        //オプション選択による追加価格取得
        return parseInt($addprice);
    };
    
    /**
     * オプション単位の追加価格を取得する.
     */
     eccube.getPerOptionPrice = function($form, $optn) {
        //金額初期値0
        var yen = 0;
        //オプション選択
        var $optn_select_text = $optn.text();
		//「+」文言を含むか
        var sp_plus = $optn_select_text.split( '+' );
		//金額があるか
        if(sp_plus.length > 1){
        	var sp2 = sp_plus[1];
        	//「円」文言を含むか
        	var sp3 = sp2.split( '円' );
        	if(sp3.length > 1){
        		yen = sp3[0];
        	}
        }
        //金額を返す
        return parseInt(yen);
        
    };
     


    /**
     * Initialize.
     */
    $(function() {
        // 規格1選択時
        $('input[name=classcategory_id1]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $(this);
                var $sele2 = $form.find('input[name=classcategory_id2]');

                // 規格1のみの場合
                if (!$sele2.length) {
                    eccube.checkStock($form, product_id, $sele1.val(), null);
                    // 規格2ありの場合
                } else {
                    eccube.setClassCategories($form, product_id, $sele1, $sele2);
                }
            });

        // 規格2選択時
        $('input[name=classcategory_id2]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                var $sele2 = $(this);
                eccube.checkStock($form, product_id, $sele1.val(), $sele2.val());
            });
            
        
        // オプション1選択時
        $('input[name=optioncategory_id1]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション2選択時
        $('input[name=optioncategory_id2]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション3選択時
        $('input[name=optioncategory_id3]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション4選択時
        $('input[name=optioncategory_id4]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション5選択時
        $('input[name=optioncategory_id5]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション6選択時
        $('input[name=optioncategory_id6]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション7選択時
        $('input[name=optioncategory_id7]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション8選択時
        $('input[name=optioncategory_id8]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション9選択時
        $('input[name=optioncategory_id9]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
        // オプション10選択時
        $('input[name=optioncategory_id10]')
            .change(function() {
                var $form = $(this).parents('form');
                var product_id = $form.find('input[name=product_id]').val();
                var $sele1 = $form.find('input[name=classcategory_id1]');
                //規格1指定ありの場合のみ在庫チェック
                if($sele1.length > 0){
                	eccube.checkStock($form, product_id, $sele1.val(), null);
                }
            });
    });
})(window);
