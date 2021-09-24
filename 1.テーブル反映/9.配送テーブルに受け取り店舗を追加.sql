--accept_shop

--���X�܃}�X�^��V�K�ǉ�
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

---��id ��auto_increment�ɐݒ肷�邱�ƁI

--INSERT INTO `dtb_delivery_time` (`delivery_id`, `delivery_time`, `sort_no`, `visible`, `create_date`, `update_date`, `discriminator_type`) VALUES
INSERT INTO `dtb_accept_shop` (`accept_shop`, `sort_no`, `visible`, `create_date`, `update_date`, `discriminator_type`) VALUES
('�a�򒆉��{�X', 1, 1, '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'acceptshop'),
('�ݘa�c�X', 2, 1, '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'acceptshop');


--�z���e�[�u���Ɏ��X�܂�ǉ�
ALTER TABLE `dtb_shipping` ADD `accept_shop` varchar(255) DEFAULT NULL;
