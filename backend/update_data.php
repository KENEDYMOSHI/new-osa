<?php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Update IDs 1-4 to match
    for ($i = 1; $i <= 4; $i++) {
        $stmt = $pdo->prepare("UPDATE instrument_categories SET pattern_type_id = ? WHERE id = ?");
        $stmt->execute([$i, $i]);
        echo "Updated Category ID $i to Pattern Type ID $i\n";
    }

} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
