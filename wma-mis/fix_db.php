<?php
mysqli_report(MYSQLI_REPORT_STRICT);

$username = 'maalim';
$password = 'maalim';
$database = 'vipimo_v2';

$configs = [
    ['host' => '127.0.0.1', 'port' => 8889, 'socket' => null],
    ['host' => '127.0.0.1', 'port' => 3306, 'socket' => null],
    ['host' => 'localhost', 'port' => 3306, 'socket' => '/tmp/mysql.sock'],
    ['host' => 'localhost', 'port' => 3306, 'socket' => '/var/mysql/mysql.sock'],
];

$connected = false;
$mysqli = null;

foreach ($configs as $conf) {
    try {
        echo "Trying {$conf['host']}:{$conf['port']} " . ($conf['socket'] ? "({$conf['socket']})" : "") . "... ";
        if ($conf['socket']) {
            $mysqli = new mysqli($conf['host'], $username, $password, $database, $conf['port'], $conf['socket']);
        } else {
            $mysqli = new mysqli($conf['host'], $username, $password, $database, $conf['port']);
        }
        echo "SUCCESS!\n";
        $connected = true;
        break;
    } catch (Exception $e) {
        echo "FAILED. " . $e->getMessage() . "\n";
    }
}

if (!$connected) {
    die("Could not connect to any database configuration.\n");
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
    echo "Table 'application_type_fees' created or already exists.\n";
    
} catch (Exception $e) {
    echo "Query Failed: " . $e->getMessage() . "\n";
}
?>
