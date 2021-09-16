-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:13
-- サーバのバージョン： 10.5.11-MariaDB-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tyokogawa_ec2`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `dtb_customer`
--

CREATE TABLE IF NOT EXISTS `dtb_customer` (
  `id` int(10) unsigned NOT NULL,
  `customer_status_id` smallint(5) unsigned DEFAULT NULL,
  `sex_id` smallint(5) unsigned DEFAULT NULL,
  `job_id` smallint(5) unsigned DEFAULT NULL,
  `country_id` smallint(5) unsigned DEFAULT NULL,
  `pref_id` smallint(5) unsigned DEFAULT NULL,
  `name01` varchar(255) NOT NULL,
  `name02` varchar(255) NOT NULL,
  `kana01` varchar(255) DEFAULT NULL,
  `kana02` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `addr01` varchar(255) DEFAULT NULL,
  `addr02` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(14) DEFAULT NULL,
  `birth` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `secret_key` varchar(255) NOT NULL,
  `first_buy_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `last_buy_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `buy_times` decimal(10,0) unsigned DEFAULT 0,
  `buy_total` decimal(12,2) unsigned DEFAULT 0.00,
  `note` varchar(4000) DEFAULT NULL,
  `reset_key` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `point` decimal(12,0) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL,
  `name_all` varchar(510) DEFAULT NULL,
  `kana_all` varchar(510) DEFAULT NULL,
  `addr_all` varchar(510) DEFAULT NULL,
  `is_family_main` smallint(5) unsigned DEFAULT NULL,
  `family_main_customer_id` int(10) unsigned DEFAULT NULL,
  `mainname` varchar(510) DEFAULT NULL,
  `family_relation01` varchar(50) DEFAULT NULL,
  `family_birth01` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_customer`
--

