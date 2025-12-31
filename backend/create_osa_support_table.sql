CREATE TABLE IF NOT EXISTS `osa_support_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` text DEFAULT NULL,
  `phone_label_1` varchar(50) DEFAULT NULL,
  `phone_number_1` varchar(50) DEFAULT NULL,
  `phone_label_2` varchar(50) DEFAULT NULL,
  `phone_number_2` varchar(50) DEFAULT NULL,
  `phone_label_3` varchar(50) DEFAULT NULL,
  `phone_number_3` varchar(50) DEFAULT NULL,
  `email_general` varchar(100) DEFAULT NULL,
  `email_tech` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initialize with default data
INSERT INTO `osa_support_details` (`id`, `address`, `phone_label_1`, `phone_number_1`, `email_general`, `email_tech`, `website`)
SELECT 1, 
'Wakala wa Vipimo (WMA)
Vipimo House, Chief Chemist Street
S.L.P. 2014, Dodoma – Tanzania',
'Office',
'+255 (26) 22610700',
'info@wma.go.tz',
'ictsupport@wma.go.tz',
'www.wma.go.tz'
WHERE NOT EXISTS (SELECT 1 FROM `osa_support_details` WHERE `id` = 1);

