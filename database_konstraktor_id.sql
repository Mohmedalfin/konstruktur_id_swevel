-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 31, 2026 at 12:46 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_konstraktor_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `ahs`
--

CREATE TABLE `ahs` (
  `id_ahs` int UNSIGNED NOT NULL,
  `id_proyek` int UNSIGNED NOT NULL,
  `kode_ahs` varchar(50) DEFAULT NULL,
  `tipe_ahs` enum('Bahan','Alat','Upah') NOT NULL,
  `nama_item` varchar(150) NOT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `spesifikasi` text,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `sumber` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_pekerjaan`
--

CREATE TABLE `kategori_pekerjaan` (
  `id_kategori_pekerjaan` int UNSIGNED NOT NULL,
  `id_proyek` int UNSIGNED DEFAULT NULL,
  `kode_kategori` varchar(50) DEFAULT NULL,
  `nama_kategori` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori_pekerjaan`
--

INSERT INTO `kategori_pekerjaan` (`id_kategori_pekerjaan`, `id_proyek`, `kode_kategori`, `nama_kategori`) VALUES
(1, NULL, 'K-PRS', 'Pekerjaan Persiapan'),
(2, NULL, 'K-STR', 'Pekerjaan Struktur'),
(3, NULL, 'K-ARS', 'Pekerjaan Arsitektur'),
(4, NULL, 'K-MEP', 'Pekerjaan MEP'),
(5, NULL, 'K-FIN', 'Pekerjaan Finishing'),
(46, 9, 'kustom_547f997e', 'Pekerjaan Cor Berat');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-03-09-025431', 'App\\Database\\Migrations\\Pekerjaan', 'default', 'App', 1774841106, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `id_pekerjaan` int UNSIGNED NOT NULL,
  `id_kategori_pekerjaan` int UNSIGNED DEFAULT NULL,
  `id_proyek` int UNSIGNED NOT NULL,
  `kode_pekerjaan` varchar(50) DEFAULT NULL,
  `nama_pekerjaan` varchar(255) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `sumber` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pekerjaan`
--

INSERT INTO `pekerjaan` (`id_pekerjaan`, `id_kategori_pekerjaan`, `id_proyek`, `kode_pekerjaan`, `nama_pekerjaan`, `satuan`, `sumber`, `created_at`, `updated_at`) VALUES
(2, NULL, 9, 'kustom_1a5e7ed1', 'Pekerjaan Ketok Palu', 'm2', 'Proyek Terkini', '2026-03-30 23:35:47', '2026-03-30 23:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `proyek`
--

CREATE TABLE `proyek` (
  `id_proyek` int UNSIGNED NOT NULL,
  `id_user` int UNSIGNED NOT NULL,
  `kode_proyek` varchar(50) DEFAULT NULL,
  `slug` varchar(50) DEFAULT NULL,
  `nama_proyek` varchar(200) NOT NULL,
  `lokasi_proyek` text,
  `jenis_proyek` varchar(100) DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `nama_owner_klien` varchar(150) DEFAULT NULL,
  `perusahaan` varchar(150) DEFAULT NULL,
  `nomor_kontrak` varchar(100) DEFAULT NULL,
  `keterangan_lain` text,
  `foto_project` varchar(255) DEFAULT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `status` enum('Berjalan','Selesai') DEFAULT 'Berjalan',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proyek`
--

INSERT INTO `proyek` (`id_proyek`, `id_user`, `kode_proyek`, `slug`, `nama_proyek`, `lokasi_proyek`, `jenis_proyek`, `tgl_mulai`, `tgl_selesai`, `nama_owner_klien`, `perusahaan`, `nomor_kontrak`, `keterangan_lain`, `foto_project`, `file_pendukung`, `status`, `deleted_at`) VALUES
(1, 3, 'PRJ-202603-0001', NULL, 'Gedung Kampus ITS', 'Semarang', 'Konstraktor', '2026-03-30', '2026-05-30', 'Prof. Bahlil  Lahadiah, S.T, M.T, Ph.D', 'ITS', '081223435678', '', '1774841295_550254b37a8ce17e3b26.jpeg', '[\"1774841295_57151c1751346827ba86.pdf\"]', 'Berjalan', '2026-03-30 05:09:12'),
(2, 3, 'PRJ-202603-0002', NULL, 'Gedung Kreativitas UGM ', 'Sleman, Yogyakarta', 'Konstraktor', '2026-04-02', '2026-06-30', 'Prof. Usama Fadlillah, S.T, M.T, Ph.D', 'UGM Center', '081245567890', '', '1774841579_d3dbe888d36d445a55e1.jpeg', '[\"1774841579_faf498febc5ece7b5cdb.pdf\",\"1774841579_c5d7d94558c5fbda9c3b.pdf\"]', 'Berjalan', '2026-03-30 05:09:07'),
(3, 3, 'PRJ-202603-0003', 'gedung-laboratorium-uty-byxegx', 'Gedung Laboratorium UTY', 'Sleman, Yogyakarta', 'Konstraktor', '2026-03-30', '2026-07-22', 'Prof. Bambang Budiono, S.T, M.T, Ph.D', 'UTY Bergerak', '081245567890', '', '1774842369_cd3f7a1fc3c5cb1e7b5a.jpg', '[\"1774842369_665b24818c5b6c894ef8.pdf\",\"1774842369_41a99ba786e447b294ad.pdf\"]', 'Selesai', NULL),
(4, 3, 'PRJ-202603-0004', 'kolam-renang-sangkanurip-p3omaj', 'Kolam Renang Sangkanurip', 'Cilimus, Kuningan', 'Wisata', '2026-05-21', '2026-09-24', 'M. Alfin Maulana', 'Kuningan Water Boom', '081245567890', '', '1774845416_207e29409e79e5f88157.jpeg', NULL, 'Berjalan', '2026-03-30 05:09:00'),
(7, 3, 'PRJ-202603-0005', 'gedung-kreativitas-ugm-ofclzs', 'Gedung Kreativitas UGM ', 'Sleman, Yogyakarta', 'Konstraktor', '2026-03-30', '2026-05-30', 'Prof. Usama Fadlillah, S.T, M.T, Ph.D', 'UGM Center', '081245567890', '', NULL, '[\"1774848377_deecd5cf72fe16b206ec.pdf\",\"1774848377_59f2f1ba2f426afb4a3a.pdf\"]', 'Selesai', NULL),
(8, 3, 'PRJ-202603-0006', 'gedung-kampus-its-4-f3fzfr', 'Gedung Kampus ITS 4', 'Semarang, Jawa Tengah', 'Gedung Kampus', '2026-03-30', '2026-08-21', 'Prof. Alfin Mualana, M.Cs', 'CATIB', '08122438995', '', '1774851573_ae5222f6fabfd082cea9.jpeg', '[\"1774851573_db61fc599a6c928306bc.pdf\",\"1774851573_078b74da7e0048681060.pdf\"]', 'Berjalan', '2026-03-30 06:57:30'),
(9, 3, 'PRJ-202603-0007', 'lab-kesehatan-stikes-kuningan-0koryn', 'Lab Kesehatan STIKES Kuningan', 'Kuningan, Jawa Barat', 'Konstraktor', '2026-03-31', '2026-09-25', 'Prof. Usama Fadlillah, S.T, M.T, Ph.D', 'CATIB', '081245567890', '', '1774859947_0c1b18c4b0d505a6ee32.png', '[\"1774859947_0bcfb5fc2f2648a84932.pdf\"]', 'Berjalan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rap`
--

CREATE TABLE `rap` (
  `id_rap` int UNSIGNED NOT NULL,
  `id_proyek` int UNSIGNED NOT NULL,
  `id_pekerjaan` int UNSIGNED NOT NULL,
  `volume` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `satuan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rap_detail`
--

CREATE TABLE `rap_detail` (
  `id_detail_rap` int UNSIGNED NOT NULL,
  `id_rap` int UNSIGNED NOT NULL,
  `id_ahs` int UNSIGNED NOT NULL,
  `koefesien` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int UNSIGNED NOT NULL,
  `kode_user` varchar(50) DEFAULT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `nama_perusahaan` varchar(150) DEFAULT NULL,
  `domisili_perusahaan` text,
  `alamat_proyek` varchar(255) DEFAULT NULL,
  `posisi_pekerjaan` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `kode_user`, `nama_lengkap`, `email`, `no_hp`, `nama_perusahaan`, `domisili_perusahaan`, `alamat_proyek`, `posisi_pekerjaan`, `password`) VALUES
(3, 'USR-20260326050055', 'Syahrul Mubarok', 'mohmedalfin170505@gmail.com', '081224389994', 'Universitas Teknologi Yogyakarta', 'yogyakarta', 'Trihanggo, Kec. Gamping, Kab. Sleman, Yogyakarta. Kode pos 55291', 'kontraktor', '$2y$10$70vay5iHkoR8B/L4JfeZzOjhCfFLHP8TE53mEB24yDIo88K9Wsre.'),
(4, 'USR-20260326055806', 'Arsy Attah Shibudin', 'arsy@gmail.com', '081224386797', 'Tukang Bangunan', 'yogyakarta', 'Trihanggo, Kec. Gamping, Kab. Sleman, Yogyakarta. Kode pos 55291', 'kontraktor', '$2y$10$/XnWDfncpGHxCSPuB3o8S.ohZnVv7GU3TVB542I6aPsY3r7LBXrgW'),
(5, 'USR-20260326062442', 'Usama Fadlilah', 'usama@gmail.com', '089090909090', 'Jakarta Konstruktor', 'yogyakarta', 'Trihanggo, Kec. Gamping, Kab. Sleman, Yogyakarta. Kode pos 55291', 'kontraktor', '$2y$10$d1emCye0ngyoeP8Uu0NTgOwyWHH9HAFUB1BcgzP72Chdy1F57Y0K2'),
(6, 'USR-20260326071205', 'Januar Agung Samapta', 'januar@gmail.com', '0898989899898', 'Ciamis Kontraktor', 'yogyakarta', 'Trihanggo, Kec. Gamping, Kab. Sleman, Yogyakarta. Kode pos 55291', 'kontraktor', '$2y$10$W5xkI53p8QuZoiXabmZYVOYmvcANXuWWek9ad70MXeqtDvlQj0N7y'),
(7, 'USR-9F26451724', 'Gege', 'gege@gmail.com', '089787878787', 'Gege Kontraktor', 'yogyakarta', 'Trihanggo, Kec. Gamping, Kab. Sleman, Yogyakarta. Kode pos 55291', 'purchasing', '$2y$10$jopIwj0oDBVDJ/Uno0EMO.ZieevOjSDE.a.P0o8uwqW12iZdPFJ3i');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
