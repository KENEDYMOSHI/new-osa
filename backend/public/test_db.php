<?php
// Bootstrap CI4
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require rtrim('/Users/keny/Desktop/Projects/new-osa/backend/app/Config/Paths.php', '\\/ ') . '';
require '/Users/keny/Desktop/Projects/new-osa/backend/vendor/autoload.php';
$app = require '/Users/keny/Desktop/Projects/new-osa/backend/system/bootstrap.php';

$db = \Config\Database::connect();
$apps = $db->table('license_applications')
    ->select('license_applications.id as app_id, license_applications.status, licenses.license_number')
    ->join('licenses', 'licenses.application_id = license_applications.id', 'left')
    ->orderBy('license_applications.created_at', 'DESC')
    ->limit(10)
    ->get()->getResult();
print_r($apps);
