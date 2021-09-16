-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:14
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
-- テーブルの構造 `dtb_order`
--

CREATE TABLE IF NOT EXISTS `dtb_order` (
  `id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `country_id` smallint(5) unsigned DEFAULT NULL,
  `pref_id` smallint(5) unsigned DEFAULT NULL,
  `sex_id` smallint(5) unsigned DEFAULT NULL,
  `job_id` smallint(5) unsigned DEFAULT NULL,
  `payment_id` int(10) unsigned DEFAULT NULL,
  `device_type_id` smallint(5) unsigned DEFAULT NULL,
  `pre_order_id` varchar(255) DEFAULT NULL,
  `order_no` varchar(255) DEFAULT NULL,
  `message` varchar(4000) DEFAULT NULL,
  `name01` varchar(255) NOT NULL,
  `name02` varchar(255) NOT NULL,
  `kana01` varchar(255) DEFAULT NULL,
  `kana02` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(14) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `addr01` varchar(255) DEFAULT NULL,
  `addr02` varchar(255) DEFAULT NULL,
  `birth` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `subtotal` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `delivery_fee_total` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `charge` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `payment_total` decimal(12,2) unsigned NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `note` varchar(4000) DEFAULT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `order_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `payment_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `currency_code` varchar(255) DEFAULT NULL,
  `complete_message` longtext DEFAULT NULL,
  `complete_mail_message` longtext DEFAULT NULL,
  `add_point` decimal(12,0) unsigned NOT NULL DEFAULT 0,
  `use_point` decimal(12,0) unsigned NOT NULL DEFAULT 0,
  `order_status_id` smallint(5) unsigned DEFAULT NULL,
  `discriminator_type` varchar(255) NOT NULL,
  `name_all` varchar(510) DEFAULT NULL,
  `kana_all` varchar(510) DEFAULT NULL,
  `addr_all` varchar(510) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_order`
--

INSERT INTO `dtb_order` (`id`, `customer_id`, `country_id`, `pref_id`, `sex_id`, `job_id`, `payment_id`, `device_type_id`, `pre_order_id`, `order_no`, `message`, `name01`, `name02`, `kana01`, `kana02`, `company_name`, `email`, `phone_number`, `postal_code`, `addr01`, `addr02`, `birth`, `subtotal`, `discount`, `delivery_fee_total`, `charge`, `tax`, `total`, `payment_total`, `payment_method`, `note`, `create_date`, `update_date`, `order_date`, `payment_date`, `currency_code`, `complete_message`, `complete_mail_message`, `add_point`, `use_point`, `order_status_id`, `discriminator_type`, `name_all`, `kana_all`, `addr_all`) VALUES
(1, 2, NULL, 27, 1, NULL, 1, 10, 'f996dff68bef208cbb7692a1ce4bd2630809d2cd', '1', NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 59.00, 0.00, 1000.00, 0.00, 78.00, 1059.00, 1059.00, '郵便振替', NULL, '2021-09-10 00:41:25', '2021-09-10 08:06:27', '2021-09-10 00:41:31', NULL, 'JPY', NULL, NULL, 1, 0, 1, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(2, 2, NULL, 27, 1, NULL, 1, 10, 'c2ab3b5fcc5dc91b3e1f09b66530ee8286f7d967', '2', NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 59.00, 0.00, 1000.00, 0.00, 78.00, 1059.00, 1059.00, '郵便振替', NULL, '2021-09-10 08:27:05', '2021-09-10 08:41:20', NULL, NULL, 'JPY', NULL, NULL, 1, 0, 8, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(3, 2, NULL, 27, 1, NULL, 1, 10, '543cdd4862c2cea50aa49366e423fb8a7e4f5a49', '3', NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 4725.00, 0.00, 1000.00, 0.00, 424.00, 5725.00, 5725.00, '郵便振替', NULL, '2021-09-10 09:32:36', '2021-09-10 09:34:45', NULL, NULL, 'JPY', NULL, NULL, 45, 0, 8, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(4, 2, NULL, 27, 1, NULL, 1, 10, '482dab53f83617351f71d55b63f19e0c96f69b56', NULL, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 6300.00, 0.00, 1000.00, 0.00, 541.00, 7300.00, 7300.00, '郵便振替', NULL, '2021-09-10 09:52:37', '2021-09-10 09:52:37', NULL, NULL, 'JPY', NULL, NULL, 60, 0, 8, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(5, 2, NULL, 27, 1, NULL, 2, 10, 'aaf8435799f62d0068735d9f7cf17b6b81f97f9e', '5', NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 6349.00, 0.00, 1000.00, 0.00, 545.00, 7349.00, 7349.00, '現金書留', NULL, '2021-09-10 09:54:49', '2021-09-10 09:54:57', NULL, NULL, 'JPY', NULL, NULL, 60, 0, 8, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(6, 2, NULL, 27, 1, NULL, 1, 10, '529bd97b34eb228e8e63569cdf9d99609e65b8a8', '6', NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 9265.00, 0.00, 1000.00, 0.00, 761.00, 10265.00, 10265.00, '郵便振替', NULL, '2021-09-10 23:59:36', '2021-09-13 01:42:44', '2021-09-11 00:00:10', NULL, 'JPY', NULL, NULL, 87, 0, 1, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(7, NULL, NULL, 28, NULL, NULL, 1, 10, '1e66cc49fcc2eebc6495327f9463ada2137cc3f4', '7', NULL, '安部', '勝', 'アベ', 'マサル', NULL, 'abe@cyujo.com', '08010678596', '6620092', '西宮市甑岩町', '10-5-404', NULL, 419.00, 0.00, 1000.00, 0.00, 105.00, 1419.00, 1419.00, '郵便振替', NULL, '2021-09-14 14:05:38', '2021-09-14 14:06:46', '2021-09-14 14:06:46', NULL, 'JPY', NULL, NULL, 0, 0, 1, 'order', '安部 勝', 'アベ マサル', '西宮市甑岩町 10-5-404'),
(8, NULL, NULL, 27, NULL, NULL, 1, 10, '64b796e265678f79c6e88849cceacc6066735879', '8', NULL, 'てすと', 'タロウ', 'アベ', 'マサル', NULL, 'abe@cyujo.com', '08010678596', '5410041', '大阪市中央区北浜', 'さん', NULL, 2916.00, 0.00, 1000.00, 0.00, 290.00, 3916.00, 3916.00, '郵便振替', NULL, '2021-09-14 15:46:47', '2021-09-14 15:46:57', NULL, NULL, 'JPY', NULL, NULL, 0, 0, 8, 'order', 'てすと タロウ', 'アベ マサル', '大阪市中央区北浜 さん'),
(9, 1, NULL, 27, NULL, NULL, 1, 10, 'd08298efa2afa4613f4b6eacce88e1f8273cf7d5', NULL, NULL, '開発用', '会員', 'カイハツヨウ', 'カイイン', NULL, 'k-inui@sic-net.co.jp', '0663704199', '5330033', '大阪市東淀川区東中島', '123', NULL, 419.00, 0.00, 1000.00, 0.00, 105.00, 1419.00, 1419.00, '郵便振替', NULL, '2021-09-15 00:48:54', '2021-09-15 00:48:54', NULL, NULL, 'JPY', NULL, NULL, 4, 0, 8, 'order', '開発用 会員', 'カイハツヨウ カイイン', '大阪市東淀川区東中島 123'),
(10, 2, NULL, 27, 1, NULL, 1, 10, 'bea758406f7ca81cd78eba6fe23df46c355281ff', NULL, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 1575.00, 0.00, 1000.00, 0.00, 191.00, 2575.00, 2575.00, '郵便振替', NULL, '2021-09-15 01:03:56', '2021-09-15 01:03:56', NULL, NULL, 'JPY', NULL, NULL, 15, 0, 8, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(11, 2, NULL, 27, 1, NULL, 1, 10, '33933f27c10e0244a6728ae12fd95024808d2548', NULL, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 2916.00, 0.00, 1000.00, 0.00, 290.00, 3916.00, 3916.00, '郵便振替', NULL, '2021-09-15 01:39:43', '2021-09-15 01:39:43', NULL, NULL, 'JPY', NULL, NULL, 27, 0, 8, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(12, 2, NULL, 27, 1, NULL, 1, 10, '6d7f9c0d4474546cd7255d3d9ceb924558e964d5', '12', NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, 'ishizuka@sic-net.co.jp', '09097032075', '5330033', '大阪市東淀川区東中島', '123', '1977-08-23 15:00:00', 11664.00, 0.00, 1000.00, 0.00, 938.00, 12664.00, 12664.00, '郵便振替', NULL, '2021-09-15 01:40:54', '2021-09-15 01:42:43', '2021-09-15 01:42:43', NULL, 'JPY', NULL, NULL, 108, 0, 1, 'order', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_order`
--
ALTER TABLE `dtb_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dtb_order_pre_order_id_idx` (`pre_order_id`),
  ADD KEY `IDX_1D66D8079395C3F3` (`customer_id`),
  ADD KEY `IDX_1D66D807F92F3E70` (`country_id`),
  ADD KEY `IDX_1D66D807E171EF5F` (`pref_id`),
  ADD KEY `IDX_1D66D8075A2DB2A0` (`sex_id`),
  ADD KEY `IDX_1D66D807BE04EA9` (`job_id`),
  ADD KEY `IDX_1D66D8074C3A3BB` (`payment_id`),
  ADD KEY `IDX_1D66D8074FFA550E` (`device_type_id`),
  ADD KEY `IDX_1D66D807D7707B45` (`order_status_id`),
  ADD KEY `dtb_order_email_idx` (`email`),
  ADD KEY `dtb_order_order_date_idx` (`order_date`),
  ADD KEY `dtb_order_payment_date_idx` (`payment_date`),
  ADD KEY `dtb_order_update_date_idx` (`update_date`),
  ADD KEY `dtb_order_order_no_idx` (`order_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_order`
--
ALTER TABLE `dtb_order`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_order`
--
ALTER TABLE `dtb_order`
  ADD CONSTRAINT `FK_1D66D8074C3A3BB` FOREIGN KEY (`payment_id`) REFERENCES `dtb_payment` (`id`),
  ADD CONSTRAINT `FK_1D66D8074FFA550E` FOREIGN KEY (`device_type_id`) REFERENCES `mtb_device_type` (`id`),
  ADD CONSTRAINT `FK_1D66D8075A2DB2A0` FOREIGN KEY (`sex_id`) REFERENCES `mtb_sex` (`id`),
  ADD CONSTRAINT `FK_1D66D8079395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `dtb_customer` (`id`),
  ADD CONSTRAINT `FK_1D66D807BE04EA9` FOREIGN KEY (`job_id`) REFERENCES `mtb_job` (`id`),
  ADD CONSTRAINT `FK_1D66D807E171EF5F` FOREIGN KEY (`pref_id`) REFERENCES `mtb_pref` (`id`),
  ADD CONSTRAINT `FK_1D66D807F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `mtb_country` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
