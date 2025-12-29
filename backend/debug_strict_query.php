<?php
// backend/debug_strict_query.php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Get User ID 3's applications (from logs)
    $userId = 3; 

    echo "--- Debugging Strict Query for User ID $userId ---\n";
    
    $sql = "
        SELECT 
            la.id, 
            la.status, 
            la.application_type, 
            lai.license_type as name,
            mr.status as manager_status,
            sr.status as surveillance_status,
            ia.result as exam_result
        FROM license_applications la
        JOIN license_application_items lai ON lai.application_id = la.id
        JOIN application_reviews mr ON mr.application_id = la.id AND mr.stage = 'Manager' AND mr.status = 'Approved'
        JOIN application_reviews sr ON sr.application_id = la.id AND sr.stage = 'Surveillance' AND sr.status = 'Approved'
        LEFT JOIN interview_assessments ia ON ia.application_id = la.id
        WHERE la.user_id = ?
    ";
    
    echo "Executing Base Join Query (Manager + Surveillance)...\n";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($results) . " base matches.\n";
    foreach ($results as $r) {
        echo "ID: " . $r['id'] . "\n";
        echo "Type: " . $r['application_type'] . "\n";
        echo "Manager: " . $r['manager_status'] . "\n";
        echo "Surveillance: " . $r['surveillance_status'] . "\n";
        echo "Exam Result: " . ($r['exam_result'] ?? 'NULL') . "\n";
        
        // Check Logic
        $isRenewal = ($r['application_type'] === 'Renewal');
        $passed = ($r['exam_result'] === 'Pass');
        
        if ($isRenewal) {
            echo " -> Eligible (Renewal)\n";
        } elseif ($passed) {
             echo " -> Eligible (New + Passed)\n";
        } else {
             echo " -> NOT Eligible (New but Exam not Passed)\n";
        }
        echo "----------------\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
