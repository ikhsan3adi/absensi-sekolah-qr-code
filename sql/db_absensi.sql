-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2023 at 11:35 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_activation_attempts`
--

CREATE TABLE IF NOT EXISTS `auth_activation_attempts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups`
--

CREATE TABLE IF NOT EXISTS `auth_groups` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_permissions`
--

CREATE TABLE IF NOT EXISTS `auth_groups_permissions` (
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  KEY `auth_groups_permissions_permission_id_foreign` (`permission_id`),
  KEY `group_id_permission_id` (`group_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_users`
--

CREATE TABLE IF NOT EXISTS `auth_groups_users` (
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  KEY `auth_groups_users_user_id_foreign` (`user_id`),
  KEY `group_id_user_id` (`group_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_logins`
--

CREATE TABLE IF NOT EXISTS `auth_logins` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `auth_logins`
--

INSERT INTO `auth_logins` (`id`, `ip_address`, `email`, `user_id`, `date`, `success`) VALUES
(1, '::1', 'xibosann1410@gmail.com', 4, '2023-03-08 16:49:49', 1),
(2, '::1', 'xibosann1410@gmail.com', 4, '2023-03-08 21:42:05', 1),
(3, '::1', 'xibosann1410@gmail.com', 4, '2023-03-09 05:42:24', 1),
(4, '::1', 'xibosann1410@gmail.com', 4, '2023-03-09 05:44:35', 1),
(5, '::1', 'xibosann1410@gmail.com', 4, '2023-03-09 13:52:07', 1),
(6, '::1', 'xibosann1410@gmail.com', 4, '2023-03-09 21:34:41', 1),
(7, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 05:34:49', 1),
(8, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 05:38:23', 1),
(9, '::1', 'superadmins', NULL, '2023-03-10 05:49:18', 0),
(10, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 05:50:09', 1),
(11, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 05:55:43', 1),
(12, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 13:05:23', 1),
(13, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 13:39:12', 1),
(14, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 17:06:56', 1),
(15, '::1', 'xibosann1410@gmail.com', 4, '2023-03-10 19:50:40', 1),
(16, '::1', 'xibosann1410@gmail.com', 4, '2023-03-11 13:30:17', 1),
(17, '::1', 'xibosann1410@gmail.com', 4, '2023-03-11 21:20:33', 1),
(18, '::1', 'xibosann1410@gmail.com', 4, '2023-03-12 07:38:34', 1),
(19, '::1', 'xibosann1410@gmail.com', 4, '2023-03-12 13:37:42', 1),
(20, '::1', 'xibosann1410@gmail.com', 4, '2023-03-12 19:54:44', 1),
(21, '::1', 'xibosann1410@gmail.com', 4, '2023-03-12 21:52:27', 1),
(22, '::1', 'xibosann1410@gmail.com', 4, '2023-03-13 16:23:30', 1),
(23, '::1', 'xibosann1410@gmail.com', 4, '2023-03-13 16:27:00', 1),
(24, '::1', 'adminsuper@gmail.com', 4, '2023-03-15 18:38:55', 1),
(25, '::1', 'adminsuper@gmail.com', 4, '2023-03-15 18:48:22', 1),
(26, '::1', 'adminsuper@gmail.com', 4, '2023-03-16 05:35:33', 1),
(27, '::1', 'adminsuper@gmail.com', 4, '2023-04-01 12:16:03', 1),
(28, '::1', 'adminsuper@gmail.com', 4, '2023-04-24 19:49:50', 1),
(29, '::1', 'adminsuper@gmail.com', 4, '2023-05-23 20:31:25', 1),
(30, '::1', 'adminsuper@gmail.com', 4, '2023-05-23 20:57:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auth_permissions`
--

CREATE TABLE IF NOT EXISTS `auth_permissions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_reset_attempts`
--

CREATE TABLE IF NOT EXISTS `auth_reset_attempts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `selector` varchar(255) NOT NULL,
  `hashedValidator` varchar(255) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_tokens_user_id_foreign` (`user_id`),
  KEY `selector` (`selector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_users_permissions`
--

CREATE TABLE IF NOT EXISTS `auth_users_permissions` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  KEY `auth_users_permissions_permission_id_foreign` (`permission_id`),
  KEY `user_id_permission_id` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2017-11-20-223112', 'Myth\\Auth\\Database\\Migrations\\CreateAuthTables', 'default', 'Myth\\Auth', 1678268800, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_guru`
--

CREATE TABLE IF NOT EXISTS `tb_guru` (
  `id_guru` int(11) NOT NULL AUTO_INCREMENT,
  `nuptk` varchar(24) NOT NULL,
  `nama_guru` varchar(255) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(32) NOT NULL,
  `unique_code` varchar(64) NOT NULL,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `unique_code` (`unique_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_kehadiran`
--

CREATE TABLE IF NOT EXISTS `tb_kehadiran` (
  `id_kehadiran` int(11) NOT NULL AUTO_INCREMENT,
  `kehadiran` enum('Hadir','Sakit','Izin','Tanpa keterangan') NOT NULL,
  PRIMARY KEY (`id_kehadiran`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_kehadiran`
--

INSERT INTO `tb_kehadiran` (`id_kehadiran`, `kehadiran`) VALUES
(1, 'Hadir'),
(2, 'Sakit'),
(3, 'Izin'),
(4, 'Tanpa keterangan');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kelas`
--

CREATE TABLE IF NOT EXISTS `tb_kelas` (
  `id_kelas` int(11) NOT NULL AUTO_INCREMENT,
  `kelas` enum('X','XI','XII') NOT NULL,
  `jurusan` enum('OTKP','BDP','AKL','RPL') NOT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `jurusan` (`jurusan`),
  KEY `kelas` (`kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_kelas`
--

INSERT INTO `tb_kelas` (`id_kelas`, `kelas`, `jurusan`) VALUES
(1, 'X', 'OTKP'),
(2, 'X', 'BDP'),
(3, 'X', 'AKL'),
(4, 'X', 'RPL'),
(5, 'XI', 'OTKP'),
(6, 'XI', 'BDP'),
(7, 'XI', 'AKL'),
(8, 'XI', 'RPL'),
(9, 'XII', 'OTKP'),
(10, 'XII', 'BDP'),
(11, 'XII', 'AKL'),
(12, 'XII', 'RPL');

-- --------------------------------------------------------

--
-- Table structure for table `tb_petugas`
--

CREATE TABLE IF NOT EXISTS `tb_petugas` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0,
  `password_hash` varchar(255) NOT NULL,
  `reset_hash` varchar(255) DEFAULT NULL,
  `reset_at` datetime DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `activate_hash` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `force_pass_reset` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tb_petugas`
--

INSERT INTO `tb_petugas` (`id`, `email`, `username`, `is_superadmin`, `password_hash`, `reset_hash`, `reset_at`, `reset_expires`, `activate_hash`, `status`, `status_message`, `active`, `force_pass_reset`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'uzy@gmail.com', 'uzy123', 0, '$2y$10$uxinNGirHUz7Ha8indna1efqjh8RRGlGobOqwX85cnXofxabI2hie', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2023-02-28 10:51:34', '2023-02-28 10:51:34', NULL),
(3, 'icb@email.com', 'icbcn', 0, '$2y$10$2Js6ylrE/OHH2ypTWNNBy.aUMAV9/QMEz0WAZ8hl5DlZQAjnds...', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2023-02-28 11:34:12', '2023-02-28 11:34:12', NULL),
(4, 'adminsuper@gmail.com', 'superadmin', 1, '$2y$10$aKv1FQB71foXkofgy3YAROfrI/24mb2DsNaPMlkUtlQaqiGQUPAnG', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2023-03-07 21:07:31', '2023-03-07 21:07:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_presensi_guru`
--

CREATE TABLE IF NOT EXISTS `tb_presensi_guru` (
  `id_presensi` int(11) NOT NULL AUTO_INCREMENT,
  `id_guru` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `id_kehadiran` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  PRIMARY KEY (`id_presensi`),
  KEY `id_kehadiran` (`id_kehadiran`),
  KEY `id_guru` (`id_guru`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_presensi_siswa`
--

CREATE TABLE IF NOT EXISTS `tb_presensi_siswa` (
  `id_presensi` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `id_kehadiran` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  PRIMARY KEY (`id_presensi`),
  KEY `id_siswa` (`id_siswa`),
  KEY `id_kehadiran` (`id_kehadiran`),
  KEY `id_kelas` (`id_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_siswa`
--

CREATE TABLE IF NOT EXISTS `tb_siswa` (
  `id_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(16) NOT NULL,
  `nama_siswa` varchar(255) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `no_hp` varchar(32) NOT NULL,
  `unique_code` varchar(64) NOT NULL,
  PRIMARY KEY (`id_siswa`),
  UNIQUE KEY `unique_code` (`unique_code`),
  KEY `id_kelas` (`id_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD CONSTRAINT `auth_groups_permissions_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD CONSTRAINT `auth_groups_users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD CONSTRAINT `auth_users_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_users_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_presensi_guru`
--
ALTER TABLE `tb_presensi_guru`
  ADD CONSTRAINT `tb_presensi_guru_ibfk_2` FOREIGN KEY (`id_kehadiran`) REFERENCES `tb_kehadiran` (`id_kehadiran`),
  ADD CONSTRAINT `tb_presensi_guru_ibfk_3` FOREIGN KEY (`id_guru`) REFERENCES `tb_guru` (`id_guru`) ON DELETE SET NULL;

--
-- Constraints for table `tb_presensi_siswa`
--
ALTER TABLE `tb_presensi_siswa`
  ADD CONSTRAINT `tb_presensi_siswa_ibfk_2` FOREIGN KEY (`id_kehadiran`) REFERENCES `tb_kehadiran` (`id_kehadiran`),
  ADD CONSTRAINT `tb_presensi_siswa_ibfk_3` FOREIGN KEY (`id_siswa`) REFERENCES `tb_siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_presensi_siswa_ibfk_4` FOREIGN KEY (`id_kelas`) REFERENCES `tb_kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD CONSTRAINT `tb_siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `tb_kelas` (`id_kelas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
