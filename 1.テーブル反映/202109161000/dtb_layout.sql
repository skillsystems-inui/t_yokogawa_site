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
-- テーブルの構造 `dtb_layout`
--

CREATE TABLE IF NOT EXISTS `dtb_layout` (
  `id` int(10) unsigned NOT NULL,
  `device_type_id` smallint(5) unsigned DEFAULT NULL,
  `layout_name` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_layout`
--

INSERT INTO `dtb_layout` (`id`, `device_type_id`, `layout_name`, `create_date`, `update_date`, `discriminator_type`) VALUES
(0, 10, 'プレビュー用レイアウト', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'layout'),
(1, 10, 'トップページ用レイアウト', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'layout'),
(2, 10, '下層ページ用レイアウト', '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'layout');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_layout`
--
ALTER TABLE `dtb_layout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A62AA7C4FFA550E` (`device_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_layout`
--
ALTER TABLE `dtb_layout`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_layout`
--
ALTER TABLE `dtb_layout`
  ADD CONSTRAINT `FK_5A62AA7C4FFA550E` FOREIGN KEY (`device_type_id`) REFERENCES `mtb_device_type` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
