-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 16, 2025 at 08:04 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_percobaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `id` int NOT NULL,
  `nama_divisi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`id`, `nama_divisi`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'purchasing', NULL, '2025-04-23 01:41:47', '2025-05-06 11:44:22'),
(2, 'finance', NULL, '2025-04-23 01:42:56', '2025-05-06 11:51:09'),
(3, 'hrd dan ga', NULL, '2025-04-23 01:42:56', '2025-05-06 11:44:22'),
(4, 'marketing', NULL, '2025-04-23 01:44:01', '2025-05-06 11:44:22'),
(5, 'produksi', NULL, '2025-04-26 23:15:14', '2025-05-06 11:44:22'),
(6, 'gudang', NULL, '2025-04-26 23:16:26', '2025-05-06 11:44:22'),
(7, 'hse', NULL, '2025-04-26 23:16:56', '2025-05-06 11:44:22'),
(8, 'project manager', NULL, '2025-04-26 23:17:53', '2025-05-06 11:44:22'),
(9, 'docon', NULL, '2025-04-26 23:18:32', '2025-05-06 11:44:22'),
(10, 'qc', NULL, '2025-04-26 23:18:48', '2025-05-06 11:44:22'),
(11, 'it', NULL, '2025-04-26 23:19:05', '2025-05-06 11:44:22'),
(12, 'tidak_ada', NULL, '2025-05-05 14:45:18', '2025-05-06 11:44:22');

-- --------------------------------------------------------

--
-- Table structure for table `evaluasi_kinerja`
--

CREATE TABLE `evaluasi_kinerja` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `pegawai_id` int NOT NULL,
  `manager_id` int NOT NULL,
  `direksi_id` int NOT NULL,
  `divisi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penilaian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `skor` int DEFAULT NULL,
  `evaluasi_dari` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_kerja`
--

CREATE TABLE `laporan_kerja` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `divisi_id` int NOT NULL,
  `manager_id` int NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto_dokumen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('Pending','selesai','berjalan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `catatan_penolakan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status_approval` enum('Approved','Rejected','Pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager_evaluations`
--

