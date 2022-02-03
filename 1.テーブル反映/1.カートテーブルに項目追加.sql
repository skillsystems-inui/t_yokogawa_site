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

--受取方法 (tentou:店頭受取、toriyose:お取り寄せ)
ALTER TABLE `dtb_cart` ADD `uketori_type` varchar(20) DEFAULT NULL;




--各オプション別(cart)
ALTER TABLE `dtb_cart_item` ADD `option_candle_dai_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_syo_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no1_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no2_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no3_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no4_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no5_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no6_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no7_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no8_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no9_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_candle_no0_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_printname_plate1` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_printname_plate2` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_printname_plate3` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_printname_plate4` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_printname_plate5` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_deco_ichigo_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_deco_fruit_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_deco_namachoco_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_deco_echoco_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_pori_cyu_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_pori_dai_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_pori_tokudai_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_housou_sentaku` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_noshi_kakekata` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_kakehousou_syurui` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_uwagaki_sentaku` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_cart_item` ADD `option_printname_nosina` varchar(100) DEFAULT NULL;
--各オプション別(order)
ALTER TABLE `dtb_order_item` ADD `option_candle_dai_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_syo_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no1_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no2_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no3_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no4_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no5_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no6_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no7_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no8_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no9_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_candle_no0_num` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_plate1` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_plate2` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_plate3` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_plate4` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_plate5` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_deco_ichigo_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_deco_fruit_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_deco_namachoco_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_deco_echoco_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_pori_cyu_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_pori_dai_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_pori_tokudai_chk` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_housou_sentaku` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_noshi_kakekata` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_kakehousou_syurui` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_uwagaki_sentaku` varchar(100) DEFAULT NULL;
ALTER TABLE `dtb_order_item` ADD `option_printname_nosina` varchar(100) DEFAULT NULL;