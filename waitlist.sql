-- Adminer 5.4.1 MySQL 8.0.36-28 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `enflow_settings`;
CREATE TABLE `enflow_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `enflow_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1,	'launch_date',	'2026-07-01 00:00:00');

DROP TABLE IF EXISTS `waitlist`;
CREATE TABLE `waitlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) DEFAULT NULL,
  `business_name` varchar(150) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `challenge` text,
  `volume` varchar(50) DEFAULT NULL,
  `ready_to_adopt` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `waitlist` (`id`, `full_name`, `business_name`, `business_type`, `city`, `phone`, `email`, `challenge`, `volume`, `ready_to_adopt`, `created_at`) VALUES
(27,	'theblackSams',	'Ccjitters',	'Café',	'Lagos',	'+2347089913116',	'black@ccjitters.com',	'Failed orders',	'100–500',	'yes',	'2026-05-20 08:10:58'),
(28,	'Blessing Stephen',	'SABB',	'Other',	'Lagos',	'+2348183660801',	'akpoebs@gmail.com',	'Management',	'0–100',	'yes',	'2026-05-20 11:45:28'),
(29,	'Victoria Okon',	'Arina’s Spot',	'Restaurant',	'Port Harcourt',	'+23408075604936',	'vickynaldo12345@gmail.com',	'Managing orders',	'100–500',	'yes',	'2026-05-20 19:00:05'),
(30,	'Great Kosisochukwu Obiekwe',	'KXSI STUDIOS',	'Restaurant',	'Abuja',	'+23408139987474',	'kozzyworkspace@gmail.com',	'Staff Coordination.',	'0–100',	'yes',	'2026-05-21 21:51:38'),
(31,	'Test User',	'Test Business',	'Restaurant',	'Lagos',	'+2348000000000',	'wsamson630@gmail.com',	'Managing rush hours',	'100-500',	'yes',	'2026-05-30 02:07:36'),
(32,	'_blackS',	'Ccjitters',	'Café',	'Lagos',	'+2347089913116',	'Tristincassey@gmail.com',	'Roi',	'100–500',	'yes',	'2026-05-30 07:45:09'),
(33,	'Test User',	'Test Business',	'Restaurant',	'Lagos',	'+2348000000000',	'wsamson630@gmail.com',	'Managing rush hours',	'100-500',	'yes',	'2026-05-30 07:58:39'),
(34,	'Samson',	'Bs',	'Lounge',	'Lagos',	'+2348096831043',	'Wsamson630@gmail.com',	'Mmne',	'100–500',	'yes',	'2026-05-30 08:24:22'),
(35,	'Skah lagos',	'Skah lagos',	'Lounge',	'Lagos',	'+2347089913116',	'Wsamson630@gmail.com',	'Yyu',	'500+',	'yes',	'2026-05-30 08:30:42'),
(36,	'Ejim Juliet',	'Ej Collections',	'Other',	'Other',	'+2347048112292',	'ejimjuliet003@gmail.com',	'',	'0–100',	'yes',	'2026-05-30 08:49:54'),
(37,	'Saka Bashir',	'KAY CHOPS',	'Restaurant',	'Lagos',	'+2347048566270',	'Bashirola2008@gmail.com',	'Managing orders',	'0–100',	'yes',	'2026-05-30 18:05:05'),
(38,	'Victoria Okon',	'Arina’s Spot',	'Restaurant',	'Port Harcourt',	'+2348037640426',	'vickynaldo12345@gmail.com',	'Managing orders.',	'500+',	'yes',	'2026-05-30 18:15:59'),
(39,	'Blessing Stephen',	'SABB',	'Other',	'Lagos',	'+2348183660801',	'akpoebs@gmail.com',	'',	'0–100',	'yes',	'2026-05-30 18:31:00'),
(40,	'Iwu Success',	'Gift blinks',	'Other',	'Other',	'+2347010616549',	'successonyekachi08@gmail.com',	'Managing orders',	'100–500',	'yes',	'2026-05-30 18:46:12'),
(41,	'_black',	'ccjitters',	'Café',	'Lagos',	'+2347089913116',	'powells@ccjitters.com',	'manual and human errors',	'0–100',	'yes',	'2026-05-31 22:10:20'),
(42,	'Ss',	'Hhj',	'Restaurant',	'Lagos',	'+2349031645823',	'Tristincassey@gmail.com',	'Jjk',	'500+',	'yes',	'2026-06-06 13:33:46'),
(43,	'Powells',	'Royal fork',	'Restaurant',	'Lagos',	'+2347089913116',	'Wsamson630@gmail.com',	'Jjt',	'100–500',	'yes',	'2026-06-06 13:52:20'),
(44,	'Powells',	'Ccjitters',	'Café',	'Abuja',	'+2347089913116',	'Tristincassey@gmail.com',	'Jjty',	'0–100',	'yes',	'2026-06-06 13:53:56'),
(45,	'PowellsSams',	'Ccjitters',	'Restaurant',	'Lagos',	'+2348096831043',	'andre.dacoster@gmail.com',	'Jhh',	'100–500',	'yes',	'2026-06-06 13:56:59');

-- 2026-06-21 09:52:34 UTC
