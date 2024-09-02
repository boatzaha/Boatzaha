-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2024 at 12:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `howdenlist`
--

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `receive_date` date DEFAULT NULL,
  `recore_date` date DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `insurance` varchar(255) DEFAULT NULL,
  `policy` varchar(255) DEFAULT NULL,
  `insure_name` varchar(255) DEFAULT NULL,
  `date_treatment` date DEFAULT NULL,
  `claim_type` varchar(255) DEFAULT NULL,
  `hosp_clinic` varchar(255) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `bill_amount` decimal(10,2) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `declined_amount` decimal(10,2) DEFAULT NULL,
  `tf_date` date DEFAULT NULL,
  `final_status` varchar(255) DEFAULT NULL,
  `complete_date` date DEFAULT NULL,
  `duration_date` int(11) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `item`, `receive_date`, `recore_date`, `company_name`, `insurance`, `policy`, `insure_name`, `date_treatment`, `claim_type`, `hosp_clinic`, `diagnosis`, `bill_amount`, `remark`, `status`, `paid_amount`, `declined_amount`, `tf_date`, `final_status`, `complete_date`, `duration_date`, `created_by`, `created_at`) VALUES
(1, '1', '2024-08-23', '2024-08-27', 'Thairung Pratners Group Co., Ltd.', 'FWD', 'E0001378.000', 'นางแก้วเก้า เผอิญโชค', '2024-07-18', 'Out-Pultient', 'โรงพยาบาลรามาธิบดี', '-', 8179.00, '-1', 'Rejected', 0.00, 0.00, '2024-08-23', 'Complete', '2024-09-16', 20, NULL, '2024-08-23 14:31:23'),
(2, '123', '2024-08-27', '2024-08-27', 'Thai', 'Test', '123', 'qwe', '2024-08-24', 'Mayor Medical', '123', 'wwwwwwwwwไไ', 12333.00, NULL, 'Approve', 13.00, 123333.00, NULL, 'Pending', '2024-09-13', 17, NULL, '2024-08-27 14:07:38'),
(3, 'Test', '2024-08-29', '2024-08-26', 'Test', 'Test', 'Test', 'Test', '2024-08-22', 'Iu-Patieut', 'Test', 'Test', 123123.00, NULL, 'On-Going', 123123.00, 1233.00, '2024-08-22', 'Decline', '2024-09-13', 18, NULL, '2024-08-26 13:31:48'),
(4, 'Test1', '2024-08-10', '2024-08-26', 'Test', 'Test', 'Test', 'Test', '2024-08-23', 'Out-Pultient', 'Test', 'Test', 123123.00, 'w', 'On-Going', 123123.00, 1233.00, '2024-08-20', 'Decline', '2024-09-13', 17, NULL, '2024-08-26 13:32:38'),
(5, 'Test2', '2024-08-24', '2024-08-26', 'Test', 'Test', 'Test', 'Test', '2024-08-24', 'Medical-Expent', 'Test', 'Test', 123123.00, '2', 'On-Going', 123123.00, 1233.00, '2024-08-16', 'Decline', '2024-09-13', 17, NULL, '2024-08-26 13:35:44'),
(6, 'Test244', '2024-08-21', '2024-08-26', 'Test', 'Test', 'Test', 'Test', '2024-08-21', 'Mayor Medical', 'Testwxw', 'Test', 123123.00, 'ewwe', 'On-Going', 123123.00, 1233.00, '2024-08-23', 'Complete', '2024-09-13', 17, NULL, '2024-08-26 13:48:00'),
(7, 'Test2444', '2024-09-26', '2024-08-27', 'Test', 'Test', 'Test', 'Test', '2024-08-22', 'Mayor Medical', 'Test', 'Test', 123123.00, NULL, 'On-Going', 123123.00, 1233.00, NULL, 'Decline', '2024-09-16', 19, NULL, '2024-08-26 14:51:21'),
(8, 'Test2444ภ', '2024-08-23', '2024-08-27', 'Test', 'Test', 'Test', 'Test', '2024-08-27', 'Iu-Patieut', 'Test', 'Test', 123123.00, NULL, 'Decline', 123123.00, 1233.00, NULL, 'Decline', '2024-09-13', 17, NULL, '2024-08-27 11:00:21'),
(9, 'Test5', '2024-08-27', '2024-08-27', 'Test', 'Test', 'Test', 'Test', '2024-08-28', 'Mayor Medical', 'Test', 'Test', 123123.00, NULL, 'On-Going', 123123.00, 1233.00, NULL, 'Complete', '2024-09-13', 17, NULL, '2024-08-27 14:06:50'),
(10, '1', '2024-08-27', '2024-08-27', '1', '1', 'aaa111', '123', '2024-08-27', 'HB-incentier', '123', '123123', 123333.00, NULL, 'Rejected', 33333.00, 33333.34, NULL, 'Decline', '2024-09-13', 17, 'ad@ad.com', '2024-08-27 15:26:41'),
(11, '1', '2024-08-08', '2024-08-27', '1', '1', 'aaa111', '123', '2024-08-29', 'Iu-Patieut', '123', '123123', 123333.00, NULL, 'On-Going', 33333.00, 33333.34, NULL, 'Complete', '2024-09-13', 17, 'ad@ad.com', '2024-08-27 15:28:55'),
(12, '1', '2024-08-30', '2024-08-27', '111', '1', 'aaa111', '123', '2024-08-27', 'Mayor Medical', '123', '123123', 123333.00, NULL, 'Decline', 33333.00, 33333.34, NULL, 'Decline', '2024-09-13', 17, 'ad@ad.com', '2024-08-27 15:32:11'),
(13, '1', '2024-08-30', '2024-08-27', '111', '1', 'aaa111', '123', '2024-08-23', 'Iu-Patieut', '123', '123123', 123333.00, NULL, 'On-Going', 33333.00, 33333.34, NULL, 'Decline', '2024-09-13', 17, 'ad@ad.com', '2024-08-27 16:07:32'),
(14, '2', '2024-08-27', '2024-08-27', 'AIA', 'AIA', 'R123123', 'พัฒนพงษ์ กิ่งจันทร์', '2024-08-27', 'Out-Pultient', 'URU', 'wwwwwwwwwไไ', 123123.00, NULL, 'Approve', 123123.00, 33333.34, NULL, 'Decline', '2024-09-13', 17, 'ad@ad.com', '2024-08-27 16:31:27');

