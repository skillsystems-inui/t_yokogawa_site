--オプションid1～10
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id1` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id2` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id3` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id4` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id5` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id6` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id7` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id8` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id9` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id10` int(10) unsigned DEFAULT NULL;



--名入れ(プレート、熨斗)
ALTER TABLE `dtb_cart_item` ADD `printname_plate` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `printname_noshi` varchar(255) DEFAULT NULL;



--追加価格★
ALTER TABLE `dtb_cart_item` ADD `additional_price` decimal(12,2) NOT NULL DEFAULT '0.00';


--オプションid11～20(拡張)
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id11` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id12` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id13` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id14` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id15` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id16` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id17` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id18` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id19` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_class_category_id20` int(10) unsigned DEFAULT NULL;



--オプション選択内容(文字列)
ALTER TABLE `dtb_cart_item` ADD `option_detail` varchar(1024) DEFAULT NULL;