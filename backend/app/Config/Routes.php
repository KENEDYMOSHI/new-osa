<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'ApprovalController::index');
$routes->get('approval/login', 'ApprovalController::login');
$routes->post('approval/login', 'ApprovalController::processLogin');
$routes->get('approval/logout', 'ApprovalController::logout');
$routes->get('viewApplication/(:segment)', 'ApprovalController::viewApplication/$1');
$routes->group('api/approval', function($routes) {
    // Dashboard Stats API
    $routes->group('dashboard', function($routes) {
        $routes->post('vtv', 'Api\DashboardController::vtv');
        $routes->post('sbl', 'Api\DashboardController::sbl');
        $routes->post('water-meters', 'Api\DashboardController::waterMeters');
        $routes->post('ppg', 'Api\DashboardController::ppg');
        $routes->post('others', 'Api\DashboardController::others');
        $routes->get('osa-stats', 'Api\DashboardController::getOsaStats');
    });

    // Approval Module API
    $routes->get('applications', 'ApprovalController::getApplications'); 
    $routes->get('application/(:segment)', 'ApprovalController::getApplicationDetails/$1');
    $routes->get('license-types', 'ApprovalController::getLicenseTypes');
    $routes->get('issued-licenses', 'ApprovalController::getIssuedLicenses');
    $routes->post('update-status', 'ApprovalController::updateApplicationStatus');
    $routes->post('update-exam-scores', 'ApprovalController::updateExamScores');
    
    // Document Operations
    $routes->post('document/(:segment)/accept', 'Api\\DocumentApiController::acceptDocument/$1');
    
    // Payment API
    $routes->group('payment', function($routes) {
        $routes->post('collection', 'Api\PaymentController::getPaymentCollection');
        $routes->post('report-data', 'Api\PaymentController::getReportData');
    });
});

service('auth')->routes($routes);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/check-phone', 'AuthController::checkPhone');
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('auth/me', 'AuthController::me');
    $routes->post('auth/update-personal', 'AuthController::updatePersonalProfile');
    $routes->post('auth/update-business', 'AuthController::updateBusinessProfile');
    $routes->post('auth/change-password', 'AuthController::changePassword');
    $routes->post('auth/forgot-password', 'AuthController::forgotPassword');
    $routes->post('auth/verify-otp', 'AuthController::verifyResetOtp');
    $routes->post('auth/reset-password', 'AuthController::resetPassword');
    $routes->post('license/submit', 'LicenseController::submit');
    $routes->post('license/upload', 'LicenseController::upload');
    $routes->get('license/documents', 'LicenseController::getUserDocuments');
    $routes->get('license/document/(:segment)/view', 'LicenseController::view/$1');
    $routes->post('document/(:segment)/submit', 'LicenseController::submitDocument/$1');
    $routes->delete('license/document/(:segment)', 'LicenseController::deleteDocument/$1');
    $routes->get('license/bill/(:segment)', 'LicenseController::getBill/$1');
    $routes->get('license/user-bills', 'LicenseController::getUserBills');
    $routes->get('license/user-applications', 'LicenseController::getUserApplications');
    $routes->get('license/eligibility', 'LicenseController::checkEligibility');
    $routes->get('license/eligible-applications', 'LicenseController::getEligibleApplications');
    $routes->get('license/application/(:segment)/documents', 'LicenseController::getApplicationDocuments/$1');
    $routes->get('license/types', 'LicenseController::getLicenseTypes');
    $routes->get('license/approved-licenses', 'LicenseController::getApprovedLicenses');
    $routes->get('license/view-image/(:segment)', 'LicenseController::viewLicenseImage/$1');
    $routes->get('admin/applications', 'AdminController::getApplications');
    $routes->get('admin/applicants', 'AdminController::getApplicants');
    $routes->get('admin/application/(:segment)', 'AdminController::getApplicationDetails/$1');
    $routes->get('admin/document/(:segment)/view', 'AdminController::viewDocument/$1');
    $routes->post('admin/application/(:segment)/approve', 'AdminController::approveApplication/$1');
    $routes->post('admin/document/return', 'AdminController::returnDocument');
    $routes->post('admin/document/accept', 'AdminController::acceptDocument');

    // Temporary Backfill Route
    $routes->get('admin/backfill-license-numbers', 'AdminController::backfillLicenseNumbers');


    // Notifications
    $routes->get('notifications', 'NotificationController::getUserNotifications');
    $routes->post('notifications/(:segment)/read', 'NotificationController::markAsRead/$1');

    // Application Review
    $routes->get('approved-applications', 'ApplicationReviewController::getApprovedApplications');
    $routes->get('application-details/(:segment)', 'ApplicationReviewController::getApplicationDetails/$1');
    $routes->get('available-license-types', 'ApplicationReviewController::getAvailableLicenseTypes');

    // Locations (Region, District, Ward, Postal Code)
    $routes->get('locations/regions', 'LocationController::getRegions');
    $routes->get('locations/districts/(:segment)', 'LocationController::getDistricts/$1');
    $routes->get('locations/wards/(:segment)', 'LocationController::getWards/$1');
    $routes->get('locations/postalcodes/(:segment)', 'LocationController::getPostalCodes/$1');

    $routes->options('auth/register', static function () {
        return response()->setStatusCode(200);
    });
    $routes->options('auth/login', static function () {
        return response()->setStatusCode(200);
    });
    // Module 1: Initial Applications
    // Document Management
    $routes->get('initial-applications/(:segment)/documents', 'InitialApplicationController::getDocuments/$1');
    $routes->post('initial-applications/(:segment)/documents', 'InitialApplicationController::uploadDocument/$1');
    $routes->delete('initial-applications/(:segment)/documents/(:segment)', 'InitialApplicationController::deleteDocument/$1/$2');
    $routes->post('initial-applications/(:segment)/documents/(:segment)/submit', 'InitialApplicationController::submitDocument/$1/$2');
    $routes->get('initial-applications/(:segment)/documents/(:segment)/view', 'InitialApplicationController::viewDocument/$1/$2');
    
    // License Type Management
    $routes->put('initial-applications/(:segment)/license-types', 'InitialApplicationController::updateLicenseTypes/$1');
    
    $routes->resource('initial-applications', ['controller' => 'InitialApplicationController']);
    $routes->post('initial-applications/(:segment)/approve', 'InitialApplicationController::approve/$1');

    // Module 2: License Applications
    $routes->resource('license-applications', ['controller' => 'LicenseApplicationController']);
    $routes->post('license-applications/(:segment)/submit', 'LicenseApplicationController::submit/$1');
    $routes->post('license-applications/(:segment)/approve', 'LicenseApplicationController::approve/$1');

    // License Fee and Payment Management
    $routes->post('license/generate-fee/(:segment)', 'LicenseController::generateLicenseFee/$1');
    $routes->get('license/payment-status/(:segment)', 'LicenseController::checkPaymentStatus/$1');
    $routes->get('license/view/(:segment)', 'LicenseController::viewLicense/$1');
    $routes->get('license/details/(:segment)', 'LicenseController::getApplicationDetails/$1');
    $routes->get('support-details', 'OsaSupportController::getDetails');

    $routes->options('(:any)', static function () {
        return response()->setStatusCode(200);
    });
});
