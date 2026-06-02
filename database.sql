-- Court Queue System Database Schema
-- Import this file into phpMyAdmin on InfinityFree

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Database: if0_39954650_ccp
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','operator') NOT NULL DEFAULT 'staff',
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `password_reset_tokens`
-- --------------------------------------------------------

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `sessions`
-- --------------------------------------------------------

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cache`
-- --------------------------------------------------------

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cache_locks`
-- --------------------------------------------------------

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `jobs`
-- --------------------------------------------------------

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `job_batches`
-- --------------------------------------------------------

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `failed_jobs`
-- --------------------------------------------------------

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `courts`
-- --------------------------------------------------------

CREATE TABLE `courts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_number` varchar(20) NOT NULL,
  `court_type` varchar(50) NOT NULL DEFAULT 'standard',
  `status` enum('available','occupied','closed') NOT NULL DEFAULT 'available',
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courts_court_number_unique` (`court_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `players`
-- --------------------------------------------------------

CREATE TABLE `players` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_name` varchar(100) NOT NULL,
  `player_code` varchar(255) NOT NULL,
  `skill_level` varchar(20) NOT NULL DEFAULT 'intermediate',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `players_player_code_unique` (`player_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `queues`
-- --------------------------------------------------------

CREATE TABLE `queues` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `court_id` bigint(20) UNSIGNED NOT NULL,
  `player_id` bigint(20) UNSIGNED DEFAULT NULL,
  `queue_number` varchar(255) NOT NULL,
  `player_name` varchar(100) NOT NULL,
  `players_json` longtext DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `party_size` int(11) NOT NULL DEFAULT 2,
  `match_type` enum('singles','doubles') NOT NULL DEFAULT 'singles',
  `status` enum('waiting','called','serving','completed','skipped','cancelled') NOT NULL DEFAULT 'waiting',
  `priority` int(11) NOT NULL DEFAULT 0,
  `called_at` timestamp NULL DEFAULT NULL,
  `served_at` timestamp NULL DEFAULT NULL,
  `rental_ends_at` timestamp NULL DEFAULT NULL,
  `rental_minutes` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `queues_court_id_foreign` (`court_id`),
  KEY `queues_player_id_foreign` (`player_id`),
  KEY `queues_created_by_foreign` (`created_by`),
  KEY `queues_updated_by_foreign` (`updated_by`),
  KEY `queues_court_id_status_index` (`court_id`,`status`),
  KEY `queues_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `queues_court_id_foreign` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `queues_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `queues_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `queues_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `queue_logs`
-- --------------------------------------------------------

CREATE TABLE `queue_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue_id` bigint(20) UNSIGNED NOT NULL,
  `court_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `old_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `queue_logs_queue_id_foreign` (`queue_id`),
  KEY `queue_logs_court_id_foreign` (`court_id`),
  KEY `queue_logs_performed_by_foreign` (`performed_by`),
  CONSTRAINT `queue_logs_queue_id_foreign` FOREIGN KEY (`queue_id`) REFERENCES `queues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `queue_logs_court_id_foreign` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `queue_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `audit_logs`
-- --------------------------------------------------------

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `migrations`
-- --------------------------------------------------------

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dumping data for table `migrations`
-- --------------------------------------------------------

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000003_create_courts_table', 1),
(5, '2024_01_01_000004_create_players_table', 1),
(6, '2024_01_01_000005_create_queues_table', 1),
(7, '2024_01_01_000006_create_queue_logs_table', 1),
(8, '2024_01_01_000007_create_settings_table', 1),
(9, '2024_01_01_000008_create_audit_logs_table', 1);

-- --------------------------------------------------------
-- Dumping data for table `users`
-- Password: "password" (hashed with bcrypt)
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@courtqueue.com', '2026-06-02 00:00:00', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '123-456-7890', 1, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(2, 'Staff User', 'staff@courtqueue.com', '2026-06-02 00:00:00', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '123-456-7891', 1, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(3, 'Operator User', 'operator@courtqueue.com', '2026-06-02 00:00:00', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator', '123-456-7892', 1, '2026-06-02 00:00:00', '2026-06-02 00:00:00');

