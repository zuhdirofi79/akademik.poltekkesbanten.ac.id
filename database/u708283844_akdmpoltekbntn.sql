-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 10 Jun 2026 pada 01.41
-- Versi server: 11.8.6-MariaDB-log
-- Versi PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u708283844_akdmpoltekbntn`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admin_activity_logs`
--

INSERT INTO `admin_activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'login', 'Login admin berhasil.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-09 04:34:19'),
(2, 2, 'login', 'Login admin berhasil.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-09 08:31:00'),
(3, 2, 'logout', 'Logout admin.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-09 09:31:34'),
(4, 2, 'login', 'Login admin berhasil.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 00:33:37'),
(5, 2, 'change_password', 'Password akun diperbarui.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 00:45:09'),
(6, 2, 'logout', 'Logout admin.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 00:46:49'),
(7, 2, 'login', 'Login admin berhasil.', '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 01:37:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('layanan-akademik-poltekkes-kemenkes-banten-cache-497e88c8b7499e652cf42d29ab5e6670a5b354ef', 'i:1;', 1781055536),
('layanan-akademik-poltekkes-kemenkes-banten-cache-497e88c8b7499e652cf42d29ab5e6670a5b354ef:timer', 'i:1781055536;', 1781055536);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `email_templates`
--

INSERT INTO `email_templates` (`id`, `code`, `name`, `subject`, `body`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'submission_received', 'Pengajuan Diterima', 'Pengajuan Layanan Akademik - {{tiket}}', 'Halo {{nama}}, pengajuan {{layanan}} dengan nomor tiket {{tiket}} telah kami terima. Simpan token progres Anda: {{token}}. Cek progres di {{link_progres}}.', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(2, 'submission_completed', 'File Selesai', 'File Layanan Akademik Tersedia - {{tiket}}', 'Halo {{nama}}, pengajuan {{layanan}} dengan nomor tiket {{tiket}} sudah selesai. Silakan cek progres di {{link_progres}}.', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(3, 'submission_status_changed', 'Perubahan Status', 'Update Status Pengajuan - {{tiket}}', 'Halo {{nama}}, status pengajuan {{layanan}} ({{tiket}}) berubah dari {{status_lama}} menjadi {{status_baru}}. Catatan: {{catatan}}. Cek progres di {{link_progres}}.', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
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
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_09_000001_add_role_to_users_table', 1),
(5, '2026_06_09_000002_create_study_programs_table', 1),
(6, '2026_06_09_000003_create_service_types_table', 1),
(7, '2026_06_09_000004_create_submissions_table', 1),
(8, '2026_06_09_000005_create_submission_files_table', 1),
(9, '2026_06_09_000006_create_status_histories_table', 1),
(10, '2026_06_09_000007_create_email_templates_table', 1),
(11, '2026_06_09_100001_update_users_for_roles', 1),
(12, '2026_06_09_100002_create_admin_activity_logs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_types`
--

CREATE TABLE `service_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_types`
--