INSERT INTO `dtb_customer` (`id`, `customer_status_id`, `sex_id`, `job_id`, `country_id`, `pref_id`, `name01`, `name02`, `kana01`, `kana02`, `company_name`, `postal_code`, `addr01`, `addr02`, `email`, `phone_number`, `birth`, `password`, `salt`, `secret_key`, `first_buy_date`, `last_buy_date`, `buy_times`, `buy_total`, `note`, `reset_key`, `reset_expire`, `point`, `create_date`, `update_date`, `discriminator_type`, `name_all`, `kana_all`, `addr_all`, `is_family_main`, `family_main_customer_id`, `mainname`, `family_relation01`, `family_birth01`) VALUES
(1, 2, NULL, NULL, NULL, 27, '開発用', '会員', 'カイハツヨウ', 'カイイン', NULL, '5330033', '大阪市東淀川区東中島', '123', 'k-inui@sic-net.co.jp', '0663704199', NULL, '9e6360a45e5c3b036980e551d26f8c59dba5eeeb5baeb35a04f7f390044ddc6f', '8e629a3c38', 'cDmam5w9Wjp7EAszOHAcv7r3fG4AzdSK', '2021-09-02 07:36:15', '2021-09-02 17:40:38', 2, 8822.00, NULL, NULL, NULL, 0, '2021-09-02 07:28:03', '2021-09-15 02:13:05', 'customer', '開発用 会員', 'カイハツヨウ カイイン', '大阪市東淀川区東中島 123', 1, NULL, NULL, '長男', '2021-08-28 15:00:00'),
(2, 2, 1, NULL, NULL, 27, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '5330033', '大阪市東淀川区東中島', '123', 'ishizuka@sic-net.co.jp', '09097032075', '1977-08-23 15:00:00', 'fab98b907cafa7fb1e02edeb48ca605ec8a659c0d5350eb5c55025ee49df72d6', '8b4565975c', 'HVcNbLsh7E0W8hOwIfKEr9vvbPBewN6y', '2021-09-10 00:41:31', '2021-09-15 01:42:43', 3, 23988.00, NULL, NULL, NULL, 0, '2021-09-02 07:29:55', '2021-09-15 02:11:40', 'customer', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123', 1, NULL, NULL, NULL, NULL),
(3, 2, 2, 18, NULL, 27, '永山', 'さくら', 'ナガヤマ', 'サクラ', NULL, '5941153', '和泉市青葉台', '123', 'so107072@gmail.com', '08038095726', '1989-03-05 15:00:00', '34be38b9f5ea99df34746a4775c3d7bcc5c0894b8199eedfb182162584922157', '06600d3b84', 'g2lX9dE73wX7xL5pcrBsFKcry6WEmCN9', NULL, NULL, 0, 0.00, NULL, NULL, NULL, 0, '2021-09-02 07:31:49', '2021-09-02 07:31:49', 'customer', '永山 さくら', 'ナガヤマ サクラ', '和泉市青葉台 123', NULL, NULL, '', NULL, NULL),
(4, 2, 1, 16, NULL, 27, '冨澤', '博史', 'トミサワ', 'ヒロシ', NULL, '5320004', '大阪市淀川区西宮原', 'Ｘ番地', 'tomisawa@sic-net.co.jp', '0120444444', NULL, '19e97244dae0db3f18f6a9069dfc98327c5095cccb3aca7c17af742b391a3e9b', '54f4cb5808', 'NHrJ08jC29HFMEyw6oQAeeQFyeinUnjb', NULL, NULL, 0, 0.00, NULL, NULL, NULL, 9999999999, '2021-09-15 06:50:21', '2021-09-15 06:52:21', 'customer', '冨澤 博史', 'トミサワ ヒロシ', '大阪市淀川区西宮原 Ｘ番地', 1, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_customer`
--
ALTER TABLE `dtb_customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `secret_key` (`secret_key`),
  ADD KEY `IDX_8298BBE3C00AF8A7` (`customer_status_id`),
  ADD KEY `IDX_8298BBE35A2DB2A0` (`sex_id`),
  ADD KEY `IDX_8298BBE3BE04EA9` (`job_id`),
  ADD KEY `IDX_8298BBE3F92F3E70` (`country_id`),
  ADD KEY `IDX_8298BBE3E171EF5F` (`pref_id`),
  ADD KEY `dtb_customer_buy_times_idx` (`buy_times`),
  ADD KEY `dtb_customer_buy_total_idx` (`buy_total`),
  ADD KEY `dtb_customer_create_date_idx` (`create_date`),
  ADD KEY `dtb_customer_update_date_idx` (`update_date`),
  ADD KEY `dtb_customer_last_buy_date_idx` (`last_buy_date`),
  ADD KEY `dtb_customer_email_idx` (`email`),
  ADD KEY `IDX_8298BBE31A390235` (`is_family_main`),
  ADD KEY `IDX_8298BBE34CEAE453` (`family_main_customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_customer`
--
ALTER TABLE `dtb_customer`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_customer`
--
ALTER TABLE `dtb_customer`
  ADD CONSTRAINT `FK_8298BBE31A390235` FOREIGN KEY (`is_family_main`) REFERENCES `mtb_family_main` (`id`),
  ADD CONSTRAINT `FK_8298BBE34CEAE453` FOREIGN KEY (`family_main_customer_id`) REFERENCES `dtb_customer` (`id`),
  ADD CONSTRAINT `FK_8298BBE35A2DB2A0` FOREIGN KEY (`sex_id`) REFERENCES `mtb_sex` (`id`),
  ADD CONSTRAINT `FK_8298BBE3BE04EA9` FOREIGN KEY (`job_id`) REFERENCES `mtb_job` (`id`),
  ADD CONSTRAINT `FK_8298BBE3C00AF8A7` FOREIGN KEY (`customer_status_id`) REFERENCES `mtb_customer_status` (`id`),
  ADD CONSTRAINT `FK_8298BBE3E171EF5F` FOREIGN KEY (`pref_id`) REFERENCES `mtb_pref` (`id`),
  ADD CONSTRAINT `FK_8298BBE3F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `mtb_country` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
