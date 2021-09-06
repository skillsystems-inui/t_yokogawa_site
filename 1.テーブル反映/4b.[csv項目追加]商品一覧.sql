--CSV種別:商品CSV (dtb_csv:1)
------※※オプション1～10のidとnameはdtb_product_class_categoryの指定のレコード(1商品1データ)から取得する必要がある
------    (予想される要望)CSV出力メニューとしてオリジナル(単純に受注や商品ではなく、月末に使うとか特化した内容)を設けておく

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'description_detail', NULL, '商品説明', 4, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'ClassCategory1', 'name', '商品サイズ', 6, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'DeliveryDuration', 'name', '発送日目安', 7, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'ProductCategories', 'Category', '商品カテゴリ', 13, 1, NOW(), NOW(), 'csv');




--CSV種別:商品CSV (dtb_csv:1)
----販売期間、販売種類、内容量、賞味期限、消費期限、原材料、お読みください、自由入力

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'sales_period_from', NULL, '販売期間(開始)', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'sales_period_to', NULL, '販売期間(終了)', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'Salestype', 'name', '販売種類', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'capacity', NULL, '内容量', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'expiry_date', NULL, '賞味期限', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'expiration_date', NULL, '消費期限', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'material', NULL, '原材料', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'please_read', NULL, 'お読みください', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'free_area', NULL, '自由入力', 23, 1, NOW(), NOW(), 'csv')
;





--CSV種別:商品CSV (dtb_csv:1)
---- 定価、表示
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'price01', NULL, '定価', 4, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'Status', 'name', '表示', 6, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'id', NULL, 'ECCUBE商品ID', 4, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'ClassCategory1', 'name', '商品規格', 4, 1, NOW(), NOW(), 'csv');


--CSV種別:商品CSV (dtb_csv:1)
---- 関連商品
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'RelatedProducts', 'ChildProduct', '関連商品', 4, 1, NOW(), NOW(), 'csv');
---- 関連商品ID
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'RelatedProducts', 'child_product_id', '関連商品(ID)', 4, 1, NOW(), NOW(), 'csv');
---- 配送料金
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'delivery_fee', NULL, '配送料金', 1, 0, NOW(), NOW(), 'csv');


