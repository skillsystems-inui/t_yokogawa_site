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
-- テーブルのデータ追加 `mtb_info_type`
--

INSERT INTO `mtb_info_type` (`id`, `name`, `sort_no`, `discriminator_type`) VALUES
(1, 'NEWS', 0, 'infotype'),
(2, 'イベント', 1, 'infotype'),
(3, 'お知らせ', 2, 'infotype');




--NEWSテーブルにも項目追加
ALTER TABLE `dtb_news` ADD `info_type` smallint(5) DEFAULT NULL;