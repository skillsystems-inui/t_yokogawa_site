--accept_shop

--σζXά}X^πVKΗΑ
CREATE TABLE IF NOT EXISTS `dtb_accept_shop` (
  `id` int(10) unsigned NOT NULL,
  `delivery_id` int(10) unsigned DEFAULT NULL,
  `accept_shop` varchar(255) NOT NULL,
  `sort_no` smallint(5) unsigned NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `dtb_accept_shop`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `dtb_delivery_time`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

---id πauto_incrementΙέθ·ι±ΖI

--INSERT INTO `dtb_delivery_time` (`delivery_id`, `delivery_time`, `sort_no`, `visible`, `create_date`, `update_date`, `discriminator_type`) VALUES
INSERT INTO `dtb_accept_shop` (`accept_shop`, `sort_no`, `visible`, `create_date`, `update_date`, `discriminator_type`) VALUES
('aς{X', 1, 1, '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'acceptshop'),
('έacX', 2, 1, '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'acceptshop');


--ze[uΙσζXάπΗΑ
ALTER TABLE `dtb_shipping` ADD `accept_shop` varchar(255) DEFAULT NULL;


--oΧ\θϊπΗΑ
ALTER TABLE `dtb_shipping` ADD `shukkayotei_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)';
  