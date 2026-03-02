<?php
// Bootstrap CI4
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require rtrim('/Users/keny/Desktop/Projects/new-osa/backend/app/Config/Paths.php', '\\/ ') . '';
require '/Users/keny/Desktop/Projects/new-osa/backend/vendor/autoload.php';
$app = require '/Users/keny/Desktop/Projects/new-osa/backend/system/bootstrap.php';

$db = \Config\Database::connect();
$apps = $db->table('license_applications')
    ->select('license_applications.id, license_applications.user_id, licenses.license_number, license_users.id as lu_id')
    ->join('licenses', 'licenses.application_id = license_applications.id', 'left')
    ->join('license_users', 'license_users.id = license_applications.user_id', 'left')
    ->whereIn('license_applications.status', ['Approved_CEO', 'License_Generated', 'Approved'])
    ->get()->getResult();
print_r($apps);
