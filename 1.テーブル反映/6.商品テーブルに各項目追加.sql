--��ǉ�(�̔����ԁA�̔���ށA���e�ʁA�ܖ������A��������A���ޗ��A���ǂ݂�������)
ALTER TABLE `dtb_product` ADD `sales_period_from` datetime DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `sales_period_to` datetime DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `sales_type` smallint(5) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `capacity` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `expiry_date` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `expiration_date` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `material` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_product` ADD `please_read` longtext;