CREATE TABLE `manager_evaluations` (
  `id` int UNSIGNED NOT NULL,
  `manager_id` int UNSIGNED NOT NULL,
  `evaluated_by` int UNSIGNED NOT NULL,
  `score` int NOT NULL,
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-04-23-190456', 'App\\Database\\Migrations\\CreateManagerEvaluationsTable', 'default', 'App', 1745435117, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('pegawai','manager','admin','hrd','direksi') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id` int NOT NULL,
  `users_id` int DEFAULT NULL,
  `jenis_pengajuan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_approval` enum('Menunggu Persetujuan HRD','Disetujui HRD','Ditolak HRD','Menunggu Keputusan Direksi','Disetujui Direksi','Ditolak Direksi') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` int NOT NULL,
  `manajer_id` int NOT NULL,
  `pegawai_id` int NOT NULL,
  `jenis_penghargaan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `alasan` text COLLATE utf8mb4_general_ci NOT NULL,
  `catatan_penolakan` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan`
--

INSERT INTO `pengajuan` (`id`, `users_id`, `jenis_pengajuan`, `status_approval`, `created_at`, `updated_at`, `manajer_id`, `pegawai_id`, `jenis_penghargaan`, `alasan`, `catatan_penolakan`) VALUES
(25, NULL, NULL, NULL, '2025-05-01 06:49:38', 2025, 0, 0, '', '', ''),
(26, NULL, NULL, NULL, '2025-05-01 06:49:38', 2025, 0, 0, '', '', ''),
(27, NULL, NULL, NULL, '2025-05-01 06:49:39', 2025, 0, 0, '', '', ''),
(28, NULL, NULL, NULL, '2025-05-01 06:49:39', 2025, 0, 0, '', '', ''),
(29, NULL, NULL, NULL, '2025-05-01 06:49:39', 2025, 0, 0, '', '', ''),
(30, NULL, NULL, NULL, '2025-05-01 06:49:40', 2025, 0, 0, '', '', ''),
(31, NULL, NULL, NULL, '2025-05-01 06:49:40', 2025, 0, 0, '', '', ''),
(32, NULL, NULL, NULL, '2025-05-01 06:49:40', 2025, 0, 0, '', '', ''),
(33, NULL, NULL, NULL, '2025-05-01 06:49:41', 2025, 0, 0, '', '', ''),
(34, NULL, NULL, NULL, '2025-05-01 06:49:41', 2025, 0, 0, '', '', ''),
(35, NULL, NULL, NULL, '2025-05-01 06:49:41', 2025, 0, 0, '', '', ''),
(36, NULL, NULL, NULL, '2025-05-01 06:49:42', 2025, 0, 0, '', '', ''),
(37, NULL, NULL, NULL, '2025-05-01 06:49:42', 2025, 0, 0, '', '', ''),
(38, NULL, NULL, NULL, '2025-05-01 06:49:42', 2025, 0, 0, '', '', ''),
(39, NULL, NULL, NULL, '2025-05-01 06:49:43', 2025, 0, 0, '', '', ''),
(40, NULL, NULL, NULL, '2025-05-01 06:49:43', 2025, 0, 0, '', '', ''),
(41, NULL, NULL, NULL, '2025-05-01 06:49:43', 2025, 0, 0, '', '', ''),
(42, NULL, NULL, NULL, '2025-05-01 06:49:44', 2025, 0, 0, '', '', ''),
(43, NULL, NULL, NULL, '2025-05-01 06:49:51', 2025, 0, 0, '', '', ''),
(44, NULL, NULL, NULL, '2025-05-01 06:49:51', 2025, 0, 0, '', '', ''),
(45, NULL, NULL, NULL, '2025-05-01 06:49:52', 2025, 0, 0, '', '', ''),
(46, NULL, NULL, NULL, '2025-05-01 06:49:52', 2025, 0, 0, '', '', ''),
(47, NULL, NULL, NULL, '2025-05-01 06:49:53', 2025, 0, 0, '', '', ''),
(48, NULL, NULL, NULL, '2025-05-01 06:49:53', 2025, 0, 0, '', '', ''),
(49, NULL, NULL, NULL, '2025-05-01 06:49:53', 2025, 0, 0, '', '', ''),
(50, NULL, NULL, NULL, '2025-05-01 06:49:54', 2025, 0, 0, '', '', ''),
(51, NULL, NULL, NULL, '2025-05-01 06:49:54', 2025, 0, 0, '', '', ''),
(52, NULL, NULL, NULL, '2025-05-01 06:49:54', 2025, 0, 0, '', '', ''),
(53, NULL, NULL, NULL, '2025-05-01 06:49:55', 2025, 0, 0, '', '', ''),
(54, NULL, NULL, NULL, '2025-05-01 06:49:55', 2025, 0, 0, '', '', ''),
(55, NULL, NULL, NULL, '2025-05-01 06:49:55', 2025, 0, 0, '', '', ''),
(56, NULL, NULL, NULL, '2025-05-01 06:49:56', 2025, 0, 0, '', '', ''),
(57, NULL, NULL, NULL, '2025-05-01 06:49:56', 2025, 0, 0, '', '', ''),
(58, NULL, NULL, NULL, '2025-05-01 06:49:56', 2025, 0, 0, '', '', ''),
(59, NULL, NULL, NULL, '2025-05-01 06:49:57', 2025, 0, 0, '', '', ''),
(60, NULL, NULL, NULL, '2025-05-01 06:49:57', 2025, 0, 0, '', '', ''),
(61, NULL, NULL, NULL, '2025-05-01 06:49:57', 2025, 0, 0, '', '', ''),
(62, NULL, NULL, NULL, '2025-05-01 06:49:58', 2025, 0, 0, '', '', ''),
(63, NULL, NULL, NULL, '2025-05-01 06:49:59', 2025, 0, 0, '', '', ''),
(64, NULL, NULL, NULL, '2025-05-01 06:49:59', 2025, 0, 0, '', '', ''),
(65, NULL, NULL, NULL, '2025-05-01 06:50:00', 2025, 0, 0, '', '', ''),
(66, NULL, NULL, NULL, '2025-05-01 06:50:00', 2025, 0, 0, '', '', ''),
(67, NULL, NULL, NULL, '2025-05-01 06:50:00', 2025, 0, 0, '', '', ''),
(68, NULL, NULL, NULL, '2025-05-01 06:50:01', 2025, 0, 0, '', '', ''),
(69, NULL, NULL, NULL, '2025-05-01 06:50:01', 2025, 0, 0, '', '', ''),
(70, NULL, NULL, NULL, '2025-05-01 06:50:01', 2025, 0, 0, '', '', ''),
(71, NULL, NULL, NULL, '2025-05-01 06:50:02', 2025, 0, 0, '', '', ''),
(72, NULL, NULL, NULL, '2025-05-01 06:50:02', 2025, 0, 0, '', '', ''),
(73, NULL, NULL, NULL, '2025-05-01 06:50:02', 2025, 0, 0, '', '', ''),
(74, NULL, NULL, NULL, '2025-05-01 06:50:03', 2025, 0, 0, '', '', ''),
(75, NULL, NULL, NULL, '2025-05-01 06:50:03', 2025, 0, 0, '', '', ''),
(76, NULL, NULL, NULL, '2025-05-01 06:50:04', 2025, 0, 0, '', '', ''),
(77, NULL, NULL, NULL, '2025-05-01 06:50:04', 2025, 0, 0, '', '', ''),
(78, NULL, NULL, NULL, '2025-05-01 06:50:04', 2025, 0, 0, '', '', ''),
(79, NULL, NULL, NULL, '2025-05-01 06:50:05', 2025, 0, 0, '', '', ''),
(80, NULL, NULL, NULL, '2025-05-01 06:50:05', 2025, 0, 0, '', '', ''),
(81, NULL, NULL, NULL, '2025-05-01 06:50:05', 2025, 0, 0, '', '', ''),
(82, NULL, NULL, NULL, '2025-05-01 06:50:06', 2025, 0, 0, '', '', ''),
(83, NULL, NULL, NULL, '2025-05-01 06:50:11', 2025, 0, 0, '', '', ''),
(84, NULL, NULL, NULL, '2025-05-01 06:50:11', 2025, 0, 0, '', '', ''),
(85, NULL, NULL, NULL, '2025-05-01 06:50:12', 2025, 0, 0, '', '', ''),
(86, NULL, NULL, NULL, '2025-05-01 06:50:12', 2025, 0, 0, '', '', ''),
(87, NULL, NULL, NULL, '2025-05-01 06:50:12', 2025, 0, 0, '', '', ''),
(88, NULL, NULL, NULL, '2025-05-01 06:50:13', 2025, 0, 0, '', '', ''),
(89, NULL, NULL, NULL, '2025-05-01 06:50:13', 2025, 0, 0, '', '', ''),
(90, NULL, NULL, NULL, '2025-05-01 06:50:13', 2025, 0, 0, '', '', ''),
(91, NULL, NULL, NULL, '2025-05-01 06:50:14', 2025, 0, 0, '', '', ''),
(92, NULL, NULL, NULL, '2025-05-01 06:50:14', 2025, 0, 0, '', '', ''),
(93, NULL, NULL, NULL, '2025-05-01 06:50:14', 2025, 0, 0, '', '', ''),
(94, NULL, NULL, NULL, '2025-05-01 06:50:14', 2025, 0, 0, '', '', ''),
(95, NULL, NULL, NULL, '2025-05-01 06:50:14', 2025, 0, 0, '', '', ''),
(96, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', ''),
(97, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', ''),
(98, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', ''),
(99, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', ''),
(100, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', ''),
(101, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', ''),
(102, NULL, NULL, NULL, '2025-05-01 06:50:15', 2025, 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_penghargaan`
--

CREATE TABLE `pengajuan_penghargaan` (
  `id` int NOT NULL,
  `users_id` int NOT NULL,
  `pegawai_id` int DEFAULT NULL,
  `hrd_id` int NOT NULL,
  `direksi_id` int NOT NULL,
  `manajer_id` int DEFAULT NULL,
  `jenis_penghargaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Disetujui','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_sp`
--

CREATE TABLE `pengajuan_sp` (
  `id` int NOT NULL,
  `pegawai_id` int DEFAULT NULL,
  `manajer_id` int DEFAULT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Disetujui','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penghargaan`
--

CREATE TABLE `penghargaan` (
  `id` int NOT NULL,
  `pegawai_id` int DEFAULT NULL,
  `manajer_id` int NOT NULL,
  `jenis_penghargaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Pending','Approved HRD','Approved DIREKSI','Rejected HRD','Rejected DIREKSI') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `catatan_penolakan` text COLLATE utf8mb4_general_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penilaian_pegawai`
--

CREATE TABLE `penilaian_pegawai` (
  `id` int NOT NULL,
  `pegawai_id` int DEFAULT NULL,
  `manajer_id` int DEFAULT NULL,
  `direksi_id` int DEFAULT NULL,
  `nilai` int DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tanggal_penilaian` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `manager_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rencana_kerja`
--

CREATE TABLE `rencana_kerja` (
  `id` int NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `divisi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `manager_id` int NOT NULL,
  `divisi_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_laporan`
--

CREATE TABLE `riwayat_laporan` (
  `id` int NOT NULL,
  `laporan_id` int NOT NULL,
  `status_sebelumnya` enum('Pending','Disetujui','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_baru` enum('Pending','Disetujui','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `users_id` int NOT NULL,
  `divisi_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pengajuan`
--

CREATE TABLE `riwayat_pengajuan` (
  `id` int NOT NULL,
  `pengajuan_id` int NOT NULL,
  `peran` enum('manager','hrd','direksi') COLLATE utf8mb4_general_ci NOT NULL,
  `keputusan` enum('approve','reject') COLLATE utf8mb4_general_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_penilaian`
--

CREATE TABLE `riwayat_penilaian` (
  `id` int NOT NULL,
  `pegawai_id` int DEFAULT NULL,
  `manajer_id` int DEFAULT NULL,
  `nilai` int DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tanggal_penilaian` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int NOT NULL,
  `nama_role` varchar(50) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `nama_role`, `create_at`, `updated_at`) VALUES
(1, 'PEGAWAI', '2025-05-05 13:56:18', '2025-05-06 11:19:20'),
(2, 'MANAGER', '2025-05-05 13:57:30', '2025-05-06 11:19:20'),
(3, 'HRD', '2025-05-05 13:57:45', '2025-05-06 11:19:20'),
(4, 'DIREKSI', '2025-05-05 13:57:59', '2025-05-06 11:19:20'),
(5, 'ADMIN', '2025-05-05 14:46:26', '2025-05-06 11:19:20');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_activity` int NOT NULL,
  `user_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sp`
--

CREATE TABLE `sp` (
  `id` int NOT NULL,
  `pegawai_id` int DEFAULT NULL,
  `manajer_id` int NOT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Approved HRD','Approved DIREKSI','Rejected HRD','Rejected DIREKSI') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `catatan_penolakan` text COLLATE utf8mb4_general_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','pegawai','manager','hrd','direksi') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `divisi` enum('tidak_ada','hrd dan ga','purchasing','finance','marketing','produksi','gudang','hse','project manager','docon','qc','it') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pegawai_id` int NOT NULL,
  `divisi_id` int NOT NULL,
  `role_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waktu_penilaian`
--

CREATE TABLE `waktu_penilaian` (
  `id` int NOT NULL,
  `tanggal_mulai` timestamp NOT NULL,
  `tanggal_selesai` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluasi_kinerja`
--
ALTER TABLE `evaluasi_kinerja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pegawai_id` (`user_id`),
  ADD UNIQUE KEY `manager_id` (`user_id`);

--
-- Indexes for table `laporan_kerja`
--
ALTER TABLE `laporan_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `manager_evaluations`
--
ALTER TABLE `manager_evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_penghargaan`
--
ALTER TABLE `pengajuan_penghargaan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hrd_id` (`users_id`),
  ADD UNIQUE KEY `direksi_id` (`users_id`),
  ADD KEY `pegawai_id` (`pegawai_id`),
  ADD KEY `manajer_id` (`manajer_id`);

--
-- Indexes for table `pengajuan_sp`
--
ALTER TABLE `pengajuan_sp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`),
  ADD KEY `manajer_id` (`manajer_id`);

--
-- Indexes for table `penghargaan`
--
ALTER TABLE `penghargaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indexes for table `penilaian_pegawai`
--
ALTER TABLE `penilaian_pegawai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`),
  ADD KEY `manajer_id` (`manajer_id`),
  ADD KEY `direksi_id` (`direksi_id`);

--
-- Indexes for table `rencana_kerja`
--
ALTER TABLE `rencana_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `riwayat_laporan`
--
ALTER TABLE `riwayat_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_id` (`laporan_id`);

--
-- Indexes for table `riwayat_pengajuan`
--
ALTER TABLE `riwayat_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`);

--
-- Indexes for table `riwayat_penilaian`
--
ALTER TABLE `riwayat_penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`),
  ADD KEY `manajer_id` (`manajer_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sp`
--
ALTER TABLE `sp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `divisi_id` (`divisi_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `waktu_penilaian`
--
ALTER TABLE `waktu_penilaian`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `evaluasi_kinerja`
--
ALTER TABLE `evaluasi_kinerja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_kerja`
--
ALTER TABLE `laporan_kerja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=292;

--
-- AUTO_INCREMENT for table `manager_evaluations`
--
ALTER TABLE `manager_evaluations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengajuan_penghargaan`
--
ALTER TABLE `pengajuan_penghargaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_sp`
--
ALTER TABLE `pengajuan_sp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penghargaan`
--
ALTER TABLE `penghargaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `penilaian_pegawai`
--
ALTER TABLE `penilaian_pegawai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `rencana_kerja`
--
ALTER TABLE `rencana_kerja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `riwayat_laporan`
--
ALTER TABLE `riwayat_laporan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_penilaian`
--
ALTER TABLE `riwayat_penilaian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sp`
--
ALTER TABLE `sp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `waktu_penilaian`
--
ALTER TABLE `waktu_penilaian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluasi_kinerja`
--
ALTER TABLE `evaluasi_kinerja`
  ADD CONSTRAINT `evaluasi_kinerja_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `laporan_kerja`
--
ALTER TABLE `laporan_kerja`
  ADD CONSTRAINT `laporan_kerja_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan_penghargaan`
--
ALTER TABLE `pengajuan_penghargaan`
  ADD CONSTRAINT `pengajuan_penghargaan_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pengajuan_penghargaan_ibfk_2` FOREIGN KEY (`manajer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pengajuan_sp`
--
ALTER TABLE `pengajuan_sp`
  ADD CONSTRAINT `pengajuan_sp_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pengajuan_sp_ibfk_2` FOREIGN KEY (`manajer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `penghargaan`
--
ALTER TABLE `penghargaan`
  ADD CONSTRAINT `pengahrga_pegawai` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penilaian_pegawai`
--
ALTER TABLE `penilaian_pegawai`
  ADD CONSTRAINT `penilaian_pegawai_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penilaian_pegawai_ibfk_2` FOREIGN KEY (`manajer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penilaian_pegawai_ibfk_3` FOREIGN KEY (`direksi_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rencana_kerja`
--
ALTER TABLE `rencana_kerja`
  ADD CONSTRAINT `rencan_kerja` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_laporan`
--
ALTER TABLE `riwayat_laporan`
  ADD CONSTRAINT `riwayat_laporan_ibfk_1` FOREIGN KEY (`laporan_id`) REFERENCES `laporan_kerja` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sp`
--
ALTER TABLE `sp`
  ADD CONSTRAINT `sp_pegawai` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
