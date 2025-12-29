<?php
// backend/check_types.php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "--- Application Types ---\n";
    $stmt = $pdo->query("SELECT DISTINCT application_type, count(*) as c FROM license_applications GROUP BY application_type");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Type: '" . $row['application_type'] . "' (Count: " . $row['c'] . ")\n";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
