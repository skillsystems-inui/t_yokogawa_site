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

