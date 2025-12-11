<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('metrology', ['namespace' => 'App\Modules\MetrologicalSupervision\Controllers'], function ($routes) {

    $routes->group('settings', function ($routes) {
        $routes->get('/', 'SettingsController::index');

        $routes->post('products/save', 'SettingsController::saveProduct');
        $routes->post('products/delete', 'SettingsController::deleteProduct');

        $routes->post('ports/save', 'SettingsController::savePort');
        $routes->post('ports/delete', 'SettingsController::deletePort');

        $routes->post('berths/save', 'SettingsController::saveBerth');
        $routes->post('berths/delete', 'SettingsController::deleteBerth');

        $routes->post('terminals/save', 'SettingsController::saveTerminal');
        $routes->post('terminals/delete', 'SettingsController::deleteTerminal');

        $routes->post('documents/save', 'SettingsController::saveDocument');
        $routes->post('documents/delete', 'SettingsController::deleteDocument');
        $routes->post('documents/delete', 'SettingsController::deleteDocument');
        $routes->post('documents/delete', 'SettingsController::deleteDocument');
    });

    $routes->get('voyages-details/(:segment)', 'VoyagesController::voyageDetails/$1');

    $routes->group('vessels', function ($routes) {
        $routes->get('/', 'VesselsController::index');
        $routes->post('save', 'VesselsController::saveVessel');
        $routes->post('delete', 'VesselsController::deleteVessel');

        $routes->group('tanks', function ($routes) {
            $routes->post('get', 'VesselsController::getTanks');
            $routes->post('save', 'VesselsController::saveTanks');
            $routes->post('update', 'VesselsController::updateTank');
            $routes->post('delete', 'VesselsController::deleteTank');
        });
    });

    $routes->group('voyages', function ($routes) {
        $routes->get('/', 'VoyagesController::index');
        $routes->get('details/(:num)', 'VoyagesController::getVesselDetails/$1');
        $routes->get('list/(:num)', 'VoyagesController::getVoyages/$1');
        $routes->get('view/(:segment)', 'VoyagesController::view/$1'); // Details Page
        $routes->get('edit/(:segment)', 'VoyagesController::edit/$1'); // Added Edit Route
        $routes->post('save', 'VoyagesController::save');
        $routes->post('delete/(:segment)', 'VoyagesController::delete/$1');



        $routes->group('products', function ($routes) {
            // Time Logs Routes
            $routes->post('time-logs/save', 'TimeLogsController::save');
            $routes->get('time-logs/list/(:segment)', 'TimeLogsController::getList/$1');
            $routes->get('time-logs/get/(:segment)', 'TimeLogsController::get/$1');
            $routes->post('time-logs/delete/(:segment)', 'TimeLogsController::delete/$1');

            // Pressure Logs Routes
            $routes->post('pressure-logs/save', 'PressureLogsController::save');
            $routes->get('pressure-logs/list/(:segment)', 'PressureLogsController::getList/$1');
            $routes->get('pressure-logs/get/(:segment)', 'PressureLogsController::get/$1');
            $routes->post('pressure-logs/delete/(:segment)', 'PressureLogsController::delete/$1');
            $routes->get('list/(:segment)', 'VoyageProductsController::getProducts/$1');
            $routes->get('list_all', 'VoyageProductsController::getList');
            $routes->get('get/(:segment)', 'VoyageProductsController::get/$1'); // Added Get Route
            $routes->post('save', 'VoyageProductsController::save');
            $routes->post('delete/(:segment)', 'VoyageProductsController::delete/$1');
        });
    });
});
