--CSV���:���iCSV (dtb_csv:1)
------�����I�v�V����1�`10��id��name��dtb_product_class_category�̎w��̃��R�[�h(1���i1�f�[�^)����擾����K�v������
------    (�\�z�����v�])CSV�o�̓��j���[�Ƃ��ăI���W�i��(�P���Ɏ󒍂⏤�i�ł͂Ȃ��A�����Ɏg���Ƃ������������e)��݂��Ă���

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'description_detail', NULL, '���i����', 4, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'ClassCategory1', 'name', '���i�T�C�Y', 6, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'DeliveryDuration', 'name', '�������ڈ�', 7, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'ProductCategories', 'Category', '���i�J�e�S��', 13, 1, NOW(), NOW(), 'csv');




--CSV���:���iCSV (dtb_csv:1)
----�̔����ԁA�̔���ށA���e�ʁA�ܖ������A��������A���ޗ��A���ǂ݂��������A���R����

INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'sales_period_from', NULL, '�̔�����(�J�n)', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'sales_period_to', NULL, '�̔�����(�I��)', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'Salestype', 'name', '�̔����', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'capacity', NULL, '���e��', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'expiry_date', NULL, '�ܖ�����', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'expiration_date', NULL, '�������', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'material', NULL, '���ޗ�', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'please_read', NULL, '���ǂ݂�������', 23, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'free_area', NULL, '���R����', 23, 1, NOW(), NOW(), 'csv')
;





--CSV���:���iCSV (dtb_csv:1)
---- �艿�A�\��
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'price01', NULL, '�艿', 4, 1, NOW(), NOW(), 'csv'),
(1, 1, 'Eccube\\\\Entity\\\\Product', 'Status', 'name', '�\��', 6, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'id', NULL, 'ECCUBE���iID', 4, 1, NOW(), NOW(), 'csv');
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'ClassCategory1', 'name', '���i�K�i', 4, 1, NOW(), NOW(), 'csv');


--CSV���:���iCSV (dtb_csv:1)
---- �֘A���i
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'RelatedProducts', 'ChildProduct', '�֘A���i', 4, 1, NOW(), NOW(), 'csv');
---- �֘A���iID
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\Product', 'RelatedProducts', 'child_product_id', '�֘A���i(ID)', 4, 1, NOW(), NOW(), 'csv');
---- �z������
INSERT INTO `dtb_csv` (`csv_type_id`, `creator_id`, `entity_name`, `field_name`, `reference_field_name`, `disp_name`, `sort_no`, `enabled`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 1, 'Eccube\\\\Entity\\\\ProductClass', 'delivery_fee', NULL, '�z������', 1, 0, NOW(), NOW(), 'csv');


