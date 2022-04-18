--CSV種別:受注CSV (dtb_csv:3)

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'ClassCategory', 'id', '商品オプションID', 72, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name1', NULL, 'オプション名1', 73, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name2', NULL, 'オプション名2', 74, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name3', NULL, 'オプション名3', 75, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name4', NULL, 'オプション名4', 76, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name5', NULL, 'オプション名5', 77, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name6', NULL, 'オプション名6', 78, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name7', NULL, 'オプション名7', 79, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name8', NULL, 'オプション名8', 80, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name9', NULL, 'オプション名9', 81, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name10', NULL, 'オプション名10', 82, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name1', NULL, 'オプション選択名1', 83, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name2', NULL, 'オプション選択名2', 84, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name3', NULL, 'オプション選択名3', 85, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name4', NULL, 'オプション選択名4', 86, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name5', NULL, 'オプション選択名5', 87, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name6', NULL, 'オプション選択名6', 88, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name7', NULL, 'オプション選択名7', 89, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name8', NULL, 'オプション選択名8', 90, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name9', NULL, 'オプション選択名9', 91, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name10', NULL, 'オプション選択名10', 92, 1, NOW(), NOW(), 'csv');



----- 注文枝番 
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'order_sub_no', NULL, '注文枝番', 92, 1, NOW(), NOW(), 'csv');






--CSV種別:商品CSV (dtb_csv:1)
------※※オプション1～10のidとnameはdtb_product_class_categoryの指定のレコード(1商品1データ)から取得する必要がある
------    (予想される要望)CSV出力メニューとしてオリジナル(単純に受注や商品ではなく、月末に使うとか特化した内容)を設けておく
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'ProductClassNames', 'ClassName', 'オプション', 22, 1, NOW(), NOW(), 'csv');
/*
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'ClassCategory', 'id', '商品オプションID', 32, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name1', NULL, 'オプション名1', 33, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name2', NULL, 'オプション名2', 34, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name3', NULL, 'オプション名3', 35, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name4', NULL, 'オプション名4', 36, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name5', NULL, 'オプション名5', 37, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name6', NULL, 'オプション名6', 38, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name7', NULL, 'オプション名7', 39, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name8', NULL, 'オプション名8', 40, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name9', NULL, 'オプション名9', 41, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name10', NULL, 'オプション名10', 42, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name1', NULL, 'オプション選択名1', 43, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name2', NULL, 'オプション選択名2', 44, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name3', NULL, 'オプション選択名3', 45, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name4', NULL, 'オプション選択名4', 46, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name5', NULL, 'オプション選択名5', 47, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name6', NULL, 'オプション選択名6', 48, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name7', NULL, 'オプション選択名7', 49, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name8', NULL, 'オプション選択名8', 50, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name9', NULL, 'オプション選択名9', 51, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name10', NULL, 'オプション選択名10', 52, 1, NOW(), NOW(), 'csv');
*/


--CSV種別:配送CSV (dtb_csv:4)

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'ClassCategory', 'id', '商品オプションID', 72, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name1', NULL, 'オプション名1', 73, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name2', NULL, 'オプション名2', 74, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name3', NULL, 'オプション名3', 75, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name4', NULL, 'オプション名4', 76, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name5', NULL, 'オプション名5', 77, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name6', NULL, 'オプション名6', 78, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name7', NULL, 'オプション名7', 79, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name8', NULL, 'オプション名8', 80, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name9', NULL, 'オプション名9', 81, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_name10', NULL, 'オプション名10', 82, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name1', NULL, 'オプション選択名1', 83, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name2', NULL, 'オプション選択名2', 84, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name3', NULL, 'オプション選択名3', 85, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name4', NULL, 'オプション選択名4', 86, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name5', NULL, 'オプション選択名5', 87, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name6', NULL, 'オプション選択名6', 88, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name7', NULL, 'オプション選択名7', 89, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name8', NULL, 'オプション選択名8', 90, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name9', NULL, 'オプション選択名9', 91, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_category_name10', NULL, 'オプション選択名10', 92, 1, NOW(), NOW(), 'csv');


--CSV種別:受注CSV (dtb_csv:3)
--オプション内容を追加
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_detail', NULL, 'オプション内容', 92, 1, NOW(), NOW(), 'csv');




--CSV種別:受注CSV (dtb_csv:3)
--オプションごとの列を追加 20220125
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_dai_num', NULL, 'オプション_キャンドル大_数', 101, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_syo_num', NULL, 'オプション_キャンドル小_数', 102, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no1_num', NULL, 'オプション_Noキャンドル1_数', 103, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no2_num', NULL, 'オプション_Noキャンドル2_数', 104, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no3_num', NULL, 'オプション_Noキャンドル3_数', 105, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no4_num', NULL, 'オプション_Noキャンドル4_数', 106, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no5_num', NULL, 'オプション_Noキャンドル5_数', 107, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no6_num', NULL, 'オプション_Noキャンドル6_数', 108, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no7_num', NULL, 'オプション_Noキャンドル7_数', 109, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no8_num', NULL, 'オプション_Noキャンドル8_数', 110, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no9_num', NULL, 'オプション_Noキャンドル9_数', 111, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no0_num', NULL, 'オプション_Noキャンドル0_数', 112, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate1', NULL, 'オプション_メッセージプレート1_内容', 113, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate2', NULL, 'オプション_メッセージプレート2_内容', 114, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate3', NULL, 'オプション_メッセージプレート3_内容', 115, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate4', NULL, 'オプション_メッセージプレート4_内容', 116, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate5', NULL, 'オプション_メッセージプレート5_内容', 117, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_ichigo_chk', NULL, 'オプション_デコレーション_いちごUP_選択', 118, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_fruit_chk', NULL, 'オプション_デコレーション_フルーツUP_選択', 119, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_namachoco_chk', NULL, 'オプション_デコレーション_生チョコUP_選択', 120, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_echoco_chk', NULL, 'オプション_デコレーション_絵チョコ(4号)_選択', 121, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_pori_cyu_chk', NULL, 'オプション_持ち帰りポリ袋_中_選択', 122, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_pori_dai_chk', NULL, 'オプション_持ち帰りポリ袋_大_選択', 123, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_pori_tokudai_chk', NULL, 'オプション_持ち帰りポリ袋_特大_選択', 124, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_housou_sentaku', NULL, 'オプション_包装方法選択', 125, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_noshi_kakekata', NULL, 'オプション_熨斗紙の掛け方', 126, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_kakehousou_syurui', NULL, 'オプション_掛け紙包装の種類選択', 127, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_uwagaki_sentaku', NULL, 'オプション_上書きを選択', 128, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_noshi', NULL, 'オプション_熨斗の名入れ希望', 129, 1, NOW(), NOW(), 'csv');


