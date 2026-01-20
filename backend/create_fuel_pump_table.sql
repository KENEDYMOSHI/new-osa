-- Fuel Pump Pattern Approval Table
-- This table stores all fuel pump pattern approval applications

CREATE TABLE IF NOT EXISTS `fuel_pump_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `application_number` varchar(50) DEFAULT NULL,
  
  -- Step 1: Manufacturer Details
  `manufacturer_name` varchar(255) NOT NULL,
  `country_of_manufacture` varchar(100) NOT NULL,
  
  -- Step 2: Fuel Pump Identification
  `make` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `quantity_of_pumps` int(11) NOT NULL,
  `manufacturing_year` int(11) DEFAULT NULL,
  `number_of_nozzles` int(11) DEFAULT NULL,
  `dispenser_type` varchar(100) DEFAULT NULL,
  
  -- Step 3: Metrological Characteristics
  `measured_quantity` varchar(50) DEFAULT NULL,
  `fuel_type` varchar(100) DEFAULT NULL,
  `other_fuel_type` varchar(100) DEFAULT NULL,
  `min_flow_rate` decimal(10,2) DEFAULT NULL,
  `max_flow_rate` decimal(10,2) DEFAULT NULL,
  `min_measured_volume` decimal(10,2) DEFAULT NULL,
  `operating_temp_min` decimal(10,2) DEFAULT NULL,
  `operating_temp_max` decimal(10,2) DEFAULT NULL,
  
  -- Step 4: Accuracy & Performance
  `declared_accuracy_class` varchar(50) DEFAULT NULL,
  `max_permissible_error` varchar(50) DEFAULT NULL,
  
  -- Step 5: Indicating & Power System
  `volume_indicator_type` varchar(50) DEFAULT NULL,
  `price_display` varchar(50) DEFAULT NULL,
  `display_location` text DEFAULT NULL COMMENT 'JSON array',
  `power_supply` text DEFAULT NULL COMMENT 'JSON array',
  
  -- Step 6: Software Information (if applicable)
  `software_version` varchar(100) DEFAULT NULL,
  `software_legally_relevant` varchar(10) DEFAULT NULL,
  `software_protection_method` text DEFAULT NULL COMMENT 'JSON array',
  `event_log_available` varchar(10) DEFAULT NULL,
  
  -- Step 7: Sealing & Security
  `adjustment_points` text DEFAULT NULL,
  `seal_type` text DEFAULT NULL COMMENT 'JSON array',
  `seal_locations` text DEFAULT NULL,
  
  -- Step 8: Installation Information
  `intended_installation` text DEFAULT NULL COMMENT 'JSON array',
  `intended_country_of_use` varchar(100) DEFAULT NULL,
  `installation_manual_available` varchar(10) DEFAULT NULL,
  
  -- Step 9: Supporting Documents (file paths)
  `calibration_manual` varchar(255) DEFAULT NULL,
  `user_manual` varchar(255) DEFAULT NULL,
  `pump_exterior_photo` varchar(255) DEFAULT NULL,
  `nameplate_photo` varchar(255) DEFAULT NULL,
  `display_photo` varchar(255) DEFAULT NULL,
  `sealing_points_photo` varchar(255) DEFAULT NULL,
  `type_examination_cert` varchar(255) DEFAULT NULL,
  `software_documentation` varchar(255) DEFAULT NULL,
  
  -- Application Status & Tracking
  `status` enum('draft','submitted','under_review','approved','rejected','returned') DEFAULT 'draft',
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `approval_certificate_path` varchar(255) DEFAULT NULL,
  
  -- Timestamps
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `application_number` (`application_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
