-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 11:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ivatan`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'user', 'User logged in', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/141.0.0.0 Safari\\/537.36\",\"method\":\"login\"}', NULL, '2025-10-16 00:35:27', '2025-10-16 00:35:27'),
(2, 'user', 'User logged in', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/142.0.0.0 Safari\\/537.36\",\"method\":\"login\"}', NULL, '2025-11-20 01:15:55', '2025-11-20 01:15:55'),
(3, 'user', 'User logged in', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/142.0.0.0 Safari\\/537.36\",\"method\":\"login\"}', NULL, '2025-11-21 01:41:42', '2025-11-21 01:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(120) NOT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `payload`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'verify', 'user', 191, '\"{\\\"user_snapshot\\\":{\\\"id\\\":191,\\\"uuid\\\":\\\"bdb917bc-ccdf-465d-8dc3-6e20a6268dad\\\",\\\"name\\\":\\\"Dr. Ambrose Rodriguez PhD\\\",\\\"email\\\":\\\"june.stark@example.com\\\",\\\"phone\\\":\\\"03719881549\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Et voluptatem impedit in nesciunt possimus adipisci ducimus quidem corporis rerum unde ad eum.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":1,\\\"last_login_at\\\":\\\"2025-08-28 05:36:51\\\",\\\"followers_count\\\":889,\\\"following_count\\\":60,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":false,\\\\\\\"language\\\\\\\":\\\\\\\"ur\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":190,\\\"created_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-10T14:48:46.000000Z\\\",\\\"deleted_at\\\":null},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-10 09:18:46', '2025-09-10 09:18:46'),
(2, 1, 'unverify', 'user', 191, '\"{\\\"user_snapshot\\\":{\\\"id\\\":191,\\\"uuid\\\":\\\"bdb917bc-ccdf-465d-8dc3-6e20a6268dad\\\",\\\"name\\\":\\\"Dr. Ambrose Rodriguez PhD\\\",\\\"email\\\":\\\"june.stark@example.com\\\",\\\"phone\\\":\\\"03719881549\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Et voluptatem impedit in nesciunt possimus adipisci ducimus quidem corporis rerum unde ad eum.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":0,\\\"last_login_at\\\":\\\"2025-08-28 05:36:51\\\",\\\"followers_count\\\":889,\\\"following_count\\\":60,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":false,\\\\\\\"language\\\\\\\":\\\\\\\"ur\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":190,\\\"created_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-10T14:48:50.000000Z\\\",\\\"deleted_at\\\":null},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-10 09:18:50', '2025-09-10 09:18:50'),
(3, 1, 'unverify', 'user', 192, '\"{\\\"user_snapshot\\\":{\\\"id\\\":192,\\\"uuid\\\":\\\"1f2ec020-db36-4230-a611-0e09e0fc6089\\\",\\\"name\\\":\\\"Yasmin Stehr\\\",\\\"email\\\":\\\"brycen.schmitt@example.net\\\",\\\"is_seller\\\":0,\\\"phone\\\":\\\"03107576807\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Natus et quia mollitia ipsa ut numquam consequuntur quia in.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":0,\\\"last_login_at\\\":\\\"2025-08-22 08:45:20\\\",\\\"followers_count\\\":858,\\\"following_count\\\":324,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":true,\\\\\\\"language\\\\\\\":\\\\\\\"en\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":112,\\\"created_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-11T06:41:54.000000Z\\\",\\\"deleted_at\\\":null},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-11 01:11:54', '2025-09-11 01:11:54'),
(4, 1, 'verify', 'user', 192, '\"{\\\"user_snapshot\\\":{\\\"id\\\":192,\\\"uuid\\\":\\\"1f2ec020-db36-4230-a611-0e09e0fc6089\\\",\\\"name\\\":\\\"Yasmin Stehr\\\",\\\"email\\\":\\\"brycen.schmitt@example.net\\\",\\\"is_seller\\\":0,\\\"phone\\\":\\\"03107576807\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Natus et quia mollitia ipsa ut numquam consequuntur quia in.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":1,\\\"last_login_at\\\":\\\"2025-08-22 08:45:20\\\",\\\"followers_count\\\":858,\\\"following_count\\\":324,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":true,\\\\\\\"language\\\\\\\":\\\\\\\"en\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":112,\\\"created_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-11T06:41:59.000000Z\\\",\\\"deleted_at\\\":null},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-11 01:11:59', '2025-09-11 01:11:59'),
(5, 1, 'toggle_seller_status', 'user', 192, '\"{\\\"user_snapshot\\\":{\\\"id\\\":192,\\\"uuid\\\":\\\"1f2ec020-db36-4230-a611-0e09e0fc6089\\\",\\\"name\\\":\\\"Yasmin Stehr\\\",\\\"email\\\":\\\"brycen.schmitt@example.net\\\",\\\"is_seller\\\":true,\\\"phone\\\":\\\"03107576807\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Natus et quia mollitia ipsa ut numquam consequuntur quia in.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":1,\\\"last_login_at\\\":\\\"2025-08-22 08:45:20\\\",\\\"followers_count\\\":858,\\\"following_count\\\":324,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":true,\\\\\\\"language\\\\\\\":\\\\\\\"en\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":112,\\\"created_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-11T06:42:45.000000Z\\\",\\\"deleted_at\\\":null},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-11 01:12:45', '2025-09-11 01:12:45'),
(6, 1, 'toggle_seller_status', 'user', 192, '\"{\\\"user_snapshot\\\":{\\\"id\\\":192,\\\"uuid\\\":\\\"1f2ec020-db36-4230-a611-0e09e0fc6089\\\",\\\"name\\\":\\\"Yasmin Stehr\\\",\\\"email\\\":\\\"brycen.schmitt@example.net\\\",\\\"is_seller\\\":false,\\\"phone\\\":\\\"03107576807\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Natus et quia mollitia ipsa ut numquam consequuntur quia in.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":1,\\\"last_login_at\\\":\\\"2025-08-22 08:45:20\\\",\\\"followers_count\\\":858,\\\"following_count\\\":324,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":true,\\\\\\\"language\\\\\\\":\\\\\\\"en\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":112,\\\"created_at\\\":\\\"2025-09-10T14:36:43.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-11T06:42:59.000000Z\\\",\\\"deleted_at\\\":null},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-11 01:12:59', '2025-09-11 01:12:59'),
(7, 1, 'toggle_Employer_status', 'user', 3, '\"{\\\"user_snapshot\\\":{\\\"id\\\":3,\\\"uuid\\\":\\\"399d80fa-4f19-4fe8-834f-24aae429ac81\\\",\\\"name\\\":\\\"Vern Hammes\\\",\\\"email\\\":\\\"larue75@example.net\\\",\\\"is_seller\\\":1,\\\"phone\\\":\\\"03692199176\\\",\\\"email_verified_at\\\":\\\"2025-09-10T14:36:41.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Laboriosam commodi quod vel cupiditate non assumenda ut tempore fugit.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":1,\\\"last_login_at\\\":\\\"2025-08-11 11:03:15\\\",\\\"followers_count\\\":997,\\\"following_count\\\":436,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":true,\\\\\\\"language\\\\\\\":\\\\\\\"en\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":85,\\\"created_at\\\":\\\"2025-09-10T14:36:41.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-15T10:55:04.000000Z\\\",\\\"deleted_at\\\":null,\\\"is_employer\\\":true},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-15 05:25:04', '2025-09-15 05:25:04'),
(8, 1, 'unblock', 'user', 11, '\"{\\\"user_snapshot\\\":{\\\"id\\\":11,\\\"uuid\\\":\\\"bf3509f6-742e-41d9-807d-8ef09e8f1470\\\",\\\"name\\\":\\\"Ms. Josianne O\'Hara\\\",\\\"email\\\":\\\"imclaughlin@example.net\\\",\\\"is_seller\\\":0,\\\"phone\\\":null,\\\"email_verified_at\\\":\\\"2025-09-10T14:36:41.000000Z\\\",\\\"profile_photo_path\\\":null,\\\"bio\\\":\\\"Magnam illo qui aspernatur quia perferendis soluta iste qui ullam porro.\\\",\\\"status\\\":\\\"active\\\",\\\"is_blocked\\\":0,\\\"is_verified\\\":1,\\\"last_login_at\\\":\\\"2025-09-08 19:07:41\\\",\\\"followers_count\\\":125,\\\"following_count\\\":313,\\\"settings\\\":\\\"{\\\\\\\"dark_mode\\\\\\\":true,\\\\\\\"language\\\\\\\":\\\\\\\"en\\\\\\\",\\\\\\\"notifications\\\\\\\":true}\\\",\\\"posts_count\\\":179,\\\"created_at\\\":\\\"2025-09-10T14:36:41.000000Z\\\",\\\"updated_at\\\":\\\"2025-09-19T14:09:34.000000Z\\\",\\\"deleted_at\\\":null,\\\"is_employer\\\":0},\\\"note\\\":null}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-19 08:39:34', '2025-09-19 08:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ad_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `media_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`media_ids`)),
  `status` enum('draft','pending_admin_approval','awaiting_payment','pending','approved','rejected','live','expired') NOT NULL DEFAULT 'draft',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `impressions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `user_id`, `ad_package_id`, `title`, `description`, `media_ids`, `status`, `start_at`, `end_at`, `impressions`, `created_at`, `updated_at`) VALUES
(1, 11, 1, 'First Add', 'My first add', NULL, 'awaiting_payment', NULL, NULL, 0, '2025-09-18 02:06:28', '2025-09-18 02:08:19'),
(10, 211, 1, 'My New Ad', 'My New Ad description', '[27,28]', 'pending_admin_approval', '2025-10-25 00:00:00', NULL, 0, '2025-10-24 05:37:38', '2025-10-24 05:37:38'),
(11, 211, 1, 'My New Ad 2', 'My New Ad description 2', '[29,30]', 'live', '2025-10-24 12:04:38', '2025-11-03 12:04:38', 1, '2025-10-24 05:47:25', '2025-10-24 07:28:47');

-- --------------------------------------------------------

--
-- Table structure for table `ad_impressions`
--

CREATE TABLE `ad_impressions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ad_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ad_impressions`
--

INSERT INTO `ad_impressions` (`id`, `ad_id`, `user_id`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 11, 211, '127.0.0.1', '2025-10-24 07:28:47', '2025-10-24 07:28:47');

-- --------------------------------------------------------

--
-- Table structure for table `ad_interest`
--

CREATE TABLE `ad_interest` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ad_id` bigint(20) UNSIGNED NOT NULL,
  `interest_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_packages`
--

CREATE TABLE `ad_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `reach_limit` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `duration_days` int(10) UNSIGNED NOT NULL DEFAULT 7,
  `targeting` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`targeting`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ad_packages`
--

INSERT INTO `ad_packages` (`id`, `name`, `description`, `price`, `currency`, `reach_limit`, `duration_days`, `targeting`, `created_at`, `updated_at`) VALUES
(1, 'Bronze', 'Add Pakge', 120.00, 'INR', 123, 10, '{\"location\":\"India\"}', '2025-09-18 01:39:42', '2025-09-18 04:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `ad_payments`
--

CREATE TABLE `ad_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ad_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ad_payments`
--

INSERT INTO `ad_payments` (`id`, `ad_id`, `user_id`, `amount`, `currency`, `status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `meta`, `created_at`, `updated_at`) VALUES
(1, 1, 11, 120.00, 'INR', 'pending', 'order_RIz7RxyibgRViO', NULL, NULL, NULL, '2025-09-18 02:08:19', '2025-09-18 02:08:21'),
(2, 11, 211, 120.00, 'INR', 'success', 'order_RXIdWw2m4UVW5e', 'pay_RXIdXxYZ123', 'c08da78b81dc39a6502c2d58fd0dc81d223d5f183810409094c1d5c833883921', NULL, '2025-10-24 06:19:53', '2025-10-24 06:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `commentable_type` varchar(255) NOT NULL,
  `commentable_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` text NOT NULL,
  `status` enum('active','deleted','flagged') NOT NULL DEFAULT 'active',
  `like_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `commentable_type`, `commentable_id`, `parent_id`, `body`, `status`, `like_count`, `created_at`, `updated_at`) VALUES
(8, 211, 'post', 5, NULL, 'Hi Testing', 'active', 0, '2025-10-18 00:36:18', '2025-10-18 00:36:18'),
(9, 211, 'userpost', 5, NULL, 'Hi Testing', 'active', 0, '2025-10-18 00:36:28', '2025-10-18 00:36:28'),
(10, 211, 'UserPost', 5, NULL, 'Hi Testing', 'active', 1, '2025-10-18 00:36:42', '2025-10-18 02:18:44'),
(11, 211, 'post', 5, NULL, 'Hi Testing 2', 'active', 0, '2025-10-18 01:37:09', '2025-10-18 01:37:09'),
(12, 211, 'Post', 5, NULL, 'Hi Testing 2', 'active', 0, '2025-10-18 01:37:48', '2025-10-18 01:37:48'),
(18, 211, 'UserPost', 5, 10, 'Nested 2 Hi Testing 2', 'active', 0, '2025-10-18 02:19:41', '2025-10-18 02:19:41');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `follower_id` bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `follower_id`, `following_id`, `created_at`, `updated_at`) VALUES
(2, 211, 10, '2025-10-17 00:59:18', '2025-10-17 00:59:18'),
(4, 211, 11, NULL, NULL),
(5, 11, 211, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `highlight_story`
--

CREATE TABLE `highlight_story` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `highlight_id` bigint(20) UNSIGNED NOT NULL,
  `story_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `highlight_story`
--

INSERT INTO `highlight_story` (`id`, `highlight_id`, `story_id`, `created_at`, `updated_at`) VALUES
(2, 3, 7, '2025-10-22 00:20:50', '2025-10-22 00:20:50');

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE `interests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `interest_category_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`id`, `name`, `description`, `created_at`, `updated_at`, `interest_category_id`) VALUES
(5, 'Web Development', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 2),
(6, 'Software Engineering', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 2),
(7, 'AI / Machine Learning', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 2),
(8, 'Mobile Apps', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 2),
(9, 'Cybersecurity', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 2),
(10, 'Gaming / eSports', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 2),
(11, 'Startups & Entrepreneurship', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 3),
(12, 'Investing & Stock Market', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 3),
(13, 'Personal Finance', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 3),
(14, 'Real Estate', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 3),
(15, 'Marketing & Sales', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 3),
(16, 'Crypto / Blockchain', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 3),
(17, 'Quick Hiring / Part-time', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 4),
(18, 'Freelancing / Remote Work', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 4),
(19, 'Corporate Jobs', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 4),
(20, 'Government Jobs', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 4),
(21, 'Skill Development', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 4),
(22, 'Coding / IT', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 5),
(23, 'Business & Management', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 5),
(24, 'Language Learning', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 5),
(25, 'Competitive Exams', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 5),
(26, 'Science & Research', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 5),
(27, 'Movies & Series', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 6),
(28, 'Music', NULL, '2025-11-21 01:45:32', '2025-11-21 01:45:32', 6);

-- --------------------------------------------------------

--
-- Table structure for table `interest_categories`
--

CREATE TABLE `interest_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interest_categories`
--

INSERT INTO `interest_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'IT', '2025-11-21 01:42:26', '2025-11-21 01:42:26'),
(2, 'Technology', '2025-11-21 01:45:32', '2025-11-21 01:45:32'),
(3, 'Business & Finance', '2025-11-21 01:45:32', '2025-11-21 01:45:32'),
(4, 'Jobs & Careers', '2025-11-21 01:45:32', '2025-11-21 01:45:32'),
(5, 'Education & Learning', '2025-11-21 01:45:32', '2025-11-21 01:45:32'),
(6, 'Entertainment & Lifestyle', '2025-11-21 01:45:32', '2025-11-21 01:45:32');

-- --------------------------------------------------------

--
-- Table structure for table `interest_user`
--

