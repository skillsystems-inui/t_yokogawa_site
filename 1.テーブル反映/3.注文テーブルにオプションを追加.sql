--列追加
ALTER TABLE `dtb_order_item` ADD `class_category_id1` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id2` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id3` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id4` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id5` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id6` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id7` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id8` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id9` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id10` int(10) unsigned DEFAULT NULL;

ALTER TABLE `dtb_order_item` ADD `option_name1` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name2` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name3` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name4` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name5` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name6` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name7` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name8` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name9` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name10` varchar(255) DEFAULT NULL;

ALTER TABLE `dtb_order_item` ADD `option_category_name1` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name2` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name3` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name4` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name5` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name6` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name7` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name8` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name9` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name10` varchar(255) DEFAULT NULL;

ALTER TABLE `dtb_order_item` ADD `option_printname_plate` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_noshi` varchar(255) DEFAULT NULL;

--注文枝番を追加
ALTER TABLE `dtb_order_item` ADD `order_sub_no` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` MODIFY `order_sub_no` varchar(255) NULL DEFAULT NULL;

--オプションによる追加料金
ALTER TABLE `dtb_order_item` ADD `additional_price` decimal(12,2) NOT NULL DEFAULT '0.00';

--オプション拡張(11～20)
ALTER TABLE `dtb_order_item` ADD `class_category_id11` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id12` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id13` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id14` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id15` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id16` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id17` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id18` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id19` int(10) unsigned DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `class_category_id20` int(10) unsigned DEFAULT NULL;

ALTER TABLE `dtb_order_item` ADD `option_name11` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name12` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name13` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name14` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name15` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name16` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name17` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name18` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name19` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_name20` varchar(255) DEFAULT NULL;

ALTER TABLE `dtb_order_item` ADD `option_category_name11` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name12` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name13` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name14` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name15` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name16` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name17` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name18` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name19` varchar(255) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_category_name20` varchar(255) DEFAULT NULL;

--オプション選択内容(文字列)
ALTER TABLE `dtb_order_item` ADD `option_detail` varchar(1024) DEFAULT NULL;


--スマレジの取引ID
ALTER TABLE `dtb_order` ADD `smaregi_id` varchar(20) DEFAULT NULL;