-- phpMyAdmin SQL Dump
-- version home.pl
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 07 Lut 2018, 09:13
-- Wersja serwera: 5.7.19-17-log
-- Wersja PHP: 7.1.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `20177771_micadoni`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(10) NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `admins`
--

INSERT INTO `admins` (`id`, `login`, `password`) VALUES
(1, 'admin', '$1$3LC3nTIF$B15Cb5DvLMdkf2mVIegmo/');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` text NOT NULL,
  `category_description` text,
  `category_nicelink` text NOT NULL,
  `category_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Zrzut danych tabeli `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_nicelink`, `category_order`) VALUES
(1, 'Armchairs', NULL, 'armchairs', 0),
(2, '2 seats sofas', NULL, '2-seats-sofas', 0),
(3, '3 seats sofas', NULL, '3-seats-sofas', 0),
(4, 'Left corner sofas', NULL, 'left-corner-sofas', 0),
(5, 'Right corner sofas', NULL, 'right-corner-sofas', 0),
(6, 'Poufs', NULL, 'poufs', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` text NOT NULL,
  `product_description` text NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `belongs_to_category` int(11) NOT NULL,
  `product_nicelink` text NOT NULL,
  `belongs_to_product` int(11) NOT NULL DEFAULT '0',
  `technical_image` text,
  `has_image` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_images`
--

CREATE TABLE IF NOT EXISTS `product_images` (
  `product_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `image` text NOT NULL,
  `belongs_to_product` int(11) NOT NULL,
  `image_order` int(11) NOT NULL,
  PRIMARY KEY (`product_image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `slides`
--

CREATE TABLE IF NOT EXISTS `slides` (
  `slide_id` int(11) NOT NULL AUTO_INCREMENT,
  `image` text NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Zrzut danych tabeli `slides`
--

INSERT INTO `slides` (`slide_id`, `image`, `name`) VALUES
(1, '1515763523.jpeg', 'Slide 1'),
(2, '1515763531.jpeg', 'Slide 2'),
(3, '1515763538.jpeg', 'Slide 3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
