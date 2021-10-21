--ポイント履歴テーブルを新規追加
CREATE TABLE IF NOT EXISTS `dtb_point_history` (
  `id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  
  `sum` decimal(12,0) NOT NULL DEFAULT 0,
  `ec_online` decimal(12,0) NOT NULL DEFAULT 0,
  `ec_yoyaku` decimal(12,0) NOT NULL DEFAULT 0,
  `app_birth` decimal(12,0) NOT NULL DEFAULT 0,
  `shop_honten` decimal(12,0) NOT NULL DEFAULT 0,
  `shop_kishiwada` decimal(12,0) NOT NULL DEFAULT 0,
  
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `dtb_point_history`
  ADD PRIMARY KEY (`id`);
  
  

