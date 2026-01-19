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
    
    echo "--- Pattern Types ---\n";
    $stmt = $pdo->query("SELECT id, name FROM pattern_types");
    while ($row = $stmt->fetch()) {
        echo "{$row['id']}: {$row['name']}\n";
    }

    echo "\n--- Instrument Categories ---\n";
    $stmt = $pdo->query("SELECT id, name, pattern_type_id FROM instrument_categories");
    while ($row = $stmt->fetch()) {
        echo "{$row['id']}: {$row['name']} (PatternID: " . ($row['pattern_type_id'] ?? 'NULL') . ")\n";
    }

} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
