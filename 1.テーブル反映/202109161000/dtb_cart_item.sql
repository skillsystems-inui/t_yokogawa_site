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
-- テーブルの構造 `dtb_cart_item`
--

CREATE TABLE IF NOT EXISTS `dtb_cart_item` (
  `id` int(10) unsigned NOT NULL,
  `product_class_id` int(10) unsigned DEFAULT NULL,
  `cart_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,0) NOT NULL DEFAULT 0,
  `point_rate` decimal(10,0) unsigned DEFAULT NULL,
  `discriminator_type` varchar(255) NOT NULL,
  `option_class_category_id1` int(10) unsigned DEFAULT NULL,
  `option_class_category_id2` int(10) unsigned DEFAULT NULL,
  `option_class_category_id3` int(10) unsigned DEFAULT NULL,
  `option_class_category_id4` int(10) unsigned DEFAULT NULL,
  `option_class_category_id5` int(10) unsigned DEFAULT NULL,
  `option_class_category_id6` int(10) unsigned DEFAULT NULL,
  `option_class_category_id7` int(10) unsigned DEFAULT NULL,
  `option_class_category_id8` int(10) unsigned DEFAULT NULL,
  `option_class_category_id9` int(10) unsigned DEFAULT NULL,
  `option_class_category_id10` int(10) unsigned DEFAULT NULL,
  `printname_plate` varchar(255) DEFAULT NULL,
  `printname_noshi` varchar(255) DEFAULT NULL,
  `additional_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `option_class_category_id11` int(10) unsigned DEFAULT NULL,
  `option_class_category_id12` int(10) unsigned DEFAULT NULL,
  `option_class_category_id13` int(10) unsigned DEFAULT NULL,
  `option_class_category_id14` int(10) unsigned DEFAULT NULL,
  `option_class_category_id15` int(10) unsigned DEFAULT NULL,
  `option_class_category_id16` int(10) unsigned DEFAULT NULL,
  `option_class_category_id17` int(10) unsigned DEFAULT NULL,
  `option_class_category_id18` int(10) unsigned DEFAULT NULL,
  `option_class_category_id19` int(10) unsigned DEFAULT NULL,
  `option_class_category_id20` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_cart_item`
--

INSERT INTO `dtb_cart_item` (`id`, `product_class_id`, `cart_id`, `price`, `quantity`, `point_rate`, `discriminator_type`, `option_class_category_id1`, `option_class_category_id2`, `option_class_category_id3`, `option_class_category_id4`, `option_class_category_id5`, `option_class_category_id6`, `option_class_category_id7`, `option_class_category_id8`, `option_class_category_id9`, `option_class_category_id10`, `printname_plate`, `printname_noshi`, `additional_price`, `option_class_category_id11`, `option_class_category_id12`, `option_class_category_id13`, `option_class_category_id14`, `option_class_category_id15`, `option_class_category_id16`, `option_class_category_id17`, `option_class_category_id18`, `option_class_category_id19`, `option_class_category_id20`) VALUES
(62, 6, 41, 2916.00, 1, NULL, 'cartitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 6, 46, 2916.00, 1, NULL, 'cartitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 305, 73, 2799.00, 1, NULL, 'cartitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 132, 73, 1469.00, 1, NULL, 'cartitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 116, 73, 3149.00, 1, NULL, 'cartitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 91, 73, 991.00, 1, NULL, 'cartitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 6, 73, 2916.00, 1, NULL, 'cartitem', 7, 19, 31, 43, 55, 62, 66, 91, 103, 115, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_cart_item`
--
ALTER TABLE `dtb_cart_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B0228F7421B06187` (`product_class_id`),
  ADD KEY `IDX_B0228F741AD5CDBF` (`cart_id`),
  ADD KEY `IDX_B0228F742C0B0D00` (`option_class_category_id1`),
  ADD KEY `IDX_B0228F74B5025CBA` (`option_class_category_id2`),
  ADD KEY `IDX_B0228F74C2056C2C` (`option_class_category_id3`),
  ADD KEY `IDX_B0228F745C61F98F` (`option_class_category_id4`),
  ADD KEY `IDX_B0228F742B66C919` (`option_class_category_id5`),
  ADD KEY `IDX_B0228F74B26F98A3` (`option_class_category_id6`),
  ADD KEY `IDX_B0228F74C568A835` (`option_class_category_id7`),
  ADD KEY `IDX_B0228F7455D7B5A4` (`option_class_category_id8`),
  ADD KEY `IDX_B0228F7422D08532` (`option_class_category_id9`),
  ADD KEY `IDX_B0228F74F4F7D42C` (`option_class_category_id10`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_cart_item`
--
ALTER TABLE `dtb_cart_item`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=112;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_cart_item`
--
ALTER TABLE `dtb_cart_item`
  ADD CONSTRAINT `FK_B0228F741AD5CDBF` FOREIGN KEY (`cart_id`) REFERENCES `dtb_cart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B0228F7421B06187` FOREIGN KEY (`product_class_id`) REFERENCES `dtb_product_class` (`id`),
  ADD CONSTRAINT `FK_B0228F7422D08532` FOREIGN KEY (`option_class_category_id9`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F742B66C919` FOREIGN KEY (`option_class_category_id5`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F742C0B0D00` FOREIGN KEY (`option_class_category_id1`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F7455D7B5A4` FOREIGN KEY (`option_class_category_id8`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F745C61F98F` FOREIGN KEY (`option_class_category_id4`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F74B26F98A3` FOREIGN KEY (`option_class_category_id6`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F74B5025CBA` FOREIGN KEY (`option_class_category_id2`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F74C2056C2C` FOREIGN KEY (`option_class_category_id3`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F74C568A835` FOREIGN KEY (`option_class_category_id7`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_B0228F74F4F7D42C` FOREIGN KEY (`option_class_category_id10`) REFERENCES `dtb_class_category` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
