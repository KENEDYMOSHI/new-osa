<?php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $userId = 3;
    echo "--- System Check ---\n";
    
    // Check Tables
    $tStmt = $pdo->query("SHOW TABLES");
    $tables = $tStmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables) . "\n";
    
    $count = $pdo->query("SELECT count(*) FROM application_reviews")->fetchColumn();
    echo "Total Reviews in DB: $count\n";
    
    // Dump Reviews
    $allReviews = $pdo->query("SELECT * FROM application_reviews")->fetchAll(PDO::FETCH_ASSOC);
    print_r($allReviews);
    
    echo "--- Discovery for User $userId ---\n";
    $stmt = $pdo->prepare("SELECT id, status, application_type FROM license_applications WHERE user_id = ?");
    $stmt->execute([$userId]);
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($apps) . " applications.\n";
    
    foreach ($apps as $app) {
        echo "\n[App ID: " . $app['id'] . "] Status: " . $app['status'] . ", Type: " . $app['application_type'] . "\n";
        
        // 2. Get Reviews
        $rStmt = $pdo->prepare("SELECT stage, status, created_at FROM application_reviews WHERE application_id = ?");
        $rStmt->execute([$app['id']]);
        $reviews = $rStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($reviews)) {
            echo "  No Reviews Found.\n";
        } else {
            foreach ($reviews as $rev) {
                echo "  Review: Stage='" . $rev['stage'] . "', Status='" . $rev['status'] . "'\n";
            }
        }
        
        // 3. Get Exams
        $eStmt = $pdo->prepare("SELECT result FROM interview_assessments WHERE application_id = ?");
        $eStmt->execute([$app['id']]);
        $exams = $eStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($exams)) {
            echo "  No Interview/Exam.\n";
        } else {
             foreach ($exams as $ex) {
                echo "  Exam Result: '" . $ex['result'] . "'\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
