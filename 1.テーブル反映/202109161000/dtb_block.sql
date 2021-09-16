-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2021 年 9 月 16 日 10:12
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
-- テーブルの構造 `dtb_block`
--

CREATE TABLE IF NOT EXISTS `dtb_block` (
  `id` int(10) unsigned NOT NULL,
  `device_type_id` smallint(5) unsigned DEFAULT NULL,
  `block_name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `use_controller` tinyint(1) NOT NULL DEFAULT 0,
  `deletable` tinyint(1) NOT NULL DEFAULT 1,
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_block`
--

INSERT INTO `dtb_block` (`id`, `device_type_id`, `block_name`, `file_name`, `use_controller`, `deletable`, `create_date`, `update_date`, `discriminator_type`) VALUES
(1, 10, 'カート', 'cart', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(2, 10, 'カテゴリ', 'category', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(3, 10, 'カテゴリナビ(PC)', 'category_nav_pc', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(4, 10, 'カテゴリナビ(SP)', 'category_nav_sp', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(5, 10, '新入荷商品特集', 'eyecatch', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(6, 10, 'フッター', 'footer', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(7, 10, 'ヘッダー(商品検索・ログインナビ・カート)', 'header', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(8, 10, 'ログインナビ(共通)', 'login', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(9, 10, 'ログインナビ(SP)', 'login_sp', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(10, 10, 'ロゴ', 'logo', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(11, 10, '新着商品', 'new_item', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(12, 10, '新着情報', 'news', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(13, 10, '商品検索', 'search_product', 1, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(14, 10, 'トピック', 'topic', 0, 0, '2021-09-02 03:47:59', '2021-09-02 03:47:59', 'block'),
(15, 10, 'TOP商品情報(yokogawa)', 'top_item', 0, 1, '2021-09-02 06:51:16', '2021-09-02 06:51:16', 'block'),
(16, 10, '心をつなぎ、幸せを運ぶ存在でありたい(yokogawa)', 'kokoro', 0, 1, '2021-09-02 06:51:40', '2021-09-02 06:51:40', 'block'),
(17, 10, 'PICK UP(yokogawa)', 'pickup', 0, 1, '2021-09-02 06:52:03', '2021-09-02 06:52:03', 'block'),
(18, 10, 'NEWS(yokogawa)', 'news_yokogawa', 0, 1, '2021-09-02 06:52:27', '2021-09-02 06:52:27', 'block'),
(19, 10, 'お菓子本来のおいしさを、もっともっと知ってほしいから(yokogawa)', 'oisisawo', 0, 1, '2021-09-02 06:52:48', '2021-09-02 06:52:48', 'block'),
(20, 10, 'shop(yokogawa)', 'shop_yokogawa', 0, 1, '2021-09-02 06:53:08', '2021-09-15 09:39:01', 'block'),
(21, 10, 'SNSバナー(yokogawa)', 'sns_banner', 0, 1, '2021-09-02 06:53:32', '2021-09-02 06:53:32', 'block'),
(22, 10, 'バナーリンク(yokogawa)', 'link_banner_yokogawa', 0, 1, '2021-09-02 06:53:54', '2021-09-02 06:53:54', 'block'),
(23, 10, '採用情報リンク(yokogawa)', 'link_recruit', 0, 1, '2021-09-02 06:54:18', '2021-09-02 06:54:18', 'block'),
(24, 10, 'フッター(yokogawa)', 'footer_yokogawa', 0, 1, '2021-09-02 06:54:41', '2021-09-02 06:54:41', 'block'),
(25, 10, '営業日カレンダー', 'businessday_calendar', 1, 0, '2021-09-02 16:47:58', '2021-09-02 16:47:58', 'block'),
(26, 10, 'ご利用ガイド', 'userguide', 0, 1, '2021-09-06 08:13:37', '2021-09-06 08:13:37', 'block');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_block`
--
ALTER TABLE `dtb_block`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_type_id` (`device_type_id`,`file_name`),
  ADD KEY `IDX_6B54DCBD4FFA550E` (`device_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_block`
--
ALTER TABLE `dtb_block`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_block`
--
ALTER TABLE `dtb_block`
  ADD CONSTRAINT `FK_6B54DCBD4FFA550E` FOREIGN KEY (`device_type_id`) REFERENCES `mtb_device_type` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
