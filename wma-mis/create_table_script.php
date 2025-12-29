<?php
// Simple script to create the application_type_fees table
// Run this file directly: php create_table_script.php

$configs = [
    ['host' => '127.0.0.1', 'user' => 'maalim', 'pass' => 'maalim', 'db' => 'vessel_discharge', 'port' => 8889, 'socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'],
    ['host' => '127.0.0.1', 'user' => 'maalim', 'pass' => 'maalim', 'db' => 'vipimo_v2', 'port' => 8889, 'socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'db' => 'vessel_discharge', 'port' => 3306, 'socket' => null],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'db' => 'vipimo_v2', 'port' => 3306, 'socket' => null],
];

$connected = false;
$mysqli = null;
$usedDb = '';

echo "Trying to connect to database...\n\n";

foreach ($configs as $config) {
    try {
        echo "Trying {$config['db']} on port {$config['port']}... ";
        
        if ($config['socket']) {
            $mysqli = @new mysqli($config['host'], $config['user'], $config['pass'], $config['db'], $config['port'], $config['socket']);
        } else {
            $mysqli = @new mysqli($config['host'], $config['user'], $config['pass'], $config['db'], $config['port']);
        }
        
        if ($mysqli->connect_error) {
            echo "FAILED (" . $mysqli->connect_error . ")\n";
            continue;
        }
        
        echo "SUCCESS!\n";
        $connected = true;
        $usedDb = $config['db'];
        break;
        
    } catch (Exception $e) {
        echo "FAILED (" . $e->getMessage() . ")\n";
    }
}

if (!$connected) {
    die("\n❌ Could not connect to any database. Please check your MySQL server is running.\n");
}

echo "\n✓ Connected to database: $usedDb\n\n";

// Check if table exists
$result = $mysqli->query("SHOW TABLES LIKE 'application_type_fees'");

if ($result->num_rows > 0) {
    echo "✓ Table 'application_type_fees' already exists!\n";
} else {
    echo "Creating table 'application_type_fees'...\n";
    
    $sql = "CREATE TABLE `application_type_fees` (
        `id` INT UNSIGNED AUTO_INCREMENT,
        `application_type` VARCHAR(100) NOT NULL,
        `nationality` VARCHAR(50) NOT NULL,
        `amount` DECIMAL(15,2) DEFAULT '0.00',
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($mysqli->query($sql)) {
        echo "✓ SUCCESS! Table 'application_type_fees' created successfully!\n";
    } else {
        echo "❌ ERROR: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
echo "\nDone!\n";
?>
