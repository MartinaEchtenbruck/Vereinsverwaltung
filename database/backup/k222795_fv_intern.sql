-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 10.35.233.43:3306
-- Erstellungszeit: 14. Nov 2026 um 00:42
-- Server-Version: 8.4.13
-- PHP-Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `k222795_fv_intern`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_bank_reconciliations`
--

CREATE TABLE `accounting_bank_reconciliations` (
  `id` bigint UNSIGNED NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `reconciled_until` date NOT NULL,
  `performed_on` date NOT NULL,
  `receipts_complete` tinyint(1) NOT NULL DEFAULT '0',
  `transactions_complete` tinyint(1) NOT NULL DEFAULT '0',
  `note_enc` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_fingerprint` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `accounting_bank_reconciliations`
--

INSERT INTO `accounting_bank_reconciliations` (`id`, `fiscal_year_id`, `reconciled_until`, `performed_on`, `receipts_complete`, `transactions_complete`, `note_enc`, `created_by`, `created_at`, `booking_fingerprint`) VALUES
(1, 2, '2026-08-31', '2026-11-11', 1, 1, NULL, 3, '2026-11-11 23:39:08', '4f53cda18c2baa0c0354bb5f9a3ecbe5ed12ab4d8e11ba873c2f11161202b945'),
(2, 2, '2026-09-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:01:18', 'af7028794b2cf7e4a158026635800f142d7c0e172725e48a8a5ea945523c3812'),
(3, 2, '2026-10-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:01:52', '9cd45a3ce11d9d7dca4239e043ff02a39a9ed22cf017f25f9de5b7ee337605e5'),
(4, 3, '2023-08-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:06:19', '59ea9175237e4696b8d7465bdc81aa79a46201125d97d75b15741e89daa687db'),
(5, 3, '2023-09-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:06:33', 'ea05c45c46e7c3c4e73e66062b48163d3b10376b53c4f167cab1598fdf8ba8c2'),
(6, 3, '2023-10-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:06:42', '2864e05a264aa1f80501e3f89f1d17eb4490d806a860239b10d341755e7660fb'),
(7, 2, '2026-09-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:45:10', 'bea6374742c7b7669a49277b56db4f97cb482e902a56e5edc17039deff43283e'),
(8, 2, '2026-10-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 00:45:21', '231d494234b2d55536ce11572ee51a6e39a6c1d5c9d0cf1f34c3f3dce2c01824'),
(9, 3, '2023-11-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:50:48', NULL),
(10, 3, '2023-12-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:51:01', NULL),
(11, 3, '2024-01-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:51:58', NULL),
(12, 3, '2024-03-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:52:33', NULL),
(13, 3, '2024-04-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:53:18', NULL),
(14, 3, '2024-05-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:53:53', NULL),
(15, 3, '2024-06-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:54:42', NULL),
(16, 3, '2024-07-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 08:55:03', NULL),
(17, 4, '2024-08-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:14:13', NULL),
(18, 4, '2024-09-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:14:51', NULL),
(19, 4, '2024-10-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:16:13', NULL),
(20, 4, '2024-11-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:19:17', NULL),
(21, 4, '2024-12-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:20:25', NULL),
(22, 4, '2025-01-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:20:48', NULL),
(23, 4, '2025-02-28', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:21:04', NULL),
(24, 4, '2025-03-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:21:11', NULL),
(25, 4, '2025-04-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:21:22', NULL),
(26, 4, '2025-05-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:21:53', NULL),
(27, 4, '2025-06-30', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:22:04', NULL),
(28, 4, '2025-07-31', '2026-11-12', 1, 1, NULL, 3, '2026-11-12 10:22:20', NULL),
(29, 1, '2025-09-30', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:32:05', NULL),
(30, 1, '2025-10-31', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:32:24', NULL),
(31, 1, '2025-11-30', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:33:52', NULL),
(32, 1, '2025-12-31', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:35:14', NULL),
(33, 1, '2026-01-31', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:35:26', NULL),
(34, 1, '2026-02-28', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:35:37', NULL),
(35, 1, '2026-03-31', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:35:47', NULL),
(36, 1, '2026-04-30', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:35:57', NULL),
(37, 1, '2026-05-31', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:36:06', NULL),
(38, 1, '2026-06-30', '2026-11-13', 1, 1, NULL, 3, '2026-11-13 10:36:23', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_bookings`
--

CREATE TABLE `accounting_bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `booking_type` enum('income','expense','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_channel` enum('bank','cash') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_from` enum('bank','cash') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_to` enum('bank','cash') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `category_enc` text COLLATE utf8mb4_unicode_ci,
  `tax_area` enum('ideell','asset_management','purpose_operation','commercial_business') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paper_reference_enc` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `accounting_bookings`
--

INSERT INTO `accounting_bookings` (`id`, `fiscal_year_id`, `booking_date`, `booking_type`, `payment_channel`, `transfer_from`, `transfer_to`, `category_id`, `category_enc`, `tax_area`, `description_enc`, `amount_enc`, `paper_reference_enc`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, '2023-08-01', 'income', 'cash', NULL, NULL, NULL, 'v1:k2bbsCjAJ02+UXF+6q1K3GE8mhpvGUVAK0TzTm5/Q4MWYqgVs0whufqgjC9xUQ==', 'commercial_business', 'v1:Ix4iMY1j+B5WdJf+ABsUWTn7N8sn2H1fe6pllPdpXpwM6UoCxpcweli+fPemIb2OZxGWuYZsjBdM', 'v1:nxao3Le7s/1xqJeF8Ak1xoLzlmAj+XnaPhC4RpZEkq2qWh8=', NULL, NULL, '2026-11-09 20:59:23', '2026-11-13 10:30:42'),
(2, 3, '2023-08-02', 'expense', 'bank', NULL, NULL, 2, NULL, 'ideell', 'v1:IOIEtGN/8+zYo+pERekdPC/7II3oa/qPXVldO2bWD1rDw4jS18s9+Cfsy/JDuzOvUuFIavVdhB90M6Wn', 'v1:/iyICxGUIzXRJQOBLs4+0N7PQVeyhNmuKSmmXtKqL61wz9I=', 'v1:YSzTsUoQDYZhoMYtycpEJwqeiumugCrnQM352tmuSNTwdfUKL62CYg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(3, 3, '2023-09-28', 'income', 'bank', NULL, NULL, NULL, 'v1:IKFN/jZ5wpcOn/oLAUy6JHXB8mfPwvwlDotFAMp+eHxyfXM=', 'ideell', 'v1:pIacHs8LjEhkiH0uiebShp1W8eHmVrBReFD2tiz/TQLb9DTwO/RkXsrS', 'v1:K9YIHugs9dhhxnUtC07Tevq/USmQcc+vLB4SQJADZN6bzw==', 'v1:LTgSz01msZtrlMRNMq5PVQBGmGBGa6knAf/Nud3/t8QyVvTLU6brvqJK30uj7A==', NULL, '2026-11-09 20:59:23', '2026-11-10 14:50:39'),
(4, 3, '2023-09-30', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:vqq+k6oMYLKe7JB7oleLpFOTB6iUw0CfiQbBdXSgylzXe3/NrnrGlnP2GOv94x5ScQ==', 'v1:Njb18PM8CnZVlVyduKDSZujhXLI8jDM6F3D1voeB2zY=', 'v1:2TfUTnnQW5JD/A/bCdVV39uKcJtnwx+X9OMgzVeO7EXrLd+AeQnLcX160O3CRA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(5, 3, '2023-10-31', 'income', 'bank', NULL, NULL, NULL, 'v1:c01tl2RxpcveOpEjbY3Y2KJlTIlM4CVBYRBiJRwNo5WeaQ014b2NDa19tfeCXQ==', 'ideell', 'v1:TSER/0ZO1roz3/WKPppUP4BtUmNJKqP63RYsIIXWdaKKRs9eZpIoWgg6to1sUgM=', 'v1:buneA06MO2AcfxR/HMbZSANL4I74AYSSCyky1kHJncwQGsY=', 'v1:fzXzy0FVUK+XKrUR8smNK7lEKnak4ApsT6vQKwdG9zyZvL7Y7x0mlRJ72TzcXw==', NULL, '2026-11-09 20:59:23', '2026-11-10 14:51:23'),
(6, 3, '2023-11-01', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:UdRMo28zl/SrHaLNhPcRYp+ivTRFPxkJ8I6yN+Urb4+Ikc+sme9o7f364NXFi4xCJks8QQ==', 'v1:resuC3VKlCSn+jRCeiuywbzcJL/+mmkbwv/uUgysY58a7g==', 'v1:r/H8ymG2jLV1Y0ULbjpX2sl4ADcXu3dNrJar1ZMQRa2QBwjgXrvN', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(7, 3, '2023-11-02', 'income', 'bank', NULL, NULL, 7, NULL, 'commercial_business', 'v1:cxtEOeXlramNwoIsU6RftJ4tLxkKbItUevqc0D3G56aeb+leJiVIcPmSP01VON9VFt35QKl2', 'v1:akPf5FztNwgpn2EGVuojlsngRhv0HTuJDPlgZDpccqwfYw==', 'v1:/G2bLzFHEymCGx6IqQH70ii7+TCOzvoWif2VklU7QswxBz7Or1hpQ+5uj28Mjg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(8, 3, '2023-11-03', 'expense', 'bank', NULL, NULL, 2, NULL, 'ideell', 'v1:SlW/ZuIAgdxDTCBO1QQ/YmbComqeBxlDWFjtZyFw6yYVGo42TfQVeWi0aJFBszS3e6g/Bf4IDYqT4z17q4ZHtrRlpLueBXtMOrcx', 'v1:dvfS4S3U2crUUt/dyHpBVUv6d/Fo6o8IVFmixRUlGcI4sw==', 'v1:RXycq5cILVkjf/aijekLWfOdR7+gkkqYoMBYGOAXP4pJ9oMnkzofH668AxM4LA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(9, 3, '2023-11-08', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:6SQrBlgn74+FdUCOnznuXoqWZzVZeDz1jLhMCE+aP1TnSNrMLliCwUg=', 'v1:CMTCrPWnFhZxz+x1z0KRgcv8UZHbQfCqQlA+YuzuhMAz9xk=', 'v1:2wJsAue3bYcfAueHB2wNqBn+KAvIou6tUBbxZXStA5rQ+A4abuOw', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(10, 3, '2023-11-08', 'expense', 'cash', NULL, NULL, 7, NULL, 'ideell', 'v1:khpaUpK642d31his2Uy6uLmR9EIkxJuYDHWIwcT3rglVvwRluNSEzBEjGABPywVaN52hT38TRM90mJDl0SuK1C041ewa4sMSLKIYyQ==', 'v1:qyoZgbDX/mne7vlrnWcnIdBmEDkMw48Rg87jFhtDhzk=', 'v1:HllcAiGWCuMNYt4PsyF+qxcV8Tp5sYxAOjViAlXSfFAaOx+00+8k', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(11, 3, '2023-11-13', 'expense', 'cash', NULL, NULL, 7, NULL, 'ideell', 'v1:37Ug+Drr4SV5znj1h5ivFSH5bMVd457ENZ87HMUkLVQdBpdfbfR1j8y/fqxQy3AfcW2Cb71gsjNwKKE5ie4hFuc=', 'v1:IqNCElb5teJ8+h7b6HTBL/9IxUhyIKPaCQ/mRq0PBmuP', 'v1:KZYxntDbDqkrRmxjG1NcVlbdPMrnsCXjHcjAUgy6qF7ZbYMDjVd5', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(12, 3, '2023-11-13', 'expense', 'cash', NULL, NULL, NULL, 'v1:HI6efe97TzA0dw2PkgBqIHfvick07P5HwgUhcYlpIxfYNL1Taso=', 'commercial_business', 'v1:0KT94YcBJKObkyePA3Ya6Poy+4UMb/3KBfS+xkJ7D8Km85XlFzVi9MElUHZg9Pj9qJg=', 'v1:/UXBvTUjkKcSOgsvlnwqiLcE97dpUdw+B2tmg0OWIHJsBg==', 'v1:MroD7tx7lDwpOG7dDveH+shRdmonRLmA2GaHH6NI9QzO+6EgnhZb', NULL, '2026-11-09 20:59:23', '2026-11-12 08:50:30'),
(13, 3, '2023-11-13', 'income', 'cash', NULL, NULL, NULL, 'v1:DLLB7S3nhsDpcbU6VG8MmZ0n/HlLHhor7H4xITvyz/1CEucoAFg=', 'commercial_business', 'v1:uosaqarozTeqFf2QVCd2OLfjINJ8KhkoDUby+sec4Ql6v335lN4Z6D5O7UA=', 'v1:ChoiDv5GWaeCkziZ0Yeid+jgM7xBWar/bVtpMWXMKwMz', NULL, NULL, '2026-11-09 20:59:23', '2026-11-12 00:07:28'),
(14, 3, '2023-11-14', 'income', 'bank', NULL, NULL, NULL, 'v1:BveswjlNKjUizjnj6okQtJOjPXVUKWlRFK2NPT+3J+JYv0o=', 'ideell', 'v1:kby/q0REH130eZXK6scCJ3WEslTljd2w7xVZovtwhoBgILTVoPBUIfJyOQ==', 'v1:fk98zaUw4Xn3ejHN9kijol2NRN1JNr+UASnD3SbskgeO2w==', 'v1:uVptBtU43pUdfeCm4WCx94aGRB5SvgB5WI+/WIJqS6DOI5NksalxWHNfLfKNEg==', NULL, '2026-11-09 20:59:23', '2026-11-10 14:52:16'),
(15, 3, '2023-11-15', 'income', 'bank', NULL, NULL, 1, NULL, 'ideell', 'v1:NC7Wp8oxne865RnSagqobM9NrmS27YY0SmW+4ZPytxuxpOhPVdgaloRY1n9FNiaVYU6ChQ==', 'v1:zRvRYusm8pHsNfdziVL7qZRyXNxI91GWPK2DLWhI959e', 'v1:VLbNxH451SjX8eKELUFOo8H9qg6Xy+WcnPeIAmBkY/BMTbnZ2JOkfgbLydZvXQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(16, 3, '2023-11-21', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:qbMKOf7WIV/5R/2pVv6j3UCGGMCiDx/zcmz6pqBYwPY+I0xzjlGNmac=', 'v1:n/wX9dPDxTQMdwFfMj614ORRHaKFy2X//9Md8bD8U1VB', 'v1:gJbipYhQrOQTNxNivtLBGZJPIFTxeLFe0mTgI7kZ3NbV/3lk2fOayw==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(17, 3, '2023-11-21', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:ltFc2ULWdIwE9wVGhjO1Kxv+3PvUJhN1SKv069yvMwxQwVCrZ57RclKr7tYgbNOBjt3J0N5EhuGpBJlDUHbz', 'v1:LVShRPvod/GoUVAPh3r/0s5H2zodz8RPHSMx+h5BRn8o', 'v1:UBQCgwcndBCGKKvJXyqMB9wPqW0BGh0/2M2z2m64o+5J3W5NsaLKnKHVvICp', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(18, 3, '2023-11-21', 'expense', 'cash', NULL, NULL, NULL, 'v1:Q+MswUd3z+67didI7Yttl+gv1QMk7jAkIG/xEge4lbFAymh51PU=', 'commercial_business', 'v1:ohBSKDvPTb00+pSq7Jy1dB+jGXWVad5I8p8vf4Vv1JYYGX0w39Cmryd5pbGue39PhWtxwes=', 'v1:QV+jyEu7hefne5dAwR3M6AOJeQjQSW9XYx+y15qapKP7', 'v1:mqu5aBb79KH0r4r3hdIlZwSvAP8KnlXiT0xKnjapgXV6BqUhLsjnb08=', NULL, '2026-11-09 20:59:23', '2026-11-12 00:07:40'),
(19, 3, '2023-11-23', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:CrQCN55rFrCRQvmo2kUueGByncdf1NLcA2nLIkXCheW/oHACxdtBQQnWv8O3EYzCiRp3GJL8c7UYMR544SCvrPbbXqfPcg==', 'v1:+fnm4DcUJzUQRA/zrHgNLVrFarb58bNfrjE8l32sZ6fs', 'v1:zCKbn8M9ovK/7hmxnl0ZFENJrls9jzGa7nTJ8MekRXbRFL8a0kIYT5Y5', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(20, 3, '2023-11-25', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:EZVaIlK6xu7nnGPep3+nweY4T6VRJU3GFXRadmPVTtDRs2u+6nm3', 'v1:MmqnjQci38TRsL6h77lgSaYdJFp9dfn7wiGm65jRQf7r', 'v1:aYD41QvjaCJIxE8UlPBMr+C3jewq39F1WFwtSk5cB3xTydBZTeipOw==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(21, 3, '2023-11-27', 'expense', 'cash', NULL, NULL, 6, NULL, 'ideell', 'v1:AnSDZdHTjVYEdFsq4+9jMMgoXUSNpiDRD22kebc0vuN/tsOTbPJYGw==', 'v1:EXpv5GnFHt4niL8Nhs7bOAmovIXXlWfNGf76KxkDPvLt', 'v1:HWkbHWf5EJ/6GyMHLSBlsweCTm0dB+oDgO8TqlArdODiqGJnBzF+cQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(22, 3, '2023-12-15', 'expense', 'bank', NULL, NULL, 2, NULL, 'ideell', 'v1:13IQN35eoLhSEIfQCteNBhcuvVlv6ZmXHskiZ3J2lHQ5kIIiKMmI0n9ndclFCESVtUcSp8i6TpDpyM9aVdZ5jdw=', 'v1:4TGZpx05EVeNdL3gFN/dgo3KFlBubMWgL4X1apLa3aIu', 'v1:sElacjOdIHuffNvsgFK9VKphcYHuoQ0vZTUA31JvzlYnvxXzPvY48oBOtWJ8/JU=', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(23, 3, '2023-12-19', 'income', 'bank', NULL, NULL, NULL, 'v1:wPb5nc5k//UJsDs1A1+ruSbnFU1wzMtIZVXVvX7DcEsenLI=', 'ideell', 'v1:ASVZhJSpUVDRSBPYJe7Gkcr2lCeIVZK6pnOmGRtIXYJwARwRvzyTR+1SDA==', 'v1:3wZhZJ64VGUadkNRd4nRPcf9dOXQXdFthgKaOOX1QgnL', 'v1:icm468b6iZtUDZZDFWZq5pRS9DWasxXDz3ru9zOWwjDhj8oxLurxBilj1dpNUqQ=', NULL, '2026-11-09 20:59:23', '2026-11-10 15:04:59'),
(24, 3, '2023-12-19', 'income', 'bank', NULL, NULL, NULL, 'v1:P/DNyirKAH8Xhxs2zV0uPBXJvl297eLc+8KiGuGchVx7Jqw=', 'ideell', 'v1:El7cftSK+8ufp8yMwdfwMLwn8Iv8ctRbDw77oVKUTIqxSVF2ak8KoQk=', 'v1:s8enHfnHZQlNJ7dY53dfyosqdPVs+cG0K2xSs15bvbO8RA==', 'v1:EcqqYraOw7g4zGkyian3IseAqQyCky0INxiARlWnygVUDpqAWHlVdcvBzy96xF4=', NULL, '2026-11-09 20:59:23', '2026-11-10 15:05:10'),
(25, 3, '2023-12-29', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:bDwcRgq93lbqQlHLopwV+xLXP5guHin8UZa77QA+sVIbJQ1VhH4D46ut3kC/idivQA==', 'v1:axrMh3eolPaxCt+gn+nhlfGuce6LUE1cQzxbjUAr4V0k', 'v1:AbIKIdFGgWys1JBfURwWPchpxP5gCau2Niep5CwMfveqZErOBsIc6XsvOnrBkPk=', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(26, 3, '2024-01-04', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:UxkjLNxq4kDz8dBQ+43YUrQ2phEu70o9jzpXCzRzJdjFaAqjLQxsdUSq9cCDY3ows7biMIHiQC0xo3P5V4aV3HVkoL/r7Cvqi5FfzHKYIhE8lsPd7g==', 'v1:Optn4TpPY1YNv8SYkJiU+R6wWAWkSr9w2J3YVu2oTl5z', 'v1:XsSSyGItdQ73Jx9yGrWfqdUx7G5Gh2RmG3zILz8MamDiIgwY0g==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(27, 3, '2024-03-28', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:ljP5L52s6ezCQt9OYkXWT4SE+/CAlU/QKy3e17a6mPrdaQ3BeoS1/VkOgj0cKXOjnA==', 'v1:lJZPZe8UmIuDk1P0DwnBGbtemJ2SIVVe+HbQsG670hs=', 'v1:4Ph8yQurdjTrrcavy43k+fQrLRxDw5WqdOuwMDZepXVXMztM0CZXyeBQnvBfYw==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(28, 3, '2024-04-12', 'income', 'cash', NULL, NULL, NULL, 'v1:IW8/ksCspVFPH72qtG8fyLG8UaI1bcFqbsApQs3ugFBglK4=', 'ideell', 'v1:iOfSt2+T5iGRK57amnQciMHRobUu/yNKawLLUA2tk44cL8pgB8vmbkupoublk2Gy2Xj43VbHuv4=', 'v1:nQj4mudZ8rQSIXk5rs/UNgBPYeVoyRmC0qyqXuXZDOeXPw==', NULL, NULL, '2026-11-09 20:59:23', '2026-11-10 15:05:23'),
(29, 3, '2024-04-15', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:siXPHw4rM7xUUvcyivX5SjjjYPZ5g6FUHYXugs+B6SGpAsU0Fjo2h8nEnaBEWwMXTyQ/', 'v1:EXAfl1qfqQwa8RUOeWRPgrgM46oDzcAf1th6va10y0E6OQ==', 'v1:br3mmccCmL5uMJ1Gy6eIMnMIMFvzbP4STaeOKVud7Dn+w0YRrWe2Kg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(30, 3, '2024-04-19', 'expense', 'cash', NULL, NULL, 8, NULL, 'ideell', 'v1:3q+3YFgQRuXh4OVGmOVF9yZ2ZAZ15jfNt7eOEKgbse6ErJYjiA==', 'v1:h8cV4IwZn2rQOwyrWS+mAD0mZBISyjtRe5olH87hoqYs6w==', 'v1:l3fG52B2pOSbeal3xC2uFRilU6mPO4xv9c8F8XG3VtHY+B5Pll8gcg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(31, 3, '2024-04-24', 'expense', 'cash', NULL, NULL, 8, NULL, 'ideell', 'v1:6lZjVGTX/M0hSQ7eN4jDisrYJEFF6vTzli5ctmxAO0YdsNt1vg==', 'v1:QC+LiGtRTneY9RcIubVeU7b1qgePXS6a0Zq5/Bz+Q7sJZQ==', 'v1:4sbFRkO0AItHK+mF9Q3ixQe/fa3zQkMrj22ojDe1IRp1lt+wFd2BmA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(32, 3, '2024-04-24', 'expense', 'bank', NULL, NULL, 2, NULL, 'ideell', 'v1:c97F2uQlH9Ud9rYfIW6MOaq/rg3r54SmbpyVUbRR+IvQ/12nDRJFGQfFQf9wZL5kxctPwsrEL4KRPifPlaCyRftT3A==', 'v1:tWuIPqMuwygeg7aW7UufvZDQ90bhEb+kbyXG2d1AlhqE', NULL, NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(33, 3, '2024-04-24', 'expense', 'bank', NULL, NULL, 6, NULL, 'ideell', 'v1:vsxnmsQ4T/0pZkanAXspVu0K/l+m6XY7WPDq7HkQgvikLF4igHyDpl0FszjdoeLCC3VBL05uZWf9', 'v1:LJWOZVvF9gIVesnt5yhocGtulKMdMtJFiOTNR0F7RPpzZw==', 'v1:qVkg0ny8mKUZD0JvQ2pUCfxlrjHQ9Kbja8a9sQA5NEkZPcyGUT2O1A==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(34, 3, '2024-05-02', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:/dq0dLGc2dwfw1ERerxnUJj+cW99BsKuM1Z/pccOyOeTelTQ9/pwZjoEqO5VIuyEcg==', 'v1:+q3HOZrfO1NGsQwhJpSA5yBV5Bk599d1DaQXEV0zN+Q=', 'v1:EVX9m3PKyOhKisJhjreQlIDCdkG0hdba4Be09XBwzl67poMLwN2ixccs9URuIQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(35, 3, '2024-05-02', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:NZOCH7Am5FS4mo4zep8NW4rATOrEgdUGKRs2XzDG517EjdQR1shTtfHU', 'v1:XPX3FWp+WEAKQi9lazEt95v3nFu4j8QmDN2iqDp7FbNl', 'v1:xWxm257HMBCitU1FxOHKD4Iw5A2cVn9M74hV5O5qiEOTYVXTV/DReA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(36, 3, '2024-05-11', 'income', 'cash', NULL, NULL, NULL, 'v1:1+Uc+FB/QOhhmRGGI2YfEspbCXjybbVqXJ6kSFBdieLMwW8RfsrusChWeH12eWmRZQ==', 'commercial_business', 'v1:wJqM93dpHN30ccdDi2BE1Y/+HYA1kDMysOESH28xl0TZ61lF4zwY/XZEdoi+fvWGeDSY1Ig=', 'v1:of0WaJqlCm+drb9sTZ3PBUBPkoE00Th/VvX6zbUn0TP5PQ==', NULL, NULL, '2026-11-09 20:59:23', '2026-11-12 08:53:39'),
(37, 3, '2024-05-14', 'expense', 'bank', NULL, NULL, 10, NULL, 'commercial_business', 'v1:wak/2ZU3+dWygMwehPvio9FNfwlTFycvo6ug8qUwz3SixxAc8kptcAcz2v/gxWkAYAnE', 'v1:FLV51ls4tYyE/1H3LO8idJitj3lcS0kxgazxcNrSRYFm', 'v1:e1+NG1YG9u3JMxSHTaceWDONF82A94NxMLf8TiUEYTxpBFI5CUYQAg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(38, 3, '2024-05-22', 'expense', 'bank', NULL, NULL, 8, NULL, 'ideell', 'v1:10NAPhKP76BsfCAnlqFzS1Iu0AXNOjcjXIQnFeXF0/icyLUHNy8EkQWz43EDybM=', 'v1:UDJgBv4HeqtPKm0BCBqqKY8TkYZTl7R/pY/rOCs+hl9jHg==', 'v1:+3nZu/ZXjKPUADKN8okVWuHtI/CM0CItW9t6EANDXs2xzXtcbUZKmA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(39, 3, '2024-05-27', 'expense', 'cash', NULL, NULL, 11, NULL, 'purpose_operation', 'v1:5/peR9AWiTtFoMNrMdwzow3x6BNvgNmrc+2g1fNuU23uIdVcWDzsuUktKUtQx1p+wVKNYzcV6w==', 'v1:gFT92xqLeJoR3Fk85xJdEaelKrGH6/jGIud55MzpbkHP', 'v1:lAn1LLnPP7V5aMDAc1fyCSnaFNj35DN9snXWmbNumeBWV1kx1YJ4kg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(40, 3, '2024-05-29', 'expense', 'cash', NULL, NULL, 11, NULL, 'purpose_operation', 'v1:5Vtj/rlSnWN1JaR5xeZ+VkK8hBhd6bLQ8YX4QAoSoI1eE0M3DiONR1wRU97d', 'v1:GHuQqNnKRTTZ/dzU6X/cyTMpSARkNt8ovCn0FZwPCWOl', 'v1:7LJSJen3gQ6zPtzFIfrB2zAskqpjaRNcXGfLeVo/Yo98JIB26dhvUw==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(41, 3, '2024-06-01', 'income', 'cash', NULL, NULL, 12, NULL, 'commercial_business', 'v1:uNAyqKtwyDpD6EECvPaUAq0tOAlSvssoPZbZrSHfpXAss8cfrZJQtOfum8gqE+Em/A3KMlvjqA==', 'v1:UUEUz2G6ASpiwxI+gNb3atmLt8stFEfeqFz3k0IfVVZNZQ==', NULL, NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(42, 3, '2024-06-06', 'expense', 'cash', NULL, NULL, 8, NULL, 'ideell', 'v1:zIKmmKUd+eLqhbA+zY8D3JJoFFzRGA5+EfKQ7ETnhO/rZljjBe2qJPnijQ==', 'v1:XEzwhGlBHxTHj++lZ//OtUeYZfurQI2eZW87TiDXbzRZzw==', 'v1:a/ZwmNzqovAx0eStG/4fAC9W0i1wbOnOpwTT9rQC4pSKqrMDYpk+VA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(43, 3, '2024-06-06', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:pIu2JH6L4jnjNXYCKhSUNh4ostDOUJ9oDXrbIOWIBCMBGys6VzKdP/fH4SabwFRl', 'v1:RFAut9zxf8HHfVObp+fxORTKkZUy3gkkVfeHsBRBw7mL', 'v1:K26sMPizZWzxujLwispd06p/qwXWAq7umW1Y1Ivq9rj22qAB3MvbzSOWarIJJQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(44, 3, '2024-06-06', 'income', 'bank', NULL, NULL, 13, NULL, 'commercial_business', 'v1:fCwgvozDt1vRno3ohUp5X6h2oj4q2PAyWamgiPPNPHWtH7xxiiXu389GXi8yPGQdu80=', 'v1:2H4cmQH+bxZ8IlHTQbfEcUaPbpvkwfXK5VTP6OPVy0AH9g==', 'v1:AN7TjAin4Mz71umq3konxKEj+/GnjyA6/wHVUd/AiGg3l16WiuHpba04koLTAg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(45, 3, '2024-06-07', 'expense', 'cash', NULL, NULL, 11, NULL, 'purpose_operation', 'v1:tX4HXYe5CI6/cldHTDUhmj0VJftav8cY1yZOJ4BJdgQAObB3njf5ofmgR+9h', 'v1:5HN1Sl5sd8bpMja7pu8uJR+54Kb+m7RruNaXXcjkVF3s', 'v1:Rh0ObN0Az59NJj5Fw+SOnFiG4kgHMjjlIeiVqK7Wke2nE3dKe/hR4g==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(46, 3, '2024-06-08', 'expense', 'cash', NULL, NULL, 10, NULL, 'commercial_business', 'v1:4slYjhAkOG56bze7abFSxzncxJQr54xCOdXGape6U8DxscHYZqL5PTNZqCQtQ/9z+LhQrz3VeswvyOjr', 'v1:9hFH7Yqns2JXgxyc3a89nvOfamkSNJpSmFh7ZiwHNoTa', 'v1:m+djQRS09/2cVZCi4CD1keMnd6xOSGPRFR/VR/9+ojXPaocUHfvvwg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(47, 3, '2024-06-08', 'income', 'cash', NULL, NULL, 10, NULL, 'commercial_business', 'v1:QAdYQ3ejBJH81DUFxnyrEjPMdpPz7QZDQMwXM33y9OKYyWh45tuGWeu0VYJuLgUGJS3+H8nFklI=', 'v1:4xbS+ptw8USmsPJGsIrCZwR+sPwsyfbTyOCYlQy75V/dFQ==', NULL, NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(48, 3, '2024-06-08', 'income', 'cash', NULL, NULL, 10, NULL, 'commercial_business', 'v1:71/IdFqnSDZGusNJ/TxGelaSlZ68YdTWxim9rldeqI6k5eFwUbAoRqsy2P30dO4kug==', 'v1:lLtsfVT026ufn1K3sfATLn//gFZr22J3s8or9LpJL1fGng==', NULL, NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(49, 3, '2024-06-08', 'expense', 'cash', NULL, NULL, 10, NULL, 'commercial_business', 'v1:uTt0LokgainGBZ+g9PcgAcRtCr4uT+zlrok8YAYa0YDt4Ewd0q0RJqKBWreUF6ZvGlHLODqiTEo=', 'v1:WgrZxmzwkDel5YVqVIH51dgeNQDtM+ANLBFwZfZvpga9', 'v1:DF+Nb/R4rw8Ielchnum27nUC8PwUwhvqQcgakwSdnbKPs4uAIP4XCg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(50, 3, '2024-06-08', 'income', 'cash', NULL, NULL, 10, NULL, 'ideell', 'v1:tpQpvjHWJkJXVZ0CiJGWLfHS/u/NgIgI687V+VolQiYeMb49o5EkYuQhT8k28EkmPmec6gKa5TGbOg==', 'v1:bqyx4o4zM3Q++tA2cbzk6q8IMfrvv7EpOIIzjJt5/BrNqg==', NULL, NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(51, 3, '2024-06-10', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:kMNLuvbZqGGWosqbYVUsVThlwMrABTkqFju5WQV9NEHhmaDhEP/nmBkmHfP4k/TsZP8TJ+kL0b/5m5cGaQd7giIZ', 'v1:YOj9EepCSGHfFyLmizupeSWF9OqC2Hp4USBUIaeJfP3M', 'v1:vuNxb0PoZfBfyRBcCweCEQs9xTKAfkIEYPh7sCF4hjFRdGPkJGqrDmArtOZh9w==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(52, 3, '2024-06-11', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:Eccaojk4+svirQasyY/8lynF8n/D23vKWWFLD9n0pYyH6oWqpM3FNthJ2ypjCJBl8HnRHe8dLsT7jQu/JRiPvQ==', 'v1:HamtRow41NtJxFg584po8TQhHX/EFslHcrilUOJSeYq9', 'v1:nPO611qH+yXQg83jh1UujoALKOgJ4K/iJ23WCjLLWZRDrUuOT8b/ADluVnL/Sg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(53, 3, '2024-06-13', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:UyjCDscj4SrceYU5wLKMguC/R3lxW6lstEok21wL9IZ1Ksz9+Nul3Qpa3xBeNA/Pt1GgRbd2KQAsuOe/MX8e9jWo6whPzoPym4Q+dE+eB/gQ5cMdA9wA7C3mxZgt0lb2PYqZvYFYMg1UgAMU7Fyqm7yWw3znZibVqA==', 'v1:7PV2OJuOcdjsrSj1UgPnW6PK1Z/i16IN4o0Mqaae9P0wdA==', 'v1:lB+lWlbfFjXolEGiY/fCJlaLzMcF2J3nLlNh5TosCUuktFW6XRBxCiM+EmCeOA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(54, 3, '2024-06-14', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:fBQKSomB22DePit17zMzehEHtutWq+m1VqtvaPT/lAfJTudB684hsUASiwmrG17Dg6dCo603muyLR2akAa9WMBKEQ84o2bo1KGMyaOP/TbvZ', 'v1:AWiXic2EUz9uZiNk3i2GMFts4mslWMmsSeOy1aZQMeVXfQ==', 'v1:nNPVnoQ0yEKZac0DECXysKt9Z1T6CUSuZbOsBl4v4R1r0t8eQ2lHv5A7v5L33A==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(55, 3, '2024-06-17', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:Vc6Jk1afNQfGh91q4LZEhMtFmh4OO7xoYO8RSbBjJOK4kiHVh5UFWLtnEfpjeKjbFaxiBtpY46v79oQfc18PprdwnmY+amuyJKAkiAN+adg/ErxqPgKn', 'v1:R53HJX05uGJ1vhilHVZBACYZcCDfRPqJN7RpxHZ6yJAfNA==', 'v1:0jJj31YBldaXvlmtzvxCGar90rqyvREIjTgvyRjcSNv4oYhCX6FKYUZraGzvig==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(56, 3, '2024-06-18', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:cHjQeN+r6/821MabZ9L4kdiqS8OAg4lSLv2qOhWiAu08QID0bhwnQr/jNSxPwvFRvThPPDypIiI=', 'v1:Z8dYVVNSAuHHGsJUnBbuogQW8VqyP0xPG+iE14bQWFCv', 'v1:6TJ1o1zVlNNm3iVFHw/qfpxuLCNyNTl+K8lpvNdpsNIRnnidHPDhRrmeGLl8+g==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(57, 3, '2024-06-19', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:+GaqkD9IoQGn5sBk0h+VRn6g2Ijf1pKzhDf6xXJnW/lCEwnzp6LSlUUo3LoU+ffzdQ4NEfQOWsQFFO1y64UzCDDGQBH3d+ck3Izs', 'v1:OuW6BcyCFHqQGo/msjQjtj4UVSgwIe0GUxoTJmk3mWDaHA==', 'v1:nUeqA/+tBkyoR9a+jUSdfxQwAIHYL1HgLHVCfO7l6TvMvRxasGif2aEHaWNQIQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(58, 3, '2024-06-21', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:k4l/fsOb2AwOjb+yOzeW37NJZIAbysMK30Fu5/rgb1S/ejZMAcOL/mXLu8bEHgfKDexar0Z14VdqYQBe', 'v1:gTqTnYlJ50SsBRk6invVtpT4kFyXXXRZPtiirPkE6X9Y', 'v1:1AGz2Tf8EqpqStg1gDhB8v53WfsH6KW0rE9VqfGhW2W2W+xPGCVTXGTqJ3lApg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(59, 3, '2024-06-24', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:k7Odq7b0dSqSNY4PbIUjrLOFqQQl0FTCrKGdtYm9TECeaEujJRrwjUynbU81SyErdtIudY/f1Qb3chxOvg==', 'v1:eiIz5rbKWQfepfTrVV7BoEgKQC8inE3RsSt0LkT5ZZk5MQ==', 'v1:kQBD/TZAMe+ekKhj6UNVGlhk6EiAgdvtaJlwW/zh5HQL1fMDxRLIzNkALA2gPg==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(60, 3, '2024-06-25', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:8BcGFG7MSjxPVXA4aXuSW5X6juhX6PSSCBbTNbSv3CGg9srUx8IoNlpMqDWWf/4kTs4=', 'v1:Icyq/EQrw177fVlUb/oj/KzuyTMIRAZWVZSD+jK0sqjT', 'v1:0lKeJ3HPVNq0Xa0Wb2LmJbf3EVGZexgRB8RT90jSN3U3KBed7Pa5Gg1FC5LWlA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(61, 3, '2024-06-27', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:vxtapPqIQOtbqngDA3UkefEyjHaJXR+8yRk4f1tpU1kmJrmdFjGlulHI1sALFsIHbeGGlUw=', 'v1:svVtPgpoW9paSurhqkRf4xVxbBmvHk599mCRgYVUVH0C', 'v1:AkTp8bRN9ECZ8+yq080XDU1v6OaLh9wpioWSshw0ShEvNfpV9hpHYd3qw6djnQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(62, 3, '2024-06-30', 'expense', 'bank', NULL, NULL, 4, NULL, 'ideell', 'v1:opkTswQ+L3byyHcYn6COykLBo3gWJGaitkmyurIyKEE5KpxWXVxxYB68SNvu5FF8tg==', 'v1:raokNJm/0GbHSOfdCRGOrfCz7zubZOHFQ6rteuSBcMQG', 'v1:4jeJPyHCz3Z/I0+3tE0mY12B+tFXpZVjWDS7gJSspZdW/Y6J46euTzSdFXG1fA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(63, 3, '2024-07-01', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:Q5mdjrZ0JGk0bQvv/HNSgUkiEuf4cGFsYe5oy3LwR8xRlZ0IOf4bNPErANC98CcgbmGd722JZwE5z5lcdeP7NsDErSjTGABc1gOE', 'v1:Rz0uQFOpjhTgIWc/OQUSU0Acu1rvRMIzsW6wrDqnlZOpUg==', 'v1:oTQOFY18Ghy11VSFW13IpFVDhmO2yYLqxp9PYpvEzclgfgiq6Xwk/rCtyUVxVA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(64, 3, '2024-07-03', 'expense', 'cash', NULL, NULL, 8, NULL, 'ideell', 'v1:stBtpVBu2/ywsu3+qnPixdX+vwmngHM5TYHBdcitNFRv4hYS', 'v1:Sj1XvpSP0gNedNWbNpvvajrUByHuwy7gSKKKTDRMHgCLBQ==', 'v1:uWd2wm/dnHaauT9S9RQWkgA56h/C03Og+uaKo/Jkt7iBsqEJvgKVkA==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(65, 3, '2024-07-05', 'expense', 'bank', NULL, NULL, 6, NULL, 'ideell', 'v1:zWzm/u+/ytqrVR5PMlkvmdY16yzzLZZ+T3vrpbPsnIdZe9yoBhxbSPqJv7ZGL1fl', 'v1:CjJGTRr6CRV6BQbK7WyGPrSH5GFxh7klv+Q1l5AcsnI0/A==', 'v1:WKoM+hsivz7VvAsSvnuZzjp0HsOSJLuBvbBiURTvxeJrIWTh2GdqoQ==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(66, 3, '2024-07-05', 'income', 'bank', NULL, NULL, 10, NULL, 'ideell', 'v1:IlvY0oS3f3Gf/dNig1uwm4lIP7R9FGRHtvZhsqj8yLg5ct0j6GPum8J/w2eLWESCI6i4PZE=', 'v1:zhIqZ1NlyqHF++eSU3UOFpWZUMVUlca/ZwzLXykD/oEV', 'v1:Pr4uP4DgcviGXlw+Vkd4xZkxOHcDoH8SuuZNHWyFFnNTv/9/KTIVF1MhlT7gBw==', NULL, '2026-11-09 20:59:23', '2026-11-10 23:04:46'),
(67, 3, '2024-07-08', 'income', 'bank', NULL, NULL, NULL, 'v1:EK58Q/+Re8rR+nad9KuJqTnrq+Ave0Vz+pfrqunUD6wzkWc=', 'ideell', 'v1:GZlCIqws3c1mtzK3Bm5yYZ075Qyrt7iLU2l6k13QUpUEgGu5z/FfPpUWcAaj', 'v1:UPj3KSpi1bx3qL6gL+N/nLc+KGIRIDSdCJrDagcr96dV', 'v1:E+VBKiov9IH13XVAGiwmCHBD2Pc32T8j1Twt9AzMkoNtOfhxkVKokUEy01cS5g==', NULL, '2026-11-09 20:59:23', '2026-11-10 15:05:48'),
(68, 4, '2024-08-21', 'income', 'bank', NULL, NULL, 14, NULL, 'ideell', 'v1:tCbQ3JJgRZvaqKFgEJONFQCjMUuRx0xTkUvbEUm/6JFKTNOd11Pntl2gYEX8zt3Lu/0=', 'v1:TNod3PS7gjT2oASABKBqQGSCRO8XWi4cjCi4tEWdLcGX', 'v1:bPwnyROpjYmHYNXeZQnUZVPvewtH9sQRDUHDpzN7IWMGCWD+zVnV6DUdjEcDtw==', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(69, 4, '2024-08-22', 'income', 'bank', NULL, NULL, 14, NULL, 'ideell', 'v1:7BX38eV7rE3qD0EScRKkRD8brTBXuHsvpxFciqoO5xjwI+UxVVDSX6GBLUWCQDR1zAHV', 'v1:MZKaarGsTk+8YSuFVSYoSI62Bja1BrTbhRb+EuwmvHeX', 'v1:8Z2syQ0LTeEaQ7lCuBxRuwefkZup+V20KAbN2wba//8DAh1LrxdKwWHEtHAiTQ==', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(70, 4, '2024-08-23', 'transfer', NULL, 'cash', 'bank', NULL, NULL, NULL, 'v1:ly8+Yt9x6WsTAdDkhgUMhoatWOHo6pqXmTAxjepN4iXc9/VrfTh23uJT2iAf/rI=', 'v1:u9UeiI/DaJSNk7XxKfqD+tsahkh07Ll+esNnEGCmpckRIg==', NULL, NULL, '2026-11-09 21:01:23', '2026-11-09 21:01:23'),
(71, 4, '2024-08-25', 'expense', 'bank', NULL, NULL, 15, NULL, 'ideell', 'v1:TEFYs25b6cHpq31MGQ7/Koy3Sny7prfMKoPYrzbC/osdxqhbdgQT6IybY9UDi+bHCUOK1N8ePb+mdbHR4wsiNRwB/GDX0bQe', 'v1:EV8cqmP3Y1YLZjRAtTubZyJDHHvgthRMuHAUPyev3rozjg==', 'v1:lC1SiU5DGZ6DFN3KNNB5OhHrm0FnR1W/5FrwIJ6sa6YRHVGLuHPp', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(72, 4, '2024-08-25', 'expense', 'bank', NULL, NULL, 15, NULL, 'ideell', 'v1:Qg2WB/uT60Y6YGK3kuA5RTSQmQ5pLK1Y+J1i59lA1qF+DE/3hudIG4+JFwOxfjb45Q23POCsOecX4e7AJQcweDUZ0/BfMPnxOhBr', 'v1:lqikjKLSe1cJRSOaLtXg3oC+MW23StmvL34aJQP0QWXKhhQ=', 'v1:U/+SNdi62Q8sRoseU/7Ajony1jI2DF9toQUSF8JUAW9HaYmbonr5', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(73, 4, '2024-08-26', 'expense', 'bank', NULL, NULL, 15, NULL, 'ideell', 'v1:Eze1gDS1tTFkhQ7uQz/qEl94aWE/OI4Eh7l6ArKzETmC3oQNfnsJ9DMvYMkk2ksX77w=', 'v1:utSLpNJ3vcoKTmOgkNfbG4EsFeWjn8vRjZeCpuuohXYdxg==', 'v1:baWlXjTZg6a+DrKUYOJTXRih2KN1LTDGQj40B7qN1uLLOcJVRBIG', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(74, 4, '2024-08-26', 'expense', 'bank', NULL, NULL, 15, NULL, 'ideell', 'v1:rYvwzw6av8nB8J2BO9bjF4RDw4My/z2ffgQUpt2ugmdxO77hhHaaE3A/rX45Jm4JiyhoBUAS8yg+LTFySiDgYys=', 'v1:yvCjqdDOMCZ2TtMHQnMH09WCu1iwi9br3Dzo6VJGK/IT', 'v1:87e2EtdumUhSYO/rInFcHtHGYJFj2AgthXHqoQhSwLKEvg2zNcR3', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(75, 4, '2024-09-04', 'expense', 'cash', NULL, NULL, 16, NULL, 'commercial_business', 'v1:RTP6hhe8IDmR7smTGNmDaApEOZuqvKelZJnzCjt95DmmHQkjni0Xpy3gjflD7hU=', 'v1:LVNLw0j5zBRjvsaUAWCyGQg62/7sS3d4e3/cGAtEvCfA', 'v1:/jjkzCqJRwuiHKObpyvT7dsMCY3XIP48i91a80tOMbBIfvL3CuB6', NULL, '2026-11-09 21:01:23', '2026-11-10 23:04:46'),
(76, 4, '2024-09-04', 'expense', 'cash', NULL, NULL, NULL, 'v1:nc6b9BUKBO9l/0d0EKPplnBw7tkq9PSVAYGUM9YbMCjsoZFDoOnonPJl6TMwFjX16AImgcTjVbfiWsyczPlDQjg=', 'commercial_business', 'v1:wXqdBKXdL5aeOlLU51sVb65jFXbzwfd/8r1kS25XrPeRMTuLT+BwucGMU0bi4bvj6No2djwLBHY=', 'v1:dLmvOyb7KG8c3gi3bnfzji+XI6wuSr8dWgyCiMF3i8jw', 'v1:x1swZIDndgWCyrnwKnZOBin+10NnlMYhWvkLj2QXwFQ8pPjRLn92acLI1lSHJlo=', NULL, '2026-11-09 21:01:23', '2026-11-12 10:14:31'),
(77, 4, '2024-09-07', 'income', 'cash', NULL, NULL, NULL, 'v1:6uO2RK0Mw1N6uXaHthp+OGEMVyerndKQnlYy3JZgjEP8T+Tx5oC30BW/3oYqQhhmayO2mhG+kcOYMcCKqzwU0vE=', 'commercial_business', 'v1:ItfNiOFh/7+YLA+ShTqMi0KQj2yLynKd38TrrG//1bueBXsc/84eabsxCPmqr6WIc1iW1RNr30LoPx75AdOkQUvZLWhFt/Np1CL5hwrdh4YQcRxm0w6ct3BU3w==', 'v1:DEDGyJYPxiVLGjnzLKMxWv+w0nMQDLrEPFr7VI9sB8Sn1w==', NULL, NULL, '2026-11-09 21:01:23', '2026-11-12 10:14:43'),
(78, 4, '2024-09-15', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:Wc6bDn/4o1ZUE2DWBsfj6JPYyp2n++RhDX9QettHi5KEpc7uVfp9Z41NijPe8w64yzG6mI8jbzL2Nmw9oOdYn7+sSjYUMMNUduoVaIKfDIMUPFyr', 'v1:2sUn0ow1oaVoDREqLQB1RZfxzeDpO1R0ZlyPi9k22Xf8EQ==', 'v1:wqYztWsoaGmzzSeJU6it0IkjO0vsfNCMHCCkf806EbrubrSkJA0l', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(79, 4, '2024-09-30', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:I07ulo35GBFcoD+ZOR2JciGRN179cxeYbbZxFDkMtzAWA0BFfOnQqD1lPuDzHcY6wQ==', 'v1:QUXf8ccnhePSrLOcJm9Iq7Q/Hex1rkbxvwKO6dIbG1Q=', 'v1:FMksSzXyNK/NvaU3DAl1xa0ufI4r8WBv94u4C6VB/CESXJumknrM16Da0Sm7UQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(80, 4, '2024-10-12', 'income', 'cash', NULL, NULL, 19, NULL, 'commercial_business', 'v1:fxdUYWOJHsAlAyvEWWZPrkGy53qTdtYdnC9fNAHprNb7B6Pf+N/nUMQ=', 'v1:ZhfI0nOquqUpo5DULQFQQ3s5NSXzUEqNfswggOWAZKvE', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(81, 4, '2024-10-20', 'income', 'bank', NULL, NULL, 20, NULL, 'commercial_business', 'v1:mA2wpZfQ3FXHrbOsJC69jaeE8TtKR1hF4BAAlNqjgbCwILhs7a43pAEq7wUwUloOlPCpYAazTZS3', 'v1:GCgBlLv8XzG54e/A7c+qagiPhnZOpkTVoqy+dbNa9/UyDw==', 'v1:xiRLcdfd/QenUP5OZiSGxHy1ErZxfhX+dBJV/p36pZha7Cw+Kl1Tamd9LZqVrA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(82, 4, '2024-10-21', 'expense', 'bank', NULL, NULL, 15, NULL, 'ideell', 'v1:GcNfKhV9XGBXn6D1bl9YuYJzj1JQOHfZ3EOsnwf5N8wyiUnVTbn1FgdtshXfa2HlFtymtkRzuSkhM0z7vwx03HSOCQ+I', 'v1:ig0DnW8R6rKCv+C94h59FPvRDx/HRlIspbw0XuNGG3EHhXc=', 'v1:n3xCziOQ4utQrLbe5JA0tePvQvxq1+ZpD6kA2CkDUSNVHo8F6aeH', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(83, 4, '2024-10-31', 'income', 'bank', NULL, NULL, 21, NULL, 'ideell', 'v1:CYa6gDhd0qTdIvKDRKgIiddPQAKhnIV7mxcmPCXbnn5xHLKmey49FyvwUHW9Oz41llZjErbA4eBZKgc=', 'v1:U7ymvLO4Ik3Oh92dHyzWfRDAPtUH89esQ5b3e2384EIyA0I=', 'v1:oyTDxlkm9ekxsqI89BH+t/uE8O5n8UtNsLfZqKO3KuBCAW9M4NflEmzuL5HoN72C1A==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(84, 4, '2024-11-04', 'expense', 'bank', NULL, NULL, 22, NULL, 'ideell', 'v1:GC8uR6+xWmcJ+7kw8w/iksCs3ez2Ap5Iqkj3em0Twf/DGP1UWnDsu+7IJ3qqqdIDQOmaqaApNIiDe2eOP3skN8RgAg==', 'v1:nnBJjtT92aFusOWKcnUKt2J1LxKo9HxkuU6GDQUbCwGq', 'v1:Ojhb1jhKRQAQx3kLcLInlPc8NYcePqjcd62SNjpt/wyrgcg6nb0/JPv6DSjaDAI=', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(85, 4, '2024-11-05', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:LmWVNa7nOalBJxr9+AZMQK5T+NNcjJp2hD6LBlDCfoLhTv8YOlTDIVGfAA==', 'v1:8BOuQkkSWw6KlJYZzt6Dq3YWEYewOhHOb7sgbdweniA=', 'v1:sRYFnMMRvQA4xKJeUCk89EN5mNxd/NCnOokugP/hmovEjIxDWEEmkQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(86, 4, '2024-11-05', 'expense', 'bank', NULL, NULL, 23, NULL, 'commercial_business', 'v1:hN9nZR+DN6PAVVugg6P0vP89ODcl4OV/uoU2EP1k4J8K2/R0lkgMVhwBEt2HrtraQXfpwCW0YZc=', 'v1:pdE1uaoslH5q3GjLiZogolBhLU1Yif51SBaiF2yAJTkM', 'v1:vKiOH3LqcMXjwKYXlkcFNsyjo/0Y/MlZ+dV7kaB1djGEhCBy9P3R+g==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(87, 4, '2024-11-07', 'expense', 'bank', NULL, NULL, 23, NULL, 'purpose_operation', 'v1:0t4k+S/7OnF7nuLM9/kNyJ/SIuxEkFnKv94mkdyxRlG4VxJNatBwKP6R5ZUmfs+PN647LIYh2D6Izz0sVfXZwVYDb0FQpcFpD7/QWZq/OD66', 'v1:K2+xiM7PchiHDZeknIehmCq3x1YsJBx5Lp53vS8zCElR9w==', 'v1:HF+25uGVXkAnRVgMalFCpDYIg38rttEDlAR0xmotHFNWRyDg4TcCMw==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(88, 4, '2024-11-12', 'income', 'cash', NULL, NULL, 24, NULL, 'ideell', 'v1:OqdmgnulyYCHO/3WkCrGddpbgGnqGujF75bS/lBBUXFJ47r+UEhkfuXw8r+SMa4j', 'v1:QLq/wFzSVIkvMZjtJbkt+12cwuB6RYOH3dIoN0N9QsRd', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(89, 4, '2024-11-12', 'income', 'cash', NULL, NULL, NULL, 'v1:o0rqefqEEBeeofcC75oyfKPnn1qRti65aYikvltXDNVe36qV2A4=', 'commercial_business', 'v1:psPx6QYxaEBp5VhnF2NglAfmoqMMc8w1hmE5IUoe9N1uBnMlZyaUk5eshSo=', 'v1:WQZAk/BeozMFdnRcp4d0KYcQVlIwaXwfKCY3ogveTd8IAg==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-12 10:16:41'),
(90, 4, '2024-11-13', 'expense', 'bank', NULL, NULL, 20, NULL, NULL, 'v1:cv/x/pooU87X7w7PRZvQXBHuFvfYNbxxTsJLIVn1n/cAWIdQ9vq1', 'v1:1d2JnL9CW+7ZCYuiRDdATcia0r5AbnF3V4wS5PwJ4aA9bA==', 'v1:6I3T0w9R8xgnSZ6C1XBUm/4IauypbjkRclrLp3kS/b4whWK+Rgj0Sw==', NULL, '2026-11-09 21:01:24', '2026-11-09 21:01:24'),
(91, 4, '2024-11-18', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:u9C66Lt7bTWHZ8FjB0QOhImVEhBQDSCwMcgRSteXXqrd/SS2xO4db8jBX+6SIhOL1hzQYA0Dc6zSE35ArPxWu5LIxgsqFOUkN1c=', 'v1:jUo72nGjLK1qziVHo6aCeE0HyJs0DDJQ0SiRjHOqiqEPIA==', 'v1:/49nkEbm0YCXrKC3JKwz4yWbwGZ8YIqexdXsHP+B1weRShskCzpbBQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(92, 4, '2024-11-19', 'expense', 'cash', NULL, NULL, 17, NULL, 'ideell', 'v1:s7c2yBMHkzCir49wPu6fdpfpAFMtQtA6wMXXVvbXuQYpO8QmUTAwvNpmhCBj2ovYdsicXA1hW2IDq3T4KDEHPhXMIJ573Dituulou7rjgqmnNlR55/rnV0+U4r4=', 'v1:nf9qxdUe/AAZ6AesC+l4iYyjMT8VsJ08gSmxhQx88oDI', 'v1:p0n4apvCGIFgqc3obt6qrnClxjDSdPbyJJ3pYYCqHvUEdi+P938E4g==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(93, 4, '2024-11-19', 'expense', 'cash', NULL, NULL, 23, NULL, 'commercial_business', 'v1:7rUgaaNRvu28NnoniOBjAhBVUhng78pK/TqL/Oedx81Ds+udTa99ITpBfXL9hLem', 'v1:hCMkvvZ3Wmd/gOAj1o5bowcow6WrfnnjvahRfutFCL9U', 'v1:Vw/Xp6WLGqCHxRgU8phdsM/YZblhN7A6S/mwgt6E96atr7CIWvoJFA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(94, 4, '2024-11-19', 'expense', 'cash', NULL, NULL, 23, NULL, 'commercial_business', 'v1:plNMsLf3tGPPfcNmfwsuRFN8KFvKoRO9/cOaHU2WgRtwhU5C9/GtXQljD9OOqiv7M5Fb', 'v1:2uyvOjAUz7Rz5y7XKD5Z1myrrRsdoQuMSpcFzbL6BX4l', 'v1:XuBzGd59nNc5KedE3Wq7rNl0h53Lv+vg/N6HxjigdFP+7jF9YoPG0w==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(95, 4, '2024-11-19', 'expense', 'cash', NULL, NULL, 25, NULL, 'purpose_operation', 'v1:iWQ+bd1CtiEH4K6V/yVUGDOB5iPAK2a1m0WgkjY6lcM=', 'v1:G4eqyV4eLMehhXJVFPyGfFTs93WImv64YK3I394xAn7w', 'v1:zzZzoVGgUtPkr7MzFJ7qhAtgQVTpImTFWiqkfXda4idiV3yFYhcY1w==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(96, 4, '2024-11-19', 'income', 'cash', NULL, NULL, 24, NULL, 'ideell', 'v1:BtqEVyiRFVlqxIbGHv+133Up820HtDrdq+89OzrtfPNw2asl1kFNAGaohpUyLBtWVehzcPfLilHSYHx2YAg0BHgO5p9jz9KmRwyd', 'v1:8orsdj4z+1WwrpYrNRaj7cLySiRBOdOZ3ZJApNX9fKe2', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(97, 4, '2024-11-23', 'income', 'cash', NULL, NULL, 23, NULL, 'commercial_business', 'v1:nha9a52oRa/6KonYDWNS4mbEp6vCoLotq0QteZZqz6A/9lqjFmCUdbPGRS4jgxQ=', 'v1:GQFLMAKB9Vo2Autrwt1DSJ065wfkw4Goo8Yo38/ZJLg=', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(98, 4, '2024-11-28', 'expense', 'bank', NULL, NULL, 25, NULL, 'ideell', 'v1:R7NvXij5A75NF12PTDZfBEAbOC4nzoPX71LqutjhQVgPtYehkhznPWPGTPAOS/WBSEi394BM1A==', 'v1:5nK3rm/FyB19Df+RX6C0osBWTL1+NXZfaOSZW8KXJ2q14g==', 'v1:nfnuRpScxF7YgRA5u4CWbT+zHwL7RKjrE2tiwJJjJQBKCxtKUB/CdQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(99, 4, '2024-11-28', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:TdNu7PR58upJ+5TxYnw8Q0DFRblivo95WkPPoJna+opZy2QBiObG+Q==', 'v1:hBr0lgtQ533nMfuyWCJQBUKytO8twc/AdBozoNCSRTrCgg==', 'v1:UghBW34W2J7Awf3cpJzwBHQs+9C4mD5eaGlTxsci9OyxZwN/4JrcHg==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(100, 4, '2024-11-28', 'expense', 'bank', NULL, NULL, 25, NULL, 'purpose_operation', 'v1:HVX2XXMAq3ZNPg9SoG3AH37kogsox6NIb6s4LFaXhjE=', 'v1:aRUTf4pLT9/cmUK5dEuXtJPAE6O11gSGrp1PRqqWEmYSAQ==', 'v1:dKl+L60TDg214/tVMC6fVN3O34LYsSXPQN1wf/Ndx4+zakZ43kG9pQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(101, 4, '2024-11-29', 'income', 'cash', NULL, NULL, NULL, 'v1:AEvj2ethsq5/d0qbOZcaYCOagShEAb8MGM05r9RnQWu8q7RpZRMCqkYVjA==', 'commercial_business', 'v1:EsKPC2CYtIRh4ys00ikQXVVhS737/mGCIcj5fOSG6lIwHkwXuiS69wna0Jx5ZFjW/7YE136i', 'v1:AOvRYvNzf/9ilFvI/PuchZUEzpO4ZugYELRuoJdo7dQImg==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-12 10:18:55'),
(102, 4, '2024-11-30', 'income', 'cash', NULL, NULL, NULL, 'v1:e8OELmh7WjrHTufz4AA9eSaQvofi5qFLP355DlU1xT/GIsQQRwCoUwlgZw==', 'commercial_business', 'v1:+xLCIZBk/xyCkXMgo+2yM5IkMZODJzX4VMsgdcZIuTt9TkmOD38+Zz9/zDS1+0vqVfhr15Zg', 'v1:DVW3JF6I/zabcAYdvJNqexNRg9DjhyvClx5TKZm0BuDj6/k=', NULL, NULL, '2026-11-09 21:01:24', '2026-11-12 10:19:09'),
(103, 4, '2024-12-01', 'income', 'cash', NULL, NULL, 23, NULL, NULL, 'v1:UQ045Ii7oOaFlKgw4Gl3RGv/AezGME4A+YHXdiM6agp0tsx53XPhmVuqvGpyAzpgOWQ8xfkZ', 'v1:2nP/6cSl7F6F2g2W0+4NM7Lnhh4bhR70XZ7vpGLJQ5HOkA==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-09 21:01:24'),
(104, 4, '2024-12-09', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:HddbCFkz3FWNxW6W6V42p5OPhS+2Y1gdFrQ1KtF/YAZsYahouOujGLghwv/oBx9rC63TXS0fs1pS7DUpq5o=', 'v1:G0PyuBN3WEuW63RY/Gx70DFKA4wDJLSdPqmRu7NbtwTi', 'v1:DVr/MRd5ioBXbeFaan1kQ1cbga0O4tBcmI2FOsXEreU3DcPIDZtpkg==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(105, 4, '2024-12-30', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:PJgDYzVHCBL+t3mVZhVL4Yglx8EcqX1IlnFGTTok72USRYl2UjF/5Sst4m3Wd1n6Dw==', 'v1:7XhsGbM2idbRKfZFIhdIcb2uANvPGuIzr6uFkas9y/0T', 'v1:+Mcpf1FNaMbEMn58WtGc4IX+80FnXdDGffj/CNBNd0ZYSbOtWrlV94/1ykkeU+U=', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(106, 4, '2025-01-05', 'income', 'cash', NULL, NULL, 26, NULL, 'commercial_business', 'v1:TuVTEmowmPZn4SMZsYdBRrdEklVeKjO2wleXLaT360lHCdVA8OK1MjjvOLMdVrKf9RQmy0gEkDKQTy9PNfujY60+nD6TldMmTzQ=', 'v1:iQ5soP04Kzw/Si6VpVDqfQdiB49UQM/6SyzvnjQ2q98A7A==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(107, 4, '2025-01-07', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:GaITlCzE6C0SfAsxZmtv+WivgKMpm3MAwyEInsmZo3YhXrquOoM7AURoALvE3/mFjH0oyYBX5/pXNxA7H4I4TfoYLImPhplZSsA=', 'v1:VmPfz9dzqnBef73yvxc0tMqA1rhxDn4SZW0s/7CyaLoh', 'v1:KJJDvvQrp2r7nT57wcIZtIZfvDTmQL8bWGxZUpYnRuzyXUUvCo9gHtxcZaTugA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(108, 4, '2025-01-27', 'expense', 'bank', NULL, NULL, 25, NULL, 'ideell', 'v1:QhjoWCfwW02Y3oirk/SiMI8jBPXA03/0gXxPnxbJFSsk5i6fPPztqWmb4xUPLX9ohnzJFD6SwQ==', 'v1:/pWONvaveDnzXEKDg2csauzuvnM4WKDQ032luO2F5K25', 'v1:fUAAGEy4krnzHXFGldSlccS4SIqY/dsdrWFQULCe9XEg2zrLtSgBLA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(109, 4, '2025-01-27', 'expense', 'bank', NULL, NULL, 15, NULL, 'ideell', 'v1:DzcaKBMN9k9QFAIfNAvw14DDZ/O5k5MAvESwIme+PNJUFpw3GE8Zt4T+gAitLYU232NSImJ9', 'v1:Iet3yxS1I+gx5BbN9A5wRksxPLHsj62dwkJCaw/E33DBYg==', 'v1:aie2l58M1rEjkc3SvxG177Go4ejRjhWFwmfUOmuQQF6fKtC8eNGK6w==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(110, 4, '2025-02-11', 'expense', 'cash', NULL, NULL, 27, NULL, 'commercial_business', 'v1:RmrWPfJYg1i2ifmMy7K+CnDWxONGMbp8mi8F073IjUaOOONqOncKnMv1bFQ4CkaPK65lkKQKOIck+4W4gKvl7dedWdM=', 'v1:7NOpBXjT4Jl0X3Zbsfxw/KTIlU3ohyezh/VoAq2gI/tY', 'v1:F40uG0PGSerRhHA6kAz376O93LDLk4P2CyeIDnGaOfviGIspk+mvbQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(111, 4, '2025-02-13', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:8YZyrKk2tIoxfqByOvqojiUwkg2ZNJNVOzgSC9zhkn7ABLR27wDGXIDAKBmmW7PIcKk=', 'v1:VQSe7U+beNYGPAQLTMAZuTgBwqWESBCyYWX8pM88xFKBSpM=', 'v1:8nXyr6KalA50C5K/vNY4QBf2coiQosqy4TdoiOXQIuBasTHrKgIWcA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(112, 4, '2025-02-13', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:IniUG2s/1TJF3o3ycuMYSnGrmuEfefyH24b9WMUacb6r1wqoSk4=', 'v1:6MjchHuDuhRUMdb9TwI6gG9bN/tHMTEPHnbAIfyDPnwC9g==', 'v1:KwoZ4VM9k+mUv6FKCOW2sxLKUkcndjlfAXh0KJLcOuRBknxc3Lvzhg==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(113, 4, '2025-03-31', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:K1bG+n8E6uTrEsCX1CNUS5/dLH2zhkW/a2AI2yMr1bvWlXE9V5yLv4Y0P9w9WZ0B/g==', 'v1:LXZ+H/BI1SHBYOsTZw4mKPwx2Oz2i6vTP6N+vURbo3o=', 'v1:6PRxOW6P4iriyOj01tRyC/u2K0vuXA/2sbhkv58sAKrcem0y+dzzUQTBaJ9lUA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(114, 4, '2025-04-07', 'expense', 'bank', NULL, NULL, 17, NULL, 'ideell', 'v1:QQ39TW+pRqKWjACQG7egbnMP4GLIFH/flqzDB5QQOk/AhEXMedUxuxVlGguQOguv5nCaRzSuaXuvruysIx501NgiMTjG+Tnw7IP4zjs2gLloU5dtvbY+ttEvmf0idvM+jd3j4Kgk1UYgS/kL0KQridnvSPZB0i4WoYtyqdHNS8YMVVwXR4ahnvIrJ548ysol', 'v1:EQjwW8Idw9H7ID24ZR3F0h3glfPTqX+S1ko9jgfKSIOIxw==', 'v1:ZviMd9LlvpwJTsQL91grEHFZl2K7heUGuQOcUtdNhITYrv4QlGzoMw==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(115, 4, '2025-04-15', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:rEw3BMpIvR82xpRjof6bsWe/hfpVFfg4XJkcTjkwYwn2zEuhexM=', 'v1:bgZ7ZaEm40zVHmTMHhbCs/nOr0gyOeky2eDzckKwYZsn9A==', 'v1:E8fuGQ+sXxE9S2yrlkHOVOUsYI54YBzYaZR0OSd1+GFY6LBxuyMalZgLIR59fA==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(116, 4, '2025-04-28', 'income', 'cash', NULL, NULL, 27, NULL, 'commercial_business', 'v1:kbWV5QXhqmyfT4rARDgEePMsatPuTP1ZQOGQ7o/xPtq+ZVr/MRaDmDhFM/f7rvoLGhwA0Spc', 'v1:1p6/jqL3MIFqsyqmA7ibUj8Fgz2IFhyP9XEMlfAJVL7Z', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(117, 4, '2025-05-05', 'expense', 'cash', NULL, NULL, 25, NULL, 'purpose_operation', 'v1:ALPfYtTsuDFvRvRy/xKgpYQC2cybORvowbl+qY2A1FM=', 'v1:UWsEJwrDf5xa6etU5qhXFGJZdXzOIoMXORU1aPd0QlpZ', 'v1:DaxoDAIHrKlLk2qWFSRb6OW/tJqQmAduPNe02dYKqyJchPXB6MYVVQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(118, 4, '2025-05-07', 'income', 'bank', NULL, NULL, 27, NULL, 'commercial_business', 'v1:65jGoliwWo5LkZYRPkc2VfOIZgn1ofHYDLr7qIh6OE36SH0rtnn6/8Y7ZjnfACSEA28TsRxf', 'v1:evvWn6+41If/UmqMlC5xhg0w8KYoFkEHsNB0S2gwdbyY', 'v1:Vet9MF/6sHJ1n77K0yZ73Xgh3Rz26CZl8aMIMtLdhGdfLnQjzPpXBppw6mk09g==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(119, 4, '2025-05-08', 'expense', 'cash', NULL, NULL, 28, NULL, 'ideell', 'v1:nS17e6ebo5VgBiZ4knrEdRi7uOMAhsOxVHYS5yYkF2KAz2kVnGTrPUUsQEby', 'v1:Q9L5soaqoA/FKhUMoV+P9WxJwmUg92PoOxFju3llzTbk+A==', 'v1:10JZpNkRYYop/XT66PglUaKFF0TRx2C2O5VjYkxWwaCBbWwUV/AK8Q==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(120, 4, '2025-05-09', 'expense', 'cash', NULL, NULL, 28, NULL, 'ideell', 'v1:mugtEbPBqsIh24m+QE6GGbXVRXBq9sWV+G8PBPcLpUL7W0T40qODiApiVZj4', 'v1:zfh8UZ+B0ht8ADaF7eH46dLJk0kCpFu4xu75+XRKs5QBDQ==', 'v1:fk2I/OY8u304wE9/J9nRU4gvp5zDewDFTMJLZ1Ff/JX4723iUUOdpQ==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(121, 4, '2025-05-10', 'income', 'cash', NULL, NULL, 29, NULL, 'purpose_operation', 'v1:nTuq+fPZRl95vPW2QX69x5zGYIQbFbsjdIyDsSHJ7C6cKSHJgHGmvxy7VTt+', 'v1:KbrC04reMw09wq+42zHkOXvL9zVScMO4u0TP4JhwqfgM0Q==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(122, 4, '2025-05-10', 'income', 'cash', NULL, NULL, 29, NULL, 'commercial_business', 'v1:FiZHdX5bWaIffnFMcGooApjX9fyOWwy6ipfcJ/W8iQYH5/xQhyoAFYEvyGa3RWB/jYlbfLdURGU=', 'v1:p0dSGP9padaccYgpcWTzqjMdmg573S6Bxq/VuGf/H4KMtw==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(123, 4, '2025-05-10', 'income', 'cash', NULL, NULL, 24, NULL, 'ideell', 'v1:3ZZhzNfg4lMrZ/Vt1FwndoeLKebY6qdFLGahzD7es9Jb4fCREpOVdYwAP3/t6slH3gsFcNM2fQ==', 'v1:GYJYQ1xMdhPYeCZ3COkZvq7ArWYntlYsdFe/9inc7hU6RQ==', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(124, 4, '2025-05-10', 'expense', 'cash', NULL, NULL, 29, NULL, 'commercial_business', 'v1:/5L2omdtZKo5G4nWRLcU1xvA5qVk3AsMZBVaDN4o/nJoFiByuBVErc8OY3BLz4GcWJx+XYBEmUsymIPVYdrO', 'v1:goEjyS+c/uJ4T4rXuOmlhnHaPtbGwVTQK9zb6Uhptu8y', 'v1:hNJQInG4LyMI+Osgtm0HuVLkmtkAknZTGvKVl+PAY8n+4C3hJXLywg==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(125, 4, '2025-06-03', 'expense', 'cash', NULL, NULL, 28, NULL, 'ideell', 'v1:YXhvXKAgl508k0cmU82s504Nkzwha8zl1MixviwqSLuXuAqQtlvxgB4AR9DqmgSE/ijxQRa85TGUbQY=', 'v1:bD08q8k3hQAEvb2g/Rxwnqurqjgrm6UkzstR1djPaP6c', 'v1:wqfJ4qqofwO9Ie90dfyIQdO9ca7ZQQqrfox8Ogei1qCJ8OVd/zihdvNE', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(126, 4, '2025-06-30', 'expense', 'bank', NULL, NULL, 18, NULL, 'ideell', 'v1:mrYu3RirI/RO3KJipJN/NyLg4rCr548zFRqDNIcTUjYJgZdqDMqCnLtAzbrwPSMZAw==', 'v1:U0nSJJGe6YkkW+ygEWpJNktLPfuv7A30MfI96vq0YSY=', 'v1:YdGTzeyiKZBPZgR4Sy+M3AgF+7D0cIe4s5aU4UAM0nCCQs/kgEgBVx89vOOa7g==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(127, 4, '2025-07-08', 'expense', 'cash', NULL, NULL, 30, NULL, 'ideell', 'v1:92Q8M0l7l6hvJf3kjpj1Q1wVOg++1UKnkYTTJ7bwgTima/xOIi5CIa7PlEU=', 'v1:aJXnOb5lL3qmzH+cX7xDSeJzUOBAH3g4my1TJ8ipMmjO2Q==', 'v1:Vhe1sdFig962kQxiBnpsDaFZdCufD4AfEaNxAYVT5MKX34Vg7HFaRg==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(128, 4, '2025-07-12', 'income', 'cash', NULL, NULL, 24, NULL, 'ideell', 'v1:6PMEbSMgF06GYjadJHV8dXpSPMGcJ0g56qyIXwihWPCSGSuvdkHJZdgofcTsae2vG5g=', 'v1:ueQ5isGoDCGmVbVAxciyNNsctTigaj0POwSQHVOCT0K2', NULL, NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(129, 4, '2025-07-24', 'expense', 'bank', NULL, NULL, 25, NULL, 'purpose_operation', 'v1:gcyVbPwgdWNvfKU045Djg0UhixW9efAh1qYKW8mAA9o=', 'v1:sEXa4rd/BDzH11XV5ajPTHxpI6U047mNP/xFOzeCGTsOGg==', 'v1:Y9sBR2K+U49ef21bLI5sJZjpshCgzIfrYHVjGm1s0ruobiDqZKC22g==', NULL, '2026-11-09 21:01:24', '2026-11-10 23:04:46'),
(130, 1, '2025-08-30', 'income', 'cash', NULL, NULL, 31, NULL, NULL, 'v1:h46/Jq2oxeuzIJLXG0P5Rt0t6GwBllBNLm05l2hoGKmP/zqb+/DZnorTJZR0Oub7HQ==', 'v1:stgwUJCxI2Ks87z0tvrsG9s5I3mLsnAjOatBSqdkZoVKcXE=', NULL, NULL, '2026-11-09 21:09:35', '2026-11-09 21:09:35'),
(131, 1, '2025-09-03', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:jBlcOHT6Zm3B4BLCODJsIqIujLUJ/0lNDsfQTuYeP8/1BwciZQNud1WXhYyJPur0XRqO12PLEd/Ai31a2lw9', 'v1:BcYyvFnN7rjM/ol3gYMZ50YfJFbtVVpNKhOvSZnZLpeT', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(132, 1, '2025-09-23', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:04Q8ovpEQhK+WuclIDoADzrHlgmHAPYkn7NUNoQeR6O7oT6RvQls0q/Srtp50dS+aFhGgDKlsE2l', 'v1:vxs1/tCEXSgoylooLliBmPrfxIGtUoGo0dHbpFin8yTF', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(133, 1, '2025-09-27', 'income', 'cash', NULL, NULL, 33, NULL, 'purpose_operation', 'v1:xI0xzxlU+7uTFLOTaLSPmVUMI+a1Ln1MoXt1CJbe0RuOA8g=', 'v1:g1EBVbQ8StI0yFZ7/2rhGTXbjb1yjsDTYnIs4D4hyM68UQ==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(134, 1, '2025-09-27', 'income', 'cash', NULL, NULL, 33, NULL, 'commercial_business', 'v1:fcQHqPYmBGoLTN8qo7Q77wWg0BSBUYSQwiyAXnxMFQ8Uy13VJraG', 'v1:zDxudgUyTBIY0v/GELcEQgEn8TBAqHYXFqP+9kqCQAqtww==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(135, 1, '2025-09-27', 'income', 'cash', NULL, NULL, 33, NULL, 'commercial_business', 'v1:gaWw061eJaNmQfpB+ioL6azDTWPVDCcwgN7h0I/pQ/+jL7Dd55z2CbFa', 'v1:AQI5JGHVGYb5yTsRttTJO6gx2i0tD/Ny36TFLkFui3cWmw==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(136, 1, '2025-09-27', 'income', 'cash', NULL, NULL, 33, NULL, 'ideell', 'v1:W/0eq/g/Z6TjmFOQgWvAQdorNgBGrytTp85WEoB9AtYiKLoN5awz0T5d1jU1bVIIuSbdBznmjMDEVan/TjsGybseczTdm/zfWkWgy74zLDRVLQ==', 'v1:7Jnom299GhEN3b6RShMwGjBzd5+kmIBizPTVdw02I0TQNg==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(137, 1, '2025-09-29', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:vgKCu2UDz/63ydM/y0TShzIY0LNJCKbtiweH7jkcmzh+fNiYALGMh4SiVUHIg8auCHKgHhA=', 'v1:nI6szcid+Yt1ryQoKzjbMzgHGC6sefGjuaWEckHASqih', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(138, 1, '2025-09-29', 'income', 'bank', NULL, NULL, 34, NULL, 'commercial_business', 'v1:yp42rw9AXw4BajiYKbma/0woyJfZIa0obd76b95kDJZbSfx8YJ/xtSWZH/1V8ANs0F9Eg2nt1Q==', 'v1:udH81QBNmtD4Qyy9UZxnA1iU38yGZ3CmROFrOpUnS4MO/A==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(139, 1, '2025-09-29', 'income', 'bank', NULL, NULL, 35, NULL, 'commercial_business', 'v1:F3U4z9QyauFnaV5XabZR+5fdblVrDMKF2p0kA3c2SQ2hf2PHPpiFaZSP0HJyWke3kNP0M0Gw4ufK', 'v1:1D0i1R3MBwPq2tfy9hvbQ0YCcKS8hYabk6he0dwM59Ma', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(140, 1, '2025-09-30', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:L+kRDp2TxZSdycWRZ1rbkMqttKFPttPFT8u2Gx0FAmQCpQ6xYaPuyFUKYEnN/D6fA1RknFLSGIA=', 'v1:LBu7aRUZ3wwbmFZ7SiXo2pP+cFt7ZvP8bjSfj4FS909M5Q==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(141, 1, '2025-09-30', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:0O9gwETS+OG7sZp5GrEdolwu7GsndLR6TzgdVv9SV5VSFDylyecKyF8BQqH5+qohjz2ExmCj8iJS7g7EHJ4ei8If7Vz60/rI', 'v1:9XvUf/y/7dHTwu+D8v9RHvK/x2xATg0PJSLY/jptvdni', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(142, 1, '2025-09-30', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:ykFASE0+mhaEly5v277xjaWwNVbL3keD5ng3u1PMpB+aqxwZmkCP+dFwV8SFGt6ExI8CmsccwX0=', 'v1:4bId0O2QM1lGjWBSgxN17102BrIUAvXfuUCikQtps/It6w==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(143, 1, '2025-09-30', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:yPY+r9cw17HLoEfXPdEm9D0tfF1no6BSU1YiHjblQAq8dn5/xyjGB3HLbcu1aGRI7mPe6E6Q', 'v1:GkGpWjXzNJiyaBuQyvTVE9JrU5ulJ3ELLuCauUPmfS30', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(144, 1, '2025-09-30', 'expense', 'bank', NULL, NULL, 36, NULL, 'ideell', 'v1:B4QJwRJJZ1f26CdPhvvC0w3E5lH8RLLqwqOJ1Zo0V4JKV4ZAWBJHNnbC', 'v1:kFtTn3lBlstuw+mbO4UfHkpc2H+nwteXXHyFfuqyaZ0=', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(145, 1, '2025-10-01', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:UqDbJ+Y90pAYooQN+A+7LaVYAUDyzE9XLE5Xg89ZL2awDOU/Nq8hDuODauYPG5dyfPQdrpGunBMzL96JDZNVni9L', 'v1:CyuMIoRg9szP4u6dqwBNMk+n/aFQV7hS/q4/CO0WsHDd', 'v1:vu+BNpUYC598jggnWwZQGENF+AmtOceztWp2T9cjIXRB91YoZFm/xiKxjtw7jA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(146, 1, '2025-10-01', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:lJ0901FmPQ9SzGPHuS8QvygiBvPl0UfQMQk/Z7pYqGvkAtVjNAdPN16c1K6KbfYaLtQy4I9Kfxj5AbkCVhvbyEQnA6X7TXA42+iG', 'v1:+jqei1lT7SyH+THx3hYxe05Rh5DziLk3T6HdwG6RUBQ0', 'v1:B5fK9XgWzF4W10d5lyLqPVrjY9woMvbyTPcb2j6q+Hp2pRbRsBZZ8n8ki/mslA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(147, 1, '2025-10-01', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:JfdVmhgGaeAYOFNPk4t3ngKXpRHG6klCiQkhy3ckGsyCm7pGdT1Qt0yfh7xqGusyHRUMvc8D7TWgDF1bqQPi8JpbeMX2oW519/3G', 'v1:bramk0/k4u8BmABz07BAN4TCbBA8VOl6xRgn0wqcs922', 'v1:hgwYh6s4ib6X2n0JP1SeQGfs2rUHXdEmz/VkzTJG7kubn6oMRWFcRGjDsReZSQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(148, 1, '2025-10-01', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:oQz3ugnbxG3WpKjRSvV+/1oGwur4FEpeHXd0CEIvMgCYcgZyyHX3cppSC/BHRPaFVoGkQn/iGzF8y7tBa7PASZKTnm4qrPJc9Cu1PBSU3vH10LGl', 'v1:oxN0FeZsMjVz1x0nE8GkKHiKidridWmUV2/hH+rNaVM4', 'v1:DGxC3Du1NoyofWj9uOQqI1arYLmB7kxm7jdwZ5hx8mIKCdRB/IhM35UMS2QaiA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(149, 1, '2025-10-01', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:o2gJYIBfEDQy2Jzwyr9zitr7733Zaz3kfDOcCgeGTcTH01AltnjVkNhIrNfQL7e6XCGUdfUio66j/nsPyoUK/L4=', 'v1:CC86BzNjs+F++oToL05DKMl9oS3Zh8u9NrVAUn7kPip4', 'v1:pgcaxeOS+ikjZFu4DcG066NNyH29vg+VuC6S/FxO3YQoyTvULduJ/b2pf8IWhA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(150, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:+RPHtpedkqz2sObXBPpaZwj5xomjPBd8M4+NhhT4RtOun/Kit5Tde1A3V9sFkzjheIklmNi8W4Vepm1Js1CDVG/O1YJG0u5H0pCb', 'v1:eE/kelT7papD+6x5a7qD5tdMZ+iN1SPy9grb5cMRTuOYJw==', 'v1:oHpt/ueF6sZEwH7GlJ2NQIxxZyYrJMMeUC+qRMQhuuEk0aUVSaK2Jx+YPDZh0g==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(151, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:Hjx7VB3svIMLKoLjfSSqyacyXDiKiseI0QqxySseo6f9EaqPx7i5KVaFSZPaEg7OB19sg+q2pEmVr4EVnI/NdA==', 'v1:Fw1i2wAE5fql7d8TLk+bF4G48SZ3hHeVBvC7XOb1rCbn', 'v1:y1B1aIVqF7FPgmCHmgcLUKAd2Agd9OfvXomfYPRO8aMgVfvkpbSJfhwVeiJb5w==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(152, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:E540xKGzqSrrrqls2xg6OImVQNTcvwmhU4ymQUafESfWt/JT8KhkmwtlDJFUraV9HzMRTpFCXW0KRCBOU5enGJZHBTvY2g==', 'v1:pN/WT7aW8G+vWFree4V8RLpzCBjIl+iP1/DG8x8NA3DM', 'v1:E+V5vnNcJhHWa/IKPELFUVhOVqxVOxxRimRNco9SWMl1ReBHuatt0UfHSLVBxA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(153, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:clCph7tw5Jb2J8CueeFQwK5iuf+eQeIvcdTX6NH6zOZ6hdMk4khIj3RhDcS3b5zOLIjnXxKot/CM', 'v1:aOgDJr7HCCShRlPBXRBnf0P72OJF3agM8uVpVWqieo86Gw==', 'v1:CUxheW+zoiB4W7n6P3cvJYRGjTJISV33U9Daksy7opCOnWCvbiEaq950w+BrXg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(154, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:yY3Um0aOyfwVjGU76/6IzhQ5uHMyVsvut6W06II0hik0yFMWS08f69co21+Ho/hQhKs5Bs8PQmcKIQ==', 'v1:/Ag3Xjw2tIPgAeFiqlckfLCyfwtwf8YwvWzJs9p4Nsuyxg==', 'v1:uV8hUikauFJaSR5bMR9aps7g7hz6TLCylgTRO985bQDxVi7DPlZe2NxVfD6qRA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46');
INSERT INTO `accounting_bookings` (`id`, `fiscal_year_id`, `booking_date`, `booking_type`, `payment_channel`, `transfer_from`, `transfer_to`, `category_id`, `category_enc`, `tax_area`, `description_enc`, `amount_enc`, `paper_reference_enc`, `created_by`, `created_at`, `updated_at`) VALUES
(155, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:bdZHuIENk1Tn6/97ng88ko3klu4yfVtO479+O/JPLOLyQt2QNoLz1PhhGxyT+7PQ24DM5y0sdIg=', 'v1:vZZl5c7/QcbGV0CFlcG2EFzEVJKA2XKB3qDsESxpIt74', 'v1:0JPepoGLRroBAHJGhcY5SK23o/9fbXIUP+SVBAX+arYcZ0OYwP+/1IpshavKrg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(156, 1, '2025-10-02', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:eEful0HYhPH8fK1zHPhTIRiA5HoD1G4eREBMJA3LNRwk4NOfErMU2jEKxnEUFbOdYUcxVF2CaYKq/4zmGt+2', 'v1:rtzxHw4E5mB31s3SZY6aAiitf701qqF/CJkj200tidHf', 'v1:iDiEVK0MDtHB4WTKj1bAaonjbzAKkHUJxxPapGjPZ0lZ8HvCO4D/tEvCSHxjPg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(157, 1, '2025-10-07', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:SEt4GYcsO07LMfarFhC2oQ4RJA9zqHkjsJC0cF8L08JUDAE66EbTRlSi5qTELRANtTeDCVeRFtSVLofCZ6OWJHpR', 'v1:kgd8JJJdzrge78H7plGtRjl+84JFmttjUySkursF/gsQ', 'v1:Q3Z3JzAQzfaJI534pL5WMuCUMhmYdCv5LVGE2Uh0adWh9SHBy8eq90OU0FGciA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(158, 1, '2025-10-07', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:Nh+YM9POyHUYRgFOfYfV+86+i4EMhPxDANTSG3Dcfz2wCA46J4amfLLQr3jor00p/tWV+8cD8bR9furBOHnv5YTVoQ==', 'v1:VJQk6UdPTK+HrpyzBgBwQQTCNYb2MdLHK/cltpxrW2rZ', 'v1:D9ZCc+F0CkbUbMZoyb28GYAHDcXwi0Oio/dPaOJ9VOw/eHEFzkQVRrieJNLdoQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(159, 1, '2025-10-08', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:/VnrSwo96zdWeHkAqH0/MirtZj272Br7OpJJImLurSsWVAjFLOYs3H+D76U0pAUMtsmqxgPK', 'v1:B+exMOVFKMa0uLQWSQwB+pcE/TYlBvOTlqcPechOEzcC', 'v1:QACTxuGCSAl3GHDrTYSWZwGKwKb7qxW1P/HhKGStiPF3rTtVbmn/5rxAjzuy3A==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(160, 1, '2025-10-08', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:ExNZISV/2cARifU2JDQ/9rOUEx5v39OOUN0dKPDm7T1BvM1hG6xMTYXVJ6T+orizDYTJabQk1ekpP/C0', 'v1:/GSPFVemt35p2gLmE6DkqwRzQ9F+nbatnPjSZxddqGX+MA==', 'v1:uBr7x52i7VIoFHoL/STNkTeHeDr2VNofwd2WpZ5NjBVW88yURiEPpXc9+a/d3Q==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(161, 1, '2025-10-09', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:g9luXY8BCAGLYY8XwobfamwOjkMhT2PeYQNTEzDY3GUUBcwJVX2yvtxVzaD0ZAc88O++d1n9qJczz+RE0lvaanjFYwc=', 'v1:mePX3CxQlGedCl1VXUq/lOcTFefpecuKrDD4kZmpCmaM', 'v1:OYedB7lvcrSlB003roCN6arunODZnwC8WC/zzEjVZ4qiUWDb4x9z0VFhKXBYZA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(162, 1, '2025-10-10', 'transfer', NULL, 'cash', 'bank', NULL, NULL, NULL, 'v1:3In1CsjnzcITTWgoLmRf6nrfBGvLY8lfbygItaC/aPxMoq183DCzQsiBDUozv84=', 'v1:BQyruX6aVoq4g1LgG9r5r8Ov9aJzP4uG9HRX2ZVDVnr6mpo=', 'v1:9iqDt9DBYnAVDkkGKC+wemn1+BIxp0+pGzNAkfOzVYhZR3H5hJBvR20HZYXFzeptdHDrFDsMRG4alw==', NULL, '2026-11-09 21:09:35', '2026-11-09 21:09:35'),
(163, 1, '2025-10-10', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:Wz/c7cPhkXrypCFipIm+lg2dGm7JJtRq9tv6PvJEDPRK/Th4Z0HXWPyas6GKkcV9PzseQomWgHPo', 'v1:3Go6Bcw1Erl/htpXrJgQUTMJfcWK7qX6f1N0sBdf20Ow', 'v1:AINEvqTz06h5qFKo2U7btzuvYFhS41qrhng+NsTlv2AL7dA7lvHhYLHGVAvocQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(164, 1, '2025-10-10', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:mKW3Jz1aO/L++ubme2VjRyNkIC/LLt/yL5rtzQ/X+FpmFy/h4YXXI1pxdLn5sV1z/F24rk+P+cRgSRKU', 'v1:CPO5jnrHuKOr2QdKgxEnaN8RU/yoHPilVLiFKbu1lHbrUg==', 'v1:5bR9yKXNIzEkjqf6X+qruexTnpAtrjDJIsT+ykYb4yzTB3gk/dUIvR5v6lDvJw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(165, 1, '2025-10-10', 'expense', 'bank', NULL, NULL, 37, NULL, 'ideell', 'v1:cAFEWjRfwvwWBU0kpvi3u/ovX8Tg7B1koQEbG4RBTXIxiBX1+TGNY/zb4BqbJZE2kfo+A6mFtG3XMNJyqGINJyazzhvV2YAz6gMWQ2xnOUn23Qeo+V/3I2ANdgFp9W0=', 'v1:U813KkfO/P9ZZKUwiWsne9WdAzKEwRbx6Kbke3RGEjG7', 'v1:NGFMNTLe0ImdJfqjEeKXWploQ3M3F2k5dghnwVyo1t2jiVQ7J+vi0QTtU1rMFg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(166, 1, '2025-10-11', 'income', 'cash', NULL, NULL, 32, NULL, 'ideell', 'v1:dP8tVUY2w2k4+rXggaORsTeTdmi16qGilLBfwtPWSybw+xaQ+K+caviSgMePGyQ=', 'v1:AU6QgmLu/Hfl7ErcLVhIgmvackcEUj7bZwiobVnCwKRPpg==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(167, 1, '2025-10-16', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:OBxPElQ9URcJeO3A+gDmKCTEAIE8eE8+E4NTcfkO00z8K0F5gMFkBFQaM9lgoJ3bp3Bx04YEMA==', 'v1:JqaMuyyP7b7eYumoEFcafZ+0+CymuR2gFGH8ROa85IvZ', 'v1:7OaZnQ/qUODfoa6bXa4UzIsdMioXKb09Akod76PLoh5ro18ioZLvfFZv6+6XEg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(168, 1, '2025-10-21', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:Adljf02wtxYddFpTe3PDEa5xhZJKnSpym1SgV8+B//bE26/Rz7vE11nN9r+6KYOe2q4=', 'v1:5VbmMXABEXai7+DyWTxZ051/G4obL/rAeIk6WM+fgSeU', 'v1:BTe1y+Ixjf03oa3Yr2L1YPRMD+dJDD4EvgbRDvP/oYbETGudsWyWjj9FYDSYdg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(169, 1, '2025-10-31', 'income', 'bank', NULL, NULL, 38, NULL, 'ideell', 'v1:ZUSnLlwMoDo6f7oSP0oW8qxlkhafWs38IfZFymfC9OoG2ohIl9U4WUHARZzwyq/er6W11yekLsvitmc=', 'v1:ck5/DtHAdoN5jpyXBpzmrb9plMDOgKxDRn5cPkIzsGD+HQs=', 'v1:vxATTxGCSAAQLLlVmGhwdprj6CpcU+oIEJ9cqQqDXN8Cc4qHjUfPKjJTiPZpcw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(170, 1, '2025-11-07', 'expense', 'bank', NULL, NULL, 39, NULL, 'purpose_operation', 'v1:cdFw4ZGhQBfkGraVBYRE5l+b57RVBGLnoFzWoak7l10=', 'v1:VkGEB/G4RcpurEOIhhLu1cIFHbbFMyBR6d/9QJiw8msj', 'v1:gvERwwnkdUvhtYQKDFQR8Au7sqR0n10K+WYIhK00kjnV551mxl/6Hy68bA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(171, 1, '2025-11-10', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:opwH6KuFbBp3Ygsebz9SDE35L2UXQsnRukOsvW/wlAL6jpDkmRxPtxMOxITPDpbi', 'v1:04q2/H5VEzoR6/eig+yxsZkvatP8h6r4fyA26GWxZ/pHWbQ=', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(172, 1, '2025-11-10', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:l2rW60TJJaCMubsyu1us1rdCfUf4PDADuzh6p4GVliNe9l0fFEDdXHhO66+iVsJQ', 'v1:a0RLu9aXwvzxp/r7TLzGrvlLIxUfnSZFPmnxPQd/JC7jhQ==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(173, 1, '2025-11-12', 'expense', 'cash', NULL, NULL, 34, NULL, 'ideell', 'v1:vgIKiR+Az5a5u/PJhPkJcXKSAkgpFf9rlwcqfMIW24ZNaz/5swPUNrBZ', 'v1:Zk5AekEA1jotTGyIiPMU8BL1Y66kdat4UIzFdTpXWM5nxg==', 'v1:rByyfg5jOnTgxXLT30WgtC1maWh4prEr47ZnPDKfeyI75uGFa66X+rKi/w==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(174, 1, '2025-11-12', 'expense', 'bank', NULL, NULL, 33, NULL, 'commercial_business', 'v1:Ctvhrm0mqfJOqgjNHnEUqPmq9clRbJoLC6youIr39D/2AsoPTQ==', 'v1:nSGoxP84GoZefVDjRyTmdWXoIdjfVwgVrlUxg+OKi4ZC', 'v1:l0S64JFJEvkBXhpWvUiybtefeoyAZM+ecF1g0NKdRwta2+43c6ZlaPM38A==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(175, 1, '2025-11-12', 'expense', 'bank', NULL, NULL, 40, NULL, 'purpose_operation', 'v1:MoGOP6Kw8bCYjRnG6gFOIlBVSxjolDyEg6b7D3qbzjwZ4P990Axvk2MDt925560jJxz0Ov0VKJBoLjnX', 'v1:BfRvNNbQPqyflxcJxWcdNzs6n7C3KD5Ui5ATGl9SjuV8', 'v1:kDJHpZAAEprpIJqOtkjMp6/D+xrCWitOrQ9FbHcfJ6Qi7RHz2ZB2mT6zJQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(176, 1, '2025-11-12', 'expense', 'bank', NULL, NULL, NULL, 'v1:WwvQ8Kdf5J/zAzU2s0wS/RST5luQLmMTD8mIVkvkWjcAdxh6vCk=', 'commercial_business', 'v1:HK7Uxp9JQTH7dXedSek3ETZzLSEMwZokZF0YauzSX/7jWSkvGhvjim9lTVoywTaQzkbrY+1UWjyO1/4=', 'v1:ySkmmM7PTrwFCgOaGizJ35Js2VYUpZXkYMOroyctm7Z2RA==', 'v1:H/7yJ+S2Vt3GwsFjZ5ebHsF6xWt3OXrn3CUNIJw31dMu9gm3WYSfsYshPw==', NULL, '2026-11-09 21:09:35', '2026-11-13 10:33:25'),
(177, 1, '2025-11-14', 'income', 'cash', NULL, NULL, 32, NULL, 'ideell', 'v1:nUu16S3hRqM7D7FKz612RUPaUKnQed5CjaJw4FSQBM1yTXr8/Eo3OH06kWiVmTzPagUgZl8oZIpkaAIJqjjG1vAU5n3xuM1hm5tiuA==', 'v1:VjKhMaxXPaKRTy2GJ8rFZK3Y8IiVGwmWWQjU1qbhQbB85lQ=', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(178, 1, '2025-11-15', 'income', 'cash', NULL, NULL, 41, NULL, 'purpose_operation', 'v1:FP7zpcgco2NWRN2gPHM8TYTa68XfXi3j5+p0i+xugzAupTN2TidRoTxUhYA=', 'v1:2mnRZm0j4xdLoUu1gVYInfADS6S9Gx8BeJXDw2Ib9pV/uQ==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(179, 1, '2025-11-17', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:l2HqZl/2vzPP+c4xsVvhX9Atq24OJ50ujdzPjHpIIfKshxjiqLSZTRJoN7kiGFy6z6d6hBNcbwSjLWeCgPjnO0MrWVbkLI4iiQ==', 'v1:FscSZITFZbfES88jmoJpl5XZMMfQp+AOgCVFzIfDP+JG', 'v1:AkRG5iLj9uH9omZNDLMIF4+KkggxqUW0JfQlofSMqLJMHCIQjZJ6CXPoXV3HrQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(180, 1, '2025-11-17', 'income', 'bank', NULL, NULL, 38, NULL, 'ideell', 'v1:AMydWufwSubwCcCN3/Jqt/SkxnZn5BM0lhNbu9qTqVegD5yfXNW7Vl0t243itaUSZMzlUivPZZwo7jcqsg==', 'v1:ztzP68OLQwGyopNBr+SHGzFeWFE8guRpbicWLJy2JceE', 'v1:/tPasZ4g4RRsS1AlFVzeWWA5CDSl/GkILMkEeNjluDGZQKb73mKywaLM9UeJ0DQ=', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(181, 1, '2025-11-17', 'income', 'bank', NULL, NULL, 33, NULL, 'ideell', 'v1:xXWMY6WXtlRshCvIEF1eEGHaGXS2nnXR5eOiakhADgcuM8MZqF3Mhld3m982DVaN933xaFuol0p9bQ==', 'v1:NpjhrMhPLwfQJ9GitTFkab0XBphiAi84RcIwJxvJEXWK', 'v1:rZUYGT+V68tirNuUQITSHxPpqfNSZRrUKDhUUzv0Gxh2Z6u2iC5UCi07qC5o7cc=', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(182, 1, '2025-11-18', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:TYFumC2fQoKW8uyzg0uu6bc2iKdjA0x07SOuFY9ePtoABE2xVFDJAwqw9mRxAb0GqTKPu9he2JsA/oY8uUSqtmyavTb1yEJgNGTX1DifMvWbxSZtoVqsuZt3z+gPi7R/ICWUn/s0aaGXynkMS5/6+z0=', 'v1:Or1XkOYdMttRHTzz8xqfIudoddPivcwkqD5xSPg6lV40Og==', 'v1:kJzXOAfAygoRecPkjmjanb2drb58Mm2WjMgKkP9q1jAcqK8gvBWPaqObnT2eGA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(183, 1, '2025-11-19', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:wXvB2oTRqYRkP7YqJYpwcPLnFiPZS3BvvN8w9EoS207UtCKA4zb84yNI3LwMctSSKmmvXjHmJsGi+lgqNWvmxms=', 'v1:RkmmjgRk+KcJaX/MBrTpU4fvx4CnjE9mDOSoOGxVqzcR', 'v1:GcPlHnSMXUdB5fWgMjxTqxM/EiiOKMhQqqx3a6Jt0EjPuKvdp2xI8PPGZgfB4g==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(184, 1, '2025-11-20', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:g4fmgIKJB4W+IigwlIBFlXj6JqZWmAB4CILTAaUUCkuPfr6nhT2S4hnsn99fk4gKT98SlbEcWExR', 'v1:SeZE7HOn7+8/QY5kJ08+ztV4YTP3r18lzTo3L4UaKZ8W', 'v1:WabFgom10WFVSjQplux5irRVgZ/tjAUdkrARDwBiEW6L4L8K8che8k0OQQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(185, 1, '2025-11-28', 'expense', 'cash', NULL, NULL, NULL, 'v1:VU4UkfRcDKgW4vQedZLRcLcIZZsaYTaGxyleFGsC+vk1t1TLjv6F9Oht1GT44GA=', 'purpose_operation', 'v1:+4+ZETllUizxkWvgDgItoJDkgJUkho/1XqbYYfCc2gVLvABsJEbJ7Y7RPfwTDQMxORIQrXeOgg==', 'v1:HYZiy5qfWfioXGI32JfobhmGK59KcYI4l3YCMKQPurLI', 'v1:M8Moz9RgK12awgKtRCTcH7HdYhFk1U93rAo2PZTiKvfxOeOqt5r0spJki3fGiWHFfE5Aavihdw==', NULL, '2026-11-09 21:09:35', '2026-11-13 10:33:44'),
(186, 1, '2025-11-30', 'income', 'cash', NULL, NULL, 40, NULL, 'purpose_operation', 'v1:l9LH5Ut5dNRXoMgLJ6eEYwS1TFZ+KhOKB/ZCvRGX30+PG0kvN9/5WGbCF9WiobAg6sxOQ6svZA==', 'v1:iA2EHsThgwl3n/Wd6t6ENbp8+nTfUwiJs89pDNL7YkoiD30=', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(187, 1, '2025-11-30', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:wKZB9m+M7JGpDM5P7LM57DD7iaO6MiEY4s8HJpccn/+L6Nro14HjS0xBa9sBVNBILlbFS7An9AU=', 'v1:IG6gBLy1DNznU0FeXW3Z1F8R1vuIEDoU+Ex/pOIDR9Y6', 'v1:toXxRuUdL0ldZHhYjeaQK6+yGu5liR07N1xNxU0DKq0o1qM6NXXMnA/YKw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(188, 1, '2025-12-01', 'income', 'bank', NULL, NULL, 43, NULL, 'commercial_business', 'v1:lzWIWIUkmD5wMfolFQOrWbqCIDkN1EDPW/OYubIDor4M2tgEs0csjXttftqfe0bE8psmWmY9YMETU+bVVw==', 'v1:6WZPp7T1XSj+Ot2PDalsspnOF5x2jdxNCRRHcsyqHDZ6', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(189, 1, '2025-12-01', 'income', 'bank', NULL, NULL, 44, NULL, 'purpose_operation', 'v1:1XjzQHaHBG59zHPZuPfNEhdlnlvTMVBSue6MQOYKuihpPk/9IJ1h0TBEvmKluJKskXAQlAqE', 'v1:24+h9jq+jrN6sS7BrIzdAKMz5DklgHMUittmhafSWjLd', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(190, 1, '2025-12-01', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:qc3DTNJSHqyU73hfK1waZgT8itsqYpi1S1S8lrWHQ4Ki', 'v1:QK4zNG01r9zBDboyASZUEeEujRgQ9sGcvdDD9xYen2+x', 'v1:PGfvF34wAHmukb2Yvhdfhih1a75uOPbaM2VV6Gp7eiDzNfaIE9unRg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(191, 1, '2025-12-02', 'transfer', NULL, 'cash', 'bank', NULL, NULL, NULL, 'v1:ceV2Hz9wJSVE6Hu7FNqzgrfDOzL0LZgNA3g+nrNtwirWt+D9vrsUGk8uJ4JpjQA=', 'v1:yKDHnDGqeMlcNdsZyKwA6C9ri2A8PqDQvHVqpXMaBoSQvec=', 'v1:wIW+ah7xfQH5uKrHPZPrjiGMCTr7kpYuRjZWxZ0hzGRjNwzsmPgB4w==', NULL, '2026-11-09 21:09:35', '2026-11-09 21:09:35'),
(192, 1, '2025-12-02', 'income', 'bank', NULL, NULL, NULL, 'v1:Fcxqyqli9GOP9NXO7d0dE9gOb/rlnN8ioi3zPfXq0gohTcQ=', 'ideell', 'v1:cLuUyzZxFvm23Ybp2cAlydhUS7y+0iZ7y0yNTJxvu9vFa8vFidP5WvTVwDCBGIkCB4xNUOg/pxQ7D4lLcf0b1dpvCnn1gjuy+2RfbflhBjU6XTn3EC4WsUJ7PLa9EcHi', 'v1:kAVxRBu92HyZM5vqQ1svRjTQuTMPxxiBcrry12CmKcqI', 'v1:BvafzhlF9wUlA9o6Ak52zzcCisoTIZSuBZHi9789YUbdnezjzv+NWQ==', NULL, '2026-11-09 21:09:35', '2026-11-13 10:34:27'),
(193, 1, '2025-12-02', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:kde3GgFFVTIg3jKVjPmcWtHTWtAUMvmAfAMDvbPLHWO5WybZvgRpa+9QEuvTlEjgBPq7EUSajwo=', 'v1:D9aqRjINTD+mQCqGiENQc0cAyEVByIZFD+Rc2H8ubS9s', 'v1:NbQCCBVhOXsX6DW9sgxxqASiiLZIE4RQbu5hyXxznY6yelm7RoWgDA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(194, 1, '2025-12-04', 'income', 'bank', NULL, NULL, NULL, 'v1:Klr0XEig4XjZD7GFnsOXJUXLL8rUMUvH1kwO3/8MkFjmNeqP181ol6TxpOn1O8k=', 'purpose_operation', 'v1:K7t/XmP2T1f+0yvbcwXgfi0qRUXlofHwWjjpL8jnCSZA958dSvVvIBq0cISDQ8Gfn8Q/', 'v1:V5M8kAqQyvtJuUtPpWzoOMnBoPUu+H2vVYljAvFVhsSA', NULL, NULL, '2026-11-09 21:09:35', '2026-11-13 10:34:42'),
(195, 1, '2025-12-04', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:wcqzOTv74pfBybjA/+w6V3cIOPnUeWoI6tBCPKGG0aB5bOi6Z2f9Usdrkyv73FTKCw==', 'v1:wEhVIF5fQ8nzTtAs5d2EW9v5SEENICShAwS90j4gpyky', 'v1:tzIeJHXOLeUV1mhO/ubC8gxNaK1Od2Ajqa1HgIj56stSHLoJc37ZEQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(196, 1, '2025-12-12', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:3ixATCqaaIbExozyCX7N2vBzoQ7ioW+m+KCSpapa/J9OCJXFRr0tAsFBqA4=', 'v1:111UovQpx6d4QGJvZQS1IpKAGhKHEBFo5KMLg+hr8Uge', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(197, 1, '2025-12-14', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:493Vqsy3xw5473VsNOX92WTbHOH04ffOFfSDaUb2bvMPk3yYOQ==', 'v1:PfHUxe1Tzg01oTz9ea2zbmpZU56BLJuR1Kp4lfoEjf8e', 'v1:qLer0YYO7fc+NxXOkUlJ/glXjb6hLOaViDpk3uPhyqtqdqzA+wBrgQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(198, 1, '2025-12-16', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:ey/FqIQkK0WAPtNkvkY8eRzvyg+VAPegXOJXttvWl6r0zc+Q3uRoVzKePCTF3VSQXFm4ltCKO8LqBdJTwF2A', 'v1:tAYi6EMl37eJbZHGZOXkhQU0BO5GnqbQCxQ2IbfshWUn', 'v1:1wCl25PDpnbDOzSQ7N9p6LHBrJYCn6movdFz4kTf82DMdT3sa8x+LA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(199, 1, '2025-12-16', 'income', 'cash', NULL, NULL, 44, NULL, 'purpose_operation', 'v1:5/kibvysYNx++sxdCADOx+XJp0TtXOCzc7zgDdJO4yzgtJ0HUzqrNGi+TBBRNCYGfgaAcqw6h4kxSpjng9Q=', 'v1:FAANEZXCpK2RP7s20Ph47HxW+sgHn9LQUbdGN2vIFmzV4g==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(200, 1, '2025-12-17', 'income', 'bank', NULL, NULL, 38, NULL, 'ideell', 'v1:WHj8dd8rTGhPQXHsW37e3Ic9gayBhj6hulht+EV46SMd1/t3ZZzdjuqC', 'v1:wPEB9pIRsJFn5eqJH8VXS8lqMSP38ra1mY6OJydMCD9c', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(201, 1, '2025-12-18', 'income', 'bank', NULL, NULL, 43, NULL, 'commercial_business', 'v1:weHt2qx7Oo1FzLpHBTAHB1sYNzlkEx1GgAWPDgG1lz7U+31hnEKAf4qL', 'v1:gjCFmr10aGIOp9eOPZ7z5qKv/aLpS35Nop3EkuTyiyzN', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(202, 1, '2025-12-19', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:+viPop+J1BvttyVPLlSm3XFyvKboDIm0xLlT7jLnSwPYgz5iufABeB/peA==', 'v1:f/OEwBNhXS24SWQNWxV2n69ekxvWXsxbIBhKz5yiSPbi', 'v1:1DZacG+akV+PlvnY0Cf7c1wsFqwz9O/HbCyKH0Kzeye8d4FcdXKloQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(203, 1, '2025-12-22', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:0jh+BDqxx4+Mk6O4QNR5cROoZZU+tWBAWg5US7in+ra/Yjo=', 'v1:PKVHPm+Wel+0eH7gU8KA4KXu5EuoQevet99xwXyWG+pIa3Wu', 'v1:uwLSAZRoRmHuDhOCWidBFJ4tqeTAQgxhoDeP0UaAV5QJXj0997QWqw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(204, 1, '2025-12-22', 'income', 'bank', NULL, NULL, 45, NULL, 'ideell', 'v1:iUA5LOtUPp2l+63isxLk3tSt01U6uoqi+wkO5j5/zkFenJcoosZDttIuCE7LQBuM1LinNEcn77Q=', 'v1:8ikjtyLjzUCL0ufwBHg1FtOMrRqxTPOKQA8LXe5hS+OCcC8=', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(205, 1, '2025-12-29', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:9W+F5HtLi1GM7F0sS+Mfmek4QK6XzkQM4saBkCC0TgWVn6eFC3Zkbhxfeg==', 'v1:lBaUpfTjwOpXNPlEMs1V/SD6xJOBmpwzoAYZ2jtd6HR7', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(206, 1, '2025-12-30', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:FVZVpeQY4y48G5CyNzXNSFIQBIuQ1eI/lPVF79cdiAFSdRi649iE', 'v1:u/fqRNUXXwrxq/WtscUH39mt9DSwxw6s82xaxSoIOEA9', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(207, 1, '2025-12-30', 'expense', 'bank', NULL, NULL, 36, NULL, 'ideell', 'v1:18eej6uysnjSl50bhcb4VATKXlr4SrP52JYtPmmTXceVp+QvJgzYb14P5hQCUEUYaw==', 'v1:CKksqBtH5zWVy/YQcPZDIW1P6SuCYf1JSSJWaOMh5yn/', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(208, 1, '2026-01-08', 'expense', 'bank', NULL, NULL, 38, NULL, 'ideell', 'v1:YlTWHIxeEprsQvJvEgdoMBAE22HrV6tGh4fJKWqnJFB62YpZjprlEVHLF7wQ6v+l1WGIBJhyOTObW5jnxPDnkWTMMN0=', 'v1:8kwVuobl103zRzH3zEHi10K72NLB5tvIYD1CZiX+t72N', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(209, 1, '2026-01-12', 'expense', 'bank', NULL, NULL, 42, NULL, 'commercial_business', 'v1:WOPcFeDxbZTVGy5OrR4f6EJnRKa6FxQrw4FGACP6M+7KzSf8iXTr', 'v1:JOzwzVZBctFrAR/qxnDT/UIDbvhA65Jpw2fugxyudpSo', 'v1:qA895HMd0OPvaSBwOjeVQxQ3881sznmGF9ihU03+RoLGmJ0MwPFL2A==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(210, 1, '2026-01-15', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:zbKbRAqKlIY5XM12Q/WjyYc+I8vOrEcwiv+e3x7nzPTrMZH6jaP6Ncb2mM1zvMSmW7cupVcUQ9x1ocLL/uK54WMEsg==', 'v1:3mUT8Wml7qLFMHdCRAg/wDHCscH2g2ZJiT0rk0AFmGGj', 'v1:v6qoDoFiPHaEfKEvJnpEJySTyQpHvnlf0G3bLbZvPVOGVMOCZ2GxFw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(211, 1, '2026-02-04', 'income', 'bank', NULL, NULL, 43, NULL, 'commercial_business', 'v1:6dTJ7PrGUkndVKy4Z56hScAqqyt/BgLu/J7chb7DkJd3xhCDPjVfPb4D', 'v1:NuQasYQLOk2HEssf+P4EDDhQ2QTsSbOgZyfXy+XmB5rC', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(212, 1, '2026-02-23', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:Fg/hO3ugr6FJoDsqGib7yYKj202g+pH1c3t1adD/5ueBBudCvzVLiMNeiQ==', 'v1:aMZlmI1wQiVau4NfMXRYfqHgCyQq0XHexygS7vpTotIE', 'v1:KcHwUbYvhgdkQcnaHTn2NbLiwkt3Sfy3/YpJ26V++hc8GJUC//n9Og==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(213, 1, '2026-03-05', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:5OARRCENFWvshdPdaeJ/kYIU7pK7lJiUNUXni6cRe8Q+sxH/6LIvaKN23Bjmj/0EJjUx4Hlj0YbgxYXxh0oBhTl1R8pcK+KPmDm6btKNXCFK', 'v1:7eCyAUJu5zKj9us9iBUPL8BxvdMrFPvvegJmD69Hct2W', 'v1:pKo6Vjs6o5IbbzFEbXE6HBI0qPGqmqo4Pd7NllCAttPMU0j7RrPS/I1+lA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(214, 1, '2026-03-16', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:OzR7JFQIT/ocKAHAusFlnXrWH9djiFu8qez9FEjeW418wJ9zdlrg', 'v1:VYzGEkpKBxdTT0led61k7P3mJ7IOEF/F1SDyY+qSIaDn', 'v1:pY+5eJm0pEzffBhCY/KJe7Iu6G2oAyTEc5baGA4j4/4fQTZHeaAmxQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(215, 1, '2026-03-24', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:E8CqlGCQ6jXE9QlWcMyUM8/RzQcL+mRsQBw/PH/UZ1lGtL4eXKt5drQ=', 'v1:HE+9eA7rJr7/KOgc2+9XGZ8jvV9M7fMQ8jvX8Sdpzy7Gpg==', 'v1:VVOQAV6Cm6aSquh/vVmr4qlSS+bTmP5H50txaRC2vd3hhk0+6KqM5Q==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(216, 1, '2026-03-31', 'expense', 'bank', NULL, NULL, 36, NULL, 'ideell', 'v1:pS8F4I/yqH0jCpafwCugZOMCVdrhdfuiSJR0k3uvbnSqXkB9LD9Xgn5lJN4Qs9+TDg==', 'v1:s6+wzN5mSsI99Fh/VAkGQPAwYqG25BkHRY0VUy7cZi0=', 'v1:s9umDKPaAYkqSjiAaAZh6FptdZyXc5EYtwAITMqZvN5TAyMKxV2OiidXwT7QJw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(217, 1, '2026-04-02', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:WipFsJ6GqrC3nC1dkFDocZtbOFY41MVtt2hbZmtLjsw=', 'v1:6d6p2fvX239CWjZdhWJVdKw397gU/biIK6c1MdJ9bMYYsw==', 'v1:7qsVCh7EC+DEujbjyUyLXmOyF3leIJYPFYpOmIgzEKS5WoqYUHXKbA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(218, 1, '2026-04-07', 'income', 'bank', NULL, NULL, 32, NULL, 'ideell', 'v1:EiPXyjbsfosbXySjbGeBl+N2BDVDHbs+439YLqLLZRfkdy1C/kdn', 'v1:wf01mhyDfpPUE2U5SkPM0f5zpbLH4Bi1gS1E2VM/Yw61', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(219, 1, '2026-04-15', 'expense', 'bank', NULL, NULL, 36, NULL, 'ideell', 'v1:RlGGQzSRhoLHmfiPx6O0AFaTSUQWeFhvDcEiIVoZTBz93Rwz7vQB0HbKNZ7nIo0OaCVE', 'v1:yHdfpxoWj620oCnxgVY97JhSZe3GkxNunZhKvRhDqRsYng==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(220, 1, '2026-04-24', 'income', 'bank', NULL, NULL, 38, NULL, 'ideell', 'v1:j8pEKH6GFcbQUAYGimH8EagXoDTQGneSOdR8SJAKA18D2A7MAoEbIhSfnftfxi8NuL8l+pYI4KvHDW+rj+do', 'v1:G75qWndBM1dn3aaQ7NZ6lEDPXAEXY48sNmyiy7BM0hYS', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(221, 1, '2026-04-29', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:nCjYTiQeF4JN2e/jpWlJczL4JOwlF/1ar6VzKwUY91rSbzwNsjw2Ce7kcUmZFz1ifzzv0IBOlA==', 'v1:mFZHDmqP7SupKG9u69Jl+iFmL4n3urjW+aswLBLZpdE=', 'v1:I7+mVKWzzapul3Q6lJOdxr+EB8wTWwLop5GUovXrHw5iQlj9MXIGQTdoow==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(222, 1, '2026-04-29', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:FXqxosXcTKwH/woFG8YN+37q/bkYqT6coeNiixOwPYfyeVfjlvt4zclu0OZ011Q8G997Dq4u3V1x60bswBd4jwp/W8+Bq6vYHtI=', 'v1:Pz5Dukte3NY3MgDYRzY657akniQzyeQe0iAAC9HxOJ0a', 'v1:Bi8Za6rvC5uMkSJsIgb5mmfkfZmFSC4x872RCHzp4qCwoBmB9vMjqw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(223, 1, '2026-05-22', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:goiZ59GxpBK0/ra2ZZIdZ3Ay0PwW4cDSXbPsOnZtsEg=', 'v1:sG1gGoz58Uv1en0mTCfkgorO8gzzgQEpQfLmQ0p8mbtnpQ==', 'v1:bagResCcG2ijiKB47DlJJy4m75fneVXz/ZZmfC4bxC2pKVHabjF3Rw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(224, 1, '2026-06-02', 'expense', 'bank', NULL, NULL, 46, NULL, 'commercial_business', 'v1:53mgNuzJ1GqrKI9aBiwgApISdQDdlOl5Tpl8KOX4facjTbJCLw==', 'v1:mwVQUW3npEi2TmbRLSeL87O9yVw2ChYirWzxWx1dfLgb', 'v1:/XdqMgbROBYFW41/FNuHgGHP70rfd33PvR4KEQK3Js+kO213N8BT3g==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(225, 1, '2026-06-04', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:pe62rdMs9Jf+ULgqKGqAGARHEhUyCzCZQHx8F+IRIYiz/sga3KfsOmWinUsiWMsG5L7cN/+GSGmu', 'v1:pZDl4mTOEr5tUdeLMHdGRSlStYfXgDI4siVd3IitHVN0', 'v1:GSfRYTSHC0xoEbz7chZHZSW7UbzTdnvhNA/2Qv/FAF6aP98OK+Eoxg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(226, 1, '2026-06-09', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:7RqMdUPJixakJqRGC58Tezj6GvT9QCXAeckZajUyI4BOD3Fd+bKEzKaO7FWjyTxw/Z33tTUV4ecaAA==', 'v1:rmSULDPuVJ7uhUNZCAZOJ1MbUQoFBwnNwebrzV569n6zPg==', 'v1:ztGDeHu9ZXEkSMTgGIku12+7iN9dKTtNY6RxWqHJq5frQNYBsDOtag==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(227, 1, '2026-06-09', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:6iLvzT3i+b659iFbUV5vGFmlDPk7I01P3EF8JyRlnx2mmcAIw65yrYqsxYbJumGcV+A=', 'v1:e3yh/NWVY9DqeCWDmhjndv7Tn9MNSTrUvI3udEcAJMQz', 'v1:ulbsDJNqqcEyqCeSx7rvzOrIUve6BX4dvdg8GBwAQb9CDIiVX6MDFg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(228, 1, '2026-06-09', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:NkenHj273cwP8WnWQcn6CAhD4fDCN8+K2FNDInIZfQOgzycDzsUCbi+m4lWiHRB6', 'v1:e3edTVP1ibOIUj9cei21wIxWjdR2HyspMwEz+6XwVQYl', 'v1:9h+zXcsmfB5DtkrjGp6XF1heNy36N4lHsfogYnesI4laqm5hhx+t9g==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(229, 1, '2026-06-19', 'expense', 'bank', NULL, NULL, 47, NULL, 'ideell', 'v1:ZybJdv5Fxp0qe5ijcAcGT0VzNzrVN5EOL1cWDbiNk+HjB+CmmZ2RciILLX2AqWY2q0GxhhK3DybwSahyDO92xw==', 'v1:hvRg0RU7Ebei/J2YTo3kbLfp+Fr43DlRSHUpWxyxRX1oKg==', 'v1:KClvaVaj3FLWtSVXDIevJzkInB+mf7uxUP/8yKbbBxR4IZ5NTXJ8RQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(230, 1, '2026-06-22', 'expense', 'cash', NULL, NULL, 47, NULL, 'ideell', 'v1:TRNKAmb/WBLGl/acy8ZYb+2gzgiquiS7o+rgMV9hKdb7c1Gc0xkCLTuZ6PQ8Z07PTQrbK1epumM6qkTb', 'v1:+19aK1HU2gnCeRULI3o8FZxokvWjd5G5ga5eNkueZH6wcQ==', 'v1:/ghgl1cPl6NZ4bMNyH7Tm6cgPAe36idXo0yNaxHUtiWXh+PnHIPtbQ==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(231, 1, '2026-06-24', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:1jcYDnSNxvotmGPEOkj+k5xaH2SlcufIEBIltCsHAWmx3dpjQ1QfyGpxtKl8rqe+gj98OfVR8L9eVQARmqtBZyH6KIVD151hxGCQnw==', 'v1:INnEqRhBcMMvk3lzXtUNpNX/REAw5dhE25DmDfdcneHJ', 'v1:CD66VrDUtLSx0GC9U/jLmIZDgmVjWiIoGVcWlULX7T0cVR/8XPxx4A==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(232, 1, '2026-06-25', 'expense', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:UNv4VsEf2sBcVToH9B5zxOHeKzgCj0yOsMpyYcO0TYpqehiBTN1DJG/n3NLVLJ6hCb7FNuOKzREwvn8vll2kuhd83w==', 'v1:Acy7aHk+jiregzVSfd7cVCOEu1aLIUV+4ttlZbz/WnWI', 'v1:2Pig7mRQGFjkw5zmqjOo5yCzjUmFNh/ftLYyZ6u1Z2lB/rkHBxPKpw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(233, 1, '2026-06-29', 'income', 'cash', NULL, NULL, 47, NULL, 'ideell', 'v1:Ji13Rg1b2L/g12Y2fMvohfUzyChrvby/46NRhfY4U+OBZj1aYvh3oatm2ryUbsIy48+VMG/GLr/+DVipF5c+OQ==', 'v1:VUu12rvHW+IneZsSUs1DPqQ6VowviAMvKIrcc/fOFL2oWA==', NULL, NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(234, 1, '2026-06-29', 'income', 'bank', NULL, NULL, 42, NULL, 'ideell', 'v1:dzcizo0eh0phaG8U/7VHIMniS5PxieZMHWUo6D9/zKOBwDfjTQUmKVdvDg==', 'v1:De4Uv2IFYtGLL/DEa6+Oit9CFsGe67rNGq8fwOynsIkMxw==', 'v1:tGRCDrYZjq2+Qirm5wxIZic0WuNPB5NbpE/4a9TQENEI2iZDxvaDte2P9lYDjg==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(235, 1, '2026-06-30', 'expense', 'bank', NULL, NULL, 36, NULL, 'ideell', 'v1:rUPyowSQBYeckV/K48yyAa3Bs527HKm0wmMdCaAduGm98dBRpPJdeLVk', 'v1:TXhlgK/OpR3Zl8bhTXFivZS8DfPlD8qDWJVLxCPhlXo=', 'v1:M28Y/KHxn0cy+Q9e3tlnynBpXEOtFvEl7Sg6J53rvcolsr0lQzqIWVWFi4YpxA==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(236, 1, '2026-07-13', 'expense', 'cash', NULL, NULL, 42, NULL, 'ideell', 'v1:eRLIWErYuWBszzS4LR3H3/GSJHoSaC6FpQfyeBZb5LNazWq9lIzM', 'v1:BdxGsXihLoPr8ESPHWKvTbObq0cJfg4trx/vYyLBuIlU', 'v1:2Bxc1g8ePDMFF0WOMPyMRC8FT+vtxT2VTy98yQD8pWvNDcKE8DyYe9a2Uw==', NULL, '2026-11-09 21:09:35', '2026-11-10 23:04:46'),
(237, 2, '2026-08-06', 'expense', 'cash', NULL, NULL, 48, NULL, 'ideell', 'v1:w7bulOjmeG+Tv2mhjkpLpzj6uFkRFwAvWW1uZpyeOqQpxSCxt1ppyZiShbFBumiN', 'v1:H2GVcaOk/oLEXE72sANbLq/UQJudpNSo/eCEYFs4Yi8=', 'v1:aX2/hgj0C6LLym/8pC5sC/6MzmCAMatKhSxu+JYbC77+mXMc1PFKS9ofT9A=', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(238, 2, '2026-09-18', 'expense', 'cash', NULL, NULL, 49, NULL, 'commercial_business', 'v1:nC06OGWBgUduwYzg30cXGsVZQZoE0Gv1JDEe5sWOuHPGo1kdC2CHqR5y6A==', 'v1:1GswjYgNtNYbLnZeiUhka+BI8fSRqggSBDdCJJu04kUW2A==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(239, 2, '2026-09-19', 'income', 'cash', NULL, NULL, 49, NULL, 'commercial_business', 'v1:j/l2NkHFNeboWDwHwfFqeRV5977RXTCOHuKiCEYX6lzBoAWQD+/NAKngX/epRE1KFPf3MYRrSaLF', 'v1:c1VRoNGwE2kTz43NJtogno14+NEGEmK/Nz5XPzz5xSHo9w==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(240, 2, '2026-09-19', 'income', 'cash', NULL, NULL, 49, NULL, 'ideell', 'v1:U2A1zUlZuVuAGDH/WPiNt69CxnFyExk2urYEIYnyzkt7xw==', 'v1:sa4kktA+x12CmbLUJoVVdvKvX7gat+lMB3790eoYz/ws', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(241, 2, '2026-09-21', 'transfer', NULL, 'cash', 'bank', NULL, NULL, NULL, 'v1:Wejh2S5E/znfnFWKpAPCk/p3UvPzuH0qL8MR1GNSxagfzss17mK0yTsEqC4PD+g=', 'v1:Y6+ZVe66NtR3Q7l/AFKx8e+AJ9OtPFE3uI6eD2N7lGG61xA=', 'v1:e90bgkfNcK78OkCq5DpT+RN72XdH4a58j4uX0yczvras6hasqsQ9fO4yS5pWNqPY2ASRZ9CJvw==', NULL, '2026-11-09 21:13:28', '2026-11-09 21:13:28'),
(242, 2, '2026-09-29', 'expense', 'cash', NULL, NULL, 48, NULL, 'ideell', 'v1:VC8gUZbVJJYrqOUqjGSn0OFsbY6+mLIAWfiu8j3jg3s/4xLQce++IQnsM2eCzsXLNEshRGs1nUQ48g==', 'v1:z9AJQ1T14ZHuThFj2LMwCMe3RQiGOraDnYlLbSVzS6A=', 'v1:VO5GeaeAwe433n5GHsrKh/2pjlkRadiABLgzBN+GmKCavdKO1VOQsg==', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(243, 2, '2026-09-30', 'expense', 'bank', NULL, NULL, 50, NULL, 'ideell', 'v1:GVWjUTFRUlTZCUNCf0gekRC5wydI3qmGevHVTmB7yYBqfxSKHiE2ifKy', 'v1:OArfbRAMdAz1g5qJQ6FHfO7kHeOhaQ8l7Sx0VGjA+MQ=', 'v1:0wDa+px1C2lDq5/vAd9nmN0bQXQYX/6HJsGUUVCx+WsqrhLe6iJR7Pc1B4I=', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(244, 2, '2026-10-06', 'income', 'bank', NULL, NULL, NULL, 'v1:i2noEMbG6zmjCxu1t/lFYYCrIeoJZDaa0CcZCaDTw6Au2A==', 'ideell', 'v1:l3Ob72ZWMyV0eN3EiHtOn7zy4iF5fMXVhSKDBKHdsKZqLz7E2bagCWV1ZNk=', 'v1:dNUJXjN1SQ6LX1xCPXBDt3C9ugrGiQa/GsOIgYzrvb9gYQ==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 14:51:37'),
(245, 2, '2026-10-12', 'expense', 'cash', NULL, NULL, 48, NULL, 'ideell', 'v1:rmRi40SOdHz6VpwbLpv0DBO2jr8buad30mZAoVzOjTuLOgEZTQtuVarokNnlrMrGyJlz1Lr4uVHVu/esfvjLdDMdpZPmFnw=', 'v1:Myj91zNzudDvbFYhzkJXf97CB6Hy/RuwY2pXfXQsnsg=', 'v1:Nu5myBr/9ppMGFObuxfrf5bqFWlKy9u6dJryjtg3rfd8+exXmS2bXA==', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(246, 2, '2026-10-17', 'income', 'cash', NULL, NULL, 52, NULL, 'commercial_business', 'v1:Udrc5yK36c5AiEJm1Aqp2T84pciapj4NQubYV6esfI4JMs17LNJ1', 'v1:gykHtZyKkG/mWgf7hO0NURMMPDFS4+uBv1ZuNUj6TRiW', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(247, 2, '2026-10-17', 'income', 'cash', NULL, NULL, 52, NULL, 'commercial_business', 'v1:IqL3cr3wdCb76EN/MdzieTTt1yKVvNp7+lfLICZ97lRLCPPGLfFgf0+rwjI33w==', 'v1:iP7BJ/rNdPauoCXL3MsEmOctS8FFAUwOoBCJjBMgi5EIRg==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(248, 2, '2026-10-17', 'income', 'cash', NULL, NULL, 52, NULL, 'ideell', 'v1:57s4NobvBc5Hc6V1NTnUpsr387vHFfb7m80E9/dR5n6772kHim6abgwnZg==', 'v1:1skVmJ/C/UmaSNUPe8gIOubsHm4dbzrrCjHHaO+ia7C9zA==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(249, 2, '2026-10-17', 'income', 'cash', NULL, NULL, 52, NULL, 'commercial_business', 'v1:bgxlz5tHKiwGnfZb+09rCPawIRhQmwjP5RoDNsgvpELs/dfrN+Fudes=', 'v1:fwwHZOEjrFlJWNE8bvsRB8dZ9UNNHMRpdD/M0GO5B4jUvw==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(250, 2, '2026-10-21', 'expense', 'bank', NULL, NULL, 53, NULL, 'purpose_operation', 'v1:fH8oQ8WxlG1na2+jD3wn30N4BQh5IOetUvNES29yCPSoEuYP+4xJ6nN4jKxFCrkA3Vg=', 'v1:bS/GCoS6bx9o1PzWTdIFeShl3jqjMFwQXt/nDcMGN4ezSg==', 'v1:gtAQ8qmE14XKoursdKLeeNk4HbDkKXQvYtzg8IxrPDRsoAc82qvPwg==', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(251, 2, '2026-10-22', 'income', 'bank', NULL, NULL, 51, NULL, 'ideell', 'v1:2YPd7UAomF1ZIko3ouEoj/hlb+gN+jwpM3fwX3vb5At7dPZeDtppGrc=', 'v1:tMm6aMVNTHkdjsN6dg1G9u4rCK0IeojvSu2koyuyV+rz', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(252, 2, '2026-10-22', 'income', 'bank', NULL, NULL, 51, NULL, 'ideell', 'v1:3T9bGy0xEurkFKMedrtDOAp8EKBFhGovDF3L0lVEb1OlKne9O0HNmU4=', 'v1:+Ybk3eIQD5Tpr9GihNbmERjZ/dDmD320fc5E9RDNaWBG5w==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(253, 2, '2026-10-23', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:660TlvsiKPvj2TUhKV/I56GlQtEr0nD8IVynbEt7MelEPLgAUeECle+QLtvt0FsG', 'v1:gx7OrT2CncOTk/qqZf99Y3IR/QnaVVDLKIfcVRoOMruN', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(254, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:eCrlBvWLVmzgoLspKby5X5gHSQALJogk5Pn8wBMqJjJE/NtoLDvAd3s5KccQzjcRIg==', 'v1:/SArZHu8vAljExopxgtET45F65Myh3OJEn/fVvuHCdxD', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(255, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:eFm5Qq5RoxXAYpU66D7SddyifM1iE/dFpYW6OirS47uPi8klRFyGROAA0Yhmwlef2A==', 'v1:6GBD1GYdVWHsjOPes2f4woR0jXjh3rD4vvAxFihQQfrWMg==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(256, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:m/2YawqvYm0gRbQMCn062ddO3UyAgw7ovnaPyZyny8uWrSk+uIH5RA/uTfAU5bch0mA=', 'v1:9zjbQarmF3QqApiGqNGl1viI3YeFwDkB9sAwAmBBf9pY4Q==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(257, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:SveuT1FxIchOiKfGtEkorFZKM7jbSpMgVRDmOC6xBC8UtG46/Y77Ex56/4FbBnY=', 'v1:kMPv06Xcuvbbayq3PN9fM99VtEFHkI/t9hVnGpx3K3G8', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(258, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:aZxFkOmMx8xCYc63uQwNUCKo5WuZXf2elLxWcURo1c3MfnA7G83/9TTelXgb9Vxj7g==', 'v1:/fb9HrJ8CHaVC8V8qm9jG+SHyqKbF2ATS72Vz83cxOAU', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(259, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 54, NULL, 'commercial_business', 'v1:Di5iSBZvmgGz2Y8PD/qkp4siYy5u8ZJykkMhJSlZXDhxE/G7NH/9c7iwrTJLuwjK7xqfdy/c', 'v1:RmOXKsetVrMsGSMyEylEiihEv8WnIcdU5770uflG2E1qbg==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(260, 2, '2026-10-26', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:ZOnIOSg7ZunikFQEYyYbkDKisR8JuVZuB6W/R0kJ7l5cgVkAf/arV6lCTqeKpydDWhpD', 'v1:XIhu4ic1iU0J2MQZCDY8us9zdd5teZ80itoKfdZX+d1R', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(261, 2, '2026-10-28', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:msv+Umj1lx9gOzSF0jkklUY3/wp/2bn7cIZq2X+o0xeW1bG2rKF78SnueaW8MF6908Ugjg==', 'v1:qbqzdX7ouaXkQ1eJcN5BMWi7h58MUpsl7sCDZuzjFEfD', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(262, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 52, NULL, 'ideell', 'v1:ANl/tPQEzCexqshHbJFintyLa0UVmnCvnGSdfF4s6O5/asQPoI1Q2mfH/epcoven', 'v1:MlSQ60cCXsYSB8Itr2tSWWJUFFdvAWMV4M0CATnmLEfI+Q==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(263, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:QFFA74rJ8Bx9Vyl0e8zut1NO3pgLmtwzt327bQr0wcqDdKhv1ILTrNaLLC7Yh8+JX/zf/PI=', 'v1:MN74Kth4wjtE/eQA1PZNsNysLl50LoBqVJK2HX3tGjy1', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(264, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:tSaEEZxLULTfuwssoBuocfEY3gFolIge+U2qinWTjNt09OwD2L4Gnt1W2pvtHz8TlSY=', 'v1:kKjLiBuTAxSkSgZvZCLNSxAhoG2oBaIzX00mBmH8543h', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(265, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:KU0SjFcnaqtbjNi4LhJRqPh/q6Jk37RjWIjhYoujdZQq0krLe7FZhd56FcYyxyIeSGY=', 'v1:dQGV+dzvhm94VmXUdq27H8Wi53WgD63PTwSbraLi/8VK', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(266, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:QNWKf0z3HRrhk2mm1WAADqbPqaQQakxHAqeJuhcJNS8BtrJvv/OGxSUdnEv9zTsSR+U+7j8=', 'v1:7wEfFjs8J0LwpSAAn8OkgEldR8Xa9FBgvXMEFPztDhi+', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(267, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:u6u+eTLdvbURmYdyBvFn9DPX+L7GtV5Y8lG57SxDeZcmAdw3eoguE94FnFNkFC6idBBkHecPG/baX2NC', 'v1:xxAjzCCeKqnsOlFn8ORLExezj+07VNHwl1cyXIZkQnOu', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(268, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:J+1ClKgXFbpsVh9ZomsAdHbe6rVK/Yl+AaWTgTSzxAtE+9TdwAQY2GarYP1gVcr0TDKJyhXM8atfonDFFA==', 'v1:WPrRcOMiU+C5v7xc+bS/HhKOz1cX4KRp/F9iGIHSAcFc', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(269, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:bStJzSWXOBU2gCkeYEPRKTxbrQTrKvgNH0Aw1Pz/tASyloi7uSpS1WAqUmxX5ayLICrhAE8=', 'v1:zvrIJRZmW/Qjra4j4zpCXzC3ZNMT4I3pja0n0Y8TDWRn', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(270, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:uY+8dPhbw2Stsrv3xc303g9jATT3gcQ+PKnmh6s6IGhi12l2HpG+C6ye9dRd5Es2hvIE9Q==', 'v1:qcM2AJb1TrWtKSo9upnPhJ2/lEe7xurJWtkVC9WvhZ3z', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(271, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:BsRiTjcu/4KRHb1WmF97tjaPPbGiiV21w4VEcSq9DLICp1uO66AG639OnbqJchY4LCqSq3Y=', 'v1:paRGQMDqCK4hO5U6gcnDzzG5egbXDcHNo+L3n1xeFIQ0', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(272, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:1nROkE0V6ILf6YfHfOepqCY0/5+R61FHwi3lJxq+Yum3VpJYndJcorYo8e9CBAbE7Ntn', 'v1:h+A6jdgFLYz4/vlav6MUCVTk+nrIm9sQMCZF7rArpwWf', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(273, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:K+eB9s5qy06FfwyruEjUjrSTMJLfLmy+gZIT6d/A5k40YqDhTfrmRwctf5AyZchfIzyI', 'v1:jrpiMRpacMI/h+aPkG+HX83wbNw63fnoj6D3Ri3XMKwfRg==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(274, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:DxogkWqIHNfb+kWc/qBsfQEOa1i//f+Tq6ujUuATlFzaPWMNj+wl58UUxND8s1cDs3gi+uvGP78=', 'v1:TFzOzCUWT4C35C05DDC7xPjTaK+Kg8PY/jiT6JGyrdFk', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(275, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:keGMLZneZQvZCM5eTHGo6KgFNtdtlFJCisiSQDpyAH34fey6brq5Lku01iQXDja00BLXN3Ml', 'v1:FgWRXL0vt4yul8jizRRwuriw7W3yrVApb74ehpSTig/8', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(276, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:pygzgmLzEh76tqZ7goFlIHltNFjOb0Rv4s7LTSF6tYDKyO5CFc4S6koSnodS5WGuThQCFw==', 'v1:5hSpSs2LaIs4nEXUP7cPgzOKSwKiv4yc/Wq9GgmzMVaY', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(277, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:T7lXKG2yEXH6y52GKcmIiKopOh7RuJd9eYVjHwjhW2E/SEVAYVSycL4WXcAShiOoUCX9', 'v1:j4/YP3pwhEueDwy88gCsr1ecCKQ8eoCVypyhDFjtsfgk', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(278, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:x7eBV+lMDiuJCsbvqV/rKzF5+UA6Y8m+xHnaADtniCw1K5MyAZMXJSurjnYXwsUVhsu8', 'v1:GeNtECDel2wSyY3kPSG6vlY+KkeEziPagjiVn7G3xvfSDg==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(279, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:IP/p+phsb2pR+gQrekzh0aeMijiqVkwtQWB+l+F8Z1pMs92VZnAMxHe4tOlNpawHpM9o5L+QIJou', 'v1:OSVuJ1zbQ7L7XCWHoTabuqIHyKvFYYPa7SD24/Rl12y2', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(280, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:/II1mvXJhTG6ep4GE4hEIDANEsZGcS5npIomU8Ck85ywFPW7XhTKdIlDWgP5BUk5Cw==', 'v1:8WTF8CXKd/OyERlmWRaKEuChxAu1q3vaQIKPVtW1CDBZ', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(281, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:EOARkVefEb0jkUAFFmvnycfMKmHnHZgXC9W+XOhyqohFYyGNyfji1EWBFKDkB69XY2s=', 'v1:+VKJAM8UoAnRMyOs8c2mENC4ehws0/LgNlTb51+9f53nXQ==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(282, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:RxcEo0Fls6XOazvh1CpiqQaaeoAxIjyRr3npr74pOLlCm0pxpjtoJM3iAgLUQpi6c/oeIBIRRVq9azU=', 'v1:C7MdGmIpIxCI0XScu/NMcSPeNXHhNq01NHRbGzuLPqd5', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(283, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:n6Xh8cQjQOIE0en0Awzco7tym+EzQFigNBoOHAZwYxgXd/aeKXlAUlWwvhozjAj0WLjc', 'v1:XEAjdLhmt5eTXCqgVTgdTudb50ILvO6Uqgxciux8+TC/', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(284, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:l8d9wBQJNwYcEZkZC+eQIkYbdkzOUZNuOcj4kxnTrvJ/52smeTyoXeBMNo76osWRew==', 'v1:Qgoi3LvVnklQaqqYv03c/Ox7Oi77TwKU9moru54APxcw', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(285, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:09dRW1uFcSTmkWw7sSo/cFlPXOjvNEW6AllCY0zdgq+WVB5cwq/TVWfGcbN0DBnLyS2nDw==', 'v1:rpwz8xsdiMhx9N4y6qdYNHQYy4Ojcd6YupYWPGMeVjE6', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(286, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:V5kHDUQwWSfSByimzE5syiKCNTtsBRQgmXjCeDWHuja3ymAr8ql5ryn/I5kGcP7ZwqrdGvp3FdrkyrwpZi2WqvyEDS4=', 'v1:YNGYgf/9DKmq2RoHt12BlLQDIzI4ZMLLX1Gmt48JCIos', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(287, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:+zyRW6VVJRn1Efgyy+u//zA37J2u8Z371fBEUocKzhOljpxJ4dxTMIjkvM/vV7UdjoUKUtQ0nTo=', 'v1:UOtJuPdWk2WBBI2FSmM2xKjloHW4lQDwD49ehpIiciB3yg==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(288, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:edtiXv8UqwYJrQzehTPXT73R6gIJcxxHYQTj9lIo1nxHLjqL9Fk4ZiTluHaFbKEWlvga', 'v1:MZCQ/JVefOH3EJQWtGOk1whj8sWwwU6gTz5wHLpDyHgp', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(289, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:h6hjdeh+sSeVwx3MblkJ631T+ffg+a0KjKKQVYZabZ++461GHhEn1U5lUC0a6Os1VsSojhUX6NHW', 'v1:kaqog6wkM1Eb6GxE7edOXonm4+Zf52M7mQDtV2mQKosE', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(290, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:iOuf6gvB+bSyhpdXn+3C7ZnW1hgnOFKRqTDweMfJQ0lJ24JdZrDvlskJmfidN1P9gfTnEgra', 'v1:xNNJd92wsgdFqzv3LADegJI19TrHaQm1WfKvzCqtT6Yr', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(291, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:G/UGW8rgiBUIO+WK301CIKEBxLcr7R/QD+iCFu2ZOBPi51BolJ0voo4qCQdXINJDEts=', 'v1:Ez4evup5V5kUpGGmqYqmFRs1et8ki48NpDr1+OmCwGk8', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(292, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:Z0rcjGXqtLXu5sPkaTzL9uues/POd51j3LSgNbQCnN/ABhfOvX7zeE3MdeqUQhnfASXEMPD50ls=', 'v1:u1a5qUJCxH7xt5/n9F2icNNwgfmj9QcKBz0r9E88RCQp', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(293, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:3DrDGf6l3P4EVQUEGRghGBtJzPIXqchx6HUED7ScLfShPNtMeuas8HvrFvPNsCbrbQ/kVQ==', 'v1:Zcf5kT+L8MI5IWkLDD2M0Am9QOUew3LBw6tI8KgCcaZ4', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(294, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:pNpbnjzHRtM2eTJOwLMzCfOFJ3V2CiK27Al+tDAsz9W8THHhq22xM47sGEYBx/Qy/+mTMgw=', 'v1:m8HWyFnNf1VYvDc0Ah+Kt/EA9rp4fgsgYEV54xoV3THTUw==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(295, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:gT9zO7/gOBqMRuY3t3n02WutJXp3C72w+SfoOluUTzQgZiILg9+J42wEliC1y3Wem3sVkms=', 'v1:5L3Sua8SnydKyWv1Fv4W6Ys/FpiwhPxCLRKQmViUl8cS', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(296, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:Jr2VHetzvnl7+OMF4ggAkFOfmJ/45htz5+ORauL+v9DZkwL7TYYU0iIqt9oOejtYk2S2K6jok2LgOVwZkXPLAg==', 'v1:XPYqAsCg2CIzM5ZOICO+V8l0Z7zAJo6TUlk9P8H9L8V8', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(297, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:vZyYW+Ma8k/Md6TlyNJxH5ladXJCGW0ABMgmSxa9hAHDI4/Kn6fnwY3uWWCUB04teHYZfZg5', 'v1:n1z4AQDU3jyGTUkv8CY1KrZ9dLXTirRDnTVL9isRLQr1', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(298, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:R1cF45Em4F43ExAqDyzlPFpQ0MNKKRg7RiJBSmr12YqG/Pm1MOFqgAajjl74NtTDBzE=', 'v1:ZtJBnYwUTCCF4rTFZ41KdtM0ytq59P8OpmhLZjQ8soIo', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(299, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:dDORAp8oZSlyjL0J+eTcZOHC83ceQvg9uBBg0nYd9JROgvqON8yDJzpiNr4vasietg==', 'v1:iA4bDcZdCrrnrv4nCFeQc4cRsFSGgZog+W+pRrksFzYd', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(300, 2, '2026-10-30', 'income', 'bank', NULL, NULL, 55, NULL, 'ideell', 'v1:RPuuDMmgHzhr/tXykFWvsAmiCEGx1DjOprpgWcB9EXPPR5dbPpB84LcWUcO4ijpG+HhkbmY=', 'v1:p0vLZ0fOiFoc4S2qwYp7SKOt8K1bpBJlt52IX1XDYKtV', NULL, NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(301, 2, '2026-11-02', 'expense', 'cash', NULL, NULL, 48, NULL, 'ideell', 'v1:jYZIVtoApJUI301S0WpzCsV7YS6hj8+Fb/sIf+1hgk7AbCP31hD9O/Nj67OHfJ3VjrHu1ukHJjU=', 'v1:PVCvUcmIdEW2Bs1nFtHnX5AxQ0T9xkbYjbliN214a6ks', 'v1:b6QUvF8QY058aAUSvnvAybO6DxC8JCDl3iHqa0UdEadIi6rb/shmfQ==', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(302, 2, '2026-11-05', 'expense', 'cash', NULL, NULL, 48, NULL, 'ideell', 'v1:cmjdddEXacKeQV9c3lQnJlZIgM/szdBhnpCxr595FnA50qkOoKvITcj6KdSdOONMZuoNQA==', 'v1:XiKmfAHHcr7dS1y22jX+WGlqtE2mOwGnMvACywCtzQR9PQ==', 'v1:x4kbHTotD0kia++kAjW8xvj+ZcALKGcZEs9kn2Z/4/TpgFdj5p33Cg==', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46'),
(303, 2, '2026-11-06', 'expense', 'cash', NULL, NULL, NULL, 'v1:b369lOp5NL0eXLO7j/dqjMzc2QoKT33gYxCLeFcnbB4SgzUvqzA=', 'ideell', 'v1:9nt6bY6BhjwNGJXKWQLN7pKx28yNd1toNXRPzQHbrIcb5cYhTyy8UbUj5/YgMUQ=', 'v1:5sG29schVOZ/6wBvgyRsCx9tSc3VuzccQA1eLaxb0L3eag==', NULL, NULL, '2026-11-09 21:13:28', '2026-11-11 23:44:58'),
(304, 2, '2026-11-16', 'expense', 'bank', NULL, NULL, 52, NULL, 'commercial_business', 'v1:kdfXBcV3a86KWr0iVl43aEJik+bG2mTwkdftpH1Tdh+ipiiwCYh7OHK325gRD7I=', 'v1:h9ihS4UtljDMMIWC1KwquUaln+Nz2UsHoFfqlfcs/SQ=', 'v1:6n1qYFzFy7PdG9A7Ix+gkQsbWF7vJAs4HMCBT0yzehC9a4RHa+yBfQ==', NULL, '2026-11-09 21:13:28', '2026-11-10 23:04:46');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_cash_counts`
--

CREATE TABLE `accounting_cash_counts` (
  `id` bigint UNSIGNED NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `counted_on` date NOT NULL,
  `actual_amount_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `calculated_amount_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `difference_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `note_enc` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_fingerprint` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `accounting_cash_counts`
--

INSERT INTO `accounting_cash_counts` (`id`, `fiscal_year_id`, `counted_on`, `actual_amount_enc`, `calculated_amount_enc`, `difference_enc`, `note_enc`, `created_by`, `created_at`, `booking_fingerprint`) VALUES
(1, 3, '2023-11-20', 'v1:m5QlSPPTlus3Y0kIfGoj7fn2hY7WkdEIqFZ4GjGLUQm29g==', 'v1:BWshSc3/63u5xhgHHla0NYrbTXnp63nPxnDGixL/9NAkJg==', 'v1:uwO+28pAF2QIYoW7IFq5XVqRGFwV3jjs4kpLKi0EIZQ=', 'v1:dxx5GEc4StpNB6VPCU2j09lRQtAKdWth87p7pnsNo1nUjtPXUA20iCpsRs84sVKjA5ytd5BwpQEt', NULL, '2026-11-09 20:59:23', '8500fe1e5aecbcd5431b0a0efa5f74ce3365c24aa7b670eb8b36610e03edf782'),
(2, 3, '2024-05-03', 'v1:EQOqEbeFXfLIIpCPO0HwYLm1XFrebcb69BqQ9/A+DIOX4Q==', 'v1:umvNG18YHz+yMMaoAJQERRekzyHOCxuXe7tCN0xWho4wyA==', 'v1:D9lAsfHAh0vEVTWqC9VsYxcVJTkrdF6HsQT8WMh0j+4=', 'v1:cJ6digC3REzrbIw+UA9sN3srDEJoiYFkWsVQEf4j8KDG1+HF2kMQzNQcJ6cBFM4OziAWUxd8TXAT', NULL, '2026-11-09 20:59:23', '63646934f69132da9d3a2a7eacd9b3f7be145c072c1d2b15b0e32b4cc815b6c5'),
(3, 3, '2024-07-22', 'v1:vhW1Bbg4QiC6UjKg6U/d5T+E96fNdJhYaRZKqKXC+Q6SECw=', 'v1:dvyGHAeIJCAHOKFxJuxo+VXSy2bgTE5Lt0iCWrzSDdibkIc=', 'v1:tDlvmUbDWWsiORy0ya76H2sE5410Yr4b+hVKjE1gSH8=', 'v1:mYgBUWlYeoZUIg2VyOxnBubL7HBL3qZyZYq0K59Tjw+pvC7l+Ccnji0J6LkCHNMTN8BLrjnGWjrBI64=', NULL, '2026-11-09 20:59:23', '1705b33cef5f0d284966d8b6af97d3214ac69ca9a1f89d75a1dfd87e3d0a445f'),
(4, 4, '2024-09-08', 'v1:F32nBHZVr/FVemopusTlhNKGZYAXCnv2CZm7D4BxxvWRW7M=', 'v1:9uyK28ZuMsa0FWJMUdMKplzUeNrzXZiBYP2Z0q6p4a4L7pY=', 'v1:r4AInxs/xFh46Xguy083me/bL/eseJ+ID7xr6n/bRnE=', 'v1:UUafT3GNGTGp8A489hzlWSQOUNO5O13IHrNM5TA0M11L1mlboia/uY9hpSeMPEXgqCHFs0G0NQojWuTZhnOF3N6hJitAlBIKMHy3ob6BfGPlXrZd8KeNQBMMKkFeRoEBy2JLdCI=', NULL, '2026-11-09 21:01:23', '33c0e988910673390781f9563449b1b03ba9b9b1ffef45d10dd3f7e6846d27e4'),
(5, 4, '2024-11-10', 'v1:0eKHGFDjmPMrXi1XtiUk8092FDf6btP/3JFY01xTO4R5i7w=', 'v1:fWLH2QQSWIc/mze4vzmOcIA1ChWZLpgUzXeyFhaVdw+XnM0=', 'v1:TsQIfDpmwk0wl94BUjAgzXQ2ZGv8MWv9gXoQd1agsMU=', 'v1:q/YF9wch7db18K/MzENxSU7pyClYOWuoOpPLdXkAR7urUGqWmMxMYQm2ZbtDlmh7AwQjHIY5Ng4BHiePXmYkDMgGxn7evfKRTutCERWSyvI1kkqXSn+E5DK8Q0b6rPoZwjONbw==', NULL, '2026-11-09 21:01:24', '754feb9f0c6bc55f5f226f33f1b4fcf31141f397db1a61633de8d5ff71b8d69b'),
(6, 4, '2024-11-28', 'v1:K8h6NUTjZajx5PBMrJw2IaPggLRmIzFIiY91VWBDfvkfXXw=', 'v1:gRLUdT6sqh6Q5iouZykmP6/G6q0Ssy+Qoh3/s3Pr9a4fzGY=', 'v1:KIEclBGee4N9lio9g6rFMb+cbQy18xnLrldRykrJce8=', 'v1:pP4Pog3k8AHpIgIIDtcPCUdLRdpUhag7hPM7ccbTmX6ZSA08h2LJ/TKj+lJomUsKxFc2C5wfTQJbLoPs7QI41O8LQV9Iytdi3ft7h1zgxCvdU/nBXDL/EIBMyCauZyukrSeb', NULL, '2026-11-09 21:01:24', 'f5a7d64fd54892c4422ab8cd799e009d8ab759da22f23ed99a373218d9eb4acd'),
(7, 4, '2024-12-05', 'v1:ckFQ93gPLn2PrXGJTAjrFBdijb7KMMQMzeDjZctnFyiIq2c=', 'v1:8Mq8baiPfs3ZXMHj+SAnDq/VIf7mqfbngaH91cfSb+Ii6yA=', 'v1:+2/EDhGafcVJKcMQPwFzDG+OdCKbHriJyCtss3vjfXU=', 'v1:Qkc54cUlGyyshXv4YSwEYe44zFrnlfiACvH1S9nUKXQMBtEn15gLssoShfXsmrXswoMe3CQyhGWRrdQgOC+ib+FMDbdF0nC+S426z9PiFruu6p25iCeJyjiMu7bdZA==', NULL, '2026-11-09 21:01:24', 'b03aeaa6056688ea4ca7e7f81da6edb00306e8029809bad6976c826969df0911'),
(8, 4, '2025-05-30', 'v1:PbBWWE/1cxgQx9OVocrZzTDSY/8Zhr3YuUIP1FXSOlUh45M=', 'v1:HtIrGzXxY8/eUH+bUPUDItEZYeLk3N0jlLhm3+j3qRl5SQE=', 'v1:+QEYMZZKURbkXxLErxoQ6XS0QuruV3VhXod1iTalpcw=', 'v1:I2dYdbpD4Tbc/KkOCqgk5+s+BrY9KLg+S9yOYqG/aqYSqAm2pigmF8eU2JOEy63LowTHmR4Ie2c8yX0iq/C8JAkisC9Pg3vaL+otMK/wH9iFxUSqPPCvEVp6LMwOjw==', NULL, '2026-11-09 21:01:24', '901a06dc9913c92f1b3195093f169a3d8d9bde6b25d86dc6fe7852da09a36bbc'),
(9, 1, '2025-08-29', 'v1:SQ13UJ0KuPgC9iS2Gg0rz4z6/mI1tONfLiC+/i5vsWxRRyE=', 'v1:QFItyTw1ukebkvJhQ7zbSBKQjiuDue53AtlpHioYMDrJC5o=', 'v1:fKL7RGbLvPHPx8H/C+D3MWMagkg4AfMbEcBh7eWZDAM=', 'v1:64U43TJwpyNwvLAFxZsCCMpUTdxhCnPzU0SWu63Qz3MHFTnRvQKaHTM5iiIVBsLpnbWf4LrkuBTB+OaJmV/vDiTINFp6wsdR5nm1xeEWfpXpRJG7nudStQ9H/PXvuyoxipE=', NULL, '2026-11-09 21:09:35', 'aaedf9865ba763fedebff46fc0a8ff9c6a4aeee285c9ec4cb5202c48459fdacc'),
(10, 1, '2025-09-16', 'v1:VPqsLoUbb3I6Z87s2cS+jRxqGUGgSyYGQF9t5K0ZFnfo/Xk=', 'v1:SxdgxVVXoHRald3anavLXNSQepY0cvErcaNarAUMRiY4Xfs=', 'v1:V3pUqewL/5/bSikhutSXB3aNoTUo5kF8pt36Ksm9+zM=', 'v1:X5+rb76LmbnwcyOkvJdSRs7opjI7pI8H+ps2zGLlxEKEj/pfynT8HT/CzZu9hlxO1ZawRhv3WQNLi8lsiPBj4q17eKnHVJHEiQdobfYKO6Pw4aRjVmJBUWVaLEPlsdK8+q4=', NULL, '2026-11-09 21:09:35', '85a0433dbe2d4b4b828a116e4c6390d9d2c950c5da5648112fda59bdd41fda0d'),
(11, 1, '2025-10-11', 'v1:GZKNyvtqCNsOFbV8qK0utq/KBNBfzzDgW9e65ryCGly16zY=', 'v1:4KKHuglqsCm93etPTry6ZhVpA7lzh5fOusteVADhzp+z1Fw=', 'v1:/y4i3c0UugMBd4fW+/MQ5reWFBKiZo/1x2QvQdGO1G0=', 'v1:6WJycqhRwLi6H4WmDCCzjfXpiq4Ckx6Va8x8l1JP0Sx3aa5SUJdsRfOmfR6jm8tGrZtNmJb5j4f0RJl46LQYbVKFEmRYFQ058SrgtcjmQKhMz1S7Y7D3Zlsty6W7l7ZGU/dr', NULL, '2026-11-09 21:09:35', '5f13ae1a09988505be642dba4d562ddca5414c8ee5817cea6c69f780003565bc'),
(12, 1, '2025-11-26', 'v1:RL9m5a6MEEHcXwb4IOq1NirmVItolV8LnezI+bH6KEm++B4=', 'v1:Hmre2sZisxT3Z0fFd3tm1QE2gfTBCmcGKBtfyhyCr8Ppt0Q=', 'v1:2kXkZwh+psITs76wlaUGHqDnDfIRpJeZfkg7kZHvfx4=', 'v1:nshbpeA2n6bj39UvRkzIgXgb2R0WjEXEa8cKqQPkb9nTo0CWPXl7ibHXf+7PxrrwFTN7RvzH15e8hcx7J243gf1Tre1zdVtl//TadBtYUxyRs+HtlkhoJkxJv9bDLYIE5OY=', NULL, '2026-11-09 21:09:35', '5853710a7a382a88563f65de315698cd6e47b8642bdbdfa99af7e7670642af76'),
(13, 1, '2025-11-30', 'v1:j9b0Ub+OnT3NR+weCfLEY+JeJstp5GsV7pEaZ3prIW4nqyw=', 'v1:PmyETYrREAGV/W8pBC+Gp9yXyhYX//xMMUB4mhEv3LdF9Os=', 'v1:sEkHTn3zgDpM+RpMCeWCNk28PmxOkqLxehobmlgo54o=', 'v1:ijQQwRP7/GNsKGwHUA88KJF7fbF8Edzym48rVx8Rzu9sYui8gXLf3IVi7ChJRKY8D3bC+v26lkIjKeIIJRStJQRSAkzc4ZgGmXRdQdilycry8IsnsHcEa7oyNoqtybXx/0YRUQ==', NULL, '2026-11-09 21:09:35', '29e1e422c3402ae2bcf72e4669e676b7861cbcf001c7f0d911f8bac23ac5d9d3'),
(14, 1, '2026-02-26', 'v1:k4LZdLvC6sld4aM5sig+NhoWM2GKYifOwdmsh4eIhWaRDjw=', 'v1:lR23PyPPgBWzeg4sf6SZiQ83eGzaWGAb2w7Zqo4l8DP9u90=', 'v1:ZVl9AbMASLtQdPD64C1toVx1HRXGzHg+xIEZvZAScsg=', 'v1:Swt7i7DyrZJoRShoTw5YgMqCp2gkPEaZd0F6y1T6oOYdbi/NkNOajJaT1e2blpl8jz+XDE4wNeYGhJIKxc1Rajme8jdi2isFB037RsMuISmEgc6R65oqDfHYY+YDXu/IaQ+w8g==', NULL, '2026-11-09 21:09:35', '9932ebd45ffc50304c636731111d3b8fe563ec7f56fc37e7b95023766bb65e18'),
(15, 1, '2026-06-29', 'v1:wIWhCKFghwdBdCM+pRXIpSVGszTl63f6/C/WWnXrf/pe3ng=', 'v1:b1YxSR7xwO2DnN1+Yk4nd3KXw7vP/wF4n6bPSRk+yuW2RIQ=', 'v1:cLW1y+CbHnTznW/z5pcUuabzJRb632hdjxiHphOPEIw=', 'v1:d3N5i0FNIYcoe6JY18wm/fW5Yf/4GySLWY9PBiEaru27SSmtJjJWdUkgdhovQKkAOnzPMo1mOpmOvcsQ6m/zj25dRcQ6gnAzHwwdEWolCLNXUqebjK/wWxDHn/hq8IbhaG+doA==', NULL, '2026-11-09 21:09:35', 'd2b05778146ac9648fbd0cb8e50d2b44cee8d265cda13ab43bd4bbfe523d417a'),
(16, 2, '2026-09-22', 'v1:iRULwCkwnkbtFFf6PGnYQ1uIs6X7UI+GeDmyMgXuidatSQ==', 'v1:5ozll835dZyUrLvisVLZWVM9V2i7h22xUQq6Y04JXVUFkg==', 'v1:sx8g6fLSUPDei4kcK4Dp0rxjPHQ11BGoVvAq4rRYeqU=', 'v1:4bWU0sDRBBVymlJ+DbPJFZd3Ooj/qi1CO7tiJEvp7LUF7uveAFpy3QOC+pdiXzt7ID23tQWMPouYaDGDrQbWdyXEmFn/qdyx6rt/P/ZD0CJ4fmPb6oo6OL3Hf0f5', NULL, '2026-11-09 21:13:28', '3831ec10e6a1b5724586d16c8271b42c09e388bbefbcc4155d5a51ff02b66591'),
(17, 2, '2026-11-06', 'v1:1vZxg2sUTkSQJVrM02/X2jj4yumKOadh9mpvE/0pF+oo5+A=', 'v1:7WGCrMDtz40EAlOE4iQrwMEEjvGXA4VhWJsrdTVGbH6m+tQ=', 'v1:GClVGCnYuIElf+0WaQktSaHI6yCSJ8lU/Chef/Z9uCw=', 'v1:D8/YPlXJRQXVN+A5J/3CznZEdHBkxPiiCnF1pX1vKaC7QFjMxUYRC8UTKuQh7R8HymD8uvQlnewjSuycsDwCrh0lmMDi9rGemouEozIeF5ubR8H9yhys7mh3tpWBHYTM', NULL, '2026-11-09 21:13:28', 'c4718d3c1f1aeae78e5714fbd484cc0c3105e523b06ce88d01f31c0f4a22289e'),
(18, 2, '2026-11-06', 'v1:OWhBW3B3W+lHgaQRN23o87xPzyHJMHqv/Wth69+FeR/+3Q==', 'v1:BB6jJt0/OrId8grx6ZDuXUbLYkS9FDmILRNhId7adRz01vk=', 'v1:cOc+DEcIzvecSnvTZmncdGrI6cjn8DstCubeLonIuXdx9fk=', NULL, 3, '2026-11-11 23:37:13', 'c4718d3c1f1aeae78e5714fbd484cc0c3105e523b06ce88d01f31c0f4a22289e'),
(19, 2, '2026-11-06', 'v1:cjYY5KT0hAC78KlMB7ci4QzhWzqu+xhMPU+51QntxgBVYg==', 'v1:f5B0eQEVvnulsDk/V7ERUUcV8nPz12Ne6r7seWkbzpNVcw==', 'v1:TcbvSGmcTgHJWxSsXPEQu+RfkqGPSNNknO29sHcKiO0=', NULL, 3, '2026-11-11 23:45:29', '492a20f7a1cabaca7fc0af893860bc54541176c00ed5ec7da14c6498c843fd4d');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_categories`
--

CREATE TABLE `accounting_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `accounting_categories`
--

INSERT INTO `accounting_categories` (`id`, `name_enc`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'v1:1nzIpkTA/bN2sczdp8fS6Zo+9hXZzyEtRb3ORLW9uwFH0+8D46Z6s7YoTNjkSA==', 1, 1, '2026-11-09 20:59:23'),
(2, 'v1:0crH4X8Vw+FGClEDi7FhK4MfgcAwtBEFvSFKn51I2wpBK0HweJmSbK0j459lick1Duazxvho7wqH', 1, 2, '2026-11-09 20:59:23'),
(3, 'v1:5LRiHi3FwX2V6CucXwQaA0MFc7zYLpX8vLhwGABkUUfBU+4=', 1, 3, '2026-11-09 20:59:23'),
(4, 'v1:ecA0Z40HuVpQudP8h7vsOOjQEx3Eoh5vpy3CV1sxJx/PHy9HCPK4DqT+m9My', 1, 4, '2026-11-09 20:59:23'),
(5, 'v1:VK6ihhVcKI0A6dC/zvcpyYF3HBxN7Ofsm9BJ0tVeJOks381zVoiDUgeFGrG1dA==', 1, 5, '2026-11-09 20:59:23'),
(6, 'v1:EarKDdfoRL7wj0XPXxF8NYOzHhIhtBK3h6WLfzcLdCTtqolChLkEKDB7tqVepdnmPycMYbsVSSFG8bUHJy6FHvI=', 1, 6, '2026-11-09 20:59:23'),
(7, 'v1:c/F8MUS6aPy1Rx8m+8qDsujra5QpCVPf49KjMI1sv4GvnpnR37o=', 1, 7, '2026-11-09 20:59:23'),
(8, 'v1:q1auy5OdNh2+4x3V/OAKiyUR3A7rA8p+5igUSzMYRgzcDtURRa+yJYKitj3rE8kDmbmfWDZNQseRK7tTJg==', 1, 8, '2026-11-09 20:59:23'),
(9, 'v1:6bayIBJp24mZjRZz47NZLty3QoAa+6txmkFob2MSoEATorEXCSQ/E8ri7IUDsEbf7g==', 1, 9, '2026-11-09 20:59:23'),
(10, 'v1:gQFMYXMGC0DiqkwyKwvvzZox7IMkIaP6E3ssbyw4A2AtJ4Gkoq2df1/xfH8Xxg==', 1, 10, '2026-11-09 20:59:23'),
(11, 'v1:akBvYx7VZDwn+l/KAEkVJsplBJJrkGBjAi72ICgw4ej2nQM=', 1, 11, '2026-11-09 20:59:23'),
(12, 'v1:C7SA2+J9dtdSbUonBfNtBjbnvZK/9lnIdMwHlU5rZVEbAtSB45SC+0PSL7RZ7zpD', 1, 12, '2026-11-09 20:59:23'),
(13, 'v1:HqHZ/+IW671q2ONq19yu2WHokQJD0a6di7d8DcevunXMI5RQZE7RAzdSmlD3BLpY9L8dGQ==', 1, 13, '2026-11-09 20:59:23'),
(14, 'v1:7MxWJWOTWePBN4ujBqCzciAgH8Jmc5B/58x8ZR3jR08rcr0AyBfWApbnceEAGQ==', 1, 1, '2026-11-09 21:01:23'),
(15, 'v1:j0RwtUUORPOZMblzkP0oC7i7y95AjxMpTcQV7kVadrmRJUh9y1wYZdHd6RfTENzRjSJT4EJpbrAeBr9Qicy+', 1, 2, '2026-11-09 21:01:23'),
(16, 'v1:34NfLjQhhZ9Fm3gfvuya9tk135C8voppdX1xcNkeY5KrANHoBdDMz7bzWZYl6Pszbel/g3FFwAT1PYG+RJZ1FxQ=', 1, 3, '2026-11-09 21:01:23'),
(17, 'v1:PNjNjPakWnkRXpm8ouZoSFUDObQG5/jRPJuQI0I1/HZ+97bCEzz10Gipah3FVqECnGBkIYn0ZUgvsTQQJJQqzdE=', 1, 4, '2026-11-09 21:01:23'),
(18, 'v1:lOz4dr+40t63yNf0/rRmoa4Lhf7+kgY7QyS2kz4LPpctT4ZAS3S7VqrRohhY', 1, 5, '2026-11-09 21:01:23'),
(19, 'v1:J9VNGUrXrjAzQmyi8bY9MXs9M+iOwd0WQB6/DuEI55Puc//iG7PA5ag46A34t4Tsgo9ybvs7RtE7vg==', 1, 6, '2026-11-09 21:01:23'),
(20, 'v1:HZ9Rm/n2xzkluJFaVk2hQtjz/3gpKHAf645zkNNND6L6+MHlK4k=', 1, 7, '2026-11-09 21:01:23'),
(21, 'v1:LLEmkZT0g6iF4FSnoFUTRjUbXtXTHfdcBsi4bVKp8rQuAErCt4ZRJ5rvzc0H0Q==', 1, 8, '2026-11-09 21:01:23'),
(22, 'v1:yqT0QdmkD0TCp7yZZ6GM6aHbL/HCqT0oX8VxK69uyQH6wnvbA0TfgsDK5F3qnF9G67C6p6xptl94', 1, 9, '2026-11-09 21:01:23'),
(23, 'v1:GB20SuA8E5i3yyNCJCdhkZqQAjeKbsigJkk7SLrRFJJxZkdFF4uIYAeL6A==', 1, 10, '2026-11-09 21:01:23'),
(24, 'v1:SH+q2hqAPnR5EnGgFEj4S7wbZMbGQoLsC2o0db+LjsinA18=', 1, 11, '2026-11-09 21:01:23'),
(25, 'v1:GeuyvaEQl6lxgYs/LaAZXg+FcRU7MzGXHtsG9GOEltdszm8=', 1, 12, '2026-11-09 21:01:23'),
(26, 'v1:kbQagmhw1QeMMe7cl549DdW7Qt2GjKfT0GDcz/sIRxR2rD0u5HuJ8AUg3CAXcfTWTn9wjz+A8XUgJtGUJw==', 1, 13, '2026-11-09 21:01:23'),
(27, 'v1:+ilgS6NvBnNOAa8+uMn7CSuzvc3FsGJ1lX54TMgtp9FcrL5VI+cwsgprNS/MAbLZCStzqh2ZrOg=', 1, 14, '2026-11-09 21:01:23'),
(28, 'v1:ODONVNz82lPPNR7mbmz0i+NSRK3CybS1YYkxsaRe5lIvLR0xzZDn1wZxN0iC1V7AGhI=', 1, 15, '2026-11-09 21:01:23'),
(29, 'v1:yry8rb1xe3Vr/o5qwa1QVtjDytP67MIQ7LPx2Bkv2dcLqUosmur7yFwvcXY=', 1, 16, '2026-11-09 21:01:23'),
(30, 'v1:MhVLFbZH4rrEsk6jB6i9cK+aIUiVTXgf+T1mN3raRVs2SEsw4QJdVUuDN9saccTIVw==', 1, 17, '2026-11-09 21:01:23'),
(31, 'v1:E409ND3V1yNrIXf+K6+nH0PDf1DGH/PEfScqFgLJ3T6XjJ8eWtI=', 1, 1, '2026-11-09 21:09:35'),
(32, 'v1:4yxhk1TkXcM+WpoGK8tftAIqL7mf511lsvFqGu60Koa60jg=', 1, 2, '2026-11-09 21:09:35'),
(33, 'v1:eCZ78cKgs8WpQbBTIggB3TvPB7RUzsin9yFZp0yNvZi0G2zUzQSUsqM=', 1, 3, '2026-11-09 21:09:35'),
(34, 'v1:PNvSRo02jBH/6y2DNLoIrLnAM2M+7u1Xx5Vlloyx6sUaX1B+SSM=', 1, 4, '2026-11-09 21:09:35'),
(35, 'v1:oEF977pYTEDEPOxpf1G+BILq4l1FnM9+hvDgYhVA8DL2LJDa', 1, 5, '2026-11-09 21:09:35'),
(36, 'v1:6irPlnBx1+bsBQniBDrTo7DaKiGMEs99rodBAOraQwe2+t/OQIuUmNDXqJ50', 1, 6, '2026-11-09 21:09:35'),
(37, 'v1:xjzTXWq0NOCFWGGavLwxeG41Hvx9UwFGxXNgCOUUqW/X0Nbzlusp3ZwgYJMNYDSvqiusogh8YJlC', 1, 7, '2026-11-09 21:09:35'),
(38, 'v1:FoP2MJ8X5ftI7oVw6GIVEr1/8cGwugYW8UVacNmh6PvLHhFK2OLjlsjbhKv7vg==', 1, 8, '2026-11-09 21:09:35'),
(39, 'v1:yp/Sx9eO31tH9tyIeP1mPthQd8ba3dQ5p7kW2Hoe1Hfra+A=', 1, 9, '2026-11-09 21:09:35'),
(40, 'v1:UnTRe59RKqsy4JGUrLKWKW5OJnf4ULL6rgQsLG5INAdwIGQGOtj0rBoGYAQjNVI=', 1, 10, '2026-11-09 21:09:35'),
(41, 'v1:SEV527XzdBDEgpKPpEfWmuvUouEjQLizJjT2brjjBn5Pt9+SATDRqKaSuuYkR54=', 1, 11, '2026-11-09 21:09:35'),
(42, 'v1:YKrgKTXAC2XWg193Il65YIooMqK1WxOle/t2b88iTxx5QKawAopXLW0ZoQVHV3voc3Yzc/Ae87nsrTV1HKksvsw=', 1, 12, '2026-11-09 21:09:35'),
(43, 'v1:SwJHbFumDupzDnGHnzCB0MGYEjGzb35uP0Fej60T/TFjK7b9F6p6U7bLx4Y3TPRPmNiodaRoxqO2oyU=', 1, 13, '2026-11-09 21:09:35'),
(44, 'v1:mk6e51WrWEMeb35Y/dw0Lv4X7ABnrlKCAq0eJYIuA9nJGpWa7xJ5Vl2e', 1, 14, '2026-11-09 21:09:35'),
(45, 'v1:yJbjhfVRnQcD5IfR0x+O3sItO06TrVeX99d2hl3+Uxf63E9XpXd152Nbkjc=', 1, 15, '2026-11-09 21:09:35'),
(46, 'v1:wrfqME7StH9RQOjT1xuUsV81JbdEB4YPeBr8k4hJYO9sOBSrCavA7QHsDFn0o2o=', 1, 16, '2026-11-09 21:09:35'),
(47, 'v1:s+Bb0Nh2Ti/X8i6myBt5PG8kZNfps7WdnW5x1kwat7Gs1XTrK5gCNrLAwyw9Yh5BwPHHsxXa/g==', 1, 17, '2026-11-09 21:09:35'),
(48, 'v1:S/9VaptKrqABblC5ScMiRpprjXYXodzfwk0yLt8EFbfhruLOxJp3HPGRoA2FrCUqRiuVZ4hg3bAIZbpYaSam6bw=', 1, 1, '2026-11-09 21:13:28'),
(49, 'v1:qyqN4vFlTcEw+pm2ARqKto3HEPR8K1dedtsJ5SP+UUg2J4s3L/w=', 1, 2, '2026-11-09 21:13:28'),
(50, 'v1:3sOGFNUYQWmYXnwlzqED3RJ8VjmhqgTzg0Jt9tO7Q/HVESfncBOXt3jXCgc2', 1, 3, '2026-11-09 21:13:28'),
(51, 'v1:cmgHjlFDV1kmXeE9cYRRuyTRWjM77hJifZNJebYkg0SUZA==', 1, 4, '2026-11-09 21:13:28'),
(52, 'v1:ARdqSSJhevizahW2kJw5MPirSqjqndTI02i+x9mmap/QJjBmR3rathpdi3hHm/YR9gjkVg==', 1, 5, '2026-11-09 21:13:28'),
(53, 'v1:F2jW09xaU1FxgWXDvEtKK1XBA4mvV/jYA+RxTJrjiWNE56cdHpADElrk5Yveo8s=', 1, 6, '2026-11-09 21:13:28'),
(54, 'v1:Y1kb3TUP5dt4M3jmclEBGYMlQe6iO7cRm/Ylqp5lQ50OQ0AoI5k=', 1, 7, '2026-11-09 21:13:28'),
(55, 'v1:+LImVyiZT6qF04Gprs0hVyElyglmrs2OdSqdwNtXBgJGRLJjZ1GS2CKObxhz6w==', 1, 8, '2026-11-09 21:13:28');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_documents`
--

CREATE TABLE `accounting_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `reference_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relative_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_fiscal_years`
--

CREATE TABLE `accounting_fiscal_years` (
  `id` bigint UNSIGNED NOT NULL,
  `label` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `opening_bank_enc` text COLLATE utf8mb4_unicode_ci,
  `opening_cash_enc` text COLLATE utf8mb4_unicode_ci,
  `closing_bank_enc` text COLLATE utf8mb4_unicode_ci,
  `closing_cash_enc` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` datetime DEFAULT NULL,
  `closed_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `accounting_fiscal_years`
--

INSERT INTO `accounting_fiscal_years` (`id`, `label`, `start_date`, `end_date`, `status`, `opening_bank_enc`, `opening_cash_enc`, `closing_bank_enc`, `closing_cash_enc`, `created_at`, `closed_at`, `closed_by`) VALUES
(1, '25-26', '2025-08-01', '2026-07-31', 'open', 'v1:KHMBiYTpwEkbHMsbQ+/zgbeJx3BYSnok+Vl4lCp9TuuT4/Q=', 'v1:eg04sNcdA6LWfCgNCVb17o5rpXmyhbWsbQFW/XhUDhPP9Io=', 'v1:HKlHq14sUQEmtn5e4kgrehdoNkSL4mguWVlJ8UVOHuIn7kY=', 'v1:a8yRY/+FtlSmmyMsnhCwOODR3UuFbz8yRi9i7UP7pOYnnQ4=', '2026-11-09 17:00:33', NULL, NULL),
(2, '26-27', '2026-08-01', '2027-07-31', 'open', 'v1:d86pemZibaNS51pd/z01olsSEmjh/pqT1dw+SPCmBIaNNfY=', 'v1:sELmnBAVp2/RLgAa9MmzAPnRDOJNmuqif+256mO1fpLtuF8=', 'v1:kQtiCM4Tqc+I6SpTk94N6J9SxOLM9RFJnY9oXWSxdCeqqJXw', 'v1:wmjznqvXKFasMitrAZaW3ELWMLOZxKL6kBBF4Mom9PVsjg==', '2026-11-09 17:00:33', NULL, NULL),
(3, '23-24', '2023-08-01', '2024-07-31', 'open', 'v1:KLYu4egaNnNSjtIH+OEriOMIPmPWDS8kY9nRyOj8ixY9+DE=', 'v1:tzGyNDxwLvSUTKKbJi5brxrSFw6JNO33R8fs+PfWmQ5B/PM=', 'v1:yfDcgRnwX4FiIaFlo4Btq+3CwqJYYfoQDlBwxyzp8UgVT7s=', 'v1:zckoGstYahTyJkQbbQ/NtZsm7n1Q55wj9AF8kHMaMxgamGo=', '2026-11-09 20:59:23', NULL, NULL),
(4, '24-25', '2024-08-01', '2025-07-31', 'open', 'v1:WjPt7SS417SBKzyXZCX36RAnjVYapwagDPGtGf/Abtq1xh8=', 'v1:WW0a40aqKGfPHUeLaUUjWkvgcaHvtqFRDBnkEd1klfD1U1Q=', 'v1:+7YT442MdO/ncFq9VRILhXka7vIp4dTDIDWuhkGxAEpDZWY=', 'v1:Zgq24CUYTzEkWbOIdZJ1VYMOXcq7Dq7b8L4IfXesD1pLfMQ=', '2026-11-09 21:01:23', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounting_system_settings`
--

CREATE TABLE `accounting_system_settings` (
  `id` tinyint UNSIGNED NOT NULL,
  `mode` enum('migration','regular') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'migration',
  `regular_activated_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `accounting_system_settings`
--

INSERT INTO `accounting_system_settings` (`id`, `mode`, `regular_activated_at`, `updated_at`) VALUES
(1, 'migration', NULL, '2026-11-12 08:48:14');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `primary_key` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `anrede` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `webseite` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailadresse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `formular_link` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link zum Online-Formular (nur für Typ Formular relevant)',
  `anschrift` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Anschrift für persönliche Kontakte',
  `status` enum('Antwort abwarten','Spende zugesagt','Spende abgesagt','Handlungsbedarf') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Antwort abwarten',
  `zuletzt_angeschrieben` date DEFAULT NULL,
  `kommentar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `letztes_ergebnis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `typ` enum('Mail','Formular','Persönlich') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Art des Kontakts: Mail, Formular oder Persönlich',
  `is_suggestion` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `contacts`
--

INSERT INTO `contacts` (`id`, `primary_key`, `name`, `anrede`, `webseite`, `emailadresse`, `formular_link`, `anschrift`, `status`, `zuletzt_angeschrieben`, `kommentar`, `letztes_ergebnis`, `created_at`, `updated_at`, `typ`, `is_suggestion`) VALUES
(1, 1, 'Adidas', 'liebes Team von Adidas', '', 'ChangingLivesthroughSport@adidas.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 07:39:28', 'Mail', 0),
(2, 2, 'airberlingroup', 'liebes Team von Airberlin', '', 'service-center@airberlin.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:08:58', 'Mail', 0),
(3, 3, 'Alltours', 'liebes Team von Alltours', '', 'info@alltours.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-28 12:23:58', 'Mail', 0),
(4, 4, 'Alpenpark Neuss', 'liebes Team vom Alpenpark Neuss', '', 'info@alpenpark-neuss.de', '', '', 'Antwort abwarten', '2026-09-25', '', 'Zu viele Anfragen / generelle absage', '2025-10-07 09:57:35', '2026-09-25 08:09:31', 'Mail', 0),
(5, 5, 'Alpincenter Bottrop', 'liebes Team vom Alpincenter Bottrop', '', 'info@alpincenter.com', '', '', 'Spende zugesagt', '2026-09-25', '', '2026: 2 All-inklusive Feierabend Gutscheine', '2025-10-07 09:57:35', '2026-10-18 18:20:03', 'Mail', 0),
(6, 6, 'AOK', 'liebes Team der AOK', '', 'du.gs_duisburg@rh.aok.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:10:20', 'Mail', 0),
(7, 7, 'AQUALAND Freizeitbad', 'liebes Team von AQUALAND', '', 'info@aqualand.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:10:36', 'Mail', 0),
(8, 8, 'Aquapark Oberhausen', 'liebes Team von Aquapark', '', 'info@aquapark-oberhausen.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:10:52', 'Mail', 0),
(9, 10, 'Bäckerei Schollin', 'liebes Team von Schollin', '', 'baeckerei@schollin.de', '', '', 'Spende zugesagt', '2026-09-25', '', '3 Schollin-Brotdosen und Becher', '2025-10-07 09:57:35', '2026-10-02 07:03:53', 'Mail', 0),
(10, 11, 'Bahlsen', 'liebes Team von Bahlsen', '', 'info@bahlsen.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:11:41', 'Mail', 0),
(11, 12, 'Barma', 'liebes Team von Barma', '', 'service@barmer.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:11:57', 'Mail', 0),
(12, 13, 'bASE c/o E-Plus Service GmbH & Co. KG', 'liebes Team von bASE', '', 'kundenservice@eplus.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:12:13', 'Mail', 0),
(13, 14, 'Brandt Zwieback', 'liebes Team von Brandt', '', 'marketing@brandt-gmbh.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', 'regionale und karitative Verbände', 'Absage', '2025-10-07 09:57:35', '2026-09-25 08:12:46', 'Mail', 0),
(14, 15, 'Braun', 'liebes Team von Braun', '', 'post@braun.fashion', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:13:02', 'Mail', 0),
(15, 16, 'Brio', 'liebes Team von Brio', '', 'kontakt@brio.de', '', '', 'Spende abgesagt', '2026-09-25', 'Mail an Brio - Ravensburger antwortet mit genereller Absage', '2026: Generelle Absage', '2025-10-07 09:57:35', '2026-09-29 10:05:32', 'Mail', 0),
(16, 17, 'BVB', 'liebes Team vom BVB', '', 'stiftung@bvb.de', '', '', 'Spende zugesagt', '2026-09-25', '', '2026: Paket mit Fanartikeln | 2025: Kissen, Schal, etc', '2025-10-07 09:57:35', '2026-09-28 09:37:03', 'Mail', 0),
(17, 18, 'Chupachups', 'liebes Team von Chupachups', '', 'info@cfp-brands.de', '', '', 'Spende abgesagt', '2026-09-25', '', '2025: generelle Absage ', '2025-10-07 09:57:35', '2026-09-25 12:08:01', 'Mail', 0),
(18, 19, 'DER Touristik', 'liebes Team von DER', '', 'info@dertour.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:15:52', 'Mail', 0),
(19, 20, 'Deutsche Bahn', 'liebes Team der Deutschen Bahn', '', 'fahrkartenservice@deutschebahn.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:16:08', 'Mail', 0),
(20, 21, 'Deutsche Sinalco GmbH', 'liebes Team von Sinalco', '', 'annika.kosubek@sinalco.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:16:25', 'Mail', 0),
(21, 22, 'EMBRICANA', 'liebes Team von EMBRICANA', '', 'info@embricana.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:17:13', 'Mail', 0),
(22, 23, 'Extrablatt Moers', 'liebes Team vom Extrablatt', '', 'moers@cafe-extrablatt.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:17:47', 'Mail', 0),
(23, 24, 'Feuerwehr Duisburg', 'liebes Team der Feuerwehr Duisburg', '', 'info@feuerwehr.duisburg.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:18:42', 'Mail', 0),
(24, 25, 'Freizeitpark Schloß Beck GmbH', 'liebes Team von Schloss Beck', '', 'info@schloss-beck.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:18:58', 'Mail', 0),
(25, 26, 'FTI Touristik', 'liebes Team von FTI Touristik', '', 'fti-gutscheine@fti.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:19:24', 'Mail', 0),
(26, 27, 'Funny Frisch', 'liebes Team von Funny', '', 'funny@funny-frisch.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', 'Generelle Absage. Anfrage lohnt vermutlich nicht.', 'Absage', '2025-10-07 09:57:35', '2026-09-28 07:17:15', 'Mail', 0),
(27, 28, 'Fussball-Club (FC) Rumeln 1955 e.V.', 'liebes Team vom Rumelner Fussball-Club', '', 'info@fcr1955.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:19:56', 'Mail', 0),
(28, 29, 'Gartencenter Schlößer GbR', 'liebes Team von Schlößer', '', 'info@gartencenter-schloesser.de', '', '', 'Antwort abwarten', '2026-09-23', '', '2025: 3 Einkaufsgutscheine', '2025-10-07 09:57:35', '2026-09-23 18:04:47', 'Mail', 0),
(29, 30, 'Gebag Duisburg', 'liebes Team der Gebag Duisburg', '', 'info@gebag.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Kontingent erschöpft', '2025-10-07 09:57:35', '2026-09-25 12:21:28', 'Mail', 0),
(30, 31, 'geobra Brandstätter (Playmobil)', 'liebes Team von Playmobil', 'https://anfragen.playmobil.com/de', 'service@playmobil.de', 'https://anfragen.playmobil.com/de', '', 'Spende abgesagt', '2025-09-30', 'Nur alle drei Jahre anfragen!', '2024: Großes Paket mit Spielesets. ', '2025-10-07 09:57:35', '2026-10-05 10:59:59', 'Formular', 0),
(31, 32, 'Grafschafter Krautfabrik J. Schmitz KG', 'liebes Team von Grafschafter', '', 'kirchartz@grafschafter.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 12:09:08', 'Mail', 0),
(32, 33, 'GreenStories.de', 'liebes Team vom GreenStories.de', '', 'kundenservice@greenstories.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:20:46', 'Mail', 0),
(33, 34, 'Hanse Merkur', 'liebes Team von Hansemerkur', '', 'info@hansemerkur.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:21:51', 'Mail', 0),
(34, 35, 'Hellmich', 'liebes Team von Hellmich', '', 'sekretariat@hellmich-gruppe.de', '', '', 'Antwort abwarten', '2026-09-25', 'Budget für dieses Jahr erschöpft', '', '2025-10-07 09:57:35', '2026-09-25 08:22:57', 'Mail', 0),
(35, 36, 'Henkel', 'liebes Team von Henkel', '', 'corporate.communications@henkel.com', '', '', 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-10-18 18:12:56', 'Mail', 0),
(36, 37, 'Hövelmann/Rheinfelsquellen', 'liebes Team von Rheinfelsquellen', '', 'info@rheinfelsquellen.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-28 12:23:34', 'Mail', 0),
(37, 38, 'http://www.phantasialand.de', 'liebes Team vom Phantasialand', '', 'info@phantasialand.de', '', '', 'Spende zugesagt', '2026-09-23', 'Wird zugeschickt', '2026: 4 Freikarten | 2025: Vier Freikarten', '2025-10-07 09:57:35', '2026-09-25 12:12:32', 'Mail', 0),
(38, 39, 'indoor-soccer-center', 'liebes Team vom Indoor-Soccer-Center', '', 'info@indoor-soccer-center.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:24:02', 'Mail', 0),
(39, 40, 'Jako-O', 'liebes Team vom Jako-O', '', 'kontakt@jako-o.com', '', '', 'Antwort abwarten', '2026-09-25', '', '2025: 4 Brotdosen, 2 Handtücher, 4 Becher', '2025-10-07 09:57:35', '2026-09-25 08:24:19', 'Mail', 0),
(40, 41, 'Jever Skihalle Neuss', 'liebes Team der Skihalle Neuss', '', 'kontakt@allrounder.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:24:35', 'Mail', 0),
(41, 42, 'Kalisto', 'liebes Team vom Kalisto-Tierpark', '', 'info@kalisto-tierpark.de', '', '', 'Antwort abwarten', '2026-09-25', '', '6 Freikarten.', '2025-10-07 09:57:35', '2026-09-25 08:24:52', 'Mail', 0),
(42, 43, 'Kampfkunstcentrum Duisburg', 'liebes Team vom Kampfkunstcentrum Duisburg', '', 'Kampfkunstcentrum.duisburg@web.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:25:08', 'Mail', 0),
(43, 44, 'Kiddywunder', 'liebes Team von Kiddywunder', '', 'info@kiddywunder.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:25:24', 'Mail', 0),
(44, 45, 'Kinderbuch.eu', 'liebes Team vom Kinderbuch.eu', '', 'info@kinderbuch.eu', NULL, NULL, 'Spende abgesagt', '2026-09-23', '', '', '2025-10-07 09:57:35', '2026-09-24 06:47:45', 'Mail', 0),
(45, 46, 'Kinkartz', 'liebes Team von Kinkartz', '', 'aachenerprinten@email.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:25:40', 'Mail', 0),
(46, 47, 'Komma Theater', 'liebes Team vom Komma Theater', '', 'info@kommatheater.de', '', '', 'Spende zugesagt', '2026-09-25', '', '4 Freikarten', '2025-10-07 09:57:35', '2026-10-02 07:04:21', 'Mail', 0),
(47, 48, 'Krups', 'liebes Team von Krups', '', 'info@krups-online.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:26:13', 'Mail', 0),
(48, 49, 'Lego Discovery Centre Oberhausen', 'liebes Team vom Legoland', '', 'oberhausen@legolanddiscoverycentre.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:27:02', 'Mail', 0),
(49, 50, 'Mallewupp', 'liebes Team von Mallewupp', '', 'info@mallewupp.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:27:51', 'Mail', 0),
(50, 51, 'Malzers Backstube', 'liebes Team von Malzers', '', 'marketing@malzers.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:28:07', 'Mail', 0),
(51, 53, 'Mattel GmbH', 'liebes Team von Mattel', '', 'info.de@mattel.com', '', '', 'Antwort abwarten', '2026-09-25', 'service.de@mattel.com gibt es nicht', '', '2025-10-07 09:57:35', '2026-09-25 08:56:34', 'Mail', 0),
(52, 54, 'Medion', 'liebes Team von Medion', '', 'unternehmen@medion.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:28:40', 'Mail', 0),
(53, 55, 'Monkey Town Duisburg', 'liebes Team vom Monkey Town Duisburg', '', 'kontakt@monkeytown.eu', '', '', 'Spende abgesagt', '2026-09-25', '', '2025: Gutscheine', '2025-10-07 09:57:35', '2026-09-25 08:50:59', 'Mail', 0),
(54, 56, 'MSC Kreuzfahrt', 'liebes Team von MSC', '', 'kundenbetreuung@msccruises.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:30:02', 'Mail', 0),
(55, 57, 'MSV Duisburg', 'liebes Team vom MSV Duisburg', '', 'info@msv-duisburg.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:30:18', 'Mail', 0),
(56, 58, 'Neanderbad', 'liebes Team vom Neanderbad', '', 'office@neanderbad.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:30:51', 'Mail', 0),
(57, 59, 'Neoliet', 'liebes Team vom Neoliet', '', 'info@neoliet.de', '', '', 'Antwort abwarten', '2026-09-25', '', 'muelheim@neoliet.de', '2025-10-07 09:57:35', '2026-09-25 08:31:07', 'Mail', 0),
(58, 60, 'Niederrhein-Gold, Frau Anke Jansen', 'liebes Team von Niederrhein-Gold', '', 'info@niederrhein-gold.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:31:23', 'Mail', 0),
(59, 61, 'Oetinger Verlag', 'liebes Team von Oetinger', '', 'oetinger@verlagsgruppe-oetinger.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:31:56', 'Mail', 0),
(61, 63, 'Parfümerie Douglas', 'liebes Team von Douglas', '', 'service@douglas.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:32:29', 'Mail', 0),
(62, 64, 'Parfümerie Pieper', 'liebes Team von Pieper', '', 'kontakt@beauty-alliance.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:32:45', 'Mail', 0),
(63, 65, 'Pelikan', 'liebes Team von Pelikan', '', 'info@pelikan.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:33:02', 'Mail', 0),
(64, 66, 'PIPPOLINO Duisburg', 'liebes Team von PIPPOLINO', '', 'info@pippolino.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:33:39', 'Mail', 0),
(65, 67, 'Radio Duisburg', 'liebes Team von Radio Duisburg', '', 'info@radioduisburg.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:34:51', 'Mail', 0),
(66, 68, 'Roskothen (Spielzeugladen in DU / Sonnenwall)', 'liebes Team von Roskothen', '', 'info@diekunstzuspielen.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:35:25', 'Mail', 0),
(67, 69, 'RTL', 'liebes Team von RTL', '', 'info@rtl.de', NULL, NULL, 'Spende abgesagt', '2026-09-23', '', 'Kontingent erschöpft', '2025-10-07 09:57:35', '2026-09-24 07:34:16', 'Mail', 0),
(68, 70, 'Schauinsland Reisen', 'liebes Team von Schauinsland', '', 'slr@schauinsland-reisen.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:35:58', 'Mail', 0),
(69, 72, 'Schwimmbad Toeppersee', 'liebes Team vom Schwimmbad Toeppersee', '', 's.gomez@duisburgsport.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:36:49', 'Mail', 0),
(70, 73, 'Schwimmclub Bayer Uerdingen', 'liebes Team vom Schwimmclub Uerdingen', '', 'info@usv-08.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:37:05', 'Mail', 0),
(71, 74, 'Sea life Oberhausen', 'liebes Team vom Sea life Oberhn life Oberhausen', '', 'oberhausen@sealife.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:37:22', 'Mail', 0),
(72, 75, 'Serengeti-Park', 'liebes Team vom Serengeti-Park', '', 'info@serengeti-park.de', NULL, NULL, 'Antwort abwarten', '2026-09-23', '', 'Kontingent erschöpft', '2025-10-07 09:57:35', '2026-09-23 18:28:51', 'Mail', 0),
(73, 76, 'Severin', 'liebes Team von Severin', '', 'information@severin.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:37:38', 'Mail', 0),
(74, 77, 'Signal Iduna', 'liebes Team von Signal', '', 'info@signal-iduna.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:38:01', 'Mail', 0),
(75, 78, 'Simba', 'liebes Team von Simba', '', 'info@simba-dickie.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', 'Formular: https://service.simba-dickie.com/de/home/simba/', '', '2025-10-07 09:57:35', '2026-09-25 08:38:17', 'Mail', 0),
(76, 79, 'Spiel Dschungel GmbH', 'liebes Team vom Spiel Dschungel', '', 'info@Spiel-Dschungel.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:38:51', 'Mail', 0),
(77, 80, 'Sportfreunde Walsum 09', 'liebes Team vom Sportfreunde Wn Walsum', '', 'mathias.quint@walsum09-fussball.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:39:07', 'Mail', 0),
(78, 81, 'STEAG', 'liebes Team von Steag', '', 'info@steag.com', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-29 06:29:09', 'Mail', 0),
(79, 82, 'Steiff', 'liebes Team von Steiff', '', 'de.service@steiff.com', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 12:10:13', 'Mail', 0),
(80, 83, 'Steinbau GmbH', 'liebes Team von Steinbau', '', 'info@steinbau.de', '', '', 'Antwort abwarten', '2026-09-23', 'Vielleicht dieses Jahr nicht schon wieder anfragen?', '0171–4050775   Arne Thomsen // 4.000 €', '2025-10-07 09:57:35', '2026-09-23 18:37:15', 'Mail', 0),
(81, 84, 'Stellwerkhof', 'liebes Team vom Stellwerkhof', '', 'info@stellwerkhof.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:40:12', 'Mail', 0),
(82, 85, 'Superfly Trampolinpark', 'liebes Team von Superfly', '', 'info@superfly.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:40:45', 'Mail', 0),
(83, 86, 'Techniker Krankenkasse', 'liebes Team der TK', '', 'service@tk.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:41:01', 'Mail', 0),
(84, 87, 'Theater Blaues Haus', 'liebes Team vom Theater Blaues Haus', '', 'Info@theaterblaueshaus.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:41:50', 'Mail', 0),
(85, 88, 'TOBEBOX Oberhausen', 'liebes Team der Tobebox', '', 'info@tobebox.de', NULL, NULL, 'Antwort abwarten', '2026-09-23', '', 'Kontingent erschöpft', '2025-10-07 09:57:35', '2026-09-23 18:38:27', 'Mail', 0),
(86, 89, 'Toys r us', 'liebes Team von Toys\'r\'us', '', 'service@toysrus.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:42:23', 'Mail', 0),
(87, 90, 'Tui Cruises', 'liebes Team von TUI Cruises', '', 'info@tuicruises.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:42:39', 'Mail', 0),
(88, 91, 'UCI', 'liebes Team von UCI', '', 'zentrale@uci-kinowelt.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:42:55', 'Mail', 0),
(89, 93, 'WAZ/NRZ', 'liebes Team von Funkemedien', '', 'kontakt@funkemedien.de', '', '', 'Antwort abwarten', '2026-09-25', 'Kommentarlos Päckchen gesendet', '', '2025-10-07 09:57:35', '2026-09-25 08:44:04', 'Mail', 0),
(90, 94, 'WDR', 'liebes Team vom WDR', '', 'redaktion@wdr.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', 'Anfrage wurde an Fachabteilung weitergeleitet', '', '2025-10-07 09:57:35', '2026-09-25 10:48:31', 'Mail', 0),
(91, 95, 'Wehrfritz GmbH -> HABA Pro', 'liebes Team von Haba-Pro', '', 'kontakt@haba-pro.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:44:37', 'Mail', 0),
(92, 96, 'Wer-wir-wurden.de', 'liebes Team von Wer Wir Wurden', '', 'kurzefrage@wer-wir-wurden.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:44:54', 'Mail', 0),
(93, 97, 'Wildpark Frankenhof', 'liebes Team vom Wildpark Frankenhof', '', 'info@wildpark-frankenhof.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:45:29', 'Mail', 0),
(94, 98, 'WMF', 'liebes Team von WMF', '', 'info@wmf-group.com', NULL, NULL, 'Spende abgesagt', '2026-09-25', 'Fokus auf lokale Projekte', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-25 10:49:28', 'Mail', 0),
(95, 99, 'Wohnungsgenossenschaft Duisburg Süd', 'liebes Team der Wohnungsgenossenschaft', '', 'kontakt@wgd-mitte.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:46:02', 'Mail', 0),
(96, 100, 'XXL-Sportcenter GmbH', 'liebes Team vom XXL-Sportcenter', '', 'info@bowlxxling.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:46:36', 'Mail', 0),
(97, 101, 'Zentis', 'liebes Team von Zentis', '', 'Info@zentis.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:46:53', 'Mail', 0),
(98, 102, 'Zoo Duisburg', 'liebes Team von Zoo', '', 'info@zoo-duisburg.de', '', '', 'Antwort abwarten', '2026-09-25', '', '2025: Freikarten | 2024: Absage', '2025-10-07 09:57:35', '2026-09-25 08:47:09', 'Mail', 0),
(99, 103, 'Zoo Krefeld', 'liebes Team vom Krefelder Zoo', '', 'info@zookrefeld.de', '', '', 'Antwort abwarten', '2026-09-25', '', '2025: Freikarten', '2025-10-07 09:57:35', '2026-09-25 08:47:27', 'Mail', 0),
(100, 104, 'Zoom Erlebniswelt', 'liebes Team von Zoom', '', 'info@zoom-erlebniswelt.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:47:44', 'Mail', 0),
(101, 105, 'Neukirchener Erziehungsverein', 'liebes Team vom Neukirchener Erziehungsverein', '', 'info@neukirchener.de', '', '', 'Antwort abwarten', '2026-09-23', '', 'Stellt Tasche zusammen / karin.braun@neukirchener.de / Ansprechpartner Andrea Leuker (Andrea.Leuker@neukirchener.de)', '2025-10-07 09:57:35', '2026-09-23 18:26:39', 'Mail', 0),
(102, 106, 'Kletterhalle Einstein neuenkamp', 'liebes Team der Einstein Kletterhalle', 'https://duisburg.einstein-boulder.com/', 'duisburg@einstein-boulder.com', NULL, NULL, 'Antwort abwarten', '2026-09-23', '', 'Kontingent erschöpft', '2025-10-07 09:57:35', '2026-09-23 18:11:01', 'Mail', 0),
(103, 107, 'Ikea', 'liebes Team von Ikea', '', 'impressum.de@IKEA.com', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle absage', '2025-10-07 09:57:35', '2026-09-25 10:48:48', 'Mail', 0),
(104, 110, 'Thalia', 'liebes Team von Thalia', 'https://www.thalia.de/', 'info@thalia.de', '', '', 'Spende abgesagt', '2026-09-25', '', 'Budget aufgebraucht, sie schlagen vor zu einem späteren Zeitpunkt erneut anzufragen', '2025-10-07 09:57:35', '2026-09-28 16:45:26', 'Mail', 0),
(105, 111, 'Mayersche Buchhandlung Duisburg', 'liebes Team der Mayerschen Buchhandlung in Duisburg', '', 'info-duisburg@thalia.de', '', '', 'Spende abgesagt', '2026-09-23', '', '2025: diverse Schreibkram/Deko', '2025-10-07 09:57:35', '2026-09-28 19:56:18', 'Mail', 0),
(106, 112, 'Monkey Spot', 'liebes Team vom Monkey Spot', 'https://monkeyspot.de/startseite-duisburg/', 'mail@monkeyspot.de', '', '', 'Antwort abwarten', '2026-09-25', '', '2025: 10 Rabattkarten (50%)', '2025-10-07 09:57:35', '2026-09-25 08:29:12', 'Mail', 0),
(108, 114, 'Volksbank Niederrhein', 'liebes Team der Volksbank Niederrhein', '', 'info@volksbank-niederrhein.de', '', '', 'Spende zugesagt', '2026-09-25', '', '2026: 250€', '2025-10-07 09:57:35', '2026-09-29 10:44:18', 'Mail', 0),
(109, 115, 'Schmidt Spiele', 'liebes Team von Schmidt Spiele', 'schmidtspiele.de', 'info@schmidtspiele.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:36:32', 'Mail', 0),
(110, 116, 'Hasbro', 'liebes Team von Hasbro', 'hasbro.de', 'zentrale@hasbro.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-28 14:59:13', 'Mail', 0),
(111, 117, 'Haba', 'liebes Team von Haba', 'haba-play.com', 'kundenservice@haba.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 09:28:41', 'Mail', 0),
(112, 118, 'Sparkasse Duisburg', 'liebes Team der Sparkasse Duisburg', 'Sparkasse-Duisburg.de', 'pr@spk-du.de', '', '', 'Antwort abwarten', '2026-10-02', '', '2026: generelle Absage, hinweis auf wirwunder.de', '2025-10-07 09:57:35', '2026-10-02 15:36:37', 'Mail', 0),
(114, 120, 'Lego Foundation', 'liebes Team von Lego', '', 'info@legofoundation.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:27:18', 'Mail', 0),
(115, 121, 'Lego', 'liebes Team von Lego', 'lego.com', 'consumerservice@lego.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:26:46', 'Mail', 0),
(116, 122, 'Rossmann', 'Liebe Team von Rossmann', 'rossmann.de', 'service@rossmann.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-25 08:50:33', 'Mail', 0),
(117, 123, 'dm Drogeriemarkt', 'liebes Team vom dm Drogeriemarkt', 'dm.de', 'ServiceCenter@dm.de', '', '', 'Spende abgesagt', '2026-09-25', '', 'Anfrage in lokalem dm Markt wurde empfohlen', '2025-10-07 09:57:35', '2026-09-25 09:03:58', 'Mail', 0),
(118, 124, 'XIAO Krefeld', 'liebes Team von Xiao', 'xiao-restaurant.de', 'krefeld@xiao-restaurant.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:46:19', 'Mail', 0),
(119, 125, 'nanunana', 'liebes Team von nanunana', 'nanu-nana.de', 'info@nanu-nana.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:30:34', 'Mail', 0),
(120, 126, 'Leuchtturm1917', 'liebes Team von Leuchtturm1917', 'leuchtturm1917.de', 'service@leuchtturm1917.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:27:35', 'Mail', 0),
(121, 127, 'Schleich', 'liebes Team von Schleich', '', 'sponsoring@schleich-s.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:36:15', 'Mail', 0),
(122, 128, 'Haribo', 'liebes Team von Haribo', '', 'info@haribo.com', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-25 12:09:49', 'Mail', 0),
(123, 129, 'Lamy', 'liebes Team von Lamy', '', 'info@lamy.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle Absage - Unterstützung nur in Heidelberg', '2025-10-07 09:57:35', '2026-09-28 09:16:56', 'Mail', 0),
(124, 130, 'happybrush.de', 'liebes Team von Happybrush', 'happybrush.de', 'info@happybrush.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:22:07', 'Mail', 0),
(125, 131, 'Procter & Gamble', 'liebes Team von Procter & Gamble', '', 'buenemann.v@pg.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:34:02', 'Mail', 0),
(126, 132, 'Amigo Spiele', 'liebes Team von Amigo Spiele', 'amigo-spiele.de', 'info@amigo-spiele.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:10:04', 'Mail', 0),
(127, 133, 'C & A', 'liebes Team von C&A', 'CundA.de', 'service@CundA.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', 'generelle Absage', '2025-10-07 09:57:35', '2026-09-25 08:14:07', 'Mail', 0),
(128, 134, 'H&M', 'liebes Team von H&M', '', 'kundenservice.de@hm.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:21:02', 'Mail', 0),
(129, 135, 'Puky', 'liebes Team von Puky', 'Puky.de', 'info@keebike.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-28 14:32:59', 'Mail', 0),
(130, 136, 'Nintendo', 'liebes Team von Nintendo', '', 'nintendo@punkt-pr.de', '', '', 'Spende abgesagt', '2026-09-29', 'Absage wegen aufgebrauchtem Budget. Unbedingt im neuen Jahr wieder anfragen!', 'Absage wegen aufgebrauchtem Budget. Unbedingt im neuen Jahr wieder anfragen!', '2025-10-07 09:57:35', '2026-11-12 10:52:40', 'Mail', 0),
(131, 137, 'Puma', 'Sehr geehrte Frau Neuber, liebes Team von Puma', 'Puma.com', 'kerstin.neuber@puma.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:34:35', 'Mail', 0),
(132, 138, 'Ferrero', 'liebes Team von Ferrero', 'ferrero.com', 'foodservice.deu@ferrero.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:18:25', 'Mail', 0),
(133, 139, 'Ehrenkind', 'liebes Team von Ehrenkind', 'ehrenkind.de', 'kundenservice@ehrenkind.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Kontingent erschöpft', '2025-10-07 09:57:35', '2026-09-25 08:51:23', 'Mail', 0),
(134, 140, 'Fackelmann', 'liebes Team von Fackelmann', 'fackelmann.de', 'fackelmannwelt@fackelmann.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:18:03', 'Mail', 0),
(135, 141, 'BabyOne', 'liebes Team von BabyOne', 'babyone.de', 'people@babyone.de', '', 'Niedieckstraße 36, 47803 Krefeld', '', '2026-09-25', 'Anfrage per Mail (people@babyone.de) ergab: Anfrage lokal stellen', '', '2025-10-07 09:57:35', '2026-10-17 21:05:02', 'Persönlich', 0),
(136, 142, 'CEWE Fotoservice', 'liebes Team vom CEWE Fotoservice', 'cewe-fotoservice.de', 'info@cewe-fotoservice.de', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-28 17:26:48', 'Mail', 0),
(137, 143, 'Kartenmacherei', 'liebes Team der Kartenmacherei', 'kartenmacherei.de', 'service@kartenmacherei.de', NULL, NULL, 'Spende abgesagt', '2026-09-23', '', 'Generelle Absage', '2025-10-07 09:57:35', '2026-09-24 07:29:40', 'Mail', 0),
(138, 144, 'Monin', 'liebes Team von Monin', 'monin.com', 'info-de@monin.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:28:56', 'Mail', 0),
(139, 145, 'Cricut', 'liebes Team von Cricut', 'cricut.com', 'support@cricut.com', NULL, NULL, 'Spende abgesagt', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-28 19:57:10', 'Mail', 0),
(140, 146, 'Epson', 'liebes Team von Epson', 'epson.de', 'kontakt_de@epson.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:17:30', 'Mail', 0),
(141, 147, 'Canon', 'liebes Team von Canon', 'Canon.de', 'customersupport@canon.co.in', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:14:29', 'Mail', 0),
(142, 148, 'Buetterken', 'liebes Team vom Bütterken', 'https://www.buetterken.de/', 'hallo@buetterken.de', '', '', 'Spende zugesagt', '2026-09-25', 'Drei Frühstücksgutscheine', '', '2025-10-07 09:57:35', '2026-09-25 15:15:21', 'Mail', 0),
(143, 149, 'Tee Gschwendner', 'liebes Team von Tee Gschwendner', '', 'moers@teegschwendner.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:41:17', 'Mail', 0),
(144, 150, 'Bauverein Rheinhausen', 'liebes Team vom Bauverein', '', 'info@bauverein-rheinhausen.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:12:30', 'Mail', 0),
(145, 151, 'Starbucks', 'liebes Team von Starbucks', '', 'Starbucks.Gaesteservice@amrest.eu', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:39:23', 'Mail', 0),
(146, 152, 'Steltens Bauernladen', 'liebes Team von Steltens Bauernladen', '', 'info@steltens.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:40:29', 'Mail', 0),
(148, 154, 'Cinzano', 'liebes Team von Cinzano', 'https://www.cinzano.de/', 'cinzano@callandcare.com', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 09:57:35', '2026-09-25 08:15:19', 'Mail', 0),
(149, 155, 'Archäologischer Park Xanten', 'liebe Frau Kühlborn', 'lvr.de', 'bianca.kuehlborn@lvr.de', '', '', 'Spende zugesagt', '2026-09-23', '', '2026: 2* 2 Gutscheine | Freikarten - bianca.kuehlborn@lvr.de / Allgemein: apx@lvr.de', '2025-10-07 09:57:35', '2026-10-20 05:49:20', 'Mail', 0),
(150, 156, 'Haniel', 'liebes Team von Haniel', 'haniel.de', 'info@haniel.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 11:05:15', '2026-09-25 08:21:35', 'Mail', 0),
(155, 157, 'Wiemann Lehrmittel', 'liebes Team von Wiemann Lehrmittel', 'wiemann-lehrmittel.de', 'info@wiemann-lehrmittel.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 18:56:37', '2026-09-25 08:45:12', 'Mail', 0),
(163, 158, 'Afrodita beauty & health', '', '', '', '', 'Düsseldorfer Str. 22, 47239', '', NULL, '', '', '2025-10-11 09:36:59', '2025-10-11 09:36:59', 'Persönlich', 0),
(164, 159, 'Allianz Robert Glüder', '', '', '', '', 'Giesenfeldstr., 47239', 'Spende zugesagt', NULL, '', '2026: RG Versicherung 150€, RG Immobilien 150€ (Dennis Gordon) | 2024: Barspende', '2025-10-11 09:37:18', '2026-11-06 16:02:05', 'Persönlich', 0),
(166, 161, 'Apotheke', '', '', '', '', 'Am Geistfeld, 47239', '', NULL, 'Asli fragt an', '2024: viele nette Präsente', '2025-10-11 09:42:59', '2026-11-04 09:07:42', 'Persönlich', 0),
(167, 162, 'Apotheke Ärtzehaus Moers', '', '', '', '', 'Goethestr., 47441', '', NULL, '', '', '2025-10-11 09:43:37', '2025-10-11 09:43:37', 'Persönlich', 0),
(168, 163, 'Apotheke Dorfstrasse', '', '', '', '', 'Dorfstraße, 47239', '', '2025-10-10', 'Asli fragt an\n', '2025: 100€ Spende 2024: viele kleine Präsente  2023: Werbegeschenke, Trostpreise', '2025-10-11 09:44:20', '2026-11-04 09:08:06', 'Persönlich', 0),
(169, 164, 'Arriotti-Beauty und viel mehr GmbH', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Gutschein Fußpflege 20€', '2025-10-11 09:44:37', '2025-10-11 10:26:07', 'Persönlich', 0),
(170, 165, 'back2school', '', '', '', '', 'Düsseldorfer Str. 128, 47239', '', NULL, '', '2024: Trostpreise', '2025-10-11 09:44:44', '2025-10-11 10:26:21', 'Persönlich', 0),
(171, 166, 'Bäckerei Büsch', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: keine Rückmeldung', '2025-10-11 09:44:58', '2025-10-11 10:28:42', 'Persönlich', 0),
(172, 167, 'Bäckerei Wiedemann', '', '', '', '', 'Düsseldorfer Str. 96, 47239', '', NULL, '', '', '2025-10-11 09:45:08', '2025-10-11 09:45:08', 'Persönlich', 0),
(173, 168, 'Ballettschule Köse', '', '', '', '', 'Asterlager Str., 47228', 'Spende zugesagt', NULL, 'Cecile fragt an', '2026: Gutscheine, 2024: Gutscheine + Kleinkram', '2025-10-11 09:46:15', '2026-11-04 09:12:01', 'Persönlich', 0),
(174, 169, 'Baumschule Moldenhauer', '', '', '', '', 'Sittardweg 17, 47239', '', NULL, '', '2025: nicht angefragt 2024: 5 Pflanzen', '2025-10-11 09:46:21', '2026-09-16 11:13:16', 'Persönlich', 0),
(175, 170, 'Bella Italia Gino', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 3 Gutscheine', '2025-10-11 09:46:40', '2025-10-11 10:29:48', 'Persönlich', 0),
(176, 171, 'Blumenladen bei Edeka (Name?)', '', '', '', '', 'Düsseldorfer Str. 107, 47239', '', NULL, '', '2024: Deko + Pflanzen', '2025-10-11 09:46:48', '2025-10-11 17:38:35', 'Persönlich', 0),
(177, 172, 'Capelli Connection', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: meldet sich evtl,', '2025-10-11 09:46:57', '2025-10-11 10:54:46', 'Persönlich', 0),
(178, 173, 'City-Grill', '', '', '', '', 'Düsseldorfer Str. 20, 47239', '', NULL, 'Dennis Gordon', '2024: 5x gutschein', '2025-10-11 09:47:03', '2026-11-06 09:00:37', 'Persönlich', 0),
(179, 174, 'Delta Stuben', '', '', '', '', 'Wagnerstr., 47239', '', NULL, '', '2024: 1 x 30€ Gutschein 2023: 2 Gutscheine', '2025-10-11 09:47:15', '2025-10-11 10:55:16', 'Persönlich', 0),
(180, 175, 'Dok Or Thaimassage', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: Gutscheine', '2025-10-11 09:47:24', '2025-10-11 10:55:30', 'Persönlich', 0),
(181, 176, 'Edeka Markt Laudage', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Schokolade, Malsachen, Stofftiere, Kaffee, Wein, CDs, etc.', '2025-10-11 09:47:35', '2025-10-11 10:55:51', 'Persönlich', 0),
(182, 177, 'Eiscafe Florenz', '', '', '', '', 'Markt, 47239', '', NULL, '', '2024: 3x10€ Gutschein,', '2025-10-11 09:47:45', '2025-10-11 11:07:15', 'Persönlich', 0),
(183, 178, 'Filor Fitnessstudio', '', '', '', '', 'Wagnerstr., 47239', '', NULL, 'Tanja', '2024: Gutschein Probetraining 2023: Gutschein Sonnenbank', '2025-10-11 09:47:49', '2026-09-25 12:19:27', 'Persönlich', 0),
(184, 179, 'Fotogeschäft', '', '', '', '', 'Bonertstr, 47239', '', NULL, '', '2024: 3 Bilderrahmen', '2025-10-11 09:47:57', '2025-10-11 11:08:49', 'Persönlich', 0),
(185, 180, 'Friseur Capelli Connection', '', '', '', '', 'Düsseldorfer Str. 18, 47239', '', NULL, '', '2024: 2 Gutscheine', '2025-10-11 09:48:03', '2025-10-11 11:07:45', 'Persönlich', 0),
(186, 181, 'Friseur Niolla', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 10€ bar 2023: Gutschein 10€', '2025-10-11 09:48:18', '2025-10-11 11:08:07', 'Persönlich', 0),
(187, 182, 'Frisör Auf der Mauer', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Gutschein und 10€ bar', '2025-10-11 09:48:30', '2025-10-11 11:08:23', 'Persönlich', 0),
(188, 183, 'Frisör Cut, Coulor & Style', '', '', '', '', 'Giesenfeldstr., 47239', '', NULL, '', '2024: 1 Gutschein a 10€', '2025-10-11 09:48:43', '2025-10-11 11:09:12', 'Persönlich', 0),
(189, 184, 'Frisör Kapper', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Gutschein', '2025-10-11 09:48:51', '2025-10-11 11:09:25', 'Persönlich', 0),
(190, 185, 'Frütel', '', '', '', '', 'Asterlager Str., 47228', '', NULL, '', '2024: nichts 2023: diverse Spielsachen und Bücher', '2025-10-11 09:49:06', '2025-10-11 11:09:42', 'Persönlich', 0),
(191, 186, 'Glüxbox', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: nicht gemeldet', '2025-10-11 09:49:18', '2025-10-11 11:10:07', 'Persönlich', 0),
(192, 187, 'Imbiss Lecker Essen & Trinken Oliver Heck', '', '', '', '', 'Birkenstr. 2, 47239', '', NULL, '', '2024: 1 Lego City + Gutschein 20€', '2025-10-11 09:49:27', '2025-10-11 11:10:20', 'Persönlich', 0),
(193, 188, 'Jojos Fellparadies', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 2 Bilder', '2025-10-11 09:49:36', '2025-10-11 11:10:38', 'Persönlich', 0),
(194, 189, 'Kleiner Dekoladen', '', '', '', '', 'Wagnerstr., 47239', '', NULL, '', '2024: div. aussortierte Deko (eher ramsch)', '2025-10-11 09:49:43', '2025-10-11 11:11:03', 'Persönlich', 0),
(195, 190, 'Kodi', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: nichts', '2025-10-11 09:49:51', '2025-10-11 11:11:15', 'Persönlich', 0),
(196, 191, 'Kosmetik Beauty Lifestyle', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2025: Ansprechpartner: Fr. Liedtke-Buchta Möchte nicht, da nie Resonanz bekommen // 2024: möchte nicht, da nie resonanz bekommen 2023: Seifen 10€', '2025-10-11 09:50:11', '2026-09-16 11:13:16', 'Persönlich', 0),
(197, 192, 'Lagerverkauf', '', '', '', '', 'Dorfstraße, 47239', '', NULL, 'Cecile fragt an', '2024: 3 Kisten Lebkuchenherzen', '2025-10-11 09:50:21', '2026-11-04 09:12:41', 'Persönlich', 0),
(198, 193, 'Leelawadee Massage', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: 10€ bar', '2025-10-11 09:50:29', '2025-10-11 11:11:31', 'Persönlich', 0),
(199, 194, 'Malerbetrieb Sven Schmitt', '', '', '', '', 'Landrat-von-Laer-Str. 9, 47239', '', NULL, '', 'Ansprechpartner: Sven Schmidt (Cousin Sally)', '2025-10-11 09:50:34', '2026-09-16 08:43:27', 'Persönlich', 0),
(200, 195, 'Massagepraxis Deltahaus', '', '', '', '', 'Wagnerstr., 47239', '', NULL, '', '', '2025-10-11 09:50:39', '2025-10-11 11:11:52', 'Persönlich', 0),
(201, 196, 'Memo\'s Pizza', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 2x 5,50', '2025-10-11 09:50:54', '2025-10-11 11:14:01', 'Persönlich', 0),
(202, 197, 'Metzgerei Heinen', '', '', '', '', 'Kapellener Str. 11, 47239', 'Spende abgesagt', '2025-10-14', '', '2026: Keine Preise abzugeben, nächstes Jahr wieder anfragen | 2025: Chef Cuisine Gusseisen Bräter 2024: alles bereits abgegeben, nächstes Jahr im Oktober fragen', '2025-10-11 09:51:02', '2026-09-30 09:50:24', 'Persönlich', 0),
(203, 198, 'MUNDO Mio Kindermode', '', '', '', '', 'Neustraße 21, 47441', '', NULL, '', '', '2025-10-11 09:51:10', '2025-10-11 11:16:28', 'Persönlich', 0),
(204, 199, 'Musikschule Rosenberger & Pögner', '', '', '', '', 'Dorfstraße 19, 47239', 'Spende zugesagt', NULL, '', '2026: Gutscheine, 2024: meldet sich, 3 Gutscheine', '2025-10-11 09:51:21', '2026-11-04 09:14:12', 'Persönlich', 0),
(205, 200, 'Optik Krasser', '', '', '', '', 'Düsseldorfer Str. 111, 47239', '', NULL, '', '2024: nichts', '2025-10-11 09:51:27', '2025-10-11 11:13:31', 'Persönlich', 0),
(207, 202, 'Optik Peerebooms', '', '', '', '', 'Dorfstraße, 47239', 'Spende zugesagt', NULL, 'Angefragt von Sally, kann nächste Woche abgeholt werden.', '2024: Kindersonnenbrille', '2025-10-11 09:51:44', '2026-11-03 15:02:43', 'Persönlich', 0),
(208, 203, 'Optives Ärztehaus Moers', '', '', '', '', 'Goethestr., 47441', '', NULL, '', '', '2025-10-11 09:51:51', '2025-10-11 09:51:51', 'Persönlich', 0),
(209, 204, 'Pane e Vino', '', '', '', '', 'Am Sportplatz, 47239', 'Spende zugesagt', NULL, '', '2026: 2* 25€ Gutschein | 2024: 50€ Gutschein 2023: 2 Gutscheine a 10€', '2025-10-11 09:52:05', '2026-11-05 20:15:15', 'Persönlich', 0),
(210, 205, 'Pizzeria Roma', '', '', '', '', '-', '', NULL, '', '2024: 1 Gutschein 20€', '2025-10-11 09:52:20', '2025-10-11 11:16:53', 'Persönlich', 0),
(211, 206, 'Pizzeria Samra', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: 5xkleine Pizza, 3 Gutscheine f.eine große Pizza 2023: Gutschein über 10 Euro', '2025-10-11 09:52:33', '2025-10-11 11:17:43', 'Persönlich', 0),
(212, 207, 'Pizzeria Trestelle', '', '', '', '', 'Asterlager Str., 47228', 'Spende zugesagt', '2026-11-11', 'Kaiser fragt an\n11.11.2026: Angefragt von Ceylan Kaiser', '2026: Gutscheine | Ansprechpartner: Richem Andelast', '2025-10-11 09:52:37', '2026-11-11 06:23:03', 'Persönlich', 0),
(213, 208, 'provinzial Versicherung', '', '', '', '', 'Düsseldorfer Str., 47239', 'Spende zugesagt', NULL, '', '2026: 200€ in Spielwaren dank Julia | 2025: nichts', '2025-10-11 09:52:42', '2026-11-05 21:34:12', 'Persönlich', 0),
(214, 209, 'Pur Natur', '', '', '', '', 'Neustraße 23, 47441', '', NULL, '', '2024: Tüte Kleinkram', '2025-10-11 09:52:52', '2025-10-11 11:18:01', 'Persönlich', 0),
(215, 210, 'Raiffeisenmarkt Schwafheim', '', '', '', '', 'Schwarzer Weg, 47447', '', NULL, 'Julia Perret fragt an', '2024: Bälle, Trinkflaschen', '2025-10-11 09:53:02', '2026-11-04 09:17:09', 'Persönlich', 0),
(216, 211, 'Raumwerk', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: keine Rückmeldung 2023: 1 Handtasche (laut Preisschild für 219€!!!)', '2025-10-11 09:53:18', '2025-10-11 12:16:26', 'Persönlich', 0),
(217, 212, 'Reisebüro Klatt', '', '', '', '', 'Dorfstraße, 47239', '', NULL, 'Julia Perret fragt an', '2024: Trostpreise', '2025-10-11 09:53:34', '2026-11-04 09:17:29', 'Persönlich', 0),
(218, 213, 'REWE Johann', 'lieber Herr Johann', '', 'rewe-rumeln@gmx.de', '', 'Rathausallee, 47239', 'Antwort abwarten', '2026-09-25', '', 'diverse Werbeartikel u.a. von Gerolsteiner // Marktleiter 02151-4029751 Ergebnis: diverse Werbeartikel u.a. von Gerolsteiner', '2025-10-11 09:53:39', '2026-09-25 08:35:08', 'Mail', 0),
(219, 214, 'Rheinland Apotheke', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Sporttasche u. Trostpreise', '2025-10-11 09:53:45', '2025-10-11 11:18:14', 'Persönlich', 0),
(220, 215, 'Rumelner Grill', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: Gutscheine 5x 10€', '2025-10-11 09:53:54', '2025-10-11 12:22:00', 'Persönlich', 0),
(221, 216, 'Rumelner TV', '', '', '', '', 'Am Sportplatz 15, 47239', 'Spende zugesagt', NULL, 'Daniela fragt an', '2026: Rucksack und Mütze (Budget erschöpft) | 2024: 5 Mützen, 5 Schals', '2025-10-11 09:54:00', '2026-11-06 09:03:47', 'Persönlich', 0),
(222, 217, 'Sanitätshaus Ermers Am Geistfeld e.K.', '', '', '', '', 'Am Geistfeld, 47239', 'Spende zugesagt', NULL, 'Dennis Gordon', '2026: Sachspendenpaket + Gutschein (Dennis Gordon) | 2024: viele nette Präsente', '2025-10-11 09:54:07', '2026-11-06 16:03:30', 'Persönlich', 0),
(223, 218, 'Sparkasse', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: keine Rückmeldung', '2025-10-11 09:54:15', '2025-10-11 12:17:28', 'Persönlich', 0),
(224, 219, 'Stahlkind', '', '', '', '', 'Dorfstraße, 47239', 'Spende zugesagt', '2025-10-11', 'Tanja', '2025: 2x Gutschein (30€) + Handtuch + Tasse + Kulturbeutel 2024: 2x Gutschein+viele Sachspenden (150€)', '2025-10-11 09:54:33', '2026-11-04 09:22:53', 'Persönlich', 0),
(225, 220, 'Steakhaus Schwafheim', '', '', '', '', ', 47447', '', NULL, 'Dennis Gordon', '2024: 2x15€ Gutscheine,', '2025-10-11 09:54:42', '2026-11-06 08:58:57', 'Persönlich', 0),
(226, 221, 'Thetford', 'liebes Team von Thetford', '', 'infod@thetford.eu', '', 'Schallbruch 14, 42781', 'Antwort abwarten', '2026-09-25', '', '2025: Campingtoilette + viele Zubehörflaschen + WC Papier 2023: Campingtoilette', '2025-10-11 09:54:48', '2026-09-25 08:42:06', 'Mail', 0),
(227, 222, 'Total Tankstelle', '', '', '', '', 'Düsseldorfer Str. 51, 47239', '', NULL, '', '2024: nichts', '2025-10-11 09:54:55', '2025-10-11 12:16:39', 'Persönlich', 0),
(228, 223, 'trinkgut', '', '', '', '', 'Kapellener Str., 47239', 'Spende zugesagt', NULL, '', '2026: Sachspendenpaket und Gutscheine (Dennis Gordon) | 2024: diverse Kleinigkeiten', '2025-10-11 09:55:00', '2026-11-06 16:02:50', 'Persönlich', 0),
(229, 224, 'Volksbank', '', '', '', '', 'Dorfstraße 65, 47239', '', '2025-10-10', '', '2024: viele nette Präsente', '2025-10-11 09:55:18', '2026-09-25 10:45:30', 'Persönlich', 0),
(230, 225, 'Wasserskianlage Toeppersee', '', '', '', '', 'Lohfelder Weg 91, 47239', '', NULL, '', '2024: diverse Gutscheine', '2025-10-11 09:55:29', '2025-10-11 10:30:25', 'Persönlich', 0),
(231, 226, 'dm', 'liebes Team von dm', '', '', '', 'Lange Str. 67 47829 Krefeld', '', NULL, '', '', '2025-10-11 12:24:21', '2025-10-11 12:24:21', 'Persönlich', 0),
(232, 227, 'dm', 'liebes Team von dm', '', '', '', 'Atroper Str. 9-13 47226 Duisburg', '', NULL, '', '', '2025-10-11 12:25:13', '2025-10-11 12:25:13', 'Persönlich', 0),
(233, 228, 'Rossmann', 'liebes Team von Rossmann', '', '', '', 'Höschenstr. 69 47228', '', NULL, '', '', '2025-10-11 12:26:00', '2025-10-11 12:26:00', 'Persönlich', 0),
(235, 229, 'UCI Kinowelt', 'Liebes Team vom UCI', 'https://uci-kinowelt.de', 'zentrale@uci-kinowelt.de', '', '', 'Antwort abwarten', '2026-09-25', '', '', '2025-10-15 05:36:18', '2026-09-25 08:43:12', 'Mail', 0),
(236, 230, 'Phantasialand', 'liebes Team von Phantasialand', 'https://phantasialand.de', 'shop@phantasialand.de', '', '', 'Antwort abwarten', '2026-09-25', '', '2025: 4 Freikarten', '2025-10-15 05:37:56', '2026-09-25 08:33:18', 'Mail', 0),
(237, 231, 'Okidoki Kinderland', 'liebes Team vom Okidoki Kinderland', 'https://okidoki-kinderland.de', 'info@okidoki-kinderland.de', '', '', 'Spende zugesagt', '2026-09-25', 'Sie können gegen Vorlage einer Abholberechtigung Ihres Vereins bei uns\nan der Info 5 große Geschenke und 5 x 2 Freikarten abholen.', '2026: 5 Geschenke und Freikarten | 2025: Geschenke und Freikarten ', '2025-10-15 05:40:07', '2026-10-22 08:01:28', 'Mail', 0),
(240, 232, 'Movie Park Bottrop', 'liebes Team vom Movie Park', 'https://moviepark.de', 'info@moviepark.de', '', '', 'Spende abgesagt', '2026-09-25', '', '2025: generelle Absage', '2025-10-22 08:42:02', '2026-09-25 10:48:42', 'Mail', 0),
(247, 234, 'REWE Stiftung Dortmund', 'liebes Team der REWE Stiftung', 'https://www.rewe-dortmund.de/stiftung/', 'stiftung-rewe-dortmund@rewe-group.com', 'https://www.rewe.de/service/kontakt/', '', 'Antwort abwarten', '2026-10-02', 'Von der Seite:\r\n1. Mail an stiftung-rewe-dortmund@rewe-group.com mit einer kurzen Vorstellung des Vereins, Projektvorstellung etc. \r\n2. Es erfolgt eine Rückmeldung, ob das Thema/Projekt zu unseren Förderrichtlinien passt. Die Rückmeldung kann aufgrund der quartalsweise stattfindenden Vorstandssitzung etwas Zeit in Anspruch nehmen.\r\n3. Bei positivem Bescheid gilt es den Förderantrag auszufüllen und alles weitere erfolgt dann in direkter Absprache.', 'Anfrage in Zukunft über die REWE Stiftung. Online Formulare herunterladbar. Mailadresse: stiftung@rewe-dortmund.de  //2025: Spende 500€', '2025-10-24 07:45:41', '2026-10-02 07:02:29', 'Mail', 0),
(248, 235, 'Venator Corp', 'liebes Team von Venator', 'https://venatorcorp.com', 'Duisburg_info@venatorcorp.com', '', '', 'Antwort abwarten', '2026-09-25', '', NULL, '2025-10-25 13:40:43', '2026-09-25 08:43:30', 'Mail', 0),
(250, 237, 'Rossmann', 'liebes Team von Rossmann', 'rossmann.de', '', '', 'Höschenstraße 69, 47228 Duisburg-Bergheim', 'Antwort abwarten', NULL, NULL, NULL, '2026-09-23 18:01:55', '2026-09-23 18:01:55', 'Persönlich', 0),
(251, 238, 'dm Drogeriemarkt', 'liebes Team von dm Drogeriemarkt', '', '', '', 'Atroper Str. 9-13; 47226 Duisburg', 'Antwort abwarten', NULL, NULL, NULL, '2026-09-23 18:03:28', '2026-09-23 18:03:28', 'Persönlich', 0),
(252, 239, 'Karls Erlebnis-Dorf', 'liebes Team von Karls Erlebnis-Dorf', 'https://karls.de', 'post@karls.de', '', '', 'Antwort abwarten', '2026-09-23', '', NULL, '2026-09-23 18:19:22', '2026-09-23 18:19:44', 'Mail', 0),
(258, 242, 'Neue Tischkultur', 'liebes Team von Neue Tischkultur', 'https://neuetischkultur.de', 'service@neuetischkultur.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 06:47:02', '2026-10-02 06:47:41', 'Mail', 0),
(259, 243, 'Kosmos', 'liebe Frau Jäkel', 'https://kosmos.de', 'a.jaekel@kosmos.de', '', '', 'Spende abgesagt', '2026-10-02', '', '2026: Generelle Absage - nur mit ausgewählten Partnern', '2026-10-02 16:39:56', '2026-10-05 07:26:23', 'Mail', 0),
(260, 244, 'ENNI', 'liebes Team von ENNI', 'https://enni.de', 'spenden_sponsoring_werbung@enni.de', 'https://www.enni.de/spendenanfrage', '', 'Spende abgesagt', '2026-10-02', '', 'Nimmt keine Anfragen aus Duisburg-Rumeln an', '2026-10-02 16:41:29', '2026-11-08 19:25:25', 'Formular', 0),
(261, 245, 'EWE', 'liebes Team von EWE', 'https://ewe.de', 'ma-sp@ewe.de', '', '', 'Spende abgesagt', '2026-10-02', '', '2026: Generelle Absage - agiert nur lokal', '2026-10-02 16:43:02', '2026-10-05 07:25:14', 'Mail', 0),
(262, 246, 'Mainova', 'liebes Team von Mainova', 'https://mainova.de', 'tombola@mainova.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 16:44:42', '2026-10-02 16:45:03', 'Mail', 0),
(263, 247, 'Grafik Werkstatt', 'liebes Team von Grafik Werkstatt', 'https://grafik-werkstatt.de', 'webshop@grafik-werkstatt.de', '', '', 'Spende zugesagt', '2026-10-02', 'Spendenquittung erwünscht', '2026: Sachspenden', '2026-10-02 16:45:36', '2026-10-16 11:13:36', 'Mail', 0),
(264, 248, 'Stiftung Lesen', 'liebes Team von Stiftung Lesen', 'https://stiftunglesen.de', 'mail@stiftunglesen.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 16:52:39', '2026-10-02 16:53:24', 'Mail', 0);
INSERT INTO `contacts` (`id`, `primary_key`, `name`, `anrede`, `webseite`, `emailadresse`, `formular_link`, `anschrift`, `status`, `zuletzt_angeschrieben`, `kommentar`, `letztes_ergebnis`, `created_at`, `updated_at`, `typ`, `is_suggestion`) VALUES
(265, 249, 'Steiff', 'liebes Team von Steiff', 'https://steiff.com', 'info@steiff.com', '', '', 'Spende abgesagt', '2026-10-02', '', '2026: generelle absage ', '2026-10-02 16:54:25', '2026-10-05 09:32:05', 'Mail', 0),
(266, 250, 'Kaufland', 'liebes Team von Kaufland', 'https://kaufland.de', 'info@kaufland.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 17:00:06', '2026-10-02 17:00:32', 'Mail', 0),
(267, 251, 'Kaufland', 'liebes Team von Kaufland', 'https://kaufland.de', '', '', 'Kirchstraße 96, 47198 Homberg-Ruhrort-Baerl', 'Antwort abwarten', NULL, NULL, NULL, '2026-10-02 17:01:56', '2026-10-02 17:01:56', 'Persönlich', 0),
(268, 252, 'Böttcher AG', '', 'https://www.bueromarkt-ag.de/', '', 'https://www.bueromarkt-ag.de/service/spendenformular', '', 'Antwort abwarten', NULL, NULL, NULL, '2026-10-02 17:03:18', '2026-10-02 17:03:18', 'Formular', 0),
(269, 253, 'Vakis Coffee Club', 'liebes Team von Vakis Coffee Club', 'https://vakis.net', 'kontakt@vakis.net', '', '', 'Spende zugesagt', '2026-10-15', '', '2026: 10 Gutscheine', '2026-10-15 08:42:12', '2026-10-18 18:17:05', 'Mail', 0),
(272, 256, 'Spiegelburg', 'liebes Team der Spiegelburg', 'https://coppenrath.de', 'info@coppenrath.de', '', '', 'Antwort abwarten', '2026-10-18', NULL, NULL, '2026-10-18 09:02:27', '2026-10-18 09:05:41', 'Mail', 0),
(273, 257, 'Loewe Verlag', 'liebes Team vom Loewe Verlag', 'https://loewe-verlag.de', 'info@loewe-verlag.de', '', '', 'Antwort abwarten', '2026-10-18', NULL, NULL, '2026-10-18 09:03:29', '2026-10-18 09:05:24', 'Mail', 0),
(274, 258, 'Tessloff Verlag', 'liebes Team von Tessloff Verlag', 'https://tessloff.com', 'info@tessloff.com', '', '', 'Spende abgesagt', '2026-10-18', '', '2026: Absage aus organisatorischen Gründen', '2026-10-18 09:04:21', '2026-10-20 09:08:57', 'Mail', 0),
(275, 259, 'Okidoki Krefeld', '', 'https://okidoki-krefeld.de/kontakt/', '', '', 'Hülser Str. 770, 47803 Krefeld', 'Spende zugesagt', NULL, '', '2026: 10 Freikarten', '2026-10-21 20:31:33', '2026-10-21 20:32:23', 'Persönlich', 0),
(276, 260, 'Bobbolino Kinderwelt', '', 'https://bobbolino.de/', '', '', 'Neustraße 18 – 47809 Krefeld', 'Spende zugesagt', NULL, '', '2026: 3 Freikarten', '2026-10-21 20:34:01', '2026-10-21 20:34:18', 'Persönlich', 0),
(277, 261, 'You Jump Krefeld | Trampolinpark', 'liebes Team von You Jump Krefeld | Trampolinpark', 'you-jump.de', '', '', 'Gahlingspfad 14/16, 47803 Krefeld', 'Spende zugesagt', NULL, '', '2026: Tombola Pakete', '2026-10-24 06:41:57', '2026-10-24 06:42:51', 'Persönlich', 0),
(279, 262, 'Irrland', 'liebes Team von Irrland', 'https://irrland.eu', 'service@irrland.eu', '', '', 'Spende abgesagt', NULL, '', '2026: allgemeine Absage', '2026-11-05 08:11:18', '2026-11-05 08:15:02', 'Mail', 0),
(280, 263, 'Kettler Hof', 'liebes Team von Kettler Hof', 'https://kettelerhof.de', 'info@kettelerhof.de', '', '', 'Spende abgesagt', NULL, '', '2026: allgemeine Absage', '2026-11-05 08:12:47', '2026-11-05 08:14:40', 'Mail', 0),
(281, 264, 'Toverland', 'liebes Team von Toverland', 'https://toverland.nl', 'info@toverland.nl', '', '', 'Spende abgesagt', NULL, '', '2026: allgemeine Absage', '2026-11-05 08:19:26', '2026-11-05 08:20:02', 'Mail', 0),
(282, 265, 'Füchse Duisburg', 'liebe Duisburger Füchse', 'https://fuechse-duisburg.de', 'info@fuechse-duisburg.de', '', '', 'Antwort abwarten', '2026-11-08', '', NULL, '2026-11-08 19:13:27', '2026-11-08 19:20:52', 'Mail', 0),
(283, 266, 'DEG Eishockey e.V.', 'liebes Team von DEG Eishockey e.V.', 'https://deg.club', 'info@deg.club', '', '', 'Antwort abwarten', '2026-11-08', '', NULL, '2026-11-08 19:19:54', '2026-11-08 19:20:16', 'Mail', 0),
(285, 267, 'Flughafen Düsseldorf', 'liebe Frau Handschug', 'https://dus.com', 'carina.handschug@dus.com', '', '', 'Antwort abwarten', '2026-11-11', 'Kontakt: Fr. Carina Handschug über Bastian Müller', '', '2026-11-11 08:48:15', '2026-11-11 08:50:58', 'Mail', 0),
(286, 268, 'Handmade-Massagen', 'liebes Team von Handmade-Massagen', 'https://handmade-massage.de', 'handmade23@gmx.de', '', '', 'Antwort abwarten', '2026-11-11', '', NULL, '2026-11-11 14:55:31', '2026-11-11 14:55:49', 'Mail', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `template_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `betreff` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mailtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_rule` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all_active',
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_name`, `betreff`, `mailtext`, `recipient_rule`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Weihnachtstombola 2025', 'Weihnachtstombola des ev. Kindergarten', 'wir, der Förderverein des evangelischen Kindergartens Duisburg-Rumeln e.V., möchten Sie herzlich um Ihre Unterstützung für eine besondere Aktion bitten.\r\nFür unsere U3-Kinder möchten wir einen eigenen Spielbereich auf dem Außengelände mit einer kleinen Kletteranlage einrichten, die unseren Kleinsten vielfältige Möglichkeiten zum Klettern, Krabbeln, Entdecken und gemeinsamen Spielen bietet und damit das Außengelände nachhaltig bereichern soll. Zur Finanzierung der Spielanlage fehlt jedoch noch ein erheblicher Betrag.\r\n\r\nUm unserem Ziel näherzukommen, möchten wir auf dem Weihnachtsmarkt des Rumelner Turnvereins erneut einen Stand mit einer Tombola anbieten. In den vergangenen Jahren war diese Aktion die erfolgreichste Einnahmequelle des Vereins und wir hoffen auf einen ähnlichen Erfolg in diesem Jahr.\r\n\r\nFür die Umsetzung der Tombola sind wir allerdings auf Sachspenden oder auch finanzielle Unterstützung für die Zusammenstellung von Preisen und Trostpreisen angewiesen. Deshalb möchten wir Sie fragen, ob Sie uns in diesem Vorhaben ein wenig unterstützen. Gerne stellen wir Ihnen auf Wunsch auch eine Spendenquittung aus.\r\n\r\nFür Rückfragen stehen wir Ihnen jederzeit sehr gerne zur Verfügung. \r\n\r\nHerzlichen Dank im Voraus für Ihre Hilfe und Ihr Engagement!\r\n\r\nMit freundlichen Grüßen\r\nMartina Echtenbruck\r\n(2. Vorsitzende)', 'all_active', 1, '2025-10-07 09:53:20', '2026-09-07 07:45:24'),
(3, 'Anmeldebestätigung', 'Herzlich Willkommen im Förderverein des Kindergartens!', 'Liebe/r {{vorname}},<br>\r\n<br>\r\nim Namen des Vorstands des Fördervereins des ev. Kindergartens Duisburg-Rumeln heiße ich Dich herzlich willkommen und bedanke mich für Deine Anmeldung!<br>\r\n<br>\r\nEs freut uns sehr, dass Du Dich dazu entschieden hast, dem Förderverein beizutreten und damit einen aktiven Beitrag zur Unterstützung unseres Kindergartens und unserer Kindergartenkinder zu leisten. Als Mitglied des Vereins hast Du die Möglichkeit, an wichtigen Entscheidungen teilzuhaben, Veranstaltungen mitzugestalten und unseren Kindergarten in vielfältiger Weise zu fördern.<br>\r\n<br>\r\nDamit Du bestens über alle Aktivitäten, Termine und Neuigkeiten informiert bist, möchte ich hier kurz die wichtigsten Infos zusammenfassen:<br>\r\n<b><br>\r\nMitgliedschaft und Mitgliedsbeitrag:</b> Deine Mitgliedschaft im Förderverein beginnt am {{mitglied_seit}} und endet am {{mitglied_bis}}. Du hast einen monatlichen Mitgliedsbeitrag von {{beitrag}} gewählt. Den fälligen Beitrag buchen wir einmal im Jahr zu Beginn des Kindergartenjahres von dem von Dir hinterlegten Konto ab.<br>\r\n<br>\r\n<b>Kommunikation:</b> Wir versuchen immer unsere Informationen auf allen möglichen Kanälen zu teilen. Direkte Infos an die Eltern bzw. unsere Mitglieder senden wir per E-Mail/Kitaplus oder über die offene WhatsApp-Gruppe des Kindergartens. Berichte und Ankündigungen finden sich auch für gewöhnlich auf unserer Website unter https://foerderverein.evkigarumeln.de. Außerdem finden sich alle Informationen auch immer an unserer Pinnwand im Eingangsbereich des Kindergartens.<br>\r\n<br>\r\n<b>Veranstaltungen und Aktionen:</b> Unser Förderverein organisiert regelmäßig Veranstaltungen und Aktionen zugunsten des Kindergartens, wie z.B. Flohmärkte, den Sponsorenlauf oder die Tombola, außerdem unterstützen wir den Kindergarten finanziell bei benötigten Anschaffungen oder bei Ausflügen. Wir freuen uns über Deine aktive Teilnahme und Deine Ideen!<br>\r\n<br>\r\n<b>Ganz aktuell:</b>&nbsp;Am ersten Adventswochenende (27.-29. November) sind wir mit einem Stand auf dem Weihnachtsmarkt des RTV vertreten und bieten dort unsere Tombola an. Schau gerne auf ein paar Lose vorbei oder unterstütze uns aktiv indem Du eine Shhicht übernimmst. Sprich uns gerne an.<br>\r\n<br><b>\r\nMitgestaltungsmöglichkeiten:</b> Als Mitglied hast Du die Möglichkeit, aktiv an der Gestaltung des Kindergartenlebens mitzuwirken. Wir sind immer offen für neue Ideen und Engagement. Sprich uns einfach an.&nbsp;<br>\r\n<br>\r\n<b>Mitgliedschaftsbestätigung:</b> Diese E-Mail dient gleichzeitig als Bestätigung Deiner Mitgliedschaft im Förderverein.<br>\r\n<br>\r\nWie schön, dass Du uns unterstützt. Bei Fragen oder Anregungen zögere nicht, uns anzusprechen.<br>\r\n<br>\r\nLiebe Grüße,<br>\r\n<br>\r\nMartina Echtenbruck<br>1. Vorsitzende', 'new_member', 0, '2026-11-09 07:10:46', '2026-11-09 07:50:19');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fundraising_campaigns`
--

CREATE TABLE `fundraising_campaigns` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at` datetime DEFAULT NULL,
  `started_by` int DEFAULT NULL,
  `ended_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `fundraising_campaigns`
--

INSERT INTO `fundraising_campaigns` (`id`, `name`, `started_at`, `ended_at`, `started_by`, `ended_by`, `created_at`) VALUES
(1, 'Sammelaktion für die Weihnachtstombola 2026', '2026-09-10 00:00:00', NULL, NULL, NULL, '2026-10-16 11:06:59');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `helper_actions`
--

CREATE TABLE `helper_actions` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('draft','active','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `default_slots` json DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `helper_actions`
--

INSERT INTO `helper_actions` (`id`, `title`, `description`, `start_date`, `end_date`, `status`, `default_slots`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Tombola auf dem Weihnachtsmarkt des RTV', 'Auf dem Parkplatz bei Am Sportplatz 15', '2026-11-27', '2026-11-29', 'active', '[{\"key\": \"14:00-16:00\", \"label\": \"14:00–16:00\"}, {\"key\": \"16:00-18:00\", \"label\": \"16:00–18:00\"}, {\"key\": \"18:00-20:00\", \"label\": \"18:00–20:00\"}, {\"key\": \"20:00-22:00\", \"label\": \"20:00–22:00\"}]', 3, '2026-11-07 06:58:43', '2026-11-07 08:59:40');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `helper_assignments`
--

CREATE TABLE `helper_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `registration_id` bigint UNSIGNED NOT NULL,
  `day_id` bigint UNSIGNED NOT NULL,
  `station_id` bigint UNSIGNED NOT NULL,
  `slot_key` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `helper_assignments`
--

INSERT INTO `helper_assignments` (`id`, `registration_id`, `day_id`, `station_id`, `slot_key`, `created_at`) VALUES
(5, 3, 1, 2, '17:00-19:30', '2026-11-07 21:21:46'),
(6, 4, 1, 2, '17:00-19:30', '2026-11-07 21:22:06'),
(7, 5, 1, 1, '15:00-17:00', '2026-11-08 09:21:52'),
(8, 5, 1, 2, '19:30-22:00', '2026-11-08 09:21:52'),
(9, 6, 2, 2, '16:00-18:00', '2026-11-08 11:08:59'),
(11, 8, 2, 2, '18:00-20:00', '2026-11-08 14:58:25'),
(12, 9, 2, 2, '18:00-20:00', '2026-11-08 15:01:41'),
(13, 2, 2, 2, '16:00-18:00', '2026-11-08 20:46:03'),
(14, 2, 1, 1, '15:00-17:00', '2026-11-08 20:46:03'),
(15, 2, 3, 3, '17:00-19:00', '2026-11-08 20:46:03'),
(16, 11, 1, 2, '19:30-22:00', '2026-11-09 06:55:42'),
(17, 12, 2, 2, '20:00-22:00', '2026-11-09 11:04:43'),
(18, 12, 3, 2, '12:00-14:30', '2026-11-09 11:04:43'),
(19, 13, 2, 2, '20:00-22:00', '2026-11-09 15:55:21'),
(20, 14, 1, 1, '15:00-17:00', '2026-11-10 14:48:17'),
(21, 15, 3, 2, '14:30-17:00', '2026-11-10 21:03:57'),
(22, 16, 2, 2, '14:00-16:00', '2026-11-11 08:12:38'),
(23, 17, 2, 2, '14:00-16:00', '2026-11-11 08:13:21'),
(24, 18, 3, 2, '12:00-14:30', '2026-11-11 22:31:35'),
(25, 19, 1, 1, '15:00-17:00', '2026-11-13 08:29:48'),
(26, 20, 1, 1, '15:00-17:00', '2026-11-13 08:32:26'),
(27, 21, 3, 2, '14:30-17:00', '2026-11-13 08:44:09'),
(28, 22, 3, 3, '17:00-19:00', '2026-11-13 08:52:46'),
(29, 23, 1, 1, '15:00-17:00', '2026-11-13 08:57:43'),
(30, 24, 3, 3, '17:00-19:00', '2026-11-13 09:05:12'),
(31, 25, 3, 3, '17:00-19:00', '2026-11-13 09:05:34');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `helper_days`
--

CREATE TABLE `helper_days` (
  `id` bigint UNSIGNED NOT NULL,
  `action_id` bigint UNSIGNED NOT NULL,
  `event_date` date NOT NULL,
  `slots_json` json NOT NULL,
  `custom_grid` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `helper_days`
--

INSERT INTO `helper_days` (`id`, `action_id`, `event_date`, `slots_json`, `custom_grid`) VALUES
(1, 1, '2026-11-27', '[{\"key\": \"15:00-17:00\", \"label\": \"15:00–17:00\"}, {\"key\": \"17:00-19:30\", \"label\": \"17:00–19:30\"}, {\"key\": \"19:30-22:00\", \"label\": \"19:30–22:00\"}]', 1),
(2, 1, '2026-11-28', '[{\"key\": \"14:00-16:00\", \"label\": \"14:00–16:00\"}, {\"key\": \"16:00-18:00\", \"label\": \"16:00–18:00\"}, {\"key\": \"18:00-20:00\", \"label\": \"18:00–20:00\"}, {\"key\": \"20:00-22:00\", \"label\": \"20:00–22:00\"}]', 0),
(3, 1, '2026-11-29', '[{\"key\": \"12:00-14:30\", \"label\": \"12:00–14:30\"}, {\"key\": \"14:30-17:00\", \"label\": \"14:30–17:00\"}, {\"key\": \"17:00-19:00\", \"label\": \"17:00–19:00\"}]', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `helper_day_stations`
--

CREATE TABLE `helper_day_stations` (
  `day_id` bigint UNSIGNED NOT NULL,
  `station_id` bigint UNSIGNED NOT NULL,
  `active_slots_json` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `helper_day_stations`
--

INSERT INTO `helper_day_stations` (`day_id`, `station_id`, `active_slots_json`) VALUES
(1, 1, '[\"15:00-17:00\"]'),
(1, 2, '[\"17:00-19:30\", \"19:30-22:00\"]'),
(1, 3, '[]'),
(2, 1, '[]'),
(2, 2, '[\"14:00-16:00\", \"16:00-18:00\", \"18:00-20:00\", \"20:00-22:00\"]'),
(2, 3, '[]'),
(3, 1, '[]'),
(3, 2, '[\"12:00-14:30\", \"14:30-17:00\"]'),
(3, 3, '[\"17:00-19:00\"]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `helper_registrations`
--

CREATE TABLE `helper_registrations` (
  `id` bigint UNSIGNED NOT NULL,
  `action_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_token_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `helper_registrations`
--

INSERT INTO `helper_registrations` (`id`, `action_id`, `full_name`, `email`, `edit_token_hash`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Martina Echtenbruck', 'martinafriese@gmail.com', 'dc9958d0dd4e8e164e2715addc4919f4337714e86e496997b5f0e13f23d40292', 'active', '2026-11-07 08:37:19', '2026-11-07 08:46:53'),
(2, 1, 'Martina Echtenbruck', 'martinafriese@gmail.com', 'b4d7730dd28d7b803ba0ffd981d1643edf30b08fe3eb30b20ce3332a910cdf3e', 'active', '2026-11-07 08:57:54', '2026-11-07 08:57:54'),
(3, 1, 'Cecile Anderheyden', 'cecile.anderheyden@gmail.com', 'f0f6e3b0f44ac2cc0e90784d109e7a22ac6caa2ad70985ff89232a5c532d31a1', 'active', '2026-11-07 21:21:46', '2026-11-07 21:21:46'),
(4, 1, 'Tanja Wende', 'tanja_205@web.de', '2cdd6b688a915536fb624dc20275198d672f9e3b5fd79c61251681cd92be49af', 'active', '2026-11-07 21:22:06', '2026-11-07 21:22:06'),
(5, 1, 'Julia Perret', 'perret_julia@gmx.de', '8d67636f8477faf74b032fe44ab2850c6eed68608d5505ad189e58ae1acb60a0', 'active', '2026-11-08 09:21:52', '2026-11-08 09:21:52'),
(6, 1, 'Daniela Gröters', 'danielalebenicnik@web.de', 'c362f8f2c2c912f4d7dae2bd15c7b8b6f68fd580857e2f0258be4eebd8ec3c8a', 'active', '2026-11-08 11:08:59', '2026-11-08 11:08:59'),
(7, 1, 'Martina Echtenbruck', 'martinafriese@gmail.com', '8b012ce16dfb67f59078c249f2a024babb2de02e736e268f6dd70a23783607c5', 'active', '2026-11-08 12:05:39', '2026-11-08 12:05:39'),
(8, 1, 'Jannik Kruppa', 'jannik.kruppa@gmx.de', '1982d3da49efcf07d74e26a3be90aa6b4398f2d9124ce5a287cb953ff301e08d', 'active', '2026-11-08 14:58:25', '2026-11-08 14:58:25'),
(9, 1, 'Christian Klaßen', 'klassen1988@hotmail.de', '5ecb1817e9316cc7c097475e762cc5cc6b02721f09bc658f6cfb926ab030dc7e', 'active', '2026-11-08 15:01:41', '2026-11-08 15:01:41'),
(11, 1, 'Miriam Klaßen', 'miriam_klassen@t-online.de', '51f73df673090aabd5c48219d9df397b0a4120078f6acb86f75ec5f04f5b133c', 'active', '2026-11-09 06:55:42', '2026-11-09 06:55:42'),
(12, 1, 'Jens Giera', 'jens-giera@web.de', '6826f58c2fffab023893e7770db540967d41a11063f70d9adab90f6ce115ca91', 'active', '2026-11-09 11:04:43', '2026-11-09 11:04:43'),
(13, 1, 'Anke Mlotkowski', 'Jens-giera@web.de', '33b6e51022838be1218f4a181689681854949d07ef06bd4a6cb8792e3d08434f', 'active', '2026-11-09 15:55:21', '2026-11-09 15:55:21'),
(14, 1, 'Bastian M.', 'laura.michaelsen@aol.de', 'cc394aad671439dd7deaeaffbcd72cefa7f0193901dcd60e3139391f3273b55b', 'active', '2026-11-10 14:48:17', '2026-11-10 14:48:17'),
(15, 1, 'Melissa Golücke', 'melissa2305@gmx.de', '659ccbd4611d463f6f6c5f56b0748cfe98b0871af81f00f5673f73ffb860aa44', 'active', '2026-11-10 21:03:57', '2026-11-10 21:03:57'),
(16, 1, 'Sally Voß', 'sally_wittmers@gmx.de', 'e68dc36de1a46ce3cf03f4ee7203a9c4d2980898c2afa807b7ba8fc6b0cc68bc', 'active', '2026-11-11 08:12:38', '2026-11-11 08:12:38'),
(17, 1, 'Saskia Schütze', 'saskiaschuetze48@gmx.de', '94bc3d5cd31850b4cd407a8b81f23fa0fe5109f99bb0a5e539aeeac2d0711c5b', 'active', '2026-11-11 08:13:21', '2026-11-11 08:13:21'),
(18, 1, 'Tanja Wende', 'tanja_205@web.de', 'e42fa4188df65c845ec30f732fb0f1c8194aaac33fe3f75a8dfa765c5ed59649', 'active', '2026-11-11 22:31:35', '2026-11-11 22:31:35'),
(19, 1, 'Annika Kapturczak', 'annika.dirks@yahoo.de', '95bb9c2a415bb8f3c8c58761dc8c081cdede462ae1028b46411511d9a30576dd', 'active', '2026-11-13 08:29:48', '2026-11-13 08:29:48'),
(20, 1, 'Wiona Döring', 'w.wortmann@yahoo.de', '4ae3aa35c4bc63dd92fa71bf6cbe73201a87ec8676447f92a1ef6d08c9d11212', 'active', '2026-11-13 08:32:26', '2026-11-13 08:32:26'),
(21, 1, 'Ann Nagel', 'anncatks@aol.com', 'f446c7ec6643a8cf8e83aae5b23593d13c2ade4243c58d1c653e86396c6a4115', 'active', '2026-11-13 08:44:09', '2026-11-13 08:44:09'),
(22, 1, 'Felix Lackmann', 'felix.lackmann@posteo.de', '904c8e7a4b3a36c6bcae3d4827e3c59bb30c7bd970331bb9855cf47f376bf277', 'active', '2026-11-13 08:52:46', '2026-11-13 08:52:46'),
(23, 1, 'Rick Chamberlain', 'rick.chamberlain@hotmail.de', '35104c9a8e7a463f89e9f9a7150b7aae7542b7a7ec224b25abd38f4d7213226a', 'active', '2026-11-13 08:57:43', '2026-11-13 08:57:43'),
(24, 1, 'Ann Nagel', 'anncatks@aol.com', '633c144682b752086a5a8e1b02e529b3399101ce47c2ca85a515422893d99490', 'active', '2026-11-13 09:05:12', '2026-11-13 09:05:12'),
(25, 1, 'Marvin Nagel', 'anncatks@aol.com', '6c9bcf4d1d7be0512a4d084748a25e60b401e8057685049be350a1a03e012e38', 'active', '2026-11-13 09:05:34', '2026-11-13 09:05:34');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `helper_stations`
--

CREATE TABLE `helper_stations` (
  `id` bigint UNSIGNED NOT NULL,
  `action_id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` smallint UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `helper_stations`
--

INSERT INTO `helper_stations` (`id`, `action_id`, `name`, `description`, `capacity`, `sort_order`, `active`) VALUES
(1, 1, 'Aufbauen', 'Die Gewinne müssen aus dem Kinderkeller im Kindergarten geholt und zum RTV Weihnachtsmarkt transportiert werden. Die Hütte muss eingerichtet und dekoriert werden.', 8, 1, 1),
(2, 1, 'Tombola', 'Lose verkaufen und Gewinne ausgeben', 2, 2, 1),
(3, 1, 'Abbauen', 'Alles muss wieder in Kisten verpackt werden und die Kisten müssen wieder zurück in den Kinderkeller geschafft werden.', 6, 3, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `members`
--

CREATE TABLE `members` (
  `id` bigint UNSIGNED NOT NULL,
  `member_number` int UNSIGNED NOT NULL,
  `last_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_enc` text COLLATE utf8mb4_unicode_ci,
  `mobile_enc` text COLLATE utf8mb4_unicode_ci,
  `email_enc` text COLLATE utf8mb4_unicode_ci,
  `monthly_fee` decimal(10,2) NOT NULL,
  `account_holder_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bic_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_years` smallint UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktiv',
  `mandate_reference_enc` text COLLATE utf8mb4_unicode_ci,
  `comment_enc` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `members`
--

INSERT INTO `members` (`id`, `member_number`, `last_name`, `first_name`, `street_enc`, `postal_code_enc`, `city_enc`, `phone_enc`, `mobile_enc`, `email_enc`, `monthly_fee`, `account_holder_enc`, `bank_name_enc`, `iban_enc`, `bic_enc`, `duration_years`, `start_date`, `end_date`, `status`, `mandate_reference_enc`, `comment_enc`, `created_at`, `updated_at`) VALUES
(1, 2, 'Mausehund', 'Anke', 'v1:EksAUWg4MrL3Y4DKGvthxaoNP6cvUstEhU7w7khJl3tCAFcuF0Lp/HjuXnanfWXWtw==', 'v1:fS8L12J0uXriVt9j3RxTjXEn6VB5SvPM2TJj74QNhB7i', 'v1:riAZ4JN3+IiZ7RNlMsu6y/0mNW/bN2f7H3oYk0h4mSn0AjmL', 'v1:02XZ0kPORezbRnQY3+rwwggpStpYopq9OCeMMakIiTEZSPBjBbpGsA==', NULL, NULL, 3.00, 'v1:V6/h/lnX4WIIoWapgFiUCZYFx99iLdIPgHBNopsS83pl4Q7SE1JC7VuZ', 'v1:QFKjgqkk5S+eXw0evy6m+pgeKT2r8tdvOERp8tN+U9ajW3Yjot1I0IDfC3cBzg==', 'v1:gip9dzMl5PTnb9yJZOxAu+51Rp45YtITMPTJurVokGg96Eki9t2aVRh47UREyAf8u6F/xeXL+A==', 'v1:NcS8Qh4tfCmOyp1/HjIQ04sh2Pg2p5oCpQVEuCCBfUGDqpCIzJQ+', NULL, '1996-02-23', NULL, 'Aktiv', 'v1:bv1WSEeZFvu9RrpibilDrCkuG7WPZhcqLpBwoO+JJWmjTrSQ', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(2, 3, 'Schüren-Hinkelmann', 'Heike', 'v1:0bT5ga5tH/g8mGAgG18c/l1912zkmpY9XjUQADjqbDPasIzJAX4sL8fFtIxORg==', 'v1:8y+Ij5C2iXBhJv8jgUq6AHOUhALNL2bInOqoH2zNWQes', 'v1:el0GJYN+gfVEag7P4SZIzfCjNHgQ/a7YTxUs7MmhExSGIk4X', 'v1:aECsF0Hz+F8Y8se4jz7urGMjc98ocgv+ujjLyhqNV3RWtNeiG5ItYg==', NULL, NULL, 1.50, 'v1:52vPuurExcXDtq8KKpeCnU2Tx1RF5Qg/sRZrfx+rV4QNhSA3oLbA6KWYsegVozOFUl9+H4E=', 'v1:SM5hmIiN5x8XvtwWggA/+hgFy2U3BT20bHHtbmXbAmPPHlzM3IjU/y/Q4V03vg==', 'v1:Oncs9vzt9rZ1QAdTAEiy5h6SsZWkuiE8gI14ITvBpwnQmPOpMMZMh0bjJa1vjZjYmUh1kqICUQ==', 'v1:nFz/YoAlOm60xtvMP2gMZ00TLsEwyGcQclqCiiH2Mjd+OBq/QKrT', NULL, '1996-09-07', NULL, 'Aktiv', 'v1:EMiNuEubunAZNN9UzDL60GsAFk+5U102mS/Be/2ORwdHTILH', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(3, 5, 'Witte', 'Gabriele', 'v1:E7zJYPovz+tlbiNH1RdrK9KB9hQkIzekD0X8khYtrurXHNxo0zXpGRSu27c=', 'v1:iyv68dZfr504fp3ORAOyrAajLPiXV1nujOHrFYYnztZD', 'v1:QmoG242eFhVlOoG+euuKQ8iq2+c5TSAIL7gL3obwc8METWCI', 'v1:iA3ALwva/el5OrpxBIlrdUbh/twVToMubbRL9ApPli8vwDIabWYsXm0=', NULL, NULL, 5.00, 'v1:d6QqQzy3KmPJO7LqRMzpIUCdJxg1I0sDsaindzYSiXtplmuNdxOL0YS0', 'v1:hLoC47+a8fwGDAMZmOn6OThhHdNgur6LsH4Gwk1BbPf5ETidmvV/3b8pHkqXNw==', 'v1:/cyC3EALKK89Y4VksJbFyUZFVkHeCjKiTp865sp3VPwmTrK+qvhGJj8+qXy6j3pxZqiEqrGcOg==', 'v1:GHUc8fuTY4dpNrX8dh66hlhPd+z/OSlyj2M0K8wuzNJe8pt8526t', NULL, '2010-07-21', NULL, 'Aktiv', 'v1:opwtFKmVi5jvt+QBlUadrVAMSTil2L19t/bQL4OqBRmH5NAq', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(4, 11, 'Baas', 'Frauke', 'v1:bcXkgOwb3+sMydRSDyv95j0CFCe9nBE4gTGMdVNTNFZHQes0PbKwBQ==', 'v1:VvpW4DmljDgaimXj9zCo2VJetguuP2hXKDexpwfYSrqU', 'v1:DQuzguQzr3usaPVpW3ZOCcvNmKYjF/zwAbxngPdUKNAoCAWnGYiz8Fz6gBs=', 'v1:FlbzM5howDRGpyPHR74WIe8yX+5NDt8HQtJS7BSF5AeyOpjpTSXDZvo=', NULL, 'v1:RTdmK93+jneGiF00gUfii+h3sFYnAgrJoBLSHHNevA7b13mdVYtGcLFO', 2.00, 'v1:Ifife0oBsb9SODkSa16+Cgn2g+W0teIPzuCtJiNJsWbzk3Q6r3bN', 'v1:71TFCLQxIlzzRtRc3bukpvy3dth4+yYafQiM+xiD6ZVdR859EWMp7LZGeu4oAA==', 'v1:DM66Wgh43yVLJcK2FOtjwOA7rlw0P9knNsoFMKqlwSGX95hXnEuKhERV3xJOCUD45koBjtvUDw==', 'v1:J1VECAI9pohPyQ4e+/cDsoQ0tpbVcrw5B8/kpPH21LqkiWtNq/NC', NULL, '2013-04-20', NULL, 'Aktiv', 'v1:pUDC/YK9BCCgKQPPi5k45ZNkDqahqJn30+x5TkKqoccpqK3L', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(5, 15, 'Albrecht', 'Christian u. Nicole', 'v1:4+utqkVs5FqE6wFGEpaJ8RveBpbkbMVPrDCOb7gRpnd3lbkmrLhQ7M11zxHg', 'v1:gdnV4L2Z5ofQqUwqKiqodNbCCL/m8wFRDqMaNlUZVQa6', 'v1:dKdJZW3RcrTH8zRoIUYdmrGPQrosaLFRutCdyZy7ut6lRxn5', 'v1:jfVIiTGdzRIxoNQmtdwrsjusrlTo5ngVFihnXJms+x8D7kBpEpgzzw==', NULL, 'v1:vT2gVlnDXc+MH9mELB5KLvdxaQAMB5XoZhI5Vw9TGc9iXhGzlM8cGfaZjDthzmc=', 2.00, 'v1:aCa8KpbzviuxyELXexvJELrfj2O3MZD8HLLOFwefyOaIkKzcUeaL5fOAK5qZ3w0nJMYizYiACi0=', 'v1:oLm/HLg2M1E36oWW4r2p8C2LpSHKdOS/hT6APYbk1EYArIVIonnXRYGDthNMdQ==', 'v1:co62VpdUPmEjU9zeqzxMEtZr8UiVXw25XxDNxMdW8eB28MCVwOlzSkjBJrO789vvBV7XCplisA==', 'v1:wK42Mr22tlSIXpzLSikmx7Q/3Er47qALd3U+uCf+xWaCXc2tZ0T8', NULL, '2014-10-01', NULL, 'Aktiv', 'v1:MPHIire87zFXxCHvCFEsdrXKHDKPglLyiT+MdJXIGnAJaBQK', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(6, 16, 'Gröters', 'Daniela', 'v1:RXrVvAomM5BgUwWRScb6vMesxbBAKRrkH+vfuOFjTyLveWkzcAbVymCweg==', 'v1:5AresEbNOhT9ab/uGQlDVGU2m2tlWzzYXkufBZlO5vgY', 'v1:vcq5Pb6u96+eoVRRtOgpIpOQGtggH9tp9isgoRC5SSrsQRlV', 'v1:R2FmdFy3kgI3DoEDZ06CmWBvSRedT9Q4IjT/UcSNG3FKumujo6IZftw=', NULL, 'v1:/IzjOmito6+9fa+afcLGxxdsU3Tb2P0dmok2KagfAdfzG6GuUB+jLrB0YocmhxyyqGk2rg==', 5.00, 'v1:Buwl+AIdKGNPfNKx4xjh2sfQovJLj5U3ldqnI9Nqd0yY80Xq6Y5jOsrkJOs=', 'v1:CG2qUVpCaiykfPJS6Ro+AIeVUFUOj02FIM9/pPqHETebc8WfLaRi1YNaqdbIng==', 'v1:dM/I0szPEjhL+050HedkWhoecICeL5/WcFeqjV4QR21jjeKFYAdfC3zFwF4TdwGpPvWSHkfDjQ==', 'v1:/zp5lCR5LLHl84GTmmrcP/VTCPHeomlbT5NWDkqueUqd/ONGKFab', NULL, '2015-08-26', NULL, 'Aktiv', 'v1:CPzCPy/jZXxlnXarVO4NbIeHyn7pmGEpZVThnjRPT9ocXdFS', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(7, 20, 'Anderheyden', 'Cecile', 'v1:2hvdXK9VbruW7I3ofTQnrzos0WZlEAQt6XR1RRHDdCdoDUpa1/mILqnHjw==', 'v1:+STA4V80G9V8UoiQbKOaHsjQegwCembBXEhsjZtuOmKc', 'v1:gvy+FDCULrWDoRt7E9M96DFxmq6rDpvpyjqOpIc620opUeLV', NULL, 'v1:/dk+bcQd33WJeTPnDvXMxBqJ9H96LTZrrBOwKDIgt94omSLkLJMmwV0=', 'v1:r2V/1U72Lh03WVZufSlEPPH+yxAzi6tEvXtbX1bI1FXmftPKcPFNVH5vO6Vb5JU/4n2Vjm8XY10=', 3.33, 'v1:p/s9mZnUwTkaDTV+1rKQAgaxkQOanO9Podxji/F9flLdVs9W/HOpiRgEyfT+5w==', 'v1:mC0c9v4rmp+G/eWmDovt3GpYygoOFqqkDUtOBa8NwtpffRvM', 'v1:TPVKIUb1bhL7w2pWnqLgodikoB5MLgfEcb+859TrFKy+/7TZIbXMRCWNTpzxalilrrELm1/Q/w==', 'v1:2T7EGu6to/hk+6eGfbbSPgZBqYYMaM00MRhgj/9Bi3H+d1NH', NULL, '2018-11-06', '2028-07-31', 'Aktiv', 'v1:oP6nQN0XQAzGWhNSVPC+8jTBKFdaE8zrZdAKlaS/ALzLRx1X', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(8, 28, 'Echtenbruck', 'Martina', 'v1:1QExO3UCvZ5U/dtzeH+AXBEtaoj7Ed3Kvr+N823amH8q5sXeGK8UdN0=', 'v1:KaxPMfjxgArxUCmOzOW6/MZF3E/zDtq9xrkXXhHqNwAq', 'v1:3wUFLIRBRkvf9MO9iUBonmD0nUHsMjxiEIq1OmeRBpj5KYJx', NULL, 'v1:MXbv7ChJukWPbFiQuBvesv9Z1jlVzxp9nkJRNaXgWapv/MuZE8yrGEk=', 'v1:cGYJ9p1BToHhN25ab3R0CfnmHnHWim3T+/4Hp3lS/Xz+O+ojQnQ09YqDUbdf97qPpl4P', 10.00, 'v1:FCMJWRirCUNdBOMhNDjhRmNpZqQWwPn5GpKK9Tt2FThJ4Lqu65pOrsyOrr7KyHw=', 'v1:Zn1sR7e0S8soWuvEeXgNtMP0qT7vby9Ow3EW0SrL0Mb6jclAq9/iLkyISqwR+g==', 'v1:O+2k7sE7topLNAZc+fhiIh4bRYhRnmAabcuHuk6lQQbj6h+WMidralI6iMR7rfVl1foMmKoq+g==', 'v1:m7kzzNRlM+iDBkVc1ym9Dzu662RlRMoQhGvigBRazlDaH8/ONzAw', NULL, '2020-08-31', NULL, 'Aktiv', 'v1:2L74Pwmt6F3ji90/63aopPnSgP5+pMk0NjPzOLa/MSrRYS3h', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(9, 36, 'Plenkers', 'Nadine', 'v1:eObqe5IasoNJmZqC6hEoeC1jOMqVlTadPnSLvt5XSJKwlIcTrqjAsBTAjA==', 'v1:7wzyZlj0wlNb/Wn9mpV439IkOiq2zq8NX5D8vCP5vK+2', 'v1:Al3KT9qbqv0ycj4fou1cUqR4ApobOBrg1lECVAmPIZEdFUWw', NULL, 'v1:Zrs9bCRMEfRItU0KZrxo8By1+S7xxs/U5k362V9lBT5SbqIrmIi1Wa4=', 'v1:17bpVbtao+khUjWAOHskKUg661gnrxfp7LG228LHAq65ih8Yvy4VlwB+IHiseNEFsmk=', 10.00, 'v1:sux9GpG3e6liCOJuhM2okQ2/jw2RB1B7sOlpSikAdlM+4JOkQgLeSV19qA==', 'v1:4XdySeJ4Uk7pPWTH4vgMACq4s0KZzMrSIMDm7D/H7nE0FwER2Yqvy2ByiVARew==', 'v1:TeR4APAdVn35md0IBlXGofQO3BJyetKe2dYKrdk0H6jYe7rKh39CvFhRGgif582yPCrzyBietQ==', 'v1:mR3q7KLtfSy775RIiDQqU6cG/d+mno9n8Whwfm+tNfZfmUUUHZcx', 5, '2022-09-27', '2027-07-31', 'Aktiv', 'v1:E0DsnieYKJ1UTWOIlAfKG3RYsxyT5qNgPhKjiiAkxUvV29u1', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(10, 37, 'Perret', 'Julia', 'v1:Xz6YobteSid17fuE8WCvEJ27AJj82fnQUV6jelS34GMx5ePdIllzCBNCkw==', 'v1:ZisBaaSy9wbLxrURANn1yYsoITU/QLaiS0kt/SBscq+W', 'v1:pb7wbztw2c9m/IRnhXkjNw6NZrN67qcyJKHZ67IrzgB9FGV0', NULL, 'v1:RZDCLPYMjOU+qBIpDsAcPqVdVeZwxunm/JGo+tYx93Dqh/cSeVqXwEU=', 'v1:jFNVqUtn76XtgsnFaIIPA3kj04c4N+P62w8mYZVsCVjPlBHNHvIMRVlBOG6Def0=', 5.00, 'v1:9xG3idnC1fk5vakRmfxLmyQ/ma0m24X0tVxteEqcj7xPqbo4dWKqO46f', 'v1:810/Qn6LkSk0ZrWZ5dslNs0KqEiYhH1dZTPKs+aDWIckx/VcHDXlliw=', 'v1:OaoIwDgM4uSnBFTbdMbkRgtkgzl9pFtC2YDsg3wQnH/X8WyG/CN+pQFksEDg2iGDxdnTRjnZOQ==', 'v1:ud6L2dst4ng/RCcf7yFfQ2QqkuLv6WBkw+AYUO8u2cm3I+r4uFHC', 4, '2023-08-15', '2027-07-31', 'Aktiv', 'v1:Icvq8GkbhZdNbMlWAgB1TgkAgiH2b/qvZtbsV5YNqBGj4doc', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(11, 38, 'Wende', 'Tanja', 'v1:x1/pvOKnkkMoZZF8edRijqJWmKlQ/z05bjwmnoKMDpL7VXB6/3ipQM8dNTI=', 'v1:SxVm2olZDQlv7zbxEGt127CS4f9+OIGzQpwMGjkrIGoD', 'v1:UF4n+jDFJVh5/xLzJt3p1anxvUScRwBceRvuAWQaJEPMZ0mS', NULL, 'v1:3flWWviBowhAP6IcL8JRLOnSU0h5W+QJKwJ6LUkT+UypnJdFev01k6U=', 'v1:FReZeaE/2OPb3qDHhVtFN9u54fKVWX6qJ6X3vLeoPgEjKeQhXsKvzAWOkok=', 2.00, 'v1:oCrZEIFTG1yJ4gckdirZ1kCavXtoozJvoLmuPh9g8SvoV5/w+1hQ', 'v1:1a1HDQ+eeVjqYA4sl+fb359rIYj1MkyuX2VjewuIqKhcwMWBopsXSshE', 'v1:rCI/qEVdye2z5y3FeVegJT2deQyaH21cOu+I0pVeu+40IipfpTIaZSL4KC+LGrKsIcxEq1H/Gw==', 'v1:sucCyd9zJLFVRz21cuX5EYDnZK/8+xyk9BZ93pJeDCM7vd9+iI74', 6, '2023-09-18', '2028-07-31', 'Aktiv', 'v1:UpZsrVWDeHeLewzrwvXgIABd9qAsyVZjF4SAdidMuTs0Noh+', NULL, '2026-11-06 15:38:02', '2026-11-06 16:05:59'),
(12, 40, 'Kremer', 'Silke u. Christian', 'v1:FP4J7cAIr/CbDwswD7UzuEOwngYGcmrho3qrA0jxjQLpLwx2BYsCwA==', 'v1:3c0DpB/NF81e75YLTLnwBPadMzmWITH6ziwMwYVMJhcJ', 'v1:RM8Jji+CPQnff8bweKwWPzyS4z4Xx8FV+AvFghABdK0uSNYU', NULL, 'v1:Uoa7+7InuEp7ykO1VSuM067ST2m+CU7DYG95yHrX3jWKVk0LXfZoKA==', 'v1:wWwtQSqdBMscudPafKIGG9teGvv2lbzU/4Wp5W3Ly8XYb5FdzceH7irVgWJS1PzMZX/M', 3.50, 'v1:RgDs4+AVlsriFiAkLSbPt/85M0q0+/Ri+Ss5ZlTmacqjIAlJumI1x7BCk+NAz23KEyfjTWI=', 'v1:D8KVq1eOY5XA70BogCCOMeUfpBQids/P9BPbR+mf0d4xSt1HsxqLN2nPHrD+xcg0Pw==', 'v1:4zyLEWZLp2jsqEzkkVdKUFr9Au8NalLNwustnBb6t0LPdD/cvLyYxDGLjYDI9pxQQ86kOS1IMA==', 'v1:WF1+4oPa32tYEkJUKVpMsIqyVEgMDI8bpBd40ZuopfYVzC6KxJA=', 4, '2023-09-27', '2027-07-31', 'Aktiv', 'v1:z4T3zILDqZZGrvWA9zgqx6tqcYDItXl2koGwmj4PFSMRMHwi', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(13, 41, 'Pfützenreuter', 'Frank', 'v1:ob/tfNRhZvBnEQKFljXx9lmZt233zKZkNnthRgkvgdg7BTky9mulXNvzOpay', 'v1:fOkTyazypsuXt2rHrGRPbEFOev9JmQcoIlgZsJXtKVTb', 'v1:NJ2AgGXzAQAdLYM6ZLdo8QU9kxfXY4PX+PEWAu3SpKw7o60o', NULL, 'v1:e8LicU85lfBEVjGEkPFCGhvGMKQ5AD9usrnBFAWvZmyb4SxfNcJRPg==', 'v1:m0FDGHyhUN0a2C6L1iRwfx2QqJUqxyT6QszyyxsbaJk8rX6jX/J71+FSLl1Uhp3t', 2.00, 'v1:AMAOwVGmrZxqmeKJ7/PbPYNhXRvO8TpEPjVP6ehJwoUrq3ElUBkyMXOHaq/KZTsv', 'v1:ry9tZFYdzm0nPJV2XkqcsCnPZVnNlYcdf6xRO+okJynFvuKTthrOyuo=', 'v1:NscaqdJEECHsVckayBToxKnX9IJiLVNfb/wkjU8+lYd8BZVz8giTN+OpGkbmsU06UziWC4AKrA==', 'v1:HNWPancJcHEl7uG5XHeVOqycVlA7DFDDtSJZsPr6fO/XlfCbzpMZ', 4, '2023-09-29', '2027-07-31', 'Aktiv', 'v1:mRUtvbIuVhIbOvdo4QoIvxUmwWdMkUNvd4YJnm/zXiVHcWLx', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(15, 45, 'Börsch', 'Jennifer und Nils', 'v1:gniuyGPH+z3B3IRNXeNOnpYZu0zQGxqkgr3FV2MJHbywyRv/5s7hUueh9LGyRhwJ', 'v1:r0vQaLnK8mkoDyGAloMpMtvltp9Xb/ijUOzn6PCx6u7d', 'v1:c5YJs4vVUsxP9aCTHXZNTRZzgCSSIccgtAqxWI/PMBzqYpZs', NULL, 'v1:i0CPha6omJ1mevKllbM3YO3pnFE+5cQ1ZEGNAEQkEqN/l8DCp06gE8Y=', 'v1:aWrJye/EjTFg3NpCsSQf8NjgxDaGr5TYO4+A4qKELotZjG3xwfBRsNu7oixOGTPn', 2.00, 'v1:QeqzSBFNiCzK8VuGQSqcwAIEveHB2Km2HMSH42zDGQ7sokLpScFxDpjjNLgs06f6dGex3l8=', 'v1:UisnSI3IxrdfKI0VjqN+v1X4IKaMy+i4piknxXuzZ2I9+O4O', 'v1:5+0mJ5ag85KV2tG6EY39/gSbyJ+k0NRLF1Go3KxFBWBlkpyPfn9oHPrWZ9AM4yDGfkPDOpjFJA==', 'v1:dfB0Q3SGNP3jFC+fUQOE6+h6StLPdk6uINYHYYdloYKxPGB/', 4, '2023-12-13', '2027-07-31', 'Aktiv', 'v1:OK0Bv4uDbzqeE0xotV94MsR3dzDOsPj5KO4JMayCqwottLRI', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(16, 48, 'Kuhn', 'Christiane', 'v1:FOnpI2F/bwdN5o33LtA0r+5jRrQ7ReQkuD+nlCSAniT1CFZyQMHQzg==', 'v1:2eiM4LCm34taQ8H7fHRb0+rHDM+kR8ELCq8i4WtvzJPD', 'v1:GATH7ZOuleb14V1APgEw3qGRUl8AtshPdNUjtuykvUYuVOdF', 'v1:ffA0ZgwDJJURfoufpae1reWPTbhLPkNJ6UyNQoSPC3rSxKYpbHUFaA==', NULL, 'v1:ol9BDi5oLD2+M8BD/0+0haCwP1lprWutM8+15pOxzQ6CtQb8EqjP/YJ63OBuueUUs15/use9', 2.00, 'v1:wVKI1syoKbOiYVYQA32ldjEZMpwVpNl5D5yCRiSC0wMzTLak6ZVaTPhZGg==', 'v1:HXkJIY/5p1n8p3I56e34znH+WnOgWX5Oua/CbxfawFxMOoorVup9jvZArH3F9A==', 'v1:ZkfxBfZHH9JRJ4m678SidjZ+DkXE3e9j6JrRe20aXoHk8/NXaDMj26TLk2yfIr1b8iJbppvFSw==', 'v1:VBVTiVicu7COtzM81KnmEILeQKM+NAVkr5X/mh+Z7XVE3cbAq7tK', NULL, '2023-12-31', NULL, 'Aktiv', 'v1:qO2M067Zk71+pHnoSlFiXs21zCh7c5KlPpw1wAY35eUQgrWK', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(18, 50, 'Voß', 'Selina', 'v1:6IdyuxK09cE00Q1gU3dO83BxCYz84AJjMCC/pVjDXJtZPKUBHB8QJL0fiQ==', 'v1:2CaIQkCs5j/7K1nGR/5J5ahbv+rkStaj0Ck7T+H9a8mw', 'v1:K3WIV0peACM0hCQiotPFoQn7xJ5hs+n1cBiIcUSGMAgPzqiJ', 'v1:TT8SiNJdY8f6/yGGUO5laY082eQdG0Wx6hH02pR4HEWbHt0yW6qWxS4=', NULL, 'v1:swKZ6ofRlOZ52VzBNmz5nt6CWm10LGNeiTjpnCmZ1H22pKAar8+Q7JcJR0bdF6PnoA==', 5.00, 'v1:B6XoGyvbdWZSrdKQZgG2YSMCEP0siepLPFLF+Rv/yXTYI0JCrjKq', 'v1:92TOAmdQf/M9rqvT0lF4iVHFo8O/DLAHDHR97P5dYvN6bqyfA2iI3Dxx', 'v1:zQikmox6E2+VRVMpIc8lTAVf53+QutaEiwkP82IOO8IVP7wZWmc1YdlrJfFS2f2Jk6GYwCVxpg==', 'v1:oIl+WnHbiAtjKnwXVFaCcbOFMs6pQijnjMWaaGfFFLK+jp9vq/2l', 4, '2023-12-18', '2027-07-31', 'Aktiv', 'v1:i3PItL9E65y9mkQy+Llbydh6vgUkqFWI80jjKm2pJx/5vDZl', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(19, 51, 'Korte', 'Marius und Henrike', 'v1:jALg7OCBOHIIGFQpINh7ZltX5ZGnCxxUBpEakPKkynPVPHDqVojH0AWC3iKuzIoN', 'v1:jYumQzOeg6VDHk72+91JCu1JWq/rO/6gH6MOhleydfCE', 'v1:LM54evcP+qCoT5GYydmGwjrBZgHm4JySui4PKUNE5TmPIpat', NULL, NULL, 'v1:jFJZ8Q4o8S1wqU9TQDJ/QprlbXvpEVgBoLo6lfvt2cUCAaXolLG6TCrARc8D9nE=', 3.50, 'v1:DDUndmoMyGNKAJHcjeFUqr2zFS3FvrlDAKIHlZT45X+m/a2JXmjzC0Qe4g2k9QWVZ0w1YQ==', 'v1:PoexlC8Q1UsejMnM20cJNKlNBI9riUX8F/bZ/E0YxMDQ3zuuIrTpAgACP/N3/mjJUkTlJg==', 'v1:c67o0ZO0S95IKVJk863+hexaB87omh2m6f3jMZqLm1/B1w5oWNm9pdQ3Tv29xrOFl0I4jWN63Q==', 'v1:dTsaoyp5Nha0stwl6VK7jsRMBcJ8jtfJbCviyGN3N6Blx1l45ifB', 4, '2023-12-15', '2027-07-31', 'Aktiv', 'v1:A6EG3X/2jIV9MGynKjn1pfRVnFP6HNE5plQkt4XjoD2yF77J', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(20, 52, 'Greschuck', 'Carina', 'v1:uPOl7Gtt/R+KDukxCzqYstCYfWZg6ohuPq+LXRi9WV6z/VW1iSaljg==', 'v1:+UtJycoII2SfsPrxVxBnPD/hf1NdHxKWF8moqlMbOo7S', 'v1:zmjOlMENjk1LZ5txv3QJtmq0qa1QyXyrYbTjQ1Kzbp/sdP53', NULL, 'v1:5upm+QHu1owScNQLatwAP4vSUDF8AJ27FW6TWJ4GRwQbaMpWYBZuDpA=', NULL, 5.00, 'v1:C4uR3yhQZVitUaPL+8cbga+rZawjlSCk/4t1lasrMhKBybspNoSFGZWaiK0=', 'v1:mr5Juq1PHUwkjLOmKI+2HJDBbkaFngMr3vZ9Kkn5ZU4VaDAg+vlylRZalQI=', 'v1:mOoG2724cOd9+jPd/BOlOg5OuXNNRekNuvIBncrOd2S3dIP1epdnlQNTXzRtizRyxsmni5W3gQ==', 'v1:Sapu1vMZ6D+9MtXUWvSVbVlr82TA8QfLPkruQUjxcdr8czNAMnhc', 4, '2024-08-01', '2028-07-31', 'Aktiv', 'v1:wP556aWsyclz13PZHsUAI9tR3WY8Fouun9Uh2lzfwOkSa8UC', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(21, 53, 'Kremer', 'Jürgen', 'v1:HEzXMi93wmutmDXV6WUNIHRpYu1dGYUqEN1TuGxmA0ucnv00pSThsvc4', 'v1:5A2Sf34lIBHCN8tyOus14X0DzHcELt42oJ73Y+3w4L4u', 'v1:XkeEWccYX9bnlqDkal8MNIWEeOItLdOLyZ7wwB64vmisMeHxPeniUQ==', 'v1:WVQi8pVkJRLz+/CzKUe6xybJ8grh6vuMTe6IVLT8WO1E02YsJKE=', NULL, 'v1:vgAcymvdKsUN9RsS1e4KnrC0pfbKkcMyk3KB5y8RwzszHKjEMV/hAAe+25dipmEFiQ==', 10.00, 'v1:7JUIOCxucCSsli5Cl0C6K8zsrc3m2t14gTAi3wVv7DVaS25D+xxKhKu1', 'v1:sMYL4ZFkF9Pgc10JYjj03BM6D3J0fuU4JYoar0zEMWzDD21fahdvQzVg2LSuhBabe02KHPbukQ==', 'v1:Svv5h6ri3rIFtfM1OkzdnVJVFkBQr9JKAaK9y1ZAOtsX1F9sx759PrHD476NLFkIXWJ2HX+LpQ==', 'v1:3OweryuoxpwHObBWel2q0kqtagWMPrAZLI8ZwmqsNc61fNbYZxyd', 3, '2024-08-01', '2027-07-31', 'Aktiv', 'v1:ai9T5wFocH8ouhXK0pu9UVdlYQuWWxF/6eqtCI9RA7HK5nVq', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(22, 54, 'Nikolin', 'Arnold', 'v1:SxBnl+6fRLn1HZItQPoipoh81oYA2QxlXu7p0+PFr5Qq4IWuK8OaJw==', 'v1:cyZHc7NofLDjT6toQ0xxB/kaoDB+z/G9rrXXzgN7XSIU', 'v1:N/O0Kazh9CqMWmDHnYt0HiVEasIhzKOVxnOqZUah26/Ph3qM', NULL, 'v1:IxWbTMQxGVQRtymAldwusjU987mzvQ9TAqjF2mJp3iKbbOr0zjNSXQ==', 'v1:fMVadB9qXalcXDXzfF+Iwr9A6HAFj7WA3fok7y9X+muLNzEtTSA486Z4Fj5uWNo=', 5.00, 'v1:eFq9YBFMYsN8caFeww2FRTKmNsVVCoITUG5RrUlrb/yhuuvD5P9ip7pL', 'v1:6/b+d5mQJ+uGhwQA//B7BqGh+hPqHIn4oeBWThIUz3Cq5nAru+vsVUKyb9IvlA==', 'v1:R3+iiXK4qIwibtPjREdXp24N0pBiJxpnWIYJuDV0aYxjDl5cWUZZUNlHQ5JtPIKSkOFMUNWvug==', 'v1:F+dZ6QYFW1wlikNRiiZjp629xRNXKknytYXV6IZsg3QYk/4XK9Jk', 4, '2024-08-01', '2028-07-31', 'Aktiv', 'v1:zgV96xgVlN6lXHyOGMZ5L71nJrkTM38EgjhVpINtZI0TXq8i', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(23, 55, 'Wende', 'Christine', 'v1:hVhdMOUswR7wuYxpny29YRsqQv32S9t2YKe+rM37rMNq1fHoE/4uEcqHm7AX', 'v1:cuxuG/WULhAFqBDer0XKT7HGGmO0kkFaZtsqMLpDwbjI', 'v1:sAryZjglSCnsB4NUjV2E5ogrULF3qajSkFGvRycYFYiw', NULL, 'v1:EngWOmSFJXLI/zjKtcvHwku1tlcYOBEk71vVDhcMcsfHYkNs586D', 'v1:tzr8PvI/BS9FyHYaXc4avXhJF3s/JIOjoI1TIYw8sBxO4JTisfV8nWQEbcZFuOrHsE4EBR2sGQ==', 2.00, 'v1:tJW8s2gCPEgZa0f96rt9+WlEHvVe1KAnXZBD7hgRG4GW5MzPoEUmllgP1Q==', 'v1:75sKDKNdRlw6w0a1kl1nh/oN+b3X4oexbsT++FN2KQ==', 'v1:oYXwARiZ1RtHQNm90gz3FlVDvhvcozv8LdVH4YGJNEddZlSaH6qUt89dqNiHM+lFzqEzCS71NA==', 'v1:esbWocOMdr/1UwqJBA4Yiq/1KNc6oOVZpmB0qYIhWrDn+nGRG5Od', 4, '2024-06-01', '2028-07-31', 'Aktiv', 'v1:jHVr8huUKgKvMHuSGczlZn+/DEtRvxsiC5JWIONXTdu0cBbw', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(24, 56, 'Möhren', 'Fabienne', 'v1:hODmHwHORj5MyQByx+nTAmf7ISJZSmhVVV1r+iizDVoPag76055g1w==', 'v1:68CYOVTVq01jSARsakzOlRb9pb8vtnSjz8Fia40UDwYY', 'v1:hPQ4Xwo/mGwtTs/rlMkqhi7mKODmiVqhTBi5meVLP6fBuJyE', NULL, NULL, 'v1:MnTQveMd9v5nLS7VN21KzbZgxNxB58ydaYOi46hIkT8YBE5M5JEiqoFLviI=', 5.00, 'v1:WDAhOssiq242FNiO7JxaViCVvOHZh8q4A4T9nzIH6iYv4zLwlL6TlnMdyZg=', 'v1:wOegu4UdmpU0LkKB/bG3LbxHPNzePuTxZD6EuT80xoWHTthdZQnmFOxkMYD58w==', 'v1:q3cfSvQV9O1f9sYzcjQbMaLv+GfEOYY5bu+Vn3eawwrbYryO4jRoX7zAajkVE87oaUTiF7StOQ==', 'v1:zJA4cgMU5D0PmN6UUp3D7/DRNcRq5Ld3KyM8WTMxbyfGbiBqdUhR', 4, '2025-04-22', '2029-07-31', 'Aktiv', 'v1:2Ly5ij4dX3wOF65sj+c38BYAbfdXHEvy5FBxK1DpgnO3pm+m', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(25, 57, 'Regier', 'Ludmilla', 'v1:cciO4aydYjVF3yaJ6N2axJ1SbIDcUZa+h6zGWF3uJRSe/jIEo5Sf7KyQVqpe', 'v1:efl/FfrSvaDmZA1+k1x+qlHEMINxt8FMwNN6teUsX1KJ', 'v1:pEbodGfm0a4NW3Zs6r43Zr0qKWZy+oZw6QbJMwhKLq2LqRBG', NULL, NULL, 'v1:q9Fk3Fb9guoymrubaX7AopZI48KYDoPFx3HEr/WFXnJRdjV4gtNtiQb9iNynFJK5s7/dsTyF+xBL', 5.00, 'v1:ylG5lzb4Qpu7fqrbKDxGkFZ3mmpvlHY5D8PL0T3KzrOIwx4UNHHLnyMF+A==', 'v1:qmvugh0PrKM+4VOteDw8dj64lgr4+JTAAuTjhTOEcr0800Ou89eHdj70', 'v1:3w+FjUA2u14BvnFZ09jO2iI3i8gfByO4VAF02V7BVwtD5RGhxVqXC6KVeT3L1BBPoZ58EuotyA==', 'v1:4k7+ExqncsIdquDT7sncBt4RkRWUXGVHZUznws0UA17NNW1eA73z', 4, '2025-08-01', '2029-07-31', 'Aktiv', 'v1:FmwkWSw/Dvo6JxSSYR1JUEY/kq19GALrydQRfMqOJikqcj2d', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(26, 58, 'Zweig', 'Tanja', 'v1:QG6BcWmgDt8qlEbFHabZkXKJDg2o9FIpLG0JKfrtAsi+rzXzR3u49TKP', 'v1:mpot14OfqoV3X9XNcS0pEECSg3uZd4QEvmRsNywbuQjQ', 'v1:o7tlsYywY8ATt0TGv+RdiGq1zQJ+hs/9MbjzqUnL+ydqeBTE', NULL, 'v1:v+JKaEoaT70aHZ8YcBNDWc8Ost5th/uHdkfDwTjajxurMdcZbZjWrA==', 'v1:iPqv+8IIOHO9abbmbrtRcS6V7RAExMjB9Ox5fhWku3ZJMoX/bjMM7ayujLfI6bsBnggg', 5.00, 'v1:/HJ4+Hg6986YIAKPRU54USWnXLL168AHnJfcUfU62HXY6Bv9Jb4Q', 'v1:ibsoJD30x0xhIMnnrDc7LvJIs480HeS48yNXpowRNfUlwDJyy73j8kQyPffvjk9U9kdIgw==', 'v1:GbNxuy+0Mpka1Od3cUsF5/zuc4K1bfJ/gJZikuon/0Hc3tf+dp72bYZrd7NsYKTM1u/BZqHeyQ==', 'v1:RPNUeBfLRISfJSBBfRJUOu/7zaBIlFBMpeVX8bNZGDcn12wRV+nV', 4, '2025-09-17', '2029-07-31', 'Aktiv', 'v1:o4xAiYE31LgN2U8IuHpQHxvoEc+tkRMuqyPiTp+WO4V7Pebf', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(27, 59, 'Schütze', 'Saskia', 'v1:ARKc628xtBNI3Hx5GeNrzBztRze6d436Vg12/4ZRCRgf/Tv5BBqjkkpBUNoT', 'v1:dBQQnSo9cE0ddaILPGfsejfOZTfAvfxA/QoxBumxirUC', 'v1:UH8e631Tyw+8iT2s39hUrgmiRXfbbJ+WhDb1ROzFqhnQO84w', NULL, 'v1:r17/rOUlB+GL/wVL4xzjEw9mSfp2CKETJlPSDlVROTsU21g8s71VcA==', 'v1:rHn5LeP0MuCoX5/gkmy1kTEMvKkrlmk8ZsJ6IQBjoN84yKM+mYEI6E8kD+g9Qq+EWlnO', 5.00, 'v1:I7vsAkaS7NPeBjE7zJQYqOwTaRcC+l3rK9Ljtz8HUYROqZDDVP8gzejYBg==', 'v1:yTOV/D8yJwYqDKEf/W+BPBlJ4Eimg714GQnqoNibw1IAOKruWWcYS2KIoC/5Lg==', 'v1:tGWHX6mihGgjYGY/1AheCWm+J7XdALJA1/76om0dUuDveotF6ZokTMrNDS+Lxw1X/Bs4nD6VKA==', 'v1:KNnZGTlljGCaVSH8hjdFcf/LzUULinbfAcz8OMcbvbWLjapW9oSg', NULL, '2025-10-10', NULL, 'Aktiv', 'v1:bXpVHHQJSxRcdJ0tXw8ivu9ItReHn9xe5/4CZrUh2+JHcT5E', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(28, 60, 'Hoderlein', 'Jeffrey', 'v1:uIrF5ICiG93nVFxq2N4msCwbhJOedqpZCnbwQyppQPMHH/+CYXw=', 'v1:HRs1Pd3A/plHSXQ/hv/j3NQLJK2g/FfDr+mrEsrQLnE7', 'v1:goC3bBi4l/EGe2jch2WlW53skGqABoWr4cgI2f2XmtEYA7DQ', NULL, NULL, 'v1:ZpdSgEH/2fLicTlFPgkpayFI59XU2WzdhNO23k1xmcszMfzVXa27cS4oOV0YzieedBczmbra', 3.50, 'v1:aQ/9y9fXFK7gtKxG+0y5DRpJw6U4z4OtBIS1nXe71sqQAsf4ahX2ZLRyL4Ie', 'v1:foKCNnJsZh0r9DTadn8PVys7W42CjxPPCMMzXF4LMQ==', 'v1:shFJYvNPe1ZF+ByMbWR3cU03lMuz0uHOS3a0653C95TrPkf4dLatFPQTD4N2chgCVbEyQH6cog==', 'v1:8+xVpMXfl5SWqPuz/DRBTS3HVQrk8BPVDy3l0b0XjIJKF/HzwPUl', NULL, '2025-10-08', NULL, 'Aktiv', 'v1:b/V5Brv+gzokmbnWzb7BEdH+UTSfHkuqTQ8fVgSn9fmdWOzX', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(29, 61, 'Lackmann', 'Felix + Evi', 'v1:OeSxbiOcWg6NWxsFWdL49mMgBEZ4nS7CfdcHiZp08M1N2k6tAY/PKWE=', 'v1:w8lSPMZMqA/ucUAEGXap/Df54H4hf9dCPmzRJM3JLoPf', 'v1:KIInVHWG1g9CjPiLvUQFczl7CAS9zvjGT2dwc48A8YDkkIxc', 'v1:XrH1JywcyzCgrVicVcsYEhscrxItKOfHmD1qT9qcVsghP6wrUmvLLoI=', NULL, 'v1:PkC1Vd3wfXAR3y4Six3qxtOpJyLaVulpFF/WU2yn6950B6VmXi3NRZ0X5CkRhsZWXzmAFw==', 5.00, 'v1:KbTsAKEi8Xe4zf28TBaSP+WYuLRAcdCcBy9UdgzgaOs7nb3tuACztNrP5d1ZrZHs', 'v1:d7aYZ6nszB4lltjZUcYYhsHfO0FKEoUdWksyJprvfA==', 'v1:9LYcQiZl4jnCIFHiwLVNNO3hJql5yt7XL7zpMnbDngW8730ELkt/QKKdqzdDfEM5FD02qzP0mA==', 'v1:JZPEylJ8DFcPXRSjR7oa5R8/TktASf+Tf+/I46AUgOa56tCD8X3/', 4, '2025-10-14', '2029-07-31', 'Aktiv', 'v1:n58c2y4NEpCdTReXlvSTKx8/FHJzD+v2dmn8Dz+PE5Qhs02B', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(30, 62, 'Golücke', 'Melissa', 'v1:2PD3VbqJoN2zmcqQwLV3+/Thf+zkk8JViOxnH+Ufad/XadwovEGlYE+t0sjF', 'v1:23yY7JWNZhOdhjpoO0fpYDeA5YWJaqeFw/47lpGSTGEe', 'v1:+zcGrJjpya4sRd2u7WmPKczMZpl0tcrwOIIVjXZuHej7noUM', NULL, 'v1:tV6Ah9ScqFaELrIYImJ3K7peOleRdIT4Ashj0bcm5Uf6UuC49j1B', 'v1:VZoKG9ZksS4Y5ExgjnNfN+6440DCjc+qziTNDvmiPodbQ3cpT7V5NgtrBetG1g==', 5.00, 'v1:JQoQ8dxryi+VaxM0/N9bWUc5Mpx0n845RQbkWYK1f/udvxjnpVFfOLP/uFk=', 'v1:FUkdIQNvkXk9K3kMlSoJEehsv8qM+Mz/vqc5QxASanRpH5d4tpSBkiJ0vXPp1ooGpcBsxg==', 'v1:EgmqG4XvwjM+zPcaRs21/T2KAXgER7HBNzbQWltYJmb0N4x5P/3MHkbQ93vvfJixKWX0752dxg==', 'v1:gTQwsfU0CHNdQOdRECXbrGjYum8QUrunTjCFL6w4tmwehZr3DkL1', 4, '2025-08-05', '2029-07-31', 'Aktiv', 'v1:NI7UrRXh2va7GzrRjqX5JDeni3z6DrTSGWBPv/VZEmpqb5ut', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(31, 63, 'Schroers', 'Elena', 'v1:FhqWbJXmXfP2k/gMf4TLjhR5oGkKASz+etsPZpohMwGFpIlLiOXJPw==', 'v1:zCBL6CxviYFviSlaeLXDDDg2t9iVrFcAzy/SYRDzxD2g', 'v1:GSk7BkQ6y90fOnOOhoJVnYqNs9GJdLU6xo4GJHlvGVKdByVX', NULL, 'v1:ybyWNeuC1wcUJq6MxaJ3RCyRRrWjn6ggrgGBBb6zzXlZNTp93VCSh2o=', 'v1:rL6snBPOcStxWyJIK1Mx90WpyFwcSdJ8izL7SLxtmAxsNdISkrg0GXP8WFHBt6qamc0CTQ==', 2.00, 'v1:7763g5qNuJUMqa3FdxjoOJrdRCZbaS9IQHXFxlH+3tfQiUKAOFCiDTdd', 'v1:auDPybbQYWiNtR1u98/Cp6jyHvqvM6jMkdNCg6OU9kPLZEbzzEULC9VadNT75n8l8A==', 'v1:aufFNzsD0tLuTbhBFRhpKdRg4dvHPOYTvNEqF9U97wrRb8FiaFTAjm9TDmNfYB8xS2s+cxZejw==', 'v1:r34nM9xy/qrEf5e+WxBor2zu89S1r9GQoBWMn0B5sMnILNGrsnLc', 4, '2025-12-01', '2029-07-31', 'Aktiv', 'v1:v30IoweB+y7rpu95vqMAzyKWvYVAsVEwxNAunoVHy09f6B8k', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(32, 64, 'Breuer', 'Inka', 'v1:032vj+uameeBnVK8yzISsMoVwHkE8aBGlBXlNJ989TlQr2XoRmNG5qoafIE=', 'v1:632+eFa6Uc3d9e3po9mowVh5PSMwXkRbKIAVNAOA5oxG', 'v1:ieH4H+XOo2NCfbs6JXD00bKhDlyuWFB9BQ6tk1W6DumGNkZH', NULL, 'v1:j2FCY5BVh5b7VvXYmxU7zQ43i8j3wgXdegm20S6RZEkDfbPem/QyuA==', 'v1:Vx9rrMlR9RSXm6FfMXWo8gL4eXqeKNCwwX70XW+dD7tIH4jiIUL8eUn/', 10.00, 'v1:KyThJR7T6wvawkyjhyKqFK6s3Qq/ZJeVM6/b9nI4e6kWrldlnJbK', 'v1:5o76HWUarUI8D8SJdD7B+WXZwqO8d8cGQoysQuxzbA==', 'v1:glKDltCYlM3FvtXjXiPP66BQHRuvMF6p3rJcN88bgiTx6pUGcVX/FaMcZrB8msZxBZaerha6MA==', 'v1:R3i3t6UzO2QUioq5B9lBFPA//kuaNww7yRaYQIbBTKCmAa2MsVbA', 4, '2026-03-03', '2030-07-31', 'Aktiv', 'v1:et8dTHYpQHrdE4bXYoDzXmxZasHSaLOM+kGYWlzbezrOPNqF', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(33, 65, 'Giera', 'Nicole', 'v1:YUVzAfNSnGVVOBIoG+aChOK7dvsPRxbVG1GmYRd0xVXyU3VLuCpartU=', 'v1:VDs5nR4gtGlnTNbroR5fjL5ITBaJXaxPz52jLtrtEj2N', 'v1:9zZTTVYO7r79YPZkIoqS/7q1vc+UAaE0IJqhDVqeQNLz1WXX', NULL, 'v1:sGRUwS9bAZMlla7zXWqWWCv0jH9GfIX0hGAf/CPIEj7trSwOyGHpCQ==', 'v1:xBBcR0A6pi9ltzn/JBcYkSs5MfHKuQYMg/EWYxGWmaaOH9DRLgr7p5E3glxhnRzRoQ==', 5.00, 'v1:B175L+gTbsbJAxb7XnSJ2rM7dqX6XWbVPu7fDh7cRQUvOVPCJTSWbA==', 'v1:TBR6nqIK67fysIgm0ZKj+5qYE9jM48zGAFb0s8rz5w==', 'v1:T1f2O+wCOECMJ6Mpot3gYJiObvc4cNBRpMunjWE3Tue4GtksVT4jxtl52Vl6TCpA4AmGM9kxVA==', 'v1:7Yf4hJBBefKpRazjbk888lZ3Gcy3gcgnsm69RLWFNGNMLgNwCJDR', 4, '2026-04-01', '2030-07-31', 'Aktiv', 'v1:R4SYuR2zIA1iyJsL7WbMglNSNy7ARQDK2y1h/kkumsxSKqhu', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(34, 66, 'Kruppa', 'Jannik', 'v1:t14xWOhpwd4Tk0GHzoAJGk/wR18e0xJkQ4fb/gmcgDrOtFRGYAAT8IFrNHY=', 'v1:xW/+TNsNZE5PzDeStBKVjamgdFJemY7NDZFOnkLphy9S', 'v1:JiFC0+OCUvEvilOVmUuU2879fuumasObTKtKAhOZ7ReCFMQb', NULL, 'v1:gswghgv1/KwLxZ1rrMm8LJtnBYMN6DFX3mEKSJqv/6/Qa+qq0o7NDw==', 'v1:Ee/5ceOxdm7Kh/rUKrQR87L0TR24EVWQrNchb79f4K3hc+6eOf0JX7hTmMmpEK1U', 10.00, 'v1:RUdzmAsqTQx5T/EKV5tFitNGSwF6FQnM3FKDaodAHUC9DNCGgdWBZKE=', 'v1:sWGub9BeVBMd9+FxdtZ/of0uKYrUtzzJi+dZLZ94Tg==', 'v1:IWquBOgnHZF/oLd1Fne5FK5u1hXWTZuiw5G8ITvak8y99VUj3LaoFDBW+el5NBgda6U/kvIBlg==', 'v1:ZHxLj3azpe7du2xF52MMETWZbE2jOjeP90BTSR1x9aFBtcNTZzVK', 4, '2026-08-17', '2030-07-31', 'Aktiv', 'v1:dy0P53qKBs7lfDjo5GkEPn8Wwg5TV2cj136LOU7vJQRhqjEx', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(35, 67, 'Kruppa', 'Jasmin', 'v1:rpHVpE8lF6pGYJZ6IKltd2q5FcvmOrn+A5vUZy2x2q2LNu9Wmw3ar0XsiTI=', 'v1:076GmlL6l8XCo01bD7IB9QJKTss+xc2iaw08u1RVIMrY', 'v1:zLk0484jjwCl/In6ZXh7+7Y8R3wslw2p2B9yrBgVZesBs1Hg', NULL, 'v1:/ME/OKCpsJHhEvRHyRzFt79QUyxDKEaTJ9QS+Mlapud7A35+/AODXg==', 'v1:E2pwy8o7gL+n7WfT98bHkFm0DV0yjFIsizkGuyBzQ7NU75t/hLh0ITC7DlWTN+C1', 5.00, 'v1:CBWcExURrh2PyRwXBbN88n+29SRMBf7LFWc12PdPFK6iUu2Q0Gzjt6g=', 'v1:rx6tzxeWtuRnZkxVQUrrjDQuay7mMCBI5Fkn57NOLw==', 'v1:FO0lT7fZQCwpXJhdS+FeDCrtpCKPB/PPKzyiORz4sfOxl+AH1VSHhLoDeTGVKpQu0J0IzqHJ9A==', 'v1:hnwl23mpPzl9+S72u1Mk/ZvhoHAXaL8pZEqSL76dfQ2MsFyTURz2', 4, '2026-08-17', '2030-07-31', 'Aktiv', 'v1:WQPoiyZG8dKTBA2kP7JHT94W+D/7LCjYrga5K1NoBb0nNfzS', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(36, 68, 'Chamberlain', 'Rick', 'v1:wQXWPLCVoCRcbohT0gy2zRqBPDCGD/Y60rGmxbB36fS8uNsNCrNehw==', 'v1:VuI3Jpb1K4n0R/HHNQevLQVOEoPov7ueXd7p18gOKAW6', 'v1:qQsNaZedXGYSgybwGF4q+QwS2bXqzK43hMr9pwWLqDiTaQrJ', NULL, 'v1:HZLGxnTgHC61re0ctTg15oKl45nNNcbwdlBmfhdmh+Ss4X+f6p6M6Ag=', 'v1:AccJTqHrRd/AtFbTp/5xnthN9X0N+/35EqHJs3XfbxO8x4JbuSr9OnUrYf7aaMKv8oofMUA2SA==', 5.00, 'v1:wGo56J8II2I/f8QzgVW/eIzGGJLiYp9ebIFF8KSgz7P1QCLJURfZH0tMxcs=', 'v1:frCt/Myn4/d+YWtO4j6C0M+oeux+tRjHjSPB1+Ojkw2Yi3zX3eIPTVtpNvJL4MPmRw==', 'v1:Xa9b3RWBuA9/vEmCAyexdS67NPEch1JPgoD6grmC8ipn6olTA3Ep/kvs1XFdZ2UNE1YoyJHFgw==', 'v1:ENCp1gTNpHSqmLQtRXs1hHT8L9K6KNiRk/TfC/rVtAVpai8d31fK', 4, '2026-09-03', '2030-07-31', 'Aktiv', 'v1:sten2/NrzKWRdas9mJO8t+iFx1bHzc7eA3XLE6+X1z4hpy0M', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(37, 69, 'Karabulut-Uyük', 'Asli', 'v1:Tw7S/5HkOm+JMmHNerIrl2FyAMOh7WtXcGSiJvJfn36vC//XTBvlU/45ew==', 'v1:lb+kb184ytJVikYmLcvijLCPVJWZpBBonL8ezCPQKQWG', 'v1:BC0FG6Icn+LSmyvvM03ZfjKkUSIWfVI38OvqPX8BvG/pJB6U', NULL, 'v1:6T4stmAJKvLu+D9TaXp1Wzlz4T+ZOOniYMmvHXf4tAoWT6B6dg64kT0=', NULL, 2.00, 'v1:tJ75gdOWcNXq5rCvfaQ+nEXXzffl7m971jmaLwnfsVPRAeAJulp0IE5XjyLdjRHO', 'v1:uvCBQfb6/zLarZIXpKTKiNxJCCYrKC05MUQFdjxmZCcsz6y0qqyGP7G4D9Sgcg==', 'v1:P+ueivtPIZriA/jUqDwhIObaCwwiAIx3G0GXDlDvUxGFLap2QRNsxaEJRjhXcTBfZ3Wizl8h8Q==', 'v1:sl2hHzSr8LV2v/+w/mAJFEysF2GvTe52yJUmQazNuKZECDiWZK1u', 4, '2026-09-21', '2030-07-31', 'Aktiv', 'v1:ZYFL2RgaFeMn/qaW9Qyx4ZVKOqdfc0ynw3B7nzuVHg7smFuh', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(38, 70, 'Kondryn', 'Denise', 'v1:Zs2kR9eeYr53O+/8GLOHsX3BL0t82btBfw/umwc2OogYbVTuN0y7RQ==', 'v1:9eQS1Ua7xINQTUPp05WMr08fZUNhxQodlfKM12Gfk3Vf', 'v1:5mZuYgHFjZBkvnCr7T7z+etVTihNzCGPvtfdfI8pld7DjVJ6', NULL, 'v1:+kR9tLj8BOlgUCL9ZQeAbzt7Nil467AwPH+JqgVp5eRHhR8nVPWh', 'v1:2Fpc+rwehLJSAkoRqIjuefiZlyh4SyOmtYMMSag758p0zfAcjat9KJu3OyDJh2mAFQ==', 3.50, 'v1:XtkBiQuzN1vOi7TmlnUI+iH+XFQFKF3jUEiMKEBfVYgKHCnVHqbBkOse', 'v1:P6ZThsQQ9KYEeDtlJIsIUL9AiPOtaPQvTj089cfKqo7pSiL/zfOcDzE5wdTxmPEYAA==', 'v1:RvpViN2V0SsTGUxRDia6CcVBTX3eA6v717yeRuwNeHCQrVij+d0oLbWGxVDqubugeGgvfJVZdQ==', 'v1:WDcGRTEDd5IpKOZhbLoiwuMjfCwPuH4+oFMe/kDv3fpZUZv1ZVZt', NULL, '2026-09-14', NULL, 'Aktiv', 'v1:cpIFqBkQrzQRV7hEZlYXxRkENXUVWox16msVT3bGJE6hashK', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(39, 71, 'Gordon', 'Dennis', 'v1:QAJ5MHzrtVBSeFaNP5EwTWliAJ6XM6g6FWI4gwCx+jd6rxVh8o2F9yIcOg==', 'v1:LqLUyBc8xnDEsdepxGnhsTuHYEvy29yO2sh4buPy6V6o', 'v1:rtApCQ0LAUVAAHxSkb11wZgaYugy1IhA+ZrxcBRGBBbiYEWd', NULL, 'v1:gbykiDIE8WnDwvlq0N3XuwpIBaQRnZl7ze+Omb+7tTv6yRr8rzQ+Og==', 'v1:079K45YquG1n3HfjotpmMTWUOY9A6gIet3I83WBrsw81sq7v/3pBp6Z8SJAdKIK/IW5oS+M=', 5.00, 'v1:5n2RKwb4Xm5d2T5gntv9X8RJLxSDo4EvgXTjg6irQvntgMPYPEE+Odw=', 'v1:XcW8dv44efa/2nbDM/0iSRLRu69jl8qcFqkvKnYZ4igyuc6xPAX3jNZNaH+ZwNgv8TU4fg==', 'v1:fMBnFWj5w+GZXrW2FP8IlH1tiuvQ86SnaFyY4uCFfH/Z3YCUhknLvPaPsMwcEljPFCh1tDZEZg==', 'v1:qbLZW7+a7KZwd/EZhEPi8IxXXoPo5TAmcMDpbMa+EemzrXVAd7MJ', NULL, '2026-10-17', NULL, 'Aktiv', 'v1:W1cz/yxH5XggToQycN0KMqA7gt4d1UKD88xBul95QMep7cTG', NULL, '2026-11-06 15:38:02', '2026-11-06 15:38:02'),
(40, 72, 'Klaßen', 'Miriam', 'v1:9/e4r3qS2rT0fAbFYNWXgfvq92G71y9dbfm3FuHwTP9cGC/F7in/1uHr3yw=', 'v1:r3YYcEaTB1rKkE59RSLETuLMB9IFB+DuT2npCxc0f+Qr', 'v1:pjD6/adCAoI/8FdeMtsjuu3AdCQAmaWFjk/pPd8uhAC+tBx4', NULL, NULL, 'v1:ortoru7Aw9OVqnExyxqAKECvPe6E8NsfnS3l3+9b1pQOL7Ae80GeGyhZcXevLTXYgmR84EHE', 5.00, 'v1:R7DXHuAepmWNovPqDyIdGkMTk/jWM4z+DpYi66WWX8a1Xpuoz334rDCB9Q==', 'v1:a8on9lC60D9yPu+6Y5iOIcKWreM1M+t63irIbpz6Mw==', 'v1:2oSrvL02oS/0yoXrxYfk8y1R2DMDoaFrVHEbb1mubJOLf49AMVmyJHMl5gK2CFOGrBJn8Uxcqw==', 'v1:JUWt2Ih1MGA9jcKonkk7f6kymS50orAjnVTyCCoqZzBa8lyVjhKB', 4, '2026-10-19', '2030-07-31', 'Aktiv', 'v1:UK5CAUgEfBrmo4viLtX7DzC/ijOcaUd1LWYAvOw2ozFRKyfs', NULL, '2026-11-06 21:14:07', '2026-11-06 21:14:07');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member_mail_batches`
--

CREATE TABLE `member_mail_batches` (
  `id` bigint UNSIGNED NOT NULL,
  `subject_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_enc` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_count` int UNSIGNED NOT NULL DEFAULT '0',
  `sent_count` int UNSIGNED NOT NULL DEFAULT '0',
  `failed_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `member_mail_batches`
--

INSERT INTO `member_mail_batches` (`id`, `subject_enc`, `body_enc`, `recipient_count`, `sent_count`, `failed_count`, `created_at`) VALUES
(1, 'v1:JEELliaDTVT8TcdGgfp/GIvpE7GuBeTDlOejeH/hOAGH4DMxQ/BnQDfaIkjvLvD0aWE73+N+3Y7TJOnmdb8ZqiK4eogiqFLc3UGyLEa78Gkbeog=', 'v1:DoFGdU/IoUPQZgheIpBsYJ/MHadlWAFU8UYgo3NJkes4PY4ggW2fwpcadX720GDkdG95K78wGR8g2PrHVhDHxmny6ablN6ysZumQ4BHA2ros40ZrJ4M3gr6J6OQ0XG4TOJfXXBcHelZH3kgdaty3XH8AWUc26kYp9mWONO5nMVs6VP3us5cGQj31YW+lGg9KCO9J/Zo0Xvvj8SHu9JKNjP0rm6dt/4D6Pdy0yBYd00XfbDVET6xTs3wfYWQGEMNz3o2SanurvEGJ2LgcvFCBndj68UvAGeA4Z2ZaX/r2SbpE6LWJRaVVPMiz5fFK3FwsfezB93js1sSpF1BAOd5qubpQscSfaq9Ltz+iIHIqCM0/tZX8chaY5aAYCUJHDv01D+i2uwT6UUk6YyihHy99Mmaeev7v6xBZKbRj8Bk/DOgi70Al9NerVPBxrA+XbJb2emvH2CuLqgteWksvTyd+mkyKdmHRR9QXJZEw8mPvNkOrDTj3A5BT7F1TMyyZJ8KUYsmWWzQGzouLEZgZbt60R9EQiydYHuN6lKaST3ndSk8LgplMlviOJJuslpLp5L9l4MjHK0d1YwEVktUi6y834qVJUONvYFPUA4ZUjNSpoYUtOJYNiH7lZDD3nfygH8OZGZFrbfkzJPul4gu7QmS8h/QRHzf8T7ofcrdKusLipV9H3yZIRVIGUzmzqbO5YXLFRw28gjPCkbZLIfFcgmt7Ae2yEF2lWe637Q+Saox2qx3khWm8S/qWmFXIu3FMfXj+rysc41WdJJai1/CIwojGm8u0oajzwx66w3V0sErQGUC1CcLdzKY7cRNHYn9Ffguq7MrN3kAYNF18N6leCn02U9O66Whc9IK4lJvZeJkSac/g+RuXocV23mLrxe02fqekc6GKaoKvj3yoUCOrzNEYI7sYRYyy0ZA8nQb4wao6NCEBSMU2Z9VVl1jA48MzOvVdY9t/gcVUFLLa1WZk8X/GlAjgkDbBLQLgOn39ftmyfWdOkietOto0A4VWY50p8RGfTQ93bPC3oZh7biRbmdnZikElKm2WKvwY4sTNMQNlVvjUEABg6/spRO9jAc3tCm4sCiOgSgmRT+Jwk/d0aJIHHlaLbEKLg3Gf/13gNwr7MBY2InLmmVisHBV47gn+jwS7LFcyyw2zEoh362fYINBD1pnXS9JHtAJgnEwSN0UpjadMESEIW7DKmiblG0pjQxX6hw3mrspy08xYth/5a2YBTQBo49dmz4zdNDFnUB0F5aY8y7lpWfVsAMg6+TSN+G/kfEVlpC0waXv89mKJXbzW6/rrAuwj32oQqaMkdxFgJO6mEWFqY6zBF2IrDpE/OGD/oIc/il8UQqFvgAG7HWzqytpwFXbKlSBMWI6xK6AbzBrofI0zsg81Og75ZtUl4Y3MdPb+wJG+fElREs0e7b25ziKJeQKgXe7N6DIskKCJJ1IY3VKJoApk6pWRRKKPHgQVM0LXFQdChXWEnRnq0Yc1bx8FY3XQtAdw+UXZZnCi2ML6rFBHfcUeSgvyDxVKCSUnvz+2Yy7Vn+y0SPEbH3poT5XOoc1oOuyWri/1TXJr/IrJUvdxvlbtO0ab1tByv7mJ24OarIPByaRxIuh6Czepe7yqfVK2qkdsYNQiEX0Qzv8lKMwefz1mzgp2g39tQqp/uz865Fu1Ovo3+K3L8HQrAeZaU/GFNiTFUOpjDREEpifhS+zqNewleeEGcS0R4HVwLCp99zZ1JU/a0M9pU4w/CEOXEFgIbvoxI3luyH8lWQbOSfuNVzkuZoEeqzDUle+Z0634kVD2d5eZYRJFihBHk7VuKD2O+FZpkDbCxAiXYqi/OoRa8+4S7Jxk+1PtgMWtpp+dqV6J90D+F+UsHR22LMhhrSRELx3AWdYI71bXclEmBKwQhTCoH+cxcyW85bX4KDIGCSWAqHnuq0G5a72Oi0upM+V7ntbrH5oZ/ayH8iYAmAcif3GhFGAdFTMQC6OqK+KBvJKV0dRTFkh2QXusF7nSSRYsDlwsBIY+BmujJx4hF45gMma/RiLOSeUiAiyXA7Up++gtbYaY6SUeBxiE+lTfZ1HpNvQ6zdFeeFy34dPtrgX4eDuWqhyUXuiW0n/7Zw97YYaqvhmN72Fb9Igpi9Ky6MtAJFf/5fZhJHw9+ZunZqxWph8LDcs+OC0G6cW67rfYl4OjHSf7XPtc7HwtQyIqosbIQRWhO//bvW4VRBdMf7MiTQhEBOJoBkVRAXTnDwH3GyfMZ7sd2wiZIMaHVdhmfsbHK/dqRmnOi5zspveo4SrlydjiYtJMrwJGAxzdND2HrCUnk2tK525dHc6110LK9VpfDnjvDokICmlk6lPc1jybZVgTX7Mqdlb93Afq1DMliqQk5X/YN7j/T+f12kSHLsWSr6+Qdna4gss2cGWxJl+x2QnbtVfEVRlmUKCcnAFxO83qWE8iu7Z9nast+a57X25XK/c0HzRfJ6jtqpX/Qln9sHZtpP++Bo/A9zGwj/RYGRu4S9RhBhoHYzIo9UCHGESKh4kmw37sR1/Y6qZlu+AZdFQyT6pxwZKcWF28KhRVLO09fWXGpmZb/Ak/FBInaTibLHujux2u7f1qhJX3LTvsJdD5qVrEw0k3tEkjnN7mT1mgDMuw2wY3+q2W/obUNXzDpW7bsl1HhuYgl4T1PfPfG6aDrGKutXvGFJEOTjmBXXEHMTETH6C0iXbKlyT5i22+dfB5g+JbNPbgG5URu+n/BOFtF/zCprQFu9hHPNHzJ4ybAyAMkaDsWFjb08E8LO7dCdCUm6ztqyyrHZXozZyzNUnGuxA5lrK7WY2+FFD7cxKhgSmR5lQXqYXBrab4l+XCN6ul0gQ7YMOrBe6us8aGLnWf3bvNf3yoax1n+Fmn9/5ON0TbSGWySbM0gzAHmmyfvW8tiWL3w/AQW/LsUC8RRNK8jGArh8f+gANRYKXXy4viYL0H8ZU8kVZLWpuC2wiDEHAY244tOigXYUF2bVfEzgGCMu6lHfZ49YVWxRlqRsP8pjhRpTGbBnpKKp9s4phcb5ZU32Dx9zQY7JSFAK0aE9HpSNM59lTXRe8J2eX09DH193s4f2yZWOZ8VSb9xhb8MKSl4r+qkSs9BzBFF6AO/30rZYVI4qL4hbLAL3p9y5RaIPpjFrkcNjWj2lFoTh7XD0KlV404n26NDhrS+K50u6J9McQJ1fNL0D/ZIPqvcS2oVHv6RPEVvFE5aLx/hlWFYxbZhDKCG2IEpJgaIqZbmBe/xsoQE/VW3T0bixw6uOhLmKE9ib66jDK9SCRLB7Y4oMh5kFJ2OQrvCiVeX2oIk1ZiLJa9oRzGIYv0gfBrWEe7IlfQBXr6YNg3+mgcLkpU3JBXGea0AVbDeX7yn1OLpLzehwJxCjDtwPGC5nQ/zEa3DpPxXVBZzBNFgveVINFsznNARUua+EfKks1a5IGf/S4rfuERyhMAHDnU1XTPAPX9cIi8BOry1/o171UUGqiiFxIKMMmUq1u5002jfBdhCDorF/NeDdzYVWlbNZCo9g4Ftu5BoOuqXPITUyfnAyuQOmJv/q8o3I6TDhHX7e5/LBkKxMuS0PekHvl5pvQUrS3BpGBts9at/CDhxwXdZtznVliwM98HO96D/foI+vguQyjtdS759y+1DaM3t9SI3TLylSsfT5b8i68ZaJZNMrfbaXP8ZXhvWb2NJp7lttMi8VsssoIgcqNEoGqyuRoqOJmSfYr86ZRHguEcQQZaLcw4EUMlMoylcum2S7OwTlWVGaOzXio=', 1, 1, 0, '2026-11-09 08:53:12');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member_mail_recipients`
--

CREATE TABLE `member_mail_recipients` (
  `id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `member_id` bigint UNSIGNED DEFAULT NULL,
  `member_number` int UNSIGNED NOT NULL,
  `recipient_name_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_email_enc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_status` enum('pending','sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_at` datetime DEFAULT NULL,
  `error_message` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `member_mail_recipients`
--

INSERT INTO `member_mail_recipients` (`id`, `batch_id`, `member_id`, `member_number`, `recipient_name_enc`, `recipient_email_enc`, `send_status`, `sent_at`, `error_message`, `created_at`) VALUES
(1, 1, 40, 72, 'v1:Pv11W5ZHBycGI5ZiUqKdMbRPX7J1hw4MqLaFyxu7o7egK8DjYseSn9Wu', 'v1:GGyQ5KbmBtOV0mnUjlYapW95VbEa1oa1s8OeW06dIN2Hkja0R/nXTfuYNIJmTJwEuESIoqpJ', 'sent', '2026-11-09 08:53:13', NULL, '2026-11-09 08:53:12');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member_number_sequence`
--

CREATE TABLE `member_number_sequence` (
  `id` tinyint UNSIGNED NOT NULL,
  `last_issued_number` int UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `member_number_sequence`
--

INSERT INTO `member_number_sequence` (`id`, `last_issued_number`, `updated_at`) VALUES
(1, 72, '2026-11-06 21:14:07');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `notification_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint NOT NULL,
  `event_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('open','done') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `notification_type`, `entity_type`, `entity_id`, `event_key`, `title`, `message`, `target_url`, `is_read`, `status`, `created_at`, `read_at`, `completed_at`) VALUES
(1, 3, 'task_overdue', 'task', 39, '2026-10-24', 'Aufgabe überfällig', '„Vergessene Bestellung bestellen“ ist seit dem 24.10.2026 überfällig.', 'projects.php?view=task&id=39', 1, 'open', '2026-10-28 19:39:38', '2026-10-28 19:56:21', NULL),
(2, 8, 'task_overdue', 'task', 39, '2026-10-24', 'Aufgabe überfällig', '„Vergessene Bestellung bestellen“ ist seit dem 24.10.2026 überfällig.', 'projects.php?view=task&id=39', 0, 'open', '2026-10-28 19:39:38', NULL, NULL),
(3, 11, 'task_overdue', 'task', 39, '2026-10-24', 'Aufgabe überfällig', '„Vergessene Bestellung bestellen“ ist seit dem 24.10.2026 überfällig.', 'projects.php?view=task&id=39', 0, 'open', '2026-10-28 19:39:38', NULL, NULL),
(4, 13, 'task_overdue', 'task', 39, '2026-10-24', 'Aufgabe überfällig', '„Vergessene Bestellung bestellen“ ist seit dem 24.10.2026 überfällig.', 'projects.php?view=task&id=39', 0, 'open', '2026-10-28 19:39:38', NULL, NULL),
(5, 14, 'task_overdue', 'task', 39, '2026-10-24', 'Aufgabe überfällig', '„Vergessene Bestellung bestellen“ ist seit dem 24.10.2026 überfällig.', 'projects.php?view=task&id=39', 1, 'open', '2026-10-28 19:39:38', '2026-11-10 20:51:36', NULL),
(781, 3, 'task_overdue', 'task', 43, '2026-11-01', 'Aufgabe überfällig', '„Posterwand mit Anschaffungen vorbereiten“ ist seit dem 01.11.2026 überfällig.', 'projects.php?view=task&id=43', 1, 'done', '2026-11-02 08:00:01', '2026-11-03 14:50:04', '2026-11-03 10:39:05'),
(782, 8, 'task_overdue', 'task', 43, '2026-11-01', 'Aufgabe überfällig', '„Posterwand mit Anschaffungen vorbereiten“ ist seit dem 01.11.2026 überfällig.', 'projects.php?view=task&id=43', 0, 'done', '2026-11-02 08:00:03', NULL, '2026-11-03 10:39:05'),
(783, 11, 'task_overdue', 'task', 43, '2026-11-01', 'Aufgabe überfällig', '„Posterwand mit Anschaffungen vorbereiten“ ist seit dem 01.11.2026 überfällig.', 'projects.php?view=task&id=43', 0, 'done', '2026-11-02 08:00:03', NULL, '2026-11-03 10:39:05'),
(784, 13, 'task_overdue', 'task', 43, '2026-11-01', 'Aufgabe überfällig', '„Posterwand mit Anschaffungen vorbereiten“ ist seit dem 01.11.2026 überfällig.', 'projects.php?view=task&id=43', 0, 'done', '2026-11-02 08:00:03', NULL, '2026-11-03 10:39:05'),
(785, 14, 'task_overdue', 'task', 43, '2026-11-01', 'Aufgabe überfällig', '„Posterwand mit Anschaffungen vorbereiten“ ist seit dem 01.11.2026 überfällig.', 'projects.php?view=task&id=43', 1, 'done', '2026-11-02 08:00:03', '2026-11-10 20:51:45', '2026-11-03 10:39:05'),
(841, 3, 'task_overdue', 'task', 40, '2026-11-02', 'Aufgabe überfällig', '„Mitgliedsbeiträge zurückbuchen“ ist seit dem 02.11.2026 überfällig.', 'projects.php?view=task&id=40', 1, 'done', '2026-11-03 08:00:02', '2026-11-03 16:42:45', '2026-11-03 16:42:45'),
(842, 8, 'task_overdue', 'task', 40, '2026-11-02', 'Aufgabe überfällig', '„Mitgliedsbeiträge zurückbuchen“ ist seit dem 02.11.2026 überfällig.', 'projects.php?view=task&id=40', 0, 'done', '2026-11-03 08:00:03', NULL, '2026-11-03 16:43:07'),
(843, 11, 'task_overdue', 'task', 40, '2026-11-02', 'Aufgabe überfällig', '„Mitgliedsbeiträge zurückbuchen“ ist seit dem 02.11.2026 überfällig.', 'projects.php?view=task&id=40', 0, 'done', '2026-11-03 08:00:03', NULL, '2026-11-03 16:43:07'),
(844, 13, 'task_overdue', 'task', 40, '2026-11-02', 'Aufgabe überfällig', '„Mitgliedsbeiträge zurückbuchen“ ist seit dem 02.11.2026 überfällig.', 'projects.php?view=task&id=40', 0, 'done', '2026-11-03 08:00:03', NULL, '2026-11-03 16:43:07'),
(845, 14, 'task_overdue', 'task', 40, '2026-11-02', 'Aufgabe überfällig', '„Mitgliedsbeiträge zurückbuchen“ ist seit dem 02.11.2026 überfällig.', 'projects.php?view=task&id=40', 1, 'done', '2026-11-03 08:00:03', '2026-11-10 20:51:42', '2026-11-03 16:43:07'),
(3616, 3, 'task_overdue', 'task', 47, '2026-11-08', 'Aufgabe überfällig', '„Kisten für Tombola bestellen“ ist seit dem 08.11.2026 überfällig.', 'projects.php?view=task&id=47', 1, 'done', '2026-11-09 00:03:16', '2026-11-09 16:55:59', '2026-11-12 12:17:30'),
(3617, 8, 'task_overdue', 'task', 47, '2026-11-08', 'Aufgabe überfällig', '„Kisten für Tombola bestellen“ ist seit dem 08.11.2026 überfällig.', 'projects.php?view=task&id=47', 0, 'done', '2026-11-09 00:03:17', NULL, '2026-11-12 12:17:30'),
(3618, 11, 'task_overdue', 'task', 47, '2026-11-08', 'Aufgabe überfällig', '„Kisten für Tombola bestellen“ ist seit dem 08.11.2026 überfällig.', 'projects.php?view=task&id=47', 0, 'done', '2026-11-09 00:03:17', NULL, '2026-11-12 12:17:30'),
(3619, 13, 'task_overdue', 'task', 47, '2026-11-08', 'Aufgabe überfällig', '„Kisten für Tombola bestellen“ ist seit dem 08.11.2026 überfällig.', 'projects.php?view=task&id=47', 0, 'done', '2026-11-09 00:03:17', NULL, '2026-11-12 12:17:30'),
(3620, 14, 'task_overdue', 'task', 47, '2026-11-08', 'Aufgabe überfällig', '„Kisten für Tombola bestellen“ ist seit dem 08.11.2026 überfällig.', 'projects.php?view=task&id=47', 1, 'done', '2026-11-09 00:03:18', '2026-11-10 20:51:34', '2026-11-12 12:17:30'),
(5141, 3, 'task_overdue', 'task', 33, '2026-11-06', 'Aufgabe überfällig', '„Tombola bei der Stadt anmelden“ ist seit dem 06.11.2026 überfällig.', 'projects.php?view=task&id=33', 0, 'done', '2026-11-09 22:31:44', NULL, '2026-11-13 08:25:06'),
(5142, 8, 'task_overdue', 'task', 33, '2026-11-06', 'Aufgabe überfällig', '„Tombola bei der Stadt anmelden“ ist seit dem 06.11.2026 überfällig.', 'projects.php?view=task&id=33', 0, 'done', '2026-11-09 22:31:45', NULL, '2026-11-13 08:25:06'),
(5143, 11, 'task_overdue', 'task', 33, '2026-11-06', 'Aufgabe überfällig', '„Tombola bei der Stadt anmelden“ ist seit dem 06.11.2026 überfällig.', 'projects.php?view=task&id=33', 0, 'done', '2026-11-09 22:31:45', NULL, '2026-11-13 08:25:06'),
(5144, 13, 'task_overdue', 'task', 33, '2026-11-06', 'Aufgabe überfällig', '„Tombola bei der Stadt anmelden“ ist seit dem 06.11.2026 überfällig.', 'projects.php?view=task&id=33', 0, 'done', '2026-11-09 22:31:45', NULL, '2026-11-13 08:25:06'),
(5145, 14, 'task_overdue', 'task', 33, '2026-11-06', 'Aufgabe überfällig', '„Tombola bei der Stadt anmelden“ ist seit dem 06.11.2026 überfällig.', 'projects.php?view=task&id=33', 1, 'done', '2026-11-09 22:31:45', '2026-11-10 20:51:32', '2026-11-13 08:25:06'),
(12006, 3, 'task_overdue', 'task', 36, '2026-11-13', 'Aufgabe überfällig', '„Lose bestellen“ ist seit dem 13.11.2026 überfällig.', 'projects.php?view=task&id=36', 0, 'open', '2026-11-14 00:40:35', NULL, NULL),
(12007, 8, 'task_overdue', 'task', 36, '2026-11-13', 'Aufgabe überfällig', '„Lose bestellen“ ist seit dem 13.11.2026 überfällig.', 'projects.php?view=task&id=36', 0, 'open', '2026-11-14 00:40:36', NULL, NULL),
(12008, 11, 'task_overdue', 'task', 36, '2026-11-13', 'Aufgabe überfällig', '„Lose bestellen“ ist seit dem 13.11.2026 überfällig.', 'projects.php?view=task&id=36', 0, 'open', '2026-11-14 00:40:36', NULL, NULL),
(12009, 13, 'task_overdue', 'task', 36, '2026-11-13', 'Aufgabe überfällig', '„Lose bestellen“ ist seit dem 13.11.2026 überfällig.', 'projects.php?view=task&id=36', 0, 'open', '2026-11-14 00:40:36', NULL, NULL),
(12010, 14, 'task_overdue', 'task', 36, '2026-11-13', 'Aufgabe überfällig', '„Lose bestellen“ ist seit dem 13.11.2026 überfällig.', 'projects.php?view=task&id=36', 0, 'open', '2026-11-14 00:40:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Daten für Tabelle `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(10, 3, 'f2256202e3fc5784cd4f110baa054b009e67e042661a3b2b98f05723fe904fcf', '2026-10-20 16:41:24', 0, '2026-10-20 13:41:24');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `description`, `created_at`) VALUES
(1, 'view_contacts', 'Kontakte anzeigen', '2025-10-18 12:07:14'),
(2, 'add_contacts', 'Neue Kontakte hinzufügen', '2025-10-18 12:07:14'),
(3, 'edit_contacts', 'Kontakte bearbeiten', '2025-10-18 12:07:14'),
(4, 'delete_contacts', 'Kontakte löschen', '2025-10-18 12:07:14'),
(5, 'send_emails', 'E-Mails versenden', '2025-10-18 12:07:14'),
(6, 'view_reports', 'Berichte anzeigen', '2025-10-18 12:07:14'),
(7, 'manage_users', 'Benutzer verwalten', '2025-10-18 12:07:14'),
(8, 'manage_settings', 'Einstellungen verwalten', '2025-10-18 12:07:14'),
(9, 'export_data', 'Daten exportieren', '2025-10-18 12:07:14'),
(10, 'change_status', 'Status von Kontakten ändern', '2025-10-18 12:07:14'),
(11, 'edit_comments', 'Kommentare bearbeiten', '2025-10-18 12:07:14'),
(12, 'suggest_contacts', 'Neue Kontakte vorschlagen', '2025-10-18 12:07:14'),
(13, 'edit_fundraising', 'Spendenanfrage bearbeiten', '2026-09-16 08:28:02'),
(14, 'ProjectManagement', 'Zugriff auf Projektmanagement, Projektvorlagen, Aufgaben und Kommentare', '2026-10-16 09:06:39'),
(15, 'Verkauf', 'Verwaltung von Verkaufsaktionen, Artikeln und Bestellungen', '2026-10-19 07:52:24'),
(16, 'manage_members', 'Zugriff auf die Mitgliederverwaltung und Mitgliederübersicht', '2026-11-05 09:55:39'),
(17, 'manage_helpers', 'Helferlisten und Helferanmeldungen verwalten', '2026-11-07 06:41:06'),
(18, 'manage_accounting', 'Zugriff auf Buchführung, Buchungen und Kassenprüfung', '2026-11-09 15:58:11');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `template_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `color` char(7) NOT NULL DEFAULT '#DCEAF0',
  `project_type` enum('event','project') NOT NULL DEFAULT 'event',
  `event_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `fiscal_year` varchar(9) NOT NULL,
  `status` enum('active','completed','archived') NOT NULL DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `projects`
--

INSERT INTO `projects` (`id`, `template_id`, `title`, `description`, `color`, `project_type`, `event_date`, `start_date`, `end_date`, `fiscal_year`, `status`, `created_by`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 2, 'Jahreshauptversammlung Oktober 2026', '', '#FEF2CB', 'event', '2026-10-21', NULL, NULL, '2026/2027', 'completed', 3, '2026-10-16 10:09:59', '2026-11-03 15:43:45', '2026-11-03 16:43:45'),
(2, 1, 'Sponsorenlauf 2026', '', '#F7C9FC', 'event', '2026-10-17', NULL, NULL, '2026/2027', 'active', 3, '2026-10-17 06:31:44', '2026-10-17 21:13:21', NULL),
(3, 1, 'Sponsorenlauf 2027', '', '#D1EBFA', 'event', '2027-05-22', NULL, NULL, '2026/2027', 'active', 3, '2026-10-17 21:16:46', '2026-10-17 21:23:22', NULL),
(4, 3, 'Tombola auf dem Weihnachtsmarkt des RTV 2026', '', '#DAF1DE', 'event', '2026-11-27', NULL, NULL, '2026/2027', 'active', 3, '2026-11-09 21:31:44', '2026-11-09 22:08:30', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project_task_resources`
--

CREATE TABLE `project_task_resources` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` int DEFAULT NULL,
  `template_task_item_id` int DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Daten für Tabelle `project_task_resources`
--

INSERT INTO `project_task_resources` (`id`, `task_id`, `template_task_item_id`, `label`, `url`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'Bisherige Ressource', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(2, 3, NULL, 'Bisherige Ressource', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(3, 5, NULL, 'Bisherige Ressource', 'https://www.overleaf.com/project/66c701b0a359dbe56f3b005f/share#ab696c2c03acfc0f3813c184c6348a90d1676d368fdd87f3', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(4, 6, NULL, 'Bisherige Ressource', 'https://www.overleaf.com/7927764558prnkpncsyqkz#a886e6', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(5, 7, NULL, 'Bisherige Ressource', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(6, 16, NULL, 'Bisherige Ressource', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(7, 18, NULL, 'Bisherige Ressource', 'https://sponsorenlauf.evkigarumeln.de/', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(8, 26, NULL, 'Bisherige Ressource', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(9, 28, NULL, 'Bisherige Ressource', 'https://sponsorenlauf.evkigarumeln.de/', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(10, 29, NULL, 'Bisherige Ressource', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(11, 30, NULL, 'Bisherige Ressource', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(12, 31, NULL, 'Bisherige Ressource', 'https://www.overleaf.com/3567354428nfcghztbqsbr#a8c09e', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(13, 34, NULL, 'Bisherige Ressource', 'https://www.finanzamt.nrw.de/system/files/media/document/file/214_09_rennwlottg_anzeige_einer_lotterie_0.pdf', 3, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(16, NULL, 9, 'Bisherige Ressource', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(17, NULL, 10, 'Bisherige Ressource', 'https://www.overleaf.com/project/66c701b0a359dbe56f3b005f/share#ab696c2c03acfc0f3813c184c6348a90d1676d368fdd87f3', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(18, NULL, 11, 'Bisherige Ressource', 'https://www.overleaf.com/7927764558prnkpncsyqkz#a886e6', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(19, NULL, 13, 'Bisherige Ressource', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(20, NULL, 14, 'Bisherige Ressource', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(21, NULL, 16, 'Bisherige Ressource', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(22, NULL, 18, 'Bisherige Ressource', 'https://sponsorenlauf.evkigarumeln.de/', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(23, NULL, 19, 'Bisherige Ressource', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(24, NULL, 20, 'Bisherige Ressource', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(25, NULL, 21, 'Bisherige Ressource', 'https://www.overleaf.com/3567354428nfcghztbqsbr#a8c09e', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56'),
(26, NULL, 24, 'Bisherige Ressource', 'https://www.finanzamt.nrw.de/system/files/media/document/file/214_09_rennwlottg_anzeige_einer_lotterie_0.pdf', NULL, '2026-11-11 16:31:56', '2026-11-11 16:31:56');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project_templates`
--

CREATE TABLE `project_templates` (
  `id` int NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text,
  `color` char(7) NOT NULL DEFAULT '#DCEAF0',
  `project_type` enum('event','project') NOT NULL DEFAULT 'event',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `project_templates`
--

INSERT INTO `project_templates` (`id`, `name`, `description`, `color`, `project_type`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Sponsorenlauf', 'Wiederkehrende Aufgaben zur Vorbereitung des Sponsorenlaufs.', '#DCEAF0', 'event', 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(2, 'Vollversammlung', 'Vorbereitung der Jahreshauptversammlung', '#DCEAF0', 'event', 1, 3, '2026-10-16 09:50:02', '2026-10-16 10:08:40'),
(3, 'Tombola', '', '#DAF1DE', 'event', 1, 3, '2026-11-09 21:30:08', '2026-11-09 21:30:08');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project_template_tasks`
--

CREATE TABLE `project_template_tasks` (
  `project_template_id` int NOT NULL,
  `task_template_id` int NOT NULL,
  `default_selected` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `project_template_tasks`
--

INSERT INTO `project_template_tasks` (`project_template_id`, `task_template_id`, `default_selected`, `sort_order`, `is_active`) VALUES
(1, 1, 1, 10, 1),
(1, 2, 1, 20, 1),
(1, 3, 1, 30, 1),
(1, 4, 1, 40, 1),
(1, 5, 1, 50, 1),
(1, 6, 1, 60, 1),
(1, 7, 1, 70, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project_template_task_items`
--

CREATE TABLE `project_template_task_items` (
  `id` int NOT NULL,
  `project_template_id` int NOT NULL,
  `source_task_template_id` int DEFAULT NULL COMMENT 'Optionale Herkunft; Inhalte werden als eigenständige Kopie gespeichert',
  `title` varchar(200) NOT NULL,
  `description` text,
  `resource_url` varchar(2048) DEFAULT NULL,
  `start_offset_days` int DEFAULT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  `due_offset_days` int DEFAULT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  `default_selected` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `project_template_task_items`
--

INSERT INTO `project_template_task_items` (`id`, `project_template_id`, `source_task_template_id`, `title`, `description`, `resource_url`, `start_offset_days`, `due_offset_days`, `default_selected`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Plakate gestalten und drucken', 'Plakate vorbereiten und den Druck organisieren.', NULL, -56, -14, 1, 10, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(2, 1, 2, 'Sponsorenlauf bei der Stadt anmelden', 'Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.', NULL, -28, -14, 1, 20, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(3, 1, 3, 'Tombola anmelden', 'Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.', NULL, -28, -14, 1, 30, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(4, 1, 4, 'Medaillen organisieren', 'Medaillenbedarf klären und rechtzeitig bestellen.', NULL, -42, -7, 1, 40, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(5, 1, 5, 'Urkunden vorbereiten', 'Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.', NULL, -21, -3, 1, 50, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(6, 1, 6, 'Teilnehmerliste vorbereiten', 'Anmeldung und Teilnehmerliste zusammenführen und prüfen.', NULL, -14, -3, 1, 60, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(7, 1, 7, 'Kaffee und Verpflegung organisieren', 'Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.', NULL, -14, -2, 1, 70, 1, '2026-10-16 09:31:36', '2026-10-16 09:31:36'),
(8, 2, NULL, 'Kassenprüfung durch die Kassenprüfer', 'Die freiwilligen Kassenprüfer einberufen und die Kassenprüfung durchführen lassen', NULL, -42, -7, 1, 2, 1, '2026-10-16 09:52:09', '2026-10-16 10:13:19'),
(9, 2, NULL, 'Kassenschluss für das letzte Jahr', 'Optimalerweise zum wechsel des Geschäfttsjahres, aber spätestens zur Planung der Vollversammlung.', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', -56, -14, 1, 1, 1, '2026-10-16 09:55:15', '2026-10-16 09:55:15'),
(10, 2, NULL, 'Jahresberichtt erstellen', 'Der Jahresbericht wird automatisch aus der Buchführung generiert. Aktuell noch per R Skript, dass muss ich noch allgemeiner verfügbar machen.', 'https://www.overleaf.com/project/66c701b0a359dbe56f3b005f/share#ab696c2c03acfc0f3813c184c6348a90d1676d368fdd87f3', -14, -2, 1, 3, 1, '2026-10-16 09:57:51', '2026-10-16 09:57:51'),
(11, 2, NULL, 'Protokoll vorbereiten', 'Die meisten Inhale stehen vorab, es kann also vorab der Orientierung dienen und das Protokollieren erleichtern.', 'https://www.overleaf.com/7927764558prnkpncsyqkz#a886e6', -7, -1, 1, 4, 1, '2026-10-16 10:02:45', '2026-10-16 10:02:45'),
(12, 2, NULL, 'Termin festlegen', 'Termin Abstimmen und verfügbarkeit des Raums klären', NULL, -56, NULL, 1, -1, 1, '2026-10-16 10:03:45', '2026-10-16 10:03:45'),
(13, 2, NULL, 'Einladung erstellen und herausgeben', '- per Mail an Mitglieder\r\n- per Eltern App an alle Eltern\r\n- per Aushang im Kindergarten', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', NULL, NULL, 1, 0, 1, '2026-10-16 10:05:08', '2026-10-16 10:05:08'),
(14, 2, NULL, 'Protokoll fertigstellen', '', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', 0, 7, 1, 7, 1, '2026-10-16 10:07:01', '2026-10-16 10:07:01'),
(15, 2, NULL, 'Dokumente Unterzeichnen und abheften', 'Kassenberichtt, Protokoll, Berichtt der Kassenprüfer und Teilnehmerliste', NULL, 0, 14, 1, 8, 1, '2026-10-16 10:08:27', '2026-10-16 10:08:27'),
(16, 1, NULL, 'Runden und Sponsorenangaben in Tabelle übernehmen', 'Die Berechnung, welcher Sponsore wieviel zahlen muss, muss erstellt werden', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', 0, 7, 1, 80, 1, '2026-10-17 20:21:01', '2026-10-17 20:21:01'),
(17, 1, NULL, 'Abrechnungsmails generieren und versenden', 'Aktuell noch ein lokales R Skript', NULL, 4, 11, 1, 90, 1, '2026-10-17 20:23:31', '2026-10-17 20:23:31'),
(18, 1, NULL, 'Wunschliste kaufen', 'Die Wunschliste ist au der Seite des Sponsorenlaufs hinterlegt', 'https://sponsorenlauf.evkigarumeln.de/', 28, 42, 1, 100, 1, '2026-10-17 21:15:58', '2026-10-17 21:15:58'),
(19, 1, NULL, 'Kassen zählen und Buchführung aktualisieren', '', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', 0, 3, 1, 110, 0, '2026-10-17 21:41:39', '2026-10-17 21:47:12'),
(20, 1, NULL, 'Kassen zählen und Buchführung aktualisieren', '', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', 0, 4, 1, 120, 1, '2026-10-17 21:49:06', '2026-10-17 21:49:06'),
(21, 2, NULL, 'Engagement-Nachweise erstellen', 'Künftig kann auch der Förderverein ev. Kindergarten Duisburg Rumeln-Kaldenhausen e.V. den Engagementnachweis NRW an seine ehrenamtlich Engagierten ausstellen. \r\n\r\nhttps://www.engagiert-in-nrw.de/engagementnachweis-hinweise-und-formulare        \r\n\r\nBenutzername: Engagementnachweis\r\n\r\nPasswort: TiqL8E4V', 'https://www.overleaf.com/3567354428nfcghztbqsbr#a8c09e', -81, -2, 1, 18, 1, '2026-10-18 19:34:34', '2026-10-18 19:34:34'),
(22, 2, NULL, 'Neue Flyer drucken', '', NULL, 1, 7, 1, 28, 1, '2026-10-21 12:30:20', '2026-10-21 12:30:20'),
(23, 3, NULL, 'Tombola bei der Stadt anmelden', '', NULL, -28, -21, 1, 0, 1, '2026-11-09 21:30:45', '2026-11-09 21:30:45'),
(24, 3, NULL, 'Tombola beim Finanzamt Köln anmelden', 'Folgende Unterlagen müssen per Brief eingereicht werden: \r\n- Formular: rennwlottg_anzeige_einer_lotterie_0\r\n- der aktuelle Freistellungsbescheid\r\n- Genehmigung der Stadt Duisburg\r\n- Tombola Spielplan (https://docs.google.com/document/d/1ytumoRCpN86isFF9f6lWdr4wHjvUEgMaL5Uf89fxJ9c/edit?usp=sharing)', 'https://www.finanzamt.nrw.de/system/files/media/document/file/214_09_rennwlottg_anzeige_einer_lotterie_0.pdf', -21, -14, 1, 0, 1, '2026-11-09 21:31:15', '2026-11-11 07:40:06'),
(25, 3, NULL, 'Helferliste bereitstellen', 'Unter Aktionen > Helferliste erstellen', NULL, -21, -14, 1, 10, 1, '2026-11-09 21:33:18', '2026-11-09 21:33:18'),
(26, 3, NULL, 'Lose bestellen', '', NULL, -21, -14, 1, 20, 1, '2026-11-09 21:33:44', '2026-11-09 21:33:44'),
(27, 3, NULL, 'Gewinne sortieren und nummerieren', '', NULL, -28, -14, 1, 30, 1, '2026-11-09 21:34:23', '2026-11-09 21:34:23'),
(28, 2, NULL, 'Vorstand bei der Bank umtragen lassen', 'Protokoll der Hauptversammlung mit Satzung, Ausweise und Steueridentifikationsnummern der neuen Vorstandsmitglieder per Mail an kundenbetreuung.SC@volksbank-nierrhein.de senden. Am besten Telefonnummer für Rückfragen mit angeben.', NULL, 0, 14, 1, 0, 1, '2026-11-11 13:06:27', '2026-11-11 13:06:27');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Admin', 'Vollzugriff auf alle Funktionen', '2025-10-18 12:07:14'),
(2, 'Manager', 'Hat keinen Zugriff auf Sensible Bereiche', '2025-10-18 12:07:14'),
(3, 'Helfer', 'Kann Kontakte einsehen, Status ändern, Kommentare bearbeiten und neue Kontakte vorschlagen', '2025-10-18 12:07:14'),
(4, 'Vorstand', '', '2026-11-09 08:30:46'),
(5, 'Kassenwart', 'Alle Rechte des Vorstands plus Buchführung', '2026-11-10 07:27:02');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `assigned_at`) VALUES
(1, 1, '2026-11-09 16:00:18'),
(1, 2, '2026-11-09 16:00:18'),
(1, 3, '2026-11-09 16:00:18'),
(1, 4, '2026-11-09 16:00:18'),
(1, 5, '2026-11-09 16:00:18'),
(1, 6, '2026-11-09 16:00:18'),
(1, 7, '2026-11-09 16:00:18'),
(1, 8, '2026-11-09 16:00:18'),
(1, 9, '2026-11-09 16:00:18'),
(1, 10, '2026-11-09 16:00:18'),
(1, 11, '2026-11-09 16:00:18'),
(1, 12, '2026-11-09 16:00:18'),
(1, 13, '2026-11-09 16:00:18'),
(1, 14, '2026-11-09 16:00:18'),
(1, 15, '2026-11-09 16:00:18'),
(1, 16, '2026-11-09 16:00:18'),
(1, 17, '2026-11-09 16:00:18'),
(1, 18, '2026-11-09 16:00:18'),
(2, 1, '2026-11-07 19:10:53'),
(2, 2, '2026-11-07 19:10:53'),
(2, 3, '2026-11-07 19:10:53'),
(2, 4, '2026-11-07 19:10:53'),
(2, 5, '2026-11-07 19:10:53'),
(2, 6, '2026-11-07 19:10:53'),
(2, 8, '2026-11-07 19:10:53'),
(2, 9, '2026-11-07 19:10:53'),
(2, 10, '2026-11-07 19:10:53'),
(2, 11, '2026-11-07 19:10:53'),
(2, 12, '2026-11-07 19:10:53'),
(2, 14, '2026-11-07 19:10:53'),
(2, 15, '2026-11-07 19:10:53'),
(2, 17, '2026-11-07 19:10:53'),
(3, 1, '2026-10-15 09:37:58'),
(3, 10, '2026-10-15 09:37:58'),
(3, 11, '2026-10-15 09:37:58'),
(3, 12, '2026-10-15 09:37:58'),
(4, 1, '2026-11-10 07:30:22'),
(4, 2, '2026-11-10 07:30:22'),
(4, 3, '2026-11-10 07:30:22'),
(4, 4, '2026-11-10 07:30:22'),
(4, 5, '2026-11-10 07:30:22'),
(4, 6, '2026-11-10 07:30:22'),
(4, 7, '2026-11-10 07:30:22'),
(4, 8, '2026-11-10 07:30:22'),
(4, 9, '2026-11-10 07:30:22'),
(4, 10, '2026-11-10 07:30:22'),
(4, 11, '2026-11-10 07:30:22'),
(4, 12, '2026-11-10 07:30:22'),
(4, 13, '2026-11-10 07:30:22'),
(4, 14, '2026-11-10 07:30:22'),
(4, 15, '2026-11-10 07:30:22'),
(4, 16, '2026-11-10 07:30:22'),
(4, 17, '2026-11-10 07:30:22'),
(5, 1, '2026-11-10 07:27:55'),
(5, 2, '2026-11-10 07:27:55'),
(5, 3, '2026-11-10 07:27:55'),
(5, 4, '2026-11-10 07:27:55'),
(5, 5, '2026-11-10 07:27:55'),
(5, 6, '2026-11-10 07:27:55'),
(5, 7, '2026-11-10 07:27:55'),
(5, 8, '2026-11-10 07:27:55'),
(5, 9, '2026-11-10 07:27:55'),
(5, 10, '2026-11-10 07:27:55'),
(5, 11, '2026-11-10 07:27:55'),
(5, 12, '2026-11-10 07:27:55'),
(5, 13, '2026-11-10 07:27:55'),
(5, 14, '2026-11-10 07:27:55'),
(5, 15, '2026-11-10 07:27:55'),
(5, 16, '2026-11-10 07:27:55'),
(5, 17, '2026-11-10 07:27:55'),
(5, 18, '2026-11-10 07:27:55');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sales_actions`
--

CREATE TABLE `sales_actions` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('draft','active','closed','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `shipping_cents` int UNSIGNED NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sales_groups`
--

CREATE TABLE `sales_groups` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `sales_groups`
--

INSERT INTO `sales_groups` (`id`, `name`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Gruppe 1', 1, 1, '2026-10-19 07:52:24'),
(2, 'Gruppe 2', 2, 1, '2026-10-19 07:52:24'),
(3, 'Gruppe 3', 3, 1, '2026-10-19 07:52:24'),
(4, 'Gruppe 4', 4, 1, '2026-10-19 07:52:24');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sales_internal_order_items`
--

CREATE TABLE `sales_internal_order_items` (
  `action_id` int UNSIGNED NOT NULL,
  `item_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int UNSIGNED NOT NULL,
  `action_id` int UNSIGNED NOT NULL,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_price_cents` int UNSIGNED NOT NULL DEFAULT '0',
  `sale_price_cents` int UNSIGNED NOT NULL DEFAULT '0',
  `sold_to_parents` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `action_id` int UNSIGNED NOT NULL,
  `order_number` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int UNSIGNED NOT NULL,
  `total_cents` int UNSIGNED NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `is_packed` tinyint(1) NOT NULL DEFAULT '0',
  `is_issued` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `item_id` int UNSIGNED DEFAULT NULL,
  `item_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price_cents` int UNSIGNED NOT NULL,
  `line_total_cents` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standalone_tasks_backup_20261113`
--

CREATE TABLE `standalone_tasks_backup_20261113` (
  `id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `resource_url` varchar(2048) DEFAULT NULL,
  `start_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('open','in_progress','done') NOT NULL DEFAULT 'open',
  `assigned_user_id` int DEFAULT NULL,
  `claimed_at` datetime DEFAULT NULL,
  `recurrence_every` int DEFAULT NULL COMMENT 'Wiederholungsintervall; NULL = keine Wiederholung',
  `recurrence_unit` enum('week','month','year') DEFAULT NULL,
  `recurrence_source_id` int DEFAULT NULL COMMENT 'Vorgängeraufgabe dieser Wiederholungsreihe; jede erledigte Aufgabe erzeugt höchstens eine Folgeaufgabe',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `standalone_tasks_backup_20261113`
--

INSERT INTO `standalone_tasks_backup_20261113` (`id`, `title`, `description`, `resource_url`, `start_date`, `due_date`, `status`, `assigned_user_id`, `claimed_at`, `recurrence_every`, `recurrence_unit`, `recurrence_source_id`, `created_by`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 'Tannengrün kaufen', 'Für Dekoration zur Adventszeit. Menge mit Frau Baas abstimmen.', NULL, '2026-11-15', '2026-12-01', 'done', NULL, NULL, 1, 'year', NULL, 3, '2026-10-18 19:26:17', '2026-11-09 22:09:33', '2026-11-09 23:09:33'),
(2, 'Vergessene Bestellung bestellen', 's. WhatsApp\r\n\r\nBESTELLT: 4* https://wunderdraw.de/products/wiederverwendbares-regenbogen-wassermaltuch-wasserbecher-pinsel-gratis-dazu\r\n\r\n2* https://www.kalalier.de/products/entdeckertisch-mini?_pos=2&_sid=e8d6a12a2&_ss=r\r\n\r\n2* https://www.kalalier.de/products/entdeckerglas-kopie?_pos=1&_sid=e10f28c41&_ss=r\r\n\r\n1* https://www.kalalier.de/products/farbenkarussell?_pos=1&_psq=farbenkarussell&_ss=e&_v=1.0', NULL, '2026-10-22', '2026-10-24', 'in_progress', 3, '2026-11-09 23:08:58', NULL, NULL, NULL, 3, '2026-10-22 16:05:21', '2026-11-09 22:08:58', NULL),
(3, 'Mitgliedsbeiträge zurückbuchen', 'Köpp und Op de DRinck', NULL, '2026-10-30', '2026-11-02', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-22 16:09:51', '2026-11-03 15:43:07', '2026-11-03 16:43:07'),
(4, 'Vorstand beim Notar umtragen lassen', '', NULL, '2026-10-22', '2026-12-11', 'open', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-22 16:14:46', '2026-10-22 16:14:46', NULL),
(5, 'Bankkonto überschreiben lassen', 'Protokoll der Hauptversammlung mit Satzung, Ausweise und Steueridentifikationsnummern der neuen Vorstandsmitglieder per Mail an kundenbetreuung.SC@volksbank-nierrhein.de senden. Am besten Telefonnummer für Rückfragen mit angeben.', NULL, '2026-10-22', '2026-12-11', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-22 16:15:11', '2026-11-13 07:39:20', '2026-11-13 08:39:20'),
(6, 'Posterwand mit Anschaffungen vorbereiten', 'Die große Schrankwand im Eingangsbereich darf dekoriert werden.', NULL, '2026-10-23', '2026-11-01', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-23 06:10:53', '2026-11-03 09:39:05', '2026-11-03 10:39:05'),
(7, 'Spendenquittung für Alex Wende', '12 Edeka Lego Sets\r\n2 Dekra Kappies', NULL, '2026-10-29', '2026-11-05', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-29 08:15:35', '2026-11-01 14:01:42', '2026-11-01 15:01:42'),
(8, 'Spendenquittung Stahlkind (Tanja)', 'Tasse 12,95\r\nLoop Schal 7,95\r\nKulturbeutel 19,95\r\nStoffbeutel 5,95\r\n3x  Gutschein a 20€ =60€\r\nErdinger alkoholfrei Trinklasche (amazon 9,99 ebay 5,99)\r\nBitburger Flaschenöffner (amazon 5,99)\r\nKöPi Flaschenöffner (kleinanzeigen 15€)', NULL, '2026-10-29', '2026-11-05', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-29 08:17:00', '2026-11-01 14:04:43', '2026-11-01 15:04:43'),
(9, 'Sachspendenquittung Fr. Baas', '', NULL, '2026-10-30', '2026-11-01', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-10-30 12:39:40', '2026-11-01 10:00:25', '2026-11-01 11:00:25'),
(10, 'Kisten für Tombola bestellen', 'Really Useful Boxes:', 'https://www.buero-bedarf-thueringen.de', '2026-11-02', '2026-11-08', 'done', 3, '2026-11-09 23:09:01', NULL, NULL, NULL, 3, '2026-11-02 12:19:23', '2026-11-12 11:17:30', '2026-11-12 12:17:30'),
(11, 'Wegen U3 Klettergerüst anfragen', '- Wie lange ist die Lieferzeit \r\n- Kann es aufgebaut werden\r\n- Kann der Standort vorher geprüft werden', 'https://aurednik.de/de/kleinkinder-spiellandschaft-robinie-p75048/', '2026-11-02', '2026-11-08', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-11-02 12:23:16', '2026-11-03 15:43:12', '2026-11-03 16:43:12'),
(12, 'Tannengrün kaufen', 'Für Dekoration zur Adventszeit. Menge mit Frau Baas abstimmen.', NULL, '2027-11-15', '2027-12-01', 'open', NULL, NULL, 1, 'year', 1, 3, '2026-11-09 22:09:33', '2026-11-09 22:09:33', NULL),
(13, 'Conny anschreiben', 'Wegen Trödelmarkt drinnen oder draußen', NULL, '2026-11-12', '2026-11-18', 'done', NULL, NULL, NULL, NULL, NULL, 3, '2026-11-11 20:22:17', '2026-11-13 07:47:52', '2026-11-13 08:47:52'),
(14, 'Spendenquittung für Vakis ausstellen', '1.  Gutschein über einen Warenwert von 10 €\r\n2.  Gutschein für 1 Stück Kuchen gratis im wert von 4,90€ \r\n\r\nUnser Vorschlag wäre eine Aufteilung von 10x 10€ Gutscheinen und 10x Gutschein für ein gratis Stück Kuchen.', 'https://www.dropbox.com/scl/fi/ae4ok0o7qnunmnu8ovzdc/Vorlage_Sachzuwendung.docx?rlkey=zwwuvmmnvutg68t4dh8vzsp6d&dl=0', '2026-11-12', '2026-11-14', 'open', NULL, NULL, NULL, NULL, NULL, 3, '2026-11-12 11:22:32', '2026-11-12 11:22:32', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standalone_task_comments_backup_20261113`
--

CREATE TABLE `standalone_task_comments_backup_20261113` (
  `id` int NOT NULL,
  `standalone_task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `standalone_task_comments_backup_20261113`
--

INSERT INTO `standalone_task_comments_backup_20261113` (`id`, `standalone_task_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 4, 3, 'Miriam hat den Notar mit den nötigen Unterlagen angeschrieben.', '2026-10-28 12:32:21'),
(2, 2, 3, '4 Regenbogen Wassermaltücher sind bestellt. Den Rest bestelle ich, wenn ich dafür eine Rechnung erhalten habe. Wird sonst zu viel zum Vorstrecken.', '2026-11-11 12:55:26'),
(3, 10, 3, 'Welche Maße brauchen wir?', '2026-11-11 12:56:12'),
(4, 4, 3, 'Termin: 16. November um 10 Uhr', '2026-11-11 12:58:23'),
(5, 5, 3, 'Protokoll der Hauptversammlung mit Satzung, Ausweise und Steueridentifikationsnummern der neuen Vorstandsmitglieder per Mail an kundenbetreuung.SC@volksbank-nierrhein.de senden. Am besten Telefonnummer für Rückfragen mit angeben.', '2026-11-11 13:04:22'),
(6, 10, 3, '64L / Günstigster Gesamtpreis hier: https://www.buero-bedarf-thueringen.de / Bestellung per Vorkasse', '2026-11-11 21:24:04'),
(7, 10, 3, 'KIsten sind bestellt. Bezahlung per Vorkasse steht noch aus.', '2026-11-12 10:57:29');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `project_id` int DEFAULT NULL,
  `task_template_id` int DEFAULT NULL,
  `project_template_task_item_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `resource_url` varchar(2048) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('open','in_progress','done') NOT NULL DEFAULT 'open',
  `assigned_user_id` int DEFAULT NULL COMMENT 'Freiwillige Übernahme; keine feste Zuweisung aus der Vorlage',
  `claimed_at` datetime DEFAULT NULL,
  `recurrence_every` int DEFAULT NULL COMMENT 'Wiederholungsintervall; NULL = keine Wiederholung',
  `recurrence_unit` enum('week','month','year') DEFAULT NULL,
  `recurrence_source_id` int DEFAULT NULL COMMENT 'Vorgängeraufgabe dieser Wiederholungsreihe',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `task_template_id`, `project_template_task_item_id`, `title`, `description`, `resource_url`, `start_date`, `due_date`, `status`, `assigned_user_id`, `claimed_at`, `recurrence_every`, `recurrence_unit`, `recurrence_source_id`, `sort_order`, `created_by`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 1, NULL, 12, 'Termin festlegen', 'Termin Abstimmen und verfügbarkeit des Raums klären', NULL, '2026-08-26', NULL, 'done', NULL, NULL, NULL, NULL, NULL, -1, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-10-16 12:42:03'),
(2, 1, NULL, 13, 'Einladung erstellen und herausgeben', '- per Mail an Mitglieder\r\n- per Eltern App an alle Eltern\r\n- per Aushang im Kindergarten', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', NULL, NULL, 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-10-16 12:42:06'),
(3, 1, NULL, 9, 'Kassenschluss für das letzte Jahr', 'Optimalerweise zum wechsel des Geschäfttsjahres, aber spätestens zur Planung der Vollversammlung.', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', '2026-08-26', '2026-10-07', 'done', NULL, NULL, NULL, NULL, NULL, 1, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-10-16 12:29:55'),
(4, 1, NULL, 8, 'Kassenprüfung durch die Kassenprüfer', 'Die freiwilligen Kassenprüfer einberufen und die Kassenprüfung durchführen lassen', NULL, '2026-09-09', '2026-10-12', 'done', NULL, NULL, NULL, NULL, NULL, 2, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-10-16 12:30:04'),
(5, 1, NULL, NULL, 'Kassenbericht erstellen', 'Der Jahresbericht wird automatisch aus der Buchführung generiert. Aktuell noch per R Skript, dass muss ich noch allgemeiner verfügbar machen.', 'https://www.overleaf.com/project/66c701b0a359dbe56f3b005f/share#ab696c2c03acfc0f3813c184c6348a90d1676d368fdd87f3', '2026-10-07', '2026-10-19', 'done', NULL, NULL, NULL, NULL, NULL, 3, 3, '2026-10-16 10:09:59', '2026-10-16 10:32:26', '2026-10-16 12:32:26'),
(6, 1, NULL, 11, 'Protokoll vorbereiten', 'Die meisten Inhale stehen vorab, es kann also vorab der Orientierung dienen und das Protokollieren erleichtern.', 'https://www.overleaf.com/7927764558prnkpncsyqkz#a886e6', '2026-10-14', '2026-10-20', 'done', NULL, NULL, NULL, NULL, NULL, 4, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-10-16 12:31:46'),
(7, 1, NULL, 14, 'Protokoll fertigstellen', '', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', '2026-10-21', '2026-10-28', 'done', 3, '2026-10-18 21:36:06', NULL, NULL, NULL, 7, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-10-28 13:32:42'),
(8, 1, NULL, 15, 'Dokumente Unterzeichnen und abheften', 'Kassenberichtt, Protokoll, Berichtt der Kassenprüfer und Teilnehmerliste', NULL, '2026-10-21', '2026-11-04', 'done', NULL, NULL, NULL, NULL, NULL, 8, 3, '2026-10-16 10:09:59', '2026-11-11 16:31:56', '2026-11-01 10:26:01'),
(9, 2, 1, 1, 'Plakate gestalten und drucken', 'Plakate vorbereiten und den Druck organisieren.', NULL, '2026-08-22', '2026-10-03', 'done', NULL, NULL, NULL, NULL, NULL, 10, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 20:59:08'),
(10, 2, 2, 2, 'Sponsorenlauf bei der Stadt anmelden', 'Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.', NULL, '2026-09-19', '2026-10-03', 'done', NULL, NULL, NULL, NULL, NULL, 20, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 22:20:02'),
(11, 2, 3, 3, 'Tombola anmelden', 'Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.', NULL, '2026-09-19', '2026-10-03', 'done', NULL, NULL, NULL, NULL, NULL, 30, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 20:59:12'),
(12, 2, 4, 4, 'Medaillen organisieren', 'Medaillenbedarf klären und rechtzeitig bestellen.', NULL, '2026-09-05', '2026-10-10', 'done', NULL, NULL, NULL, NULL, NULL, 40, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 20:59:14'),
(13, 2, 5, 5, 'Urkunden vorbereiten', 'Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.', NULL, '2026-09-26', '2026-10-14', 'done', NULL, NULL, NULL, NULL, NULL, 50, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 20:59:16'),
(14, 2, 6, 6, 'Teilnehmerliste vorbereiten', 'Anmeldung und Teilnehmerliste zusammenführen und prüfen.', NULL, '2026-10-03', '2026-10-14', 'done', NULL, NULL, NULL, NULL, NULL, 60, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 20:59:18'),
(15, 2, 7, 7, 'Kaffee und Verpflegung organisieren', 'Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.', NULL, '2026-10-03', '2026-10-15', 'done', NULL, NULL, NULL, NULL, NULL, 70, 3, '2026-10-17 06:31:44', '2026-11-11 16:31:56', '2026-10-17 20:59:19'),
(16, 2, NULL, 16, 'Runden und Sponsorenangaben in Tabelle übernehmen', 'Die Berechnung, welcher Sponsore wieviel zahlen muss, muss erstellt werden', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', '2026-10-17', '2026-10-24', 'done', 3, '2026-10-18 21:36:02', NULL, NULL, NULL, 500, 3, '2026-10-17 20:21:01', '2026-11-11 16:31:56', '2026-10-21 10:39:57'),
(17, 2, NULL, 17, 'Abrechnungsmails generieren und versenden', 'Aktuell noch ein lokales R Skript', NULL, '2026-10-21', '2026-10-28', 'done', 3, '2026-10-22 13:22:52', NULL, NULL, NULL, 500, 3, '2026-10-17 20:23:31', '2026-11-11 16:31:56', '2026-10-22 18:04:30'),
(18, 2, NULL, 18, 'Wunschliste kaufen', 'Die Wunschliste ist au der Seite des Sponsorenlaufs hinterlegt', 'https://sponsorenlauf.evkigarumeln.de/', '2026-11-14', '2026-11-28', 'open', NULL, NULL, NULL, NULL, NULL, 500, 3, '2026-10-17 21:15:58', '2026-11-11 16:31:56', NULL),
(19, 3, 1, 1, 'Plakate gestalten und drucken', 'Plakate vorbereiten und den Druck organisieren.', NULL, '2027-03-27', '2027-05-08', 'open', NULL, NULL, NULL, NULL, NULL, 10, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(20, 3, 2, 2, 'Sponsorenlauf bei der Stadt anmelden', 'Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.', NULL, '2027-04-24', '2027-05-08', 'open', NULL, NULL, NULL, NULL, NULL, 20, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(21, 3, 3, 3, 'Tombola anmelden', 'Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.', NULL, '2027-04-24', '2027-05-08', 'open', NULL, NULL, NULL, NULL, NULL, 30, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(22, 3, 4, 4, 'Medaillen organisieren', 'Medaillenbedarf klären und rechtzeitig bestellen.', NULL, '2027-04-10', '2027-05-15', 'open', NULL, NULL, NULL, NULL, NULL, 40, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(23, 3, 5, 5, 'Urkunden vorbereiten', 'Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.', NULL, '2027-05-01', '2027-05-19', 'open', NULL, NULL, NULL, NULL, NULL, 50, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(24, 3, 6, 6, 'Teilnehmerliste vorbereiten', 'Anmeldung und Teilnehmerliste zusammenführen und prüfen.', NULL, '2027-05-08', '2027-05-19', 'open', NULL, NULL, NULL, NULL, NULL, 60, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(25, 3, 7, 7, 'Kaffee und Verpflegung organisieren', 'Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.', NULL, '2027-05-08', '2027-05-20', 'open', NULL, NULL, NULL, NULL, NULL, 70, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(26, 3, NULL, 16, 'Runden und Sponsorenangaben in Tabelle übernehmen', 'Die Berechnung, welcher Sponsore wieviel zahlen muss, muss erstellt werden', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', '2027-05-22', '2027-05-29', 'open', NULL, NULL, NULL, NULL, NULL, 80, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(27, 3, NULL, 17, 'Abrechnungsmails generieren und versenden', 'Aktuell noch ein lokales R Skript', NULL, '2027-05-26', '2027-06-02', 'open', NULL, NULL, NULL, NULL, NULL, 90, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(28, 3, NULL, 18, 'Wunschliste kaufen', 'Die Wunschliste ist au der Seite des Sponsorenlaufs hinterlegt', 'https://sponsorenlauf.evkigarumeln.de/', '2027-06-19', '2027-07-03', 'open', NULL, NULL, NULL, NULL, NULL, 100, 3, '2026-10-17 21:16:46', '2026-11-11 16:31:56', NULL),
(29, 2, NULL, 20, 'Kassen zählen und Buchführung aktualisieren', '', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', '2026-10-17', '2026-10-20', 'done', NULL, NULL, NULL, NULL, NULL, 500, 3, '2026-10-17 21:41:39', '2026-11-11 16:31:56', '2026-10-18 21:35:59'),
(30, 3, NULL, 20, 'Kassen zählen und Buchführung aktualisieren', '', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', '2027-05-22', '2027-05-26', 'open', NULL, NULL, NULL, NULL, NULL, 500, 3, '2026-10-17 21:49:06', '2026-11-11 16:31:56', NULL),
(31, 1, NULL, 21, 'Engagement-Nachweise erstellen', 'Künftig kann auch der Förderverein ev. Kindergarten Duisburg Rumeln-Kaldenhausen e.V. den Engagementnachweis NRW an seine ehrenamtlich Engagierten ausstellen. \r\n\r\nhttps://www.engagiert-in-nrw.de/engagementnachweis-hinweise-und-formulare        \r\n\r\nBenutzername: Engagementnachweis\r\n\r\nPasswort: TiqL8E4V', 'https://www.overleaf.com/3567354428nfcghztbqsbr#a8c09e', '2026-08-01', '2026-10-19', 'done', 3, '2026-10-18 21:35:34', NULL, NULL, NULL, 500, 3, '2026-10-18 19:34:34', '2026-11-11 16:31:56', '2026-10-21 10:39:48'),
(32, 1, NULL, 22, 'Neue Flyer drucken', '', NULL, '2026-10-22', '2026-10-28', 'done', 3, '2026-10-22 23:11:53', NULL, NULL, NULL, 500, 3, '2026-10-21 12:30:20', '2026-11-11 16:31:56', '2026-10-23 21:43:39'),
(33, 4, NULL, 23, 'Tombola bei der Stadt anmelden', '', NULL, '2026-10-30', '2026-11-06', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-11-09 21:31:44', '2026-11-13 07:25:06', '2026-11-13 08:25:06'),
(34, 4, NULL, 24, 'Tombola beim Finanzamt Köln anmelden', 'Folgende Unterlagen müssen per Brief eingereicht werden: \r\n- Formular: rennwlottg_anzeige_einer_lotterie_0\r\n- der aktuelle Freistellungsbescheid\r\n- Genehmigung der Stadt Duisburg\r\n- Tombola Spielplan ( https://docs.google.com/document/d/1ytumoRCpN86isFF9f6lWdr4wHjvUEgMaL5Uf89fxJ9c/edit?tab=t.0#heading=h.2c7pbkp748p6 )\r\n\r\nAnschreiben hier: https://www.overleaf.com/project/672dd9f646873713d55154de/share#fe99f2273861be4ad7b37847c05a330a6d5244a2b67d5c7c', 'https://www.finanzamt.nrw.de/system/files/media/document/file/214_09_rennwlottg_anzeige_einer_lotterie_0.pdf', '2026-11-06', '2026-11-13', 'done', 3, '2026-11-11 13:57:17', NULL, NULL, NULL, 0, 3, '2026-11-09 21:31:44', '2026-11-13 08:49:59', '2026-11-13 09:49:59'),
(35, 4, NULL, 25, 'Helferliste bereitstellen', 'Unter Aktionen > Helferliste erstellen', NULL, '2026-11-06', '2026-11-13', 'done', NULL, NULL, NULL, NULL, NULL, 500, 3, '2026-11-09 21:33:18', '2026-11-11 16:31:56', '2026-11-09 23:09:16'),
(36, 4, NULL, 26, 'Lose bestellen', '', NULL, '2026-11-06', '2026-11-13', 'open', NULL, NULL, NULL, NULL, NULL, 500, 3, '2026-11-09 21:33:44', '2026-11-11 16:31:56', NULL),
(37, 4, NULL, 27, 'Gewinne sortieren und nummerieren', '', NULL, '2026-10-30', '2026-11-13', 'done', NULL, NULL, NULL, NULL, NULL, 500, 3, '2026-11-09 21:34:23', '2026-11-12 20:48:02', '2026-11-12 21:48:02'),
(38, NULL, NULL, NULL, 'Tannengrün kaufen', 'Für Dekoration zur Adventszeit. Menge mit Frau Baas abstimmen.', NULL, '2026-11-15', '2026-12-01', 'done', NULL, NULL, 1, 'year', NULL, 0, 3, '2026-10-18 19:26:17', '2026-11-09 22:09:33', '2026-11-09 23:09:33'),
(39, NULL, NULL, NULL, 'Vergessene Bestellung bestellen', 's. WhatsApp\r\n\r\nBESTELLT: 4* https://wunderdraw.de/products/wiederverwendbares-regenbogen-wassermaltuch-wasserbecher-pinsel-gratis-dazu\r\n\r\n2* https://www.kalalier.de/products/entdeckertisch-mini?_pos=2&_sid=e8d6a12a2&_ss=r\r\n\r\n2* https://www.kalalier.de/products/entdeckerglas-kopie?_pos=1&_sid=e10f28c41&_ss=r\r\n\r\n1* https://www.kalalier.de/products/farbenkarussell?_pos=1&_psq=farbenkarussell&_ss=e&_v=1.0', NULL, '2026-10-22', '2026-10-24', 'in_progress', 3, '2026-11-09 23:08:58', NULL, NULL, NULL, 0, 3, '2026-10-22 16:05:21', '2026-11-09 22:08:58', NULL),
(40, NULL, NULL, NULL, 'Mitgliedsbeiträge zurückbuchen', 'Köpp und Op de DRinck', NULL, '2026-10-30', '2026-11-02', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-22 16:09:51', '2026-11-03 15:43:07', '2026-11-03 16:43:07'),
(41, NULL, NULL, NULL, 'Vorstand beim Notar umtragen lassen', '', NULL, '2026-10-22', '2026-12-11', 'open', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-22 16:14:46', '2026-10-22 16:14:46', NULL),
(42, NULL, NULL, NULL, 'Bankkonto überschreiben lassen', 'Protokoll der Hauptversammlung mit Satzung, Ausweise und Steueridentifikationsnummern der neuen Vorstandsmitglieder per Mail an kundenbetreuung.SC@volksbank-nierrhein.de senden. Am besten Telefonnummer für Rückfragen mit angeben.', NULL, '2026-10-22', '2026-12-11', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-22 16:15:11', '2026-11-13 07:39:20', '2026-11-13 08:39:20'),
(43, NULL, NULL, NULL, 'Posterwand mit Anschaffungen vorbereiten', 'Die große Schrankwand im Eingangsbereich darf dekoriert werden.', NULL, '2026-10-23', '2026-11-01', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-23 06:10:53', '2026-11-03 09:39:05', '2026-11-03 10:39:05'),
(44, NULL, NULL, NULL, 'Spendenquittung für Alex Wende', '12 Edeka Lego Sets\r\n2 Dekra Kappies', NULL, '2026-10-29', '2026-11-05', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-29 08:15:35', '2026-11-01 14:01:42', '2026-11-01 15:01:42'),
(45, NULL, NULL, NULL, 'Spendenquittung Stahlkind (Tanja)', 'Tasse 12,95\r\nLoop Schal 7,95\r\nKulturbeutel 19,95\r\nStoffbeutel 5,95\r\n3x  Gutschein a 20€ =60€\r\nErdinger alkoholfrei Trinklasche (amazon 9,99 ebay 5,99)\r\nBitburger Flaschenöffner (amazon 5,99)\r\nKöPi Flaschenöffner (kleinanzeigen 15€)', NULL, '2026-10-29', '2026-11-05', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-29 08:17:00', '2026-11-01 14:04:43', '2026-11-01 15:04:43'),
(46, NULL, NULL, NULL, 'Sachspendenquittung Fr. Baas', '', NULL, '2026-10-30', '2026-11-01', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-10-30 12:39:40', '2026-11-01 10:00:25', '2026-11-01 11:00:25'),
(47, NULL, NULL, NULL, 'Kisten für Tombola bestellen', 'Really Useful Boxes:', 'https://www.buero-bedarf-thueringen.de', '2026-11-02', '2026-11-08', 'done', 3, '2026-11-09 23:09:01', NULL, NULL, NULL, 0, 3, '2026-11-02 12:19:23', '2026-11-12 11:17:30', '2026-11-12 12:17:30'),
(48, NULL, NULL, NULL, 'Wegen U3 Klettergerüst anfragen', '- Wie lange ist die Lieferzeit \r\n- Kann es aufgebaut werden\r\n- Kann der Standort vorher geprüft werden', 'https://aurednik.de/de/kleinkinder-spiellandschaft-robinie-p75048/', '2026-11-02', '2026-11-08', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-11-02 12:23:16', '2026-11-03 15:43:12', '2026-11-03 16:43:12'),
(49, NULL, NULL, NULL, 'Tannengrün kaufen', 'Für Dekoration zur Adventszeit. Menge mit Frau Baas abstimmen.', NULL, '2027-11-15', '2027-12-01', 'open', NULL, NULL, 1, 'year', 38, 0, 3, '2026-11-09 22:09:33', '2026-11-09 22:09:33', NULL),
(50, NULL, NULL, NULL, 'Conny anschreiben', 'Wegen Trödelmarkt drinnen oder draußen', NULL, '2026-11-12', '2026-11-18', 'done', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-11-11 20:22:17', '2026-11-13 07:47:52', '2026-11-13 08:47:52'),
(51, NULL, NULL, NULL, 'Spendenquittung für Vakis ausstellen', '1.  Gutschein über einen Warenwert von 10 €\r\n2.  Gutschein für 1 Stück Kuchen gratis im wert von 4,90€ \r\n\r\nUnser Vorschlag wäre eine Aufteilung von 10x 10€ Gutscheinen und 10x Gutschein für ein gratis Stück Kuchen.', 'https://www.dropbox.com/scl/fi/ae4ok0o7qnunmnu8ovzdc/Vorlage_Sachzuwendung.docx?rlkey=zwwuvmmnvutg68t4dh8vzsp6d&dl=0', '2026-11-12', '2026-11-14', 'open', NULL, NULL, NULL, NULL, NULL, 0, 3, '2026-11-12 11:22:32', '2026-11-12 11:22:32', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `task_comments`
--

CREATE TABLE `task_comments` (
  `id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `task_comments`
--

INSERT INTO `task_comments` (`id`, `task_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 17, 3, 'Abrechnungsmails sind generiert und liegen als Text in der DropBox. Mails müssen noch von Hand versendet werden', '2026-10-22 11:24:24'),
(2, 33, 3, 'Das Formular ist zur Unterschrift beim RTV', '2026-11-09 22:07:27'),
(3, 33, 3, 'Die Anmeldung ist an die Stadt gesendet (Mail vom 11.11.2026)', '2026-11-11 07:21:42'),
(4, 37, 3, '11. November ab 18 Uhr im Kinderkeller.', '2026-11-11 12:56:48'),
(5, 34, 3, 'Ich warte noch bis Donnerstag eine Rückmeldung der Stadt ab, dann sende ich die Anmeldung ohne die Genehmigung der Stadt ein.', '2026-11-11 12:57:50'),
(6, 34, 3, 'Formular, Freistellungsbescheid, Spielplan und Anschreiben liegen in der Dropbox\\Förderverein\\KiGa-Jahr_26-27\\2026-11-27__Weihnachtsmarkt-Tombola\\Anmeldung_Finanzamt-Koeln', '2026-11-12 20:20:02'),
(7, 34, 3, 'Anschreiben ist vorbereitet und geht morgen in die Post ( Einschreiben einwurf)', '2026-11-12 20:47:37'),
(8, 41, 3, 'Miriam hat den Notar mit den nötigen Unterlagen angeschrieben.', '2026-10-28 12:32:21'),
(9, 39, 3, '4 Regenbogen Wassermaltücher sind bestellt. Den Rest bestelle ich, wenn ich dafür eine Rechnung erhalten habe. Wird sonst zu viel zum Vorstrecken.', '2026-11-11 12:55:26'),
(10, 47, 3, 'Welche Maße brauchen wir?', '2026-11-11 12:56:12'),
(11, 41, 3, 'Termin: 16. November um 10 Uhr', '2026-11-11 12:58:23'),
(12, 42, 3, 'Protokoll der Hauptversammlung mit Satzung, Ausweise und Steueridentifikationsnummern der neuen Vorstandsmitglieder per Mail an kundenbetreuung.SC@volksbank-nierrhein.de senden. Am besten Telefonnummer für Rückfragen mit angeben.', '2026-11-11 13:04:22'),
(13, 47, 3, '64L / Günstigster Gesamtpreis hier: https://www.buero-bedarf-thueringen.de / Bestellung per Vorkasse', '2026-11-11 21:24:04'),
(14, 47, 3, 'KIsten sind bestellt. Bezahlung per Vorkasse steht noch aus.', '2026-11-12 10:57:29');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `task_comment_merge_map_20261113`
--

CREATE TABLE `task_comment_merge_map_20261113` (
  `old_standalone_comment_id` int NOT NULL,
  `new_comment_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Daten für Tabelle `task_comment_merge_map_20261113`
--

INSERT INTO `task_comment_merge_map_20261113` (`old_standalone_comment_id`, `new_comment_id`) VALUES
(1, 8),
(2, 9),
(3, 10),
(4, 11),
(5, 12),
(6, 13),
(7, 14);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `task_merge_map_20261113`
--

CREATE TABLE `task_merge_map_20261113` (
  `old_standalone_task_id` int NOT NULL,
  `new_task_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Daten für Tabelle `task_merge_map_20261113`
--

INSERT INTO `task_merge_map_20261113` (`old_standalone_task_id`, `new_task_id`) VALUES
(1, 38),
(2, 39),
(3, 40),
(4, 41),
(5, 42),
(6, 43),
(7, 44),
(8, 45),
(9, 46),
(10, 47),
(11, 48),
(12, 49),
(13, 50),
(14, 51);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `task_templates`
--

CREATE TABLE `task_templates` (
  `id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `start_offset_days` int DEFAULT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  `due_offset_days` int DEFAULT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `task_templates`
--

INSERT INTO `task_templates` (`id`, `title`, `description`, `start_offset_days`, `due_offset_days`, `sort_order`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Plakate gestalten und drucken', 'Plakate vorbereiten und den Druck organisieren.', -56, -14, 10, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(2, 'Sponsorenlauf bei der Stadt anmelden', 'Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.', -28, -14, 20, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(3, 'Tombola anmelden', 'Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.', -28, -14, 30, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(4, 'Medaillen organisieren', 'Medaillenbedarf klären und rechtzeitig bestellen.', -42, -7, 40, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(5, 'Urkunden vorbereiten', 'Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.', -21, -3, 50, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(6, 'Teilnehmerliste vorbereiten', 'Anmeldung und Teilnehmerliste zusammenführen und prüfen.', -14, -3, 60, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39'),
(7, 'Kaffee und Verpflegung organisieren', 'Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.', -14, -2, 70, 1, NULL, '2026-10-16 09:06:39', '2026-10-16 09:06:39');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `comment` text,
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `full_name`, `comment`, `is_active`, `created_at`, `last_login`, `updated_at`) VALUES
(3, 'martina', '$2y$10$jEX7DxO0rQs2yUXo5nCiFepthyKNoMouY2PcCEPHj4EVQ.jKKA0vu', 'martinafriese@gmail.com', 'Martina Echtenbruck', '.', 1, '2025-10-19 09:26:59', '2026-11-13 23:40:35', '2026-11-13 23:40:35'),
(8, 'Tanja', '$2y$10$vtl6b5klcLlaBJMvCOwDUeT3M5Y044x.hWFLS/1QKlNXJsb8f00Ke', 'tanja_205@web.de', 'Tanja Wende', '', 1, '2025-10-24 11:54:52', '2026-11-06 16:55:55', '2026-11-06 16:55:55'),
(11, 'Julia Perret', '$2y$10$u.7fiQGIlogJ5X3baMTwROAmPKW8A.XrblezSt0sYFBXIpA4t57Sm', 'perret_julia@gmx.de', 'Julia Perret', '', 1, '2025-11-28 15:48:08', '2026-09-22 18:45:52', '2026-09-22 18:45:52'),
(12, 'FamKruppa', '$2y$10$0sWvfBiucv04GyxRJHN49e9esc0J7ONiDMjmXwNWdJkQOsBUO43/m', 'jannik.kruppa@gmx.de', 'Jannik Kruppa', 'Papa von Pauline aus Gruppe 3\r\nBzw. Familienaccount', 1, '2026-09-07 08:09:22', '2026-09-09 13:54:55', '2026-09-09 13:54:55'),
(13, 'Miriam Klaßen', '$2y$10$t4MtpVUjQBg4Ov9ct5kyWuql5vfBp3xEn8svMjSnP9usl10gYnzl.', 'miriam_klassen@t-online.de', 'Miriam Klaßen', '', 1, '2026-09-18 07:46:22', '2026-11-09 21:35:42', '2026-11-09 21:35:42'),
(14, 'Daniela', '$2y$10$GEBWBNl2U8Ez1dmA9KbsfecPx4fX/kbcWBJZ6Nu34r1zsF8ZM9cOG', 'danielalebenicnik@web.de', 'Daniela Gröters', '', 1, '2026-09-26 07:04:59', '2026-11-13 18:49:44', '2026-11-13 18:49:44'),
(15, 'Möhren', '$2y$10$lS8seSnwMfHJvOO1wcgZROFnfxgCxHfzYGqFQ4fzfvMGzvUXyFKrO', 'f.moehren@gmx.de', 'Fabienne Möhren', '', 1, '2026-09-30 13:07:40', '2026-09-30 16:54:36', '2026-09-30 16:54:36'),
(17, 'GIera.1624', '$2y$10$kTRUUTcNWQHd8Y3f0VImm.rJ9Pt8rQUhOS5PIwnUfChNQ3DuDEQ8q', 'nicole-gie@gmx.de', 'Nicole Giera', '', 1, '2026-10-20 14:22:46', '2026-10-21 12:55:48', '2026-10-21 12:55:48'),
(18, 'martina-testet', '$2y$10$I2vkNeBl5OH.B5eaIhilIu0IMbPVGAJNMV.svydOX5FBhv/B1B1MK', 'martina.echtenbruck@th-koeln.de', 'Martina Echtenbruck (testet)', '', 1, '2026-10-28 12:06:07', '2026-11-05 19:24:04', '2026-11-05 19:24:04'),
(19, 'Sally Voß', '$2y$10$heCAa.scxPHLRFzniEqHneL6JGgh7xSNUR4uZgg5aT0MiGQYoy6Ju', 'sally_wittmers@gmx.de', 'Sally Voß', '', 1, '2026-11-04 11:10:36', NULL, '2026-11-04 12:34:18');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_notification_preferences`
--

CREATE TABLE `user_notification_preferences` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `notification_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `push_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(3, 1, '2025-10-21 06:58:30'),
(8, 2, '2026-11-11 07:52:53'),
(11, 4, '2026-11-09 09:22:48'),
(12, 3, '2026-09-07 08:09:22'),
(13, 5, '2026-11-10 07:30:31'),
(14, 2, '2026-09-30 14:37:01'),
(15, 3, '2026-09-30 13:07:40'),
(17, 3, '2026-10-20 14:22:46'),
(18, 3, '2026-10-28 12:06:07'),
(19, 3, '2026-11-04 11:10:36');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `webpush_subscriptions`
--

CREATE TABLE `webpush_subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `endpoint_hash` char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `subscription_json` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `webpush_subscriptions`
--

INSERT INTO `webpush_subscriptions` (`id`, `user_id`, `endpoint_hash`, `subscription_json`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 3, '6657f793a473aa1a1d5f8beb73c0cbe9c082686936c794d2e7dcc9b9f25f88be', '{\"endpoint\":\"https://updates.push.services.mozilla.com/wpush/v2/gAAAAABq40pvkDgxPwMxkhGP3Kz6zb1CS1uG1Zw0MvN6QavM-GJvTcY0pAfxxIyQTpyPi8ehTRQoEZ-bHBNsO5-gMCUev7HHhMQltNYuZEP3zMZuQCF44MsQ1K1mKjhUws_XlYNFqvEmotdXq5RYzn4_5xcLRf3UnAREKjavOXtUis7mkzMUjSQ\",\"expirationTime\":null,\"keys\":{\"p256dh\":\"BBZWA5RAsOg0XAtwrUW541VxzzAauVX3xISIfTw5kzbI9ZY3HYxbsR7Y-V9fGnI-TWZioN3ocAP9BpPD9gHhrQ4\",\"auth\":\"zeEl6hTUrYnOBxc_Ijzg8g\"}}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', '2026-10-29 14:30:23', '2026-10-29 14:30:23'),
(2, 3, '1c5d1dc8f12f8f2f6c017bab0e39c1e8f2d66532148719ef705957ce6edad6b9', '{\"endpoint\":\"https://web.push.apple.com/QCUzMUuCbUl99WnMLmhcFYnbqlrEIdVqg8YmM5Id-l6pcC0TyNmW4E3QRkR35t4VzvyuIkb2D9yYY6LhmAWPl9KhL0PX9KwrgryU3rIllYAm5aZzWULtNVt_PykXXytZm6PiWPjmZgmAHjvgdKjfx9IT9T0PUACIeLhGaRZR7jY\",\"expirationTime\":null,\"keys\":{\"p256dh\":\"BMht8g39OEMMBqWE5MMRI7gw0qz1dfMsrsP2x_PI2o_B02XzpC6EUD-HQ9agVgRodJbj8c-J_REjW3MXB5D0mQY\",\"auth\":\"lQisvSIMFfa2HkxmUHs2ZQ\"}}', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/27.0.1 Mobile/15E148 Safari/604.1', '2026-10-29 14:57:25', '2026-10-29 14:57:25'),
(3, 13, '9c053811fe770137628587c8a8d214adcf8da7ee1c746c468f9cfea393cfd309', '{\"endpoint\":\"https://web.push.apple.com/QBDg-qzetBk-F43UsTX6B4jmhYBtH5E7nYQcl76RfC8TNhmEc3JCYvdAqMfwBkiBmoVg2EN-6Ys2CuU1_A_VB-QOyaaA266b3pn5DHW8CXuM653IcDe6lLERyZV6E5Ab549JFJ6C85xwdpvUco7l8y33oE-UENOkHnlkII4nhjQ\",\"expirationTime\":null,\"keys\":{\"p256dh\":\"BJLbpXQpwy7IHIj1I7OyH2hJRRuTPzQWYrSFaaWfPDJ-DGN8EVG4HYwXb1hUeueegs01TI3wpIvnqcsJpGVvl2c\",\"auth\":\"aA6kn3K7pzO8pPGsKW1Sbw\"}}', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6.1 Mobile/15E148 Safari/604.1', '2026-11-06 22:20:37', '2026-11-06 22:20:37');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `accounting_bank_reconciliations`
--
ALTER TABLE `accounting_bank_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accounting_reconciliations_year` (`fiscal_year_id`,`reconciled_until`),
  ADD KEY `fk_accounting_reconciliations_user` (`created_by`);

--
-- Indizes für die Tabelle `accounting_bookings`
--
ALTER TABLE `accounting_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accounting_bookings_date` (`booking_date`,`id`),
  ADD KEY `idx_accounting_bookings_year` (`fiscal_year_id`,`booking_date`),
  ADD KEY `idx_accounting_bookings_category` (`category_id`),
  ADD KEY `fk_accounting_bookings_user` (`created_by`),
  ADD KEY `idx_accounting_bookings_tax_area` (`tax_area`);

--
-- Indizes für die Tabelle `accounting_cash_counts`
--
ALTER TABLE `accounting_cash_counts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accounting_cash_counts_year` (`fiscal_year_id`,`counted_on`),
  ADD KEY `fk_accounting_cash_counts_user` (`created_by`);

--
-- Indizes für die Tabelle `accounting_categories`
--
ALTER TABLE `accounting_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accounting_categories_active` (`is_active`,`sort_order`);

--
-- Indizes für die Tabelle `accounting_documents`
--
ALTER TABLE `accounting_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_accounting_document_reference` (`reference_code`),
  ADD KEY `idx_accounting_documents_booking` (`booking_id`);

--
-- Indizes für die Tabelle `accounting_fiscal_years`
--
ALTER TABLE `accounting_fiscal_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_accounting_fiscal_year_label` (`label`),
  ADD UNIQUE KEY `uq_accounting_fiscal_year_dates` (`start_date`,`end_date`),
  ADD KEY `idx_accounting_fiscal_year_closed_by` (`closed_by`);

--
-- Indizes für die Tabelle `accounting_system_settings`
--
ALTER TABLE `accounting_system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `primary_key` (`primary_key`),
  ADD KEY `idx_contacts_status` (`status`),
  ADD KEY `idx_contacts_email` (`emailadresse`),
  ADD KEY `idx_contacts_datum` (`zuletzt_angeschrieben`),
  ADD KEY `idx_contacts_typ` (`typ`),
  ADD KEY `idx_contacts_suggestion` (`is_suggestion`);

--
-- Indizes für die Tabelle `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fundraising_campaigns`
--
ALTER TABLE `fundraising_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fundraising_campaign_active` (`ended_at`,`started_at`),
  ADD KEY `fk_fundraising_campaign_started_by` (`started_by`),
  ADD KEY `fk_fundraising_campaign_ended_by` (`ended_by`);

--
-- Indizes für die Tabelle `helper_actions`
--
ALTER TABLE `helper_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_helper_actions_status` (`status`,`start_date`,`end_date`);

--
-- Indizes für die Tabelle `helper_assignments`
--
ALTER TABLE `helper_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_helper_assignment` (`registration_id`,`day_id`,`station_id`,`slot_key`),
  ADD KEY `idx_helper_slot` (`day_id`,`station_id`,`slot_key`),
  ADD KEY `fk_helper_assignment_station` (`station_id`);

--
-- Indizes für die Tabelle `helper_days`
--
ALTER TABLE `helper_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_helper_day` (`action_id`,`event_date`);

--
-- Indizes für die Tabelle `helper_day_stations`
--
ALTER TABLE `helper_day_stations`
  ADD PRIMARY KEY (`day_id`,`station_id`),
  ADD KEY `fk_helper_ds_station` (`station_id`);

--
-- Indizes für die Tabelle `helper_registrations`
--
ALTER TABLE `helper_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_helper_token` (`edit_token_hash`),
  ADD KEY `idx_helper_reg_action` (`action_id`,`status`);

--
-- Indizes für die Tabelle `helper_stations`
--
ALTER TABLE `helper_stations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_helper_station_action` (`action_id`,`sort_order`);

--
-- Indizes für die Tabelle `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_members_member_number` (`member_number`),
  ADD KEY `idx_members_name` (`last_name`,`first_name`),
  ADD KEY `idx_members_dates` (`start_date`,`end_date`),
  ADD KEY `idx_members_status_end_date` (`status`,`end_date`);

--
-- Indizes für die Tabelle `member_mail_batches`
--
ALTER TABLE `member_mail_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_mail_batches_created_at` (`created_at`);

--
-- Indizes für die Tabelle `member_mail_recipients`
--
ALTER TABLE `member_mail_recipients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_member_mail_batch_member` (`batch_id`,`member_id`),
  ADD KEY `idx_member_mail_recipients_status` (`send_status`),
  ADD KEY `fk_member_mail_recipients_member` (`member_id`);

--
-- Indizes für die Tabelle `member_number_sequence`
--
ALTER TABLE `member_number_sequence`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_notification_event_user` (`user_id`,`notification_type`,`entity_type`,`entity_id`,`event_key`),
  ADD KEY `idx_notification_user_state` (`user_id`,`status`,`is_read`,`created_at`),
  ADD KEY `idx_notification_entity` (`entity_type`,`entity_id`);

--
-- Indizes für die Tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indizes für die Tabelle `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indizes für die Tabelle `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_projects_status_dates` (`status`,`event_date`,`start_date`),
  ADD KEY `idx_projects_fiscal_year` (`fiscal_year`),
  ADD KEY `fk_projects_template` (`template_id`),
  ADD KEY `fk_projects_creator` (`created_by`);

--
-- Indizes für die Tabelle `project_task_resources`
--
ALTER TABLE `project_task_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project_task_resources_task` (`task_id`),
  ADD KEY `idx_project_task_resources_template` (`template_task_item_id`),
  ADD KEY `fk_ptr_created_by` (`created_by`);

--
-- Indizes für die Tabelle `project_templates`
--
ALTER TABLE `project_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project_templates_active` (`is_active`),
  ADD KEY `fk_project_templates_creator` (`created_by`);

--
-- Indizes für die Tabelle `project_template_tasks`
--
ALTER TABLE `project_template_tasks`
  ADD PRIMARY KEY (`project_template_id`,`task_template_id`),
  ADD KEY `fk_ptt_task_template` (`task_template_id`);

--
-- Indizes für die Tabelle `project_template_task_items`
--
ALTER TABLE `project_template_task_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ptti_template_active` (`project_template_id`,`is_active`,`sort_order`),
  ADD KEY `idx_ptti_source` (`source_task_template_id`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indizes für die Tabelle `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indizes für die Tabelle `sales_actions`
--
ALTER TABLE `sales_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_actions_public` (`status`,`start_date`,`end_date`),
  ADD KEY `idx_sales_actions_completed` (`status`,`completed_at`),
  ADD KEY `fk_sales_action_creator` (`created_by`),
  ADD KEY `fk_sales_action_completer` (`completed_by`);

--
-- Indizes für die Tabelle `sales_groups`
--
ALTER TABLE `sales_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sales_group_name` (`name`);

--
-- Indizes für die Tabelle `sales_internal_order_items`
--
ALTER TABLE `sales_internal_order_items`
  ADD PRIMARY KEY (`action_id`,`item_id`),
  ADD KEY `fk_sales_internal_item` (`item_id`);

--
-- Indizes für die Tabelle `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_items_action` (`action_id`,`sort_order`);

--
-- Indizes für die Tabelle `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sales_order_number` (`order_number`),
  ADD KEY `idx_sales_orders_action` (`action_id`,`created_at`),
  ADD KEY `idx_sales_orders_group` (`group_id`);

--
-- Indizes für die Tabelle `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_order_items_order` (`order_id`),
  ADD KEY `idx_sales_order_items_item` (`item_id`);

--
-- Indizes für die Tabelle `standalone_tasks_backup_20261113`
--
ALTER TABLE `standalone_tasks_backup_20261113`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_standalone_recurrence_source` (`recurrence_source_id`),
  ADD KEY `idx_standalone_status_dates` (`status`,`start_date`,`due_date`),
  ADD KEY `idx_standalone_assignee` (`assigned_user_id`),
  ADD KEY `fk_standalone_creator` (`created_by`);

--
-- Indizes für die Tabelle `standalone_task_comments_backup_20261113`
--
ALTER TABLE `standalone_task_comments_backup_20261113`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_standalone_task_comments_task` (`standalone_task_id`,`created_at`),
  ADD KEY `fk_standalone_task_comments_user` (`user_id`);

--
-- Indizes für die Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tasks_recurrence_source` (`recurrence_source_id`),
  ADD KEY `idx_tasks_project_status` (`project_id`,`status`),
  ADD KEY `idx_tasks_due_date` (`due_date`),
  ADD KEY `idx_tasks_assignee` (`assigned_user_id`),
  ADD KEY `fk_tasks_template` (`task_template_id`),
  ADD KEY `fk_tasks_creator` (`created_by`),
  ADD KEY `idx_tasks_project_template_task_item` (`project_template_task_item_id`);

--
-- Indizes für die Tabelle `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_comments_task_date` (`task_id`,`created_at`),
  ADD KEY `fk_task_comments_user` (`user_id`);

--
-- Indizes für die Tabelle `task_comment_merge_map_20261113`
--
ALTER TABLE `task_comment_merge_map_20261113`
  ADD PRIMARY KEY (`old_standalone_comment_id`),
  ADD UNIQUE KEY `uq_task_comment_merge_new_id` (`new_comment_id`);

--
-- Indizes für die Tabelle `task_merge_map_20261113`
--
ALTER TABLE `task_merge_map_20261113`
  ADD PRIMARY KEY (`old_standalone_task_id`),
  ADD UNIQUE KEY `uq_task_merge_new_id` (`new_task_id`);

--
-- Indizes für die Tabelle `task_templates`
--
ALTER TABLE `task_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_templates_active_order` (`is_active`,`sort_order`),
  ADD KEY `fk_task_templates_creator` (`created_by`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_notification_type` (`user_id`,`notification_type`),
  ADD KEY `idx_notification_preferences_type` (`notification_type`);

--
-- Indizes für die Tabelle `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indizes für die Tabelle `webpush_subscriptions`
--
ALTER TABLE `webpush_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_webpush_endpoint_hash` (`endpoint_hash`),
  ADD KEY `idx_webpush_user` (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `accounting_bank_reconciliations`
--
ALTER TABLE `accounting_bank_reconciliations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT für Tabelle `accounting_bookings`
--
ALTER TABLE `accounting_bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- AUTO_INCREMENT für Tabelle `accounting_cash_counts`
--
ALTER TABLE `accounting_cash_counts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `accounting_categories`
--
ALTER TABLE `accounting_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT für Tabelle `accounting_documents`
--
ALTER TABLE `accounting_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `accounting_fiscal_years`
--
ALTER TABLE `accounting_fiscal_years`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=561;

--
-- AUTO_INCREMENT für Tabelle `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;

--
-- AUTO_INCREMENT für Tabelle `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `fundraising_campaigns`
--
ALTER TABLE `fundraising_campaigns`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `helper_actions`
--
ALTER TABLE `helper_actions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `helper_assignments`
--
ALTER TABLE `helper_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT für Tabelle `helper_days`
--
ALTER TABLE `helper_days`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `helper_registrations`
--
ALTER TABLE `helper_registrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT für Tabelle `helper_stations`
--
ALTER TABLE `helper_stations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT für Tabelle `member_mail_batches`
--
ALTER TABLE `member_mail_batches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `member_mail_recipients`
--
ALTER TABLE `member_mail_recipients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12086;

--
-- AUTO_INCREMENT für Tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `project_task_resources`
--
ALTER TABLE `project_task_resources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `project_templates`
--
ALTER TABLE `project_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `project_template_task_items`
--
ALTER TABLE `project_template_task_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT für Tabelle `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `sales_actions`
--
ALTER TABLE `sales_actions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `sales_groups`
--
ALTER TABLE `sales_groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `standalone_tasks_backup_20261113`
--
ALTER TABLE `standalone_tasks_backup_20261113`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `standalone_task_comments_backup_20261113`
--
ALTER TABLE `standalone_task_comments_backup_20261113`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT für Tabelle `task_comments`
--
ALTER TABLE `task_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `task_templates`
--
ALTER TABLE `task_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `webpush_subscriptions`
--
ALTER TABLE `webpush_subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `accounting_bank_reconciliations`
--
ALTER TABLE `accounting_bank_reconciliations`
  ADD CONSTRAINT `fk_accounting_reconciliations_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_accounting_reconciliations_year` FOREIGN KEY (`fiscal_year_id`) REFERENCES `accounting_fiscal_years` (`id`) ON DELETE RESTRICT;

--
-- Constraints der Tabelle `accounting_bookings`
--
ALTER TABLE `accounting_bookings`
  ADD CONSTRAINT `fk_accounting_bookings_category` FOREIGN KEY (`category_id`) REFERENCES `accounting_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_accounting_bookings_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_accounting_bookings_year` FOREIGN KEY (`fiscal_year_id`) REFERENCES `accounting_fiscal_years` (`id`) ON DELETE RESTRICT;

--
-- Constraints der Tabelle `accounting_cash_counts`
--
ALTER TABLE `accounting_cash_counts`
  ADD CONSTRAINT `fk_accounting_cash_counts_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_accounting_cash_counts_year` FOREIGN KEY (`fiscal_year_id`) REFERENCES `accounting_fiscal_years` (`id`) ON DELETE RESTRICT;

--
-- Constraints der Tabelle `accounting_documents`
--
ALTER TABLE `accounting_documents`
  ADD CONSTRAINT `fk_accounting_documents_booking` FOREIGN KEY (`booking_id`) REFERENCES `accounting_bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `accounting_fiscal_years`
--
ALTER TABLE `accounting_fiscal_years`
  ADD CONSTRAINT `fk_accounting_fiscal_year_closed_by` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `fundraising_campaigns`
--
ALTER TABLE `fundraising_campaigns`
  ADD CONSTRAINT `fk_fundraising_campaign_ended_by` FOREIGN KEY (`ended_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fundraising_campaign_started_by` FOREIGN KEY (`started_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `helper_assignments`
--
ALTER TABLE `helper_assignments`
  ADD CONSTRAINT `fk_helper_assignment_day` FOREIGN KEY (`day_id`) REFERENCES `helper_days` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_helper_assignment_reg` FOREIGN KEY (`registration_id`) REFERENCES `helper_registrations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_helper_assignment_station` FOREIGN KEY (`station_id`) REFERENCES `helper_stations` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `helper_days`
--
ALTER TABLE `helper_days`
  ADD CONSTRAINT `fk_helper_day_action` FOREIGN KEY (`action_id`) REFERENCES `helper_actions` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `helper_day_stations`
--
ALTER TABLE `helper_day_stations`
  ADD CONSTRAINT `fk_helper_ds_day` FOREIGN KEY (`day_id`) REFERENCES `helper_days` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_helper_ds_station` FOREIGN KEY (`station_id`) REFERENCES `helper_stations` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `helper_registrations`
--
ALTER TABLE `helper_registrations`
  ADD CONSTRAINT `fk_helper_reg_action` FOREIGN KEY (`action_id`) REFERENCES `helper_actions` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `helper_stations`
--
ALTER TABLE `helper_stations`
  ADD CONSTRAINT `fk_helper_station_action` FOREIGN KEY (`action_id`) REFERENCES `helper_actions` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `member_mail_recipients`
--
ALTER TABLE `member_mail_recipients`
  ADD CONSTRAINT `fk_member_mail_recipients_batch` FOREIGN KEY (`batch_id`) REFERENCES `member_mail_batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_member_mail_recipients_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_projects_template` FOREIGN KEY (`template_id`) REFERENCES `project_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `project_task_resources`
--
ALTER TABLE `project_task_resources`
  ADD CONSTRAINT `fk_ptr_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ptr_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ptr_template_task_item` FOREIGN KEY (`template_task_item_id`) REFERENCES `project_template_task_items` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `project_templates`
--
ALTER TABLE `project_templates`
  ADD CONSTRAINT `fk_project_templates_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `project_template_tasks`
--
ALTER TABLE `project_template_tasks`
  ADD CONSTRAINT `fk_ptt_project_template` FOREIGN KEY (`project_template_id`) REFERENCES `project_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ptt_task_template` FOREIGN KEY (`task_template_id`) REFERENCES `task_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `project_template_task_items`
--
ALTER TABLE `project_template_task_items`
  ADD CONSTRAINT `fk_ptti_project_template` FOREIGN KEY (`project_template_id`) REFERENCES `project_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ptti_source_template` FOREIGN KEY (`source_task_template_id`) REFERENCES `task_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `sales_actions`
--
ALTER TABLE `sales_actions`
  ADD CONSTRAINT `fk_sales_action_completer` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sales_action_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `sales_internal_order_items`
--
ALTER TABLE `sales_internal_order_items`
  ADD CONSTRAINT `fk_sales_internal_action` FOREIGN KEY (`action_id`) REFERENCES `sales_actions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sales_internal_item` FOREIGN KEY (`item_id`) REFERENCES `sales_items` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `fk_sales_item_action` FOREIGN KEY (`action_id`) REFERENCES `sales_actions` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `fk_sales_order_action` FOREIGN KEY (`action_id`) REFERENCES `sales_actions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sales_order_group` FOREIGN KEY (`group_id`) REFERENCES `sales_groups` (`id`);

--
-- Constraints der Tabelle `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `fk_sales_order_item_item` FOREIGN KEY (`item_id`) REFERENCES `sales_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sales_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `standalone_tasks_backup_20261113`
--
ALTER TABLE `standalone_tasks_backup_20261113`
  ADD CONSTRAINT `fk_standalone_assignee` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_standalone_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_standalone_recurrence_source` FOREIGN KEY (`recurrence_source_id`) REFERENCES `standalone_tasks_backup_20261113` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `standalone_task_comments_backup_20261113`
--
ALTER TABLE `standalone_task_comments_backup_20261113`
  ADD CONSTRAINT `fk_standalone_task_comments_task` FOREIGN KEY (`standalone_task_id`) REFERENCES `standalone_tasks_backup_20261113` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_standalone_task_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints der Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_assignee` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tasks_recurrence_source` FOREIGN KEY (`recurrence_source_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_template` FOREIGN KEY (`task_template_id`) REFERENCES `task_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `task_comments`
--
ALTER TABLE `task_comments`
  ADD CONSTRAINT `fk_task_comments_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `task_templates`
--
ALTER TABLE `task_templates`
  ADD CONSTRAINT `fk_task_templates_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD CONSTRAINT `fk_user_notification_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
