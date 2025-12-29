<?php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // User 3's Application ID from previous discovery
    $appId = '730f0134-f4e1-4c2d-a0cf-421680dc58be';
    
    echo "--- Approving App $appId for User 3 ---\n";
    
    // Cleanup partial run
    $pdo->prepare("DELETE FROM application_reviews WHERE application_id = ?")->execute([$appId]);
    $pdo->prepare("DELETE FROM interview_assessments WHERE application_id = ?")->execute([$appId]);

    // 1. Manager Review
    $mId = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    $pdo->prepare("INSERT INTO application_reviews (id, application_id, application_type, stage, status, comments, created_at) VALUES (?, ?, 'License', 'Manager', 'Approved', 'Auto Approved', NOW())")
        ->execute([$mId, $appId]);
    echo "Inserted Manager Approval.\n";

    // 2. Surveillance Review
    $sId = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    $pdo->prepare("INSERT INTO application_reviews (id, application_id, application_type, stage, status, comments, created_at) VALUES (?, ?, 'License', 'Surveillance', 'Approved', 'Auto Approved', NOW())")
        ->execute([$sId, $appId]);
    echo "Inserted Surveillance Approval.\n";
    
    // 3. Exam Result (Correct columns)
    $eId = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    // Using columns found in DESCRIBE: theory_score, practical_score, total_score, result
    $pdo->prepare("INSERT INTO interview_assessments (id, application_id, theory_score, practical_score, total_score, result, created_at) VALUES (?, ?, 80, 80, 80, 'Pass', NOW())")
        ->execute([$eId, $appId]);
    echo "Inserted Exam Pass.\n";

    // 4. Update Status
    $pdo->prepare("UPDATE license_applications SET status = 'Approved_Surveillance' WHERE id = ?")
        ->execute([$appId]);
    echo "Updated Status to Approved_Surveillance.\n";
    
    echo "Done. Please refresh the page.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
