<?php
$host = '127.0.0.1';
$db   = 'osa_app';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "--- Populating license_completions with Approved Applications ---\n";
    
    // Find all approved applications
    $sql = "
        SELECT DISTINCT
            la.id as application_id,
            la.user_id,
            lai.license_type
        FROM license_applications la
        JOIN license_application_items lai ON lai.application_id = la.id
        JOIN application_reviews mr ON mr.application_id = la.id AND mr.stage = 'Manager' AND mr.status = 'Approved'
        JOIN application_reviews sr ON sr.application_id = la.id AND sr.stage = 'Surveillance' AND sr.status = 'Approved'
        WHERE la.status = 'Approved_Surveillance'
    ";
    
    $stmt = $pdo->query($sql);
    $approved = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($approved) . " approved applications.\n\n";
    
    foreach ($approved as $app) {
        // Check if already exists
        $checkStmt = $pdo->prepare("SELECT id FROM license_completions WHERE application_id = ?");
        $checkStmt->execute([$app['application_id']]);
        
        if ($checkStmt->fetch()) {
            echo "Skipping {$app['license_type']} (already exists)\n";
            continue;
        }
        
        // Generate UUID
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        
        // Insert with empty JSON arrays for now
        $insertStmt = $pdo->prepare("
            INSERT INTO license_completions 
            (id, application_id, user_id, license_type, previous_licenses, qualifications, experiences, tools, declaration, created_at) 
            VALUES (?, ?, ?, ?, '[]', '[]', '[]', '[]', 0, NOW())
        ");
        
        $insertStmt->execute([
            $uuid,
            $app['application_id'],
            $app['user_id'],
            trim($app['license_type'])
        ]);
        
        echo "✓ Inserted: {$app['license_type']} (User {$app['user_id']})\n";
    }
    
    echo "\nDone!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
