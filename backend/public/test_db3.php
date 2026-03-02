<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require rtrim('/Users/keny/Desktop/Projects/new-osa/backend/app/Config/Paths.php', '\\/ ') . '';
require '/Users/keny/Desktop/Projects/new-osa/backend/vendor/autoload.php';
$app = require '/Users/keny/Desktop/Projects/new-osa/backend/system/bootstrap.php';

$db = \Config\Database::connect();
$licenses = $db->table('licenses')->select('application_id, license_number, issue_date')->get()->getResult();
print_r($licenses);
