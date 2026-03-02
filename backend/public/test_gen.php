<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require rtrim('/Users/keny/Desktop/Projects/new-osa/backend/app/Config/Paths.php', '\\/ ') . '';
require '/Users/keny/Desktop/Projects/new-osa/backend/vendor/autoload.php';
$app = require '/Users/keny/Desktop/Projects/new-osa/backend/system/bootstrap.php';

$generator = new \App\Libraries\LicenseGenerator();
$licenseData = (object)[
    'licenseType' => 'Class A',
    'licenseNumber' => 'WL-2026-0002',
    'createdAt' => '20 Feb 2026',
    'expiryDate' => '19 Feb 2027',
    'applicantName' => 'Test Applicanto',
    'company' => 'Test Company',
    'address' => 'Dar es Salaam',
    'licenseToken' => '123456789',
    'commissionerName' => 'Alban M. Kihulla'
];
try {
    $licenseUrl = $generator->generateLicense($licenseData);
    echo "Success: " . $licenseUrl;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
