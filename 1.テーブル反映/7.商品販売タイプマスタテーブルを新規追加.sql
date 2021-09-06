CREATE TABLE IF NOT EXISTS `mtb_sales_type` (
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_no` smallint(5) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `mtb_sales_type`
--
ALTER TABLE `mtb_sales_type`
  ADD PRIMARY KEY (`id`);
  

--
-- テーブルのデータ追加 `mtb_sales_type`
--

INSERT INTO `mtb_sales_type` (`id`, `name`, `sort_no`, `discriminator_type`) VALUES
(1, '店頭予約', 0, 'salestype'),
(2, '通信販売', 1, 'salestype'),
(3, '店舗と通信販売の両方', 2, 'salestype');
