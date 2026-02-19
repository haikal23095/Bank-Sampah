-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 14, 2026 at 09:08 AM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bank_sampah`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_09_091630_waste_categories', 1),
(5, '2026_02_09_091706_waste_types', 1),
(6, '2026_02_09_091754_transactions', 1),
(7, '2026_02_09_091803_transactions_details', 1),
(8, '2026_02_09_091916_wallets', 1),
(9, '2026_02_10_120000_drop_method_from_transactions', 1),
(10, '2026_02_10_121500_create_withdrawals_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('fx6jm4JJOGOdIUwIkSYgVil56Pj7QaFoSsqmFqht', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiak45RFJvQ0kzUHdLV2V3SmpFSlpLWTVZMXNtTGlXWExEeXhhcUpQYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9uYXNhYmFoL2NhdGFsb2ciO3M6NToicm91dGUiO3M6MjE6Im5hc2FiYWguY2F0YWxvZy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7fQ==', 1770971373);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `staff_id`, `date`, `admin_note`, `created_at`, `updated_at`) VALUES
(1, 4, 2, '2024-11-01', 'Setoran awal rutin', '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(2, 5, 3, '2024-11-01', 'Pembersihan kantor', '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(3, 6, 2, '2024-11-01', 'Setoran warga RT 01', '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(4, 7, 3, '2024-11-01', 'Sampah rumah tangga', '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(5, 4, 2, '2024-11-01', 'Tambahan kardus pindahan', '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(6, 5, 3, '2024-11-01', 'Setoran harian', '2024-10-31 19:27:39', '2024-10-31 19:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED NOT NULL,
  `waste_type_id` bigint UNSIGNED NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `waste_type_id`, `weight`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5.00, 22500.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(2, 1, 4, 10.00, 22000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(3, 2, 5, 20.00, 60000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(4, 2, 6, 5.00, 60000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(5, 3, 2, 15.00, 37500.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(6, 3, 7, 50.00, 200000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(7, 4, 1, 2.00, 9000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(8, 4, 8, 12.00, 6000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(9, 5, 4, 15.00, 33000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(10, 6, 1, 3.00, 13500.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39'),
(11, 6, 2, 4.00, 10000.00, '2024-10-31 19:27:39', '2024-10-31 19:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('ADMIN','PETUGAS','NASABAH') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NASABAH',
  `address` text COLLATE utf8mb4_unicode_ci,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `address`, `bank_name`, `account_number`, `join_date`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@mail.com', '$2y$12$n.wAyU.Sve.Po9ElJIO/VeK6j3/uH80irvcF9YS8wzUScPnraRH.u', '081232201960', 'ADMIN', 'Kecamatan Sukamaju No. 46', 'BCA', '721087632', '2024-12-13', '2024-12-13 00:27:39', NULL),
(2, 'Siti Aminah (PETUGAS)', 'siti@mail.com', '$2y$12$6BlhHJpTvtLaYZrCUbz4OexVRvcuPJzKTD3ViovMf0X5.rajUD3ha', '081225059766', 'PETUGAS', 'Kecamatan Sukamaju No. 47', 'BCA', '706338812', '2024-12-13', '2024-12-13 00:27:39', NULL),
(3, 'Eko Prasetyo (PETUGAS)', 'eko@mail.com', '$2y$12$lOY82NGlsiq9ALnAO2Kd/uxuBec6KQleAtv6YXUscy1tQsnb3GLWO', '081217669398', 'PETUGAS', 'Kecamatan Sukamaju No. 20', 'BCA', '658323740', '2024-12-13', '2024-12-13 00:27:39', NULL),
(4, 'Budi Santoso', 'budi@mail.com', '$2y$12$hboOaVU4wAQVJgD/PtFlduZXhqQdjpnviJ0HjKcYbzBCnFhzgBav6', '081268496681', 'NASABAH', 'Kecamatan Sukamaju No. 61', 'BCA', '768181651', '2024-12-13', '2024-12-13 00:27:39', NULL),
(5, 'Ani Wijaya', 'ani@mail.com', '$2y$12$XH6a5WHcgt0JpwVkyt3bEeNTnvAWFRqsABjkvgSbjp5e8ULvc/GYq', '081266480127', 'NASABAH', 'Kecamatan Sukamaju No. 74', 'BCA', '126447334', '2024-12-13', '2024-12-13 00:27:39', NULL),
(6, 'Iwan Fals', 'iwan@mail.com', '$2y$12$b7cEcN4zmuh7D5fgfvy4ye1fC4XDHS4YedAEU4D9FIcQB5Cn.JbZO', '081250584183', 'NASABAH', 'Kecamatan Sukamaju No. 64', 'BCA', '424017177', '2024-12-13', '2024-12-13 00:27:39', NULL),
(7, 'Siska Putri', 'siska@mail.com', '$2y$12$Oz7vdK3lnFMIyLLNNFyD.e49x2QFIoUXCsrJlhJDN7gW.MmFspLLq', '081270363828', 'NASABAH', 'Kecamatan Sukamaju No. 78', 'BCA', '988443464', '2024-12-13', '2024-12-13 00:27:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `last_updated`, `created_at`, `updated_at`) VALUES
(1, 4, 55000.00, '2024-12-13 00:27:39', '2024-12-13 00:27:39', NULL),
(2, 5, 120000.00, '2024-12-13 00:27:39', '2024-12-13 00:27:39', NULL),
(3, 6, 0.00, '2024-12-13 00:27:39', '2024-12-13 00:27:39', NULL),
(4, 7, 15000.00, '2024-12-13 00:27:39', '2024-12-13 00:27:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `waste_categories`
--

CREATE TABLE `waste_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waste_categories`
--

INSERT INTO `waste_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Plastik', 'Botol PET, PP, HD, gelas plastik, dll.', '2026-02-13 00:27:39', NULL),
(2, 'Kertas', 'Kardus, kertas kantor, koran, majalah.', '2026-02-13 00:27:39', NULL),
(3, 'Logam', 'Besi, aluminium, kuningan, tembaga.', '2026-02-13 00:27:39', NULL),
(4, 'Kaca', 'Botol kaca utuh atau beling.', '2026-02-13 00:27:39', NULL),
(5, 'Elektronik', 'Hp rusak, kabel, komponen PC.', '2026-02-13 00:27:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `waste_types`
--

CREATE TABLE `waste_types` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_per_kg` decimal(12,2) NOT NULL,
  `unit` enum('kg','pcs') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waste_types`
--

INSERT INTO `waste_types` (`id`, `category_id`, `name`, `price_per_kg`, `unit`, `created_at`, `updated_at`) VALUES
(1, 1, 'Botol PET Bersih', 4500.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(2, 1, 'Gelas Plastik PP', 2500.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(3, 1, 'Plastik Campuran', 1000.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(4, 2, 'Kardus Double Wall', 2200.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(5, 2, 'Kertas Putih HVS', 3000.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(6, 3, 'Kaleng Softdrink (Alu)', 12000.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(7, 3, 'Besi Tua', 4000.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39'),
(8, 4, 'Botol Sirup/Kecap', 500.00, 'kg', '2026-02-13 00:27:39', '2026-02-13 00:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('PENDING','SUCCESS','FAILED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `method` enum('CASH','TRANSFER') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `user_id`, `staff_id`, `date`, `amount`, `status`, `method`, `admin_note`, `created_at`, `updated_at`) VALUES
(1, 4, 2, '2024-09-18', 15000.00, 'SUCCESS', 'CASH', 'Penarikan tunai sukses', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(2, 4, 2, '2024-09-18', 100000.00, 'FAILED', 'TRANSFER', 'Saldo tidak mencukupi', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(3, 4, 2, '2024-09-18', 5000.00, 'PENDING', 'CASH', 'Proses verifikasi fisik', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(4, 5, 3, '2024-09-18', 50000.00, 'SUCCESS', 'TRANSFER', 'Transfer via BCA', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(5, 5, 3, '2024-09-18', 20000.00, 'SUCCESS', 'CASH', 'Penarikan di kantor unit', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(6, 5, 3, '2024-09-18', 10000.00, 'PENDING', 'TRANSFER', 'Menunggu persetujuan admin', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(7, 7, 2, '2024-09-18', 10000.00, 'SUCCESS', 'CASH', 'Penarikan rutin', '2024-09-17 16:42:39', '2024-09-17 16:42:39'),
(8, 7, 2, '2024-09-18', 50000.00, 'FAILED', 'TRANSFER', 'Limit harian terlampaui', '2024-09-17 16:42:39', '2024-09-17 16:42:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_staff_id_foreign` (`staff_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_details_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_details_waste_type_id_foreign` (`waste_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallets_user_id_unique` (`user_id`);

--
-- Indexes for table `waste_categories`
--
ALTER TABLE `waste_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waste_types`
--
ALTER TABLE `waste_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `waste_types_category_id_foreign` (`category_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawals_user_id_foreign` (`user_id`),
  ADD KEY `withdrawals_staff_id_foreign` (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `waste_categories`
--
ALTER TABLE `waste_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `waste_types`
--
ALTER TABLE `waste_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_waste_type_id_foreign` FOREIGN KEY (`waste_type_id`) REFERENCES `waste_types` (`id`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `waste_types`
--
ALTER TABLE `waste_types`
  ADD CONSTRAINT `waste_types_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `waste_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
