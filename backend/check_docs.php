<?php
// Quick script to check documents in database
require __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

echo "=== ALL DOCUMENTS IN DATABASE ===\n\n";

$query = $db->query("
    SELECT 
        la.id as app_id,
        la.status as app_status,
        att.id,
        att.document_type,
        att.category,
        att.status,
        att.created_at
    FROM license_application_attachments att
    LEFT JOIN license_applications la ON la.id = att.application_id
    ORDER BY att.application_id, att.category, att.created_at
");

$results = $query->getResult();

echo "Total documents: " . count($results) . "\n\n";

$byCategory = ['attachment' => 0, 'qualification' => 0, 'null' => 0];
$byAppId = [];

foreach ($results as $row) {
    $cat = $row->category ?? 'null';
    $byCategory[$cat] = ($byCategory[$cat] ?? 0) + 1;
    
    $appId = $row->app_id ?? 'unknown';
    if (!isset($byAppId[$appId])) {
        $byAppId[$appId] = ['attachment' => 0, 'qualification' => 0];
    }
    if ($cat === 'attachment' || $cat === 'qualification') {
        $byAppId[$appId][$cat]++;
    }
    
    printf("App: %s | Doc: %-50s | Cat: %-15s | Status: %s\n", 
        $row->app_id, 
        substr($row->document_type, 0, 50),
        $cat,
        $row->status ?? 'null'
    );
}

echo "\n=== SUMMARY ===\n";
echo "By Category:\n";
foreach ($byCategory as $cat => $count) {
    echo "  $cat: $count\n";
}

echo "\nBy Application:\n";
foreach ($byAppId as $appId => $cats) {
    echo "  App $appId: {$cats['attachment']} attachments, {$cats['qualification']} qualifications\n";
}
