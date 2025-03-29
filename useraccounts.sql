-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 29, 2025 at 09:44 AM
-- Server version: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admi_dbgreenvalley`
--

-- --------------------------------------------------------

--
-- Table structure for table `useraccounts`
--

CREATE TABLE `useraccounts` (
  `ACCOUNT_ID` int(11) NOT NULL,
  `ACCOUNT_NAME` varchar(255) NOT NULL,
  `ACCOUNT_USERNAME` varchar(255) NOT NULL,
  `ACCOUNT_PASSWORD` text NOT NULL,
  `ACCOUNT_TYPE` varchar(30) NOT NULL,
  `EMPID` int(11) NOT NULL,
  `USERIMAGE` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `useraccounts`
--

INSERT INTO `useraccounts` (`ACCOUNT_ID`, `ACCOUNT_NAME`, `ACCOUNT_USERNAME`, `ACCOUNT_PASSWORD`, `ACCOUNT_TYPE`, `EMPID`, `USERIMAGE`) VALUES
(1, 'Admin', 'Administrator1', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 1234, 'photos/LoginRed.jpg'),
(2, 'Norhan Alamada', 'norhan', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Registrar', 12, ''),
(3, 'Mark Ryan', 'Ceniz', '05ba10396996f55ddf3f820c9f4562490b0d8e9c', 'Administrator', 123, ''),
(6, 'admin3', 'admin3', '$2y$10$Tn.OPtT9x7eHZVlApGWbFu8h8TUfGlCr4er9r048DquCemJQHb4Ae', 'Administrator', 910, ''),
(7, 'admin4', 'admin4', '$2y$10$jKs3SUpt7mSyC56jDBZgiedeZfJruIudXb50fokct/X/A3dY.UpF6', 'Administrator', 911, ''),
(12, 'superman', 'jojo', '18c28604dd31094a8d69dae60f1bcd347f1afc5a', 'Administrator', 909, ''),
(14, 'jeje', 'jeje', 'ad930d57ccd78b88bf3d2ef72d416fc146e98154', 'Administrator', 999, ''),
(15, 'janjan', 'janjan', '8d2af28c2c499b8aeb71547ced95eec18ad2e570', 'Administrator', 911, ''),
(17, 'Kent Clark', 'superhero', 'b9a06bf244c85ae47a4d25676f519a7dcfd67589', 'Administrator', 1212, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD PRIMARY KEY (`ACCOUNT_ID`),
  ADD UNIQUE KEY `ACCOUNT_USERNAME` (`ACCOUNT_USERNAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `useraccounts`
--
ALTER TABLE `useraccounts`
  MODIFY `ACCOUNT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
