-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 10, 2017 at 02:42 AM
-- Server version: 5.7.17
-- PHP Version: 7.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `repairs`
--
CREATE DATABASE IF NOT EXISTS `repairs` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `repairs`;

-- --------------------------------------------------------

--
-- Table structure for table `custs`
--

CREATE TABLE `custs` (
  `cid` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `facebook` varchar(512) NOT NULL,
  `address` varchar(512) NOT NULL,
  `area` varchar(32) NOT NULL,
  `notes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `custs`
--

INSERT INTO `custs` (`cid`, `name`, `phone`, `facebook`, `address`, `area`, `notes`) VALUES
(1000, 'Joe Blogs', '0800000000', '', '18 Nowhere St', 'Papakura', ''),
(1001, 'John & Jane Doe', '', 'johndoe', '44 Unknown Place', 'Mt Eden', '');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `jid` mediumint(8) UNSIGNED NOT NULL,
  `cid` smallint(5) UNSIGNED NOT NULL,
  `jobdate` date NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `price` smallint(6) NOT NULL,
  `task` tinyint(3) UNSIGNED NOT NULL,
  `notes` text NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `model` varchar(24) NOT NULL,
  `os` tinyint(3) UNSIGNED DEFAULT NULL,
  `cpu` varchar(24) NOT NULL,
  `ram` int(10) UNSIGNED DEFAULT NULL,
  `gpu` varchar(24) NOT NULL,
  `hdd` int(10) UNSIGNED DEFAULT NULL,
  `hddmodel` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`jid`, `cid`, `jobdate`, `status`, `price`, `task`, `notes`, `type`, `model`, `os`, `cpu`, `ram`, `gpu`, `hdd`, `hddmodel`) VALUES
(2000, 1000, '2017-08-10', 200, 100, 3, '04/07/2017 - Fans clogged, CPU was thermal throttling making the PC slow. Cleaned fans all ok now.\r\n\r\n05/07/2017 - Picked up and paid.', 20, 'HP P6040A', 6, 'E7400', 2, 'nVidia GTX 1080', 20, 'Toshiba'),
(2001, 1001, '2017-08-04', 200, 80, 1, '06/08/2017 - OS bootloader corrupted. Reinstalled Win10, activated with key built into BIOS. Backed up user data and copied onto new install.\r\n\r\n07/08/2017 - Picked up and paid.', 10, 'Lenovo B50-30', 10, 'N2840', 4, 'Intel', 200, 'WD Blue'),
(2002, 1000, '2017-08-10', 50, 250, 2, '10/08/2017 - OS will not boot. Linux live boots fine. Reinstalling Windows failed, disk errors. Ran badblocks from linux, found >5% of HDD sectors were bad. Quoted $150 for new 500GB Western Digital HDD. Customer says will transfer money on pay day. On hold until then.', 21, 'HP TouchSmart 23-F202A', 7, 'AMD Tri-Core', 3, 'AMD APU', 500, 'Hitachi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `custs`
--
ALTER TABLE `custs`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`jid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
