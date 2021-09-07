--列追加(会員)
ALTER TABLE `dtb_customer` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `addr_all` varchar(510) DEFAULT NULL;

--列追加(運送)
ALTER TABLE `dtb_shipping` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_shipping` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_shipping` ADD `addr_all` varchar(510) DEFAULT NULL;


--列追加(会員アドレス) 
ALTER TABLE `dtb_customer_address` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer_address` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer_address` ADD `addr_all` varchar(510) DEFAULT NULL;


--列追加(注文)
ALTER TABLE `dtb_order` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_order` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_order` ADD `addr_all` varchar(510) DEFAULT NULL;


--CSV項目にも追加(会員)
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'name_all', NULL, 'お名前(姓+名)', 4, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'kana_all', NULL, 'お名前(セイ+メイ)', 6, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'addr_all', NULL, '住所(1+2)', 13, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Order', 'name_all', NULL, 'お名前(姓+名)', 7, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Order', 'kana_all', NULL, 'お名前(セイ+メイ)', 9, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Order', 'addr_all', NULL, '住所(1+2)', 14, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'name_all', NULL, '配送先_お名前(姓+名)', 64, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'kana_all', NULL, '配送先_お名前(セイ+メイ)', 66, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'addr_all', NULL, '配送先_住所(1+2)', 72, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'name_all', NULL, 'お名前(姓+名)', 6, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'kana_all', NULL, 'お名前(セイ+メイ)', 8, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'addr_all', NULL, '住所(1+2)', 14, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'name_all', NULL, '配送先_お名前(姓+名)', 71, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'kana_all', NULL, '配送先_お名前(セイ+メイ)', 73, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'addr_all', NULL, '配送先_住所(1+2)', 79, 1, NOW(), NOW(), 'csv');


--家族紐づけ(家族代表フラグ、家族代表会員ID)
ALTER TABLE `dtb_customer` ADD `is_family_main` smallint(5) DEFAULT 0;
ALTER TABLE `dtb_customer` ADD `family_main_customer_id` int(10) unsigned  DEFAULT NULL;




----- 家族代表区分テーブル ------
--
-- テーブルの構造 `mtb_family_main`
--

CREATE TABLE IF NOT EXISTS `mtb_family_main` (
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_no` smallint(5) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mtb_family_main`
  ADD PRIMARY KEY (`id`);
  
INSERT INTO `mtb_family_main` (`id`, `name`, `sort_no`, `discriminator_type`) VALUES
(0, '代表者ではない', 0, 'familymain'),
(1, '代表者である', 1, 'familymain');
----- .家族代表区分テーブル ------

--列追加(家族代表会員名)
ALTER TABLE `dtb_customer` ADD `mainname` varchar(510) DEFAULT NULL;

--CSV項目にも追加(家族代表区分)
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'Familymain', 'name', '家族代表フラグ', 4, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'Maincustomer', 'name_all', '家族代表会員', 6, 1, NOW(), NOW(), 'csv');






--会員　家族情報
-----家族情報01　続柄
ALTER TABLE `dtb_customer` ADD `family_relation01` varchar(50) DEFAULT NULL;
-----家族情報01　誕生日
ALTER TABLE `dtb_customer` ADD `family_birth01` datetime DEFAULT NULL;