-- --------------------------------------------------------

--
-- Table structure for table `client_groups`
--

CREATE TABLE `client_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_groups`
--

INSERT INTO `client_groups` (`id`, `group_name`) VALUES
(4, 'Non'),
(5, 'TTP'),
(6, 'CP'),
(7, 'CUTE'),
(8, 'MAT');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `inception_date` date NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `revenue` decimal(15,2) NOT NULL,
  `premium` decimal(15,2) NOT NULL,
  `close_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `policy_type` varchar(50) DEFAULT NULL,
  `sum_insured` decimal(18,2) DEFAULT NULL,
  `client_group` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `inception_date`, `class`, `revenue`, `premium`, `close_date`, `created_at`, `department`, `status`, `description`, `created_by`, `policy_type`, `sum_insured`, `client_group`) VALUES
(129, 'S&SONs TRADING CO.,LTD. (SCOTCH)', '2024-10-01', 'GLH', 200000.00, 4000000.00, '0000-00-00', '2024-08-15 03:22:50', 'EB', 'Quoting', 'With AIA we have proposed Prudential Life and MTL as 2nd rank', 'Thanadonk', 'NewRecurring', 0.00, NULL),
(130, 'KNS Trans Company Limited and Subsidiaries', '2024-08-15', 'GLH', 90000.00, 1700000.00, '2024-08-15', '2024-08-15 03:29:51', 'EB', 'Booked', 'win due to competitive rate and connections', 'Thanadonk', 'NewRecurring', 0.00, NULL),
(132, 'GHL (Thailand) Co., Ltd.', '2024-10-01', 'GLH', 150000.00, 2000000.00, '0000-00-00', '2024-08-15 08:35:52', 'EB', 'Quoting', '', 'Narong', 'Renewal', 0.00, NULL),
(133, 'HFC Prestige Manufacturing (Thailand) Co., Ltd.', '2024-10-01', 'GLH', 500000.00, 5000000.00, '0000-00-00', '2024-08-15 08:39:59', 'EB', 'Quoting', 'Global client', 'Narong', 'NewRecurring', 0.00, NULL),
(134, 'Lightrousce Company Limited.', '2024-11-27', 'GLH', 100000.00, 1000000.00, '0000-00-00', '2024-08-15 08:41:54', 'EB', 'Quoting', '', 'Narong', 'Renewal', 0.00, NULL),
(135, 'Thairung Partners Group Co., Ltd.', '2025-01-01', 'BOND', 50000.00, 300000.00, '0000-00-00', '2024-08-15 08:44:36', 'EB', 'Quoting', 'work with Khun Chai & Khun Key\r\nQuote - MSIG\r\nWaiting - Viriya', 'Narong', 'NewRecurring', 200000.00, NULL),
(148, 'Material Automation (Thailand) Co., Ltd.', '2024-10-01', 'GLH', 150000.00, 3000000.00, '0000-00-00', '2024-08-20 02:32:53', 'EB', 'Quoting', 'Contact : Noi (Siriwan) HR Manager 02 261 5100\r\nLocation : 12th FL., CTI Tower 191/78 Ratchadapisek Road, Klongtoey, Klongtoey, Bangkok 10110 THAILAND\r\nBusiness : Sales and service of IT hardware and software\r\nSent Quote : \r\n- Thai Life 20/08/2024\r\n- Bangkok Life 20/08/2024', 'Narong', 'NewRecurring', 0.00, 'MAT'),
(150, 'Reynolds Polymer Technology', '2024-10-01', 'D&O', 30000.00, 180000.00, '0000-00-00', '2024-08-22 08:55:33', 'EB', 'Quoting', 'Transfer to Max Howden', 'Narong', 'NewRecurring', 200000000.00, 'Non');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `email`, `created_at`, `role`, `remember_token`, `branch`) VALUES
('1103701443430', '$2y$10$mog8hlwyP79s6JnsE/zkkOBA/3D23mKq4zjESRoY0Km5GoG8/77sS', 'tiyadab@sabuymaxi.com', '2024-08-16 02:39:23', 'officer', NULL, NULL),
('5', '$2y$10$ovQMoWHoIDiCwKoFxbFviui8Juc5cZDyEf.ksMWBIU46FK9kB9dn.', 'user5@gmail.com', '2024-08-13 11:47:46', 'manager', NULL, NULL),
('6', '$2y$10$eQPvjuL2TUkCiIQ.XeSc/.KtShiMYqUHPNapMhxzZcB.rq17aLyZ2', 'aa6@a.com', '2024-08-19 02:12:41', 'officer', NULL, NULL),
('ad@ad.com', '$2y$10$OTAHhgcOGXXWyw2UFwP4Xe7eatDg2H7iLnHcdtGUbnpUpFpmZY.hC', 'a@a.com', '2024-08-09 07:45:06', 'manager', NULL, NULL),
('apinya', '$2y$10$fGw6h2RXtO6UphNFZoD13eWOvl3P2YX3kh./Jm1hsBXDwXVK.ruwy', 'apinya.intern@gmail.com', '2024-08-14 02:48:55', 'manager', NULL, NULL),
('boatzaha2905', '$2y$10$qAGSGh5OnKYDNq0jHCX8i./X6gWNyJI2hUw/Pve3BjgvpjPQo4sRa', 'boatzaha2905@gmail.com', '2024-08-20 02:53:29', 'officer', NULL, NULL),
('Chai', '$2y$10$a3nNtPO649J7b9mDcoraQO4vzpl0XiAFQ7XL.E.Kq1SGjVPMzauR2', 'chaip@sabuymaxi.com', '2024-08-16 02:12:32', 'manager', NULL, NULL),
('Marut', '$2y$10$Zcr0y8w.QQIWmDpgbIcFxeZHCyNBQHKFTnk9V95a0mXSPKiu0gP5m', 'marutp@sabuymaxi.com', '2024-08-16 04:02:12', 'officer', NULL, NULL),
('Narong', '$2y$10$egBufc2d4EhMeKkv1.qN1.Bl7dw0JgCfJ4usRIaPi8lK0MXGprzQu', 'narongk@sabuymaxi.com', '2024-08-15 08:32:15', 'manager', NULL, NULL),
('test', '$2y$10$6v1e3O7ieUy0z59iJfk5VuqMlvB/YB.zJobp9dLyiIxLVREVJkwMy', 'aa@aa.com', '2024-08-19 07:40:05', 'manager', NULL, NULL),
('Thanadonk', '$2y$10$DoE22jsWgm5mpYv3lzF.2udP9r4dq/Xc3ICEOo599lZk3DD3RxRuu', 'thanadonk@sabuymaxi.com', '2024-08-06 02:35:26', 'officer', NULL, NULL),
('thunsuda', '$2y$10$M2p003dMYmDa.F4.GrD2Iea7zeopymI7WT/1dqXzx55ZEIy6wZ8/2', 'thunsudac@sabuymaxi.com', '2024-08-16 02:24:13', 'officer', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_backup`
--

CREATE TABLE `users_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('หัวหน้า','พนักงาน') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_backup`
--

INSERT INTO `users_backup` (`id`, `username`, `password`, `email`, `role`, `created_at`, `approved`) VALUES
(1, 'aq@gmail.com', '$2y$10$pDYqpiPnOy01OG7k1wREKehAsMcbXHKiT1D.19m.MB0TpMy9uMXSG', 'thassanim@howdenmaxi.com', 'หัวหน้า', '2024-08-05 04:27:23', 1),
(2, '1@Gmail.com', '$2y$10$zvs.dd6ow15smlWPnPfGP.cUrZugvG1O.8VS41Dd987WUH.bUB0Tm', 'thassanim@howdenmaxi.com', 'พนักงาน', '2024-08-05 05:16:02', 0),
(3, 'test22', '$2y$10$BacZMYURc1QxNSPW9mV8GeT4SLIeFXLc0xLSFZtu/T8RfjjZTUMcm', 'user22@user22.com', 'หัวหน้า', '2024-08-05 06:25:18', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_groups`
--
ALTER TABLE `client_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `client_groups`
--
ALTER TABLE `client_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
