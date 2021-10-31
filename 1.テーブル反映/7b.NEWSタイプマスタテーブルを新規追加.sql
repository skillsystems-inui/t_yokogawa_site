CREATE TABLE IF NOT EXISTS `mtb_info_type` (
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_no` smallint(5) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `mtb_info_type`
--
ALTER TABLE `mtb_info_type`
  ADD PRIMARY KEY (`id`);
  

--
-- �e�[�u���̃f�[�^�ǉ� `mtb_info_type`
--

INSERT INTO `mtb_info_type` (`id`, `name`, `sort_no`, `discriminator_type`) VALUES
(1, 'NEWS', 0, 'infotype'),
(2, '�C�x���g', 1, 'infotype'),
(3, '���m�点', 2, 'infotype');




--NEWS�e�[�u���ɂ����ڒǉ�
ALTER TABLE `dtb_news` ADD `info_type` smallint(5) DEFAULT NULL;