CREATE TABLE `interest_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `interest_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(31, 'database', '{\"uuid\":\"10b68b3b-57c8-40e5-a268-7ba7e17e8844\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:4:\\\"test\\\";s:7:\\\"payload\\\";a:1:{s:7:\\\"message\\\";s:11:\\\"Hello demo!\\\";}s:2:\\\"id\\\";s:36:\\\"e8f750f0-ffd9-4796-9a1e-caf9769863fc\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029025,\"delay\":null}', 0, NULL, 1758029025, 1758029025),
(32, 'database', '{\"uuid\":\"613deac9-67e2-4488-8d50-c86b629355a1\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:4:\\\"test\\\";s:7:\\\"payload\\\";a:1:{s:7:\\\"message\\\";s:11:\\\"Hello demo!\\\";}s:2:\\\"id\\\";s:36:\\\"e8f750f0-ffd9-4796-9a1e-caf9769863fc\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029025,\"delay\":null}', 0, NULL, 1758029025, 1758029025),
(33, 'database', '{\"uuid\":\"a6544acc-4b6c-4822-baf9-2ff5e2fe24f6\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:13;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 1\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"5bbb3be7-cd4b-478e-90c2-7aad156d0b5c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029752,\"delay\":null}', 0, NULL, 1758029752, 1758029752),
(34, 'database', '{\"uuid\":\"059b4036-09ab-4e56-a62e-056fd6317399\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:13;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 1\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"5bbb3be7-cd4b-478e-90c2-7aad156d0b5c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029752,\"delay\":null}', 0, NULL, 1758029752, 1758029752),
(37, 'database', '{\"uuid\":\"8c905e17-dce3-4442-91f5-9681196565f2\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:14;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"e457b629-a3cc-4d1a-8cc0-529f2bd51894\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029803,\"delay\":null}', 0, NULL, 1758029803, 1758029803),
(38, 'database', '{\"uuid\":\"71f397a1-f633-4860-89ba-0c5c14165747\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:14;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"e457b629-a3cc-4d1a-8cc0-529f2bd51894\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029803,\"delay\":null}', 0, NULL, 1758029803, 1758029803),
(41, 'database', '{\"uuid\":\"87d9777d-61e4-486c-ae25-c8f992c33310\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:15;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"926e89a8-4626-4e56-8103-8c7adcb7d113\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029824,\"delay\":null}', 0, NULL, 1758029824, 1758029824),
(42, 'database', '{\"uuid\":\"af99cdf9-7d40-4bcd-883d-f693d3243363\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:15;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"926e89a8-4626-4e56-8103-8c7adcb7d113\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029824,\"delay\":null}', 0, NULL, 1758029824, 1758029824),
(45, 'database', '{\"uuid\":\"5c8c0905-791b-42d3-a65b-b906bc617b40\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:16;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b4cd1aff-141e-42b2-9537-814816aa9841\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029832,\"delay\":null}', 0, NULL, 1758029832, 1758029832),
(46, 'database', '{\"uuid\":\"3c5a0fdc-6db8-4298-9dd9-23cb64a2536a\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:16;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b4cd1aff-141e-42b2-9537-814816aa9841\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029832,\"delay\":null}', 0, NULL, 1758029832, 1758029832),
(49, 'database', '{\"uuid\":\"e169b55c-42db-4010-b9ae-8bce52adb629\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:17;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"7f3ca4ae-9811-4c56-bb70-604ef719b172\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029852,\"delay\":null}', 0, NULL, 1758029852, 1758029852),
(50, 'database', '{\"uuid\":\"e95bb93c-70d5-4dfd-b295-5f63e3020905\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:17;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"7f3ca4ae-9811-4c56-bb70-604ef719b172\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758029852,\"delay\":null}', 0, NULL, 1758029852, 1758029852),
(57, 'database', '{\"uuid\":\"38c44ee9-326f-4f31-b59a-1f4d2817413d\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:20;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"ca16e5aa-c628-4323-ad79-969573f77566\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030065,\"delay\":null}', 0, NULL, 1758030065, 1758030065),
(58, 'database', '{\"uuid\":\"9b971def-ea4f-4489-9fc1-1a8c6162c716\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:20;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"ca16e5aa-c628-4323-ad79-969573f77566\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030065,\"delay\":null}', 0, NULL, 1758030065, 1758030065),
(63, 'database', '{\"uuid\":\"6005b887-8367-4521-97cd-23d037ed3935\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:21;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"cd29b46d-4344-46d8-b234-62f57839a04e\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030077,\"delay\":null}', 0, NULL, 1758030077, 1758030077),
(64, 'database', '{\"uuid\":\"f7aa8201-6899-49f5-a749-46c5439c1204\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:21;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"cd29b46d-4344-46d8-b234-62f57839a04e\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030077,\"delay\":null}', 0, NULL, 1758030077, 1758030077),
(67, 'database', '{\"uuid\":\"6a65c259-72d9-42f0-90c2-588d129fb7a3\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:22;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b0ef1550-7583-4906-914b-009767a47385\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030079,\"delay\":null}', 0, NULL, 1758030079, 1758030079),
(68, 'database', '{\"uuid\":\"c06ec25e-d6a3-4d40-a6ea-2d828633f288\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:22;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b0ef1550-7583-4906-914b-009767a47385\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030079,\"delay\":null}', 0, NULL, 1758030079, 1758030079),
(73, 'database', '{\"uuid\":\"b19e3fc1-f75e-4e35-8413-9d5908e9c38f\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:23;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0d19f2fc-ec77-4c08-a4ea-68df84bde643\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030089,\"delay\":null}', 0, NULL, 1758030089, 1758030089),
(74, 'database', '{\"uuid\":\"1a67e3a1-9bc3-4fc5-9615-41bf00658a8c\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:23;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:10:\\\"Hey Dear 2\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0d19f2fc-ec77-4c08-a4ea-68df84bde643\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030089,\"delay\":null}', 0, NULL, 1758030089, 1758030089),
(79, 'database', '{\"uuid\":\"b5016eb0-42f2-4450-bbd3-0e5859da3ae2\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:24;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0db6be5f-dcbc-4462-8c5b-dffe6906c219\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030102,\"delay\":null}', 0, NULL, 1758030102, 1758030102),
(80, 'database', '{\"uuid\":\"fb6d796b-ce1d-4d7e-8f39-5da4d6dc67af\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:24;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0db6be5f-dcbc-4462-8c5b-dffe6906c219\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030102,\"delay\":null}', 0, NULL, 1758030102, 1758030102),
(85, 'database', '{\"uuid\":\"a92f2c01-fe0e-41bf-a5b9-d41ba4092aa9\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:25;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"3fcb5be7-e72b-412d-907e-46101bc0481c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030125,\"delay\":null}', 0, NULL, 1758030125, 1758030125),
(86, 'database', '{\"uuid\":\"312b1460-60f0-4b28-acf8-e6fde6cd5911\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:25;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"3fcb5be7-e72b-412d-907e-46101bc0481c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030125,\"delay\":null}', 0, NULL, 1758030125, 1758030125),
(89, 'database', '{\"uuid\":\"35fea0f6-b408-4618-901e-eaf72ca92ca2\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:26;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"342db335-1150-4d29-92be-10c68c420794\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030129,\"delay\":null}', 0, NULL, 1758030129, 1758030129),
(90, 'database', '{\"uuid\":\"020c4932-8155-4df1-add8-2a34019f65f3\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:26;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"342db335-1150-4d29-92be-10c68c420794\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030129,\"delay\":null}', 0, NULL, 1758030129, 1758030129),
(93, 'database', '{\"uuid\":\"c461dc6d-29b9-4f1d-9640-13a75a179e91\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:27;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"877fd51e-fb4e-402a-9d11-c769681a9241\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030135,\"delay\":null}', 0, NULL, 1758030135, 1758030135),
(94, 'database', '{\"uuid\":\"e759ebaf-e77a-4f65-a62b-bd25948ac8bb\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:27;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"877fd51e-fb4e-402a-9d11-c769681a9241\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030135,\"delay\":null}', 0, NULL, 1758030135, 1758030135),
(97, 'database', '{\"uuid\":\"38fbb019-5587-47f3-af27-00ecdbd082d8\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:28;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"ba6ce015-c34d-4ee5-b214-9171aaaa95b5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030212,\"delay\":null}', 0, NULL, 1758030212, 1758030212),
(98, 'database', '{\"uuid\":\"32b98149-aa8b-4f81-9fe7-b017e0933954\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:28;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"ba6ce015-c34d-4ee5-b214-9171aaaa95b5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030212,\"delay\":null}', 0, NULL, 1758030212, 1758030212),
(101, 'database', '{\"uuid\":\"6ba84b5d-4d84-4e88-827f-72655924dcb6\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:29;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"757fd59f-e548-4766-ba5c-5fadac7daa3c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030451,\"delay\":null}', 0, NULL, 1758030451, 1758030451),
(102, 'database', '{\"uuid\":\"36decbbe-d9af-4e84-b82b-9666232a854e\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:29;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"757fd59f-e548-4766-ba5c-5fadac7daa3c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030451,\"delay\":null}', 0, NULL, 1758030451, 1758030451),
(105, 'database', '{\"uuid\":\"79048e92-db48-4185-bd02-1489fdce8da2\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:30;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a85b2f02-6ec8-4806-99d9-6f0115ec96bf\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030599,\"delay\":null}', 0, NULL, 1758030599, 1758030599),
(106, 'database', '{\"uuid\":\"914e6cbb-0894-4870-96e4-b9a52a573aa9\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:30;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a85b2f02-6ec8-4806-99d9-6f0115ec96bf\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030599,\"delay\":null}', 0, NULL, 1758030599, 1758030599),
(109, 'database', '{\"uuid\":\"2e3fef38-f6c7-4c3c-b51e-2c7dda1661a7\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:31;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"91100992-06b3-4f0a-a8d4-7533b10a7365\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030627,\"delay\":null}', 0, NULL, 1758030627, 1758030627);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(110, 'database', '{\"uuid\":\"de3ae4a9-0c14-4b42-a93d-3a3607a7c285\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:31;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"91100992-06b3-4f0a-a8d4-7533b10a7365\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030627,\"delay\":null}', 0, NULL, 1758030627, 1758030627),
(113, 'database', '{\"uuid\":\"1190330a-86c1-4eab-92a0-54bf334327fc\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:32;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"e9f033c1-d8a9-441d-ba82-3132a0f27048\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030635,\"delay\":null}', 0, NULL, 1758030635, 1758030635),
(114, 'database', '{\"uuid\":\"5969324b-8dc8-400b-a622-76dcffee7fab\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:32;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"e9f033c1-d8a9-441d-ba82-3132a0f27048\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030635,\"delay\":null}', 0, NULL, 1758030635, 1758030635),
(117, 'database', '{\"uuid\":\"98c790ec-3444-4e09-9397-f52ee3faeef0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:33;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"87584934-40ca-4357-9dc4-ecd68cfceadd\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030847,\"delay\":null}', 0, NULL, 1758030847, 1758030847),
(118, 'database', '{\"uuid\":\"306b20c2-24e7-4577-b9aa-6dca193d2cc0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:33;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"87584934-40ca-4357-9dc4-ecd68cfceadd\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030847,\"delay\":null}', 0, NULL, 1758030847, 1758030847),
(120, 'database', '{\"uuid\":\"e40d3a2c-5285-4122-aecc-97fd8175695b\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:33;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"1afd31c8-b237-4937-8a27-2ee949c68723\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030847,\"delay\":null}', 0, NULL, 1758030847, 1758030847),
(121, 'database', '{\"uuid\":\"3e170464-c9d8-4ab1-8f6e-10f68dde6046\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:33;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"1afd31c8-b237-4937-8a27-2ee949c68723\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758030847,\"delay\":null}', 0, NULL, 1758030847, 1758030847),
(123, 'database', '{\"uuid\":\"c8f29588-8c7c-4a2d-b55c-d4c9638386d7\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:34;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"7aa06a46-29ec-4294-92bf-bfcfdd59edc0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758085593,\"delay\":null}', 0, NULL, 1758085593, 1758085593),
(124, 'database', '{\"uuid\":\"ae7c1bc0-14ff-4f84-ab7f-e081741cc6cd\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:34;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"7aa06a46-29ec-4294-92bf-bfcfdd59edc0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758085593,\"delay\":null}', 0, NULL, 1758085593, 1758085593),
(126, 'database', '{\"uuid\":\"7dd40d67-bda6-4061-8798-ffa432e360a3\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:34;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"b9322039-78ea-4d47-aaca-e7a7d01d0305\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758085593,\"delay\":null}', 0, NULL, 1758085593, 1758085593),
(127, 'database', '{\"uuid\":\"f0455605-c094-426c-8eff-79fd67914ba2\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:34;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"b9322039-78ea-4d47-aaca-e7a7d01d0305\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758085593,\"delay\":null}', 0, NULL, 1758085593, 1758085593),
(129, 'database', '{\"uuid\":\"945cd2f0-f773-4b64-bec5-e27f8b77f0f6\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:35;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"da76e366-d7b2-4d51-ab1e-ebc9273a6fbc\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088669,\"delay\":null}', 0, NULL, 1758088669, 1758088669),
(130, 'database', '{\"uuid\":\"ecc0c0bb-62fd-4abb-8c2a-fee656cee445\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:35;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"da76e366-d7b2-4d51-ab1e-ebc9273a6fbc\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088669,\"delay\":null}', 0, NULL, 1758088669, 1758088669),
(132, 'database', '{\"uuid\":\"8de561c9-0fa1-486a-86c6-19bdbe6a27e3\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:35;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"16852b88-db3a-40a8-a825-87fc13aee8b4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088669,\"delay\":null}', 0, NULL, 1758088669, 1758088669),
(133, 'database', '{\"uuid\":\"013a2183-bf6f-45f8-94f5-9c97118a67b4\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:35;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"16852b88-db3a-40a8-a825-87fc13aee8b4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088669,\"delay\":null}', 0, NULL, 1758088669, 1758088669),
(135, 'database', '{\"uuid\":\"2c7f8f43-9ad1-44b0-8ae4-faeb562548fc\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:36;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"c0e81711-0940-4731-be0e-93cc3b265a15\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088729,\"delay\":null}', 0, NULL, 1758088729, 1758088729),
(136, 'database', '{\"uuid\":\"45afd210-b1cb-4b25-ba3b-145610424228\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:36;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"c0e81711-0940-4731-be0e-93cc3b265a15\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088729,\"delay\":null}', 0, NULL, 1758088729, 1758088729),
(138, 'database', '{\"uuid\":\"4a03e523-3089-402b-b252-52e651da2155\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:36;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"b4ed1e9e-a5fe-4521-ab56-f9d1ce132da6\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088729,\"delay\":null}', 0, NULL, 1758088729, 1758088729),
(139, 'database', '{\"uuid\":\"1ee132d2-efae-40a2-a149-eee6f557e664\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:36;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"b4ed1e9e-a5fe-4521-ab56-f9d1ce132da6\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758088729,\"delay\":null}', 0, NULL, 1758088729, 1758088729),
(141, 'database', '{\"uuid\":\"c95ff900-8de3-4c88-b8cc-6bfc55c25127\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:37;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"eaf1fe73-7671-40ff-a194-7ca1903a2fa3\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089035,\"delay\":null}', 0, NULL, 1758089035, 1758089035),
(142, 'database', '{\"uuid\":\"767d0f3d-e716-43fc-ab30-83c2404bce60\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:37;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"eaf1fe73-7671-40ff-a194-7ca1903a2fa3\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089035,\"delay\":null}', 0, NULL, 1758089035, 1758089035),
(144, 'database', '{\"uuid\":\"9cf82b07-3410-458d-894b-d41f4cc6f7d7\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:37;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"b1e7443b-15ff-41d4-a972-0e5223614bb3\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089035,\"delay\":null}', 0, NULL, 1758089035, 1758089035),
(145, 'database', '{\"uuid\":\"29f1248e-e236-4a71-977c-7eb1be10bac6\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:37;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"b1e7443b-15ff-41d4-a972-0e5223614bb3\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089035,\"delay\":null}', 0, NULL, 1758089035, 1758089035),
(159, 'database', '{\"uuid\":\"87bbd6a9-fdf8-4410-bc38-2caa68cfbcb3\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:38;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"f5f1f5af-b930-46ef-b654-6a489da243e9\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089209,\"delay\":null}', 0, NULL, 1758089209, 1758089209),
(160, 'database', '{\"uuid\":\"858bca74-f798-4ef5-b958-aee8a043d0cf\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:38;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"f5f1f5af-b930-46ef-b654-6a489da243e9\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089209,\"delay\":null}', 0, NULL, 1758089209, 1758089209),
(162, 'database', '{\"uuid\":\"bb39fad9-4eba-4694-8a66-609353f9ca47\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:38;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"2e38da73-76aa-4f2c-a4be-75ea1e8b1635\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089209,\"delay\":null}', 0, NULL, 1758089209, 1758089209),
(163, 'database', '{\"uuid\":\"83555d4f-a53f-49e1-9242-36af25f45418\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:38;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"2e38da73-76aa-4f2c-a4be-75ea1e8b1635\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089209,\"delay\":null}', 0, NULL, 1758089209, 1758089209),
(165, 'database', '{\"uuid\":\"7013ff6e-6afd-42a9-b756-b2f36b9981f4\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:39;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0e7d8bf6-8639-4c6b-8542-b78eb0c3790e\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089249,\"delay\":null}', 0, NULL, 1758089249, 1758089249),
(166, 'database', '{\"uuid\":\"135f492b-b3d1-4cff-9995-e03fbd1b9814\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:39;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0e7d8bf6-8639-4c6b-8542-b78eb0c3790e\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089249,\"delay\":null}', 0, NULL, 1758089249, 1758089249),
(168, 'database', '{\"uuid\":\"82337684-d8a5-4ed8-ba91-16fbb0513bf5\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:39;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"9f1bc806-9d33-45b2-b7cc-26d84d6ae1d0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089249,\"delay\":null}', 0, NULL, 1758089249, 1758089249),
(169, 'database', '{\"uuid\":\"af1ca5cb-684f-4a81-8571-2dea866c32ad\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:39;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"9f1bc806-9d33-45b2-b7cc-26d84d6ae1d0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089249,\"delay\":null}', 0, NULL, 1758089249, 1758089249),
(171, 'database', '{\"uuid\":\"51417f5d-1e67-4734-b7d8-aaed37ed4812\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:40;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"3054601e-eced-4a20-a15e-ce7cb6d9df4d\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089355,\"delay\":null}', 0, NULL, 1758089355, 1758089355),
(172, 'database', '{\"uuid\":\"2c3af6e9-9e6e-4634-945f-6e17cd14ea49\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:40;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"3054601e-eced-4a20-a15e-ce7cb6d9df4d\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089355,\"delay\":null}', 0, NULL, 1758089355, 1758089355),
(174, 'database', '{\"uuid\":\"6419323d-b9ca-470a-bbb3-1256b4d4fc6d\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:40;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"c145fadf-cf73-4275-986e-db63ba8981b8\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089355,\"delay\":null}', 0, NULL, 1758089355, 1758089355),
(175, 'database', '{\"uuid\":\"1acebf2a-9e38-4deb-8281-50f3c7a4ebd0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:40;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"c145fadf-cf73-4275-986e-db63ba8981b8\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089355,\"delay\":null}', 0, NULL, 1758089355, 1758089355),
(177, 'database', '{\"uuid\":\"4c422869-1578-4029-8db7-5e4da04f6e3a\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:41;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"69dcfa86-5d25-405b-8359-71e7c08af910\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089716,\"delay\":null}', 0, NULL, 1758089716, 1758089716);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(178, 'database', '{\"uuid\":\"a3ec36d2-84d5-4d56-bd34-398f08287096\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:41;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"69dcfa86-5d25-405b-8359-71e7c08af910\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089716,\"delay\":null}', 0, NULL, 1758089716, 1758089716),
(180, 'database', '{\"uuid\":\"5f756226-1469-49f3-9f4d-d1a932a4c321\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:41;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"5e45824b-a8c9-442c-b829-835fe737304e\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089716,\"delay\":null}', 0, NULL, 1758089716, 1758089716),
(181, 'database', '{\"uuid\":\"7e012c06-96be-4d35-a58a-086f66489810\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:41;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"5e45824b-a8c9-442c-b829-835fe737304e\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089716,\"delay\":null}', 0, NULL, 1758089716, 1758089716),
(183, 'database', '{\"uuid\":\"95a3314e-0d10-49b1-bf06-fb3321a13509\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:42;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b5a6fa63-965f-4d11-b2c0-99a064a6349c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089829,\"delay\":null}', 0, NULL, 1758089829, 1758089829),
(184, 'database', '{\"uuid\":\"76d568f9-358c-4284-a894-d343600c5aeb\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:42;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b5a6fa63-965f-4d11-b2c0-99a064a6349c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089829,\"delay\":null}', 0, NULL, 1758089829, 1758089829),
(186, 'database', '{\"uuid\":\"277eab0f-79ff-4d9c-b715-e5d494f3bad0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:42;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"ddca25cf-8b20-4ffe-80d3-f0526b8be307\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089829,\"delay\":null}', 0, NULL, 1758089829, 1758089829),
(187, 'database', '{\"uuid\":\"1c0ebec5-67be-44ae-b022-5db5349c5b23\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:42;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"ddca25cf-8b20-4ffe-80d3-f0526b8be307\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089829,\"delay\":null}', 0, NULL, 1758089829, 1758089829),
(189, 'database', '{\"uuid\":\"40eb68f1-9851-4bae-8dd9-39b43a367cb5\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:43;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"be03ff29-a424-4114-87ef-1742c4f8f0d7\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089948,\"delay\":null}', 0, NULL, 1758089948, 1758089948),
(190, 'database', '{\"uuid\":\"2533b158-94bd-45c2-9cab-5f482437fdad\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:43;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"be03ff29-a424-4114-87ef-1742c4f8f0d7\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089948,\"delay\":null}', 0, NULL, 1758089948, 1758089948),
(192, 'database', '{\"uuid\":\"d4ff5ff3-415a-4b75-ab7f-0fba58a5c765\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:43;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"603882a1-33ca-404a-a892-5d3bdb1d156c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089948,\"delay\":null}', 0, NULL, 1758089948, 1758089948),
(193, 'database', '{\"uuid\":\"d2e8fb55-46ea-411e-9694-6d8b90505964\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:43;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"603882a1-33ca-404a-a892-5d3bdb1d156c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758089948,\"delay\":null}', 0, NULL, 1758089948, 1758089948),
(195, 'database', '{\"uuid\":\"e538dc5b-ac7c-447d-9705-aace476a63aa\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:44;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b63e8661-a69e-4bd2-a8bb-ead1b4f572ef\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090028,\"delay\":null}', 0, NULL, 1758090028, 1758090028),
(196, 'database', '{\"uuid\":\"c448ffd3-b2a8-42df-af18-cf7faa67ac7a\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:44;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"b63e8661-a69e-4bd2-a8bb-ead1b4f572ef\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090028,\"delay\":null}', 0, NULL, 1758090028, 1758090028),
(198, 'database', '{\"uuid\":\"375a2962-9442-4441-8a02-6db2bebbd1cc\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:44;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"38eede81-b7bd-4073-8ff5-f8883d331266\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090028,\"delay\":null}', 0, NULL, 1758090028, 1758090028),
(199, 'database', '{\"uuid\":\"8f9f992b-f92d-422b-89e1-205aeb14145d\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:44;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"38eede81-b7bd-4073-8ff5-f8883d331266\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090028,\"delay\":null}', 0, NULL, 1758090028, 1758090028),
(201, 'database', '{\"uuid\":\"b957e644-7dc4-4a90-8979-3fdb70a1c422\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:123;s:7:\\\"content\\\";s:12:\\\"Test message\\\";}s:2:\\\"id\\\";s:36:\\\"0363389e-388e-4a3d-90cd-e2ac61970fc6\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090218,\"delay\":null}', 0, NULL, 1758090218, 1758090218),
(202, 'database', '{\"uuid\":\"a797901d-e3c8-4a62-a229-69e1b0354fcc\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:123;s:7:\\\"content\\\";s:12:\\\"Test message\\\";}s:2:\\\"id\\\";s:36:\\\"0363389e-388e-4a3d-90cd-e2ac61970fc6\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090218,\"delay\":null}', 0, NULL, 1758090218, 1758090218),
(203, 'database', '{\"uuid\":\"2e1f1700-8c3e-4d65-9abd-8b94f26e8d75\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:45;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a7a3ffea-4f3e-459e-9282-fea21968de8c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090228,\"delay\":null}', 0, NULL, 1758090228, 1758090228),
(204, 'database', '{\"uuid\":\"3b2a7af8-2fb4-4b3c-9fbc-6f567df9f376\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:45;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a7a3ffea-4f3e-459e-9282-fea21968de8c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090228,\"delay\":null}', 0, NULL, 1758090228, 1758090228),
(206, 'database', '{\"uuid\":\"0ef19cfc-46b6-4d69-82cc-5416707b4b87\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:45;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"110c64fa-6c90-4d3c-a4a4-0d64e18fe9a0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090228,\"delay\":null}', 0, NULL, 1758090228, 1758090228),
(207, 'database', '{\"uuid\":\"92ecbc49-f030-44a0-930d-db7e786acea4\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:45;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"110c64fa-6c90-4d3c-a4a4-0d64e18fe9a0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090228,\"delay\":null}', 0, NULL, 1758090228, 1758090228),
(209, 'database', '{\"uuid\":\"3d771185-ff2f-4a21-b1fd-d4cb8f3f9ef5\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:46;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"bf3ac7ef-0e9a-4167-a8b2-43c99dc2af17\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090244,\"delay\":null}', 0, NULL, 1758090244, 1758090244),
(210, 'database', '{\"uuid\":\"59deef7a-a328-4968-ab23-5a2622565efb\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:46;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"bf3ac7ef-0e9a-4167-a8b2-43c99dc2af17\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090244,\"delay\":null}', 0, NULL, 1758090244, 1758090244),
(212, 'database', '{\"uuid\":\"6c391613-47a1-4746-9498-af4c8efa7d9d\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:46;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"fc76f6cf-738c-4fb2-886d-bee5774cb2c5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090244,\"delay\":null}', 0, NULL, 1758090244, 1758090244),
(213, 'database', '{\"uuid\":\"bcfd97c4-98dc-4ee4-b9cc-da9050801a31\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:46;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"fc76f6cf-738c-4fb2-886d-bee5774cb2c5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090244,\"delay\":null}', 0, NULL, 1758090244, 1758090244),
(215, 'database', '{\"uuid\":\"3a4c1680-68ec-4d9b-a012-aa4809ebe401\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:47;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a9876bd1-5d19-4b51-98c7-f3034a3b9ac5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090479,\"delay\":null}', 0, NULL, 1758090479, 1758090479),
(216, 'database', '{\"uuid\":\"ca280e4b-c613-4d12-abd7-4511549013d0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:47;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a9876bd1-5d19-4b51-98c7-f3034a3b9ac5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090479,\"delay\":null}', 0, NULL, 1758090479, 1758090479),
(218, 'database', '{\"uuid\":\"f0c3118b-2750-47a2-baef-67aa1d75a2a0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:47;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"50b6d654-e3bd-4858-b3cb-8e175db679c4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090479,\"delay\":null}', 0, NULL, 1758090479, 1758090479),
(219, 'database', '{\"uuid\":\"193768d1-2752-48ec-82df-5cd843655e17\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:47;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"50b6d654-e3bd-4858-b3cb-8e175db679c4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090479,\"delay\":null}', 0, NULL, 1758090479, 1758090479),
(221, 'database', '{\"uuid\":\"c78a6c0d-dcf1-4bb4-b68f-98c2cda4d14f\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:48;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a6e462d3-9297-4f7b-bd87-5167075abadc\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090547,\"delay\":null}', 0, NULL, 1758090547, 1758090547),
(222, 'database', '{\"uuid\":\"7fea2631-caa2-4799-86b9-41de1bee2cad\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:48;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"a6e462d3-9297-4f7b-bd87-5167075abadc\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090547,\"delay\":null}', 0, NULL, 1758090547, 1758090547),
(224, 'database', '{\"uuid\":\"18ccd4b7-aea1-460e-ac81-992dfd063211\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:48;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"5e0a52a2-25a0-474c-8388-dfd47738d531\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090547,\"delay\":null}', 0, NULL, 1758090547, 1758090547),
(225, 'database', '{\"uuid\":\"2e18ff1c-5584-4a58-b844-5897a5952b2b\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:48;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"5e0a52a2-25a0-474c-8388-dfd47738d531\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090547,\"delay\":null}', 0, NULL, 1758090547, 1758090547),
(227, 'database', '{\"uuid\":\"627e3887-835d-4c7b-98d5-85fb4072494b\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:49;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"92b479b6-4196-4404-bfc7-40f26ec36386\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090816,\"delay\":null}', 0, NULL, 1758090816, 1758090816),
(228, 'database', '{\"uuid\":\"e00feed1-9283-4258-ac2b-dd23dab68ad4\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:49;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"92b479b6-4196-4404-bfc7-40f26ec36386\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090816,\"delay\":null}', 0, NULL, 1758090816, 1758090816),
(230, 'database', '{\"uuid\":\"a0bed6d5-743b-481d-b00d-d0a5b730d097\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:49;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"ef689629-31dc-4035-9a6c-ac40ee2b8e75\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090816,\"delay\":null}', 0, NULL, 1758090816, 1758090816),
(231, 'database', '{\"uuid\":\"f7d1acd9-6507-4d9e-8bb7-48db95c456fe\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:49;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"ef689629-31dc-4035-9a6c-ac40ee2b8e75\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090816,\"delay\":null}', 0, NULL, 1758090816, 1758090816);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(233, 'database', '{\"uuid\":\"a8fec1ff-9851-444e-956d-159913e90336\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:123;s:7:\\\"content\\\";s:12:\\\"Test message\\\";}s:2:\\\"id\\\";s:36:\\\"09882cf4-0b21-446b-9689-f3d0b482b73c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090924,\"delay\":null}', 0, NULL, 1758090924, 1758090924),
(234, 'database', '{\"uuid\":\"45a46ecf-1a90-42ae-a9a3-5614cc09238a\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:123;s:7:\\\"content\\\";s:12:\\\"Test message\\\";}s:2:\\\"id\\\";s:36:\\\"09882cf4-0b21-446b-9689-f3d0b482b73c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758090924,\"delay\":null}', 0, NULL, 1758090924, 1758090924),
(235, 'database', '{\"uuid\":\"690da47d-95a4-4b42-a411-0a7293862668\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:123;s:7:\\\"content\\\";s:12:\\\"Test message\\\";}s:2:\\\"id\\\";s:36:\\\"c0251773-76f2-46c8-adb9-519d2a96e1a4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758091315,\"delay\":null}', 0, NULL, 1758091315, 1758091315),
(236, 'database', '{\"uuid\":\"52f44632-6e5f-47c3-ac91-dbfbbec9bb94\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:123;s:7:\\\"content\\\";s:12:\\\"Test message\\\";}s:2:\\\"id\\\";s:36:\\\"c0251773-76f2-46c8-adb9-519d2a96e1a4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758091315,\"delay\":null}', 0, NULL, 1758091315, 1758091315),
(237, 'database', '{\"uuid\":\"74cb2afa-afb2-4ac4-818a-ce7a55bd286b\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:50;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"6477ad9b-b730-4306-b2c5-77ab1727f4d4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758091765,\"delay\":null}', 0, NULL, 1758091765, 1758091765),
(238, 'database', '{\"uuid\":\"e6089d86-d917-4c38-b5ce-e397415d6c69\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:50;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"6477ad9b-b730-4306-b2c5-77ab1727f4d4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758091765,\"delay\":null}', 0, NULL, 1758091765, 1758091765),
(240, 'database', '{\"uuid\":\"bc7c651a-49eb-4e89-a591-1f744a42b7f0\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:50;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"f9ac92f2-89bf-4040-9b84-d6ef9299895c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758091765,\"delay\":null}', 0, NULL, 1758091765, 1758091765),
(241, 'database', '{\"uuid\":\"a7e5d7d7-b4ef-4156-bee8-128664fec002\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:50;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"f9ac92f2-89bf-4040-9b84-d6ef9299895c\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758091765,\"delay\":null}', 0, NULL, 1758091765, 1758091765),
(243, 'database', '{\"uuid\":\"f769d28f-7281-4da3-bb5d-241ef701d289\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:51;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"c583a805-1e1f-43d3-b8d8-f9a6ce170f55\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758094575,\"delay\":null}', 0, NULL, 1758094575, 1758094575),
(244, 'database', '{\"uuid\":\"2c9079c8-1af1-4a89-925d-ba70f3a42ea3\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:51;s:9:\\\"sender_id\\\";i:11;s:11:\\\"sender_name\\\";s:19:\\\"Ms. Josianne O\'Hara\\\";s:7:\\\"content\\\";s:3:\\\"Hey\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"c583a805-1e1f-43d3-b8d8-f9a6ce170f55\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758094575,\"delay\":null}', 0, NULL, 1758094575, 1758094575),
(246, 'database', '{\"uuid\":\"0a367561-e962-4a79-9567-bb02fa350851\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:51;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"86559442-4bf0-4304-bbc6-2da1594c9de9\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758094575,\"delay\":null}', 0, NULL, 1758094575, 1758094575),
(247, 'database', '{\"uuid\":\"8317a353-f697-46ec-9439-a22538334bd1\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:2;s:10:\\\"message_id\\\";i:51;s:7:\\\"content\\\";s:3:\\\"Hey\\\";}s:2:\\\"id\\\";s:36:\\\"86559442-4bf0-4304-bbc6-2da1594c9de9\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1758094575,\"delay\":null}', 0, NULL, 1758094575, 1758094575),
(256, 'database', '{\"uuid\":\"0e8f0fdf-1a04-4dd1-bff1-15cce0824525\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:52;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0e9be19b-b556-4910-9f23-a70e47aec444\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782522,\"delay\":null}', 0, NULL, 1760782522, 1760782522),
(257, 'database', '{\"uuid\":\"ce97eab8-6ee9-4ef1-836f-6acca22086d8\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:52;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0e9be19b-b556-4910-9f23-a70e47aec444\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782522,\"delay\":null}', 0, NULL, 1760782522, 1760782522),
(258, 'database', '{\"uuid\":\"ef68bd28-b256-427b-8b61-e365ad206eff\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:52;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"95ea24c5-26fb-4bd3-8324-57e81415adfb\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782522,\"delay\":null}', 0, NULL, 1760782522, 1760782522),
(259, 'database', '{\"uuid\":\"dfa4b2db-68bc-4a6b-a67c-6cd63fc1d322\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:52;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"95ea24c5-26fb-4bd3-8324-57e81415adfb\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782522,\"delay\":null}', 0, NULL, 1760782522, 1760782522),
(262, 'database', '{\"uuid\":\"07995a4c-4805-4bad-8b22-e1b40ee26d2e\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:53;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"878a66ad-62e8-49f1-b847-9dfa3fe97f26\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782538,\"delay\":null}', 0, NULL, 1760782538, 1760782538),
(263, 'database', '{\"uuid\":\"44f51612-d9f0-46d9-8310-f28e2a3a0682\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:53;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"878a66ad-62e8-49f1-b847-9dfa3fe97f26\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782538,\"delay\":null}', 0, NULL, 1760782538, 1760782538),
(264, 'database', '{\"uuid\":\"1cde9d6e-bf6b-4106-bce4-ce134042c291\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:53;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"e557afb4-717a-4be5-9a46-799974f89300\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782538,\"delay\":null}', 0, NULL, 1760782538, 1760782538),
(265, 'database', '{\"uuid\":\"18471451-5ffe-4c5d-988b-d221730e7b91\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:53;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"e557afb4-717a-4be5-9a46-799974f89300\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782538,\"delay\":null}', 0, NULL, 1760782538, 1760782538),
(268, 'database', '{\"uuid\":\"4f143914-69ac-4c49-830b-655eb49fd785\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:54;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"bfae9730-7155-4598-80b7-f3c7d9c331f4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782540,\"delay\":null}', 0, NULL, 1760782540, 1760782540),
(269, 'database', '{\"uuid\":\"46adf501-bb1a-4833-b99e-ed24b722531f\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:54;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"bfae9730-7155-4598-80b7-f3c7d9c331f4\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782540,\"delay\":null}', 0, NULL, 1760782540, 1760782540),
(270, 'database', '{\"uuid\":\"f0d55579-2987-4dc0-9926-72e1f5066f7f\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:54;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"9046ef7d-afb4-4388-9a86-61ea5a2c5013\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782540,\"delay\":null}', 0, NULL, 1760782540, 1760782540),
(271, 'database', '{\"uuid\":\"24396956-311a-407e-ac7c-89aefe9c791b\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:54;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"9046ef7d-afb4-4388-9a86-61ea5a2c5013\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782540,\"delay\":null}', 0, NULL, 1760782540, 1760782540),
(274, 'database', '{\"uuid\":\"b663871b-200e-4639-82eb-efaa5a231226\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:55;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"6c123d15-bdfb-433c-b905-67b92657e0f2\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782550,\"delay\":null}', 0, NULL, 1760782550, 1760782550),
(275, 'database', '{\"uuid\":\"e2a916b9-905f-43f4-923a-e7177fc634a1\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:55;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"6c123d15-bdfb-433c-b905-67b92657e0f2\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782550,\"delay\":null}', 0, NULL, 1760782550, 1760782550),
(276, 'database', '{\"uuid\":\"51d4b69a-995e-4b13-ab20-2939112b4df8\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:55;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"183532a3-a59e-4314-beda-f512fcd97ab5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782550,\"delay\":null}', 0, NULL, 1760782550, 1760782550),
(277, 'database', '{\"uuid\":\"c4d0b554-de46-4de8-86c3-09023ff752fb\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:55;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"183532a3-a59e-4314-beda-f512fcd97ab5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782550,\"delay\":null}', 0, NULL, 1760782550, 1760782550),
(281, 'database', '{\"uuid\":\"f3b68763-d1b2-4543-bb7d-0b708488a0be\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:56;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0222e185-dc2b-44b2-8a64-d379efe64689\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782555,\"delay\":null}', 0, NULL, 1760782555, 1760782555),
(282, 'database', '{\"uuid\":\"e7b3a227-bffd-46eb-b802-cef854bb061e\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:56;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"0222e185-dc2b-44b2-8a64-d379efe64689\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782555,\"delay\":null}', 0, NULL, 1760782555, 1760782555),
(283, 'database', '{\"uuid\":\"af1ad0f3-441b-4d69-8f26-16dc83bc69fc\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:56;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"41044775-51ad-4221-8f0f-fca48a2966d0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782555,\"delay\":null}', 0, NULL, 1760782555, 1760782555),
(284, 'database', '{\"uuid\":\"e17954ea-baaf-477a-91e5-45063f36115c\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:56;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"41044775-51ad-4221-8f0f-fca48a2966d0\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760782555,\"delay\":null}', 0, NULL, 1760782555, 1760782555),
(290, 'database', '{\"uuid\":\"3d97f277-53e8-4eea-aae7-feaa5813307a\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:57;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"141ef7fd-3486-437e-9286-018d13333383\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784709,\"delay\":null}', 0, NULL, 1760784709, 1760784709),
(291, 'database', '{\"uuid\":\"58a0e66e-4a50-4527-a601-65b6d8023413\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:57;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:2:\\\"hi\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"141ef7fd-3486-437e-9286-018d13333383\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784709,\"delay\":null}', 0, NULL, 1760784709, 1760784709),
(292, 'database', '{\"uuid\":\"d56721c1-7c36-41ff-ab86-bdf7b7c1ceef\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:57;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"b88ca0c0-0d14-4b40-8ffc-0fac2efd8099\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784709,\"delay\":null}', 0, NULL, 1760784709, 1760784709),
(293, 'database', '{\"uuid\":\"9e8ef2d1-b8f7-4b27-a0b4-670510281aff\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:57;s:7:\\\"content\\\";s:2:\\\"hi\\\";}s:2:\\\"id\\\";s:36:\\\"b88ca0c0-0d14-4b40-8ffc-0fac2efd8099\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784709,\"delay\":null}', 0, NULL, 1760784709, 1760784709),
(296, 'database', '{\"uuid\":\"9c64f54b-1ac2-4308-840f-1f38452ae48c\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:58;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:4:\\\"4:25\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"71d40789-9f60-4250-823a-bbb129262285\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784963,\"delay\":null}', 0, NULL, 1760784963, 1760784963);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(297, 'database', '{\"uuid\":\"a54fb5f7-e7e5-41ae-9fd8-11da103cdd76\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:6:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:58;s:9:\\\"sender_id\\\";i:211;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:4:\\\"4:25\\\";s:4:\\\"type\\\";s:4:\\\"text\\\";}s:2:\\\"id\\\";s:36:\\\"71d40789-9f60-4250-823a-bbb129262285\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784963,\"delay\":null}', 0, NULL, 1760784963, 1760784963),
(298, 'database', '{\"uuid\":\"f054e4d8-8936-4697-bb16-3ad6dfbfa6f5\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:58;s:7:\\\"content\\\";s:4:\\\"4:25\\\";}s:2:\\\"id\\\";s:36:\\\"1c24f9bf-2f82-43c0-9928-c03e55d5adb5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784963,\"delay\":null}', 0, NULL, 1760784963, 1760784963),
(299, 'database', '{\"uuid\":\"1a60a0cb-e283-4717-88c5-d7e6ab071649\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:211;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:10:\\\"message_id\\\";i:58;s:7:\\\"content\\\";s:4:\\\"4:25\\\";}s:2:\\\"id\\\";s:36:\\\"1c24f9bf-2f82-43c0-9928-c03e55d5adb5\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1760784963,\"delay\":null}', 0, NULL, 1760784963, 1760784963),
(859, 'default', '{\"uuid\":\"4e539ec1-f7cd-406d-9c75-a9b608607676\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:31;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763721227,\"delay\":null}', 255, NULL, 1763968201, 1763968201),
(860, 'default', '{\"uuid\":\"e1c5e1ce-ee2c-4810-97d4-4213cf88cac6\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:32;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763966096,\"delay\":null}', 255, NULL, 1763968201, 1763968201),
(861, 'default', '{\"uuid\":\"12712e0b-d242-494b-9e08-c5cccb2edc0c\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:33;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763968238,\"delay\":null}', 0, NULL, 1763968238, 1763968238),
(862, 'default', '{\"uuid\":\"05fe2375-4ac5-4780-8be7-65a0b16375ae\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:33;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763968238,\"delay\":null}', 0, NULL, 1763968238, 1763968238),
(863, 'default', '{\"uuid\":\"d969678a-66be-4777-b30f-c7531d5e616b\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:36;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763968863,\"delay\":null}', 0, NULL, 1763968863, 1763968863),
(864, 'default', '{\"uuid\":\"7e44b8b4-d95e-4fa7-ae21-634d21861e93\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:36;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763968863,\"delay\":null}', 0, NULL, 1763968863, 1763968863),
(865, 'default', '{\"uuid\":\"b7454b73-fdf4-489b-985e-33a898c163ce\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:38;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763969057,\"delay\":null}', 0, NULL, 1763969057, 1763969057),
(866, 'default', '{\"uuid\":\"6603e4eb-7c30-4b00-ae71-5dc46b0ad2e7\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:38;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763969057,\"delay\":null}', 0, NULL, 1763969057, 1763969057),
(867, 'default', '{\"uuid\":\"1fd7773a-daed-43b1-8ed3-4deb5af50c7a\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:42;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763969999,\"delay\":null}', 0, NULL, 1763969999, 1763969999),
(868, 'default', '{\"uuid\":\"6433122d-1b1e-4e4c-9385-ac77ab6cb535\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:1;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:4:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}s:5:\\\"width\\\";a:1:{i:0;i:480;}s:6:\\\"height\\\";a:1:{i:0;i:854;}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:42;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763969999,\"delay\":null}', 0, NULL, 1763969999, 1763969999),
(869, 'default', '{\"uuid\":\"cc8ce158-0232-452c-9f26-6040d2074164\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:43;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763970249,\"delay\":null}', 0, NULL, 1763970249, 1763970249),
(870, 'default', '{\"uuid\":\"3a447fab-13f1-4216-9906-edafc9c0b57a\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:1;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:4:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}s:5:\\\"width\\\";a:1:{i:0;i:480;}s:6:\\\"height\\\";a:1:{i:0;i:854;}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:43;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763970249,\"delay\":null}', 0, NULL, 1763970249, 1763970249),
(871, 'default', '{\"uuid\":\"5d7312dd-67f6-4b31-a9fb-a4b5e0964b12\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:44;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763970263,\"delay\":null}', 0, NULL, 1763970263, 1763970263),
(872, 'default', '{\"uuid\":\"034e41cc-98e5-47e5-aabc-1bc1c3a4449d\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:1;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:4:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}s:5:\\\"width\\\";a:1:{i:0;i:480;}s:6:\\\"height\\\";a:1:{i:0;i:854;}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:44;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763970263,\"delay\":null}', 0, NULL, 1763970263, 1763970263),
(873, 'default', '{\"uuid\":\"14e7bfad-9e2a-4ecc-b56e-01c471254811\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:45;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763970361,\"delay\":null}', 0, NULL, 1763970361, 1763970361);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(874, 'default', '{\"uuid\":\"1ba8fd24-3092-401f-b8d9-3a9d7778fbc4\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:45;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763970361,\"delay\":null}', 0, NULL, 1763970361, 1763970361),
(875, 'default', '{\"uuid\":\"ec1b4a9f-3f98-4249-9b6b-f91e613f11d8\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:46;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763970370,\"delay\":null}', 0, NULL, 1763970370, 1763970370),
(876, 'default', '{\"uuid\":\"c60a3ed4-d02b-4614-a323-997b52b10565\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:0;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:2:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:46;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763970370,\"delay\":null}', 0, NULL, 1763970370, 1763970370),
(877, 'default', '{\"uuid\":\"27b3c8f3-38b5-40cc-a685-3c1505c8487f\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:47;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763970737,\"delay\":null}', 0, NULL, 1763970737, 1763970737),
(878, 'default', '{\"uuid\":\"e6b692ed-a83f-4a5d-8057-6d4be829545a\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:1;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:4:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}s:5:\\\"width\\\";a:1:{i:0;i:480;}s:6:\\\"height\\\";a:1:{i:0;i:854;}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:47;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763970737,\"delay\":null}', 0, NULL, 1763970737, 1763970737),
(879, 'default', '{\"uuid\":\"810275b6-d4ff-4a32-b286-ac0ae5f3a933\",\"displayName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":600,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessMediaJob\",\"command\":\"O:24:\\\"App\\\\Jobs\\\\ProcessMediaJob\\\":1:{s:5:\\\"media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:48;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"},\"createdAt\":1763970839,\"delay\":null}', 0, NULL, 1763970839, 1763970839),
(880, 'default', '{\"uuid\":\"c731de97-a6e3-4c7e-b891-2377306ec012\",\"displayName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\",\"command\":\"O:58:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Jobs\\\\PerformConversionsJob\\\":6:{s:14:\\\"\\u0000*\\u0000conversions\\\";O:52:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\ConversionCollection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:1:{i:1;O:42:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Conversion\\\":11:{s:12:\\\"\\u0000*\\u0000fileNamer\\\";O:54:\\\"Spatie\\\\MediaLibrary\\\\Support\\\\FileNamer\\\\DefaultFileNamer\\\":0:{}s:28:\\\"\\u0000*\\u0000extractVideoFrameAtSecond\\\";d:1;s:16:\\\"\\u0000*\\u0000manipulations\\\";O:45:\\\"Spatie\\\\MediaLibrary\\\\Conversions\\\\Manipulations\\\":1:{s:16:\\\"\\u0000*\\u0000manipulations\\\";a:4:{s:8:\\\"optimize\\\";a:1:{i:0;O:36:\\\"Spatie\\\\ImageOptimizer\\\\OptimizerChain\\\":3:{s:13:\\\"\\u0000*\\u0000optimizers\\\";a:7:{i:0;O:42:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Jpegoptim\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:4:\\\"-m85\\\";i:1;s:7:\\\"--force\\\";i:2;s:17:\\\"--all-progressive\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:9:\\\"jpegoptim\\\";}i:1;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Pngquant\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:7:\\\"--force\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"pngquant\\\";}i:2;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Optipng\\\":5:{s:7:\\\"options\\\";a:3:{i:0;s:3:\\\"-i0\\\";i:1;s:3:\\\"-o2\\\";i:2;s:6:\\\"-quiet\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"optipng\\\";}i:3;O:37:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Svgo\\\":5:{s:7:\\\"options\\\";a:1:{i:0;s:20:\\\"--disable=cleanupIDs\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:4:\\\"svgo\\\";}i:4;O:41:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Gifsicle\\\":5:{s:7:\\\"options\\\";a:2:{i:0;s:2:\\\"-b\\\";i:1;s:3:\\\"-O3\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:8:\\\"gifsicle\\\";}i:5;O:38:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Cwebp\\\":5:{s:7:\\\"options\\\";a:4:{i:0;s:4:\\\"-m 6\\\";i:1;s:8:\\\"-pass 10\\\";i:2;s:3:\\\"-mt\\\";i:3;s:5:\\\"-q 90\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:5:\\\"cwebp\\\";}i:6;O:40:\\\"Spatie\\\\ImageOptimizer\\\\Optimizers\\\\Avifenc\\\":6:{s:7:\\\"options\\\";a:8:{i:0;s:14:\\\"-a cq-level=23\\\";i:1;s:6:\\\"-j all\\\";i:2;s:7:\\\"--min 0\\\";i:3;s:8:\\\"--max 63\\\";i:4;s:12:\\\"--minalpha 0\\\";i:5;s:13:\\\"--maxalpha 63\\\";i:6;s:14:\\\"-a end-usage=q\\\";i:7;s:12:\\\"-a tune=ssim\\\";}s:9:\\\"imagePath\\\";s:0:\\\"\\\";s:10:\\\"binaryPath\\\";s:0:\\\"\\\";s:7:\\\"tmpPath\\\";N;s:10:\\\"binaryName\\\";s:7:\\\"avifenc\\\";s:16:\\\"decodeBinaryName\\\";s:7:\\\"avifdec\\\";}}s:9:\\\"\\u0000*\\u0000logger\\\";O:33:\\\"Spatie\\\\ImageOptimizer\\\\DummyLogger\\\":0:{}s:10:\\\"\\u0000*\\u0000timeout\\\";i:60;}}s:6:\\\"format\\\";a:1:{i:0;s:3:\\\"jpg\\\";}s:5:\\\"width\\\";a:1:{i:0;i:480;}s:6:\\\"height\\\";a:1:{i:0;i:854;}}}s:23:\\\"\\u0000*\\u0000performOnCollections\\\";a:1:{i:0;s:6:\\\"videos\\\";}s:17:\\\"\\u0000*\\u0000performOnQueue\\\";b:1;s:26:\\\"\\u0000*\\u0000keepOriginalImageFormat\\\";b:0;s:27:\\\"\\u0000*\\u0000generateResponsiveImages\\\";b:0;s:18:\\\"\\u0000*\\u0000widthCalculator\\\";N;s:24:\\\"\\u0000*\\u0000loadingAttributeValue\\\";N;s:16:\\\"\\u0000*\\u0000pdfPageNumber\\\";i:1;s:7:\\\"\\u0000*\\u0000name\\\";s:5:\\\"thumb\\\";}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}s:8:\\\"\\u0000*\\u0000media\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:49:\\\"Spatie\\\\MediaLibrary\\\\MediaCollections\\\\Models\\\\Media\\\";s:2:\\\"id\\\";i:48;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:14:\\\"\\u0000*\\u0000onlyMissing\\\";b:0;s:10:\\\"connection\\\";s:8:\\\"database\\\";s:5:\\\"queue\\\";s:0:\\\"\\\";s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1763970839,\"delay\":null}', 0, NULL, 1763970839, 1763970839),
(881, 'default', '{\"uuid\":\"fa7d3d12-054b-4112-bed2-da7b5b300b97\",\"displayName\":\"App\\\\Events\\\\Chat\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:27:\\\"App\\\\Events\\\\Chat\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:31:\\\"App\\\\Models\\\\Chat\\\\UserChatMessage\\\";s:2:\\\"id\\\";i:59;s:9:\\\"relations\\\";a:1:{i:0;s:6:\\\"sender\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1763971868,\"delay\":null}', 0, NULL, 1763971868, 1763971868),
(882, 'database', '{\"uuid\":\"c00ad2d9-dcfc-4bdb-b4f6-3b55f00aedde\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:24:\\\"yeh 52 msg ka reply hian\\\";}s:2:\\\"id\\\";s:36:\\\"06ca0802-e4b4-445b-99f3-850619a73f3f\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1763971869,\"delay\":null}', 0, NULL, 1763971869, 1763971869),
(883, 'database', '{\"uuid\":\"c0205b1f-814c-4018-be18-2943ad1ac4ca\",\"displayName\":\"App\\\\Notifications\\\\GenericNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":4:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:37:\\\"App\\\\Notifications\\\\GenericNotification\\\":4:{s:8:\\\"category\\\";s:12:\\\"chat_message\\\";s:7:\\\"payload\\\";a:3:{s:7:\\\"chat_id\\\";i:3;s:11:\\\"sender_name\\\";s:10:\\\"Aman Verma\\\";s:7:\\\"content\\\";s:24:\\\"yeh 52 msg ka reply hian\\\";}s:2:\\\"id\\\";s:36:\\\"06ca0802-e4b4-445b-99f3-850619a73f3f\\\";s:5:\\\"queue\\\";s:8:\\\"database\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:9:\\\"broadcast\\\";}s:5:\\\"queue\\\";s:8:\\\"database\\\";}\"},\"createdAt\":1763971869,\"delay\":null}', 0, NULL, 1763971869, 1763971869);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `likeable_type` varchar(255) NOT NULL,
  `likeable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `likeable_type`, `likeable_id`, `created_at`, `updated_at`) VALUES
(1, 11, 'UserPost', 1, '2025-09-19 06:14:03', '2025-09-19 06:14:03'),
(4, 211, 'UserPost', 3, '2025-10-17 04:49:13', '2025-10-17 04:49:13'),
(5, 211, 'UserPost', 5, '2025-10-17 05:02:45', '2025-10-17 05:02:45'),
(8, 211, 'App\\Models\\Comment', 10, '2025-10-18 02:18:43', '2025-10-18 02:18:43'),
(10, 211, 'UserPost', 7, '2025-11-24 01:05:19', '2025-11-24 01:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(16, 'App\\Models\\UserStory', 6, '056bd201-a8e0-4667-ad1b-fdd44b14e8e3', 'stories', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '{\"caption\":\"First Story\",\"meta\":[]}', '[]', '[]', 1, '2025-10-18 09:25:26', '2025-10-18 09:25:26'),
(17, 'App\\Models\\UserStory', 7, '5736eed5-d766-4390-9363-f60f5d2981d7', 'stories', 'Fittest on Earth', 'Fittest-on-Earth.jpg', 'image/jpeg', 'public', 'public', 245396, '[]', '{\"caption\":\"Images Story\",\"meta\":[]}', '[]', '[]', 1, '2025-10-19 23:30:58', '2025-10-19 23:30:59'),
(18, 'App\\Models\\UserStoryHighlight', 3, '2b79ecc4-a2ff-458f-a96e-3e38f44c9ec0', 'cover_media', 'Fittest on Earth', '68f5db0e43dcf.jpg', 'image/jpeg', 'public', 'public', 245396, '[]', '[]', '{\"cover\":true}', '[]', 1, '2025-10-20 01:17:42', '2025-10-21 23:27:31'),
(19, 'App\\Models\\Ad', 6, 'c255ec22-64f1-4d79-bae0-ed50c0f68123', 'ads', '61tHzSf4zQL._SX679_', '61tHzSf4zQL._SX679_.jpg', 'image/jpeg', 'public', 'public', 42873, '[]', '[]', '[]', '[]', 1, '2025-10-24 05:24:19', '2025-10-24 05:24:19'),
(20, 'App\\Models\\Ad', 6, '41650c56-c785-42b6-9da0-25da2fb34405', 'ads', '71LLpkmmZpL._SX679_', '71LLpkmmZpL._SX679_.jpg', 'image/jpeg', 'public', 'public', 61182, '[]', '[]', '[]', '[]', 2, '2025-10-24 05:24:19', '2025-10-24 05:24:19'),
(21, 'App\\Models\\Ad', 7, 'd520aa11-7a6a-4eb3-81b7-c9ff0760247f', 'ads', '61tHzSf4zQL._SX679_', '61tHzSf4zQL._SX679_.jpg', 'image/jpeg', 'public', 'public', 42873, '[]', '[]', '[]', '[]', 1, '2025-10-24 05:29:34', '2025-10-24 05:29:34'),
(22, 'App\\Models\\Ad', 7, '96d5a26c-a272-4dc5-b554-320ff140f5ac', 'ads', '71LLpkmmZpL._SX679_', '71LLpkmmZpL._SX679_.jpg', 'image/jpeg', 'public', 'public', 61182, '[]', '[]', '[]', '[]', 2, '2025-10-24 05:29:34', '2025-10-24 05:29:34'),
(23, 'App\\Models\\Ad', 8, 'c318fe32-be32-45a8-9fae-0ba0e0e11005', 'ads', '61tHzSf4zQL._SX679_', '61tHzSf4zQL._SX679_.jpg', 'image/jpeg', 'public', 'public', 42873, '[]', '[]', '[]', '[]', 1, '2025-10-24 05:31:37', '2025-10-24 05:31:37'),
(24, 'App\\Models\\Ad', 8, '4fb9a9a8-214d-4809-9636-15f358d429bc', 'ads', '71LLpkmmZpL._SX679_', '71LLpkmmZpL._SX679_.jpg', 'image/jpeg', 'public', 'public', 61182, '[]', '[]', '[]', '[]', 2, '2025-10-24 05:31:37', '2025-10-24 05:31:37'),
(25, 'App\\Models\\Ad', 9, 'f5bd1c32-1021-4a38-94dc-61d5c4718e84', 'ads', '61tHzSf4zQL._SX679_', '61tHzSf4zQL._SX679_.jpg', 'image/jpeg', 'public', 'public', 42873, '[]', '[]', '[]', '[]', 1, '2025-10-24 05:32:16', '2025-10-24 05:32:16'),
(26, 'App\\Models\\Ad', 9, '8ff099d1-b764-4428-a75d-e0efba2dbb5f', 'ads', '71LLpkmmZpL._SX679_', '71LLpkmmZpL._SX679_.jpg', 'image/jpeg', 'public', 'public', 61182, '[]', '[]', '[]', '[]', 2, '2025-10-24 05:32:16', '2025-10-24 05:32:16'),
(27, 'App\\Models\\Ad', 10, '78b24eee-3670-4b7c-a886-6cebe7e3efb1', 'ads', '61tHzSf4zQL._SX679_', '61tHzSf4zQL._SX679_.jpg', 'image/jpeg', 'public', 'public', 42873, '[]', '[]', '[]', '[]', 1, '2025-10-24 05:37:38', '2025-10-24 05:37:38'),
(28, 'App\\Models\\Ad', 10, '48cd310e-452d-438b-aa30-8c1e6f78fa75', 'ads', '71LLpkmmZpL._SX679_', '71LLpkmmZpL._SX679_.jpg', 'image/jpeg', 'public', 'public', 61182, '[]', '[]', '[]', '[]', 2, '2025-10-24 05:37:38', '2025-10-24 05:37:38'),
(29, 'App\\Models\\Ad', 11, '7c3e3659-3e1c-44cd-9cd6-f2f5724a34d1', 'ads', '61tHzSf4zQL._SX679_', '61tHzSf4zQL._SX679_.jpg', 'image/jpeg', 'public', 'public', 42873, '[]', '[]', '[]', '[]', 1, '2025-10-24 05:47:25', '2025-10-24 05:47:25'),
(30, 'App\\Models\\Ad', 11, '2e411678-0606-4834-96d9-4f9a739b0e38', 'ads', '71LLpkmmZpL._SX679_', '71LLpkmmZpL._SX679_.jpg', 'image/jpeg', 'public', 'public', 61182, '[]', '[]', '[]', '[]', 2, '2025-10-24 05:47:25', '2025-10-24 05:47:25'),
(31, 'UserPost', 7, 'd1d3dd25-82a9-4e3e-a2f6-790a424dfb86', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-21 05:03:47', '2025-11-21 05:03:47'),
(32, 'UserPost', 8, 'c6294a2b-4ee1-4372-b0f1-370d7ee608c1', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-24 01:04:54', '2025-11-24 01:04:54'),
(33, 'UserPost', 9, '1d2fbc67-5918-4672-84b8-39e74b2125ef', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-24 01:40:38', '2025-11-24 01:40:38'),
(36, 'UserPost', 12, '90a0da43-d2b0-4c27-bce5-89f391aee779', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-24 01:51:03', '2025-11-24 01:51:03'),
(38, 'UserPost', 14, '6ef24ec5-4548-4ee2-87cf-9a6a5d5f26d9', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-24 01:54:17', '2025-11-24 01:54:17'),
(42, 'UserPost', 18, '94ecac64-a665-4bf4-a887-51c8a1abf8fe', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-11-24 02:09:56', '2025-11-24 02:09:59'),
(43, 'UserPost', 19, 'b084fde2-e031-4d60-afca-f5c3bb9ac1ba', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-11-24 02:14:08', '2025-11-24 02:14:09'),
(44, 'UserPost', 20, 'e5d533d3-8c75-4460-a08a-35e114a31ccc', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-11-24 02:14:22', '2025-11-24 02:14:23'),
(45, 'UserPost', 21, 'd30204e8-f192-4ddd-ae3d-0845aa44c9db', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-24 02:16:01', '2025-11-24 02:16:01'),
(46, 'UserPost', 22, '30d4493a-e45a-4402-84fd-e471259b28ee', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '[]', '[]', 1, '2025-11-24 02:16:10', '2025-11-24 02:16:10'),
(47, 'UserPost', 23, '16784480-1bbf-4298-9b88-9c80082147bf', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-11-24 02:22:16', '2025-11-24 02:22:17'),
(48, 'UserPost', 24, 'e0e37b91-b436-4b61-9b92-859cca7de15d', 'videos', 'videoplayback', 'videoplayback.mp4', 'video/mp4', 'public', 'public', 3805389, '[]', '[]', '{\"thumb\":true}', '[]', 1, '2025-11-24 02:23:58', '2025-11-24 02:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_02_101030_create_permission_tables', 1),
(5, '2025_09_02_102639_create_media_table', 1),
(6, '2025_09_02_124054_create_personal_access_tokens_table', 1),
(7, '2025_09_03_083223_create_posts_table', 1),
(8, '2025_09_03_083223_create_reels_table', 1),
(9, '2025_09_03_083224_create_products_table', 1),
(10, '2025_09_03_083224_create_stories_table', 1),
(11, '2025_09_03_083225_create_user_jobs_table', 1),
(12, '2025_09_03_083225_create_reports_table', 1),
(13, '2025_09_03_100922_create_orders_table', 1),
(14, '2025_09_03_102645_add_extra_fileds_to_users_table', 1),
(15, '2025_09_04_072801_create_admin_logs_table', 1),
(16, '2025_09_04_151241_create_followers_table', 1),
(17, '2025_09_05_084414_create_likes_table', 1),
(18, '2025_09_05_084833_create_comments_table', 1),
(19, '2025_09_08_050639_create_user_posts_table', 1),
(20, '2025_09_09_123410_create_user_stories_table', 1),
(21, '2025_09_09_123435_create_user_story_likes_table', 1),
(22, '2025_09_09_123620_create_user_story_highlights_table', 1),
(23, '2025_09_11_061002_create_user_products_table', 2),
(24, '2025_09_11_061007_create_user_product_images_table', 2),
(25, '2025_09_11_061030_create_user_services_table', 2),
(26, '2025_09_11_061040_create_user_service_images_table', 2),
(27, '2025_09_11_061049_create_user_orders_table', 2),
(28, '2025_09_11_061053_create_user_order_items_table', 2),
(30, '2025_09_11_061107_create_user_shippings_table', 2),
(31, '2025_09_11_061115_create_user_addresses_table', 2),
(32, '2025_09_11_062321_add_is_seller_to_users_table', 3),
(33, '2025_09_11_110812_add_admin_note_to_user_products_table', 4),
(34, '2025_09_11_123921_create_user_carts_table', 5),
(35, '2025_09_11_123923_create_user_cart_items_table', 5),
(36, '2025_09_11_061059_create_user_payments_table', 6),
(37, '2025_09_15_101355_add_is_employer_to_users_table', 7),
(38, '2025_09_15_101418_create_user_job_posts_table', 8),
(39, '2025_09_15_101458_create_user_job_applications_table', 8),
(40, '2025_09_16_060035_create_user_chats_table', 9),
(41, '2025_09_16_060052_create_user_chat_participants_table', 9),
(42, '2025_09_16_060105_create_user_chat_messages_table', 9),
(43, '2025_09_16_112949_create_notifications_table', 10),
(44, '2025_09_16_115522_add_notifications_indexes_and_unread_counts_table', 11),
(45, '2025_09_18_055528_create_ad_packages_table', 12),
(46, '2025_09_18_055548_create_ads_table', 12),
(47, '2025_09_18_055559_create_ad_payments_table', 12),
(48, '2025_09_18_055614_create_ad_impressions_table', 12),
(51, '2025_09_18_125215_create_views_table', 13),
(52, '2025_09_20_052021_add_interests_to_users_table', 13),
(53, '2025_09_20_053800_add_additional_fields_to_users_table', 14),
(54, '2025_09_20_055227_add_unique_constraint_to_username_column_in_users_table', 15),
(55, '2025_09_20_061057_add_occupation_to_users_table', 16),
(56, '2025_09_20_111343_create_activity_log_table', 17),
(57, '2025_09_20_111344_add_event_column_to_activity_log_table', 17),
(58, '2025_09_20_111345_add_batch_uuid_column_to_activity_log_table', 17),
(59, '2025_09_20_111456_add_index_to_activity_log', 18),
(60, '2025_10_17_051051_create_otps_table', 19),
(61, '2025_10_17_132748_add_view_count_to_user_posts_table', 20),
(62, '2025_10_20_063757_alter_user_story_highlights_change_cover_media_id_to_foreign_key', 21),
(63, '2025_10_22_103920_add_cover_image_to_user_services_table', 22),
(64, '2025_10_24_094512_create_interests_table', 23),
(65, '2025_10_24_094808_update_ads_table_for_interests_and_media', 23),
(66, '2025_10_24_103924_create_ad_interest_table', 24),
(68, '2025_10_24_110052_update_ads_table_for_media', 25),
(69, '2025_10_24_110416_remove_unnecessary_columns_from_ads_table', 26),
(70, '2025_11_20_065610_create_interest_user_table', 27),
(71, '2025_11_21_065921_create_interest_categories_table', 28),
(72, '2025_11_21_070001_add_category_id_to_interests_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 25),
(3, 'App\\Models\\User', 26),
(3, 'App\\Models\\User', 27),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29),
(3, 'App\\Models\\User', 30),
(3, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 32),
(3, 'App\\Models\\User', 33),
(3, 'App\\Models\\User', 34),
(3, 'App\\Models\\User', 35),
(3, 'App\\Models\\User', 36),
(3, 'App\\Models\\User', 37),
(3, 'App\\Models\\User', 38),
(3, 'App\\Models\\User', 39),
(3, 'App\\Models\\User', 40),
(3, 'App\\Models\\User', 41),
(3, 'App\\Models\\User', 42),
(3, 'App\\Models\\User', 43),
(3, 'App\\Models\\User', 44),
(3, 'App\\Models\\User', 45),
(3, 'App\\Models\\User', 46),
(3, 'App\\Models\\User', 47),
(3, 'App\\Models\\User', 48),
(3, 'App\\Models\\User', 49),
(3, 'App\\Models\\User', 50),
(3, 'App\\Models\\User', 51),
(3, 'App\\Models\\User', 52),
(3, 'App\\Models\\User', 53),
(3, 'App\\Models\\User', 54),
(3, 'App\\Models\\User', 55),
(3, 'App\\Models\\User', 56),
(3, 'App\\Models\\User', 57),
(3, 'App\\Models\\User', 58),
(3, 'App\\Models\\User', 59),
(3, 'App\\Models\\User', 60),
(3, 'App\\Models\\User', 61),
(3, 'App\\Models\\User', 62),
(3, 'App\\Models\\User', 63),
(3, 'App\\Models\\User', 64),
(3, 'App\\Models\\User', 65),
(3, 'App\\Models\\User', 66),
(3, 'App\\Models\\User', 67),
(3, 'App\\Models\\User', 68),
(3, 'App\\Models\\User', 69),
(3, 'App\\Models\\User', 70),
(3, 'App\\Models\\User', 71),
(3, 'App\\Models\\User', 72),
(3, 'App\\Models\\User', 73),
(3, 'App\\Models\\User', 74),
(3, 'App\\Models\\User', 75),
(3, 'App\\Models\\User', 76),
(3, 'App\\Models\\User', 77),
(3, 'App\\Models\\User', 78),
(3, 'App\\Models\\User', 79),
(3, 'App\\Models\\User', 80),
(3, 'App\\Models\\User', 81),
(3, 'App\\Models\\User', 82),
(3, 'App\\Models\\User', 83),
(3, 'App\\Models\\User', 84),
(3, 'App\\Models\\User', 85),
(3, 'App\\Models\\User', 86),
(3, 'App\\Models\\User', 87),
(3, 'App\\Models\\User', 88),
(3, 'App\\Models\\User', 89),
(3, 'App\\Models\\User', 90),
(3, 'App\\Models\\User', 91),
(3, 'App\\Models\\User', 92),
(3, 'App\\Models\\User', 93),
(3, 'App\\Models\\User', 94),
(3, 'App\\Models\\User', 95),
(3, 'App\\Models\\User', 96),
(3, 'App\\Models\\User', 97),
(3, 'App\\Models\\User', 98),
(3, 'App\\Models\\User', 99),
(3, 'App\\Models\\User', 100),
(3, 'App\\Models\\User', 101),
(3, 'App\\Models\\User', 102),
(3, 'App\\Models\\User', 103),
(3, 'App\\Models\\User', 104),
(3, 'App\\Models\\User', 105),
(3, 'App\\Models\\User', 106),
(3, 'App\\Models\\User', 107),
(3, 'App\\Models\\User', 108),
(3, 'App\\Models\\User', 109),
(3, 'App\\Models\\User', 110),
(3, 'App\\Models\\User', 111),
(3, 'App\\Models\\User', 112),
(3, 'App\\Models\\User', 113),
(3, 'App\\Models\\User', 114),
(3, 'App\\Models\\User', 115),
(3, 'App\\Models\\User', 116),
(3, 'App\\Models\\User', 117),
(3, 'App\\Models\\User', 118),
(3, 'App\\Models\\User', 119),
(3, 'App\\Models\\User', 120),
(3, 'App\\Models\\User', 121),
(3, 'App\\Models\\User', 122),
(3, 'App\\Models\\User', 123),
(3, 'App\\Models\\User', 124),
(3, 'App\\Models\\User', 125),
(3, 'App\\Models\\User', 126),
(3, 'App\\Models\\User', 127),
(3, 'App\\Models\\User', 128),
(3, 'App\\Models\\User', 129),
(3, 'App\\Models\\User', 130),
(3, 'App\\Models\\User', 131),
(3, 'App\\Models\\User', 132),
(3, 'App\\Models\\User', 133),
(3, 'App\\Models\\User', 134),
(3, 'App\\Models\\User', 135),
(3, 'App\\Models\\User', 136),
(3, 'App\\Models\\User', 137),
(3, 'App\\Models\\User', 138),
(3, 'App\\Models\\User', 139),
(3, 'App\\Models\\User', 140),
(3, 'App\\Models\\User', 141),
(3, 'App\\Models\\User', 142),
(3, 'App\\Models\\User', 143),
(3, 'App\\Models\\User', 144),
(3, 'App\\Models\\User', 145),
(3, 'App\\Models\\User', 146),
(3, 'App\\Models\\User', 147),
(3, 'App\\Models\\User', 148),
(3, 'App\\Models\\User', 149),
(3, 'App\\Models\\User', 150),
(3, 'App\\Models\\User', 151),
(3, 'App\\Models\\User', 152),
(3, 'App\\Models\\User', 153),
(3, 'App\\Models\\User', 154),
(3, 'App\\Models\\User', 155),
(3, 'App\\Models\\User', 156),
(3, 'App\\Models\\User', 157),
(3, 'App\\Models\\User', 158),
(3, 'App\\Models\\User', 159),
(3, 'App\\Models\\User', 160),
(3, 'App\\Models\\User', 161),
(3, 'App\\Models\\User', 162),
(3, 'App\\Models\\User', 163),
(3, 'App\\Models\\User', 164),
(3, 'App\\Models\\User', 165),
(3, 'App\\Models\\User', 166),
(3, 'App\\Models\\User', 167),
(3, 'App\\Models\\User', 168),
(3, 'App\\Models\\User', 169),
(3, 'App\\Models\\User', 170),
(3, 'App\\Models\\User', 171),
(3, 'App\\Models\\User', 172),
(3, 'App\\Models\\User', 173),
(3, 'App\\Models\\User', 174),
(3, 'App\\Models\\User', 175),
(3, 'App\\Models\\User', 176),
(3, 'App\\Models\\User', 177),
(3, 'App\\Models\\User', 178),
(3, 'App\\Models\\User', 179),
(3, 'App\\Models\\User', 180),
(3, 'App\\Models\\User', 181),
(3, 'App\\Models\\User', 182),
(3, 'App\\Models\\User', 183),
(3, 'App\\Models\\User', 184),
(3, 'App\\Models\\User', 185),
(3, 'App\\Models\\User', 186),
(3, 'App\\Models\\User', 187),
(3, 'App\\Models\\User', 188),
(3, 'App\\Models\\User', 189),
(3, 'App\\Models\\User', 190),
(3, 'App\\Models\\User', 191),
(3, 'App\\Models\\User', 192),
(3, 'App\\Models\\User', 193),
(3, 'App\\Models\\User', 194),
(3, 'App\\Models\\User', 195),
(3, 'App\\Models\\User', 196),
(3, 'App\\Models\\User', 197),
(3, 'App\\Models\\User', 198),
(3, 'App\\Models\\User', 199),
(3, 'App\\Models\\User', 200),
(3, 'App\\Models\\User', 201),
(3, 'App\\Models\\User', 202),
(3, 'App\\Models\\User', 203),
(3, 'App\\Models\\User', 204),
(3, 'App\\Models\\User', 205),
(3, 'App\\Models\\User', 206),
(3, 'App\\Models\\User', 207),
(3, 'App\\Models\\User', 208),
(3, 'App\\Models\\User', 209),
(3, 'App\\Models\\User', 210),
(3, 'App\\Models\\User', 211),
(3, 'App\\Models\\User', 212);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_unread_counts`
--

