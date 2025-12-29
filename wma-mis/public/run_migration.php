<?php
echo "<h1>Database Migration Tool</h1>";

mysqli_report(MYSQLI_REPORT_STRICT);

$username = 'maalim';
$password = 'maalim';
$database = 'vipimo_v2';

// Try standard MAMP/Local configurations
$configs = [
    ['host' => '127.0.0.1', 'port' => 8889, 'socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'],
    ['host' => '127.0.0.1', 'port' => 8889, 'socket' => null],
    ['host' => 'localhost', 'port' => 8889, 'socket' => null],
    ['host' => '127.0.0.1', 'port' => 3306, 'socket' => null],
    ['host' => 'localhost', 'port' => 3306, 'socket' => null],
];

$connected = false;
$mysqli = null;

echo "<ul>";
foreach ($configs as $conf) {
    try {
        $socketStr = $conf['socket'] ? " (Socket: {$conf['socket']})" : "";
        echo "<li>Trying {$conf['host']}:{$conf['port']}{$socketStr}... ";
        
        if ($conf['socket']) {
            $mysqli = new mysqli($conf['host'], $username, $password, $database, $conf['port'], $conf['socket']);
        } else {
            $mysqli = new mysqli($conf['host'], $username, $password, $database, $conf['port']);
        }
        
        echo "<strong style='color:green'>SUCCESS!</strong></li>";
        $connected = true;
        break;
    } catch (Exception $e) {
        echo "<span style='color:red'>FAILED</span> ({$e->getMessage()})</li>";
    }
}
echo "</ul>";

if (!$connected) {
    die("<h2 style='color:red'>Could not connect to database. Please check your credentials and server status.</h2>");
}

try {
    $query = "CREATE TABLE IF NOT EXISTS `application_type_fees` (
        `id` INT UNSIGNED AUTO_INCREMENT,
        `application_type` VARCHAR(100) NOT NULL,
        `nationality` VARCHAR(50) NOT NULL,
        `amount` DECIMAL(15,2) DEFAULT '0.00',
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $mysqli->query($query);
    echo "<h2 style='color:green'>Table 'application_type_fees' created successfully!</h2>";
    echo "<p>You can now go back to the <a href='/licenseSetting'>License Setting Page</a>.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Query Failed: " . $e->getMessage() . "</h2>";
}
?>
