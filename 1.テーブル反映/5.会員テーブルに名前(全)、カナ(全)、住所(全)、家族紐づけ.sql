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


-----家族情報02-10　続柄　誕生日
ALTER TABLE `dtb_customer` ADD `family_relation02` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth02` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation03` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth03` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation04` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth04` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation05` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth05` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation06` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth06` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation07` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth07` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation08` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth08` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation09` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_birth09` datetime DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_relation10` varchar(50) DEFAULT NULL;	
ALTER TABLE `dtb_customer` ADD `family_birth10` datetime DEFAULT NULL;

-----家族情報01-10　名前
ALTER TABLE `dtb_customer` ADD `family_name01` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name02` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name03` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name04` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name05` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name06` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name07` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name08` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name09` varchar(50) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_name10` varchar(50) DEFAULT NULL;


-----家族情報01-10　性別
ALTER TABLE `dtb_customer` ADD `family_sex01_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex02_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex03_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex04_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex05_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex06_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex07_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex08_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex09_id` smallint(5) unsigned DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `family_sex10_id` smallint(5) unsigned DEFAULT NULL;


--列追加(会員)
ALTER TABLE `dtb_customer` ADD `customer_code` varchar(255) DEFAULT NULL;

--列追加(デバイストークン1)
ALTER TABLE `dtb_customer` ADD `device_token1` varchar(200) DEFAULT NULL;

--列追加(通知設定フラグ)
ALTER TABLE `dtb_customer` ADD `notice_flg` varchar(5) DEFAULT NULL;

----- 会員コード 
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, NULL, 'Eccube\\\\Entity\\\\Customer', 'customer_code', NULL, '会員コード', 1, 1, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'csv');




--CSV項目にも追加(会員)
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name01', NULL, '家族情報1 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex01', 'name', '家族情報1 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation01', NULL, '家族情報1 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth01', NULL, '家族情報1 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name02', NULL, '家族情報2 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex02', 'name', '家族情報2 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation02', NULL, '家族情報2 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth02', NULL, '家族情報2 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name03', NULL, '家族情報3 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex03', 'name', '家族情報3 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation03', NULL, '家族情報3 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth03', NULL, '家族情報3 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name04', NULL, '家族情報4 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex04', 'name', '家族情報4 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation04', NULL, '家族情報4 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth04', NULL, '家族情報4 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name05', NULL, '家族情報5 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex05', 'name', '家族情報5 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation05', NULL, '家族情報5 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth05', NULL, '家族情報5 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name06', NULL, '家族情報6 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex06', 'name', '家族情報6 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation06', NULL, '家族情報6 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth06', NULL, '家族情報6 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name07', NULL, '家族情報7 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex07', 'name', '家族情報7 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation07', NULL, '家族情報7 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth07', NULL, '家族情報7 誕生日', 50, 1, NOW(), NOW(), 'csv');

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_name08', NULL, '家族情報8 お名前', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_sex08', 'name', '家族情報8 性別(1;男性,2;女性,3;その他)', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_relation08', NULL, '家族情報8 続柄', 50, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'family_birth08', NULL, '家族情報8 誕生日', 50, 1, NOW(), NOW(), 'csv');