INSERT INTO `service_types` (`id`, `code`, `name`, `slug`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'PDM', 'Perubahan Data Mahasiswa Aktif', 'perubahan-data-mahasiswa-aktif', 'Untuk mengusulkan perbaikan data mahasiswa aktif seperti NIM, NIK, nama, tempat/tanggal lahir, dan nama ibu kandung.', 1, 1, '2026-06-09 02:56:18', '2026-06-09 09:43:17'),
(2, 'SKAK', 'Surat Keterangan Aktif Kuliah (Legacy)', 'surat-aktif-kuliah', 'Layanan lama — dialihkan ke Surat Keterangan Aktif Kuliah.', 0, 99, '2026-06-09 02:56:18', '2026-06-09 09:46:55'),
(3, 'REGISTRASI', 'Surat Keterangan Registrasi', 'surat-keterangan-registrasi', 'Untuk mengusulkan pembuatan Surat Keterangan Registrasi.', 1, 3, '2026-06-09 02:56:18', '2026-06-09 09:46:55'),
(4, 'CUTI', 'Surat Cuti Mahasiswa', 'surat-cuti-mahasiswa', 'Untuk mengusulkan Surat Cuti Mahasiswa.', 1, 4, '2026-06-09 02:56:18', '2026-06-09 09:46:55'),
(5, 'SKAL', 'Surat Keterangan Alumni (Legacy)', 'surat-keterangan-alumni', 'Layanan lama — dialihkan ke Surat Keterangan Verifikasi Ijazah Alumni.', 0, 99, '2026-06-09 02:56:18', '2026-06-09 09:46:55'),
(6, 'SIAKAD', 'Layanan Sistem Informasi Akademik (SIAKAD)', 'siakad', 'Untuk melaporkan kendala SIAKAD seperti lupa password, login, KRS, dan KHS.', 1, 9, '2026-06-09 02:56:18', '2026-06-09 09:46:55'),
(8, 'AKTIF_KULIAH', 'Surat Keterangan Aktif Kuliah', 'surat-keterangan-aktif-kuliah', 'Untuk mengusulkan pembuatan Surat Keterangan Aktif Kuliah.', 1, 2, '2026-06-09 09:43:17', '2026-06-09 09:43:17'),
(11, 'BEASISWA', 'Surat Keterangan Beasiswa', 'surat-keterangan-beasiswa', 'Untuk mengusulkan pembuatan Surat Keterangan Beasiswa.', 1, 5, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(12, 'BERKELAKUAN_BAIK', 'Surat Keterangan Berkelakuan Baik', 'surat-keterangan-berkelakuan-baik', 'Untuk mengusulkan pembuatan Surat Keterangan Berkelakuan Baik.', 1, 6, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(13, 'VERIFIKASI_IJAZAH_ALUMNI', 'Surat Keterangan Verifikasi Ijazah Alumni', 'surat-keterangan-verifikasi-ijazah-alumni', 'Untuk mengusulkan pembuatan Surat Keterangan Verifikasi Ijazah Alumni.', 1, 7, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(14, 'REKOMENDASI', 'Surat Rekomendasi Mahasiswa/Alumni', 'surat-rekomendasi', 'Untuk mengusulkan Surat Rekomendasi Mahasiswa/Alumni, termasuk keperluan magang dan keperluan lainnya.', 1, 8, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(15, 'PDM_LEGACY', 'Perbaikan Data Mahasiswa (Legacy)', 'perbaikan-data-mahasiswa', 'Layanan lama — dialihkan ke Perubahan Data Mahasiswa Aktif.', 0, 99, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(16, 'AKTIFKULIAH', 'Surat Aktif Kuliah (Legacy)', 'aktifkuliah', 'Layanan lama — dialihkan ke Surat Keterangan Aktif Kuliah.', 0, 99, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(17, 'MASALAH_SIAKAD', 'Masalah SIAKAD (Legacy)', 'masalahsiakad', 'Layanan lama — dialihkan ke Layanan Sistem Informasi Akademik (SIAKAD).', 0, 99, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(18, 'KETREGIS_LEGACY', 'Ketregis (Legacy)', 'ketregis', 'Layanan lama — dialihkan ke Surat Keterangan Registrasi.', 0, 99, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(19, 'KETCUTI_LEGACY', 'Ketcuti (Legacy)', 'ketcuti', 'Layanan lama — dialihkan ke Surat Cuti Mahasiswa.', 0, 99, '2026-06-09 09:46:55', '2026-06-09 09:46:55'),
(20, 'KETALUMNI_LEGACY', 'Ketalumni (Legacy)', 'ketalumni', 'Layanan lama — dialihkan ke Surat Keterangan Verifikasi Ijazah Alumni.', 0, 99, '2026-06-09 09:46:55', '2026-06-09 09:46:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
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
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('538TYHJ3g5O79ETSuJVvKQwHbAbZfEMwAqMeEIXu', NULL, '34.90.48.51', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'eyJfdG9rZW4iOiJDSDEyWU9WNk02eEJvZ2xhWXJJZ3lvQ2llU2NRY0NZY0YwdThtdGRmIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYWthZGVtaWsucG9sdGVra2VzYmFudGVuLmFjLmlkXC9iZXJhbmRhIiwicm91dGUiOiJiZXJhbmRhIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1781008893),
('9osZEmcTRJnSCDP2js1McVR6fjDeMWd12mk1eQ8H', NULL, '44.211.35.18', 'DataHubFargateScanner/1.0 (+domain liveness and security fingerprinting; no port scan)', 'eyJfdG9rZW4iOiJ3U2RHMVJZbGk2ZWZPalhoVmFEUzhvcktub0hCakxSSndMOXAyQWRwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYWthZGVtaWsucG9sdGVra2VzYmFudGVuLmFjLmlkIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1781016725),
('9wuhY3MylUn4eTOGxCFJ0DlVPsMCWRukrCSil7UJ', 2, '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJtYldFMVA2Z0JKSGx6ak9zQnRLcHlFUWEwSGFMYjdwbUdzanNPRDVHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2FkbWluXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImFkbWluLmRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyfQ==', 1781055476),
('AEKX7bV4376juTCKm3qAJk6xlD4UhhNB0QU3B85f', NULL, '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIybUtheGZENFJDZWN2ZFVuZmhnSmxsWUxaWnYwNDBzcjlNVkVWOGdMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1780997515),
('Dcy4ECedpOoQ9jfB9PExHvQLpHsgdL1KxEpMDm5D', NULL, '2404:c0:9602:603:553e:520a:28ca:53f6', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmOG9UTlk0dGQ2WjRZdzhvOUdrMnBEMVFuUzl3SGZya1RJYUw4UTFCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1780990548),
('fKBrt1Pragwiyo35xpuB9Hwz2r4X2MdG61meRIDs', NULL, '182.2.191.43', 'WhatsApp/2.23.20.0', 'eyJfdG9rZW4iOiJ3ZHpSRmY0VkR0TElDclNURmpGTzZJdWFUSmg2SDhPaGlhR29nUldkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1781051784),
('hXpjWjE7IxTjaSlMdo0X2YUFtPm7oWHKDHImTme3', NULL, '44.211.35.18', 'DataHubFargateScanner/1.0 (+domain liveness and security fingerprinting; no port scan)', 'eyJfdG9rZW4iOiJEaDU5S1p2ZmFvdkpSY0NicUdidmhDcEFOTFRNdXIzQWdVa2VQZHZtIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYWthZGVtaWsucG9sdGVra2VzYmFudGVuLmFjLmlkXC9iZXJhbmRhIiwicm91dGUiOiJiZXJhbmRhIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1781016726),
('I0GY3tv1er1D3ezqcl9OWcgfmHMBsyyXiI2zDZTu', NULL, '146.148.49.84', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'eyJfdG9rZW4iOiJQeUxMOGJjREN6ZlZwQ2txRWI0UkNSOGliSnB6M001eDR1R1l4UHFOIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYWthZGVtaWsucG9sdGVra2VzYmFudGVuLmFjLmlkXC9iZXJhbmRhIiwicm91dGUiOiJiZXJhbmRhIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1781019196),
('Lk1cqgN6o1ku0XfC7CIWy8hhfMGgwyQaQGASNR6E', NULL, '77.91.96.90', 'Mozilla/5.0 validator/1.0', 'eyJfdG9rZW4iOiJjOFN6dlZtMkJsbTh4SE5la2gzaHNvZ3dXeFRNNkVZQVVHMlZUUUxEIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1780991976),
('NmabNycwfekg9g5iSpsOCngiK42VVuvNqQo1XPCl', NULL, '146.148.49.84', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'eyJfdG9rZW4iOiJkRTJla1QyeEtSblR0NXFrWWQ2OTljQ2RGdUl5cXljak5GNHF0ZjkwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYWthZGVtaWsucG9sdGVra2VzYmFudGVuLmFjLmlkIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1781019196),
('Q032OOOCL4WRsOCW7Fcinz1cRKYCmjRSvlP1G4c3', NULL, '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIyNjNUTmtUdFRDS3MxQzB3ODdrcmZKcFpVV0NSYUxiMGV5QzdXQlJDIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2FrYWRlbWlrLnBvbHRla2tlc2JhbnRlbi5hYy5pZFwvYmVyYW5kYSIsInJvdXRlIjoiYmVyYW5kYSJ9fQ==', 1781052942),
('QUKzDHCpeO3vsMkaJZZkFIcwXZd5Gm5hUywQuorz', NULL, '140.213.128.231', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJKWTlkOGNwZmVPNWVWTDlWSTJjbkNDTUJxZk9KS1lHRHFoSkQ0WDNzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1781049126),
('T1M4DJX5IynTzUaAnReYKlntS06bE5APdSSypofM', NULL, '77.91.96.90', 'Mozilla/5.0 validator/1.0', 'eyJfdG9rZW4iOiJQWDZpb2dUVmtnUm9FM2hPa2dqVXE3Zjk3TDZCY2dxTjkyekNnYWNJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWQiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780991975),
('tTJuWcg9buWBqExlNQRKO2snACYBYHUqP6zUxbFa', NULL, '103.177.177.217', 'WhatsApp/2.23.20.0', 'eyJfdG9rZW4iOiJITmd4SWF2dU9kWkU5YlB2RzNscVRWMHBuOFB6QjY2UUQ3TmFLSVo1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2FkbWluXC9sb2dpbiIsInJvdXRlIjoiYWRtaW4ubG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1781052417),
('UJXmupdARwRF5mX2oXOznVG1ITwDBRUYZrPz1Dby', NULL, '35.194.61.148', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'eyJfdG9rZW4iOiJ0cFF3THF6c3p2QmI0SG9DU0YzY3NYNTNmTlJyQWpMSGdOZGlST3hVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1781013749),
('UtJhnfEGzMtDKDluNmVMRras7oxTtCTQ5CG7fGOV', NULL, '34.90.48.51', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'eyJfdG9rZW4iOiI3VXl5RXFuSVBRY1NQQnFMU0ZjMnh3eWlHNlNQQmwwSnJrVDJ0NEVIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYWthZGVtaWsucG9sdGVra2VzYmFudGVuLmFjLmlkIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1781008892),
('x8LaMf3JypF9AaNe407Pb9YYGJxb7W4wWNLD3GnZ', NULL, '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJQVGFkM1UzdDltY1IyaTVidFNDZ0xFQktoeDQ4ZFJ6RGNsQnBTRjM4IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2FrYWRlbWlrLnBvbHRla2tlc2JhbnRlbi5hYy5pZFwvYmVyYW5kYSIsInJvdXRlIjoiYmVyYW5kYSJ9fQ==', 1780998466),
('xjYmZwNUSrRac0Bm0rOCLutUlPkrMgpM6Ft6CUMh', NULL, '35.194.61.148', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'eyJfdG9rZW4iOiJ1Mks0Q2V1bFN3UGN1V1UyOWl4TkNnV1hPTE1zNjVBZFVVd1VnbnYwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWQiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1781013748),
('ZJuY7YybBVtDVDFlYAUcpqW2uJhkOB4JjVzXxywB', NULL, '103.177.177.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJidThDSEhOckY5dHljeEp5bnZmVVl0aThrSENBOVJKZ2JxUTVLZjM4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2xheWFuYW5cL3NpYWthZCIsInJvdXRlIjoic2VydmljZXMuc2lha2FkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1781052338),
('zVuWqcqHBNaWDNgcyoLH3okqxgCEF3N3S9DKZWFg', NULL, '140.213.128.231', 'WhatsApp/2.23.20.0', 'eyJfdG9rZW4iOiJsNE02UUxOekpURzFLRXlDOEFwaXVZbmtYTGNYWW95SXZiYk85QTFTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9ha2FkZW1pay5wb2x0ZWtrZXNiYW50ZW4uYWMuaWRcL2JlcmFuZGEiLCJyb3V0ZSI6ImJlcmFuZGEifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1781049133);

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_histories`
--

CREATE TABLE `status_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `old_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `status_histories`
--

INSERT INTO `status_histories` (`id`, `submission_id`, `old_status`, `new_status`, `note`, `changed_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Diajukan', 'Pengajuan dibuat oleh pemohon.', NULL, '2026-06-10 00:51:47', '2026-06-10 00:51:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `study_programs`
--

CREATE TABLE `study_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `study_programs`
--

INSERT INTO `study_programs` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'D3 Keperawatan', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(2, 'D3 Kebidanan', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(3, 'D3 Teknologi Laboratorium Medis', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(4, 'Sarjana Terapan Keperawatan', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(5, 'Sarjana Terapan Kebidanan', 1, '2026-06-09 02:56:18', '2026-06-09 02:56:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `submissions`
--

CREATE TABLE `submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `progress_token` varchar(64) NOT NULL,
  `service_type_id` bigint(20) UNSIGNED NOT NULL,
  `study_program_id` bigint(20) UNSIGNED NOT NULL,
  `nim` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `request_type` varchar(255) DEFAULT NULL,
  `corrected_data` text DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `issue_type` varchar(255) DEFAULT NULL,
  `leave_reason` text DEFAULT NULL,
  `entry_year` varchar(255) DEFAULT NULL,
  `graduation_year` varchar(255) DEFAULT NULL,
  `alumni_letter_type` varchar(255) DEFAULT NULL,
  `certificate_number` varchar(255) DEFAULT NULL,
  `additional_note` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Diajukan',
  `admin_note` text DEFAULT NULL,
  `ready_to_send_email` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `email_error` text DEFAULT NULL,
  `data_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `submissions`
--

INSERT INTO `submissions` (`id`, `ticket_number`, `progress_token`, `service_type_id`, `study_program_id`, `nim`, `full_name`, `nik`, `email`, `whatsapp`, `semester`, `mother_name`, `request_type`, `corrected_data`, `purpose`, `issue_type`, `leave_reason`, `entry_year`, `graduation_year`, `alumni_letter_type`, `certificate_number`, `additional_note`, `status`, `admin_note`, `ready_to_send_email`, `email_sent_at`, `email_error`, `data_confirmed`, `created_at`, `updated_at`) VALUES
(1, 'AKD-2026-000001', '4d24855f9fe3584a432cb160ed846b49f6f0ef0a3289c484124af0daf30cbb36', 12, 3, '08148679503123', 'Qowiy', '3154687032432656', 'qowiyrofi@gmail.com', '6281945821517', '2', NULL, NULL, NULL, 'Untuk Tes CPNS', NULL, NULL, NULL, NULL, NULL, NULL, 'Untuk Tes CPNS', 'Diajukan', NULL, 0, NULL, NULL, 1, '2026-06-10 00:51:47', '2026-06-10 00:51:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `submission_files`
--

CREATE TABLE `submission_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `file_category` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` enum('applicant','admin') NOT NULL DEFAULT 'applicant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `submission_files`
--

INSERT INTO `submission_files` (`id`, `submission_id`, `file_category`, `original_name`, `stored_path`, `mime_type`, `size`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'registration_proof', '20260526103203NOTA DINAS PERMOHONNAN SISTEM PELAYANAN AKADEMIK 2026_preview (1).pdf', 'submissions/1/registration_proof/69293e10-1b1d-4cf2-9c4e-2bbafd83a5c7.pdf', 'application/pdf', 214652, 'applicant', '2026-06-10 00:51:47', '2026-06-10 00:51:47'),
(2, 1, 'active_krs', '20260526103203NOTA DINAS PERMOHONNAN SISTEM PELAYANAN AKADEMIK 2026_preview (1).pdf', 'submissions/1/active_krs/5a465ce2-f05a-41fb-8c6e-9224a2f65fa4.pdf', 'application/pdf', 214652, 'applicant', '2026-06-10 00:51:47', '2026-06-10 00:51:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `must_change_password`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Qowiyrofi', 'qowiyrofi@poltekkesbanten.ac.id', NULL, '$2y$12$y7kJQBIVlcq2JiUlmPi4IeljqwVEyDBa7rDaWtxX7lByyELRy4pI.', 'superadmin', 1, 1, NULL, '2026-06-09 02:56:18', '2026-06-09 02:56:18'),
(2, 'Kuntum Ekawati', 'kuntum.ekawati@poltekkesbanten.ac.id', NULL, '$2y$12$5VXpTDW1R9zMnhXElNp6Z.G3tNB8YLo9TwK2UhbASeEEu6rXpyO.O', 'superadmin', 0, 1, NULL, '2026-06-09 02:56:18', '2026-06-10 00:45:09');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_activity_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `admin_activity_logs_action_index` (`action`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_templates_code_unique` (`code`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_types_code_unique` (`code`),
  ADD UNIQUE KEY `service_types_slug_unique` (`slug`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `status_histories`
--
ALTER TABLE `status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_histories_submission_id_foreign` (`submission_id`),
  ADD KEY `status_histories_changed_by_foreign` (`changed_by`);

--
-- Indeks untuk tabel `study_programs`
--
ALTER TABLE `study_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `submissions_ticket_number_unique` (`ticket_number`),
  ADD UNIQUE KEY `submissions_progress_token_unique` (`progress_token`),
  ADD KEY `submissions_service_type_id_foreign` (`service_type_id`),
  ADD KEY `submissions_study_program_id_foreign` (`study_program_id`),
  ADD KEY `submissions_status_created_at_index` (`status`,`created_at`),
  ADD KEY `submissions_nim_index` (`nim`),
  ADD KEY `submissions_email_index` (`email`);

--
-- Indeks untuk tabel `submission_files`
--
ALTER TABLE `submission_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_files_submission_id_foreign` (`submission_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `status_histories`
--
ALTER TABLE `status_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `study_programs`
--
ALTER TABLE `study_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD CONSTRAINT `admin_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `status_histories`
--
ALTER TABLE `status_histories`
  ADD CONSTRAINT `status_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `status_histories_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_service_type_id_foreign` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_study_program_id_foreign` FOREIGN KEY (`study_program_id`) REFERENCES `study_programs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `submission_files`
--
ALTER TABLE `submission_files`
  ADD CONSTRAINT `submission_files_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
