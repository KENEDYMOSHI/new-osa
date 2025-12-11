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
    $routes->get('applications', 'ApprovalController::getApplications');
    $routes->get('application/(:segment)', 'ApprovalController::getApplicationDetails/$1');
    $routes->post('update-status', 'ApprovalController::updateApplicationStatus');
});

service('auth')->routes($routes);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('auth/me', 'AuthController::me');
    $routes->post('auth/update-personal', 'AuthController::updatePersonalProfile');
    $routes->post('auth/update-business', 'AuthController::updateBusinessProfile');
    $routes->post('auth/change-password', 'AuthController::changePassword');
    $routes->post('license/submit', 'LicenseController::submit');
    $routes->post('license/upload', 'LicenseController::upload');
    $routes->get('license/documents', 'LicenseController::getUserDocuments');
    $routes->get('license/document/(:segment)/view', 'LicenseController::view/$1');
    $routes->delete('license/document/(:segment)', 'LicenseController::deleteDocument/$1');
    $routes->get('license/bill/(:segment)', 'LicenseController::getBill/$1');
    $routes->get('license/user-bills', 'LicenseController::getUserBills');
    $routes->get('license/user-applications', 'LicenseController::getUserApplications');
    $routes->get('admin/applications', 'AdminController::getApplications');
    $routes->get('admin/applicants', 'AdminController::getApplicants');
    $routes->get('admin/application/(:segment)', 'AdminController::getApplicationDetails/$1');
    $routes->get('admin/document/(:segment)/view', 'AdminController::viewDocument/$1');
    $routes->post('admin/application/(:segment)/approve', 'AdminController::approveApplication/$1');
    $routes->post('admin/document/return', 'AdminController::returnDocument');

    // Notifications
    $routes->get('notifications', 'NotificationController::getUserNotifications');
    $routes->post('notifications/(:segment)/read', 'NotificationController::markAsRead/$1');

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

    $routes->options('(:any)', static function () {
        return response()->setStatusCode(200);
    });
});
