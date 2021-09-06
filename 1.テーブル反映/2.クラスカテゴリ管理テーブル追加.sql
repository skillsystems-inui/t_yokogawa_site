--drop table dtb_product_class_category;

/*
--
-- テーブルの構造 `dtb_product_class_category`
--

CREATE TABLE IF NOT EXISTS `dtb_product_class_category` (
  `id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `class_category_id1` int(10) unsigned NOT NULL,
  `class_category_id2` int(10) unsigned NOT NULL,
  `class_category_id3` int(10) unsigned NOT NULL,
  `class_category_id4` int(10) unsigned NOT NULL,
  `class_category_id5` int(10) unsigned NOT NULL,
  `class_category_id6` int(10) unsigned NOT NULL,
  `class_category_id7` int(10) unsigned NOT NULL,
  `class_category_id8` int(10) unsigned NOT NULL,
  `class_category_id9` int(10) unsigned NOT NULL,
  `class_category_id10` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--

--
-- Indexes for table `dtb_product_class_category`
--
ALTER TABLE `dtb_product_class_category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dtb_product_class_category`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
*/


--
-- テーブルの構造 `dtb_product_class_category`
--

CREATE TABLE IF NOT EXISTS `dtb_product_class_category` (
  `product_id` int(10) unsigned NOT NULL,
  `class_category_id` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--

--
-- Indexes for table `dtb_product_class_category`
--
ALTER TABLE `dtb_product_class_category`
  ADD PRIMARY KEY (`product_id`,`class_category_id`);



--
--※オプション名自体も登録することにした！
--
-- テーブルの構造 `dtb_product_class_name`
--

CREATE TABLE IF NOT EXISTS `dtb_product_class_name` (
  `product_id` int(10) unsigned NOT NULL,
  `class_name_id` int(10) unsigned NOT NULL,
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--

--
-- Indexes for table `dtb_product_class_name`
--
ALTER TABLE `dtb_product_class_name`
  ADD PRIMARY KEY (`product_id`,`class_name_id`);

