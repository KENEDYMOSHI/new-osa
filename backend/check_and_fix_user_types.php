<?php
// backend/check_and_fix_user_types.php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = ''; // Default XAMPP/MAMP password often empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "--- User Type Check ---\n";
    $stmt = $pdo->query("SELECT user_type, count(*) as count FROM users GROUP BY user_type");
    $results = $stmt->fetchAll();
    
    foreach ($results as $row) {
        $type = $row['user_type'] === null ? 'NULL' : $row['user_type'];
        echo "Type: '$type' -> Count: {$row['count']}\n";
    }
    
    // Check for NULLs or Empties
    $nullStmt = $pdo->query("SELECT count(*) as count FROM users WHERE user_type IS NULL OR user_type = ''");
    $nullCount = $nullStmt->fetch()['count'];
    
    echo "\nFound $nullCount users with NULL or empty user_type.\n";
    
    if ($nullCount > 0) {
        echo "Fixing users... Setting default to 'practitioner' (unless they look like pattern approval).\n";
        
        // Update logic: Default to practitioner
        $updateStmt = $pdo->prepare("UPDATE users SET user_type = 'practitioner' WHERE user_type IS NULL OR user_type = ''");
        $updateStmt->execute();
        
        echo "Updated {$updateStmt->rowCount()} users to 'practitioner'.\n";
    }
    
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
