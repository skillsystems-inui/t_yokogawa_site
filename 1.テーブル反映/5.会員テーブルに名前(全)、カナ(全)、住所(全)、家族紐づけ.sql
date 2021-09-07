--��ǉ�(���)
ALTER TABLE `dtb_customer` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer` ADD `addr_all` varchar(510) DEFAULT NULL;

--��ǉ�(�^��)
ALTER TABLE `dtb_shipping` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_shipping` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_shipping` ADD `addr_all` varchar(510) DEFAULT NULL;


--��ǉ�(����A�h���X) 
ALTER TABLE `dtb_customer_address` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer_address` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_customer_address` ADD `addr_all` varchar(510) DEFAULT NULL;


--��ǉ�(����)
ALTER TABLE `dtb_order` ADD `name_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_order` ADD `kana_all` varchar(510) DEFAULT NULL;
ALTER TABLE `dtb_order` ADD `addr_all` varchar(510) DEFAULT NULL;


--CSV���ڂɂ��ǉ�(���)
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'name_all', NULL, '�����O(��+��)', 4, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'kana_all', NULL, '�����O(�Z�C+���C)', 6, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'addr_all', NULL, '�Z��(1+2)', 13, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Order', 'name_all', NULL, '�����O(��+��)', 7, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Order', 'kana_all', NULL, '�����O(�Z�C+���C)', 9, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Order', 'addr_all', NULL, '�Z��(1+2)', 14, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'name_all', NULL, '�z����_�����O(��+��)', 64, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'kana_all', NULL, '�z����_�����O(�Z�C+���C)', 66, 1, NOW(), NOW(), 'csv'),
(3, 1, 'Eccube\\\\Entity\\\\Shipping', 'addr_all', NULL, '�z����_�Z��(1+2)', 72, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'name_all', NULL, '�����O(��+��)', 6, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'kana_all', NULL, '�����O(�Z�C+���C)', 8, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Order', 'addr_all', NULL, '�Z��(1+2)', 14, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'name_all', NULL, '�z����_�����O(��+��)', 71, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'kana_all', NULL, '�z����_�����O(�Z�C+���C)', 73, 1, NOW(), NOW(), 'csv'),
(4, 1, 'Eccube\\\\Entity\\\\Shipping', 'addr_all', NULL, '�z����_�Z��(1+2)', 79, 1, NOW(), NOW(), 'csv');


--�Ƒ��R�Â�(�Ƒ���\�t���O�A�Ƒ���\���ID)
ALTER TABLE `dtb_customer` ADD `is_family_main` smallint(5) DEFAULT 0;
ALTER TABLE `dtb_customer` ADD `family_main_customer_id` int(10) unsigned  DEFAULT NULL;




----- �Ƒ���\�敪�e�[�u�� ------
--
-- �e�[�u���̍\�� `mtb_family_main`
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
(0, '��\�҂ł͂Ȃ�', 0, 'familymain'),
(1, '��\�҂ł���', 1, 'familymain');
----- .�Ƒ���\�敪�e�[�u�� ------

--��ǉ�(�Ƒ���\�����)
ALTER TABLE `dtb_customer` ADD `mainname` varchar(510) DEFAULT NULL;

--CSV���ڂɂ��ǉ�(�Ƒ���\�敪)
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'Familymain', 'name', '�Ƒ���\�t���O', 4, 1, NOW(), NOW(), 'csv'),
(2, 1, 'Eccube\\\\Entity\\\\Customer', 'Maincustomer', 'name_all', '�Ƒ���\���', 6, 1, NOW(), NOW(), 'csv');






--����@�Ƒ����
-----�Ƒ����01�@����
ALTER TABLE `dtb_customer` ADD `family_relation01` varchar(50) DEFAULT NULL;
-----�Ƒ����01�@�a����
ALTER TABLE `dtb_customer` ADD `family_birth01` datetime DEFAULT NULL;