CREATE TABLE `notification_unread_counts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `unread_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `status` enum('pending','paid','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `placed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `uuid`, `buyer_id`, `total_amount`, `shipping_address`, `status`, `placed_at`, `created_at`, `updated_at`) VALUES
(1, '9f098ac3-3fca-3360-8d39-dd338528b876', 127, 967.95, '{\"line1\":\"79984 Marvin Brook Apt. 107\",\"city\":\"Rueckerfort\",\"state\":\"Colorado\",\"postal_code\":\"54492-7996\",\"country\":\"Iceland\"}', 'cancelled', '2025-08-25 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(2, 'e1ca89cb-ad3f-3fda-8ede-2dbd4d15f959', 128, 5905.09, '{\"line1\":\"216 Caleigh Via Suite 471\",\"city\":\"Okeyview\",\"state\":\"Florida\",\"postal_code\":\"05408\",\"country\":\"Moldova\"}', 'pending', '2025-08-19 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(3, '55e2f144-45ed-3eee-b194-526b017f12f3', 129, 1470.03, '{\"line1\":\"91345 Dooley Walks Apt. 410\",\"city\":\"Dangeloton\",\"state\":\"Florida\",\"postal_code\":\"20182\",\"country\":\"Zimbabwe\"}', 'cancelled', '2025-08-17 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(4, '2414573f-e916-3514-8b6b-bffefe6a32ad', 130, 1466.97, '{\"line1\":\"466 Kelley Road\",\"city\":\"Herzogville\",\"state\":\"Utah\",\"postal_code\":\"77107\",\"country\":\"France\"}', 'delivered', '2025-08-11 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(5, 'f619ae8d-307a-38ea-b4a4-f5dd0d57101a', 131, 2516.38, '{\"line1\":\"85265 Monahan Center Apt. 059\",\"city\":\"New Ocie\",\"state\":\"South Carolina\",\"postal_code\":\"90362\",\"country\":\"Bosnia and Herzegovina\"}', 'delivered', '2025-09-05 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(6, 'cb25edc2-0358-36c7-b9ae-fdd617e446a6', 132, 586.64, '{\"line1\":\"85149 Ryan Mall Apt. 039\",\"city\":\"Stantonview\",\"state\":\"Maryland\",\"postal_code\":\"49711\",\"country\":\"Barbados\"}', 'shipped', '2025-09-03 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(7, 'ededb13a-7818-36f7-8846-24a29f4eedba', 133, 574.39, '{\"line1\":\"88178 Agustina Pike Suite 229\",\"city\":\"Lake Dovieshire\",\"state\":\"Oklahoma\",\"postal_code\":\"32064\",\"country\":\"Andorra\"}', 'delivered', '2025-08-13 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(8, '32191022-9769-3386-bea6-bbf0786b681c', 134, 2325.67, '{\"line1\":\"38097 Stacey Plaza Suite 569\",\"city\":\"New Sonya\",\"state\":\"Utah\",\"postal_code\":\"33955-1117\",\"country\":\"Taiwan\"}', 'shipped', '2025-08-17 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(9, '01276957-76ab-3c5c-b9b0-86f6feb0e583', 135, 1861.46, '{\"line1\":\"621 Hassan Park\",\"city\":\"Lebsackshire\",\"state\":\"Arizona\",\"postal_code\":\"35139\",\"country\":\"El Salvador\"}', 'refunded', '2025-08-30 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(10, 'e4501e8f-d220-3e4c-b65a-b45eb186e182', 136, 3113.47, '{\"line1\":\"2339 Katrina Corner\",\"city\":\"East Macey\",\"state\":\"Delaware\",\"postal_code\":\"34799-5398\",\"country\":\"Djibouti\"}', 'paid', '2025-08-16 09:06:42', '2025-09-10 09:06:42', '2025-09-10 09:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `seller_id`, `sku`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 31, 138, 'SKU-7917', 3, 322.65, 967.95, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(2, 2, 32, 140, 'SKU-8100', 3, 209.41, 628.23, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(3, 2, 33, 142, 'SKU-0313', 5, 491.26, 2456.30, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(4, 2, 34, 144, 'SKU-9916', 3, 271.23, 813.69, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(5, 2, 35, 146, 'SKU-4488', 1, 391.17, 391.17, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(6, 2, 36, 148, 'SKU-0588', 5, 323.14, 1615.70, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(7, 3, 37, 150, 'SKU-6615', 3, 166.35, 499.05, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(8, 3, 38, 152, 'SKU-0757', 3, 323.66, 970.98, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(9, 4, 39, 154, 'SKU-0162', 3, 382.58, 1147.74, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(10, 4, 40, 156, 'SKU-2135', 1, 319.23, 319.23, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(11, 5, 41, 158, 'SKU-3750', 3, 151.50, 454.50, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(12, 5, 42, 160, 'SKU-2764', 5, 286.70, 1433.50, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(13, 5, 43, 162, 'SKU-9119', 3, 209.46, 628.38, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(14, 6, 44, 164, 'SKU-1765', 4, 146.66, 586.64, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(15, 7, 45, 166, 'SKU-8036', 1, 247.81, 247.81, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(16, 7, 46, 168, 'SKU-4134', 2, 163.29, 326.58, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(17, 8, 47, 170, 'SKU-2093', 2, 338.06, 676.12, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(18, 8, 48, 172, 'SKU-8193', 3, 362.04, 1086.12, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(19, 8, 49, 174, 'SKU-4365', 3, 187.81, 563.43, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(20, 9, 50, 176, 'SKU-8272', 3, 293.62, 880.86, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(21, 9, 51, 178, 'SKU-3131', 5, 196.12, 980.60, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(22, 10, 52, 180, 'SKU-5867', 4, 450.16, 1800.64, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(23, 10, 53, 182, 'SKU-7804', 3, 134.67, 404.01, '2025-09-10 09:06:42', '2025-09-10 09:06:42'),
(24, 10, 54, 184, 'SKU-3906', 3, 302.94, 908.82, '2025-09-10 09:06:42', '2025-09-10 09:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 3, 'MyApp', '3b0cb1cce037906818ec2d7641b6d900a325dc4efa3eda1447abf3312b0b3721', '[\"*\"]', '2025-09-11 04:09:58', NULL, '2025-09-11 01:21:58', '2025-09-11 04:09:58'),
(2, 'App\\Models\\User', 11, 'MyApp', 'bb1d20c83e480ead5dfd7bd7aca98b38bd39804b0b652c38fd189812a4710375', '[\"*\"]', '2025-09-11 07:57:39', NULL, '2025-09-11 07:28:02', '2025-09-11 07:57:39'),
(3, 'App\\Models\\User', 11, 'MyApp', '36979dd9d606d88e11f080c6978d5434fc8dc5ee4d5ebea3f76564d2854e91f9', '[\"*\"]', '2025-09-12 04:57:06', NULL, '2025-09-11 23:43:51', '2025-09-12 04:57:06'),
(4, 'App\\Models\\User', 11, 'MyApp', '5db18ab7091d950e5c116a5126a562ea675c96bb8de2b0c39f20434cf1917a02', '[\"*\"]', '2025-09-12 08:08:41', NULL, '2025-09-12 04:57:36', '2025-09-12 08:08:41'),
(5, 'App\\Models\\User', 3, 'MyApp', 'c1257ccb42f16febcccc1bef9c20d85e331e96b7064099ec4dc168e301e5944e', '[\"*\"]', '2025-09-17 01:21:58', NULL, '2025-09-15 02:53:23', '2025-09-17 01:21:58'),
(7, 'App\\Models\\User', 208, 'MyApp', 'a55042821c4b8164d3840e581011591504e324d35e4024e36afb39f803fcb89d', '[\"*\"]', NULL, NULL, '2025-09-20 01:43:58', '2025-09-20 01:43:58'),
(8, 'App\\Models\\User', 209, 'MyApp', '875c52974be02a53fbfca7c64968cd1c3afea4d51d188ca7299c02fddd87d943', '[\"*\"]', NULL, NULL, '2025-09-20 01:59:55', '2025-09-20 01:59:55'),
(9, 'App\\Models\\User', 209, 'MyApp', '675af18f98cae6374ff31c1c3a7c230cd43526acaf47e9e38c1d76939871106b', '[\"*\"]', '2025-09-20 02:19:27', NULL, '2025-09-20 02:01:02', '2025-09-20 02:19:27'),
(10, 'App\\Models\\User', 210, 'MyApp', '7a288f800761cc0f3d00a97b75d79ce7e2d3e0643710beda6ab615f6b1584e84', '[\"*\"]', NULL, NULL, '2025-10-16 02:20:57', '2025-10-16 02:20:57'),
(11, 'App\\Models\\User', 211, 'MyApp', '214df82477cc55d14ef910c120283cb2adb5ae06aedcd7a51d4d3f94bbd09b5d', '[\"*\"]', NULL, NULL, '2025-10-16 02:21:39', '2025-10-16 02:21:39'),
(12, 'App\\Models\\User', 211, 'MyApp', '10c8a313f3c71f964d6035c449bceb9ef4f24bc6167dc2b79f20468b0ad8a096', '[\"*\"]', NULL, NULL, '2025-10-16 02:27:29', '2025-10-16 02:27:29'),
(13, 'App\\Models\\User', 211, 'MyApp', 'c074a9259a229e0aa852e6e9aa64fd544bc499187df10c77ff02a183031c1d50', '[\"*\"]', NULL, NULL, '2025-10-16 02:27:31', '2025-10-16 02:27:31'),
(15, 'App\\Models\\User', 211, 'MyApp', '87c20dd896293699f726d5ece87e04e5eb6c0d8065265f08f1552f5655ca83bc', '[\"*\"]', '2025-10-18 09:26:09', NULL, '2025-10-18 00:09:19', '2025-10-18 09:26:09'),
(16, 'App\\Models\\User', 211, 'MyApp', '1f2faa5c09a149ef2db88940f8f649c81606e3c36b3ad9fd1a446245bd37d0e2', '[\"*\"]', NULL, NULL, '2025-10-19 23:23:22', '2025-10-19 23:23:22'),
(17, 'App\\Models\\User', 211, 'MyApp', '1521f3f8e4319dbfc1d55e26e868da21f1028bbda4c3af6844a65cefcfe9ea98', '[\"*\"]', NULL, NULL, '2025-10-19 23:27:55', '2025-10-19 23:27:55'),
(18, 'App\\Models\\User', 211, 'MyApp', '9d87a79704c632047848764ac13d930d7d1d835202b358d6ee8057052b5ee60e', '[\"*\"]', NULL, NULL, '2025-10-19 23:28:09', '2025-10-19 23:28:09'),
(20, 'App\\Models\\User', 211, 'MyApp', '1371f04851d794e910802610b258a9dd95a13a7a25ec2d47be47dd10c37c38a3', '[\"*\"]', '2025-11-24 02:46:11', NULL, '2025-10-19 23:29:59', '2025-11-24 02:46:11'),
(21, 'App\\Models\\User', 211, 'MyApp', 'ae81363eef27fd611f951132b7b8dfd3ed01933c87b3e565dc5ea85b7611c9ea', '[\"*\"]', '2025-10-24 00:46:28', NULL, '2025-10-22 08:38:17', '2025-10-24 00:46:28'),
(22, 'App\\Models\\User', 212, 'MyApp', '7500132fb1cd7d63546b01e53ef2b62b4d1f842927d030598775e6c84a10c6bb', '[\"*\"]', '2025-10-24 09:18:26', NULL, '2025-10-24 09:10:01', '2025-10-24 09:18:26'),
(23, 'App\\Models\\User', 211, 'MyApp', '593706ad938592fed9aca76f793dac91ef9b015b3c4af77774317f6655504d50', '[\"*\"]', NULL, NULL, '2025-11-21 02:01:14', '2025-11-21 02:01:14'),
(24, 'App\\Models\\User', 211, 'MyApp', '531a27c70a86cce81b64f88ae35515e4976cd2aafe52c368ef93f7965b857d7c', '[\"*\"]', NULL, NULL, '2025-11-21 03:48:25', '2025-11-21 03:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `caption` text DEFAULT NULL,
  `type` enum('image','video','carousel') NOT NULL DEFAULT 'image',
  `media_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`media_metadata`)),
  `status` enum('active','deleted','flagged') NOT NULL DEFAULT 'active',
  `visibility` enum('public','private','friends') NOT NULL DEFAULT 'public',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `uuid`, `user_id`, `caption`, `type`, `media_metadata`, `status`, `visibility`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'f2c17a64-9142-47a3-aba0-882b196c3f80', 12, 'Asperiores qui velit eius beatae voluptates doloribus et.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/003388?text=aut\\\",\\\"size\\\":1468}\"', 'flagged', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(2, 'c583fbff-959a-4925-9d46-1e9450464b45', 13, 'Numquam ea perferendis earum qui.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0055cc?text=eum\\\",\\\"size\\\":4911}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(3, '8e6ead1e-f4fd-429b-803b-c67fb04136c9', 14, 'Aliquam facere quae pariatur molestias animi.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00bb55?text=non\\\",\\\"size\\\":1788}\"', 'deleted', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(4, '737c8cbf-407f-4537-8bac-84b7091c3ec0', 15, 'Illo non qui nihil fugit qui.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/008833?text=maiores\\\",\\\"size\\\":851}\"', 'flagged', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(5, '24bf6e7b-a519-47b7-a4bf-84387ad6a2c4', 16, 'Facilis ullam rerum amet fugit quia.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ff55?text=temporibus\\\",\\\"size\\\":4776}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(6, '5c0c8831-3be5-49df-b99d-ab9783911310', 17, 'Et quia eum doloremque sint.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00aa66?text=maxime\\\",\\\"size\\\":3749}\"', 'flagged', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(7, '3c54ef72-3f04-474a-af5d-8ba233816ff8', 18, 'Ullam ea reprehenderit quam veniam.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0022bb?text=voluptates\\\",\\\"size\\\":2159}\"', 'active', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(8, '0814cdd8-6494-4a37-8ee6-53f6457bdeb8', 19, 'Deleniti rerum quia quia provident provident.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ff00?text=itaque\\\",\\\"size\\\":4171}\"', 'active', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(9, '891e794b-7653-41d6-8f57-4c5d87295b47', 20, 'Amet ab est neque quaerat.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ee33?text=qui\\\",\\\"size\\\":427}\"', 'deleted', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(10, 'b1346cc6-db38-490b-9c4f-e24b86d47c8d', 21, 'Quas consequatur in sint ad non ea.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00eeee?text=et\\\",\\\"size\\\":3012}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(11, 'd7af16f3-1941-4f06-8e5a-c31d8dea4acc', 22, 'Dolore quidem aliquid voluptatem nemo.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00bb44?text=ea\\\",\\\"size\\\":4091}\"', 'active', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(12, '3496b706-71be-4ece-bb6f-bf95c7f8ad94', 23, 'Ut accusantium ut rerum vel ut.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ddff?text=ut\\\",\\\"size\\\":3712}\"', 'active', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(13, 'bb4303f6-041d-4163-850c-414d3dd3d29c', 24, 'Non id soluta quia et quisquam.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ee66?text=eaque\\\",\\\"size\\\":686}\"', 'active', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(14, 'f905d021-d8bc-489c-9bc7-a8fcf4d822ec', 25, 'Et minus quis ea in dignissimos provident.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/007744?text=quae\\\",\\\"size\\\":472}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(15, '05b69dbb-3590-4bec-ac4f-7de7820e6530', 26, 'Accusamus est id nemo harum.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00aa44?text=voluptates\\\",\\\"size\\\":1002}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(16, '43a6b5b1-9e78-4df1-9867-87068e874c9f', 27, 'Iure consequuntur explicabo atque et sint quas eaque.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00aa55?text=aliquam\\\",\\\"size\\\":768}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(17, '1af38a02-55ae-4652-a7b5-3a699e3134f9', 28, 'Reiciendis saepe sunt magni dolorem facere reiciendis consequatur.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/001133?text=tenetur\\\",\\\"size\\\":1357}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(18, '88ae2d8e-a7c0-4380-b9b1-c1cf9f61da10', 29, 'Modi et quibusdam ea provident sit nemo nesciunt.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/004444?text=quis\\\",\\\"size\\\":4904}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(19, '51c7632d-8a5a-4b27-92fd-bb52cd195445', 30, 'Eaque sed voluptatibus distinctio assumenda temporibus corporis.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ff00?text=voluptatibus\\\",\\\"size\\\":3392}\"', 'deleted', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(20, '3733f60d-47da-4aff-ab95-5a416d8e2fcf', 31, 'Debitis cum quis laborum cum.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0066ff?text=dolor\\\",\\\"size\\\":435}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(21, '3a3de694-fdbb-4de2-8507-5c7703f31c72', 32, 'Autem odio tempore quos aliquid.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0022bb?text=quaerat\\\",\\\"size\\\":3470}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(22, '84ef5659-f125-47c7-a150-8899573830be', 33, 'Provident doloribus animi nemo odit consequatur ex.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/002277?text=aliquid\\\",\\\"size\\\":990}\"', 'flagged', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(23, 'bec107cf-4743-467d-9cda-84f96bc97799', 34, 'Et minima rem velit impedit possimus inventore eos.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ccdd?text=modi\\\",\\\"size\\\":4246}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(24, 'c2f043c1-9a59-4af9-84c9-8d11ecc85401', 35, 'Sint sit quas non officia.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0011aa?text=eligendi\\\",\\\"size\\\":918}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(25, '2a7f2952-7c64-4209-8140-aff5a53536f2', 36, 'Esse quaerat quod veritatis sapiente facilis ipsa molestias.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/001111?text=consectetur\\\",\\\"size\\\":3664}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(26, 'f21de7a0-fd8d-479d-af8e-0d84b031efd0', 37, 'Ipsum rerum et aut consequatur odio sed.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/005577?text=ut\\\",\\\"size\\\":2992}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(27, '7184a0bb-bbdd-46f0-a97f-f1b6f07a11d7', 38, 'Esse ut harum magnam molestiae doloribus quis.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ccbb?text=similique\\\",\\\"size\\\":2841}\"', 'flagged', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(28, '25655c1e-5507-4d15-8cf5-8e267d5e62b5', 39, 'Nemo qui ab laborum dolorum nemo.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/007722?text=perspiciatis\\\",\\\"size\\\":1447}\"', 'active', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(29, 'c187ba3d-d82f-433a-8ee8-6d1e2faeae9b', 40, 'Velit in recusandae tenetur modi nulla.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/002277?text=quos\\\",\\\"size\\\":4577}\"', 'flagged', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(30, '34cb41a2-cb9f-4677-9d17-27c83060b2c9', 41, 'Dolorem quae nostrum totam et reprehenderit beatae.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/002222?text=quod\\\",\\\"size\\\":2648}\"', 'deleted', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(31, '8a42b20c-20eb-4ee3-a027-7d4a1cfb587a', 42, 'Alias vel architecto et quo.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0022aa?text=incidunt\\\",\\\"size\\\":3757}\"', 'active', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(32, '379c38f4-3be9-473d-a26f-171dc68ed04b', 43, 'Aliquid in quasi eveniet voluptatem reprehenderit.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ddaa?text=quis\\\",\\\"size\\\":2802}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(33, 'acf81fdd-8876-4e4d-bc90-3d4c2fb548a0', 44, 'Consectetur consequatur facere qui sunt ut quia nihil dolorem.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00bb33?text=facere\\\",\\\"size\\\":3402}\"', 'active', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(34, 'f9fced18-a9ce-4202-ab57-9dfb885ea965', 45, 'Unde nihil quibusdam reiciendis at officiis qui et est.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ddff?text=qui\\\",\\\"size\\\":1453}\"', 'flagged', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(35, 'c5653849-9db7-4f25-97ea-e833a49058c1', 46, 'Fugit saepe voluptate tenetur quaerat rerum labore.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/007711?text=voluptatem\\\",\\\"size\\\":3977}\"', 'active', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(36, '283d2cf4-628c-47f8-86f4-ad978e086e10', 47, 'Fuga qui enim aut sunt quam consequuntur corporis.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/008822?text=dolorum\\\",\\\"size\\\":2111}\"', 'flagged', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(37, '6dc1173f-ac15-4712-8f02-9ec967259541', 48, 'Nisi saepe voluptatem soluta expedita fuga.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0077bb?text=est\\\",\\\"size\\\":1688}\"', 'deleted', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(38, 'eecf1b86-efb7-4e46-b266-6a4a545922a0', 49, 'Voluptatem cum rerum consequuntur sit reprehenderit sint temporibus.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0011bb?text=nisi\\\",\\\"size\\\":3106}\"', 'deleted', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(39, 'f3e9d387-fd2c-4fdf-bf3d-4e957d35a0a1', 50, 'Repellendus voluptas velit facilis.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/008899?text=et\\\",\\\"size\\\":695}\"', 'active', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(40, 'c3ce26d0-77ff-441a-9c98-006cbae8d293', 51, 'Quasi ut est consequatur voluptas odit.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/000055?text=quod\\\",\\\"size\\\":1546}\"', 'active', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(41, 'c6fc326d-b76f-42a1-a1a6-f4cf79c3f5ed', 52, 'Labore iste aut sequi et doloribus expedita quidem.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ccbb?text=sunt\\\",\\\"size\\\":1027}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(42, '341e980c-5b2a-4fd3-8a2e-92718397ff6b', 53, 'Velit vitae dolorum laborum.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ccaa?text=et\\\",\\\"size\\\":2022}\"', 'active', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(43, '24d8e7a7-5f59-47d8-889e-d86ae29a404f', 54, 'Autem excepturi et dolor unde beatae impedit ut ullam.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0000cc?text=aut\\\",\\\"size\\\":2405}\"', 'active', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(44, '647ce026-52da-4390-b108-8f65f6a70572', 55, 'Et temporibus dolorem consequatur est commodi molestias sequi aliquam.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/004444?text=nostrum\\\",\\\"size\\\":2707}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(45, '501a9d8f-fac2-42d5-ad28-63e83928add5', 56, 'Minima aut et excepturi non aut et doloribus.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0000bb?text=est\\\",\\\"size\\\":3371}\"', 'active', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(46, '7e10860f-86a7-48aa-b285-0ac0522550fa', 57, 'Voluptates et laboriosam fugiat expedita.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/001199?text=magnam\\\",\\\"size\\\":3088}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(47, '486a22d8-f560-461c-85cf-add2fecce296', 58, 'Sequi modi et expedita aut corporis sint quibusdam.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0033ee?text=sunt\\\",\\\"size\\\":1990}\"', 'flagged', 'private', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(48, '3e854a06-3f05-4640-b47c-9cfaa61efd09', 59, 'Voluptatem sint rem ex corrupti non.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/000011?text=et\\\",\\\"size\\\":4304}\"', 'deleted', 'friends', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(49, 'e24485db-61ac-4ba9-a065-f5120dd450ca', 60, 'Consectetur voluptas maiores vero non pariatur et assumenda laudantium.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/005577?text=qui\\\",\\\"size\\\":3340}\"', 'active', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(50, 'b86215ff-8971-4923-a1ac-0f513e4b5db8', 61, 'Quasi esse error ea nemo omnis enim.', 'image', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00cc00?text=alias\\\",\\\"size\\\":4876}\"', 'deleted', 'public', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(51, '8e51c92d-c02d-42ca-a004-4357f133964c', 190, 'Autem numquam dignissimos vel facilis quaerat.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0044cc?text=temporibus\\\",\\\"size\\\":2727}\"', 'deleted', 'private', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(52, '99e653e7-5893-416e-a67f-79446ea8a0c8', 192, 'Numquam cum repellendus reprehenderit facere dignissimos quia.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0066ff?text=atque\\\",\\\"size\\\":135}\"', 'flagged', 'friends', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(53, 'fb1b9f56-d413-4d42-979f-6c66d1ecb40e', 194, 'Quia quia et quisquam in alias voluptas qui.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/0000ee?text=iusto\\\",\\\"size\\\":1431}\"', 'active', 'friends', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(54, 'd07479ca-5c97-46b5-9a88-16b735498091', 196, 'Aperiam veritatis soluta odio accusantium.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/006644?text=soluta\\\",\\\"size\\\":2521}\"', 'active', 'private', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(55, '8d68faf7-703f-460a-94ff-d80ebb320584', 198, 'Iusto libero quo aspernatur quis.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/003300?text=at\\\",\\\"size\\\":1668}\"', 'active', 'public', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(56, '56c24e45-ce58-40aa-b515-e145983991d5', 200, 'Commodi et amet explicabo optio dolores placeat occaecati placeat.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/00ddff?text=occaecati\\\",\\\"size\\\":1114}\"', 'flagged', 'private', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(57, '19cd8bca-567d-4001-a4cd-4d8643f00402', 203, 'Error qui aut accusantium non.', 'video', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/005577?text=in\\\",\\\"size\\\":1193}\"', 'flagged', 'friends', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL),
(58, '625f6e3c-4675-4aab-be56-1732638c2c53', 205, 'Sed molestiae voluptate id fugit ullam dicta laborum.', 'carousel', '\"{\\\"url\\\":\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/640x480.png\\\\\\/000033?text=voluptatem\\\",\\\"size\\\":2729}\"', 'deleted', 'friends', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `status` enum('pending','approved','rejected','deleted') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `uuid`, `user_id`, `title`, `slug`, `description`, `price`, `stock`, `images`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '991dda32-6f72-352b-aaf0-ec5b59bc5736', 97, 'qui vel occaecati', 'qui-vel-occaecati-M2qk7', 'Quidem reiciendis consectetur aut. Aut corrupti quae consequatur atque vel tenetur aliquam. Ipsam voluptas qui voluptatem dolore. Eius ut nobis id.', 6400.48, 22, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0055ee?text=product+nobis\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006666?text=product+delectus\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(2, '9ec43313-bf92-325e-8162-d24f73b1627a', 98, 'adipisci non ad', 'adipisci-non-ad-obgpm', 'Omnis alias minus explicabo omnis qui. Maxime aperiam doloribus eaque.', 9929.59, 36, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb33?text=product+ipsum\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00cc99?text=product+libero\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(3, '46daa65e-cbba-33ae-ad4a-b6804d81852b', 99, 'nulla quae corporis', 'nulla-quae-corporis-a2YHk', 'Quibusdam et necessitatibus deserunt accusamus magni et non. Eveniet enim occaecati asperiores accusantium itaque. Atque omnis est eum minima deleniti vero aut. Inventore aut in consequatur reprehenderit soluta aperiam at. Doloremque perspiciatis alias velit odit.', 9404.09, 9, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022ee?text=product+officiis\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb44?text=product+aut\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(4, 'ee52a197-3f1f-329a-a26d-69dd4f0057d4', 100, 'non esse quo', 'non-esse-quo-nJhX1', 'Quis dicta placeat vel delectus quibusdam aperiam. Maxime consequuntur atque aliquid laboriosam. Aut vel et architecto qui eum occaecati inventore.', 9869.87, 65, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/001111?text=product+quos\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bbbb?text=product+similique\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(5, '89a43fcb-d906-3ab8-b4c8-4eadc3358cad', 101, 'ipsa vitae magni', 'ipsa-vitae-magni-VYxCj', 'Aut et qui aut adipisci dolorem est. Ut aut voluptatem expedita et quae vitae earum. Assumenda voluptatem et eum deleniti corrupti consectetur. Corrupti ea et deleniti debitis aut.', 9941.53, 20, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/002277?text=product+enim\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006677?text=product+eaque\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(6, '22fe0267-ec83-390e-ae3c-ec27e5d871ab', 102, 'eaque quos accusamus', 'eaque-quos-accusamus-EvWBC', 'Ut architecto ea et quia aut voluptas doloribus. Et eaque aspernatur quod adipisci ex ad. Id sit et deserunt aut voluptas fugit. Voluptas sed ut dignissimos.', 9212.81, 90, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00dd00?text=product+minus\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0011ee?text=product+expedita\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(7, '1e823fa8-2466-388f-b3fc-3fa0828d2afb', 103, 'ea corrupti non', 'ea-corrupti-non-5p4GK', 'Deserunt tempora voluptatem ducimus sit animi. Et possimus vitae deserunt maxime non et ea. Sunt tempore optio odit accusamus commodi. Animi et et provident rerum est reiciendis et.', 3151.73, 38, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/000044?text=product+eligendi\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0033bb?text=product+nemo\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(8, '012df5f4-f6df-36b0-a3aa-078824863111', 104, 'libero commodi facilis', 'libero-commodi-facilis-a1ZAh', 'Nisi id fugit libero autem. Aspernatur dolorum consequatur est ipsam repellat veniam. Eligendi iure commodi voluptatem beatae. Inventore velit suscipit quae est aut est eum.', 4670.27, 54, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/004411?text=product+alias\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006644?text=product+libero\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(9, '2d5872e6-e79a-329a-b6b0-25905e545181', 105, 'sit sunt maiores', 'sit-sunt-maiores-Qhca6', 'Rerum consectetur ipsum accusantium placeat molestias ad quae. Rerum est in maxime amet beatae labore et.', 1227.15, 81, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0011ee?text=product+rerum\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006677?text=product+esse\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(10, '56e6d904-6c5f-3eff-9330-06de279534a2', 106, 'occaecati quia iusto', 'occaecati-quia-iusto-AlJg4', 'Iure sunt debitis aspernatur aut animi vero beatae id. Inventore molestias deserunt eum omnis pariatur sapiente autem et. Libero eligendi voluptatum blanditiis quia.', 8289.77, 27, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb33?text=product+qui\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ffdd?text=product+non\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(11, 'f1d84b07-a644-3391-9d2f-243afc2d128b', 107, 'est iure perferendis', 'est-iure-perferendis-kQswa', 'Totam est est accusantium qui laboriosam sed officiis. Nostrum quaerat commodi magni nobis repudiandae consequatur. Repellendus odit rerum vitae et.', 737.06, 33, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022bb?text=product+ut\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00cc33?text=product+sit\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(12, 'd7eb027c-fc0b-354c-9acc-66556b2ab327', 108, 'illo reprehenderit accusamus', 'illo-reprehenderit-accusamus-zLXUP', 'Voluptas veritatis odio inventore et dolorem suscipit eum. In odit totam blanditiis reprehenderit dignissimos. Quam dolores quis repellendus minima officia.', 4011.30, 30, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb44?text=product+et\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/003388?text=product+quia\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(13, 'c68e90c5-5ab6-3dfb-a822-af33a62d2d34', 109, 'perspiciatis sit consequatur', 'perspiciatis-sit-consequatur-ApG6B', 'Quas eligendi dicta incidunt sunt totam nihil. Pariatur voluptatem eum doloremque eaque architecto.', 8018.75, 78, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0000cc?text=product+qui\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007744?text=product+doloremque\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(14, '7cd8749d-b314-3ac7-a294-60e07be71535', 110, 'ipsa illum soluta', 'ipsa-illum-soluta-u4DlM', 'Tempora ullam a aut aut adipisci. Rerum et molestias a magni nulla qui. Animi dolorem consequatur molestiae quia fugit. Labore voluptates odit odit molestias voluptas.', 5933.55, 94, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/005500?text=product+minus\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00dd44?text=product+quibusdam\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(15, 'e3ed163f-7751-3592-8f4c-45f686d3bfcd', 111, 'non nihil est', 'non-nihil-est-htWvd', 'Eum qui rerum eum molestiae aperiam blanditiis. Voluptate qui quis quia veritatis eos vero adipisci. Neque doloremque asperiores accusantium alias enim eveniet.', 6344.53, 12, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/003399?text=product+ut\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022cc?text=product+dolore\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(16, 'a7d5d761-624a-37ee-80fa-d9df33f68ce0', 112, 'veritatis consectetur corporis', 'veritatis-consectetur-corporis-pMeQb', 'Natus natus similique ut minus sunt. Dolorum fugit consectetur voluptatem perferendis sed et esse esse. Est iste ut possimus voluptatibus ducimus a. Quibusdam eaque molestiae maxime voluptas.', 4432.13, 48, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ee99?text=product+commodi\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0000ee?text=product+aut\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(17, '264f44b3-3e00-3d73-87d1-231b78441764', 113, 'voluptas aut numquam', 'voluptas-aut-numquam-lzdta', 'Quaerat suscipit consequatur incidunt aspernatur. Suscipit rerum facere tempore beatae voluptatem non. Veniam et sunt ipsam repellendus et laudantium dolorum.', 4843.03, 58, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007766?text=product+voluptatem\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00dd00?text=product+amet\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(18, 'ee5549c8-2e14-34b8-a6c6-29d4579f86c7', 114, 'officia quia sit', 'officia-quia-sit-130PH', 'Quam consequuntur voluptas harum sed et aliquam. Qui voluptatibus ut quis vel ut ducimus. Aperiam sit iure ut provident cupiditate nobis earum.', 3101.75, 62, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0099dd?text=product+et\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00eeee?text=product+quisquam\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(19, 'f1f9b0ac-cc88-36e1-b9cf-5a1d6d2acca4', 115, 'nisi soluta eos', 'nisi-soluta-eos-tvN0c', 'Error tempore aut et doloremque facere aperiam placeat. Aliquam error nihil autem repudiandae id totam iure. Magni nesciunt adipisci tempora asperiores at ipsa molestiae.', 3861.58, 10, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00aadd?text=product+esse\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0000cc?text=product+voluptatibus\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(20, '60398a4f-c947-33db-8dfa-d01e63a38e65', 116, 'et vero quidem', 'et-vero-quidem-szKY8', 'Nihil ut hic aliquam dicta deserunt accusamus dolores totam. Architecto sed deleniti repudiandae iste voluptas eveniet. Vitae dicta aut unde labore voluptatem.', 8599.26, 46, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ff44?text=product+id\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/009966?text=product+eum\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(21, 'fde82c13-f598-365b-ac31-625c977793b8', 117, 'deleniti similique omnis', 'deleniti-similique-omnis-mYO4H', 'Et alias est repudiandae ipsum voluptatem animi et vel. Ab aut quam consequuntur et libero fugit asperiores. Cumque debitis architecto sequi consequatur nesciunt.', 3449.63, 4, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/009955?text=product+delectus\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ee55?text=product+omnis\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(22, '70c1ba99-bcac-30e6-b2b3-dd75efc819f0', 118, 'tempore explicabo quia', 'tempore-explicabo-quia-B9yFV', 'Porro et ipsum rerum qui natus officiis earum. Fugiat natus laborum velit voluptatum commodi facilis rerum. Dolorem et et ea qui.', 5568.16, 75, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00cc55?text=product+fuga\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006677?text=product+quas\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(23, 'ff8264ae-c68a-3a74-a4ac-588ad87d2e04', 119, 'accusantium aut molestiae', 'accusantium-aut-molestiae-udyMv', 'Dolor aut quod at optio corrupti sint id. Quos autem voluptatem ratione itaque labore. Autem qui blanditiis vel. Consequuntur illum laudantium occaecati perferendis quisquam asperiores sit. Eveniet incidunt ad placeat.', 7487.87, 33, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ee66?text=product+illo\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00aa44?text=product+nulla\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(24, '81a4a79f-2fb8-3b3d-a277-acbf64807ea1', 120, 'corporis unde aperiam', 'corporis-unde-aperiam-ZbsGq', 'Iure ducimus in quia. Quia cupiditate inventore cum eum enim harum doloremque. Et ut aut aut non.', 5993.85, 64, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007700?text=product+laudantium\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/002277?text=product+quo\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(25, 'c29ffe8b-138b-3c7b-bb0b-bcdcbde87e17', 121, 'sit officiis facere', 'sit-officiis-facere-6ijqJ', 'Rerum maiores dolorem sunt et. Voluptatem eveniet repellendus aut iste cumque. Ex explicabo laboriosam nihil ut perferendis.', 3320.86, 51, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0033ff?text=product+ab\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0011aa?text=product+eum\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(26, '4a4afb55-1cae-3154-a883-4f61be6d252c', 122, 'perferendis est ipsam', 'perferendis-est-ipsam-aMtDg', 'Eveniet aspernatur similique error cumque. Ipsam totam rerum iste ut ut non. Aut nihil aut cupiditate consequuntur.', 477.43, 11, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0055dd?text=product+id\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/005544?text=product+qui\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(27, 'e4a3cd26-b9d1-3b01-b5a5-69fe1b304f86', 123, 'sit optio accusantium', 'sit-optio-accusantium-DpL2V', 'Delectus necessitatibus pariatur error esse qui excepturi qui enim. Et et accusamus deleniti. Rerum velit laboriosam earum aliquid minus.', 8149.78, 47, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/000088?text=product+sunt\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00aa77?text=product+sapiente\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(28, '90bc77d2-f013-36d6-a573-a6ad5c916fb6', 124, 'rerum dolorem provident', 'rerum-dolorem-provident-0AB06', 'Qui sunt nobis doloribus quas. Qui quo sunt vero. Aliquam doloribus nostrum id dicta ipsum necessitatibus maxime.', 3517.33, 33, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/004411?text=product+atque\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0077cc?text=product+consequuntur\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(29, '4bf17966-4d70-3bfd-8ebd-2884e03f60f3', 125, 'inventore fugit beatae', 'inventore-fugit-beatae-PQfWr', 'Sit dolor voluptates ratione sed. Magnam et provident et voluptatum aut aliquid eaque. Ut odio iusto delectus illum et aliquid. Voluptas deleniti repellendus officiis consequuntur.', 455.72, 39, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022ff?text=product+cum\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00cc33?text=product+sunt\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(30, 'fd9c25ed-50a0-3658-8af0-482395636f71', 126, 'cum quia earum', 'cum-quia-earum-6oKuQ', 'Pariatur doloribus illum nihil esse. Inventore delectus ut consequatur tempora. Fugiat ducimus doloribus nisi vitae nulla eaque. Aperiam est animi odit.', 4731.74, 32, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022ff?text=product+aliquid\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb00?text=product+sequi\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(31, 'ca8a5fb0-1bde-35f6-b088-56d58bd67653', 137, 'error esse nisi', 'error-esse-nisi-TaHC1', 'Dolor aliquid repellendus rerum adipisci odio in voluptatem. Dolore quia dolores fuga hic sint eos.', 6530.37, 10, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006666?text=product+mollitia\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ee44?text=product+odit\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(32, 'e26714fc-a50d-3ed9-ba28-1591fc9d3434', 139, 'architecto explicabo veritatis', 'architecto-explicabo-veritatis-JshlL', 'A animi est laudantium sed nemo rerum. Totam cumque corporis est quaerat. Quos repellendus debitis repellendus placeat officia ullam.', 355.39, 17, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/001144?text=product+esse\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/004466?text=product+qui\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(33, '9f095ff2-d9f0-3ef9-90e1-d36436c0d7cd', 141, 'consequatur voluptates dolorum', 'consequatur-voluptates-dolorum-DcMU8', 'Provident veniam eveniet et inventore tempora sed animi. Exercitationem expedita nulla eum nobis error dolor quis. Rem voluptatem molestias harum voluptate sit voluptatibus voluptatem. Quis minima quia eos veniam.', 9056.04, 5, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007766?text=product+tenetur\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0033bb?text=product+sequi\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(34, '77d077d5-1546-35e1-86c4-e0b88abb7101', 143, 'perferendis cumque facere', 'perferendis-cumque-facere-P3nAi', 'Sunt id ipsam odio sint ex nulla voluptatem. Maiores vero sed placeat iure sit.', 4768.99, 17, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006600?text=product+aperiam\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/001166?text=product+a\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(35, '1a111a2c-a985-3913-8068-c2348200fefa', 145, 'delectus eius molestiae', 'delectus-eius-molestiae-oqO9M', 'Quasi commodi autem itaque soluta. Quo voluptatem alias rerum doloremque perspiciatis dignissimos exercitationem. Deleniti mollitia voluptatibus reprehenderit architecto similique beatae.', 9961.19, 14, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007744?text=product+et\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022aa?text=product+qui\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(36, '17977451-39ad-31ce-ba5d-25bcf6f37a1d', 147, 'a illum qui', 'a-illum-qui-sFJmV', 'Dolorem aliquid libero debitis cum. Quas sit sit debitis minima est dolor accusamus. Libero corporis eveniet alias consequatur laboriosam nesciunt. Amet tempore sint sapiente similique nobis.', 5512.95, 87, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/001144?text=product+quae\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0066aa?text=product+sint\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(37, 'ad0f21c7-cc3d-3f07-8b8a-fc866fc3a7f0', 149, 'et ratione totam', 'et-ratione-totam-wTXbS', 'Qui cumque porro voluptate et velit. Omnis dolor voluptates vitae sunt repudiandae architecto. Numquam saepe vitae numquam ad odit sit. Aspernatur culpa est nihil sit.', 595.24, 65, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/000000?text=product+quo\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007722?text=product+adipisci\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(38, '254fdd24-42fe-3f10-8c35-5374ab3ebe1b', 151, 'dolores ut ut', 'dolores-ut-ut-ZnBqG', 'Esse sed reprehenderit dolore aut ducimus quibusdam. Error aut consequatur ut molestiae vel asperiores voluptas. Quo deleniti sapiente sapiente natus deleniti architecto necessitatibus. Aut distinctio et et nemo dignissimos corrupti laborum molestias. Repellendus consequatur aperiam fuga illum quod qui.', 5216.36, 51, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/000011?text=product+deserunt\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/001155?text=product+cumque\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(39, 'f12c7a1d-a37c-3659-9ade-6ff37f0f24bc', 153, 'quos aut odit', 'quos-aut-odit-etYgE', 'Ut neque voluptatum consequatur eos commodi fuga quaerat quod. Quia cum et dolores voluptates. Explicabo facilis dolorem nihil.', 8798.59, 58, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bbff?text=product+in\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb88?text=product+ipsum\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(40, '90aa8128-9dec-3c81-879d-16f75179dcda', 155, 'laudantium in veniam', 'laudantium-in-veniam-IhW02', 'Voluptas non minus perferendis molestiae. Qui dolorem et numquam perferendis ab ut. Officia molestiae cupiditate adipisci nihil quaerat autem. Harum incidunt quidem distinctio et eveniet molestiae in.', 9817.57, 55, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0022dd?text=product+necessitatibus\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0088ee?text=product+architecto\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(41, 'cf8940fe-1dd8-388c-af34-21fc28dc6e7a', 157, 'commodi temporibus quo', 'commodi-temporibus-quo-UBoFS', 'Et voluptatibus nulla adipisci occaecati. Et consectetur neque quisquam sunt molestiae ut rerum. Eligendi expedita et maiores amet eum quisquam itaque. Dicta exercitationem odio labore perferendis magnam quae. Animi iusto porro dolores.', 257.41, 20, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/004411?text=product+eveniet\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0077ff?text=product+id\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(42, '445d321d-52ff-36ef-8981-e0e830052e85', 159, 'deserunt maxime fuga', 'deserunt-maxime-fuga-qdmwZ', 'Dolor necessitatibus quibusdam a ipsa expedita. Incidunt ipsam sint modi accusantium autem velit et. Ut quas natus labore quos.', 1466.97, 10, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00dd44?text=product+ipsum\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006655?text=product+consequatur\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(43, '5ab2082b-1adb-384d-bbef-aecfca462636', 161, 'sit voluptas animi', 'sit-voluptas-animi-3qF4S', 'Necessitatibus molestiae culpa veniam reprehenderit aliquam reprehenderit quibusdam. Error esse ut nihil fugiat.', 5028.50, 46, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007722?text=product+illo\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ff22?text=product+qui\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(44, '4d12a004-d4db-3418-adba-9d2aca111672', 163, 'aperiam officia earum', 'aperiam-officia-earum-5Q84u', 'Voluptas consequatur ipsa vero eos modi saepe. Possimus dolorem illo non qui. Nisi aut modi veritatis vel. Cum omnis ex voluptatem deleniti consequatur porro perspiciatis.', 9795.58, 17, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ccbb?text=product+aspernatur\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bb77?text=product+ad\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(45, '3ebbac34-6931-3789-af32-5482ffd548b8', 165, 'sunt aut occaecati', 'sunt-aut-occaecati-XVPzY', 'Consequatur commodi enim vel atque qui. Aliquid a aspernatur qui nisi. Tenetur quis minima error cum qui consequuntur.', 2781.00, 46, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/002211?text=product+mollitia\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0000bb?text=product+omnis\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(46, '0e8abdd9-1658-30d0-b621-72847b97920b', 167, 'sit assumenda error', 'sit-assumenda-error-YWSc1', 'Consequatur magnam facere occaecati et. Accusantium tenetur fugiat sed molestias omnis voluptatem. Dolores repellendus consectetur voluptas fugit eum harum cum qui. Sit deleniti omnis sed maxime.', 3541.63, 88, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/008833?text=product+officiis\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/005599?text=product+id\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(47, 'e1960fb6-50ab-3a8e-824f-9f7d34d8045a', 169, 'ea pariatur dolorem', 'ea-pariatur-dolorem-9jsod', 'Fugiat tempora qui qui aspernatur quo. Exercitationem quia sint occaecati blanditiis ducimus culpa magni.', 5878.48, 1, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/002211?text=product+nemo\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0066bb?text=product+dignissimos\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(48, '880359ee-18b2-3c6d-bc92-0fe30be883e0', 171, 'ducimus beatae quidem', 'ducimus-beatae-quidem-0WeiT', 'Quos amet ad dicta maiores incidunt voluptatem accusantium totam. Itaque optio voluptatem qui quia. Sequi quis maiores expedita dicta vero ut earum. Esse eum tempora id.', 1414.29, 22, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006699?text=product+veniam\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00bbbb?text=product+vitae\\\"]\"', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(49, '653304ac-ffc1-3738-9852-39257d0b4060', 173, 'pariatur nemo exercitationem', 'pariatur-nemo-exercitationem-scIXu', 'Est recusandae enim error est. Iste libero et illum possimus ratione repudiandae. Doloremque eaque ut dignissimos.', 5689.49, 75, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007711?text=product+esse\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/008866?text=product+qui\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(50, '5da7facb-ce5e-3beb-a296-e4f77b107ae6', 175, 'vero odio deserunt', 'vero-odio-deserunt-iBa0h', 'Laborum velit corporis dolores est nobis odit atque. Quidem ipsa aliquam omnis sed. Sit incidunt aut accusamus quia.', 7157.83, 24, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0066ee?text=product+velit\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/006644?text=product+dolorem\\\"]\"', 'deleted', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(51, 'e2017834-3995-3ee3-a331-c0cbc76a09ab', 177, 'temporibus reprehenderit eum', 'temporibus-reprehenderit-eum-gP1C2', 'Reprehenderit non enim maxime aut. Aut quaerat animi et similique. Occaecati tempora quia quas quis.', 7833.82, 69, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/007766?text=product+iste\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ff11?text=product+dolorem\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(52, 'f6c62842-13f3-38db-b987-d9fbc2859241', 179, 'et sit in', 'et-sit-in-mPKkE', 'A soluta quisquam doloremque provident praesentium facilis tenetur. Veritatis consequatur illum eveniet culpa eveniet nisi rem. Officiis provident qui occaecati qui. Optio ut aut sed.', 6240.33, 83, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0099dd?text=product+facere\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00ee88?text=product+dolores\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(53, '48c65087-daa7-3dd4-99b2-79e3ea339dd9', 181, 'sunt soluta neque', 'sunt-soluta-neque-4dIx4', 'Sed ut harum nisi nihil. Deleniti nemo qui eius eligendi dolorem aut voluptates. Itaque expedita soluta est earum adipisci. Maxime fuga iusto alias et rerum.', 6221.53, 84, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/002288?text=product+nemo\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00cc33?text=product+corporis\\\"]\"', 'pending', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(54, 'a89a6888-918d-3e97-928b-2f65a8ee308c', 183, 'ipsa voluptas itaque', 'ipsa-voluptas-itaque-IrGb3', 'Non corporis cupiditate culpa molestiae eos consectetur aut. Sequi ut fugit qui et ab veritatis at. Qui praesentium rerum aut error vitae tempore doloribus et. Architecto ut quo expedita non voluptatem.', 3848.68, 91, '\"[\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/0044cc?text=product+mollitia\\\",\\\"https:\\\\\\/\\\\\\/via.placeholder.com\\\\\\/800x800.png\\\\\\/00cc44?text=product+recusandae\\\"]\"', 'approved', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reels`
--

CREATE TABLE `reels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `video_url` varchar(1024) DEFAULT NULL,
  `cover_url` varchar(1024) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration_seconds` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','deleted','flagged') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reels`
--

INSERT INTO `reels` (`id`, `uuid`, `user_id`, `video_url`, `cover_url`, `description`, `duration_seconds`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '4bf28a3a-6fd7-321c-baf1-33e05d57d949', 62, NULL, NULL, 'Cumque temporibus consequatur similique veniam. Voluptatem nam iste veritatis deleniti quisquam doloribus. Ex ad consequatur omnis aut sunt nihil veniam.', NULL, 'flagged', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(2, '7a277b7e-553a-34f8-a49b-6d9b1d9c8534', 63, 'http://lind.com/ab-maxime-ut-possimus-possimus', 'http://pagac.com/quo-et-quo-facere-corporis', NULL, 90, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(3, '140a5bb7-448a-321d-8f3a-ace18d124b9e', 64, 'http://www.oconner.com/iste-quia-illum-et-consectetur-eveniet-magni-doloribus-eius.html', 'http://west.com/aut-reprehenderit-voluptatem-expedita-ut-itaque-aut', NULL, NULL, 'flagged', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(4, '68886808-51f6-3b74-8cec-0ad9c45283e1', 65, NULL, 'http://schowalter.com/architecto-cumque-soluta-hic-possimus-earum', 'Reprehenderit necessitatibus sed odio explicabo. A velit temporibus et enim. Aut quis id dolorem rerum et.', NULL, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(5, '7d40201a-2434-34bf-90d3-ff5ba4fef75e', 66, NULL, NULL, 'Necessitatibus ut sit dolorum quasi aut nam a. Facilis laboriosam labore totam numquam magni consequatur. Aspernatur harum exercitationem nam modi.', NULL, 'flagged', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(6, '9725988c-949c-35c6-858e-8f64bede9b49', 67, NULL, 'http://mayer.net/', NULL, NULL, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(7, '51a1c5c2-8344-3eb9-bc8f-72982209cc77', 68, 'http://kemmer.com/harum-ut-aut-esse-iusto-quo', NULL, NULL, 134, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(8, 'e6b3ecef-d75c-314a-9a8e-a4d13ac42793', 69, NULL, NULL, NULL, 291, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(9, 'fcd14c3c-3410-3ca2-8e37-1f1c23f78bc1', 70, 'http://www.lemke.info/', NULL, NULL, 9, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(10, '0fe65213-79d7-3ee3-960a-a8059ba526e4', 71, 'http://www.boyer.net/voluptate-neque-similique-eligendi-nisi-praesentium-aspernatur', 'http://www.brekke.com/et-alias-ut-nihil-magnam-explicabo-et-consequatur', 'Dolor eum quis hic. Ipsa illum labore est eum. Rerum occaecati qui est alias.', 123, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(11, 'f6c49dcb-a338-3641-a6b9-194b92905bcb', 72, NULL, NULL, NULL, 295, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(12, '2b826cad-eca3-362c-88fb-4c562de325ee', 73, NULL, 'http://pouros.info/delectus-eum-eum-laudantium-sequi-autem-explicabo.html', 'Mollitia aut qui ut sint atque officia quod. Minus ut commodi ipsam ipsam aut. Assumenda rerum eos autem repellendus. Rerum sunt ut quo eos ea et rerum.', NULL, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(13, '6009d428-5ed2-3431-b99c-72a6127ea0e7', 74, NULL, NULL, NULL, NULL, 'flagged', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(14, '27bf56c2-c2d0-39f6-893e-286119ecff13', 75, NULL, 'http://www.weber.org/quia-ut-vel-ducimus-unde.html', NULL, 75, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(15, 'd0625c09-4938-3066-93e5-b9ec1458be91', 76, NULL, NULL, NULL, 17, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(16, '3b6f5279-1c17-3410-81ae-b7d789b1a5ed', 77, NULL, 'http://www.schneider.com/et-ex-consequatur-culpa-iure-et-laboriosam.html', NULL, 73, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(17, '5d239b29-05c8-3450-8fe4-16ef9b458c18', 78, NULL, 'http://www.strosin.com/sit-soluta-quidem-quos-iste-animi-eligendi-aut-est', NULL, 30, 'active', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(18, 'c721e67a-b483-3d3e-a42a-403c7de62537', 79, 'http://www.schulist.biz/et-ut-in-et-eos-error-est-molestiae.html', NULL, NULL, NULL, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(19, '909afd14-d3df-3d59-a787-31aa5cc79a46', 80, 'http://www.adams.com/sit-asperiores-incidunt-adipisci-ut.html', NULL, 'Beatae rem sapiente numquam cum sint similique. Consequuntur necessitatibus vel sint ratione beatae sint voluptatibus. Nemo nobis omnis repudiandae quo. Aut amet vel quibusdam possimus est rerum repudiandae.', NULL, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(20, 'c5da9535-166f-30a9-bab9-33f8330f8c1e', 81, NULL, 'https://fritsch.com/ducimus-non-qui-eveniet-labore-sit-vel-rerum.html', NULL, 142, 'deleted', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reported_by` bigint(20) UNSIGNED NOT NULL,
  `reportable_type` varchar(255) NOT NULL,
  `reportable_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `status` enum('pending','in_review','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reported_by`, `reportable_type`, `reportable_id`, `reason`, `details`, `status`, `resolved_by`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 191, 'App\\Models\\Post', 51, 'Nihil aut tempore et.', NULL, 'in_review', NULL, NULL, '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(2, 193, 'App\\Models\\Post', 52, 'Quo veritatis vel minus tempore.', NULL, 'in_review', NULL, NULL, '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(3, 195, 'App\\Models\\Post', 53, 'Est ut et.', 'Est quia eum dolore delectus atque. Fugit aut pariatur non id qui. Porro voluptatem illum eos.', 'in_review', NULL, NULL, '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(4, 197, 'App\\Models\\Post', 54, 'Doloribus aut omnis enim quo.', NULL, 'in_review', NULL, NULL, '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(5, 199, 'App\\Models\\Post', 55, 'Et sint quia voluptatem ad.', 'Sunt veniam necessitatibus in dolores dolores dolores assumenda. Ipsum hic et velit explicabo voluptatem aliquid et. Doloribus quidem non aliquam aut tempora placeat.', 'pending', NULL, NULL, '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(6, 201, 'App\\Models\\Post', 56, 'Libero asperiores quas ut omnis iste.', NULL, 'dismissed', 202, '2025-09-08 09:06:43', '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(7, 204, 'App\\Models\\Post', 57, 'Esse atque nihil ea ut.', 'Porro pariatur vel consectetur aut saepe sed. Sit aut et numquam vel vero aut aut maxime. Accusantium earum dolore est itaque sunt dolor. Sequi omnis ducimus nisi ab aut voluptas. Suscipit explicabo voluptatem qui qui.', 'in_review', NULL, NULL, '2025-09-10 09:06:43', '2025-09-10 09:06:43'),
(8, 206, 'App\\Models\\Post', 58, 'Esse nostrum voluptatem dolores.', NULL, 'dismissed', 207, '2025-09-07 09:06:43', '2025-09-10 09:06:43', '2025-09-10 09:06:43');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-09-10 09:06:24', '2025-09-10 09:06:24'),
(2, 'moderator', 'web', '2025-09-10 09:06:24', '2025-09-10 09:06:24'),
(3, 'user', 'web', '2025-09-10 09:06:24', '2025-09-10 09:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0HHx1rptz5SIYFODgP0N6ThO4wBCFhKfrRng2XXB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidENweW5VOEsyaDNNRUd0V2dBRUVvOG11TDlxbjJCdDY5bmU3amp0OCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1761284929),
('cQxHS7gaxsWEt2ncEQ56BUGspYwO1lYie3vzTsKE', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUUJnbXdnMUQ5QjZOOVZTaGl1SEx1VjFFc1VRN3NmbklQb0RiQ1l2ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQvYWN0aXZpdHkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1760615529),
('fosipEhDUA1SGrNz3s6TgEz6kxhGnEQGOBWsigSI', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMXNVdFJmenlPaEVMdjFHTk52NzhLSFk1RnpuNFJlRUR0NTloNzV0TyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9pbnRlcmVzdHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1763622463),
('JkZY4k0db0xMcyIs8kcKqXidf9SPVrXJEPkkmGyh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUUdQcnhDWVRTUWpKWXlGMTVBMlphTnhqcUV5b3V5U1g5bXJkSk13UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1763976686),
('LGhvxtERF7C0ZQlz9U50G4iXz00y4pjj0JaGtiDJ', NULL, '127.0.0.1', 'PostmanRuntime/7.49.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmR2WmtERDRIblNrUko5aEF6djZMSE5xZnRDMnE5ang2Vk5iaDVaTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1760772072),
('O0OzEUEm31WjZiwzNiQzJxQZb6G1xmnWZZBvq9SQ', NULL, '127.0.0.1', 'PostmanRuntime/7.49.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicW9EcVB5VElPekRlWVBPeTNlUjF3RjZidFVOY2g3MGFjOHpCZmhSaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1760941180),
('otFMzq58zwNPd15qRIiy8B7IsKCsTd3tNV4kdLP9', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieGIzRUZGTzZyMmxjSldlUHBscm9sRjUwNGhlbEdJeFcweDRtaEYwNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9pbnRlcmVzdHMiO3M6NToicm91dGUiO3M6MjE6ImFkbWluLmludGVyZXN0cy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1763716436),
('UF6WHlNtSOtDhgJIJrzsgx5IrYwS3SiZQKIVQkxY', NULL, '127.0.0.1', 'PostmanRuntime/7.49.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUE4weDlucVZvQmdRWU00b1hmdlJVb1JCUldleGQxTEFYNVBPelZOdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1761122081);

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `media_url` varchar(1024) NOT NULL,
  `type` enum('image','video') NOT NULL DEFAULT 'image',
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_id`, `media_url`, `type`, `expires_at`, `is_archived`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 82, 'https://via.placeholder.com/1080x1920.png/00ddbb?text=people+omnis', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(2, 83, 'http://schneider.info/quis-facere-placeat-quos', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(3, 84, 'https://via.placeholder.com/1080x1920.png/009944?text=people+et', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(4, 85, 'https://via.placeholder.com/1080x1920.png/0055aa?text=people+laboriosam', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(5, 86, 'http://www.gottlieb.biz/expedita-commodi-perferendis-illum-eos-qui-quod', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(6, 87, 'http://kub.com/recusandae-quo-molestiae-omnis-natus-tempora-eaque', 'video', '2025-09-11 09:06:41', 1, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(7, 88, 'https://via.placeholder.com/1080x1920.png/005588?text=people+provident', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(8, 89, 'https://via.placeholder.com/1080x1920.png/00cccc?text=people+dolor', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(9, 90, 'https://via.placeholder.com/1080x1920.png/0011cc?text=people+labore', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(10, 91, 'https://via.placeholder.com/1080x1920.png/0077bb?text=people+placeat', 'image', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(11, 92, 'http://www.von.biz/beatae-sit-eveniet-laboriosam-beatae.html', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(12, 93, 'http://www.adams.biz/', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(13, 94, 'http://www.collier.info/', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(14, 95, 'http://weissnat.biz/est-ut-dolores-eum-velit-officia-quia', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL),
(15, 96, 'http://www.paucek.com/sit-est-amet-qui-doloremque-aliquid-possimus.html', 'video', '2025-09-11 09:06:41', 0, '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_seller` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `language_preference` varchar(10) NOT NULL DEFAULT 'en',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `messaging_privacy` enum('everyone','followers_only','no_one') NOT NULL DEFAULT 'everyone',
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `device_tokens` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_tokens`)),
  `reputation_score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `email_notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`email_notification_preferences`)),
  `account_privacy` enum('public','private','hidden') NOT NULL DEFAULT 'public',
  `password` varchar(255) NOT NULL,
  `profile_photo_path` varchar(1024) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `followers_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `following_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `posts_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_employer` tinyint(1) NOT NULL DEFAULT 0,
  `interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '["entertainment"]' CHECK (json_valid(`interests`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `username`, `occupation`, `name`, `email`, `is_seller`, `phone`, `email_verified_at`, `date_of_birth`, `gender`, `language_preference`, `two_factor_enabled`, `messaging_privacy`, `is_online`, `last_seen_at`, `device_tokens`, `reputation_score`, `email_notification_preferences`, `account_privacy`, `password`, `profile_photo_path`, `bio`, `status`, `is_blocked`, `is_verified`, `last_login_at`, `followers_count`, `following_count`, `settings`, `posts_count`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `is_employer`, `interests`) VALUES
(1, '7a45d6ab-10d1-49af-bd15-8ac5639fb064', 'user_1', NULL, 'Super Admin', 'admin@admin.com', 0, NULL, NULL, NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$nvZg6r0Y0cvP3Pu/ivBnZOCBWTqYRyiirpRL6IOzBDptaUCH5CufK', NULL, NULL, 'active', 0, 0, NULL, 0, 0, NULL, 0, NULL, '2025-09-10 09:06:25', '2025-09-10 09:06:25', NULL, 0, '[\"entertainment\"]'),
(2, '779596fa-517e-483f-9d19-aaabf1aab73d', 'user_2', NULL, 'Orpha Krajcik', 'schuster.hayden@example.net', 0, NULL, '2025-09-10 09:06:40', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Vel sed similique culpa asperiores ducimus possimus odit modi iusto aut ut nostrum amet.', 'active', 0, 1, '2025-09-09 04:29:35', 479, 119, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 133, 'GUHHyChr1b', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(3, '399d80fa-4f19-4fe8-834f-24aae429ac81', 'user_3', NULL, 'Vern Hammes', 'larue75@example.net', 1, '03692199176', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$nvZg6r0Y0cvP3Pu/ivBnZOCBWTqYRyiirpRL6IOzBDptaUCH5CufK', NULL, 'Laboriosam commodi quod vel cupiditate non assumenda ut tempore fugit.', 'active', 0, 1, '2025-08-11 05:33:15', 997, 436, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 85, 'PCoA62dogE', '2025-09-10 09:06:41', '2025-09-15 05:25:04', NULL, 1, '[\"entertainment\"]'),
(4, 'f51b6ad5-426b-4b87-8323-0c118c88cce1', 'user_4', NULL, 'Christelle Wyman', 'hagenes.wyman@example.net', 0, '03737186002', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-31 01:12:46', 577, 774, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 5, 'WOTL5qGsOm', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(5, '209088b7-1d45-468f-aa56-ce2a44741079', 'user_5', NULL, 'Mr. General Lesch', 'apowlowski@example.net', 0, '03944081795', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Non facere sit cumque quidem nihil minima minus.', 'active', 0, 0, '2025-08-12 11:57:12', 589, 256, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":false}', 111, 'tgdgQldDEa', '2025-09-10 09:06:41', '2025-10-17 00:59:04', NULL, 0, '[\"entertainment\"]'),
(6, 'd60be341-e0e1-44a5-992c-979c4c43ddcb', 'user_6', NULL, 'Armand Nolan', 'nella83@example.com', 0, '03259023869', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Recusandae et minus rerum sit ut consequuntur voluptatem explicabo.', 'active', 0, 1, '2025-09-10 04:35:39', 144, 589, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 69, 'ZLdTGdLK1o', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(7, '2a463d21-63a0-4846-b118-14b892847e85', 'user_7', NULL, 'Lera Swaniawski', 'deborah32@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Sunt sit aut corrupti placeat provident soluta.', 'active', 1, 1, '2025-09-07 21:07:00', 672, 333, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 7, '9tdIkU0Mk5', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(8, '86a8f649-8c2b-4bf0-88c0-ffcac818b1dc', 'user_8', NULL, 'Brycen Corkery', 'torp.elias@example.org', 0, '03075175705', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Aspernatur modi adipisci eveniet vel rerum quod.', 'active', 0, 1, '2025-08-19 21:37:13', 930, 555, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 59, 'r3jJfWzGjx', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(9, '72cf1335-44dd-4a32-85bb-0a5d572aa7a4', 'user_9', NULL, 'Dejah Murphy', 'ruben39@example.net', 0, '03639418822', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-01 08:29:45', 120, 397, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 126, 'Yt9XXuX0zL', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(10, 'dd4234ed-4e52-4cbf-9554-5c45879e8938', 'user_10', NULL, 'Levi Murray', 'brant.lubowitz@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-05 18:50:31', 845, 739, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 155, 'E9bOmseoYo', '2025-09-10 09:06:41', '2025-10-17 00:59:18', NULL, 0, '[\"entertainment\"]'),
(11, 'bf3509f6-742e-41d9-807d-8ef09e8f1470', 'user_11', NULL, 'Ms. Josianne O\'Hara', 'imclaughlin@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$nvZg6r0Y0cvP3Pu/ivBnZOCBWTqYRyiirpRL6IOzBDptaUCH5CufK', NULL, 'Magnam illo qui aspernatur quia perferendis soluta iste qui ullam porro.', 'active', 0, 1, '2025-09-08 13:37:41', 125, 313, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 179, 'wn21bqRDtucqD9ec8xlenPf5dUxhkzqJVHTnC1FpUsb8GJoW9NT0Yjt4IlHP', '2025-09-10 09:06:41', '2025-09-19 08:39:34', NULL, 0, '[\"entertainment\"]'),
(12, 'de919049-c064-4feb-b14b-24c5fc054df6', 'user_12', NULL, 'Roberta Emard', 'bayer.augustus@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-18 22:41:27', 249, 8, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 154, 'xum2MhuN21', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(13, '75e3b86a-6718-49c2-86e1-defc4d489f2a', 'user_13', NULL, 'Gregory Bauch MD', 'ankunding.dorothy@example.net', 0, '03284612896', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Provident maiores provident sapiente magni porro eos sed commodi velit inventore omnis molestias est.', 'active', 0, 1, '2025-08-28 18:58:04', 26, 270, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 134, 'SwyDPdovvF', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(14, '5a9f6ba5-2a47-420f-a91b-edf7d8db2cfb', 'user_14', NULL, 'Logan Wintheiser PhD', 'abel.cummerata@example.org', 0, '03788039078', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-11 03:31:29', 477, 226, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 193, 'CfhS4yqyli', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(15, 'f83e407c-98ae-4ce5-b1c0-dffa274725f9', 'user_15', NULL, 'Freddie Lebsack II', 'valerie.lakin@example.com', 0, '03497472489', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-17 17:09:41', 530, 134, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 94, 'mjrBnlJndJ', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(16, '9a2eec34-ef9d-46cd-b82e-e63474f17fb8', 'user_16', NULL, 'Maribel Sauer', 'ykuhlman@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Laudantium enim nihil temporibus perferendis nihil aperiam cupiditate libero rerum quo.', 'active', 0, 1, '2025-08-16 13:51:31', 749, 997, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 197, 'FL732SyKl3', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(17, 'd9d6deb2-e997-444b-8bd7-3a0d91b674ad', 'user_17', NULL, 'Ms. Bryana Hand MD', 'patsy.schumm@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Laborum possimus velit aut facere quia labore non molestias.', 'active', 0, 1, '2025-09-07 21:32:24', 911, 723, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 155, 'kVsVQBWMCm', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(18, 'cb04ea12-34a8-4d8a-9005-34b77681c133', 'user_18', NULL, 'Junius Watsica', 'liza81@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Cupiditate a corporis ab ut exercitationem non reiciendis maiores maxime doloremque.', 'active', 0, 1, '2025-08-22 07:38:48', 757, 724, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 175, 'LtZXkjT9CF', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(19, '7fd2f537-b5f6-4715-879f-e17ee121be94', 'user_19', NULL, 'Celine Lang', 'jerald.lindgren@example.com', 0, '03276431419', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Expedita et molestiae unde ea molestiae fugiat at porro consequatur.', 'active', 0, 1, '2025-08-20 06:58:12', 752, 446, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 4, 'NQxNtOQ6wx', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(20, '27271977-ad63-49a8-8222-6149897191b0', 'user_20', NULL, 'Hipolito Howe', 'candace.walker@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Perspiciatis voluptate culpa molestias corrupti quo optio id et labore provident.', 'active', 0, 1, '2025-08-12 13:04:11', 698, 855, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":false}', 50, 'EmwTsSx6cj', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(21, 'fd138386-3e74-461a-bffd-c7f378fb452b', 'user_21', NULL, 'Dr. Salvador Sawayn Sr.', 'erdman.fabiola@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Perferendis laudantium enim excepturi esse hic sit occaecati iste.', 'active', 0, 1, '2025-09-06 22:57:27', 937, 612, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 35, 'XBOZA22SoI', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(22, '8dcb09fa-3e45-436f-88e7-857d4563e84a', 'user_22', NULL, 'Lisette Leannon', 'aron.gutmann@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-13 14:28:43', 897, 959, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 20, 'hqFso7mYWO', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(23, '7979fa8d-d894-4934-b710-cea4acb13168', 'user_23', NULL, 'Alessia Wiza V', 'jazmyn58@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Magni molestiae vitae ipsam accusantium odit pariatur quod numquam consequatur ullam in occaecati omnis.', 'active', 0, 0, '2025-09-04 22:14:04', 725, 895, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 124, 'ibdmGthX3z', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(24, '84e97142-a85c-48b1-9202-94c92fb8ef53', 'user_24', NULL, 'Burnice Zboncak', 'landen.block@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-15 07:29:56', 83, 484, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 154, 'UwmWJbKsrE', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(25, 'c2b37207-4059-4c44-a67f-3cacc0d6ec8a', 'user_25', NULL, 'Darby Stracke', 'heidenreich.pierce@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-22 21:49:31', 544, 972, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 73, 'gRou51GUGx', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(26, '47a4ca4d-7a82-44d1-83da-dbdb47da37ba', 'user_26', NULL, 'Cortez Bartoletti', 'ymitchell@example.com', 0, '03731558295', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Quisquam minima molestiae nisi assumenda repudiandae adipisci inventore voluptas.', 'active', 0, 1, '2025-09-01 21:11:37', 956, 334, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 115, 'WlWDFW5dDx', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(27, '7cb8c716-232d-43a7-9ce9-23b4ec698c05', 'user_27', NULL, 'Prof. Ivy Glover MD', 'awillms@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-29 00:48:23', 431, 636, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 117, 'OQeCDbG8k8', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(28, '6bd0b91f-35e1-4dc2-8525-73100171dc8e', 'user_28', NULL, 'Lemuel Feeney MD', 'sophie.grant@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-27 06:29:53', 387, 430, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 10, 'prfA1AqNPC', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(29, 'db41cbbb-61bc-47ce-9985-5c4599292b04', 'user_29', NULL, 'Payton Barrows', 'dklein@example.net', 0, '03696364976', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Et harum accusamus qui unde velit rerum praesentium rerum deserunt modi occaecati.', 'active', 0, 1, '2025-08-22 14:54:03', 829, 951, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 13, 'USVVOyGXo2', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(30, '3133f123-0d59-48c9-aca3-4ec5a37aa8d0', 'user_30', NULL, 'Mr. Luis Beahan', 'hills.kadin@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-27 00:27:03', 683, 979, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 119, 'YqddPZ2JdJ', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(31, '6d4ef472-b8d1-45fb-8f8a-d3f5d7f90c03', 'user_31', NULL, 'Vernie Veum V', 'katelin.littel@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-09 21:32:57', 641, 790, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 94, 'onatOgj1wV', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(32, 'a48629e9-2b7b-429f-b93b-d10178649684', 'user_32', NULL, 'Mrs. Viva Block MD', 'jeramie67@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Quaerat nobis ipsa unde mollitia minus est cum.', 'active', 0, 1, '2025-08-15 22:46:05', 965, 215, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 127, 'FAt45megea', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(33, 'd73090db-b77a-475a-96ce-7fc70b6059ae', 'user_33', NULL, 'Jamal Sipes I', 'khalid.halvorson@example.org', 0, '03013029666', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-31 08:56:44', 262, 50, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":false}', 144, '4CzkPhGwug', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(34, '8b571308-528b-40c5-bd33-4916fb7834b3', 'user_34', NULL, 'Lisandro Stark', 'buckridge.lukas@example.org', 0, '03551569549', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-27 23:56:28', 413, 311, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":false}', 155, 'FwSLvTE9ob', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(35, 'f08f7586-4064-431e-b38a-a00b6070ed14', 'user_35', NULL, 'Prof. Mohammed Veum', 'enrique.armstrong@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Laborum nemo animi earum vitae qui eum et aut non quia.', 'active', 0, 1, '2025-08-30 05:01:58', 567, 492, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 199, 'gJXizmYFVK', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(36, '601d3df8-fae1-47b8-b679-a45cc97e829f', 'user_36', NULL, 'Dr. Jada Botsford', 'ngislason@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-14 13:54:40', 17, 817, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 61, '58X1lLtXvi', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(37, '68a8b7e4-7450-4420-9939-4478f3522157', 'user_37', NULL, 'Katelyn Lind', 'phettinger@example.org', 0, '03425731645', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Nemo enim rerum veritatis eos alias possimus unde omnis doloribus.', 'active', 0, 1, '2025-08-15 13:48:07', 458, 708, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 63, '5KZ7MwNxkV', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(38, '27efd7be-5071-48a3-9fa2-5e1a9a83d1ba', 'user_38', NULL, 'Lucy Cassin', 'hudson.kali@example.net', 0, '03218883389', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-30 09:40:54', 570, 504, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 106, 'zWEqfJ5Hmt', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(39, '402882b4-95e5-4c95-a36c-99cbf86ee6f3', 'user_39', NULL, 'Chelsie Corwin V', 'terrell.schamberger@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-05 05:27:05', 633, 656, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 78, 'pDyfZd5JjJ', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(40, '27e481be-7ee0-41b7-9b65-2c2c7070b799', 'user_40', NULL, 'Gerard Hayes Sr.', 'ally37@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Animi illo earum occaecati voluptatibus ipsam in et.', 'active', 0, 1, '2025-08-28 16:54:05', 239, 657, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 33, 'bfwkmqzwHd', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(41, '1e2925b8-011e-4f99-a99b-897db77211bc', 'user_41', NULL, 'Dean Klein', 'fredy70@example.net', 0, '03517428327', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-17 06:04:37', 706, 298, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 119, 'D6auJWDrBg', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(42, '24418cc4-1084-4bb6-a482-fb3c98b119e5', 'user_42', NULL, 'Faustino Smith', 'zelma26@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Numquam voluptatem non quos aut eos et eum vero.', 'active', 0, 1, '2025-08-14 21:13:55', 698, 815, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 91, '7CieuQOTnE', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(43, 'cc1880cf-f00e-4e52-a2a5-db69be244ec8', 'user_43', NULL, 'Dr. Hollis Christiansen DVM', 'ygrady@example.org', 0, '03503055066', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-27 03:10:02', 157, 225, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 124, 'BjpLO9MWHX', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(44, 'de40bdba-1a18-4615-8ea8-fb9783b3b120', 'user_44', NULL, 'Mr. Domenic Johnston Jr.', 'rebekah55@example.net', 0, '03726343187', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-20 09:43:34', 285, 714, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 46, 'yHKPpB2uRM', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(45, '2eda75ed-8e89-4cf8-8c06-92adfa912aa1', 'user_45', NULL, 'Norene Considine', 'caleigh.mosciski@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-28 20:38:17', 758, 920, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 29, 'Drq8em6SFt', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(46, '11cbae4d-a701-4251-b0a6-effb280352ba', 'user_46', NULL, 'Jordon Crooks', 'rocky.smith@example.org', 0, '03846614858', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-25 04:04:51', 755, 582, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 36, 'RQBDm3RWaP', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(47, '25108b16-3669-459d-8dbf-6009fbf95038', 'user_47', NULL, 'Dr. Arden Hamill', 'vladimir.turcotte@example.com', 0, '03597810411', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-07 20:18:53', 496, 186, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 6, 'B4IAmtM3iC', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(48, '7888d13c-5009-4072-a432-706db21b91e7', 'user_48', NULL, 'Mr. Mohamed Schulist Jr.', 'paucek.flavio@example.org', 0, '03961841735', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Sed consequuntur praesentium nihil molestias quod ut.', 'active', 0, 1, '2025-09-09 11:44:33', 410, 507, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 173, 'xMTimBare3', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(49, '0ad74559-ad34-4a6d-b529-f7b9880340e5', 'user_49', NULL, 'Ms. Frida Bayer DVM', 'pharber@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-21 21:22:12', 986, 837, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 122, 'h5EY0IAMw9', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(50, 'b981edcb-6468-4897-8559-b1eb3d9ccd25', 'user_50', NULL, 'Trinity Hoeger', 'kellen.bartoletti@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-03 12:49:33', 881, 777, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 112, 'I6VjyoeBAs', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(51, 'a1f7662f-1553-4508-a618-95d142fd4d4e', 'user_51', NULL, 'Delta Boyle', 'colton.flatley@example.net', 0, '03155158518', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Et nesciunt cumque aliquid dolores nesciunt omnis repellat qui eum quis.', 'active', 0, 1, '2025-08-28 14:22:32', 976, 30, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 81, 'R9Zw8Zs3eE', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(52, '1f0d3fb8-a847-4684-9e4a-a40092d24cc6', 'user_52', NULL, 'Prof. Erich Strosin Sr.', 'dicki.gloria@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-17 18:02:57', 808, 147, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 74, 'DpqRwP3BsI', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(53, '8caf4d3c-ebb7-4126-9142-fc65649fe213', 'user_53', NULL, 'Scotty Macejkovic', 'marvin.keven@example.com', 0, '03388343461', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Qui quaerat consequatur assumenda explicabo quae nisi rerum officiis accusamus est sint reprehenderit repudiandae.', 'active', 0, 1, '2025-09-08 20:57:47', 401, 204, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":false}', 142, 'gWhAr7FFpd', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(54, '6dc17121-1516-4618-ac01-063efe7aca72', 'user_54', NULL, 'Mariah Rolfson', 'brendon57@example.com', 0, '03970953711', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-26 07:10:20', 147, 176, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 25, 'T17j52c4kB', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(55, 'f90769b2-4b5b-41bf-b43a-9a2301557d11', 'user_55', NULL, 'Quinton Jacobs DDS', 'wrunte@example.org', 0, '03792437071', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-07 02:33:44', 143, 693, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 197, 'vtzZBS8cFq', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(56, '45ce603a-3882-4941-9be2-2702f5b56f44', 'user_56', NULL, 'Cleveland Wintheiser', 'chester64@example.org', 0, '03513357253', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-16 20:48:08', 605, 978, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 88, 'p9WsBYhotX', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(57, '8b441f49-a204-4480-b77d-54703bf88a04', 'user_57', NULL, 'Candice Daugherty', 'phyllis01@example.com', 0, '03633276777', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-22 01:00:07', 461, 192, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 27, 'jTHquxRQ4o', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(58, 'e8cfeef2-4d90-4d4e-9237-6ffe1817bce7', 'user_58', NULL, 'Haylee Ondricka', 'marlee.runte@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-17 20:25:04', 747, 555, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 57, 'eIj4PRxe4a', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(59, '5481961d-fadd-4e2b-bd04-440d6114bb8c', 'user_59', NULL, 'Prof. Kellen Fahey', 'brook90@example.org', 0, '03715178466', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-04 06:22:52', 736, 148, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 161, 'M1FScViqKA', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(60, '43cac276-87ea-4d6a-ad5f-46976be9ae45', 'user_60', NULL, 'Vesta Hermiston II', 'norberto02@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-25 05:20:53', 763, 168, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 192, 'ifgHwIAGPX', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(61, 'd5462637-a5ea-4dbc-8153-164b2a9f1429', 'user_61', NULL, 'Pascale Jacobs', 'mgutmann@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Dolores necessitatibus eveniet voluptatibus error eum autem.', 'active', 0, 0, '2025-08-14 08:23:58', 663, 1000, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 16, 'C79iyl9lq3', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(62, 'ace6b70a-1689-466c-b95e-3b223371720c', 'user_62', NULL, 'Abdul Schinner', 'mae48@example.net', 0, '03328211383', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Placeat inventore dolores dolorem est magni reiciendis eaque voluptas dolore ut.', 'active', 0, 1, '2025-08-23 03:29:05', 487, 409, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 22, '8xHl7OngQD', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(63, '247a0e53-2660-4513-a371-9bcd154fcad6', 'user_63', NULL, 'Crystel Emard MD', 'deborah.muller@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Minima dolores est assumenda id qui excepturi nulla nobis laboriosam fugiat maiores odit.', 'active', 0, 1, '2025-09-04 11:02:25', 404, 436, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 26, '86fGJ2tswO', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(64, '1d8faed8-4528-4f89-bfd1-376c243cc76a', 'user_64', NULL, 'Kathleen Lemke IV', 'wharris@example.com', 0, '03000256320', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Quia temporibus odio voluptatem modi et enim rerum nemo in quis ipsa voluptatibus omnis.', 'active', 0, 1, '2025-09-07 02:38:27', 355, 825, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 116, 'LVJ1kOWVWr', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(65, '58cbed5a-a07b-41e1-ae4d-c2f23816cdf1', 'user_65', NULL, 'Mr. Shayne Konopelski', 'arturo33@example.net', 0, '03084125774', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-26 02:54:23', 288, 935, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 62, 'pZ4AMvQHTp', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(66, 'faff6d2f-5569-446b-9022-aec30b1e9957', 'user_66', NULL, 'Estevan Windler', 'duane.reichert@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Modi beatae dolores delectus ut et qui aspernatur mollitia.', 'active', 0, 0, '2025-08-21 21:56:02', 594, 565, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 42, 'Sh8bQ0mEHQ', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(67, '7ccf0c82-5e66-4fc5-95ab-1425819f9f9b', 'user_67', NULL, 'Stacey Koelpin', 'adam.rowe@example.org', 0, '03831177511', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-30 07:20:59', 37, 6, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":false}', 95, 'r443bCI5vG', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(68, 'e3b08c96-347c-4f02-8798-21a2e0107f9d', 'user_68', NULL, 'Humberto Adams', 'kutch.dayna@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-18 07:14:40', 1000, 444, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 73, 'aSVs46PQ1W', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(69, 'b3e4c872-3668-43a1-be32-66f8e20c15d0', 'user_69', NULL, 'Prof. Alejandra DuBuque', 'kendra.mccullough@example.net', 0, '03392490026', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-05 01:06:22', 203, 116, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 135, 'M0bwK1oqCq', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(70, '353d2999-cb12-40c5-bc5d-61340ebdb2ec', 'user_70', NULL, 'Rhett Hackett', 'hherman@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-07 10:17:08', 818, 207, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 79, '3eQIzj2jnp', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(71, 'c2b87a56-c884-49ea-bcd6-15e3c59c0467', 'user_71', NULL, 'Armand Crist IV', 'lynn80@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-21 23:36:10', 39, 24, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 125, 'LAXPundhPQ', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(72, 'cb1b05b8-440a-4267-a7b3-a74c30d19e42', 'user_72', NULL, 'Miss Pat Ankunding', 'maria.bergstrom@example.org', 0, '03177852242', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-13 00:44:45', 612, 398, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 78, '8yLbcnr9BN', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(73, '5aa3c5de-f7bd-4b01-a358-0baf766b040e', 'user_73', NULL, 'Kiera Ankunding I', 'sauer.esther@example.org', 0, '03120992286', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Non atque nobis unde alias aut provident odio.', 'active', 0, 1, '2025-08-12 08:03:59', 440, 219, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 119, 'XTTdhpI53k', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(74, '528f75f5-41c4-479f-8e0a-d1896f278a34', 'user_74', NULL, 'Retta Harris III', 'qcassin@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-11 20:58:48', 771, 131, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 13, 'FOXoirKqkv', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(75, 'bac254fd-572c-4c7d-912d-82c0c4e1f20b', 'user_75', NULL, 'Antonetta Heaney', 'tobin24@example.org', 0, '03838470366', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Non aliquam dolorem optio autem molestiae molestiae aut eum quam aut.', 'active', 1, 1, '2025-08-26 01:57:07', 349, 465, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 95, 'yjZx442dW0', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(76, 'a1d4af42-4ee1-4067-819c-1d373bc399ec', 'user_76', NULL, 'Sydnee Wisoky', 'wyman.jazmyn@example.org', 0, '03664197772', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Similique est eligendi est possimus facilis voluptatem fuga odit commodi similique natus.', 'active', 0, 1, '2025-09-02 09:14:12', 168, 429, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 130, '3Fq2ONKN93', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(77, '5c31bb29-76b6-49e1-b363-54735ddd1479', 'user_77', NULL, 'Alvis Aufderhar', 'vdonnelly@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-09 12:38:01', 283, 934, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 100, 'exRdNlPIYa', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(78, 'e16cacc7-73da-48b1-a3ac-33617809ba29', 'user_78', NULL, 'Geoffrey Hagenes', 'hcollier@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Cum ipsa non totam placeat minima voluptate eum minima quod architecto porro.', 'active', 0, 1, '2025-09-07 19:11:19', 70, 206, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 109, 'jz9tXpAxYd', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(79, '2d7f0f62-8f72-4415-bc59-598d8577ca28', 'user_79', NULL, 'Ms. Glenna Hettinger', 'huel.lavon@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Dolor tempora sunt et id velit ad dolor alias numquam optio ipsum incidunt molestiae.', 'active', 0, 1, '2025-09-07 23:48:00', 459, 748, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 10, 'VQGfLsGwoa', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(80, 'e4f05bc6-fc60-49a4-ae10-8134e294fd4b', 'user_80', NULL, 'Norris Hagenes', 'rhartmann@example.org', 0, '03702749745', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Numquam pariatur et corrupti commodi non quo voluptates voluptate voluptatem mollitia consequatur in.', 'active', 0, 1, '2025-08-21 18:35:17', 372, 933, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 1, 'H68qgwl6nR', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(81, '01ba8b61-3993-43b9-b7fc-e07a0c9d11cb', 'user_81', NULL, 'Dr. Christophe Jones PhD', 'eladio.dickens@example.net', 0, '03113203007', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-10 15:33:30', 588, 16, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 50, '9XSh6EwmQt', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(82, '737ed626-f2e6-4550-aa34-c94ff8ed7aa1', 'user_82', NULL, 'Angelica Johns', 'catalina71@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-13 00:01:19', 691, 280, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 117, 'lkDzqV1zTc', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(83, '3cfa3501-2212-4047-88a7-d2798f0f7f8f', 'user_83', NULL, 'Dannie Bahringer', 'hroob@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-17 11:04:07', 288, 976, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 60, 'ufW7mFlrsu', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(84, '2c3a5e26-c0a2-43fd-9f72-2c5171a91acc', 'user_84', NULL, 'Nikita Bashirian', 'macejkovic.gudrun@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-18 07:06:58', 723, 841, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 126, 'JOWF0lbEYy', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(85, 'fe3dd4aa-6704-4ba0-912c-259e47e300de', 'user_85', NULL, 'Myra Dicki II', 'leanna.botsford@example.com', 0, '03498630105', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-16 17:51:50', 800, 198, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 57, 'DU45sv7Bto', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(86, '1d202e8e-7ad1-46a8-b887-d44c7a5be100', 'user_86', NULL, 'Chad Friesen', 'zoe54@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Asperiores quasi dolores et aut deserunt inventore.', 'active', 0, 1, '2025-09-03 21:32:38', 47, 920, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":false}', 183, '4cCxc6BAWc', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(87, '3d83defc-93be-43be-863b-8cdbf14edac1', 'user_87', NULL, 'Emil Feil', 'bgaylord@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Laudantium omnis laudantium quis incidunt ratione culpa aut officiis ut similique maxime possimus voluptates qui.', 'active', 0, 0, '2025-08-11 20:48:21', 3, 111, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 51, 'y5UQ4m9vKh', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(88, '02cf4df2-6e58-47b4-8264-4c4e587e65ff', 'user_88', NULL, 'Erick Pagac', 'mclaughlin.katherine@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Ipsam sint qui rerum pariatur ut at blanditiis ut optio consequatur sit.', 'active', 0, 1, '2025-08-29 18:42:09', 47, 829, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 117, 'tsQwkQpJpL', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(89, '76d565f7-3788-4569-9309-8f5916db3f8a', 'user_89', NULL, 'Dr. Jaren Medhurst MD', 'bberge@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Consectetur non cupiditate sed et itaque sed fuga.', 'active', 0, 0, '2025-08-16 02:36:58', 297, 332, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 99, 'xCIH7q7kLM', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(90, '4e1a4acd-23ba-413d-9483-6a63ff089ee8', 'user_90', NULL, 'Justen Gutmann', 'margie.wiza@example.org', 0, '03669852271', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-11 09:25:19', 699, 11, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 128, 'J7xxFgWHIc', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(91, '8a82a9ab-974c-458e-8783-a7ae889c3be4', 'user_91', NULL, 'Leda Hettinger', 'vzemlak@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Excepturi error quaerat nemo explicabo facere autem nam eius iusto dolorem necessitatibus quas.', 'active', 1, 1, '2025-08-13 02:05:36', 133, 641, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 18, '7j0Zbmjeh1', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(92, '042145d6-fd87-4380-bbae-54970c3f6d48', 'user_92', NULL, 'Webster Rowe I', 'june71@example.net', 0, '03612855235', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-12 05:37:20', 758, 709, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 48, '7GvwbtOIm6', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(93, '1638facf-f8aa-412f-990d-6ac9136e0d11', 'user_93', NULL, 'Wilber Armstrong Jr.', 'jking@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-16 05:23:56', 525, 213, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":false}', 80, '35NE4N6pK4', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]');
INSERT INTO `users` (`id`, `uuid`, `username`, `occupation`, `name`, `email`, `is_seller`, `phone`, `email_verified_at`, `date_of_birth`, `gender`, `language_preference`, `two_factor_enabled`, `messaging_privacy`, `is_online`, `last_seen_at`, `device_tokens`, `reputation_score`, `email_notification_preferences`, `account_privacy`, `password`, `profile_photo_path`, `bio`, `status`, `is_blocked`, `is_verified`, `last_login_at`, `followers_count`, `following_count`, `settings`, `posts_count`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `is_employer`, `interests`) VALUES
(94, 'd1e23a9c-c55b-42ce-8c58-380462f8ecc5', 'user_94', NULL, 'Ms. Cathryn Cummerata IV', 'yvonne.hintz@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-08 01:46:58', 284, 795, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":false}', 190, 'FEDDFQblkr', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(95, '2fc41df6-60a0-40a3-bbbc-5fe3c98c1145', 'user_95', NULL, 'Domenica Wehner', 'dennis.graham@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-31 14:53:22', 123, 207, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 71, '0NDZUnad1j', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(96, '5533f5a9-b301-4835-bf0e-5465777d0338', 'user_96', NULL, 'Monserrate Fahey', 'ywuckert@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Quam magni et autem nam perspiciatis non distinctio.', 'active', 0, 1, '2025-08-29 01:53:36', 666, 500, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 112, '2u8dtaYwSl', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(97, '4e4b7cfd-2d62-46b7-996d-0dcafb6a0e73', 'user_97', NULL, 'Ivory Weimann III', 'goldner.cornelius@example.com', 0, '03578663624', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-03 10:10:50', 316, 378, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 130, 'S4pifQmPKr', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(98, 'b0d11e03-11fe-485e-bff3-ca5ff7d8a86b', 'user_98', NULL, 'Bette Herzog', 'tierra.bayer@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-16 22:24:30', 278, 966, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":false}', 72, '5Zm7t2xk6Q', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(99, 'ad1da901-ef3a-4bd4-8dc3-fb853c3d300f', 'user_99', NULL, 'Bernadette Goldner', 'fadel.edmund@example.org', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Eaque labore autem cum ad saepe voluptatum aut explicabo deleniti autem sapiente.', 'active', 0, 1, '2025-09-08 03:58:22', 311, 380, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 72, 'dujbgvFpar', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(100, 'c9a56d7b-382b-4804-b0ba-a15631aed16a', 'user_100', NULL, 'Jovan Krajcik', 'skyla28@example.net', 0, '03894713862', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Ut qui dolore minima voluptatum libero maiores rerum quidem omnis voluptatem odit.', 'active', 0, 1, '2025-09-09 05:11:24', 542, 992, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 62, 'F9X1RlIeIF', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(101, '8ccba4c9-c056-4c2b-8dbf-b5bfa3ba5e29', 'user_101', NULL, 'Willis Hahn', 'nicklaus52@example.org', 0, '03805947580', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Sint et pariatur consequatur in animi et et aut cumque cumque aut rerum.', 'active', 0, 0, '2025-08-13 13:10:27', 622, 808, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":false}', 126, 'tgwAdYQIsc', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(102, 'd19e6d78-f309-46e0-ac33-13f006897313', 'user_102', NULL, 'Elena Langosh', 'louisa.paucek@example.org', 0, '03546139983', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-10 03:52:04', 927, 243, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 143, 'H3K8wCg61a', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(103, 'c377ea22-4287-45af-9512-5e33fc1b3398', 'user_103', NULL, 'Dr. Lelah Farrell Sr.', 'mortimer.braun@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Excepturi mollitia dolor illum est commodi dolorem repellat nam perspiciatis.', 'active', 1, 1, '2025-08-28 04:42:24', 944, 195, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 118, 'QGXQ9NcYh9', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(104, '3455bcd2-991a-4d28-97d0-3a707682a54c', 'user_104', NULL, 'Ms. Jazmin Cruickshank III', 'nzemlak@example.net', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Aliquid eveniet sit cum hic eos officia aut voluptatem assumenda alias dolorum dicta.', 'active', 0, 1, '2025-08-23 17:36:25', 557, 445, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 87, 'hKQyzVrAvE', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(105, '028acf2b-73af-4e16-bb8e-95bce213068c', 'user_105', NULL, 'Demond Little', 'jada29@example.com', 0, NULL, '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Nulla modi reprehenderit dolor voluptates sit temporibus illum nam deleniti qui.', 'active', 0, 1, '2025-09-04 21:22:55', 935, 668, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 168, '3UBhnufou7', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(106, '3e4ed9b6-a201-4532-a937-15eae651802e', 'user_106', NULL, 'Jacinthe Nitzsche DDS', 'green.giovanny@example.net', 0, '03315103074', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Animi rerum repellat autem molestias voluptates quaerat.', 'active', 0, 0, '2025-09-07 10:35:55', 302, 151, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 157, '2nLdWfG9oT', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(107, '3162183d-1ebd-469c-920e-8af12ba59bea', 'user_107', NULL, 'Demarco Jakubowski', 'rrunolfsson@example.org', 0, '03061392875', '2025-09-10 09:06:41', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Rerum quisquam deleniti dolor sapiente facere illo impedit asperiores repudiandae.', 'active', 0, 0, '2025-09-02 11:52:29', 351, 815, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 188, 'VDMh8EGHQd', '2025-09-10 09:06:41', '2025-09-10 09:06:41', NULL, 0, '[\"entertainment\"]'),
(108, '237b6748-cc50-461d-a7ca-d562f52e7ccf', 'user_108', NULL, 'Mrs. Eileen Klocko', 'august.okuneva@example.com', 0, '03986366686', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Amet molestias dolores repellendus tempora omnis unde molestiae beatae ut necessitatibus.', 'active', 0, 1, '2025-09-08 02:53:18', 307, 555, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 148, 'cB7tQB7src', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(109, '5cc941ba-9b52-44b7-ba87-05e2f0be5c26', 'user_109', NULL, 'Ted Kemmer', 'jayde65@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-30 11:27:37', 463, 935, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 144, 'VrhHQARjyY', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(110, 'eeb0cf97-38e0-43a8-a9ee-a6e9e39e1cb3', 'user_110', NULL, 'Augustine Bechtelar', 'kirsten.hamill@example.org', 0, '03557559228', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-24 04:29:05', 464, 144, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 105, 'plua42PanA', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(111, '5446885b-615d-4b9c-8044-411a7f51b1cc', 'user_111', NULL, 'Eloisa Bogan MD', 'nola89@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-21 09:20:03', 144, 736, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 151, 'mNfrbL0A6P', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(112, 'd6757b21-abd4-4562-a45e-b5779ed74449', 'user_112', NULL, 'Dr. Maverick Windler MD', 'destin75@example.net', 0, '03682428732', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Et illo soluta molestias magni deleniti accusamus culpa voluptatem odit beatae.', 'active', 0, 1, '2025-09-04 17:50:39', 24, 79, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 54, 't52pWhRtE9', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(113, '621b45ef-a475-4f47-95c1-16318bc51137', 'user_113', NULL, 'Bell Kihn', 'oconner.al@example.com', 0, '03962492918', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-04 05:55:34', 340, 842, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 164, 'SCPl5yZA0P', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(114, '9a239f00-67d6-465b-bf69-0401a135a079', 'user_114', NULL, 'Isabella Hammes MD', 'lehner.madison@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Qui sunt dolores blanditiis vel nobis nam deleniti sunt at et provident odit.', 'active', 0, 0, '2025-08-20 01:21:30', 938, 148, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 138, '2ufKK6xsBb', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(115, 'f0fb724b-b806-44c2-a580-e3aefef8f88b', 'user_115', NULL, 'Taya Johnson', 'ndoyle@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-24 22:09:38', 469, 974, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 168, 'sH6lL9N5Un', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(116, '49d4a30f-4871-40cd-8e48-63b71ba555d4', 'user_116', NULL, 'Deja Marvin PhD', 'denesik.lori@example.net', 0, '03060740538', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Nihil voluptas quas quo accusamus a porro aliquam laboriosam.', 'active', 0, 1, '2025-08-28 21:51:04', 965, 638, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 69, 'j6haoAf42F', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(117, '99ec8af0-59de-4a0c-9e47-8144d26b04b0', 'user_117', NULL, 'Salvatore Jacobs Jr.', 'mustafa18@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-12 02:04:38', 245, 494, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 184, 'WzpaHrareQ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(118, '97e2a4de-eae9-497b-816a-170089dd3e8e', 'user_118', NULL, 'Reta Purdy I', 'mante.jeffry@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Laboriosam ab aliquid ducimus fuga et occaecati ea ratione blanditiis dicta dolorem.', 'active', 0, 1, '2025-09-04 00:44:07', 848, 563, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 165, 'Hz3nXaCqZN', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(119, 'fd789aee-c737-4cba-8e5f-bd31215d610a', 'user_119', NULL, 'Dr. Selmer Brekke', 'mebert@example.org', 0, '03613850418', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Enim voluptas et dolores dolore dolorem recusandae.', 'active', 0, 1, '2025-09-06 15:05:44', 62, 765, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 136, 'C0FCNmrz6e', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(120, '24d72411-99b3-4cad-85ba-9a14264a6128', 'user_120', NULL, 'Roberto Rath II', 'schinner.ashlynn@example.com', 0, '03478579277', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-18 15:07:52', 353, 80, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 50, 'AiybawqTn0', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(121, 'ae540746-4b5e-4d4e-9864-243a8fb5bb75', 'user_121', NULL, 'Mr. Efrain Beatty', 'sibyl.dickinson@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Est aut et est ut commodi velit consequatur vel ut quasi recusandae.', 'active', 0, 0, '2025-08-16 23:57:24', 271, 332, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 59, 'R5xvuUcISF', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(122, '8552e209-47fc-4bca-a407-a0209a4e8ea9', 'user_122', NULL, 'Prof. Breanne Streich I', 'rodriguez.neva@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-12 08:04:40', 116, 423, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 5, 'YYEopbIVYJ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(123, 'bb342d7b-c1fa-4475-9741-1773a70b8ccf', 'user_123', NULL, 'Idella Miller MD', 'danial.weber@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Doloribus accusamus doloremque sint unde rerum delectus aut commodi omnis.', 'active', 0, 0, '2025-08-16 09:14:41', 416, 811, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 3, 'puSTJI8y1f', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(124, 'b993bd17-9364-45f7-831b-c46930427a5b', 'user_124', NULL, 'Magdalena Crona', 'chelsea.moen@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'In consequatur quo ea in ad nulla quisquam autem.', 'active', 0, 1, '2025-09-04 18:54:04', 854, 883, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 138, 'IdtVpXPOsg', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(125, '5ecd3f4e-9003-4c90-bff3-cf75ee43521a', 'user_125', NULL, 'Dillon Zemlak', 'abner98@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-25 13:44:04', 431, 571, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 33, '7j8D8o8KtB', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(126, '6eb730b8-2aff-4b05-a404-c6ddd6067698', 'user_126', NULL, 'Chasity Connelly', 'powlowski.joyce@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-09 12:28:59', 634, 119, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 154, 'nwLHyLuZqG', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(127, 'fbec5883-507a-4065-b326-eafe8408e91a', 'user_127', NULL, 'Malcolm Lehner', 'eva84@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-10 14:45:00', 595, 684, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":false}', 193, 'VZiYDC2adD', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(128, '1025c0f8-345c-455e-9dad-86ee1e035e60', 'user_128', NULL, 'Mrs. Miracle Adams II', 'jacquelyn37@example.org', 0, '03053280033', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-30 09:26:13', 216, 108, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 49, '8K0cSZld6Z', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(129, 'e82c1814-99e9-4619-b9fc-bef6d18aa1ff', 'user_129', NULL, 'Taurean Bailey', 'daugherty.russel@example.org', 0, '03845467859', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Sapiente quidem quisquam debitis magni adipisci non aut a nihil.', 'active', 0, 1, '2025-09-02 09:13:43', 969, 968, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 51, '5sw61z4gY3', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(130, '59aa02fd-5387-404d-bfdd-843aab6341d6', 'user_130', NULL, 'Gabriella Pollich', 'dominic23@example.com', 0, '03772930387', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Nesciunt saepe consequatur occaecati excepturi consequatur non magnam eligendi esse repudiandae.', 'active', 0, 1, '2025-09-05 00:21:38', 513, 195, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 176, 'vdxW2VDRLw', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(131, '7ae7d847-99a8-4a18-a239-f8582cf82464', 'user_131', NULL, 'Ila Tremblay', 'cyrus80@example.org', 0, '03710325298', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Dolorem voluptate odit voluptas vitae ut nihil dolores.', 'active', 0, 0, '2025-09-03 20:56:33', 513, 773, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 34, 'a8FTXr70YW', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(132, 'b7041a65-55c7-49b1-a5ec-3418eb8703d3', 'user_132', NULL, 'Dr. Laurie Roob Sr.', 'shoppe@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-17 15:15:39', 449, 571, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 186, 'gnHv24PgpZ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(133, '49331282-4ffa-4c2a-9479-682d277dc98f', 'user_133', NULL, 'Cassandra Fay', 'sherwood65@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-04 07:03:58', 741, 695, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 29, 'M8gBQrOqyD', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(134, '2279833d-f5b3-4740-962a-d9cf51d44d88', 'user_134', NULL, 'Mrs. Bridget Gerhold Jr.', 'rice.bailey@example.net', 0, '03346414884', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Corrupti aperiam sint maxime earum hic inventore iusto et perspiciatis explicabo ratione reiciendis.', 'active', 0, 1, '2025-09-09 11:37:49', 690, 707, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 153, 'nNIbdc3BFe', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(135, '54ee480f-a511-4c57-8030-118a5e3c61fc', 'user_135', NULL, 'Destin Wehner', 'boehm.amanda@example.com', 0, '03186784256', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Aut molestiae natus dolore et rerum neque eveniet sint.', 'active', 0, 0, '2025-08-31 14:13:39', 516, 991, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 103, 'QqKPF9ozVV', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(136, '10f8fe41-45c5-4c3d-ae58-03a8050226e7', 'user_136', NULL, 'Brown Lang', 'collier.juwan@example.com', 0, '03406017639', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-11 20:55:47', 496, 840, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 21, 'hbStIOlG3b', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(137, '646707cb-9188-45af-bc90-9d42ece3ca2a', 'user_137', NULL, 'Efrain Koch III', 'abbey.strosin@example.com', 0, '03369264218', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Suscipit qui esse vero corporis est odit excepturi fuga et.', 'active', 0, 1, '2025-08-22 01:48:01', 519, 715, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 45, 'JQNgaNIhUz', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(138, 'd020862b-d87d-4341-9c52-92520c37fcef', 'user_138', NULL, 'Cierra Lemke', 'enoch.conn@example.org', 0, '03727131950', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Facere sed ut aperiam ea nesciunt dolorum debitis ut.', 'active', 0, 0, '2025-08-11 16:09:22', 881, 23, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 94, 'meGgPvPmlA', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(139, '481334e1-359c-48e3-98d7-c9bacadef03b', 'user_139', NULL, 'Prof. Erica Gusikowski IV', 'koepp.orin@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Tempore totam eum nostrum pariatur quia doloribus.', 'active', 0, 1, '2025-09-08 03:37:25', 362, 244, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 67, 'KASeumEIb2', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(140, 'cf1da74e-ccac-47a8-8f3f-371492c7f410', 'user_140', NULL, 'Mertie Hansen', 'ykohler@example.com', 0, '03588424943', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-09 19:31:39', 735, 546, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 73, 'kEpzrZQyqm', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(141, '15633844-4c58-4614-90d6-67b1934cd5cc', 'user_141', NULL, 'Leola Williamson', 'daija.hettinger@example.com', 0, '03189569436', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-09-05 00:27:08', 104, 187, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 27, 'KVp1m2GIDL', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(142, '9abbe08f-6e4d-4512-aa35-1b0b2d9810f8', 'user_142', NULL, 'Prof. Vidal Heller Sr.', 'pshields@example.org', 0, '03101925318', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Ut consequatur possimus dolor ut voluptatem quaerat accusamus voluptas vel vitae.', 'active', 0, 0, '2025-08-11 15:42:54', 504, 234, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 180, 'bxRuR6uz3n', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(143, '629f40ee-b1c0-40bc-8cb1-f1da1df6c7c2', 'user_143', NULL, 'Prof. Glen Steuber', 'rupert26@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Placeat nihil ea ea delectus velit eligendi laboriosam reiciendis dolores omnis aut quo et blanditiis.', 'active', 1, 1, '2025-09-04 20:50:47', 358, 617, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 122, 'oASy5d95Ws', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(144, 'ba8825b1-5f4d-4270-a8cc-a2116c04cd34', 'user_144', NULL, 'Prof. Raquel Koss PhD', 'zmurray@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Illo culpa explicabo molestiae sequi est tempore id aut atque.', 'active', 0, 1, '2025-08-11 01:56:50', 144, 771, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 56, 'HeS0lkGY0N', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(145, 'a33a7530-a37c-4ee3-a84a-a8e29ce857c2', 'user_145', NULL, 'Cayla Harris', 'kertzmann.edwardo@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-10 09:13:06', 78, 860, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 14, 't4lCiI31ga', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(146, '547dbd83-52db-408c-9fa2-6533e2e0ee8b', 'user_146', NULL, 'Jeanne Schultz', 'kimberly29@example.org', 0, '03529408003', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Odio amet omnis facere molestiae aut rerum fugit blanditiis ut.', 'active', 1, 1, '2025-09-08 04:22:00', 747, 338, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 172, 'tv6uq6Qqhu', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(147, '81e8f96e-63ca-41f2-ac7f-dbb7b349c7a2', 'user_147', NULL, 'Prof. Haylie Runte', 'major83@example.org', 0, '03738570445', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-14 01:46:57', 454, 438, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 44, 'wFDxrx7Hb5', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(148, '157ac3c1-da8e-4180-ba12-06e00df4b406', 'user_148', NULL, 'Gaston Collier', 'parker.marie@example.net', 0, '03163532939', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-10 05:35:03', 563, 560, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":false}', 133, '0lDy0zMkhj', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(149, 'b2f9ac76-5d09-4f0b-b206-6e39e0e450a0', 'user_149', NULL, 'Demetris Shields', 'libby26@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Amet assumenda quae voluptates aspernatur aspernatur facere est voluptatem optio voluptatem.', 'active', 0, 1, '2025-08-19 23:26:36', 369, 647, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 137, 'WEKRQ614Cn', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(150, '41c207fd-01b5-4d56-b307-83a7401991f0', 'user_150', NULL, 'Miss Lyla Koelpin', 'ola79@example.com', 0, '03348080614', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-13 14:39:18', 951, 15, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 147, 'X7JKKJLUrk', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(151, '55cba33b-2e76-4a2b-bc12-a157c2a34dfa', 'user_151', NULL, 'Madilyn Mohr', 'trantow.keara@example.com', 0, '03822587518', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Consequatur exercitationem dolores dolores saepe odit qui nulla architecto deleniti.', 'active', 0, 1, '2025-08-19 04:27:45', 494, 687, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":false}', 118, 'PCCBUjWM3N', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(152, '6e1b3b5f-7275-466e-978c-1f9c145ee18d', 'user_152', NULL, 'Albertha Gulgowski', 'vullrich@example.net', 0, '03176594629', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Voluptatum debitis nisi tempore provident ex eos et.', 'active', 0, 1, '2025-09-01 16:00:31', 884, 95, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 93, 'PSOvvRc6wJ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(153, '9a55d35e-41d8-4359-8ddd-efbca4111818', 'user_153', NULL, 'Leonel Gleichner DDS', 'alanna.spinka@example.com', 0, '03900858771', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-12 18:01:31', 561, 856, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 85, 'H3b8PU1vzk', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(154, '74271da1-c3ad-47e9-8519-f3232049588d', 'user_154', NULL, 'Sarina Heaney', 'dameon71@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-29 13:27:43', 736, 665, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 69, 'CDjIZTZQEz', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(155, '610b3f19-5414-4d07-9fa4-422fe5906281', 'user_155', NULL, 'Dr. Gregorio Gutmann', 'cullen.toy@example.com', 0, '03728437120', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Nemo perspiciatis quia aliquam voluptas velit aut accusamus.', 'active', 0, 1, '2025-08-18 14:01:27', 729, 554, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":false}', 163, 'LAfB6XFfU5', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(156, '6057f7a5-1112-4e89-ad8a-949d98aff9cc', 'user_156', NULL, 'Kurt Rice', 'nat.prosacco@example.com', 0, '03133527890', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Aut rerum recusandae sit quis in ea nesciunt eos.', 'active', 0, 1, '2025-08-22 09:38:14', 523, 562, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 95, 'vzzACUryt2', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(157, '87c10545-5e16-4ec7-9ee5-c16d0de1798e', 'user_157', NULL, 'Marcelle Buckridge II', 'jmurray@example.net', 0, '03504307529', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Nihil porro magni rem voluptatem et doloremque optio.', 'active', 0, 1, '2025-08-27 20:55:50', 39, 270, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 3, 'DFWnKhfFKv', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(158, '9accd332-0d81-41c0-9601-9238a178d6db', 'user_158', NULL, 'Felicia Klocko', 'ohara.cecilia@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-02 12:21:09', 26, 596, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 33, 'QY8AL08Kud', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(159, '33eabd3a-2153-40d9-9284-0aeb2f017cf7', 'user_159', NULL, 'Olga Durgan', 'zola66@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Et sit dolores soluta veritatis reprehenderit aliquam rem.', 'active', 0, 1, '2025-09-03 01:03:57', 724, 686, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 49, '9bsKEdHzFt', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(160, '6695f69c-924a-4919-bbe9-782167045ec8', 'user_160', NULL, 'Isac Boehm Sr.', 'vidal.parisian@example.org', 0, '03105641719', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-29 08:54:57', 852, 225, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 141, '8RRspdR7Ig', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(161, '135014cd-9f6d-433a-9e53-a82dcb55bd5e', 'user_161', NULL, 'Bret Boyer', 'wfeest@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 1, 1, '2025-08-31 21:08:49', 884, 125, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 115, 'XlCeY6umBO', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(162, '44445598-b5ed-4b49-9cad-651071989a71', 'user_162', NULL, 'Magnolia Cruickshank DDS', 'lhill@example.com', 0, '03527839881', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Voluptatibus non aliquid eum molestias cum est.', 'active', 0, 1, '2025-08-23 02:24:15', 802, 206, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 167, 'mMMD1WdHDT', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(163, 'cdb60d6c-db43-4b4e-b9be-37930c49555c', 'user_163', NULL, 'Dr. Wilhelm Heidenreich', 'odie42@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Illo autem cum consequatur id error temporibus saepe aut.', 'active', 0, 1, '2025-09-09 14:09:39', 839, 367, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 130, '4oy8RvVJyU', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(164, 'e2ec9aa8-8923-46c7-9a09-ad3b12840dc4', 'user_164', NULL, 'Mr. Joel Stanton', 'issac80@example.com', 0, '03591066582', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Quos eius qui est ipsam reprehenderit et laudantium magnam eum.', 'active', 0, 1, '2025-08-17 14:02:54', 440, 945, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 58, 'GAGGKG9t0o', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(165, '43f2d312-a0c4-4030-8510-328c8c988eb8', 'user_165', NULL, 'Wilhelm Predovic', 'obie.daniel@example.com', 0, '03745837474', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Deserunt nulla reprehenderit tenetur qui ut non reiciendis saepe.', 'active', 0, 1, '2025-08-17 14:02:46', 793, 390, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 197, 'ruSYYXOfjo', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(166, 'ef98fdb8-ee26-44d7-867f-93dbad6ad39b', 'user_166', NULL, 'Emilia Skiles PhD', 'sid.damore@example.com', 0, '03738913365', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-07 00:54:48', 403, 695, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 26, 'FY5XQDVmRz', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(167, '62ee5871-b8c9-48a1-88d4-a26d32d5d92f', 'user_167', NULL, 'Miss Idella Kuphal IV', 'smitham.shayne@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Iusto quam culpa mollitia voluptates cumque non deleniti ullam qui ut accusamus sit et doloremque.', 'active', 0, 1, '2025-09-05 10:35:17', 671, 863, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 98, 'SQ4U7ksn4r', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(168, '27dde33c-f535-427f-bdc2-ade66e8fcc38', 'user_168', NULL, 'Florian Okuneva MD', 'margarita.stiedemann@example.net', 0, '03939772185', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Aut quam ab placeat quidem ab libero sunt possimus et quae quasi velit sed.', 'active', 0, 1, '2025-09-02 14:11:26', 666, 826, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 186, '3eB1oQoNS0', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(169, 'a531fa02-4b12-4b42-991d-82c9a391287f', 'user_169', NULL, 'Justina Barrows', 'bbraun@example.org', 0, '03483364372', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-25 06:48:56', 253, 195, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 16, 'dBmklU6AhZ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(170, 'c726dc93-aa4f-4b2f-98a4-a96ba31e96f8', 'user_170', NULL, 'Avery Rutherford', 'jena80@example.com', 0, '03772317271', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-09-06 14:56:37', 695, 493, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":false}', 2, 'vgtLoVhneJ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(171, '094eeac3-1e10-4686-bbcc-303c97366059', 'user_171', NULL, 'Dr. Kaden Jakubowski MD', 'leannon.jerel@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-28 19:56:25', 827, 222, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 7, 'N8VHP7S5My', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(172, '0de0f9ca-70f2-4bf1-a87f-c4963265633a', 'user_172', NULL, 'Geoffrey Toy', 'prath@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Neque sed dolor eligendi earum aut eum vero totam quam non temporibus aut maiores.', 'active', 0, 0, '2025-08-12 05:27:26', 660, 889, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 94, 'C3XuF46x21', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(173, '9a2e5146-88da-4c06-8330-8d76dc3d70fe', 'user_173', NULL, 'Dr. Randi Larson III', 'chandler.harvey@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Asperiores suscipit nulla molestiae ut quas et rem doloremque inventore repellat.', 'active', 1, 1, '2025-08-13 06:24:59', 141, 714, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 82, 'noPZdeObiQ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(174, 'e9964693-eba0-49f0-ad31-7e871a0068e8', 'user_174', NULL, 'Bertrand Schulist', 'pansy56@example.net', 0, '03711042858', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-09-07 06:54:51', 81, 851, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":false}', 136, 'pioEKWJDzn', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(175, '286e48a2-39b8-4c63-86ee-c2be7101302f', 'user_175', NULL, 'Alessandra Wilkinson II', 'mccullough.stella@example.net', 0, '03535438363', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-31 13:53:00', 342, 265, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":false}', 156, 'eaaK3Tf5nk', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(176, '2eaefd44-f7eb-4a60-b753-eec2eaf1d3f5', 'user_176', NULL, 'Saul Bruen MD', 'ueichmann@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-02 20:09:40', 34, 91, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 103, '9qqsygimAS', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(177, 'e55d72ef-f133-40db-808f-62299192444c', 'user_177', NULL, 'Mrs. Leonie Collins', 'tremaine44@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Fugit rerum enim eum odit ad quo et.', 'active', 0, 1, '2025-09-10 05:58:58', 598, 432, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 108, 'HH4uYZir9Q', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(178, '34bdfe28-e146-410e-845b-33b873a51eb8', 'user_178', NULL, 'Dwight Satterfield', 'nelle48@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-02 23:05:19', 987, 657, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 57, 'JQ8hC7XapG', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(179, '522070a9-8502-4825-b049-59652cf262bf', 'user_179', NULL, 'Rory Tillman', 'kristy67@example.net', 0, '03919061785', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Facere quia quis consequatur laudantium et eveniet ea excepturi voluptas tempora vitae id.', 'active', 0, 1, '2025-08-21 06:53:43', 126, 27, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 115, 'vkhZwRHLIb', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(180, 'fe5721ad-4a3e-4c6d-9f28-7a4a793418f6', 'user_180', NULL, 'Cleo Kunde IV', 'marc94@example.com', 0, '03157759534', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-02 17:30:08', 622, 767, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 14, 'MioAqWj1s0', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(181, 'cb638e7b-d000-4acd-87a8-3d974fc24cdb', 'user_181', NULL, 'Aliya Mann', 'taylor.ritchie@example.net', 0, '03578984197', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-01 17:02:26', 382, 945, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 184, 'wYLcg3kCqX', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(182, '4be4d206-3fea-4a6d-82a7-8577db8df4df', 'user_182', NULL, 'Natalia Morar', 'eliezer.bogisich@example.org', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Culpa architecto pariatur placeat rerum excepturi consectetur.', 'active', 0, 1, '2025-08-23 12:52:49', 62, 466, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 125, 'yto3hlDqYv', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(183, '1a2454cc-e6cb-4cd8-a613-23be2869cf61', 'user_183', NULL, 'Prof. Titus Hill', 'schneider.kacie@example.org', 0, '03422062313', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-01 16:18:43', 544, 490, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 83, 'EvxCC8rmR0', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(184, '22ad2892-9d54-4c39-adeb-5b4b2c110e06', 'user_184', NULL, 'Nathan Johnson', 'jonathan.vandervort@example.net', 0, '03548403660', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Quisquam qui numquam rerum eveniet id fugiat aut sed aut incidunt laborum.', 'active', 0, 0, '2025-09-07 21:45:04', 28, 455, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 143, 'xd7DukPcFh', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]');
INSERT INTO `users` (`id`, `uuid`, `username`, `occupation`, `name`, `email`, `is_seller`, `phone`, `email_verified_at`, `date_of_birth`, `gender`, `language_preference`, `two_factor_enabled`, `messaging_privacy`, `is_online`, `last_seen_at`, `device_tokens`, `reputation_score`, `email_notification_preferences`, `account_privacy`, `password`, `profile_photo_path`, `bio`, `status`, `is_blocked`, `is_verified`, `last_login_at`, `followers_count`, `following_count`, `settings`, `posts_count`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `is_employer`, `interests`) VALUES
(185, 'b76de226-5198-4d52-937b-ebbd556a6387', 'user_185', NULL, 'Ms. Yesenia Kulas', 'toberbrunner@example.com', 0, '03175402065', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 1, 1, '2025-08-15 18:42:34', 222, 414, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 57, '26guqGaVJ9', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(186, '764ee1a2-542c-4433-b67c-92eb6c093878', 'user_186', NULL, 'Mr. Tony Yost DDS', 'abbey15@example.org', 0, '03180490917', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-14 00:12:08', 471, 792, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 27, 'GTajsOXbjU', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(187, 'c463340c-3e2b-47cd-ae64-6f058a956d3d', 'user_187', NULL, 'Ms. Mittie Sauer II', 'russel.lorna@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Sapiente itaque minus nemo aspernatur neque aut.', 'active', 0, 1, '2025-08-24 17:34:57', 592, 972, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 196, 'iCDls6nOms', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(188, 'e4c9d48b-3740-4718-8cbe-2c04e2dfd1e1', 'user_188', NULL, 'Teresa Koelpin', 'okovacek@example.net', 0, '03563182072', '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-27 06:08:05', 39, 457, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 32, 'K3GpVspeBQ', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(189, '40e86547-d2df-450c-b154-9e0bf9acd2b1', 'user_189', NULL, 'Lottie DuBuque', 'janie.pfannerstill@example.com', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-31 14:30:41', 587, 180, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 77, 'xYIUCQdZie', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(190, 'ce42427d-00fc-430d-92de-43590652eaf8', 'user_190', NULL, 'Dr. Koby Rau Sr.', 'hand.yesenia@example.net', 0, NULL, '2025-09-10 09:06:42', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-15 09:38:33', 857, 587, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 31, 'iz2NU4kT7S', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL, 0, '[\"entertainment\"]'),
(191, 'bdb917bc-ccdf-465d-8dc3-6e20a6268dad', 'user_191', NULL, 'Dr. Ambrose Rodriguez PhD', 'june.stark@example.com', 0, '03719881549', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Et voluptatem impedit in nesciunt possimus adipisci ducimus quidem corporis rerum unde ad eum.', 'active', 0, 0, '2025-08-28 00:06:51', 889, 60, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 190, 'Y7GKMXg4qq', '2025-09-10 09:06:43', '2025-09-10 09:18:50', NULL, 0, '[\"entertainment\"]'),
(192, '1f2ec020-db36-4230-a611-0e09e0fc6089', 'user_192', NULL, 'Yasmin Stehr', 'brycen.schmitt@example.net', 0, '03107576807', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Natus et quia mollitia ipsa ut numquam consequuntur quia in.', 'active', 0, 1, '2025-08-22 03:15:20', 858, 324, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 112, 'YcSQT02L88', '2025-09-10 09:06:43', '2025-09-11 01:12:59', NULL, 0, '[\"entertainment\"]'),
(193, '6ff75aca-a9a3-420e-b271-c0cd8ac1ba8f', 'user_193', NULL, 'Frederique Schaden', 'nmuller@example.org', 0, '03310828511', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Totam alias consequuntur ut fugiat reprehenderit dolor deleniti.', 'active', 0, 1, '2025-09-07 01:37:34', 320, 810, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":false}', 197, 'UOx6yEpNXh', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(194, 'e31df249-a496-426b-bd8b-602253d202e6', 'user_194', NULL, 'Gene Champlin', 'willow96@example.org', 0, '03556717626', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Excepturi quo earum corporis pariatur natus illum non perferendis soluta id libero quo.', 'active', 0, 1, '2025-08-19 16:18:54', 334, 410, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 27, 'WB4K34Cq7c', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(195, '77955806-2fc8-4831-aede-1dffb9a5c78c', 'user_195', NULL, 'Arthur Brekke I', 'kerluke.ashly@example.net', 0, '03450606151', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-08-26 20:40:07', 368, 496, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 197, 'FLZc7JHRjy', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(196, '76cb4f77-019e-403e-830f-267283d1c07f', 'user_196', NULL, 'Deion Jacobi', 'gleichner.leopoldo@example.org', 0, '03969275908', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 0, '2025-09-05 18:02:26', 819, 2, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 185, 'K3KM127U5S', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(197, '90049c5d-9554-4f08-b5d4-12023fdacdd5', 'user_197', NULL, 'Brown O\'Keefe DVM', 'josie31@example.net', 0, '03311095102', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-12 13:21:05', 82, 698, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":false}', 24, 'aw9toMo4ZP', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(198, 'b9b6a8c0-0ef9-4ffa-8a28-758aa89b2d35', 'user_198', NULL, 'Camilla Conn', 'mustafa.langosh@example.net', 0, NULL, '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-07 02:58:17', 161, 437, '{\"dark_mode\":false,\"language\":\"hi\",\"notifications\":true}', 12, '23HfUiP5Wu', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(199, 'fef387e6-9aac-4762-9b1f-2259ba3fcbf1', 'user_199', NULL, 'Dr. Issac McLaughlin', 'fturcotte@example.com', 0, NULL, '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-29 03:22:33', 363, 251, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 24, 'kXIRV4b7L1', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(200, 'a34bb2c1-e20c-40b5-9e37-d1f340ea0970', 'user_200', NULL, 'Euna Williamson', 'hill.roberto@example.com', 0, '03275470755', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-19 22:17:49', 643, 233, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 28, 'JDMP3681Lt', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(201, 'ec1e6aa0-e4f2-4854-b503-0b76e016c6a8', 'user_201', NULL, 'Dr. Elliott Waters', 'murazik.marcos@example.com', 0, NULL, '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Tenetur porro eos fugit rem tempore ea.', 'active', 0, 1, '2025-09-05 17:34:17', 795, 915, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 50, 'EoiCedjFLM', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(202, '1e5564c5-07b5-4c62-88c0-ba8111ddf358', 'user_202', NULL, 'Freida Spinka I', 'okassulke@example.net', 0, NULL, '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-07 00:33:02', 411, 389, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 114, 'on0pNwP6ak', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(203, 'baa9bf36-65ca-465c-a07a-e45c3b676e27', 'user_203', NULL, 'Prof. Derek Towne', 'shaun.powlowski@example.org', 0, '03365388051', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 1, 1, '2025-08-24 08:17:01', 18, 848, '{\"dark_mode\":true,\"language\":\"en\",\"notifications\":true}', 51, 'eLSyGqObCT', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(204, 'ca8dfb7c-db53-43b6-beda-8a22386b7dd4', 'user_204', NULL, 'Clay VonRueden', 'elizabeth.reinger@example.com', 0, '03129084916', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, 'Incidunt impedit dicta dolores quisquam placeat qui iure.', 'active', 0, 1, '2025-08-16 00:17:21', 109, 385, '{\"dark_mode\":false,\"language\":\"ur\",\"notifications\":true}', 61, 'qMtNpN4RhX', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(205, 'd537d452-6a82-435d-a204-eae96d496c1d', 'user_205', NULL, 'Leonardo Walker II', 'paris.wehner@example.com', 0, NULL, '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-09-02 19:08:21', 962, 325, '{\"dark_mode\":true,\"language\":\"ur\",\"notifications\":true}', 100, 'tmvh3P8CtZ', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(206, 'b8575510-56cb-44cf-8ae7-c1401ca7fa18', 'user_206', NULL, 'Mr. Judson Shanahan Jr.', 'batz.emile@example.org', 0, '03243244690', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-23 19:43:12', 269, 526, '{\"dark_mode\":true,\"language\":\"hi\",\"notifications\":true}', 199, 'v9MhuowQT2', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(207, '72d742ab-a946-48de-9a48-7d9b87da2c8e', 'user_207', NULL, 'Giovanny Vandervort', 'frida58@example.net', 0, '03256948062', '2025-09-10 09:06:43', NULL, NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$PCIYKz.QaR8hk6/vFJBWPOebVyjYDcWgZwS2W/u6ypQ44yCMxYi5a', NULL, NULL, 'active', 0, 1, '2025-08-26 05:33:45', 517, 26, '{\"dark_mode\":false,\"language\":\"en\",\"notifications\":true}', 160, 'eSshEIsTzH', '2025-09-10 09:06:43', '2025-09-10 09:06:43', NULL, 0, '[\"entertainment\"]'),
(209, 'e8226aab-9b8e-4c21-a405-d81802e8472c', 'shaikhzaid', 'software developer', 'Shaikh Zaid', 'shaikhzaid123@gmail.com', 0, '9876543210', NULL, '2002-05-28', NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$5nEQgn99qvh.phR1PHaSjexSKOMbwegyvEy1c3yAel8hB/T6euxEC', NULL, NULL, 'active', 0, 0, '2025-09-20 02:01:02', 0, 0, NULL, 0, NULL, '2025-09-20 01:59:55', '2025-09-20 02:19:08', NULL, 0, '[\"Music\",\"Coding\"]'),
(210, '1409b9e7-d970-42b5-9ae6-c460020de7d2', 'amanverma78', 'Software Developer', 'Aman Verma', 'aman.verma1978@example.com', 0, '9876543220', NULL, '1990-05-15', NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$El3NPtEtpVnkF9xPC.DlVuT9dm8iwWwG9V3MJ8WdjEBwVNiasXca6', NULL, NULL, 'active', 0, 0, NULL, 0, 0, NULL, 0, NULL, '2025-10-16 02:20:57', '2025-10-16 02:20:57', NULL, 0, '[\"entertainment\"]'),
(211, 'a313eb30-6e54-4c7d-8d4e-bad8f415677a', 'amanverma79', 'Software Developer', 'Aman Verma', 'aman.verma1979@example.com', 1, '9876543221', NULL, '1990-05-15', NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$smAjfxnyTZnY1qSULC22Tu7ykr2kTecn1QIioXoyQ2XfG0KKf4vte', 'http://localhost:8000/storage/7/1760696049_mens-shirt-black-rvq2R20-600.jpg', 'Passionate Software Developer with over 10 years of experience in building scalable web applications. Avid coder, reader, and traveler, always eager to learn new technologies and explore new places.', 'active', 0, 1, '2025-11-21 03:48:25', 0, 2, NULL, 0, NULL, '2025-10-16 02:21:39', '2025-11-21 03:48:25', NULL, 1, '[1, 2]'),
(212, '767ed7f3-555b-4cc2-8a51-59828095f4db', 'amanverma69', 'Software Developer', 'Aman Verma', 'aman.verma1969@example.com', 0, '9876543621', NULL, '1990-05-15', NULL, 'en', 0, 'everyone', 0, NULL, NULL, 0, NULL, 'public', '$2y$12$yPPyJdNpfL5FhzwlAYtOMeNUUc3Xm2B79NqtrLxVk/ypnfeL.B7/W', NULL, NULL, 'active', 0, 0, NULL, 0, 0, NULL, 0, NULL, '2025-10-24 09:10:01', '2025-10-24 09:10:01', NULL, 0, '[\"entertainment\"]');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'shipping',
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'IN',
  `postal_code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `uuid`, `user_id`, `order_id`, `type`, `name`, `phone`, `address_line1`, `address_line2`, `city`, `state`, `country`, `postal_code`, `created_at`, `updated_at`) VALUES
(3, '8dc32d99-9b27-471a-8228-018870b9dd3f', 11, 3, 'shipping', 'Aman Sharma', '9876543210', 'Street 12', 'Near X', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(4, 'e4127706-90c7-4eb1-af8a-0a62ed958fae', 11, 4, 'shipping', 'John Doe', '9999999999', '123 Main Street', 'Near Market', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 04:23:50', '2025-09-12 04:23:50'),
(5, 'c2478202-dc33-4e87-ae4b-e6d18bcd9356', 11, 5, 'shipping', 'John Doe', '9999999999', '123 Main Street', 'Near Market', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 04:30:38', '2025-09-12 04:30:38'),
(7, '81deca81-e92b-4b83-9e4e-2bf8809f0ef9', 11, 7, 'shipping', 'John Doe', '9999999999', '123 Main Street', 'Near Market', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 04:38:40', '2025-09-12 04:38:40'),
(8, '56b0a585-acfb-4abd-bac9-3e32b837daa9', 11, 8, 'shipping', 'John Doe', '9999999999', '123 Main Street', 'Near Market', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 05:26:04', '2025-09-12 05:26:04'),
(9, '0b8754d6-bc0e-4620-b394-4cea44c9bded', 11, 9, 'shipping', 'John Doe', '9999999999', '123 Main Street', 'Near Market', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 06:28:06', '2025-09-12 06:28:06'),
(10, 'da89cb7b-eb34-4a9a-9029-638fc8529fe8', 11, 10, 'shipping', 'John Doe', '9999999999', '123 Main Street', 'Near Market', 'Mumbai', 'Maharashtra', 'IN', '400001', '2025-09-12 06:43:26', '2025-09-12 06:43:26'),
(11, '212c71e3-4b70-49d5-a596-82903d16bd01', 211, 11, 'shipping', 'Raj Kumar', '9876543210', '123, MG Road', 'Near Metro Station', 'Delhi', 'Delhi', 'IN', '110001', '2025-10-22 08:13:32', '2025-10-22 08:13:32'),
(12, 'c6ca867c-fd72-4028-9252-bfafe0758c36', 211, 12, 'shipping', 'Raj Kumar', '9876543210', '123, MG Road', 'Near Metro Station', 'Delhi', 'Delhi', 'IN', '110001', '2025-10-22 08:32:29', '2025-10-22 08:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_carts`
--

CREATE TABLE `user_carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_carts`
--

INSERT INTO `user_carts` (`id`, `uuid`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '8556d2e1-bf87-4de5-abb5-30518abb11f2', 11, '2025-09-11 07:28:43', '2025-09-11 07:28:43'),
(6, '9c41eea8-63d9-4487-8147-537dd475121d', 211, '2025-10-22 08:32:22', '2025-10-22 08:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_cart_items`
--

CREATE TABLE `user_cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_chats`
--

CREATE TABLE `user_chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `type` enum('private','group') NOT NULL DEFAULT 'private',
  `name` varchar(255) DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_chats`
--

INSERT INTO `user_chats` (`id`, `uuid`, `type`, `name`, `owner_id`, `meta`, `last_message_at`, `created_at`, `updated_at`) VALUES
(1, '2f68307d-00dc-40f9-88b0-4b44d37fc478', 'private', NULL, NULL, NULL, NULL, '2025-09-16 01:27:24', '2025-09-16 01:27:24'),
(2, '45965908-d75f-4249-90ad-1d083ae59037', 'private', NULL, NULL, NULL, '2025-09-17 02:06:14', '2025-09-16 01:27:31', '2025-09-17 02:06:14'),
(3, '40655dc0-8400-4a26-a89e-31f48451ac20', 'private', NULL, NULL, NULL, '2025-11-24 02:41:08', '2025-10-18 04:17:20', '2025-11-24 02:41:08'),
(4, '35e79ac6-fb95-4ac4-af58-d2640d1c6119', 'group', 'Developers Group', 211, NULL, NULL, '2025-10-18 07:17:00', '2025-10-18 07:17:00'),
(5, '06699310-df47-47ef-ad62-ae2f785f2fdd', 'group', 'Developers Group', 211, NULL, NULL, '2025-10-18 07:20:42', '2025-10-18 07:20:42'),
(6, '0a3867ba-14cf-43b9-bbd1-072a13c9d755', 'group', 'Developers Group', 211, NULL, NULL, '2025-10-18 07:22:00', '2025-10-18 07:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_chat_messages`
--

CREATE TABLE `user_chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `content` text DEFAULT NULL,
  `message_type` enum('text','image','file','system') NOT NULL DEFAULT 'text',
  `attachment_path` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `reply_to_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_chat_messages`
--

INSERT INTO `user_chat_messages` (`id`, `uuid`, `chat_id`, `sender_id`, `content`, `message_type`, `attachment_path`, `meta`, `reply_to_message_id`, `delivered_at`, `created_at`, `updated_at`) VALUES
(1, 'f80869c2-e806-4c21-a91b-1a51b55d2981', 2, 11, 'Hey Dear', 'text', NULL, NULL, NULL, NULL, '2025-09-16 01:33:44', '2025-09-16 01:33:44'),
(2, 'f6e456c3-40cc-4839-87b0-98347085ec8c', 2, 3, 'how are you?', 'text', NULL, NULL, NULL, NULL, '2025-09-16 01:39:42', '2025-09-16 01:39:42'),
(3, '53198bd1-a277-409e-89dd-d337b54d80ad', 2, 3, 'how are you?', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:06:24', '2025-09-16 04:06:24'),
(4, 'e536ba3a-5d16-473c-b2b6-a0e1149d63be', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:07:48', '2025-09-16 04:07:48'),
(5, '90e65700-4edc-4fed-8fdb-efb9abfbf7fa', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:08:48', '2025-09-16 04:08:48'),
(6, '31ae384c-9c9f-4428-bb6d-2e17d893907c', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:09:04', '2025-09-16 04:09:04'),
(7, '5cf0575e-2f64-4f54-84c8-c4f2b6af3667', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:10:26', '2025-09-16 04:10:26'),
(8, '98809410-bb29-4e2e-856d-09fed465791e', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:13:48', '2025-09-16 04:13:48'),
(9, 'bf6a0668-3bd7-4505-8578-42c55b75f75f', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:16:08', '2025-09-16 04:16:08'),
(10, '70b51bef-0e45-450e-b9a9-b9fa28c6be88', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:28:27', '2025-09-16 04:28:27'),
(11, '5ef51b83-9107-4e0f-9872-c7d06552e87f', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 04:39:01', '2025-09-16 04:39:01'),
(12, '03568aca-0e83-486b-b107-44721255df99', 2, 3, 'What are you doing???', 'text', NULL, NULL, NULL, NULL, '2025-09-16 07:42:24', '2025-09-16 07:42:24'),
(13, '30c352aa-99cd-4f17-a356-4cde0ffe213b', 2, 11, 'Hey Dear 1', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:05:52', '2025-09-16 08:05:52'),
(14, '8cab64c7-e0c2-4469-b1a8-51d36547d6e3', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:06:43', '2025-09-16 08:06:43'),
(15, '296b64a3-63c6-493e-be99-05ae7ac83cd4', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:07:04', '2025-09-16 08:07:04'),
(16, 'f0f66cfa-046c-4105-bff3-e07b52a20f39', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:07:12', '2025-09-16 08:07:12'),
(17, '6e085de4-efda-4f26-9477-9d0909615427', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:07:32', '2025-09-16 08:07:32'),
(18, '7b95b4ee-d271-4b75-ac33-4298d2c72e0f', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:07:49', '2025-09-16 08:07:49'),
(19, 'afc2e17d-56bb-475c-8b93-ef7bcbef7e9d', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:08:04', '2025-09-16 08:08:04'),
(20, '55ba9621-5c8a-428a-80e4-8666463bd8eb', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:11:05', '2025-09-16 08:11:05'),
(21, '5bd242ab-fe2d-439f-ae7e-551ef184fdf7', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:11:17', '2025-09-16 08:11:17'),
(22, '1497c859-40eb-48ca-a1a3-52af1ec0d313', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:11:19', '2025-09-16 08:11:19'),
(23, 'ede7a72b-703d-4b07-a58c-11a511490a98', 2, 11, 'Hey Dear 2', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:11:29', '2025-09-16 08:11:29'),
(24, '2db8003f-dc24-43de-b45f-a9b2ef5517d7', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:11:42', '2025-09-16 08:11:42'),
(25, 'c4a2b29d-e777-44fb-b9a4-bf9d263dfb09', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:12:05', '2025-09-16 08:12:05'),
(26, 'f1346a76-2f19-4300-99af-83c8e7d8c1b6', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:12:09', '2025-09-16 08:12:09'),
(27, 'ffc0f8a9-eeaf-4331-beb4-4a7103bd88f1', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:12:15', '2025-09-16 08:12:15'),
(28, 'e871d3c9-47c1-4785-ba5d-f1a76efefb74', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:13:32', '2025-09-16 08:13:32'),
(29, 'a57f1f8f-8bd2-4b0c-8f57-dbc73c5872bc', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:17:31', '2025-09-16 08:17:31'),
(30, 'ac814cf7-df5d-4a08-8f13-9cdfbed1fbd1', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:19:59', '2025-09-16 08:19:59'),
(31, '6eacc954-c51f-47dd-afe2-f4c561568b5f', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:20:27', '2025-09-16 08:20:27'),
(32, '3d4d2c6c-0002-4485-b949-0eb501aff961', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:20:35', '2025-09-16 08:20:35'),
(33, 'f8739cc2-f9a9-46aa-bb1d-8cdbc8ca22d5', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 08:24:07', '2025-09-16 08:24:07'),
(34, '1b94ca84-4c6b-47ea-97ec-4e5d8eb69062', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-16 23:36:32', '2025-09-16 23:36:32'),
(35, 'd5ce9426-a4ff-4719-a7d0-f554c3bf3f00', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:27:49', '2025-09-17 00:27:49'),
(36, '1ce87921-008a-4b04-8c1b-bd48fdecb7fb', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:28:49', '2025-09-17 00:28:49'),
(37, '2766cf4a-3976-47ee-b0dd-41e934eadcfe', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:33:55', '2025-09-17 00:33:55'),
(38, '64c29033-910e-4ab4-9cfa-24b5bcd34bfd', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:36:49', '2025-09-17 00:36:49'),
(39, 'ce8796d2-81cd-4815-87db-fbdbd6a9be8f', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:37:29', '2025-09-17 00:37:29'),
(40, 'c056735e-da23-45bc-8ee2-58529bb8be8b', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:39:15', '2025-09-17 00:39:15'),
(41, 'eb63453a-016e-4c9f-b9b6-4286f3a3668e', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:45:16', '2025-09-17 00:45:16'),
(42, 'ce28d08d-65ed-4854-8db9-18b2f46abc0f', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:47:09', '2025-09-17 00:47:09'),
(43, 'e5cb75b6-6a89-428c-99fc-f26ab79700e8', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:49:08', '2025-09-17 00:49:08'),
(44, 'f731b0cc-90e8-4638-baee-4193a6aa6d8b', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:50:28', '2025-09-17 00:50:28'),
(45, 'd2f2347c-fac1-4930-9471-507ef8524659', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:53:48', '2025-09-17 00:53:48'),
(46, 'f4e116eb-3347-4be4-b7bb-45786256b319', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:54:04', '2025-09-17 00:54:04'),
(47, 'ba7552c8-4c70-4eae-8bdd-926b7ffdf815', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:57:59', '2025-09-17 00:57:59'),
(48, '5719fbc2-7cfc-4935-9a73-afe5330cb6f1', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 00:59:07', '2025-09-17 00:59:07'),
(49, '1a6c10c5-83d3-4da8-88b0-1af4190c34d0', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 01:03:36', '2025-09-17 01:03:36'),
(50, '6b69da0e-3923-4623-a743-cc7ce64ac782', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 01:19:24', '2025-09-17 01:19:24'),
(51, '83bd51dd-9a14-4094-8ffd-b9eaf379be0b', 2, 11, 'Hey', 'text', NULL, NULL, NULL, NULL, '2025-09-17 02:06:14', '2025-09-17 02:06:14'),
(52, '469261f0-667c-41bb-b0aa-5960a7abb2a0', 3, 211, 'hi', 'text', NULL, NULL, NULL, NULL, '2025-10-18 04:45:21', '2025-10-18 04:45:21'),
(53, 'b117f4ee-7e3f-45cd-9a96-33ecd5c47b14', 3, 211, 'hi', 'text', NULL, NULL, NULL, NULL, '2025-10-18 04:45:38', '2025-10-18 04:45:38'),
(54, '8cd8117e-2aee-41a7-bbd4-7363dc5f3f45', 3, 211, 'hi', 'text', NULL, NULL, NULL, NULL, '2025-10-18 04:45:40', '2025-10-18 04:45:40'),
(55, '1e6153a5-58bb-491b-86bf-a98406fd8349', 3, 211, 'hi', 'text', NULL, NULL, NULL, NULL, '2025-10-18 04:45:50', '2025-10-18 04:45:50'),
(56, '247cbd33-bef8-492b-8e7a-9bf3200ee511', 3, 211, 'hi', 'text', NULL, NULL, NULL, NULL, '2025-10-18 04:45:55', '2025-10-18 04:45:55'),
(57, 'd675eebf-6c8f-4b59-a7f8-ae4c457262c2', 3, 211, 'hi', 'text', NULL, NULL, NULL, NULL, '2025-10-18 05:21:48', '2025-10-18 05:21:48'),
(58, '5590ad55-3c59-4d11-aa95-45a7c4261bf5', 3, 211, '4:25', 'text', NULL, NULL, NULL, NULL, '2025-10-18 05:26:03', '2025-10-18 05:26:03'),
(59, '45141792-6d0a-47ef-aa68-de424bdc6855', 3, 211, 'yeh 52 msg ka reply hian', 'text', NULL, NULL, 52, '2025-11-24 02:41:08', '2025-11-24 02:41:08', '2025-11-24 02:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `user_chat_participants`
--

CREATE TABLE `user_chat_participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_read_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_chat_participants`
--

INSERT INTO `user_chat_participants` (`id`, `chat_id`, `user_id`, `is_admin`, `last_read_message_id`, `joined_at`, `created_at`, `updated_at`) VALUES
(1, 1, 11, 0, NULL, '2025-09-16 06:57:24', '2025-09-16 01:27:24', '2025-09-16 01:27:24'),
(2, 1, 13, 0, NULL, '2025-09-16 06:57:24', '2025-09-16 01:27:24', '2025-09-16 01:27:24'),
(3, 2, 11, 0, NULL, '2025-09-16 06:57:31', '2025-09-16 01:27:31', '2025-09-16 01:27:31'),
(4, 2, 3, 0, NULL, '2025-09-16 06:57:31', '2025-09-16 01:27:31', '2025-09-16 01:27:31'),
(5, 3, 211, 0, 57, '2025-10-18 09:47:20', '2025-10-18 04:17:20', '2025-10-18 06:59:25'),
(6, 3, 11, 0, NULL, '2025-10-18 09:47:20', '2025-10-18 04:17:20', '2025-10-18 04:17:20'),
(7, 4, 211, 1, NULL, '2025-10-18 12:47:01', '2025-10-18 07:17:01', '2025-10-18 07:17:01'),
(8, 4, 11, 0, NULL, '2025-10-18 12:47:01', '2025-10-18 07:17:01', '2025-10-18 07:17:01'),
(9, 4, 201, 0, NULL, '2025-10-18 12:47:01', '2025-10-18 07:17:01', '2025-10-18 07:17:01'),
(10, 4, 58, 0, NULL, '2025-10-18 12:47:01', '2025-10-18 07:17:01', '2025-10-18 07:17:01'),
(11, 5, 211, 1, NULL, '2025-10-18 12:50:42', '2025-10-18 07:20:42', '2025-10-18 07:20:42'),
(12, 5, 11, 0, NULL, '2025-10-18 12:50:42', '2025-10-18 07:20:42', '2025-10-18 07:20:42'),
(13, 5, 201, 0, NULL, '2025-10-18 12:50:42', '2025-10-18 07:20:42', '2025-10-18 07:20:42'),
(14, 5, 58, 0, NULL, '2025-10-18 12:50:42', '2025-10-18 07:20:42', '2025-10-18 07:20:42'),
(16, 6, 11, 0, NULL, '2025-10-18 12:52:00', '2025-10-18 07:22:00', '2025-10-18 07:22:00'),
(17, 6, 201, 0, NULL, '2025-10-18 12:52:00', '2025-10-18 07:22:00', '2025-10-18 07:22:00'),
(18, 6, 58, 0, NULL, '2025-10-18 12:52:00', '2025-10-18 07:22:00', '2025-10-18 07:22:00'),
(20, 6, 25, 0, NULL, '2025-10-18 12:56:47', '2025-10-18 07:26:47', '2025-10-18 07:26:47'),
(21, 6, 30, 0, NULL, '2025-10-18 12:56:47', '2025-10-18 07:26:47', '2025-10-18 07:26:47'),
(23, 6, 36, 0, NULL, '2025-10-18 13:36:03', '2025-10-18 08:06:03', '2025-10-18 08:06:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_jobs`
--

CREATE TABLE `user_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `posted_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `salary_from` int(11) DEFAULT NULL,
  `salary_to` int(11) DEFAULT NULL,
  `employment_type` enum('full-time','part-time','contract','freelance') DEFAULT NULL,
  `status` enum('pending','approved','rejected','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_jobs`
--

INSERT INTO `user_jobs` (`id`, `uuid`, `posted_by`, `title`, `description`, `company_name`, `location`, `salary_from`, `salary_to`, `employment_type`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '8953fb18-3473-3240-8a17-223df2f588c1', 185, 'Usher', 'Ab ullam provident commodi in eum quia. Temporibus illo facere in fuga. Delectus delectus dolorum et ipsam odio et quia. Quisquam ipsam aperiam sint est porro. Minus rerum autem voluptatum sit repudiandae.', 'Pouros-Tillman', NULL, 74384, 87797, 'full-time', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(2, '0c23f6a4-860f-366c-a6d8-6023ead4b209', 186, 'Fiber Product Cutting Machine Operator', 'Soluta architecto sunt est placeat molestiae. In et voluptatem quia nihil quo. Minima ut doloremque eligendi ut. Et modi rem dolor error.', 'Keeling-Wolf', 'Audreannebury', 71239, 102984, 'full-time', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(3, 'a7e07876-7348-3ff9-8bde-df92519320c6', 187, 'Storage Manager OR Distribution Manager', NULL, NULL, NULL, 69699, 102708, 'part-time', 'closed', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(4, '5e0715e7-9c6e-364c-83c6-4c940a26bc55', 188, 'Precision Mold and Pattern Caster', NULL, 'Spencer, Thompson and Olson', 'North Alexaneland', 51057, 64256, 'freelance', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL),
(5, '42613fe0-8331-38fd-8c99-d9879bc312ef', 189, 'Production Planner', 'Autem est doloribus tempora iste. Quae recusandae vel inventore numquam magni. Aut quasi deleniti est quasi qui dolor blanditiis laborum.', NULL, 'Jerrodville', 75254, 112918, 'part-time', 'rejected', '2025-09-10 09:06:42', '2025-09-10 09:06:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_job_applications`
--

CREATE TABLE `user_job_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `job_id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` bigint(20) UNSIGNED NOT NULL,
  `cover_message` text DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `resume_media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('applied','viewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'applied',
  `applied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_job_applications`
--

INSERT INTO `user_job_applications` (`id`, `uuid`, `job_id`, `applicant_id`, `cover_message`, `resume_path`, `resume_media_id`, `status`, `applied_at`, `created_at`, `updated_at`) VALUES
(1, '6869b759-e192-4f95-b27d-215b5e59da8c', 2, 11, 'Excited to apply!!!', 'resumes/11/OG0wRi5pC3oYfWCKEMYm9sDtFG2nTdEaEn3Oc27T.pdf', NULL, 'shortlisted', NULL, '2025-09-15 07:05:35', '2025-09-15 08:00:51'),
(3, '2aac0b24-4218-4e7f-9edc-69e5e2946189', 5, 212, 'I am Intrested', 'resumes/212/Ist2yN29Lt754t4hT0d45i5xcyXkYWhi2daACo0J.pdf', NULL, 'viewed', NULL, '2025-10-24 09:12:26', '2025-10-24 09:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `user_job_posts`
--

CREATE TABLE `user_job_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `employer_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `responsibilities` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','internship','freelance') NOT NULL DEFAULT 'full_time',
  `salary_min` decimal(12,2) DEFAULT NULL,
  `salary_max` decimal(12,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `is_remote` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'published',
  `views_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_job_posts`
--

INSERT INTO `user_job_posts` (`id`, `uuid`, `employer_id`, `title`, `slug`, `company_name`, `company_website`, `company_logo`, `description`, `responsibilities`, `requirements`, `location`, `country`, `employment_type`, `salary_min`, `salary_max`, `currency`, `is_remote`, `status`, `views_count`, `created_at`, `updated_at`) VALUES
(2, '59458f86-e7bf-4770-8cfc-8f32930a8722', 3, 'Backend Engineer', 'backend-engineers-xyz-pvt-ltd-RNnaWhqxRh', 'XYZ Pvt Ltd', 'https://xyz.com', NULL, 'Responsible for APIs\r\nPerfect', NULL, NULL, 'Bangalore', 'India', 'full_time', 40000.00, 80000.00, 'INR', 0, 'published', 0, '2025-09-15 06:35:15', '2025-10-24 08:40:24'),
(5, '13d73f30-89f8-450a-869e-fa8edd32a777', 211, 'Backend Developer', 'backend-developer-tech-corp-DCQkQuCZUm', 'Tech Corp', 'https://techcorp.com', NULL, 'Updated job description with more details', 'Develop and maintain APIs, database design', 'PHP, Laravel, MySQL, REST APIs', 'Mumbai', 'India', 'full_time', 55000.00, 90000.00, 'INR', 1, 'published', 1, '2025-10-24 08:51:47', '2025-10-24 09:05:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_orders`
--

CREATE TABLE `user_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL,
  `payment_status` enum('unpaid','paid','pending','initiated','failed') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_orders`
--

INSERT INTO `user_orders` (`id`, `uuid`, `buyer_id`, `total_amount`, `status`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, '416eccf8-5af2-4eb2-9f62-52ffe3b00503', 11, 69970.00, 'pending', 'unpaid', '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(4, '960dda18-f00f-4e1d-afc8-c9d8f95f607d', 11, 59990.00, 'pending', 'unpaid', '2025-09-12 04:23:50', '2025-09-12 04:23:50'),
(5, 'c44a44ae-bd33-4d1a-9fd7-f654e805e053', 11, 599900.00, 'pending', 'unpaid', '2025-09-12 04:30:38', '2025-09-12 04:30:38'),
(7, '46e98dd5-55d1-4124-ab78-99553bf38795', 11, 599900.00, 'pending', 'initiated', '2025-09-12 04:38:40', '2025-09-12 04:38:40'),
(8, '9c33b5cd-ef74-466c-8639-145797c5ff68', 11, 59900.00, 'processing', 'paid', '2025-09-12 05:26:04', '2025-09-12 07:04:06'),
(9, '12747065-9fac-4628-8dc4-2320e6083327', 11, 59900.00, 'processing', 'paid', '2025-09-12 06:28:06', '2025-09-12 07:04:06'),
(10, 'be3ce3b9-0200-45b1-9fde-9aa72e6f657a', 11, 599.00, 'processing', 'paid', '2025-09-12 06:43:26', '2025-09-12 07:04:06'),
(11, '92155247-3f5e-4181-8065-5c7c72b27de1', 211, 599.00, 'processing', 'paid', '2025-10-22 08:13:32', '2025-10-22 09:16:32'),
(12, 'b3348365-0727-4f50-80c4-73dc51567cf1', 211, 8985.00, 'processing', 'paid', '2025-10-22 08:32:29', '2025-10-24 00:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_order_items`
--

CREATE TABLE `user_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_order_items`
--

INSERT INTO `user_order_items` (`id`, `uuid`, `order_id`, `seller_id`, `item_type`, `item_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(5, '3f1a1e33-eb42-4520-b7ed-8bbb6e594e3e', 3, 3, 'user_products', 7, 10, 499.00, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(6, '425d21eb-5327-468c-89a1-434266f2cbbb', 3, 3, 'user_products', 10, 10, 5999.00, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(7, '00d0fbc5-3b1a-4ab4-9e07-ba36ed2af18c', 4, 3, 'user_products', 10, 10, 5999.00, '2025-09-12 04:23:50', '2025-09-12 04:23:50'),
(8, '3b506f5d-6ce6-4a31-8a9c-49cd3c072df4', 5, 3, 'user_products', 10, 100, 5999.00, '2025-09-12 04:30:38', '2025-09-12 04:30:38'),
(10, '341d36b9-48a7-4ce6-883a-6e0c64cf6a3e', 7, 3, 'user_products', 10, 100, 5999.00, '2025-09-12 04:38:40', '2025-09-12 04:38:40'),
(11, '44a0311d-d88b-48d2-94ef-cdc0db77a409', 8, 3, 'user_products', 10, 100, 599.00, '2025-09-12 05:26:04', '2025-09-12 05:26:04'),
(12, 'ccd85386-3765-40c4-b48a-e561dc0b703a', 9, 3, 'user_products', 10, 100, 599.00, '2025-09-12 06:28:06', '2025-09-12 06:28:06'),
(13, '1858dc97-0c96-4da7-a2b0-7b612a1c2cc0', 10, 3, 'user_products', 10, 1, 599.00, '2025-09-12 06:43:26', '2025-09-12 06:43:26'),
(14, 'a46b031b-7af9-4a35-8c3a-5354a61550d1', 11, 3, 'user_products', 8, 1, 599.00, '2025-10-22 08:13:32', '2025-10-22 08:13:32'),
(15, '2d72d70a-de81-41b0-a843-6ca51f96b5d9', 12, 3, 'user_products', 10, 15, 599.00, '2025-10-22 08:32:29', '2025-10-22 08:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_payments`
--

CREATE TABLE `user_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `gateway` enum('cod','razorpay','stripe') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('initiated','pending','successful','failed','refunded') NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_payments`
--

INSERT INTO `user_payments` (`id`, `uuid`, `order_id`, `gateway`, `amount`, `status`, `transaction_id`, `meta`, `created_at`, `updated_at`) VALUES
(1, '3d12ae5e-3569-4980-9b74-35e0a3129bbb', 3, 'cod', 69970.00, 'pending', NULL, '\"{\\\"ip\\\":\\\"127.0.0.1\\\",\\\"user_agent\\\":\\\"PostmanRuntime\\\\\\/7.46.0\\\"}\"', '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(2, 'bb071cd3-f3f1-4130-9ca1-0ba96ff9c684', 4, 'cod', 59990.00, 'pending', NULL, '\"{\\\"ip\\\":null,\\\"user_agent\\\":null}\"', '2025-09-12 04:23:50', '2025-09-12 04:23:50'),
(3, '0e030172-4e34-469a-b504-3e33f7e67ea4', 5, 'cod', 599900.00, 'pending', NULL, '\"{\\\"ip\\\":null,\\\"user_agent\\\":null}\"', '2025-09-12 04:30:38', '2025-09-12 04:30:38'),
(4, '24c53f63-3b07-4028-84c8-3c4acc0860e7', 7, 'razorpay', 599900.00, 'initiated', NULL, '\"{\\\"ip\\\":null,\\\"user_agent\\\":null}\"', '2025-09-12 04:38:40', '2025-09-12 04:38:40'),
(5, '2e9429d2-0fbb-4074-840f-b7a9b66cb9ac', 8, 'razorpay', 59900.00, 'successful', 'rzp_test_RGf1fPWjx9IrVD', '\"{\\\"razorpay_order_id\\\":\\\"order_RGfZDor95GErpl\\\",\\\"razorpay_signature\\\":\\\"78cbb2983012ef9519cf97ab3f2ce5cd55d51e08e8a03dfaeab05040d866c70e\\\"}\"', '2025-09-12 05:26:04', '2025-09-12 07:04:06'),
(6, '63e27391-e2d3-43c3-b8b5-e3c5edb77ffb', 9, 'razorpay', 59900.00, 'successful', 'rzp_test_RGf1fPWjx9IrVD', '\"{\\\"razorpay_order_id\\\":\\\"order_RGgNzRnZSdScxq\\\",\\\"razorpay_signature\\\":\\\"4bd8aac552ca20afc8f8c57c3caeb2e44491317ee2fe20d0a93860537108ce07\\\"}\"', '2025-09-12 06:28:06', '2025-09-12 07:04:06'),
(7, '5f5abba1-34f7-456f-827b-9f50c994ebc6', 10, 'razorpay', 599.00, 'successful', 'rzp_test_RGf1fPWjx9IrVD', '\"{\\\"razorpay_order_id\\\":\\\"order_RGgqGUmve4pGNj\\\",\\\"razorpay_signature\\\":\\\"7b90840a832849674194c236e8046d945be213e6e7c625953267eb53ee081055\\\"}\"', '2025-09-12 06:43:26', '2025-09-12 07:04:06'),
(8, '9fd4507f-21a4-4a80-8496-b9fea6903062', 11, 'razorpay', 599.00, 'successful', 'pay_12345', '\"{\\\"razorpay_order_id\\\":\\\"order_RWY3GdMHjKDO4F\\\",\\\"razorpay_signature\\\":\\\"f0becc6e086b67399db9afc93f58ecc014e11868b578cd05930092a1e46eb684\\\"}\"', '2025-10-22 08:13:32', '2025-10-22 09:16:32'),
(9, '84e20530-407a-4b6d-b3ba-ff0487ec8c0c', 12, 'razorpay', 8985.00, 'successful', 'b3348365-0727-4f50-80c4-73dc51567cf1', '\"{\\\"razorpay_order_id\\\":\\\"order_RXCNrM8OnHPJ4r\\\",\\\"razorpay_signature\\\":\\\"10f28f33590d8f93909ff4664155221c9e5aab759070857ffc5e302042c0eac3\\\"}\"', '2025-10-22 08:32:29', '2025-10-24 00:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_posts`
--

CREATE TABLE `user_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('post','video','reel','carousel') NOT NULL DEFAULT 'post',
  `caption` text DEFAULT NULL,
  `like_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comment_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('active','deleted','flagged') NOT NULL DEFAULT 'active',
  `visibility` enum('public','private','friends') NOT NULL DEFAULT 'public',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_posts`
--

INSERT INTO `user_posts` (`id`, `uuid`, `user_id`, `type`, `caption`, `like_count`, `comment_count`, `status`, `visibility`, `deleted_at`, `created_at`, `updated_at`, `view_count`) VALUES
(1, '0f31d576-faa3-45c2-8dc0-d51ac9cad9d9', 11, 'post', 'first post', 1, 0, 'active', 'private', NULL, '2025-09-17 02:14:48', '2025-11-24 01:01:39', 2),
(2, '27b3e562-e411-478a-9955-8602717dc1a1', 11, 'reel', 'first Reel', 0, 0, 'active', 'public', NULL, '2025-09-17 03:26:53', '2025-11-20 01:15:58', 2),
(3, 'e7f4e8b9-ce51-4c97-ae3b-2acb94607ce5', 211, 'post', 'Sample caption', 1, 0, 'active', 'public', '2025-10-17 04:51:44', '2025-10-17 03:06:28', '2025-10-17 04:51:44', 0),
(4, '809b0f20-db4f-4cca-9932-feed201c4159', 211, 'post', 'Sample caption', 0, 0, 'active', 'public', '2025-10-17 05:01:48', '2025-10-17 04:57:33', '2025-10-17 05:01:48', 0),
(5, '4b2fdd47-a2b4-4ac3-b612-b4b096a35c50', 211, 'post', 'Sample caption', 1, 3, 'active', 'public', NULL, '2025-10-17 05:01:57', '2025-11-20 01:15:58', 2),
(6, '19b14b57-d1d0-4ee7-9f78-8d2278f0a5d0', 211, 'post', 'Sample caption private', 0, 0, 'active', 'private', NULL, '2025-10-17 05:02:16', '2025-11-21 04:59:06', 2),
(7, '8ac69610-127d-4d8d-9ac0-330751f3cc09', 211, 'video', 'Sample caption private', 1, 0, 'active', 'public', NULL, '2025-11-21 05:03:47', '2025-11-24 01:05:19', 1),
(8, '76709418-4609-4e54-a26c-07a6dffa29cb', 211, 'reel', 'first reel', 0, 0, 'active', 'public', NULL, '2025-11-24 01:04:53', '2025-11-24 01:05:09', 1),
(9, '0834f1a8-b1ce-4f13-902e-30dfe17f7c44', 211, 'reel', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 01:40:38', '2025-11-24 01:40:38', 0),
(12, 'ce7fcc40-54f7-4fd9-b6f8-967b61954951', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 01:51:03', '2025-11-24 02:26:28', 1),
(14, '975f733f-d712-4d9c-bc91-65d508b9730e', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 01:54:17', '2025-11-24 02:26:28', 1),
(18, '9799e94b-3d49-4c1d-8065-f2d4c3383f3f', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:09:56', '2025-11-24 02:26:28', 1),
(19, '01bdec5f-4e6f-4eba-9d47-17cc8a296f61', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:14:08', '2025-11-24 02:26:28', 1),
(20, '2eccbc93-61e6-4ccf-97b8-a412c9855c8a', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:14:22', '2025-11-24 02:26:28', 1),
(21, '9d61dd5d-1002-4f36-a2aa-c24c53b62e96', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:16:01', '2025-11-24 02:26:28', 1),
(22, '5667e4ce-465a-4996-aff7-71178333dbe6', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:16:10', '2025-11-24 02:26:28', 1),
(23, 'e297c04f-bf4e-4cad-8b9a-2ffc6127d3c7', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:22:16', '2025-11-24 02:26:28', 1),
(24, 'cd18f1cd-ccaf-40e9-91ab-f405202aa758', 211, 'video', 'reel', 0, 0, 'active', 'public', NULL, '2025-11-24 02:23:58', '2025-11-24 02:26:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_products`
--

CREATE TABLE `user_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_products`
--

INSERT INTO `user_products` (`id`, `uuid`, `seller_id`, `title`, `slug`, `description`, `price`, `stock`, `cover_image`, `status`, `admin_note`, `created_at`, `updated_at`) VALUES
(2, 'eb3050a0-35ed-4c6c-84c1-d5aedc81d9b3', 3, 'Black Shirt XL', 'black-shirt-N4Gto9Za', '100% cotton', 499.00, 20, NULL, 'approved', 'products have no images', '2025-09-11 03:13:51', '2025-09-17 04:50:24'),
(3, '93a858dc-8182-45a4-8b0d-b29e1c708fcf', 3, 'Black Shirt', 'black-shirt-VdOl60Bw', '100% cotton', 499.00, 10, NULL, 'pending', NULL, '2025-09-11 03:14:11', '2025-09-11 03:14:11'),
(4, '5829b596-174a-4a7b-9017-38ed8ba7083a', 3, 'Black Shirt', 'black-shirt-cNdg5kSm', '100% cotton', 499.00, 10, NULL, 'pending', NULL, '2025-09-11 03:14:58', '2025-09-11 03:14:58'),
(5, 'f7cbe8a5-a3a2-4c37-ac52-d76365573d34', 3, 'Black Shirt', 'black-shirt-Jvu3VxXT', '100% cotton', 499.00, 10, NULL, 'pending', NULL, '2025-09-11 03:17:06', '2025-09-11 03:17:06'),
(6, '8c349c17-e52c-4aad-9075-f4380be05c6f', 3, 'Black Shirt', 'black-shirt-H1odIKWT', '100% cotton', 499.00, 10, NULL, 'approved', NULL, '2025-09-11 03:18:16', '2025-09-18 06:14:08'),
(7, 'c0771378-af0e-44ec-9829-f079c429ebae', 3, 'Black Shirt', 'black-shirt-4typupJt', '100% cotton', 499.00, 0, 'ecom/3/products/cover/1757580972_rnsnt1ZK.webp', 'approved', NULL, '2025-09-11 03:26:11', '2025-09-12 00:00:11'),
(8, 'a34995e2-3ab0-4f42-8ea4-58a138a5781c', 3, 'Black Shirt for Men', 'black-shirt-for-men-qKNE3Jmp', '100% cotton', 599.00, 12, 'ecom/3/products/cover/1757581419_ESFewhr1.webp', 'pending', NULL, '2025-09-11 03:33:38', '2025-10-22 09:16:32'),
(9, '9273c597-8dcf-4f91-8d6a-0d62a496c741', 3, 'Black Shirt for Mens', 'black-shirt-for-mens-yrFhutJR', '100% cotton', 5999.00, 15, 'ecom/3/products/cover/1757581455_ojjGrKmw.webp', 'pending', NULL, '2025-09-11 03:34:15', '2025-09-11 03:34:15'),
(10, 'c93df8eb-ddcf-44e3-acf6-5674ccc2cb73', 3, 'Black Shirt for Mens', 'black-shirt-for-mens-HuV9cPnP', '100% cotton', 599.00, 254, 'ecom/3/products/cover/1757581588_GMlWMnq4.webp', 'approved', NULL, '2025-09-11 03:36:28', '2025-10-24 00:47:20'),
(13, 'b4a64179-ffe8-4053-a478-27e815cf6006', 211, 'Amazon Brand - Umi 1 Pc Makeup Organiser Cosmetic Storage Box with Drawer Plastic Case Holder for Home, Dressing Table, Cosmetic, Lipstick, Nail Polish, Brushes, Skincare, Lotions, Perfumes (Pink)', 'amazon-brand-umi-1-pc-makeup-organiser-cosmetic-storage-box-with-drawer-plastic-case-holder-for-home-dressing-table-cosmetic-lipstick-nail-polish-brushes-skincare-lotions-perfumes-pink-iHmBMTTr', 'The makeup organizer with a small drawer and 6 compartments, which can hold makeup items such as lotion, cologne, nail polish, cleanser, brushes, lipstick, nail polish and more, well organized.\nThis cute makeup organizer can not only be used for makeup storage, but also suitable for your various storage needs, such as dresser organizer, student stationery organizer, desk organizer, bathroom organizer and more. Less desk clutter, less prep time, and more space.\nIt is constructed of plastic material, which is eco-friendly, safe and durable. You can safely use it to store delicate makeup and never go out of style.\nSuitable for bedroom, bathroom, office, dressing room, student dormitory, apartment, etc. Product Size : 26 x 17 x 8cm.\nCustomer Note : Cosmetics Are Not Included With The Box.\nPackage included: 1 x Makeup Organizer. Size : 26 x 17 x 8cm. Material : Made of high-quality Plastic.Color : Pink', 599.00, 300, 'ecom/211/products/cover/1761123202_FTe7Qbcu.webp', 'pending', NULL, '2025-10-22 03:23:22', '2025-10-22 03:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_product_images`
--

CREATE TABLE `user_product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_product_images`
--

INSERT INTO `user_product_images` (`id`, `product_id`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 7, 'ecom/3/products/gallery/1757580972_w5MlH2XO.webp', '2025-09-11 03:26:12', '2025-09-11 03:26:12'),
(2, 7, 'ecom/3/products/gallery/1757580973_ArqctSsI.webp', '2025-09-11 03:26:13', '2025-09-11 03:26:13'),
(3, 8, 'ecom/3/products/gallery/1757581419_G5sAyXrh.webp', '2025-09-11 03:33:39', '2025-09-11 03:33:39'),
(4, 9, 'ecom/3/products/gallery/1757581455_h7N5YcFo.webp', '2025-09-11 03:34:15', '2025-09-11 03:34:15'),
(5, 10, 'ecom/3/products/gallery/1757581588_4MT85RaN.webp', '2025-09-11 03:36:28', '2025-09-11 03:36:28'),
(14, 13, 'ecom/211/products/gallery/1761123202_yCHyxaC1.webp', '2025-10-22 03:23:22', '2025-10-22 03:23:22'),
(15, 13, 'ecom/211/products/gallery/1761123202_BMUQHGu5.webp', '2025-10-22 03:23:22', '2025-10-22 03:23:22'),
(16, 13, 'ecom/211/products/gallery/1761123202_SVxn1dri.webp', '2025-10-22 03:23:22', '2025-10-22 03:23:22'),
(17, 13, 'ecom/211/products/gallery/1761123202_g1veXhOd.webp', '2025-10-22 03:23:22', '2025-10-22 03:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_services`
--

CREATE TABLE `user_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_services`
--

INSERT INTO `user_services` (`id`, `uuid`, `seller_id`, `title`, `slug`, `description`, `price`, `status`, `admin_note`, `created_at`, `updated_at`, `cover_image`) VALUES
(2, 'e591d9da-32a2-4efc-9e55-215190797214', 3, 'Service 2', 'service-2-xHD0Ogdo', 'Services 2 description', 1500.00, 'approved', NULL, '2025-09-15 02:58:42', '2025-09-18 06:10:12', NULL),
(6, 'd35b0ba0-ad29-4218-bb4c-ea19c7292525', 211, 'Complete Digital Marketing Package', 'complete-digital-marketing-package-1ErwnGUw', 'A full-service digital marketing package including SEO, SEM, social media management, and content marketing to boost your online presence.', 799.00, 'pending', NULL, '2025-10-22 05:14:34', '2025-10-22 05:14:34', 'ecom/211/services/cover/1761129874_OlF4OZqZ.webp'),
(7, '774f8633-b4c0-4519-afc7-2c684b287759', 211, 'Professional Photography for Event', 'professional-photography-for-events-53HTntbZ', 'Capture your special moments with a professional photographer. I offer services for weddings, parties, corporate events, and more.', 1799.00, 'pending', NULL, '2025-10-22 05:19:38', '2025-10-22 05:21:33', 'ecom/211/services/cover/1761130178_c6wtvthe.webp');

-- --------------------------------------------------------

--
-- Table structure for table `user_service_images`
--

CREATE TABLE `user_service_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_service_images`
--

INSERT INTO `user_service_images` (`id`, `service_id`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 2, 'ecom/3/services/gallery/1757924924_FqC0B2tf.webp', '2025-09-15 02:58:44', '2025-09-15 02:58:44'),
(7, 6, 'ecom/211/services/gallery/1761129875_kXefvp1D.webp', '2025-10-22 05:14:35', '2025-10-22 05:14:35'),
(8, 6, 'ecom/211/services/gallery/1761129875_LXrupZjA.webp', '2025-10-22 05:14:35', '2025-10-22 05:14:35'),
(9, 6, 'ecom/211/services/gallery/1761129875_NrZArBZS.webp', '2025-10-22 05:14:35', '2025-10-22 05:14:35'),
(10, 7, 'ecom/211/services/gallery/1761130178_Yn2N2tPE.webp', '2025-10-22 05:19:38', '2025-10-22 05:19:38'),
(11, 7, 'ecom/211/services/gallery/1761130178_4o4nWb4c.webp', '2025-10-22 05:19:38', '2025-10-22 05:19:38'),
(12, 7, 'ecom/211/services/gallery/1761130178_hqxBDm3D.webp', '2025-10-22 05:19:38', '2025-10-22 05:19:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_shippings`
--

CREATE TABLE `user_shippings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `status` enum('pending','shipped','in_transit','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_shippings`
--

INSERT INTO `user_shippings` (`id`, `uuid`, `order_id`, `provider`, `tracking_number`, `status`, `meta`, `created_at`, `updated_at`) VALUES
(1, '34330f27-fbb5-47b6-96ee-ac2a0ae60a7e', 3, NULL, NULL, 'pending', NULL, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(2, '22047d47-a6a6-445e-a569-7e610975a0d2', 10, 'delhivary', '9876543210', 'pending', NULL, '2025-09-12 07:04:06', '2025-09-12 08:08:34'),
(3, '8a0e9a3e-1f22-4481-bb3f-9c3da161489d', 9, NULL, NULL, 'pending', NULL, '2025-09-12 07:04:06', '2025-09-12 07:04:06'),
(4, '74905385-f6e5-49f7-a44d-16c4bf51cef1', 8, NULL, NULL, 'pending', NULL, '2025-09-12 07:04:07', '2025-09-12 07:04:07'),
(5, '49457795-fd62-45e8-9a54-46b06ab3fbd5', 11, NULL, NULL, 'pending', NULL, '2025-10-22 09:16:32', '2025-10-22 09:16:32'),
(6, '37efaddd-4bee-465b-8167-6033f97beb2b', 12, 'delhevery', '123654987', 'shipped', NULL, '2025-10-24 00:47:20', '2025-10-24 02:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_stories`
--

CREATE TABLE `user_stories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('image','video') DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `like_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_stories`
--

INSERT INTO `user_stories` (`id`, `user_id`, `type`, `caption`, `meta`, `like_count`, `expires_at`, `created_at`, `updated_at`) VALUES
(3, 211, 'video', 'First Story', NULL, 0, '2025-10-19 09:10:57', '2025-10-18 09:10:57', '2025-10-18 09:10:57'),
(4, 211, 'video', 'First Story', NULL, 0, '2025-10-19 09:14:21', '2025-10-18 09:14:21', '2025-10-18 09:14:21'),
(5, 211, 'video', 'First Story', NULL, 0, '2025-10-19 09:21:55', '2025-10-18 09:21:55', '2025-10-18 09:21:55'),
(6, 211, 'video', 'First Story', NULL, 0, '2025-10-19 09:25:26', '2025-10-18 09:25:26', '2025-10-18 09:25:26'),
(7, 211, 'image', 'Images Story', NULL, 1, '2025-10-20 05:40:03', '2025-10-19 23:30:56', '2025-10-20 00:10:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_story_highlights`
--

CREATE TABLE `user_story_highlights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `cover_media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_story_highlights`
--

INSERT INTO `user_story_highlights` (`id`, `user_id`, `title`, `cover_media_id`, `created_at`, `updated_at`) VALUES
(1, 211, 'Travel', NULL, '2025-10-20 00:50:46', '2025-10-20 00:50:46'),
(2, 211, 'Friends', NULL, '2025-10-20 01:09:21', '2025-10-20 01:09:21'),
(3, 211, 'Friends', 18, '2025-10-20 01:17:42', '2025-10-20 01:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_story_likes`
--

CREATE TABLE `user_story_likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `story_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_story_likes`
--

INSERT INTO `user_story_likes` (`id`, `story_id`, `user_id`, `created_at`, `updated_at`) VALUES
(8, 7, 211, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `viewable_type` varchar(255) NOT NULL,
  `viewable_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `views`
--

INSERT INTO `views` (`id`, `user_id`, `viewable_type`, `viewable_id`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 211, 'App\\Models\\UserPost', 2, '127.0.0.1', '2025-10-17 08:19:47', '2025-10-17 08:19:47'),
(2, 211, 'App\\Models\\UserPost', 5, '127.0.0.1', '2025-10-18 00:24:20', '2025-10-18 00:24:20'),
(5, 211, 'App\\Models\\Jobs\\UserJobPost', 5, '127.0.0.1', '2025-10-24 09:05:02', '2025-10-24 09:05:02'),
(6, 1, 'App\\Models\\UserPost', 6, '127.0.0.1', '2025-11-20 01:15:58', '2025-11-20 01:15:58'),
(7, 1, 'App\\Models\\UserPost', 5, '127.0.0.1', '2025-11-20 01:15:58', '2025-11-20 01:15:58'),
(8, 1, 'App\\Models\\UserPost', 2, '127.0.0.1', '2025-11-20 01:15:58', '2025-11-20 01:15:58'),
(9, 1, 'App\\Models\\UserPost', 1, '127.0.0.1', '2025-11-20 01:15:58', '2025-11-20 01:15:58'),
(10, 211, 'App\\Models\\UserPost', 6, '127.0.0.1', '2025-11-21 04:59:06', '2025-11-21 04:59:06'),
(11, 211, 'App\\Models\\UserPost', 7, '127.0.0.1', '2025-11-24 01:01:39', '2025-11-24 01:01:39'),
(12, 211, 'App\\Models\\UserPost', 1, '127.0.0.1', '2025-11-24 01:01:39', '2025-11-24 01:01:39'),
(13, 211, 'App\\Models\\UserPost', 8, '127.0.0.1', '2025-11-24 01:05:09', '2025-11-24 01:05:09'),
(14, 211, 'App\\Models\\UserPost', 24, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(15, 211, 'App\\Models\\UserPost', 23, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(16, 211, 'App\\Models\\UserPost', 22, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(17, 211, 'App\\Models\\UserPost', 21, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(18, 211, 'App\\Models\\UserPost', 20, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(19, 211, 'App\\Models\\UserPost', 19, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(20, 211, 'App\\Models\\UserPost', 18, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(21, 211, 'App\\Models\\UserPost', 14, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28'),
(22, 211, 'App\\Models\\UserPost', 12, '127.0.0.1', '2025-11-24 02:26:28', '2025-11-24 02:26:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`),
  ADD KEY `activity_log_causer_id_index` (`causer_id`),
  ADD KEY `activity_log_subject_id_index` (`subject_id`),
  ADD KEY `activity_log_created_at_index` (`created_at`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_logs_admin_id_target_type_target_id_index` (`admin_id`,`target_type`,`target_id`);

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ads_user_id_foreign` (`user_id`),
  ADD KEY `ads_ad_package_id_foreign` (`ad_package_id`);

--
-- Indexes for table `ad_impressions`
--
ALTER TABLE `ad_impressions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_impressions_ad_id_foreign` (`ad_id`),
  ADD KEY `ad_impressions_user_id_foreign` (`user_id`);

--
-- Indexes for table `ad_interest`
--
ALTER TABLE `ad_interest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_interest_ad_id_foreign` (`ad_id`),
  ADD KEY `ad_interest_interest_id_foreign` (`interest_id`);

--
-- Indexes for table `ad_packages`
--
ALTER TABLE `ad_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ad_payments`
--
ALTER TABLE `ad_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_payments_ad_id_foreign` (`ad_id`),
  ADD KEY `ad_payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `followers_follower_id_following_id_unique` (`follower_id`,`following_id`),
  ADD KEY `followers_follower_id_index` (`follower_id`),
  ADD KEY `followers_following_id_index` (`following_id`);

--
-- Indexes for table `highlight_story`
--
ALTER TABLE `highlight_story`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `highlight_story_highlight_id_story_id_unique` (`highlight_id`,`story_id`),
  ADD KEY `highlight_story_story_id_foreign` (`story_id`);

--
-- Indexes for table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `interests_name_unique` (`name`),
  ADD KEY `interests_interest_category_id_foreign` (`interest_category_id`);

--
-- Indexes for table `interest_categories`
--
ALTER TABLE `interest_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `interest_categories_name_unique` (`name`);

--
-- Indexes for table `interest_user`
--
ALTER TABLE `interest_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `interest_user_user_id_foreign` (`user_id`),
  ADD KEY `interest_user_interest_id_foreign` (`interest_id`);

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
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `likes_user_id_likeable_type_likeable_id_unique` (`user_id`,`likeable_type`,`likeable_id`),
  ADD KEY `likes_likeable_type_likeable_id_index` (`likeable_type`,`likeable_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  ADD KEY `notifications_notifiable_read_idx` (`notifiable_type`,`notifiable_id`,`read_at`),
  ADD KEY `notifications_created_at_idx` (`created_at`);

--
-- Indexes for table `notification_unread_counts`
--
ALTER TABLE `notification_unread_counts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_unread_counts_user_unique` (`user_id`),
  ADD KEY `notification_unread_counts_unread_idx` (`unread_count`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_uuid_unique` (`uuid`),
  ADD KEY `orders_buyer_id_status_placed_at_index` (`buyer_id`,`status`,`placed_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_seller_id_foreign` (`seller_id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `posts_uuid_unique` (`uuid`),
  ADD KEY `posts_user_id_status_created_at_index` (`user_id`,`status`,`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_user_id_status_index` (`user_id`,`status`);

--
-- Indexes for table `reels`
--
ALTER TABLE `reels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reels_uuid_unique` (`uuid`),
  ADD KEY `reels_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_reported_by_foreign` (`reported_by`),
  ADD KEY `reports_resolved_by_foreign` (`resolved_by`),
  ADD KEY `reports_reportable_type_reportable_id_status_index` (`reportable_type`,`reportable_id`,`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stories_user_id_expires_at_index` (`user_id`,`expires_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_addresses_uuid_unique` (`uuid`),
  ADD KEY `user_addresses_user_id_foreign` (`user_id`),
  ADD KEY `user_addresses_order_id_foreign` (`order_id`);

--
-- Indexes for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_carts_uuid_unique` (`uuid`),
  ADD KEY `user_carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_cart_items`
--
ALTER TABLE `user_cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_cart_items_uuid_unique` (`uuid`),
  ADD KEY `user_cart_items_cart_id_foreign` (`cart_id`),
  ADD KEY `user_cart_items_item_type_item_id_index` (`item_type`,`item_id`);

--
-- Indexes for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_chats_uuid_unique` (`uuid`),
  ADD KEY `user_chats_owner_id_foreign` (`owner_id`);

--
-- Indexes for table `user_chat_messages`
--
ALTER TABLE `user_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_chat_messages_uuid_unique` (`uuid`),
  ADD KEY `user_chat_messages_chat_id_foreign` (`chat_id`),
  ADD KEY `user_chat_messages_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `user_chat_participants`
--
ALTER TABLE `user_chat_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_chat_participants_chat_id_user_id_unique` (`chat_id`,`user_id`),
  ADD KEY `user_chat_participants_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_jobs`
--
ALTER TABLE `user_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_jobs_uuid_unique` (`uuid`),
  ADD KEY `user_jobs_posted_by_foreign` (`posted_by`);

--
-- Indexes for table `user_job_applications`
--
ALTER TABLE `user_job_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_job_applications_job_id_applicant_id_unique` (`job_id`,`applicant_id`),
  ADD UNIQUE KEY `user_job_applications_uuid_unique` (`uuid`),
  ADD KEY `user_job_applications_applicant_id_foreign` (`applicant_id`);

--
-- Indexes for table `user_job_posts`
--
ALTER TABLE `user_job_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_job_posts_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `user_job_posts_slug_unique` (`slug`),
  ADD KEY `user_job_posts_employer_id_foreign` (`employer_id`);

--
-- Indexes for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_orders_uuid_unique` (`uuid`),
  ADD KEY `user_orders_buyer_id_foreign` (`buyer_id`);

--
-- Indexes for table `user_order_items`
--
ALTER TABLE `user_order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_order_items_uuid_unique` (`uuid`),
  ADD KEY `user_order_items_order_id_foreign` (`order_id`),
  ADD KEY `user_order_items_item_type_item_id_index` (`item_type`,`item_id`);

--
-- Indexes for table `user_payments`
--
ALTER TABLE `user_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_payments_uuid_unique` (`uuid`),
  ADD KEY `user_payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `user_posts`
--
ALTER TABLE `user_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_posts_uuid_unique` (`uuid`),
  ADD KEY `user_posts_user_id_status_created_at_index` (`user_id`,`status`,`created_at`);

--
-- Indexes for table `user_products`
--
ALTER TABLE `user_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_products_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `user_products_slug_unique` (`slug`),
  ADD KEY `user_products_seller_id_foreign` (`seller_id`);

--
-- Indexes for table `user_product_images`
--
ALTER TABLE `user_product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `user_services`
--
ALTER TABLE `user_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_services_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `user_services_slug_unique` (`slug`),
  ADD KEY `user_services_seller_id_foreign` (`seller_id`);

--
-- Indexes for table `user_service_images`
--
ALTER TABLE `user_service_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_service_images_service_id_foreign` (`service_id`);

--
-- Indexes for table `user_shippings`
--
ALTER TABLE `user_shippings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_shippings_uuid_unique` (`uuid`),
  ADD KEY `user_shippings_order_id_foreign` (`order_id`);

--
-- Indexes for table `user_stories`
--
ALTER TABLE `user_stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_stories_user_id_foreign` (`user_id`),
  ADD KEY `user_stories_expires_at_index` (`expires_at`);

--
-- Indexes for table `user_story_highlights`
--
ALTER TABLE `user_story_highlights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_story_highlights_user_id_foreign` (`user_id`),
  ADD KEY `user_story_highlights_cover_media_id_foreign` (`cover_media_id`);

--
-- Indexes for table `user_story_likes`
--
ALTER TABLE `user_story_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_story_likes_story_id_user_id_unique` (`story_id`,`user_id`),
  ADD KEY `user_story_likes_user_id_foreign` (`user_id`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `views_viewable_type_viewable_id_index` (`viewable_type`,`viewable_id`),
  ADD KEY `views_user_id_viewable_id_viewable_type_index` (`user_id`,`viewable_id`,`viewable_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ad_impressions`
--
ALTER TABLE `ad_impressions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ad_interest`
--
ALTER TABLE `ad_interest`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ad_packages`
--
ALTER TABLE `ad_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ad_payments`
--
ALTER TABLE `ad_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `highlight_story`
--
ALTER TABLE `highlight_story`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `interests`
--
ALTER TABLE `interests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `interest_categories`
--
ALTER TABLE `interest_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `interest_user`
--
ALTER TABLE `interest_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=884;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `notification_unread_counts`
--
ALTER TABLE `notification_unread_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `reels`
--
ALTER TABLE `reels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_carts`
--
ALTER TABLE `user_carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_cart_items`
--
ALTER TABLE `user_cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_chats`
--
ALTER TABLE `user_chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_chat_messages`
--
ALTER TABLE `user_chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `user_chat_participants`
--
ALTER TABLE `user_chat_participants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_jobs`
--
ALTER TABLE `user_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_job_applications`
--
ALTER TABLE `user_job_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_job_posts`
--
ALTER TABLE `user_job_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_orders`
--
ALTER TABLE `user_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_order_items`
--
ALTER TABLE `user_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_posts`
--
ALTER TABLE `user_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_products`
--
ALTER TABLE `user_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_product_images`
--
ALTER TABLE `user_product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_services`
--
ALTER TABLE `user_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_service_images`
--
ALTER TABLE `user_service_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_shippings`
--
ALTER TABLE `user_shippings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_stories`
--
ALTER TABLE `user_stories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_story_highlights`
--
ALTER TABLE `user_story_highlights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_story_likes`
--
ALTER TABLE `user_story_likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ad_package_id_foreign` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_impressions`
--
ALTER TABLE `ad_impressions`
  ADD CONSTRAINT `ad_impressions_ad_id_foreign` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ad_impressions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ad_interest`
--
ALTER TABLE `ad_interest`
  ADD CONSTRAINT `ad_interest_ad_id_foreign` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ad_interest_interest_id_foreign` FOREIGN KEY (`interest_id`) REFERENCES `interests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_payments`
--
ALTER TABLE `ad_payments`
  ADD CONSTRAINT `ad_payments_ad_id_foreign` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ad_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `highlight_story`
--
ALTER TABLE `highlight_story`
  ADD CONSTRAINT `highlight_story_highlight_id_foreign` FOREIGN KEY (`highlight_id`) REFERENCES `user_story_highlights` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `highlight_story_story_id_foreign` FOREIGN KEY (`story_id`) REFERENCES `user_stories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interests`
--
ALTER TABLE `interests`
  ADD CONSTRAINT `interests_interest_category_id_foreign` FOREIGN KEY (`interest_category_id`) REFERENCES `interest_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interest_user`
--
ALTER TABLE `interest_user`
  ADD CONSTRAINT `interest_user_interest_id_foreign` FOREIGN KEY (`interest_id`) REFERENCES `interests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interest_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_unread_counts`
--
ALTER TABLE `notification_unread_counts`
  ADD CONSTRAINT `notification_unread_counts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reels`
--
ALTER TABLE `reels`
  ADD CONSTRAINT `reels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `user_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD CONSTRAINT `user_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_cart_items`
--
ALTER TABLE `user_cart_items`
  ADD CONSTRAINT `user_cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `user_carts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD CONSTRAINT `user_chats_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_chat_messages`
--
ALTER TABLE `user_chat_messages`
  ADD CONSTRAINT `user_chat_messages_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `user_chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_chat_participants`
--
ALTER TABLE `user_chat_participants`
  ADD CONSTRAINT `user_chat_participants_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `user_chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_chat_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_jobs`
--
ALTER TABLE `user_jobs`
  ADD CONSTRAINT `user_jobs_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_job_applications`
--
ALTER TABLE `user_job_applications`
  ADD CONSTRAINT `user_job_applications_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_job_applications_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `user_job_posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_job_posts`
--
ALTER TABLE `user_job_posts`
  ADD CONSTRAINT `user_job_posts_employer_id_foreign` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD CONSTRAINT `user_orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_order_items`
--
ALTER TABLE `user_order_items`
  ADD CONSTRAINT `user_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `user_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_payments`
--
ALTER TABLE `user_payments`
  ADD CONSTRAINT `user_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `user_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_posts`
--
ALTER TABLE `user_posts`
  ADD CONSTRAINT `user_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_products`
--
ALTER TABLE `user_products`
  ADD CONSTRAINT `user_products_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_product_images`
--
ALTER TABLE `user_product_images`
  ADD CONSTRAINT `user_product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `user_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_services`
--
ALTER TABLE `user_services`
  ADD CONSTRAINT `user_services_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_service_images`
--
ALTER TABLE `user_service_images`
  ADD CONSTRAINT `user_service_images_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `user_services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_shippings`
--
ALTER TABLE `user_shippings`
  ADD CONSTRAINT `user_shippings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `user_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_stories`
--
ALTER TABLE `user_stories`
  ADD CONSTRAINT `user_stories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_story_highlights`
--
ALTER TABLE `user_story_highlights`
  ADD CONSTRAINT `user_story_highlights_cover_media_id_foreign` FOREIGN KEY (`cover_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_story_highlights_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_story_likes`
--
ALTER TABLE `user_story_likes`
  ADD CONSTRAINT `user_story_likes_story_id_foreign` FOREIGN KEY (`story_id`) REFERENCES `user_stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_story_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
