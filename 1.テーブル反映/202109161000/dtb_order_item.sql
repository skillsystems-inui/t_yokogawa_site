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
-- テーブルの構造 `dtb_order_item`
--

CREATE TABLE IF NOT EXISTS `dtb_order_item` (
  `id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `product_class_id` int(10) unsigned DEFAULT NULL,
  `shipping_id` int(10) unsigned DEFAULT NULL,
  `rounding_type_id` smallint(5) unsigned DEFAULT NULL,
  `tax_type_id` smallint(5) unsigned DEFAULT NULL,
  `tax_display_type_id` smallint(5) unsigned DEFAULT NULL,
  `order_item_type_id` smallint(5) unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `class_name1` varchar(255) DEFAULT NULL,
  `class_name2` varchar(255) DEFAULT NULL,
  `class_category_name1` varchar(255) DEFAULT NULL,
  `class_category_name2` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,0) NOT NULL DEFAULT 0,
  `tax` decimal(10,0) NOT NULL DEFAULT 0,
  `tax_rate` decimal(10,0) unsigned NOT NULL DEFAULT 0,
  `tax_adjust` decimal(10,0) unsigned NOT NULL DEFAULT 0,
  `tax_rule_id` smallint(5) unsigned DEFAULT NULL,
  `currency_code` varchar(255) DEFAULT NULL,
  `processor_name` varchar(255) DEFAULT NULL,
  `point_rate` decimal(10,0) unsigned DEFAULT NULL,
  `discriminator_type` varchar(255) NOT NULL,
  `class_category_id1` int(10) unsigned DEFAULT NULL,
  `option_name1` varchar(255) DEFAULT NULL,
  `option_name2` varchar(255) DEFAULT NULL,
  `option_name3` varchar(255) DEFAULT NULL,
  `option_name4` varchar(255) DEFAULT NULL,
  `option_name5` varchar(255) DEFAULT NULL,
  `option_name6` varchar(255) DEFAULT NULL,
  `option_name7` varchar(255) DEFAULT NULL,
  `option_name8` varchar(255) DEFAULT NULL,
  `option_name9` varchar(255) DEFAULT NULL,
  `option_name10` varchar(255) DEFAULT NULL,
  `option_category_name1` varchar(255) DEFAULT NULL,
  `option_category_name2` varchar(255) DEFAULT NULL,
  `option_category_name3` varchar(255) DEFAULT NULL,
  `option_category_name4` varchar(255) DEFAULT NULL,
  `option_category_name5` varchar(255) DEFAULT NULL,
  `option_category_name6` varchar(255) DEFAULT NULL,
  `option_category_name7` varchar(255) DEFAULT NULL,
  `option_category_name8` varchar(255) DEFAULT NULL,
  `option_category_name9` varchar(255) DEFAULT NULL,
  `option_category_name10` varchar(255) DEFAULT NULL,
  `option_printname_plate` varchar(255) DEFAULT NULL,
  `option_printname_noshi` varchar(255) DEFAULT NULL,
  `order_sub_no` varchar(255) DEFAULT NULL,
  `additional_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `class_category_id11` int(10) unsigned DEFAULT NULL,
  `class_category_id12` int(10) unsigned DEFAULT NULL,
  `class_category_id13` int(10) unsigned DEFAULT NULL,
  `class_category_id14` int(10) unsigned DEFAULT NULL,
  `class_category_id15` int(10) unsigned DEFAULT NULL,
  `class_category_id16` int(10) unsigned DEFAULT NULL,
  `class_category_id17` int(10) unsigned DEFAULT NULL,
  `class_category_id18` int(10) unsigned DEFAULT NULL,
  `class_category_id19` int(10) unsigned DEFAULT NULL,
  `class_category_id20` int(10) unsigned DEFAULT NULL,
  `option_name11` varchar(255) DEFAULT NULL,
  `option_name12` varchar(255) DEFAULT NULL,
  `option_name13` varchar(255) DEFAULT NULL,
  `option_name14` varchar(255) DEFAULT NULL,
  `option_name15` varchar(255) DEFAULT NULL,
  `option_name16` varchar(255) DEFAULT NULL,
  `option_name17` varchar(255) DEFAULT NULL,
  `option_name18` varchar(255) DEFAULT NULL,
  `option_name19` varchar(255) DEFAULT NULL,
  `option_name20` varchar(255) DEFAULT NULL,
  `option_category_name11` varchar(255) DEFAULT NULL,
  `option_category_name12` varchar(255) DEFAULT NULL,
  `option_category_name13` varchar(255) DEFAULT NULL,
  `option_category_name14` varchar(255) DEFAULT NULL,
  `option_category_name15` varchar(255) DEFAULT NULL,
  `option_category_name16` varchar(255) DEFAULT NULL,
  `option_category_name17` varchar(255) DEFAULT NULL,
  `option_category_name18` varchar(255) DEFAULT NULL,
  `option_category_name19` varchar(255) DEFAULT NULL,
  `option_category_name20` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `dtb_order_item`
--

INSERT INTO `dtb_order_item` (`id`, `order_id`, `product_id`, `product_class_id`, `shipping_id`, `rounding_type_id`, `tax_type_id`, `tax_display_type_id`, `order_item_type_id`, `product_name`, `product_code`, `class_name1`, `class_name2`, `class_category_name1`, `class_category_name2`, `price`, `quantity`, `tax`, `tax_rate`, `tax_adjust`, `tax_rule_id`, `currency_code`, `processor_name`, `point_rate`, `discriminator_type`, `class_category_id1`, `option_name1`, `option_name2`, `option_name3`, `option_name4`, `option_name5`, `option_name6`, `option_name7`, `option_name8`, `option_name9`, `option_name10`, `option_category_name1`, `option_category_name2`, `option_category_name3`, `option_category_name4`, `option_category_name5`, `option_category_name6`, `option_category_name7`, `option_category_name8`, `option_category_name9`, `option_category_name10`, `option_printname_plate`, `option_printname_noshi`, `order_sub_no`, `additional_price`, `class_category_id11`, `class_category_id12`, `class_category_id13`, `class_category_id14`, `class_category_id15`, `class_category_id16`, `class_category_id17`, `class_category_id18`, `class_category_id19`, `class_category_id20`, `option_name11`, `option_name12`, `option_name13`, `option_name14`, `option_name15`, `option_name16`, `option_name17`, `option_name18`, `option_name19`, `option_name20`, `option_category_name11`, `option_category_name12`, `option_category_name13`, `option_category_name14`, `option_category_name15`, `option_category_name16`, `option_category_name17`, `option_category_name18`, `option_category_name19`, `option_category_name20`) VALUES
(1, 1, 131, 130, 4, 1, 1, 1, 1, '【店頭受取】有料紙袋（小）', NULL, NULL, NULL, NULL, NULL, 55.00, 1, 4, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1_1', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1_2', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, NULL, NULL, 4, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1_3', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 2, 131, 130, 5, 1, 1, 1, 1, '【店頭受取】有料紙袋（小）', NULL, NULL, NULL, NULL, NULL, 55.00, 1, 4, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 2, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 2, NULL, NULL, 5, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 3, 48, 47, 6, 1, 1, 1, 1, 'リーフパイ　10枚入り', NULL, NULL, NULL, NULL, NULL, 2160.00, 2, 173, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 3, 131, 130, 6, 1, 1, 1, 1, '【店頭受取】有料紙袋（小）', NULL, NULL, NULL, NULL, NULL, 55.00, 1, 4, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 3, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 3, NULL, NULL, 6, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 4, 48, 47, 7, 1, 1, 1, 1, 'リーフパイ　10枚入り', NULL, NULL, NULL, NULL, NULL, 2160.00, 2, 173, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 4, 131, 130, 7, 1, 1, 1, 1, '【店頭受取】有料紙袋（小）', NULL, NULL, NULL, NULL, NULL, 55.00, 1, 4, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 4, 148, 147, 7, 1, 1, 1, 1, '【店頭受取】パウンドケーキ　マロン', NULL, NULL, NULL, NULL, NULL, 1458.00, 1, 117, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 4, NULL, NULL, 7, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 4, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 5, 48, 47, 8, 1, 1, 1, 1, 'リーフパイ　10枚入り', NULL, NULL, NULL, NULL, NULL, 2160.00, 2, 173, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 5, 148, 147, 8, 1, 1, 1, 1, '【店頭受取】パウンドケーキ　マロン', NULL, NULL, NULL, NULL, NULL, 1458.00, 1, 117, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 5, 128, 127, 8, 1, 1, 1, 1, 'ギフト商品用　手提げ', NULL, NULL, NULL, NULL, NULL, 100.00, 1, 8, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 5, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 5, NULL, NULL, 8, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 6, 7, 6, 9, 1, 1, 1, 1, '【店頭受取】苺デコレーション', NULL, NULL, NULL, NULL, NULL, 2700.00, 1, 216, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6_1', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 6, 48, 47, 9, 1, 1, 1, 1, 'リーフパイ　10枚入り', NULL, NULL, NULL, NULL, NULL, 2160.00, 2, 173, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6_2', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 6, 148, 147, 9, 1, 1, 1, 1, '【店頭受取】パウンドケーキ　マロン', NULL, NULL, NULL, NULL, NULL, 1458.00, 1, 117, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6_3', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 6, 128, 127, 9, 1, 1, 1, 1, 'ギフト商品用　手提げ', NULL, NULL, NULL, NULL, NULL, 100.00, 1, 8, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6_4', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 6, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6_5', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 6, NULL, NULL, 9, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '6_6', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 7, 78, 77, 10, 1, 1, 1, 1, 'ショコララーム', NULL, NULL, NULL, NULL, NULL, 388.00, 1, 31, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '7_1', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 7, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '7_2', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 7, NULL, NULL, 10, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '7_3', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 8, 7, 6, 11, 1, 1, 1, 1, '【店頭受取】苺デコレーション', NULL, NULL, NULL, NULL, NULL, 2700.00, 1, 216, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 8, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 8, NULL, NULL, 11, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 9, 78, 77, 12, 1, 1, 1, 1, 'ショコララーム', NULL, NULL, NULL, NULL, NULL, 388.00, 1, 31, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 9, NULL, NULL, 12, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 9, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 10, 148, 147, 13, 1, 1, 1, 1, '【店頭受取】パウンドケーキ　マロン', NULL, NULL, NULL, NULL, NULL, 1458.00, 1, 117, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 10, NULL, NULL, 13, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 10, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 11, 7, 6, 14, 1, 1, 1, 1, '【店頭受取】苺デコレーション', NULL, NULL, NULL, NULL, NULL, 2700.00, 1, 216, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, '受取り店舗', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '和泉中央本店', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 11, NULL, NULL, 14, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 11, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 12, 7, 6, 15, 1, 1, 1, 1, '【店頭受取】苺デコレーション', NULL, NULL, NULL, NULL, NULL, 2700.00, 2, 216, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, '受取り店舗', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '和泉中央本店', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12_1', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 12, 7, 6, 15, 1, 1, 1, 1, '【店頭受取】苺デコレーション', NULL, NULL, NULL, NULL, NULL, 2700.00, 2, 216, 8, 0, NULL, 'JPY', NULL, NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12_2', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 12, NULL, NULL, NULL, 1, 1, 2, 3, '手数料', NULL, NULL, NULL, NULL, NULL, 0.00, 1, 0, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\PaymentChargePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12_3', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 12, NULL, NULL, 15, 1, 1, 2, 2, '送料', NULL, NULL, NULL, NULL, NULL, 1000.00, 1, 74, 8, 0, NULL, 'JPY', 'Eccube\\Service\\PurchaseFlow\\Processor\\DeliveryFeePreprocessor', NULL, 'orderitem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12_4', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtb_order_item`
--
ALTER TABLE `dtb_order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A0C8C3ED8D9F6D38` (`order_id`),
  ADD KEY `IDX_A0C8C3ED4584665A` (`product_id`),
  ADD KEY `IDX_A0C8C3ED21B06187` (`product_class_id`),
  ADD KEY `IDX_A0C8C3ED4887F3F8` (`shipping_id`),
  ADD KEY `IDX_A0C8C3ED1BD5C574` (`rounding_type_id`),
  ADD KEY `IDX_A0C8C3ED84042C99` (`tax_type_id`),
  ADD KEY `IDX_A0C8C3EDA2505856` (`tax_display_type_id`),
  ADD KEY `IDX_A0C8C3EDCAD13EAD` (`order_item_type_id`),
  ADD KEY `IDX_A0C8C3ED248D128` (`class_category_id1`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtb_order_item`
--
ALTER TABLE `dtb_order_item`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=67;
--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `dtb_order_item`
--
ALTER TABLE `dtb_order_item`
  ADD CONSTRAINT `FK_A0C8C3ED1BD5C574` FOREIGN KEY (`rounding_type_id`) REFERENCES `mtb_rounding_type` (`id`),
  ADD CONSTRAINT `FK_A0C8C3ED21B06187` FOREIGN KEY (`product_class_id`) REFERENCES `dtb_product_class` (`id`),
  ADD CONSTRAINT `FK_A0C8C3ED248D128` FOREIGN KEY (`class_category_id1`) REFERENCES `dtb_class_category` (`id`),
  ADD CONSTRAINT `FK_A0C8C3ED4584665A` FOREIGN KEY (`product_id`) REFERENCES `dtb_product` (`id`),
  ADD CONSTRAINT `FK_A0C8C3ED4887F3F8` FOREIGN KEY (`shipping_id`) REFERENCES `dtb_shipping` (`id`),
  ADD CONSTRAINT `FK_A0C8C3ED84042C99` FOREIGN KEY (`tax_type_id`) REFERENCES `mtb_tax_type` (`id`),
  ADD CONSTRAINT `FK_A0C8C3ED8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `dtb_order` (`id`),
  ADD CONSTRAINT `FK_A0C8C3EDA2505856` FOREIGN KEY (`tax_display_type_id`) REFERENCES `mtb_tax_display_type` (`id`),
  ADD CONSTRAINT `FK_A0C8C3EDCAD13EAD` FOREIGN KEY (`order_item_type_id`) REFERENCES `mtb_order_item_type` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
