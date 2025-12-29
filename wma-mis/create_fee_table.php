<?php
// Script to create application_type_fees table
// Run this file by accessing it in browser: http://localhost:8081/create_fee_table.php

require_once __DIR__ . '/app/Config/Database.php';

$config = new \Config\Database();
$db = \Config\Database::connect();

$sql = "CREATE TABLE IF NOT EXISTS `application_type_fees` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_type` varchar(100) NOT NULL COMMENT 'New License or Renew License',
  `nationality` varchar(50) NOT NULL COMMENT 'Citizen or Non-Citizen',
  `amount` decimal(10,2) NOT NULL COMMENT 'Fee amount in TZS',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    $db->query($sql);
    echo "<h2 style='color: green;'>✓ Success!</h2>";
    echo "<p>Table 'application_type_fees' has been created successfully.</p>";
    echo "<p><a href='/licenseSetting'>Go to License Setting</a></p>";
} catch (\Exception $e) {
    echo "<h2 style='color: red;'>✗ Error!</h2>";
    echo "<p>Failed to create table: " . $e->getMessage() . "</p>";
}
