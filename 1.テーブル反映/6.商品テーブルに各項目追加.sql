--��ǉ�(�̔����ԁA�̔���ށA���e�ʁA�ܖ������A��������A���ޗ��A���ǂ݂�������)
ALTER TABLE `dtb_product` ADD `sales_period_from` datetime DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `sales_period_to` datetime DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `sales_type` smallint(5) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `capacity` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `expiry_date` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `expiration_date` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `material` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `please_read` longtext;

--�X�}���W�p�J�e�S��--
CREATE TABLE IF NOT EXISTS `dtb_product_smart_category` (
  `product_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `dtb_product_smart_category`
  ADD PRIMARY KEY (`product_id`,`category_id`);