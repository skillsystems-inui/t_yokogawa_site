-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:15
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
-- テーブルの構造 `dtb_shipping`
--

CREATE TABLE IF NOT EXISTS `dtb_shipping` (
  `id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned DEFAULT NULL,
  `country_id` smallint(5) unsigned DEFAULT NULL,
  `pref_id` smallint(5) unsigned DEFAULT NULL,
  `delivery_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name01` varchar(255) NOT NULL,
  `name02` varchar(255) NOT NULL,
  `kana01` varchar(255) DEFAULT NULL,
  `kana02` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(14) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `addr01` varchar(255) DEFAULT NULL,
  `addr02` varchar(255) DEFAULT NULL,
  `delivery_name` varchar(255) DEFAULT NULL,
  `time_id` int(10) unsigned DEFAULT NULL,
  `delivery_time` varchar(255) DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `shipping_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `tracking_number` varchar(255) DEFAULT NULL,
  `note` varchar(4000) DEFAULT NULL,
  `sort_no` smallint(5) unsigned DEFAULT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `mail_send_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL,
  `name_all` varchar(510) DEFAULT NULL,
  `kana_all` varchar(510) DEFAULT NULL,
  `addr_all` varchar(510) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_shipping`
--

INSERT INTO `dtb_shipping` (`id`, `order_id`, `country_id`, `pref_id`, `delivery_id`, `creator_id`, `name01`, `name02`, `kana01`, `kana02`, `company_name`, `phone_number`, `postal_code`, `addr01`, `addr02`, `delivery_name`, `time_id`, `delivery_time`, `delivery_date`, `shipping_date`, `tracking_number`, `note`, `sort_no`, `create_date`, `update_date`, `mail_send_date`, `discriminator_type`, `name_all`, `kana_all`, `addr_all`) VALUES
(4, 1, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-10 00:41:25', '2021-09-10 00:41:25', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(5, 2, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-10 08:27:05', '2021-09-10 08:27:05', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(6, 3, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-10 09:32:36', '2021-09-10 09:32:36', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(7, 4, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-10 09:52:37', '2021-09-10 09:52:37', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(8, 5, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-10 09:54:49', '2021-09-10 09:54:49', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(9, 6, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-10 23:59:36', '2021-09-10 23:59:36', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(10, 7, NULL, 28, 1, NULL, '安部', '勝', 'アベ', 'マサル', NULL, '08010678596', '6620092', '西宮市甑岩町', '10-5-404', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-14 14:05:38', '2021-09-14 14:05:38', NULL, 'shipping', '安部 勝', 'アベ マサル', '西宮市甑岩町 10-5-404'),
(11, 8, NULL, 27, 1, NULL, 'てすと', 'タロウ', 'アベ', 'マサル', NULL, '08010678596', '5410041', '大阪市中央区北浜', 'さん', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-14 15:46:47', '2021-09-14 15:46:47', NULL, 'shipping', 'てすと タロウ', 'アベ マサル', '大阪市中央区北浜 さん'),
(12, 9, NULL, 27, 1, NULL, '開発用', '会員', 'カイハツヨウ', 'カイイン', NULL, '0663704199', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-15 00:48:54', '2021-09-15 00:48:54', NULL, 'shipping', '開発用 会員', 'カイハツヨウ カイイン', '大阪市東淀川区東中島 123'),
(13, 10, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-15 01:03:56', '2021-09-15 01:03:56', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(14, 11, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-15 01:39:43', '2021-09-15 01:39:43', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123'),
(15, 12, NULL, 27, 1, NULL, '石塚', '崇', 'イシヅカ', 'タカシ', NULL, '09097032075', '5330033', '大阪市東淀川区東中島', '123', 'ヤマト運輸', 1, '午前', '2021-09-19 15:00:00', NULL, NULL, NULL, NULL, '2021-09-15 01:40:54', '2021-09-15 01:42:40', NULL, 'shipping', '石塚 崇', 'イシヅカ タカシ', '大阪市東淀川区東中島 123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_shipping`
--
ALTER TABLE `dtb_shipping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2EBD22CE8D9F6D38` (`order_id`),
  ADD KEY `IDX_2EBD22CEF92F3E70` (`country_id`),
  ADD KEY `IDX_2EBD22CEE171EF5F` (`pref_id`),
  ADD KEY `IDX_2EBD22CE12136921` (`delivery_id`),
  ADD KEY `IDX_2EBD22CE61220EA6` (`creator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_shipping`
--
ALTER TABLE `dtb_shipping`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_shipping`
--
ALTER TABLE `dtb_shipping`
  ADD CONSTRAINT `FK_2EBD22CE12136921` FOREIGN KEY (`delivery_id`) REFERENCES `dtb_delivery` (`id`),
  ADD CONSTRAINT `FK_2EBD22CE61220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`),
  ADD CONSTRAINT `FK_2EBD22CE8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `dtb_order` (`id`),
  ADD CONSTRAINT `FK_2EBD22CEE171EF5F` FOREIGN KEY (`pref_id`) REFERENCES `mtb_pref` (`id`),
  ADD CONSTRAINT `FK_2EBD22CEF92F3E70` FOREIGN KEY (`country_id`) REFERENCES `mtb_country` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
