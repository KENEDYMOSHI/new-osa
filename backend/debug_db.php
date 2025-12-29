<?php
// backend/debug_db.php
// Force environment to development to see errors
define('ENVIRONMENT', 'development');

// Define the path to the framework
$minPath = __DIR__ . '/public/index.php';
$appPath = __DIR__ . '/app';

// Load CodeIgniter framework bootstrap
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

use Config\Database;

try {
    $db = Database::connect();
    
    echo "--- Database Connection Successful ---\n";
    
    // 1. Check Distinct Statuses
    echo "\n1. Distinct Statuses in license_applications:\n";
    $query = $db->query("SELECT DISTINCT status, COUNT(*) as count FROM license_applications GROUP BY status");
    foreach ($query->getResultArray() as $row) {
        echo "   Status: '" . $row['status'] . "' (Count: " . $row['count'] . ")\n";
    }
    
    // 2. Sample 'Approved_Surveillance' Applications
    echo "\n2. Sample 'Approved_Surveillance' Applications (Limit 5):\n";
    $query = $db->query("SELECT id, user_id, status FROM license_applications WHERE status LIKE '%Surveillance%' LIMIT 5");
    $results = $query->getResultArray();
    if (empty($results)) {
        echo "   NO records found matching LIKE '%Surveillance%'\n";
    } else {
        foreach ($results as $row) {
            echo "   ID: " . $row['id'] . ", UserID: " . $row['user_id'] . ", Status: '" . $row['status'] . "'\n";
            
            // Sub-check items
            $itemQ = $db->query("SELECT * FROM license_application_items WHERE application_id = ?", [$row['id']]);
            $items = $itemQ->getResultArray();
            echo "      Items found: " . count($items) . "\n";
            foreach($items as $item) {
                 echo "      - Item: " . $item['license_type'] . "\n";
            }
        }
    }

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
