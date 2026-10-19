-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 10.35.233.43:3306
-- Erstellungszeit: 19. Okt 2026 um 09:50
-- Server-Version: 8.4.11
-- PHP-Version: 8.4.25

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
(107, 113, 'Ravensburger', 'liebes Team von Ravensburger', 'ravensburger.de', 'info@ravensburger.de', 'https://www.ravensburger-gruppe.de/de/verantwortung/spendenkonzept-von-ravensburger/index.html', '', '', '2025-10-02', '', 'keine Unterstützung von: Tombola-Anfragen von Schulen und Kindergärten', '2025-10-07 09:57:35', '2026-09-16 11:13:16', 'Formular', 0),
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
(130, 136, 'Nintendo', 'liebes Team von Nintendo', '', 'nintendo@punkt-pr.de', '', '', 'Antwort abwarten', '2026-09-29', 'Alte Adresse: nintendo@sz-scala.de', '', '2025-10-07 09:57:35', '2026-09-29 07:49:02', 'Mail', 0),
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
(149, 155, 'Archäologischer Park Xanten', 'liebe Frau Kühlborn', 'lvr.de', 'bianca.kuehlborn@lvr.de', '', '', 'Antwort abwarten', '2026-09-23', '', 'Freikarten - bianca.kuehlborn@lvr.de / Allgemein: apx@lvr.de', '2025-10-07 09:57:35', '2026-09-23 17:55:02', 'Mail', 0),
(150, 156, 'Haniel', 'liebes Team von Haniel', 'haniel.de', 'info@haniel.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 11:05:15', '2026-09-25 08:21:35', 'Mail', 0),
(155, 157, 'Wiemann Lehrmittel', 'liebes Team von Wiemann Lehrmittel', 'wiemann-lehrmittel.de', 'info@wiemann-lehrmittel.de', NULL, NULL, 'Antwort abwarten', '2026-09-25', '', '', '2025-10-07 18:56:37', '2026-09-25 08:45:12', 'Mail', 0),
(163, 158, 'Afrodita beauty & health', '', '', '', '', 'Düsseldorfer Str. 22, 47239', '', NULL, '', '', '2025-10-11 09:36:59', '2025-10-11 09:36:59', 'Persönlich', 0),
(164, 159, 'Allianz Robert Glöder', '', '', '', '', 'Giesenfeldstr., 47239', '', NULL, '', '2024: Barspende', '2025-10-11 09:37:18', '2025-10-11 10:25:07', 'Persönlich', 0),
(166, 161, 'Apotheke', '', '', '', '', 'Am Geistfeld, 47239', '', NULL, '', '2024: viele nette Präsente', '2025-10-11 09:42:59', '2025-10-11 10:25:56', 'Persönlich', 0),
(167, 162, 'Apotheke Ärtzehaus Moers', '', '', '', '', 'Goethestr., 47441', '', NULL, '', '', '2025-10-11 09:43:37', '2025-10-11 09:43:37', 'Persönlich', 0),
(168, 163, 'Apotheke Dorfstrasse', '', '', '', '', 'Dorfstraße, 47239', '', '2025-10-10', '', '2025: 100€ Spende 2024: viele kleine Präsente  2023: Werbegeschenke, Trostpreise', '2025-10-11 09:44:20', '2026-09-25 10:44:51', 'Persönlich', 0),
(169, 164, 'Arriotti-Beauty und viel mehr GmbH', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Gutschein Fußpflege 20€', '2025-10-11 09:44:37', '2025-10-11 10:26:07', 'Persönlich', 0),
(170, 165, 'back2school', '', '', '', '', 'Düsseldorfer Str. 128, 47239', '', NULL, '', '2024: Trostpreise', '2025-10-11 09:44:44', '2025-10-11 10:26:21', 'Persönlich', 0),
(171, 166, 'Bäckerei Büsch', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: keine Rückmeldung', '2025-10-11 09:44:58', '2025-10-11 10:28:42', 'Persönlich', 0),
(172, 167, 'Bäckerei Wiedemann', '', '', '', '', 'Düsseldorfer Str. 96, 47239', '', NULL, '', '', '2025-10-11 09:45:08', '2025-10-11 09:45:08', 'Persönlich', 0),
(173, 168, 'Balettschule Köse', '', '', '', '', 'Asterlager Str., 47228', '', NULL, '', '2024: Gutscheine + Kleinkram', '2025-10-11 09:46:15', '2025-10-11 10:28:54', 'Persönlich', 0),
(174, 169, 'Baumschule Moldenhauer', '', '', '', '', 'Sittardweg 17, 47239', '', NULL, '', '2025: nicht angefragt 2024: 5 Pflanzen', '2025-10-11 09:46:21', '2026-09-16 11:13:16', 'Persönlich', 0),
(175, 170, 'Bella Italia Gino', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 3 Gutscheine', '2025-10-11 09:46:40', '2025-10-11 10:29:48', 'Persönlich', 0),
(176, 171, 'Blumenladen bei Edeka (Name?)', '', '', '', '', 'Düsseldorfer Str. 107, 47239', '', NULL, '', '2024: Deko + Pflanzen', '2025-10-11 09:46:48', '2025-10-11 17:38:35', 'Persönlich', 0),
(177, 172, 'Capelli Connection', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: meldet sich evtl,', '2025-10-11 09:46:57', '2025-10-11 10:54:46', 'Persönlich', 0),
(178, 173, 'City-Grill', '', '', '', '', 'Düsseldorfer Str. 20, 47239', '', NULL, '', '2024: 5x gutschein', '2025-10-11 09:47:03', '2025-10-11 10:54:57', 'Persönlich', 0),
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
(197, 192, 'Lagerverkauf', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 3 Kisten Lebkuchenherzen', '2025-10-11 09:50:21', '2025-10-11 12:12:41', 'Persönlich', 0),
(198, 193, 'Leelawadee Massage', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: 10€ bar', '2025-10-11 09:50:29', '2025-10-11 11:11:31', 'Persönlich', 0),
(199, 194, 'Malerbetrieb Sven Schmitt', '', '', '', '', 'Landrat-von-Laer-Str. 9, 47239', '', NULL, '', 'Ansprechpartner: Sven Schmidt (Cousin Sally)', '2025-10-11 09:50:34', '2026-09-16 08:43:27', 'Persönlich', 0),
(200, 195, 'Massagepraxis Deltahaus', '', '', '', '', 'Wagnerstr., 47239', '', NULL, '', '', '2025-10-11 09:50:39', '2025-10-11 11:11:52', 'Persönlich', 0),
(201, 196, 'Memo\'s Pizza', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: 2x 5,50', '2025-10-11 09:50:54', '2025-10-11 11:14:01', 'Persönlich', 0),
(202, 197, 'Metzgerei Heinen', '', '', '', '', 'Kapellener Str. 11, 47239', 'Spende abgesagt', '2025-10-14', '', '2026: Keine Preise abzugeben, nächstes Jahr wieder anfragen | 2025: Chef Cuisine Gusseisen Bräter 2024: alles bereits abgegeben, nächstes Jahr im Oktober fragen', '2025-10-11 09:51:02', '2026-09-30 09:50:24', 'Persönlich', 0),
(203, 198, 'MUNDO Mio Kindermode', '', '', '', '', 'Neustraße 21, 47441', '', NULL, '', '', '2025-10-11 09:51:10', '2025-10-11 11:16:28', 'Persönlich', 0),
(204, 199, 'Musikschule Rosenberger & Pögner', '', '', '', '', 'Dorfstraße 19, 47239', '', NULL, '', '2024: meldet sich, 3 Gutscheine', '2025-10-11 09:51:21', '2025-10-11 11:12:10', 'Persönlich', 0),
(205, 200, 'Optik Krasser', '', '', '', '', 'Düsseldorfer Str. 111, 47239', '', NULL, '', '2024: nichts', '2025-10-11 09:51:27', '2025-10-11 11:13:31', 'Persönlich', 0),
(207, 202, 'Optik Peerebooms', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: Kindersonnenbrille', '2025-10-11 09:51:44', '2025-10-11 11:12:31', 'Persönlich', 0),
(208, 203, 'Optives Ärztehaus Moers', '', '', '', '', 'Goethestr., 47441', '', NULL, '', '', '2025-10-11 09:51:51', '2025-10-11 09:51:51', 'Persönlich', 0),
(209, 204, 'Pane e Vino', '', '', '', '', 'Am Sportplatz, 47239', '', NULL, 'Cecile', '2024: 50€ Gutschein 2023: 2 Gutscheine a 10€', '2025-10-11 09:52:05', '2026-09-25 12:18:14', 'Persönlich', 0),
(210, 205, 'Pizzeria Roma', '', '', '', '', '-', '', NULL, '', '2024: 1 Gutschein 20€', '2025-10-11 09:52:20', '2025-10-11 11:16:53', 'Persönlich', 0),
(211, 206, 'Pizzeria Samra', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: 5xkleine Pizza, 3 Gutscheine f.eine große Pizza 2023: Gutschein über 10 Euro', '2025-10-11 09:52:33', '2025-10-11 11:17:43', 'Persönlich', 0),
(212, 207, 'Pizzeria Trestelle', '', '', '', '', 'Asterlager Str., 47228', '', NULL, '', 'Ansprechpartner: Richem Andelast', '2025-10-11 09:52:37', '2026-09-16 08:44:28', 'Persönlich', 0),
(213, 208, 'provinzial Versicherung', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', 'nichts', '2025-10-11 09:52:42', '2025-10-11 11:17:31', 'Persönlich', 0),
(214, 209, 'Pur Natur', '', '', '', '', 'Neustraße 23, 47441', '', NULL, '', '2024: Tüte Kleinkram', '2025-10-11 09:52:52', '2025-10-11 11:18:01', 'Persönlich', 0),
(215, 210, 'Raiffeisenmarkt Schwafheim', '', '', '', '', 'Schwarzer Weg, 47447', '', NULL, '', '2024: Bälle, Trinkflaschen', '2025-10-11 09:53:02', '2025-10-11 12:15:08', 'Persönlich', 0),
(216, 211, 'Raumwerk', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: keine Rückmeldung 2023: 1 Handtasche (laut Preisschild für 219€!!!)', '2025-10-11 09:53:18', '2025-10-11 12:16:26', 'Persönlich', 0),
(217, 212, 'Reisebüro Klatt', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: Trostpreise', '2025-10-11 09:53:34', '2026-09-16 11:13:16', 'Persönlich', 0),
(218, 213, 'REWE Johann', 'lieber Herr Johann', '', 'rewe-rumeln@gmx.de', '', 'Rathausallee, 47239', 'Antwort abwarten', '2026-09-25', '', 'diverse Werbeartikel u.a. von Gerolsteiner // Marktleiter 02151-4029751 Ergebnis: diverse Werbeartikel u.a. von Gerolsteiner', '2025-10-11 09:53:39', '2026-09-25 08:35:08', 'Mail', 0),
(219, 214, 'Rheinland Apotheke', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: Sporttasche u. Trostpreise', '2025-10-11 09:53:45', '2025-10-11 11:18:14', 'Persönlich', 0),
(220, 215, 'Rumelner Grill', '', '', '', '', 'Dorfstraße, 47239', '', NULL, '', '2024: Gutscheine 5x 10€', '2025-10-11 09:53:54', '2025-10-11 12:22:00', 'Persönlich', 0),
(221, 216, 'Rumelner TV', '', '', '', '', 'Am Sportplatz 15, 47239', '', NULL, '', '2024: 5 Mützen, 5 Schals', '2025-10-11 09:54:00', '2025-10-11 12:22:13', 'Persönlich', 0),
(222, 217, 'Sanitätshaus Am Geistfeld e.K.', '', '', '', '', 'Am Geistfeld, 47239', '', NULL, '', '2024: viele nette Präsente', '2025-10-11 09:54:07', '2025-10-11 12:17:41', 'Persönlich', 0),
(223, 218, 'Sparkasse', '', '', '', '', 'Düsseldorfer Str., 47239', '', NULL, '', '2024: keine Rückmeldung', '2025-10-11 09:54:15', '2025-10-11 12:17:28', 'Persönlich', 0),
(224, 219, 'Stahlkind', '', '', '', '', 'Dorfstraße, 47239', '', '2025-10-11', 'Tanja', '2025: 2x Gutschein (30€) + Handtuch + Tasse + Kulturbeutel 2024: 2x Gutschein+viele Sachspenden (150€)', '2025-10-11 09:54:33', '2026-09-25 12:16:53', 'Persönlich', 0),
(225, 220, 'Steakhaus Schwafheim', '', '', '', '', ', 47447', '', NULL, 'Tanja', '2024: 2x15€ Gutscheine,', '2025-10-11 09:54:42', '2026-09-25 12:17:10', 'Persönlich', 0),
(226, 221, 'Thetford', 'liebes Team von Thetford', '', 'infod@thetford.eu', '', 'Schallbruch 14, 42781', 'Antwort abwarten', '2026-09-25', '', '2025: Campingtoilette + viele Zubehörflaschen + WC Papier 2023: Campingtoilette', '2025-10-11 09:54:48', '2026-09-25 08:42:06', 'Mail', 0),
(227, 222, 'Total Tankstelle', '', '', '', '', 'Düsseldorfer Str. 51, 47239', '', NULL, '', '2024: nichts', '2025-10-11 09:54:55', '2025-10-11 12:16:39', 'Persönlich', 0),
(228, 223, 'trinkgut', '', '', '', '', 'Kapellener Str., 47239', '', NULL, '', '2024: diverse Kleinigkeiten', '2025-10-11 09:55:00', '2025-10-11 10:30:37', 'Persönlich', 0),
(229, 224, 'Volksbank', '', '', '', '', 'Dorfstraße 65, 47239', '', '2025-10-10', '', '2024: viele nette Präsente', '2025-10-11 09:55:18', '2026-09-25 10:45:30', 'Persönlich', 0),
(230, 225, 'Wasserskianlage Toeppersee', '', '', '', '', 'Lohfelder Weg 91, 47239', '', NULL, '', '2024: diverse Gutscheine', '2025-10-11 09:55:29', '2025-10-11 10:30:25', 'Persönlich', 0),
(231, 226, 'dm', 'liebes Team von dm', '', '', '', 'Lange Str. 67 47829 Krefeld', '', NULL, '', '', '2025-10-11 12:24:21', '2025-10-11 12:24:21', 'Persönlich', 0),
(232, 227, 'dm', 'liebes Team von dm', '', '', '', 'Atroper Str. 9-13 47226 Duisburg', '', NULL, '', '', '2025-10-11 12:25:13', '2025-10-11 12:25:13', 'Persönlich', 0),
(233, 228, 'Rossmann', 'liebes Team von Rossmann', '', '', '', 'Höschenstr. 69 47228', '', NULL, '', '', '2025-10-11 12:26:00', '2025-10-11 12:26:00', 'Persönlich', 0),
(235, 229, 'UCI Kinowelt', 'Liebes Team vom UCI', 'https://uci-kinowelt.de', 'zentrale@uci-kinowelt.de', '', '', 'Antwort abwarten', '2026-09-25', '', '', '2025-10-15 05:36:18', '2026-09-25 08:43:12', 'Mail', 0),
(236, 230, 'Phantasialand', 'liebes Team von Phantasialand', 'https://phantasialand.de', 'shop@phantasialand.de', '', '', 'Antwort abwarten', '2026-09-25', '', '2025: 4 Freikarten', '2025-10-15 05:37:56', '2026-09-25 08:33:18', 'Mail', 0),
(237, 231, 'Okidoki Kinderland', 'liebes Team vom Okidoki Kinderland', 'https://okidoki-kinderland.de', 'info@okidoki-kinderland.de', '', '', 'Handlungsbedarf', '2026-09-25', 'Sie können gegen Vorlage einer Abholberechtigung Ihres Vereins bei uns\nan der Info 5 große Geschenke und 5 x 2 Freikarten abholen.', '2026: 5 Geschenke und Freikarten | 2025: Geschenke und Freikarten ', '2025-10-15 05:40:07', '2026-09-25 09:23:01', 'Mail', 0),
(240, 232, 'Movie Park Bottrop', 'liebes Team vom Movie Park', 'https://moviepark.de', 'info@moviepark.de', '', '', 'Spende abgesagt', '2026-09-25', '', '2025: generelle Absage', '2025-10-22 08:42:02', '2026-09-25 10:48:42', 'Mail', 0),
(247, 234, 'REWE Stiftung Dortmund', 'liebes Team der REWE Stiftung', 'https://www.rewe-dortmund.de/stiftung/', 'stiftung-rewe-dortmund@rewe-group.com', 'https://www.rewe.de/service/kontakt/', '', 'Antwort abwarten', '2026-10-02', 'Von der Seite:\r\n1. Mail an stiftung-rewe-dortmund@rewe-group.com mit einer kurzen Vorstellung des Vereins, Projektvorstellung etc. \r\n2. Es erfolgt eine Rückmeldung, ob das Thema/Projekt zu unseren Förderrichtlinien passt. Die Rückmeldung kann aufgrund der quartalsweise stattfindenden Vorstandssitzung etwas Zeit in Anspruch nehmen.\r\n3. Bei positivem Bescheid gilt es den Förderantrag auszufüllen und alles weitere erfolgt dann in direkter Absprache.', 'Anfrage in Zukunft über die REWE Stiftung. Online Formulare herunterladbar. Mailadresse: stiftung@rewe-dortmund.de  //2025: Spende 500€', '2025-10-24 07:45:41', '2026-10-02 07:02:29', 'Mail', 0),
(248, 235, 'Venator Corp', 'liebes Team von Venator', 'https://venatorcorp.com', 'Duisburg_info@venatorcorp.com', '', '', 'Antwort abwarten', '2026-09-25', '', NULL, '2025-10-25 13:40:43', '2026-09-25 08:43:30', 'Mail', 0),
(250, 237, 'Rossmann', 'liebes Team von Rossmann', 'rossmann.de', '', '', 'Höschenstraße 69, 47228 Duisburg-Bergheim', 'Antwort abwarten', NULL, NULL, NULL, '2026-09-23 18:01:55', '2026-09-23 18:01:55', 'Persönlich', 0),
(251, 238, 'dm Drogeriemarkt', 'liebes Team von dm Drogeriemarkt', '', '', '', 'Atroper Str. 9-13; 47226 Duisburg', 'Antwort abwarten', NULL, NULL, NULL, '2026-09-23 18:03:28', '2026-09-23 18:03:28', 'Persönlich', 0),
(252, 239, 'Karls Erlebnis-Dorf', 'liebes Team von Karls Erlebnis-Dorf', 'https://karls.de', 'post@karls.de', '', '', 'Antwort abwarten', '2026-09-23', '', NULL, '2026-09-23 18:19:22', '2026-09-23 18:19:44', 'Mail', 0),
(258, 242, 'Neue Tischkultur', 'liebes Team von Neue Tischkultur', 'https://neuetischkultur.de', 'service@neuetischkultur.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 06:47:02', '2026-10-02 06:47:41', 'Mail', 0),
(259, 243, 'Kosmos', 'liebe Frau Jäkel', 'https://kosmos.de', 'a.jaekel@kosmos.de', '', '', 'Spende abgesagt', '2026-10-02', '', '2026: Generelle Absage - nur mit ausgewählten Partnern', '2026-10-02 16:39:56', '2026-10-05 07:26:23', 'Mail', 0),
(260, 244, 'ENNI', 'liebes Team von ENNI', 'https://enni.de', 'spenden_sponsoring_werbung@enni.de', 'https://www.enni.de/spendenanfrage', '', '', '2026-10-02', '', '', '2026-10-02 16:41:29', '2026-10-05 05:21:49', 'Formular', 0),
(261, 245, 'EWE', 'liebes Team von EWE', 'https://ewe.de', 'ma-sp@ewe.de', '', '', 'Spende abgesagt', '2026-10-02', '', '2026: Generelle Absage - agiert nur lokal', '2026-10-02 16:43:02', '2026-10-05 07:25:14', 'Mail', 0),
(262, 246, 'Mainova', 'liebes Team von Mainova', 'https://mainova.de', 'tombola@mainova.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 16:44:42', '2026-10-02 16:45:03', 'Mail', 0),
(263, 247, 'Grafik Werkstatt', 'liebes Team von Grafik Werkstatt', 'https://grafik-werkstatt.de', 'webshop@grafik-werkstatt.de', '', '', 'Spende zugesagt', '2026-10-02', 'Spendenquittung erwünscht', '2026: Sachspenden', '2026-10-02 16:45:36', '2026-10-16 11:13:36', 'Mail', 0),
(264, 248, 'Stiftung Lesen', 'liebes Team von Stiftung Lesen', 'https://stiftunglesen.de', 'mail@stiftunglesen.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 16:52:39', '2026-10-02 16:53:24', 'Mail', 0),
(265, 249, 'Steiff', 'liebes Team von Steiff', 'https://steiff.com', 'info@steiff.com', '', '', 'Spende abgesagt', '2026-10-02', '', '2026: generelle absage ', '2026-10-02 16:54:25', '2026-10-05 09:32:05', 'Mail', 0),
(266, 250, 'Kaufland', 'liebes Team von Kaufland', 'https://kaufland.de', 'info@kaufland.de', '', '', 'Antwort abwarten', '2026-10-02', '', NULL, '2026-10-02 17:00:06', '2026-10-02 17:00:32', 'Mail', 0),
(267, 251, 'Kaufland', 'liebes Team von Kaufland', 'https://kaufland.de', '', '', 'Kirchstraße 96, 47198 Homberg-Ruhrort-Baerl', 'Antwort abwarten', NULL, NULL, NULL, '2026-10-02 17:01:56', '2026-10-02 17:01:56', 'Persönlich', 0);
INSERT INTO `contacts` (`id`, `primary_key`, `name`, `anrede`, `webseite`, `emailadresse`, `formular_link`, `anschrift`, `status`, `zuletzt_angeschrieben`, `kommentar`, `letztes_ergebnis`, `created_at`, `updated_at`, `typ`, `is_suggestion`) VALUES
(268, 252, 'Böttcher AG', '', 'https://www.bueromarkt-ag.de/', '', 'https://www.bueromarkt-ag.de/service/spendenformular', '', 'Antwort abwarten', NULL, NULL, NULL, '2026-10-02 17:03:18', '2026-10-02 17:03:18', 'Formular', 0),
(269, 253, 'Vakis Coffee Club', 'liebes Team von Vakis Coffee Club', 'https://vakis.net', 'kontakt@vakis.net', '', '', 'Spende zugesagt', '2026-10-15', '', '2026: 10 Gutscheine', '2026-10-15 08:42:12', '2026-10-18 18:17:05', 'Mail', 0),
(270, 254, 'Martina Testet die neue App', 'liebes Team von Martina Testet die neue App', 'https://sweetchaos.de', 'martinafriese@gmail.com', '', '', 'Antwort abwarten', '2026-10-15', '', '', '2026-10-15 10:13:07', '2026-10-15 10:56:32', 'Mail', 0),
(271, 255, 'Martina testet noch mal', 'liebes Team von Martina testet noch mal', 'https://sweetchaos.de', 'martinafriese@gmail.com', '', '', 'Antwort abwarten', '2026-10-15', '', '', '2026-10-15 10:54:35', '2026-10-15 10:56:49', 'Mail', 0),
(272, 256, 'Spiegelburg', 'liebes Team der Spiegelburg', 'https://coppenrath.de', 'info@coppenrath.de', '', '', 'Antwort abwarten', '2026-10-18', NULL, NULL, '2026-10-18 09:02:27', '2026-10-18 09:05:41', 'Mail', 0),
(273, 257, 'Loewe Verlag', 'liebes Team vom Loewe Verlag', 'https://loewe-verlag.de', 'info@loewe-verlag.de', '', '', 'Antwort abwarten', '2026-10-18', NULL, NULL, '2026-10-18 09:03:29', '2026-10-18 09:05:24', 'Mail', 0),
(274, 258, 'Tessloff Verlag', 'liebes Team von Tessloff Verlag', 'https://tessloff.com', 'info@tessloff.com', '', '', 'Antwort abwarten', '2026-10-18', NULL, NULL, '2026-10-18 09:04:21', '2026-10-18 09:06:00', 'Mail', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `template_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `betreff` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mailtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_name`, `betreff`, `mailtext`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Weihnachtstombola 2025', 'Weihnachtstombola des ev. Kindergarten', 'wir, der Förderverein des evangelischen Kindergartens Duisburg-Rumeln e.V., möchten Sie herzlich um Ihre Unterstützung für eine besondere Aktion bitten.\r\nFür unsere U3-Kinder möchten wir einen eigenen Spielbereich auf dem Außengelände mit einer kleinen Kletteranlage einrichten, die unseren Kleinsten vielfältige Möglichkeiten zum Klettern, Krabbeln, Entdecken und gemeinsamen Spielen bietet und damit das Außengelände nachhaltig bereichern soll. Zur Finanzierung der Spielanlage fehlt jedoch noch ein erheblicher Betrag.\r\n\r\nUm unserem Ziel näherzukommen, möchten wir auf dem Weihnachtsmarkt des Rumelner Turnvereins erneut einen Stand mit einer Tombola anbieten. In den vergangenen Jahren war diese Aktion die erfolgreichste Einnahmequelle des Vereins und wir hoffen auf einen ähnlichen Erfolg in diesem Jahr.\r\n\r\nFür die Umsetzung der Tombola sind wir allerdings auf Sachspenden oder auch finanzielle Unterstützung für die Zusammenstellung von Preisen und Trostpreisen angewiesen. Deshalb möchten wir Sie fragen, ob Sie uns in diesem Vorhaben ein wenig unterstützen. Gerne stellen wir Ihnen auf Wunsch auch eine Spendenquittung aus.\r\n\r\nFür Rückfragen stehen wir Ihnen jederzeit sehr gerne zur Verfügung. \r\n\r\nHerzlichen Dank im Voraus für Ihre Hilfe und Ihr Engagement!\r\n\r\nMit freundlichen Grüßen\r\nMartina Echtenbruck\r\n(2. Vorsitzende)', 1, '2025-10-07 09:53:20', '2026-09-07 07:45:24');

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
(9, 3, 'f057c4effaba8bdb0a0fee0e8113089061a6544c7aac03e515e4ccbec35ad545', '2026-10-15 13:08:54', 0, '2026-10-15 10:08:54');

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
(14, 'ProjectManagement', 'Zugriff auf Projektmanagement, Projektvorlagen, Aufgaben und Kommentare', '2026-10-16 09:06:39');

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
(1, 2, 'Jahreshauptversammlung Oktober 2026', '', '#FEF2CB', 'event', '2026-10-21', NULL, NULL, '2026/2027', 'active', 3, '2026-10-16 10:09:59', '2026-10-17 20:22:35', NULL),
(2, 1, 'Sponsorenlauf 2026', '', '#F7C9FC', 'event', '2026-10-17', NULL, NULL, '2026/2027', 'active', 3, '2026-10-17 06:31:44', '2026-10-17 21:13:21', NULL),
(3, 1, 'Sponsorenlauf 2027', '', '#D1EBFA', 'event', '2027-05-22', NULL, NULL, '2026/2027', 'active', 3, '2026-10-17 21:16:46', '2026-10-17 21:23:22', NULL);

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
(2, 'Vollversammlung', 'Vorbereitung der Jahreshauptversammlung', '#DCEAF0', 'event', 1, 3, '2026-10-16 09:50:02', '2026-10-16 10:08:40');

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
(21, 2, NULL, 'Engagement-Nachweise erstellen', 'Künftig kann auch der Förderverein ev. Kindergarten Duisburg Rumeln-Kaldenhausen e.V. den Engagementnachweis NRW an seine ehrenamtlich Engagierten ausstellen. \r\n\r\nhttps://www.engagiert-in-nrw.de/engagementnachweis-hinweise-und-formulare        \r\n\r\nBenutzername: Engagementnachweis\r\n\r\nPasswort: TiqL8E4V', 'https://www.overleaf.com/3567354428nfcghztbqsbr#a8c09e', -81, -2, 1, 18, 1, '2026-10-18 19:34:34', '2026-10-18 19:34:34');

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
(1, 'admin', 'Vollzugriff auf alle Funktionen', '2025-10-18 12:07:14'),
(2, 'manager', 'Kann Kontakte verwalten und Berichte einsehen', '2025-10-18 12:07:14'),
(3, 'user', 'Kann Kontakte einsehen, Status ändern, Kommentare bearbeiten und neue Kontakte vorschlagen', '2025-10-18 12:07:14');

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
(1, 1, '2026-10-16 09:12:59'),
(1, 2, '2026-10-16 09:12:59'),
(1, 3, '2026-10-16 09:12:59'),
(1, 4, '2026-10-16 09:12:59'),
(1, 5, '2026-10-16 09:12:59'),
(1, 6, '2026-10-16 09:12:59'),
(1, 7, '2026-10-16 09:12:59'),
(1, 8, '2026-10-16 09:12:59'),
(1, 9, '2026-10-16 09:12:59'),
(1, 10, '2026-10-16 09:12:59'),
(1, 11, '2026-10-16 09:12:59'),
(1, 12, '2026-10-16 09:12:59'),
(1, 13, '2026-10-16 09:12:59'),
(1, 14, '2026-10-16 09:12:59'),
(2, 1, '2026-10-16 09:15:01'),
(2, 2, '2026-10-16 09:15:01'),
(2, 3, '2026-10-16 09:15:01'),
(2, 4, '2026-10-16 09:15:01'),
(2, 5, '2026-10-16 09:15:01'),
(2, 6, '2026-10-16 09:15:01'),
(2, 8, '2026-10-16 09:15:01'),
(2, 9, '2026-10-16 09:15:01'),
(2, 10, '2026-10-16 09:15:01'),
(2, 11, '2026-10-16 09:15:01'),
(2, 12, '2026-10-16 09:15:01'),
(2, 14, '2026-10-16 09:15:01'),
(3, 1, '2026-10-15 09:37:58'),
(3, 10, '2026-10-15 09:37:58'),
(3, 11, '2026-10-15 09:37:58'),
(3, 12, '2026-10-15 09:37:58');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standalone_tasks`
--

CREATE TABLE `standalone_tasks` (
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
-- Daten für Tabelle `standalone_tasks`
--

INSERT INTO `standalone_tasks` (`id`, `title`, `description`, `resource_url`, `start_date`, `due_date`, `status`, `assigned_user_id`, `claimed_at`, `recurrence_every`, `recurrence_unit`, `recurrence_source_id`, `created_by`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 'Tannengrün kaufen', 'Für Dekoration zur Adventszeit. Menge mit Frau Baas abstimmen.', NULL, '2026-11-15', '2026-12-01', 'open', NULL, NULL, 1, 'year', NULL, 3, '2026-10-18 19:26:17', '2026-10-18 19:26:17', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `project_id` int NOT NULL,
  `task_template_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `resource_url` varchar(2048) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('open','in_progress','done') NOT NULL DEFAULT 'open',
  `assigned_user_id` int DEFAULT NULL COMMENT 'Freiwillige Übernahme; keine feste Zuweisung aus der Vorlage',
  `claimed_at` datetime DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `task_template_id`, `title`, `description`, `resource_url`, `start_date`, `due_date`, `status`, `assigned_user_id`, `claimed_at`, `sort_order`, `created_by`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 1, NULL, 'Termin festlegen', 'Termin Abstimmen und verfügbarkeit des Raums klären', NULL, '2026-08-26', NULL, 'done', NULL, NULL, -1, 3, '2026-10-16 10:09:59', '2026-10-16 10:42:03', '2026-10-16 12:42:03'),
(2, 1, NULL, 'Einladung erstellen und herausgeben', '- per Mail an Mitglieder\r\n- per Eltern App an alle Eltern\r\n- per Aushang im Kindergarten', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', NULL, NULL, 'done', NULL, NULL, 0, 3, '2026-10-16 10:09:59', '2026-10-16 10:42:06', '2026-10-16 12:42:06'),
(3, 1, NULL, 'Kassenschluss für das letzte Jahr', 'Optimalerweise zum wechsel des Geschäfttsjahres, aber spätestens zur Planung der Vollversammlung.', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', '2026-08-26', '2026-10-07', 'done', NULL, NULL, 1, 3, '2026-10-16 10:09:59', '2026-10-16 10:29:55', '2026-10-16 12:29:55'),
(4, 1, NULL, 'Kassenprüfung durch die Kassenprüfer', 'Die freiwilligen Kassenprüfer einberufen und die Kassenprüfung durchführen lassen', NULL, '2026-09-09', '2026-10-12', 'done', NULL, NULL, 2, 3, '2026-10-16 10:09:59', '2026-10-16 10:30:04', '2026-10-16 12:30:04'),
(5, 1, NULL, 'Kassenbericht erstellen', 'Der Jahresbericht wird automatisch aus der Buchführung generiert. Aktuell noch per R Skript, dass muss ich noch allgemeiner verfügbar machen.', 'https://www.overleaf.com/project/66c701b0a359dbe56f3b005f/share#ab696c2c03acfc0f3813c184c6348a90d1676d368fdd87f3', '2026-10-07', '2026-10-19', 'done', NULL, NULL, 3, 3, '2026-10-16 10:09:59', '2026-10-16 10:32:26', '2026-10-16 12:32:26'),
(6, 1, NULL, 'Protokoll vorbereiten', 'Die meisten Inhale stehen vorab, es kann also vorab der Orientierung dienen und das Protokollieren erleichtern.', 'https://www.overleaf.com/7927764558prnkpncsyqkz#a886e6', '2026-10-14', '2026-10-20', 'done', NULL, NULL, 4, 3, '2026-10-16 10:09:59', '2026-10-16 10:31:46', '2026-10-16 12:31:46'),
(7, 1, NULL, 'Protokoll fertigstellen', '', 'https://www.overleaf.com/project/66c9c42faceb8ff5d055d312/share#0651de0149d67b13bfc0862d344d41267ab89e0cff35ca45', '2026-10-21', '2026-10-28', 'in_progress', 3, '2026-10-18 21:36:06', 7, 3, '2026-10-16 10:09:59', '2026-10-18 19:36:06', NULL),
(8, 1, NULL, 'Dokumente Unterzeichnen und abheften', 'Kassenberichtt, Protokoll, Berichtt der Kassenprüfer und Teilnehmerliste', NULL, '2026-10-21', '2026-11-04', 'open', NULL, NULL, 8, 3, '2026-10-16 10:09:59', '2026-10-16 10:09:59', NULL),
(9, 2, 1, 'Plakate gestalten und drucken', 'Plakate vorbereiten und den Druck organisieren.', NULL, '2026-08-22', '2026-10-03', 'done', NULL, NULL, 10, 3, '2026-10-17 06:31:44', '2026-10-17 18:59:08', '2026-10-17 20:59:08'),
(10, 2, 2, 'Sponsorenlauf bei der Stadt anmelden', 'Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.', NULL, '2026-09-19', '2026-10-03', 'done', NULL, NULL, 20, 3, '2026-10-17 06:31:44', '2026-10-17 20:20:02', '2026-10-17 22:20:02'),
(11, 2, 3, 'Tombola anmelden', 'Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.', NULL, '2026-09-19', '2026-10-03', 'done', NULL, NULL, 30, 3, '2026-10-17 06:31:44', '2026-10-17 18:59:12', '2026-10-17 20:59:12'),
(12, 2, 4, 'Medaillen organisieren', 'Medaillenbedarf klären und rechtzeitig bestellen.', NULL, '2026-09-05', '2026-10-10', 'done', NULL, NULL, 40, 3, '2026-10-17 06:31:44', '2026-10-17 18:59:14', '2026-10-17 20:59:14'),
(13, 2, 5, 'Urkunden vorbereiten', 'Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.', NULL, '2026-09-26', '2026-10-14', 'done', NULL, NULL, 50, 3, '2026-10-17 06:31:44', '2026-10-17 18:59:16', '2026-10-17 20:59:16'),
(14, 2, 6, 'Teilnehmerliste vorbereiten', 'Anmeldung und Teilnehmerliste zusammenführen und prüfen.', NULL, '2026-10-03', '2026-10-14', 'done', NULL, NULL, 60, 3, '2026-10-17 06:31:44', '2026-10-17 18:59:18', '2026-10-17 20:59:18'),
(15, 2, 7, 'Kaffee und Verpflegung organisieren', 'Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.', NULL, '2026-10-03', '2026-10-15', 'done', NULL, NULL, 70, 3, '2026-10-17 06:31:44', '2026-10-17 18:59:19', '2026-10-17 20:59:19'),
(16, 2, NULL, 'Runden und Sponsorenangaben in Tabelle übernehmen', 'Die Berechnung, welcher Sponsore wieviel zahlen muss, muss erstellt werden', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', '2026-10-17', '2026-10-24', 'in_progress', 3, '2026-10-18 21:36:02', 500, 3, '2026-10-17 20:21:01', '2026-10-18 19:36:02', NULL),
(17, 2, NULL, 'Abrechnungsmails generieren und versenden', 'Aktuell noch ein lokales R Skript', NULL, '2026-10-21', '2026-10-28', 'open', NULL, NULL, 500, 3, '2026-10-17 20:23:31', '2026-10-17 20:23:31', NULL),
(18, 2, NULL, 'Wunschliste kaufen', 'Die Wunschliste ist au der Seite des Sponsorenlaufs hinterlegt', 'https://sponsorenlauf.evkigarumeln.de/', '2026-11-14', '2026-11-28', 'open', NULL, NULL, 500, 3, '2026-10-17 21:15:58', '2026-10-17 21:15:58', NULL),
(19, 3, 1, 'Plakate gestalten und drucken', 'Plakate vorbereiten und den Druck organisieren.', NULL, '2027-03-27', '2027-05-08', 'open', NULL, NULL, 10, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(20, 3, 2, 'Sponsorenlauf bei der Stadt anmelden', 'Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.', NULL, '2027-04-24', '2027-05-08', 'open', NULL, NULL, 20, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(21, 3, 3, 'Tombola anmelden', 'Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.', NULL, '2027-04-24', '2027-05-08', 'open', NULL, NULL, 30, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(22, 3, 4, 'Medaillen organisieren', 'Medaillenbedarf klären und rechtzeitig bestellen.', NULL, '2027-04-10', '2027-05-15', 'open', NULL, NULL, 40, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(23, 3, 5, 'Urkunden vorbereiten', 'Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.', NULL, '2027-05-01', '2027-05-19', 'open', NULL, NULL, 50, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(24, 3, 6, 'Teilnehmerliste vorbereiten', 'Anmeldung und Teilnehmerliste zusammenführen und prüfen.', NULL, '2027-05-08', '2027-05-19', 'open', NULL, NULL, 60, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(25, 3, 7, 'Kaffee und Verpflegung organisieren', 'Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.', NULL, '2027-05-08', '2027-05-20', 'open', NULL, NULL, 70, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(26, 3, NULL, 'Runden und Sponsorenangaben in Tabelle übernehmen', 'Die Berechnung, welcher Sponsore wieviel zahlen muss, muss erstellt werden', 'https://docs.google.com/spreadsheets/d/1Gor5TLxz1aaWGbha1tiTfmLPf6WnHmXZxbkD-ymwKao/edit?usp=sharing', '2027-05-22', '2027-05-29', 'open', NULL, NULL, 80, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(27, 3, NULL, 'Abrechnungsmails generieren und versenden', 'Aktuell noch ein lokales R Skript', NULL, '2027-05-26', '2027-06-02', 'open', NULL, NULL, 90, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(28, 3, NULL, 'Wunschliste kaufen', 'Die Wunschliste ist au der Seite des Sponsorenlaufs hinterlegt', 'https://sponsorenlauf.evkigarumeln.de/', '2027-06-19', '2027-07-03', 'open', NULL, NULL, 100, 3, '2026-10-17 21:16:46', '2026-10-17 21:16:46', NULL),
(29, 2, NULL, 'Kassen zählen und Buchführung aktualisieren', '', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', '2026-10-17', '2026-10-20', 'done', NULL, NULL, 500, 3, '2026-10-17 21:41:39', '2026-10-18 19:35:59', '2026-10-18 21:35:59'),
(30, 3, NULL, 'Kassen zählen und Buchführung aktualisieren', '', 'https://cryptpad.fr/sheet/#/2/sheet/edit/LanPF1PFeULA3Fu0DcdFyot2/', '2027-05-22', '2027-05-26', 'open', NULL, NULL, 500, 3, '2026-10-17 21:49:06', '2026-10-17 21:49:06', NULL),
(31, 1, NULL, 'Engagement-Nachweise erstellen', 'Künftig kann auch der Förderverein ev. Kindergarten Duisburg Rumeln-Kaldenhausen e.V. den Engagementnachweis NRW an seine ehrenamtlich Engagierten ausstellen. \r\n\r\nhttps://www.engagiert-in-nrw.de/engagementnachweis-hinweise-und-formulare        \r\n\r\nBenutzername: Engagementnachweis\r\n\r\nPasswort: TiqL8E4V', 'https://www.overleaf.com/3567354428nfcghztbqsbr#a8c09e', '2026-08-01', '2026-10-19', 'in_progress', 3, '2026-10-18 21:35:34', 500, 3, '2026-10-18 19:34:34', '2026-10-18 19:35:34', NULL);

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
(1, 'admin', '$2y$10$nXdqECj/NUqAsfJ6ava2ZeGntGELX0FdIItzXD1r76lmvT92d6UMK', 'admin@example.com', 'Administrator', 'Standard-Admin-Benutzer - Bitte Passwort ändern!', 0, '2025-10-18 12:07:14', '2025-10-21 07:00:13', '2026-09-07 12:18:04'),
(3, 'martina', '$2y$10$jEX7DxO0rQs2yUXo5nCiFepthyKNoMouY2PcCEPHj4EVQ.jKKA0vu', 'martinafriese@gmail.com', 'Martina Echtenbruck', '.', 1, '2025-10-19 09:26:59', '2026-10-18 19:25:03', '2026-10-18 19:25:03'),
(8, 'Tanja', '$2y$10$vtl6b5klcLlaBJMvCOwDUeT3M5Y044x.hWFLS/1QKlNXJsb8f00Ke', 'tanja_205@web.de', 'Tanja Wende', '', 1, '2025-10-24 11:54:52', '2026-10-17 20:26:12', '2026-10-17 20:26:12'),
(11, 'Julia Perret', '$2y$10$u.7fiQGIlogJ5X3baMTwROAmPKW8A.XrblezSt0sYFBXIpA4t57Sm', 'perret_julia@gmx.de', 'Julia Perret', '', 1, '2025-11-28 15:48:08', '2026-09-22 18:45:52', '2026-09-22 18:45:52'),
(12, 'FamKruppa', '$2y$10$0sWvfBiucv04GyxRJHN49e9esc0J7ONiDMjmXwNWdJkQOsBUO43/m', 'jannik.kruppa@gmx.de', 'Jannik Kruppa', 'Papa von Pauline aus Gruppe 3\r\nBzw. Familienaccount', 1, '2026-09-07 08:09:22', '2026-09-09 13:54:55', '2026-09-09 13:54:55'),
(13, 'Miriam Klaßen', '$2y$10$t4MtpVUjQBg4Ov9ct5kyWuql5vfBp3xEn8svMjSnP9usl10gYnzl.', 'miriam_klassen@t-online.de', 'Miriam Klaßen', '', 1, '2026-09-18 07:46:22', '2026-10-02 07:15:32', '2026-10-02 07:15:32'),
(14, 'Daniela', '$2y$10$GEBWBNl2U8Ez1dmA9KbsfecPx4fX/kbcWBJZ6Nu34r1zsF8ZM9cOG', 'danielalebenicnik@web.de', 'Daniela Gröters', '', 1, '2026-09-26 07:04:59', '2026-10-18 08:45:11', '2026-10-18 08:45:11'),
(15, 'Möhren', '$2y$10$lS8seSnwMfHJvOO1wcgZROFnfxgCxHfzYGqFQ4fzfvMGzvUXyFKrO', 'f.moehren@gmx.de', 'Fabienne Möhren', '', 1, '2026-09-30 13:07:40', '2026-09-30 16:54:36', '2026-09-30 16:54:36');

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
(1, 1, '2025-10-18 12:07:14'),
(3, 1, '2025-10-21 06:58:30'),
(8, 1, '2025-10-24 11:57:06'),
(11, 3, '2026-10-16 11:40:03'),
(12, 3, '2026-09-07 08:09:22'),
(13, 3, '2026-10-16 11:40:00'),
(14, 2, '2026-09-30 14:37:01'),
(15, 3, '2026-09-30 13:07:40');

--
-- Indizes der exportierten Tabellen
--

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
-- Indizes für die Tabelle `standalone_tasks`
--
ALTER TABLE `standalone_tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_standalone_recurrence_source` (`recurrence_source_id`),
  ADD KEY `idx_standalone_status_dates` (`status`,`start_date`,`due_date`),
  ADD KEY `idx_standalone_assignee` (`assigned_user_id`),
  ADD KEY `fk_standalone_creator` (`created_by`);

--
-- Indizes für die Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tasks_project_status` (`project_id`,`status`),
  ADD KEY `idx_tasks_due_date` (`due_date`),
  ADD KEY `idx_tasks_assignee` (`assigned_user_id`),
  ADD KEY `fk_tasks_template` (`task_template_id`),
  ADD KEY `fk_tasks_creator` (`created_by`);

--
-- Indizes für die Tabelle `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_comments_task_date` (`task_id`,`created_at`),
  ADD KEY `fk_task_comments_user` (`user_id`);

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
-- Indizes für die Tabelle `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT für Tabelle `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `fundraising_campaigns`
--
ALTER TABLE `fundraising_campaigns`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `project_templates`
--
ALTER TABLE `project_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `project_template_task_items`
--
ALTER TABLE `project_template_task_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT für Tabelle `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `standalone_tasks`
--
ALTER TABLE `standalone_tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT für Tabelle `task_comments`
--
ALTER TABLE `task_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `task_templates`
--
ALTER TABLE `task_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `fundraising_campaigns`
--
ALTER TABLE `fundraising_campaigns`
  ADD CONSTRAINT `fk_fundraising_campaign_ended_by` FOREIGN KEY (`ended_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fundraising_campaign_started_by` FOREIGN KEY (`started_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints der Tabelle `standalone_tasks`
--
ALTER TABLE `standalone_tasks`
  ADD CONSTRAINT `fk_standalone_assignee` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_standalone_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_standalone_recurrence_source` FOREIGN KEY (`recurrence_source_id`) REFERENCES `standalone_tasks` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_assignee` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tasks_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
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
-- Constraints der Tabelle `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
