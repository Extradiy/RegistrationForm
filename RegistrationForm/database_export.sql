-- MySQL Database Export
-- Database: registration_db
-- Date: March 30, 2026
-- 
-- Instructions:
-- 1. Open phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Click "Import" tab
-- 3. Choose this file and click "Go"
-- OR
-- Run this command in terminal:
-- mysql -u root -p < database_export.sql

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `registration_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `registration_db`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `username_index` (`username`),
  KEY `email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Indexes for table `users`
--

ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;