-- --------------------------------------------------------
-- Dumping data for table `courts`
-- --------------------------------------------------------

INSERT INTO `courts` (`id`, `court_number`, `court_type`, `status`, `description`, `capacity`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Court 1', 'standard', 'available', 'A well-maintained court for various activities.', 10, 1, 1, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(2, 'Court 2', 'standard', 'occupied', 'A well-maintained court for various activities.', 10, 1, 2, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(3, 'Court 3', 'vip', 'available', 'A well-maintained court for various activities.', 8, 1, 3, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(4, 'Court 4', 'premium', 'available', 'A well-maintained court for various activities.', 12, 1, 4, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(5, 'Court 5', 'standard', 'available', 'A well-maintained court for various activities.', 10, 1, 5, '2026-06-02 00:00:00', '2026-06-02 00:00:00');

-- --------------------------------------------------------
-- Dumping data for table `players`
-- --------------------------------------------------------

INSERT INTO `players` (`id`, `player_name`, `player_code`, `skill_level`, `created_at`, `updated_at`) VALUES
(1, 'John Smith', 'P-ABC123', 'intermediate', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(2, 'Jane Doe', 'P-DEF456', 'intermediate', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(3, 'Mike Johnson', 'P-GHI789', 'intermediate', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(4, 'Sarah Williams', 'P-JKL012', 'intermediate', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(5, 'David Brown', 'P-MNO345', 'intermediate', '2026-06-02 00:00:00', '2026-06-02 00:00:00');

-- --------------------------------------------------------
-- Dumping data for table `queues`
-- --------------------------------------------------------

INSERT INTO `queues` (`id`, `court_id`, `player_id`, `queue_number`, `player_name`, `contact_number`, `party_size`, `status`, `priority`, `called_at`, `served_at`, `completed_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'Q-20260602-0001', 'John Smith', '0917-123-4567', 2, 'serving', 0, '2026-06-02 00:00:00', '2026-06-02 00:00:00', NULL, NULL, NULL, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(2, 2, NULL, 'Q-20260602-0002', 'Jane Doe', '0918-234-5678', 4, 'waiting', 0, NULL, NULL, NULL, NULL, NULL, '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(3, 2, NULL, 'Q-20260602-0003', 'Mike Johnson', '0919-345-6789', 2, 'waiting', 5, NULL, NULL, NULL, NULL, NULL, '2026-06-02 00:00:00', '2026-06-02 00:00:00');

-- --------------------------------------------------------
-- Dumping data for table `settings`
-- --------------------------------------------------------

INSERT INTO `settings` (`id`, `group`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
(1, 'general', 'system_name', 'Court Queue System', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(2, 'general', 'organization_name', 'City Sports Complex', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(3, 'general', 'contact_email', 'info@sportscomplex.com', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(4, 'general', 'contact_phone', '(02) 8123-4567', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(5, 'display', 'display_refresh_interval', '10', 'integer', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(6, 'display', 'show_waiting_count', '1', 'boolean', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(7, 'display', 'display_theme', 'dark', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(13, 'display', 'rental_default_minutes', '60', 'integer', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(14, 'display', 'rental_extend_minutes', '30', 'integer', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(8, 'queue', 'queue_prefix', 'Q', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(9, 'queue', 'reset_queue_daily', '1', 'boolean', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(10, 'queue', 'default_party_size', '1', 'integer', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(11, 'notifications', 'enable_sound', '1', 'boolean', '2026-06-02 00:00:00', '2026-06-02 00:00:00'),
(12, 'notifications', 'call_message', 'Queue {number} please proceed to {court}', 'string', '2026-06-02 00:00:00', '2026-06-02 00:00:00');

-- --------------------------------------------------------
-- Auto_INCREMENT for tables
-- --------------------------------------------------------

ALTER TABLE `users` AUTO_INCREMENT = 4;
ALTER TABLE `courts` AUTO_INCREMENT = 6;
ALTER TABLE `players` AUTO_INCREMENT = 6;
ALTER TABLE `queues` AUTO_INCREMENT = 4;
ALTER TABLE `settings` AUTO_INCREMENT = 15;

COMMIT;