--CSV種別:出荷(配送)CSV (dtb_csv:4)
--オプションごとの列を追加 20220125
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_dai_num', NULL, 'オプション_キャンドル大_数', 101, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_syo_num', NULL, 'オプション_キャンドル小_数', 102, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no1_num', NULL, 'オプション_Noキャンドル1_数', 103, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no2_num', NULL, 'オプション_Noキャンドル2_数', 104, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no3_num', NULL, 'オプション_Noキャンドル3_数', 105, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no4_num', NULL, 'オプション_Noキャンドル4_数', 106, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no5_num', NULL, 'オプション_Noキャンドル5_数', 107, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no6_num', NULL, 'オプション_Noキャンドル6_数', 108, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no7_num', NULL, 'オプション_Noキャンドル7_数', 109, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no8_num', NULL, 'オプション_Noキャンドル8_数', 110, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no9_num', NULL, 'オプション_Noキャンドル9_数', 111, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_candle_no0_num', NULL, 'オプション_Noキャンドル0_数', 112, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate1', NULL, 'オプション_メッセージプレート1_内容', 113, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate2', NULL, 'オプション_メッセージプレート2_内容', 114, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate3', NULL, 'オプション_メッセージプレート3_内容', 115, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate4', NULL, 'オプション_メッセージプレート4_内容', 116, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_plate5', NULL, 'オプション_メッセージプレート5_内容', 117, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_ichigo_chk', NULL, 'オプション_デコレーション_いちごUP_選択', 118, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_fruit_chk', NULL, 'オプション_デコレーション_フルーツUP_選択', 119, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_namachoco_chk', NULL, 'オプション_デコレーション_生チョコUP_選択', 120, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_deco_echoco_chk', NULL, 'オプション_デコレーション_絵チョコ(4号)_選択', 121, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_pori_cyu_chk', NULL, 'オプション_持ち帰りポリ袋_中_選択', 122, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_pori_dai_chk', NULL, 'オプション_持ち帰りポリ袋_大_選択', 123, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_pori_tokudai_chk', NULL, 'オプション_持ち帰りポリ袋_特大_選択', 124, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_housou_sentaku', NULL, 'オプション_包装方法選択', 125, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_noshi_kakekata', NULL, 'オプション_熨斗紙の掛け方', 126, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_kakehousou_syurui', NULL, 'オプション_掛け紙包装の種類選択', 127, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_uwagaki_sentaku', NULL, 'オプション_上書きを選択', 128, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_printname_noshi', NULL, 'オプション_熨斗の名入れ希望', 129, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_plate_sentaku', NULL, 'オプション_メッセージプレート選択', 130, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'option_plate_sentaku', NULL, 'オプション_メッセージプレート選択', 130, 1, NOW(), NOW(), 'csv');



----- 注文枝番  20220125 出荷(配送)CSVにも追加
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(4, 1, 'Eccube\\\\Entity\\\\OrderItem', 'order_sub_no', NULL, '注文枝番', 130, 1, NOW(), NOW(), 'csv');

----- スマレジ取引ID  20220125 受注、出荷(配送)CSVにも追加する
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\Order', 'smaregi_id', NULL, 'スマレジ取引ID', 130, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(4, 1, 'Eccube\\\\Entity\\\\Order', 'smaregi_id', NULL, 'スマレジ取引ID', 130, 1, NOW(), NOW(), 'csv');



--会員コード
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\Order', 'Customer', 'customer_code', '会員コード', 56, 1, '2021-09-02 03:47:59', '2022-02-04 11:47:05', 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'Customer', 'customer_code', '会員コード', 56, 1, '2021-09-02 03:47:59', '2022-02-04 11:47:05', 'csv');

--出荷予定日
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'shipping_shukkayotei_date', NULL, '出荷予定日', 6, 1, '2021-09-02 03:47:59', '2022-02-04 11:47:05', 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'shipping_shukkayotei_date', NULL, '出荷予定日', 56, 0, '2021-09-02 03:47:59', '2022-03-27 04:19:48', 'csv');


--販売タイプ
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(3, 1, 'Eccube\\\\Entity\\\\Order', 'uketori_type', NULL, '販売タイプ(ECから取り置き注文：1、ECから配送注文：2、店舗で注文：空白)', 6, 1, '2021-09-02 03:47:59', '2022-02-04 11:47:05', 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'uketori_type', NULL, '販売タイプ(ECから取り置き注文：1、ECから配送注文：2、店舗で注文：空白)', 56, 0, '2021-09-02 03:47:59', '2022-03-27 04:19:48', 'csv');