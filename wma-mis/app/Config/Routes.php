<?php

use CodeIgniter\Router\RouteCollection;



/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index');

// Test route for product dropdown (remove in production)
$routes->get('test/voyages', 'TestProductController::voyages');
$routes->get('test/tables', 'TableCheck::index');

// $routes = Services::routes();

// Sync API
$routes->get('sync-data/(:any)', 'SyncController::syncApplicantData/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
// service('auth')->routes($routes, ['login', 'register']);
// $routes->get('login', '\App\Controllers\Auth\LoginController::loginView');
// $routes->get('register', '\App\Controllers\Auth\RegisterController::registerView');

// Authentication Middleware

$routes->match(['GET', 'POST'], 'user/activateAccount/(:any)/(:any)/(:any)', 'UserAccountController::activateAccount/$1/$2/$3');
$routes->match(['GET', 'POST'], 'user/passwordChange/(:any)/(:any)/(:any)', 'UserAccountController::passwordChange/$1/$2/$3');

$routes->post('loginAction', 'Login::loginAction');
$routes->get('checkSession', 'Miscellaneous::checkSession');
$routes->get('fill', 'Home::regions');
$routes->get('billTest', 'BillController::billTest');

// $appRoutes['control_number'] = 'BillController::controlNumber';
// $appRoutes['bill_payment'] = 'BillController::billPayment';
// $appRoutes['bill_reconciliation'] = 'BillController::billReconciliation';
//  https://training.wma.go.tz/bill_request
//  https://training.wma.go.tz/bill_payment
//  https://training.wma.go.tz/bill_reconciliation

$routes->match(['GET', 'POST'], 'control_number', 'GepgBillController::controlNumber');
$routes->match(['GET', 'POST'], 'bill_request', 'GepgBillController::controlNumber');
$routes->match(['GET', 'POST'], 'bill_payment', 'GepgBillController::billPayment');
$routes->match(['GET', 'POST'], 'bill_reconciliation', 'GepgBillController::reconciliation');

// ================ROUTES FOR RECEIVING DATA FROM GEPG FOR TR ====================  
$routes->match(['GET', 'POST'], 'controlNumberHandler', 'TreasuryGepgBillController::controlNumber');
$routes->match(['GET', 'POST'], 'paymentHandler', 'TreasuryGepgBillController::billPayment');
$routes->match(['GET', 'POST'], 'reconHandler', 'TreasuryGepgBillController::reconciliation');


// callback for control number - https://mis.wma.go.tz/controlNumberHandler 
// callback for payment https://mis.wma.go.tz/paymentHandler
// callback for recon https://mis.wma.go.tz/reconHandler



//Dream$4045



$routes->match(['POST'], 'billPaymentSimulation', 'Home::billPaymentSimulation');
$routes->post('service/serviceBillRequest', 'ServiceBillProcessorApi::serviceBillRequest', ['namespace' => 'App\Controllers\Api']);
$routes->post('service/verifyPayment', 'ServiceBillProcessorApi::verifyPayment', ['namespace' => 'App\Controllers\Api']);
$routes->post('service/osaActivation', 'ServiceBillProcessorApi::osaActivation', ['namespace' => 'App\Controllers\Api']);
$routes->post('service/osaPasswordReset', 'ServiceBillProcessorApi::osaPasswordReset', ['namespace' => 'App\Controllers\Api']);

$routes->get('verification/verifySticker/(:any)', 'VerificationController::verifySticker/$1', ['filter' => 'RateLimiter']);
$routes->get('verifyCalibrationChart/(:any)/(:any)/(:any)', 'VerificationController::verifyCalibrationChart/$1/$2/$3', ['filter' => 'RateLimiter']);

//$routes->get('bill', 'Sky::index');
// $routes->get('shop', 'Shop::index');
// $routes->get('data', 'Shop::data');
// $routes->post('addItem', 'Shop::addItem');
// $routes->get('pdf', 'Yap::pdf');
// $routes->get('getVisitors', 'Home::getData');
// $routes->get('groupBy', 'Yap::groupBy');
// $routes->get('searchBy', 'Yap::searchBy');
// $routes->get('bill', 'Yap::bill');
// $routes->get('curl', 'XmlController::curl');
// $routes->get('xml', 'XmlController::xml');
// $routes->get('xmlPost', 'XmlController::xmlPost');
// $routes->get('fileContent', 'XmlController::fileContent');
// $routes->get('billDate', 'XmlController::billDate');
// $routes->get('arrayXml', 'XmlController::arrayXml');
// $routes->get('qrCode', 'XmlController::qrCode');
// $routes->get('createBackup', 'BackupController::createBackup');
// $routes->get('cheti', 'ConformityCertificateController::generateCertificate');

//
$routes->get('verificationAlert', 'NotificationController::verificationAlert');
$routes->get('debtAlert', 'NotificationController::debtAlert');


$routes->get('cars', 'DataDevController::cars');
$routes->post('addCar', 'DataDevController::addCar');


$routes->get('sms', 'DataTestController::sms');
$routes->get('test', 'DataDevController::updateWmaBillDates');
$routes->get('updateCn', 'DataDevController::updateCn');
$routes->get('updateItemsCn', 'DataDevController::updateItemsCn');
$routes->get('verifyCertificate/(:any)/(:any)', 'VerifyController::index/$1/$2');


$routes->post('cnQueue', 'DataDevController::controlNumber');
$routes->get('setBillControlNumbers', 'DataDevController::setBillControlNumbers');
$routes->get('fixBillItems', 'DataDevController::fixBillItems');


$routes->get('verifyTin', 'DataDevController::verifyTin');




// $routes->get('image', 'DataTestController::image');
// $routes->get('updateVtv', 'DataTestController::updateVtv');
// $routes->get('updateSbl', 'DataTestController::updateSbl');

// $routes->post('validateForm', 'DataTestController::validateForm');
// $routes->get('reconTest', 'DataTestController::reconTest');
// $routes->get('import', 'DataTestController::csv');
// $routes->post('csvData', 'DataTestController::csvData');
// $routes->get('generateQRCode', 'DataTestController::generateQRCode');
// $routes->get('map', 'DataTestController::map');
// $routes->post('geoLocation', 'DataTestController::geoLocation');
// $routes->match(['GET', 'POST'], 'sms', 'DataTestController::sms');
// $routes->match(['GET', 'POST'], 'print', 'DataTestController::sticker');
// $routes->match(['GET', 'POST'], 'img', 'StickerController::img');
// $routes->match(['GET', 'POST'], 'printing', 'StickerController::printing');

//=================API ROUTES====================

$routes->post('api/login', 'ApiAuthController::login', ['namespace' => 'App\Controllers\Api']);
$routes->get('approval/login', 'ApprovalController::login');

$routes->get('api/noAuth', 'ApiAuthController::noAuth', ['namespace' => 'App\Controllers\Api']);
$routes->post('api/meters', 'MetersApiController::meters', ['namespace' => 'App\Controllers\Api']);

// ================GOV ESB==============
$routes->post('apidata/lorries', 'MetersApiController::lorries', ['namespace' => 'App\Controllers\Api']);
$routes->post('apidata/fuelStations', 'MetersApiController::fuelStations', ['namespace' => 'App\Controllers\Api']);
$routes->get('apidata/processFuelStationData', 'MetersApiController::processFuelStationData', ['namespace' => 'App\Controllers\Api']);
$routes->get('apidata/meters', 'MetersApiController::functionSendWaterMeters', ['namespace' => 'App\Controllers\Api']);

$routes->get('smsJob', 'MetersApiController::smsJob', ['namespace' => 'App\Controllers\Api']);

$routes->post('apiData/verifiedWaterMeters', 'MetersApiController::verifiedMetersRequest', ['namespace' => 'App\Controllers\Api']);


$routes->get('getMeters', 'MetersApiController::getMeters', ['namespace' => 'App\Controllers\Api']);
$routes->get('invite', 'MetersApiController::invite', ['namespace' => 'App\Controllers\Api']);
$routes->get('review', 'MetersApiController::review', ['namespace' => 'App\Controllers\Api']);


$routes->get('getCertificates', 'CertController::getCertificates');
$routes->get('certs', 'CertController::index');

$routes->post('requestCertificateOfQuantity', 'CoqController::requestCertificateOfQuantity');
$routes->post('consolidatedReport', 'PetroleumImportController::consolidatedReport');
$routes->post('sailingReports', 'SailingReportController::sailingReports');


// $routes->post('profile', 'Profile::index');
// $routes->post('demo', 'Demo::index');
// $routes->get('user', 'Demo::user');



$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'ApiAuthFilter'], function ($routes) {
    $routes->get('units', 'BillApiController::units');
    $routes->get('profile', 'ApiProfileController::index');
    $routes->post('billRequest', 'BillApiController::billSubmissionRequest');
    $routes->post('billTest', 'BillApiController::billTest');
    $routes->post('searchBill', 'BillApiController::searchBill');
    $routes->post('selectBill', 'BillApiController::selectBill');
    $routes->post('searchReceipt', 'BillApiController::searchPaymentReceipt');
    $routes->post('selectReceipt', 'BillApiController::selectPaymentReceipt');
    $routes->post('selectReceipt', 'BillApiController::selectPaymentReceipt');
    $routes->post('billCancellationRequest', 'BillApiController::billCancellationRequest');

    $routes->get('exit', 'ApiAuthController::exit');

    $routes->get('logout', 'ApiAuthController::logout');
    //poss
    $routes->post('generateBill', 'PosController::billSubmissionRequest');

    $routes->post('billRenewRequest', 'BillApiController::billRenewRequest');


    //=================search====================
    $routes->post('searchInstrument', 'SearchApiController::searchInstrument');
    $routes->post('selectInstrument', 'SearchApiController::selectInstrument');

    //=================Qr Code Verify====================

    $routes->post('verifyInstrument', 'QrCodeApiController::verifyInstrument');

    //revenue sources
    $routes->get('revenueSources', 'BillApiController::revenueSources');
});




$routes->get('updateMobile/(:any)', 'Login::updateMobile/$1');
$routes->match(['GET', 'POST'], 'updateMobile/(:any)', 'Login::updateMobile/$1');
$routes->match(['GET', 'POST'], 'destroySession', 'Profile::destroySession');
// $appRoutes['destroySession'] = 'Profile::destroySession';



// Include remaining module routes
include APPPATH . 'Modules/MetrologicalSupervision/Config/Routes.php';

$routes->group('', ['filter' => 'AuthFilter'], function ($routes, $appRoutes = []) {
    // $appRoutes['updateMobile/(:any)'] = 'Login::updateMobile/$1';
    //sendActivationLink
    $appRoutes['applicationAproval'] = 'OsaController::index';
    $appRoutes['initialApplicationApproval'] = 'OsaController::index';
    $appRoutes['api/applications'] = 'OsaController::getApplicationsApi';
    $appRoutes['api/application/(:any)'] = 'OsaController::getApplicationDetailsApi/$1';
    $appRoutes['viewApplication/(:any)'] = 'OsaController::viewApplication/$1';
    $appRoutes['viewCompletedApplication/(:any)'] = 'OsaController::viewCompletedApplication/$1';
    $appRoutes['viewCompleteApplication'] = 'OsaController::completedApplications';
    $appRoutes['applicationVerification'] = 'OsaController::applicationVerification';
    $appRoutes['osaDashboard'] = 'OsaController::osaDashboard';
    $appRoutes['examRemark'] = 'OsaController::examRemark';
    $appRoutes['saveExamRemark'] = 'OsaController::saveExamRemark';
    $appRoutes['licenseReport'] = 'OsaController::licenseReport';
    $appRoutes['osaApproveApplication'] = 'OsaController::approveApplication';
    $appRoutes['osaRejectApplication'] = 'OsaController::rejectApplication';
    $appRoutes['paymentSimulator'] = 'Home::index';
    $appRoutes['sticker'] = 'StickerController::index';
    $appRoutes['searchSticker'] = 'StickerController::searchSticker';
    $appRoutes['printSticker/(:any)'] = 'StickerController::printSticker/$1';


    // FREE LICENSE SETTING ROUTES
    $appRoutes['licenseSetting'] = 'LicenseSettingController::index';
    
    // Application Fees
    $appRoutes['licenseSetting/getFees'] = 'LicenseSettingController::getFees';
    $appRoutes['licenseSetting/addFee'] = 'LicenseSettingController::addFee';
    $appRoutes['licenseSetting/updateFee'] = 'LicenseSettingController::updateFee'; // For POST
    $appRoutes['licenseSetting/updateFee/(:num)'] = 'LicenseSettingController::updateFee/$1'; // For URL segment
    $appRoutes['licenseSetting/deleteFee/(:num)'] = 'LicenseSettingController::deleteFee/$1';
    
    // License Types
    $appRoutes['licenseSetting/getLicenseTypes'] = 'LicenseSettingController::getLicenseTypes';
    $appRoutes['licenseSetting/addLicenseType'] = 'LicenseSettingController::addLicenseType';
    $appRoutes['licenseSetting/updateLicenseType/(:any)'] = 'LicenseSettingController::updateLicenseType/$1';
    $appRoutes['licenseSetting/deleteLicenseType/(:any)'] = 'LicenseSettingController::deleteLicenseType/$1';
    
    // Support/Help Settings
    $appRoutes['licenseSetting/getSupportDetails'] = 'LicenseSettingController::getSupportDetails';
    $appRoutes['licenseSetting/saveSupportDetails'] = 'LicenseSettingController::saveSupportDetails';

    $appRoutes['run-migration'] = 'LicenseSettingController::runMigration';

    // Print Application
    $appRoutes['print-application/(:any)'] = 'PrintApplicationController::printApplication/$1';


    //certificates routes
    $appRoutes['certificate'] = 'ConformityCertificateController::index';
    $appRoutes['importSignature'] = 'ConformityCertificateController::generateCertificateNumber';
    //CONFORMITY CERTIFICATE
    $appRoutes['viewConformityCertificate'] = 'ConformityCertificateController::viewConformityCertificate';
    $appRoutes['searchConformityCertificate'] = 'ConformityCertificateController::searchConformityCertificate';
    $appRoutes['printConformityCertificate/(:any)'] = 'ConformityCertificateController::printConformityCertificate/$1';


    //CORRECTNESS CERTIFICATE
    $appRoutes['viewCorrectnessCertificate'] = 'CorrectnessCertificateController::viewCorrectnessCertificate';
    $appRoutes['searchCorrectnessCertificate'] = 'CorrectnessCertificateController::searchCorrectnessCertificate';
    $appRoutes['printCorrectnessCertificate/(:any)'] = 'CorrectnessCertificateController::printCorrectnessCertificate/$1';




    $appRoutes['sendActivationLink'] = 'UserAccountController::sendActivationLink';

    $routes->post('checkEmail', 'Admin::checkEmail');
    //=================customer====================
    $appRoutes['addCustomer'] = 'CustomerController::addCustomer';
    $appRoutes['selectClient'] = 'CustomerController::selectCustomer';
    $appRoutes['searchCustomer'] = 'CustomerController::searchCustomer';
    $appRoutes['dataChart'] = 'DashboardController::dataChart';
    $appRoutes['dataChart'] = 'DashboardController::dataChart';
    // $appRoutes['dataChartManager'] = 'Manager::analytics';

    //=================INSTRUMENTS REPORTS====================

    $appRoutes['stampedInstruments'] = 'InstrumentReportController::stamped';
    $appRoutes['instrumentsTarget'] = 'InstrumentReportController::instrumentsTarget';
    $appRoutes['addInstrumentTarget'] = 'InstrumentReportController::addInstrumentTarget';
    $appRoutes['filterInstruments'] = 'InstrumentReportController::filterInstruments';
    $appRoutes['downloadStampedInstruments/(:any)/(:any)/(:any)'] = 'InstrumentReportController::downloadStampedInstruments/$1/$2/$3';


    $appRoutes['varianceAnalysis'] = 'VarianceAnalysisController::index';
    $appRoutes['downloadEstimate/(:any)/(:any)'] = 'VarianceAnalysisController::downloadEstimate/$1/$2';
    $routes->match(['POST'], 'filterEstimates', 'VarianceAnalysisController::filterEstimates');


    // ================tr collection reports====================
    $appRoutes['trCollection'] = 'TrAnalysisController::index';
    $appRoutes['filterTrCollection'] = 'TrAnalysisController::filterTrCollection';
    $appRoutes['downloadTrContribution/(:any)/(:any)'] = 'TrAnalysisController::downloadTrContribution/$1/$2';


    // $appRoutes['downloadInstrumentEstimate/(:any)/(:any)'] = 'VarianceAnalysisController::downloadInstrumentEstimate/$1/$2';
    // $routes->match(['POST'], 'filterInstrumentEstimates', 'VarianceAnalysisController::filterInstrumentEstimates');




    $appRoutes['activitiesSummary/(:any)/(:any)'] = 'CollectionSummaryController::activitiesSummary/$1/$2';
    $appRoutes['downloadCentersSummary'] = 'CollectionSummaryController::downloadCentersSummary';

    $appRoutes['estimates'] = 'EstimatesController::index';

    $appRoutes['createEstimate'] = 'EstimatesController::createEstimate';
    $appRoutes['editEstimate'] = 'EstimatesController::editEstimate';
    $appRoutes['updateEstimate'] = 'EstimatesController::updateEstimate';

    //estimates for instruments
    $appRoutes['instrumentEstimates'] = 'InstrumentEstimatesController::index';
    $appRoutes['createInstrumentEstimate'] = 'InstrumentEstimatesController::createInstrumentEstimate';
    $appRoutes['editInstrumentEstimate'] = 'InstrumentEstimatesController::editInstrumentEstimate';
    $appRoutes['updateInstrumentEstimate'] = 'InstrumentEstimatesController::updateInstrumentEstimate';



    //allocation
    $appRoutes['allocateInstruments'] = 'EstimatesController::allocateInstruments';
    $appRoutes['activityEstimates'] = 'EstimatesController::activityEstimates';
    $appRoutes['createActivityEstimate'] = 'EstimatesController::createActivityEstimate';
    $appRoutes['editActivityEstimate'] = 'EstimatesController::editActivityEstimate';
    $appRoutes['updateActivityEstimate'] = 'EstimatesController::updateActivityEstimate';

    //=================INSPECTION REPORT====================
    $appRoutes['adjusted'] = 'AdjustmentReportController::adjusted';
    $appRoutes['filterAdjustedInstruments'] = 'AdjustmentReportController::filterAdjustedInstruments';
    $appRoutes['downloadAdjustedInstruments/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'AdjustmentReportController::downloadAdjustedInstruments/$1/$2/$3/$4/$5';
    $appRoutes['rejected'] = 'InspectionReportController::rejected';



    $appRoutes['condemned'] = 'InspectionReportController::condemned';
    $appRoutes['filterInspected'] = 'InspectionReportController::filterInspected';
    $appRoutes['downloadInspected/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'InspectionReportController::downloadInspected/$1/$2/$3/$4/$5/$6';

    $appRoutes['rejectionNote/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'RejectedReportController::rejectionNote/$1/$2/$3/$4/$5';

    //routes for condemned instruments report



    $appRoutes['downloadReportData/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'CollectionReports::downloadReportData/$1/$2/$3/$4/$5/$6/$7/$8/$9';

    //=================GEO LOCATION ROUTES====================
    $appRoutes['GeolocationReport'] = 'GeolocationController::index';
    $appRoutes['GeolocationReport2'] = 'GeolocationController::index2';
    $appRoutes['filterLocationData'] = 'GeolocationController::filterLocationData';


    // ================DashboardController==============
    $appRoutes['dashboard'] = 'DashboardController::index';
    $appRoutes['main'] = 'DashboardController::index2';
    $appRoutes['graphData'] = 'DashboardController::graphData';





    // ================User profile==============
    $appRoutes['profile'] = 'Profile::index';
    $appRoutes['changePassword'] = 'Profile::changePassword';
    $appRoutes['managerProfile'] = 'Profile::managerProfile';
    $appRoutes['directorProfile'] = 'Profile::directorProfile';
    $appRoutes['confirmTask/(:any)'] = 'Profile::confirmTask/$1';
    $appRoutes['home'] = 'Home::index';

    // $routes->add('activate/(:any)','Auth\Signup::activate/$1');
    // ================Authentication==============
    //$appRoutes['activate'] = 'Auth\Signup::activate';  

    // $appRoutes['login']  = 'Login::index';
    $appRoutes['newCustomer'] = 'PersonalDetails::newCustomer';
    $appRoutes['searchExistingCustomer'] = 'PersonalDetails::searchExistingCustomer';
    $appRoutes['selectCustomer'] = 'PersonalDetails::selectCustomer';
    $appRoutes['updateCustomer'] = 'PersonalDetails::updateCustomer';
    // =======================Scales===================================
    $appRoutes['newScale'] = 'Scales::newScale';
    $appRoutes['listScales'] = 'Scales::listRegisteredScales';
    $appRoutes['registerScale'] = 'Scales::registerScale';
    $appRoutes['getCustomerScales'] = 'Scales::getCustomerScales';
    $appRoutes['saveScaleData'] = 'Scales::saveScaleData';

    // =======================Fuel Pumps==========================
    $appRoutes['newPump'] = 'FuelPumps::newPump';
    $appRoutes['listFuelPumps'] = 'FuelPumps::listRegisteredFuelPumps';
    // ============================================================
    $appRoutes['dashboard'] = 'DashboardController::index';

    $appRoutes['signout'] = 'Profile::logout';

    // ======================Industrial Packages==========================================
    $appRoutes['newIndustrialPackage'] = 'IndustrialPackages::newIndustrialPackage';
    $appRoutes['listIndustrialPackages'] = 'IndustrialPackages::listIndustrialPackages';

    // ==================== Lories================================
    $appRoutes['addVehicleTank'] = 'VehicleTankCalibration::addVtc';
    $appRoutes['checkPlateNumber'] = 'VehicleTankCalibration::checkPlateNumber';
    $appRoutes['addLorry'] = 'Lorries::addLorry';
    $appRoutes['addWaterMeter'] = 'WaterMeter::addWaterMeter';
    $appRoutes['addExtraWaterMeters'] = 'WaterMeter::addExtraWaterMeters';
    $appRoutes['viewPrepackage'] = 'PrepackageController::listPrepackage';
    $appRoutes['imported'] = 'ImportedController::index';
    $appRoutes['downloadImportedReport/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'ImportedController::downloadImportedReport/$1/$2/$3/$4/$5';
    $appRoutes['filterImportedReport'] = 'ImportedController::filterImportedReport';

    $appRoutes['registerLorry'] = 'Lorries::registerLorry';
    $appRoutes['getUnpaidLorries'] = 'Lorries::getUnpaidLorries';
    $appRoutes['listLorries/(:any)'] = 'Lorries::listRegisteredLorries/$1';
    $appRoutes['publishLorryData'] = 'Lorries::publishLorryData';
    $appRoutes['searchSbl'] = 'Lorries::searchSbl';
    $appRoutes['grabLastLorry'] = 'Lorries::grabLastLorry';
    $appRoutes['updateLorryData'] = 'Lorries::updateLorryData';
    $appRoutes['editLorry'] = 'Lorries::editLorry';
    $appRoutes['deleteLorry'] = 'Lorries::deleteLorry';
    $appRoutes['downloadLorryChart/(:any)'] = 'Lorries::downloadLorryChart/$1';

    // ================Vehicle Tanks==============

    $appRoutes['listVehicleTanks/(:any)'] = 'VehicleTankCalibration::listRegisteredVtc/$1';
    $appRoutes['registerVehicleTank'] = 'VehicleTankCalibration::registerVehicleTank';
    $appRoutes['grabLastVehicle'] = 'VehicleTankCalibration::grabLastVehicle';
    $appRoutes['getUnpaidVehicles'] = 'VehicleTankCalibration::getUnpaidVehicles';
    $appRoutes['publishVtcData'] = 'VehicleTankCalibration::publishVtcData';
    $appRoutes['searchVtc'] = 'VehicleTankCalibration::searchVtc';
    $appRoutes['editVtc'] = 'VehicleTankCalibration::editVtc';

    $appRoutes['newVehicleTank'] = 'VehicleTankCalibration::newVehicleTank';
    $appRoutes['updateVehicleTank'] = 'VehicleTankCalibration::updateVehicleTank';
    $appRoutes['deleteCompartmentData'] = 'VehicleTankCalibration::deleteCompartmentData';

    $appRoutes['getVehicleDetails'] = 'VehicleTankCalibration::getVehicleDetails';
    $appRoutes['getCalibratedTanks'] = 'VehicleTankCalibration::getCalibratedTanks';
    $appRoutes['downloadCalibrationChart/(:any)'] = 'VehicleTankCalibration::downloadCalibrationChart/$1';
    $appRoutes['renewChart/(:any)/(:any)/(:any)'] = 'VehicleTankCalibration::renewChart/$1/$2/$3';

    //=================chart====================

    $appRoutes['createChart'] = 'VehicleTankCalibration::createChart';
    $appRoutes['vehicleCalibrationChart'] = 'VehicleTankCalibration::vehicleCalibrationChart';
    $appRoutes['searchVehicleTank'] = 'VehicleTankCalibration::searchVehicleTank';
    $appRoutes['updateChart'] = 'VehicleTankCalibration::updateChart';
    $appRoutes['editChart'] = 'VehicleTankCalibration::editChart';

    // ================Bulk Storage Tanks==============
    $appRoutes['addBulkStorageTank'] = 'BulkStorageTank::addBulkStorageTank';
    $appRoutes['listBulkStorageTanks'] = 'BulkStorageTank::listRegisteredBulkStorageTanks';
    $appRoutes['editBulkStorageTank/(:any)'] = 'BulkStorageTank::editBulkStorageTank/$1';
    $appRoutes['deleteBulkStorageTank/(:any)'] = 'BulkStorageTank::deleteBulkStorageTank/$1';

    // ================Fixed Storage Tanks==============
    $appRoutes['addFixedStorageTank'] = 'FixedStorageTank::addFixedStorageTank';
    $appRoutes['listFixedStorageTanks'] = 'FixedStorageTank::listRegisteredFixedStorageTanks';
    $appRoutes['editFixedStorageTank/(:any)'] = 'FixedStorageTank::editFixedStorageTank/$1';
    $appRoutes['deleteFixedStorageTank/(:any)'] = 'FixedStorageTank::deleteFixedStorageTank/$1';

    // ================flow Meter==============
    $appRoutes['addFlowMeter'] = 'FlowMeter::addFlowMeter';
    $appRoutes['FlowMeterList'] = 'FlowMeter::listRegisteredFlowMeters';
    $appRoutes['editFlowMeter/(:any)'] = 'FlowMeter::editFlowMeter/$1';
    $appRoutes['deleteFlowMeter/(:any)'] = 'FlowMeter::deleteFlowMeter/$1';

    // ================Water Meter==============


    $appRoutes['registerWaterMeter'] = 'WaterMeter::registerWaterMeter';
    $appRoutes['getUnpaidWaterMeters'] = 'WaterMeter::getUnpaidWaterMeters';
    $appRoutes['publishWaterMeterData'] = 'WaterMeter::publishWaterMeterData';
    $appRoutes['WaterMeterList/(:any)'] = 'WaterMeter::listRegisteredWaterMeters/$1';
    $appRoutes['editWaterMeter/(:any)'] = 'WaterMeter::editWaterMeter/$1';
    $appRoutes['deleteWaterMeter/(:any)'] = 'WaterMeter::deleteWaterMeter/$1';
    $appRoutes['downloadMeterChart/(:any)'] = 'WaterMeter::downloadMeterChart/$1';
    $appRoutes['printMeterChart/(:any)'] = 'WaterMeter::printMeterChart/$1';

    $appRoutes['addExtraWaterMeters'] = 'WaterMeter::addExtraWaterMeters';

    //=================Reports====================
    $appRoutes['reports'] = 'CollectionReports::index';
    $appRoutes['reportsManager'] = 'CollectionReports::index';
    $appRoutes['reportsDashboardController'] = 'CollectionReports::index';

    $appRoutes['getCollectionReport'] = 'CollectionReports::getCollectionReport';


    //=================Receivables Report====================

    $appRoutes['receivableSummary'] = 'ReceivableController::index';
    $appRoutes['downloadReceivableSummary/(:any)'] = 'ReceivableController::downloadReceivableSummary/$1';
    $appRoutes['regionDebt/(:any)/(:any)'] = 'ReceivableController::regionDebt/$1/$2';
    $appRoutes['downloadRegionalReceivables/(:any)/(:any)'] = 'ReceivableController::downloadRegionalReceivables/$1/$2';
    $appRoutes['getBillDetails'] = 'ReceivableController::getBillDetails';


    $appRoutes['getQuarterReportWithDateRange'] = 'CollectionReports::getQuarterReportWithDateRange';
    $appRoutes['getQuarterReport'] = 'CollectionReports::getQuarterReport';
    $appRoutes['getMonthlyReport'] = 'CollectionReports::getMonthlyReport';
    $appRoutes['customDateReport'] = 'CollectionReports::customDateReport';
    $appRoutes['downloadQuarterReport/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'CollectionReports::downloadQuarterReport/$1/$2/$3/$4/$5/$6/$7';
    $appRoutes['downloadMonthlyReport/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'CollectionReports::downloadMonthlyReport/$1/$2/$3/$4/$5/$6';
    $appRoutes['downloadCustomDateReport/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = 'CollectionReports::downloadCustomDateReport/$1/$2/$3/$4/$5/$6';

    //=================searching ====================
    $appRoutes['search'] = 'SearchController::index';


    $appRoutes['searchItem'] = 'SearchController::searchItem';
    $appRoutes['selectItem'] = 'SearchController::selectItem';


    //=================PortUnit====================
    $appRoutes['timeLog'] = 'TimeLog::timeLog';
    $appRoutes['addTimeLog'] = 'TimeLog::addTimeLog';
    $appRoutes['getLastLog'] = 'TimeLog::getLastLog';
    $appRoutes['getAllTimeLogs'] = 'TimeLog::getAllTimeLogs';
    $appRoutes['getAllTimeLogs'] = 'TimeLog::getAllTimeLogs';
    $appRoutes['downloadTimeLog/(:any)'] = 'TimeLog::downloadTimeLog/$1';

    $appRoutes['ullageBeforeDischarging'] = 'ShipUllage::index';
    $appRoutes['addShipOilTank'] = 'ShipUllage::addShipOilTank';
    $appRoutes['getAvailableShipUllageB4Discharge'] = 'ShipUllage::getAvailableShipUllageB4Discharge';
    $appRoutes['downloadUllageB4Discharging/(:any)'] = 'ShipUllage::downloadUllageB4Discharging/$1';

    $appRoutes['ullageAfterDischarging'] = 'ShipUllageAfter::index';
    $appRoutes['addShipOilTankUllageAfter'] = 'ShipUllageAfter::addShipOilTank';
    $appRoutes['getAvailableShipUllageAfterDischarge'] = 'ShipUllageAfter::getAvailableShipUllageAfterDischarge';
    $appRoutes['downloadUllageAfterDischarging/(:any)'] = 'ShipUllageAfter::downloadUllageAfterDischarging/$1';

    $appRoutes['addShipParticulars'] = 'PortUnit::addShipParticulars';
    $appRoutes['documents'] = 'PortUnit::documents';
    $appRoutes['searchExistingShips'] = 'PortUnit::searchExistingShips';
    $appRoutes['saveShipDocumentsInfo'] = 'PortUnit::saveShipDocumentsInfo';
    $appRoutes['selectShipDocuments'] = 'PortUnit::selectShipDocuments';
    $appRoutes['selectedShip'] = 'PortUnit::selectedShip';
    $appRoutes['downloadPortDocsPDF/(:any)'] = 'PortUnit::downloadPortDocsPDF/$1';

    $appRoutes['certificateOfQuantity'] = 'CertificateOfQuantity::index';
    $appRoutes['addCertificateOfQuantity'] = 'CertificateOfQuantity::addCertificateOfQuantity';
    $appRoutes['getCertificateOfQuantity'] = 'CertificateOfQuantity::getCertificateOfQuantity';
    $appRoutes['downloadCertificateOfQuantity/(:any)'] = 'CertificateOfQuantity::downloadCertificateOfQuantity/$1';

    $appRoutes['noteOfFactBeforeDischarging'] = 'NoteOfFactBeforeDischarge::index';
    $appRoutes['getNoteOfFactBefore'] = 'NoteOfFactBeforeDischarge::getNoteOfFactBefore';
    $appRoutes['addNoteOfFactBefore'] = 'NoteOfFactBeforeDischarge::addNoteOfFactBefore';
    $appRoutes['getNoteOfFactBefore'] = 'NoteOfFactBeforeDischarge::getNoteOfFactBefore';
    $appRoutes['downloadNoteOfFactBefore/(:any)'] = 'NoteOfFactBeforeDischarge::downloadNoteOfFactBefore/$1';

    //=================After====================
    $appRoutes['noteOfFactAfterDischarging'] = 'NoteOfFactAfterDischarge::index';
    $appRoutes['addNoteOfFactAfter'] = 'NoteOfFactAfterDischarge::addNoteOfFactAfter';
    $appRoutes['getNoteOfFactAfter'] = 'NoteOfFactAfterDischarge::getNoteOfFactAfter';
    $appRoutes['downloadNoteOfFactAfter/(:any)'] = 'NoteOfFactAfterDischarge::downloadNoteOfFactAfter/$1';

    //=================pressure log====================
    $appRoutes['pressureLog'] = 'PressureLog::index';
    $appRoutes['addPressureLog'] = 'PressureLog::addPressureLog';
    $appRoutes['getLastPressureLog'] = 'PressureLog::getLastPressureLog';
    $appRoutes['getAllPressureLogs'] = 'PressureLog::getAllPressureLogs';
    $appRoutes['downloadPressureLog/(:any)'] = 'PressureLog::downloadPressureLog/$1';

    $appRoutes['dischargingSequence'] = 'DischargingSequence::index';
    $appRoutes['addTankDischargingSequence'] = 'DischargingSequence::addTankDischargingSequence';
    $appRoutes['checkTanks'] = 'DischargingSequence::checkTanks';
    $appRoutes['updateTankTimeDate'] = 'DischargingSequence::updateTankTimeDate';
    $appRoutes['getDischargingSequence'] = 'DischargingSequence::getDischargingSequence';
    $appRoutes['downloadDischargingSequence/(:any)'] = 'DischargingSequence::downloadDischargingSequence/$1';

    $appRoutes['lineDisplacement'] = 'LineDisplacement::index';
    $appRoutes['addLineDisplacement'] = 'LineDisplacement::addLineDisplacement';
    $appRoutes['getLineDisplacement'] = 'LineDisplacement::getLineDisplacement';
    $appRoutes['downloadLineDisplacement/(:any)'] = 'LineDisplacement::downloadLineDisplacement/$1';

    $appRoutes['provisionalReport'] = 'ProvisionalReport::index';
    $appRoutes['addProvisionalReport'] = 'ProvisionalReport::addProvisionalReport';
    $appRoutes['getProvisionalReport'] = 'ProvisionalReport::getProvisionalReport';
    $appRoutes['downloadProvisionalReport/(:any)'] = 'ProvisionalReport::downloadProvisionalReport/$1';

    //=================Discharge order====================
    $appRoutes['dischargeOrder'] = 'DischargeOrder::index';
    $appRoutes['addDischargeOrder'] = 'DischargeOrder::addDischargeOrder';
    $appRoutes['getDischargeOrder'] = 'DischargeOrder::getDischargeOrder';
    $appRoutes['downloadDischargeOrder/(:any)'] = 'DischargeOrder::downloadDischargeOrder/$1';

    //=================ON SHORE====================

    $appRoutes['shoreTankMeasurement'] = 'shoreTankMeasurementData::index';
    $appRoutes['addShoreTank'] = 'shoreTankMeasurementData::addShoreTank';
    $appRoutes['checkShoreTanks'] = 'shoreTankMeasurementData::checkTanks';
    $appRoutes['getTankDetails'] = 'shoreTankMeasurementData::getTankDetails';
    $appRoutes['addMeasurementData'] = 'shoreTankMeasurementData::addMeasurementData';
    $appRoutes['getTankMeasurements'] = 'shoreTankMeasurementData::getTankMeasurements';
    $appRoutes['addSealPosition'] = 'shoreTankMeasurementData::addSealPosition';
    $appRoutes['getSealPositions'] = 'shoreTankMeasurementData::getSealPositions';
    $appRoutes['addStatus'] = 'shoreTankMeasurementData::addStatus';
    $appRoutes['getStatus'] = 'shoreTankMeasurementData::getStatus';
    $appRoutes['downloadShoreTankMeasurementData/(:any)/(:any)'] = 'shoreTankMeasurementData::downloadShoreTankMeasurementData/$1/$2';

    //=================PortUnit====================

    //=================Analytic====================

    $appRoutes['analytics'] = 'Analytics::index';
    $appRoutes['analyticsOfficer'] = 'Analytics::index';
    $appRoutes['analyticsManager'] = 'Analytics::index';
    $appRoutes['projection'] = 'Analytics::index';
    $appRoutes['getActivityCollection'] = 'Analytics::getActivityCollection';
    $appRoutes['getVtcCollection'] = 'Analytics::getVtcCollection';
    $appRoutes['getSblCollection'] = 'Analytics::getSblCollection';
    $appRoutes['collectionTarget'] = 'Analytics::targets';

    $appRoutes['saveRegionalTarget'] = 'Analytics::saveRegionalTarget';
    $appRoutes['getRegionTargets'] = 'Analytics::getRegionTargets';
    $appRoutes['editRegionTarget'] = 'Analytics::editRegionTarget';

    $appRoutes['activitiesInRegion'] = 'Analytics::activitiesInRegion';

    $appRoutes['updateRegionTarget'] = 'Analytics::updateRegionTarget';

    $appRoutes['saveActivityTarget'] = 'Analytics::saveActivityTarget';
    $appRoutes['getActivityTargets'] = 'Analytics::getActivityTargets';
    $appRoutes['editActivityTarget'] = 'Analytics::editActivityTarget';
    $appRoutes['updateActivityTarget'] = 'Analytics::updateActivityTarget';

    $appRoutes['xxx'] = 'Analytics::xxx';

    $appRoutes['printPage'] = 'Yap::pdf';
    $appRoutes['getControlNumber'] = 'Miscellaneous::getControlNumber';

    //=================PRE PACKAGE ROUTES==================== 

    $appRoutes['prePackage'] = 'PrePackageController::index';
    $appRoutes['addPrePackageCustomer'] = 'PrePackageController::addPrePackageCustomer';
    $appRoutes['searchPrePackageCustomer'] = 'PrePackageController::searchCustomer';
    $appRoutes['editPrePackageCustomer'] = 'PrePackageController::editPrePackageCustomer';
    $appRoutes['getPrePackageCustomer'] = 'PrePackageController::getPrePackageCustomer';
    $appRoutes['addProductDetails'] = 'PrePackageController::addProductDetails';
    $appRoutes['selectProduct'] = 'PrePackageController::selectProduct';
    $appRoutes['saveMeasurementData'] = 'PrePackageController::saveMeasurementData';
    $appRoutes['getMeasurementData'] = 'PrePackageController::getMeasurementData';
    $appRoutes['getCompleteProducts'] = 'PrePackageController::getCompleteProducts';
    $appRoutes['createBill'] = 'PrePackageController::createBill';
    $appRoutes['registeredPrepackages/(:any)'] = 'PrePackageController::registeredPrepackages/$1';
    $appRoutes['downloadProductData/(:any)/(:any)/(:any)'] = 'PrePackageController::downloadProductData/$1/$2/$3';

    $appRoutes['prePackageReport'] = 'PrePackageController::prePackageReport';
    $appRoutes['productList'] = 'PrePackageController::productList';
    $appRoutes['generatePrepackageReport'] = 'PrePackageController::generatePrepackageReport';
    $appRoutes['downloadPrepackageReport/(:any)'] = 'PrePackageController::downloadPrepackageReport/$1';

    $appRoutes['getProductsWithMeasurements'] = 'PrePackageController::getProductsWithMeasurements';
    $appRoutes['checkQuantityId'] = 'PrePackageController::checkQuantityId';
    $appRoutes['getAllProducts'] = 'PrePackageController::getAllProducts';
    // $appRoutes['verifiedProducts'] = 'PrePackageController::getCompleteProducts';



    //NON TR BILL ROUTES\

    $appRoutes['billManagement'] = 'WmaBillController::index';
    $appRoutes['cancelledBills'] = 'WmaBillController::cancelledBills';
    $appRoutes['cancellationRequests'] = 'BillController::cancellationRequests';
    // $appRoutes['searchBill'] = 'WmaBillController::searchBill';
    // $appRoutes['selectBill'] = 'WmaBillController::selectBill';
    $appRoutes['billCreation'] = 'WmaBillController::billCreation';
    $appRoutes['billSubmissionRequest'] = 'WmaBillController::billSubmissionRequest';
    $appRoutes['billResubmissionRequest'] = 'WmaBillController::billResubmissionRequest';
    $appRoutes['billRenewRequest'] = 'WmaBillController::billRenewRequest';
    // $appRoutes['dom'] = 'WmaBillController::dom';




    // Billing and Receipt 
    // $appRoutes['billManagement'] = 'BillController::index';
    // $appRoutes['cancelledBills'] = 'BillController::cancelledBills';
    // $appRoutes['cancellationRequests'] = 'BillController::cancellationRequests';
    $appRoutes['searchBill'] = 'BillController::searchBill';
    $appRoutes['selectBill'] = 'BillController::selectBill';
    $appRoutes['billCreationCombined'] = 'BillController::billCreationCombined';
    $appRoutes['combinedBillSubmissionRequest'] = 'BillController::billSubmissionRequest';
    // $appRoutes['billResubmissionRequest'] = 'BillController::billResubmissionRequest';
    // $appRoutes['dom'] = 'BillController::dom';
    // $appRoutes['domAjax'] = 'BillController::domAjax';


    $appRoutes['reconciliation'] = 'BillController::reconciliationData';
    $appRoutes['billCancellationRequest'] = 'BillController::billCancellationRequest';
    $appRoutes['cancel'] = 'BillController::cancel';
    $appRoutes['wmaBillReconciliation'] = 'WmaBillController::billReconciliation';
    $appRoutes['billReconciliation'] = 'BillController::billReconciliation';
    $appRoutes['billChange'] = 'BillController::billChange';
    $appRoutes['billCancellation'] = 'BillController::billCancellation';
    $appRoutes['billCancellationApproval'] = 'BillController::billCancellationApproval';

    $appRoutes['wmaBillCancellationApproval'] = 'WmaBillController::billCancellationApproval'; //non tr bill


    $appRoutes['trBillRenewRequest'] = 'BillController::billRenewRequest';




    //=================call back urls for GePg====================



    $appRoutes['payments'] = 'BillController::payments';
    $appRoutes['searchPayment'] = 'BillController::searchPayment';
    $appRoutes['selectPayment'] = 'BillController::selectPayment';


    //=================RECONCILIATION====================

    $appRoutes['paymentReconciliation'] = 'ReconciliationController::index';
    $appRoutes['processRecon'] = 'ReconciliationController::processRecon';
    $appRoutes['cashbookToBank/(:any)'] = 'ReconciliationController::CashbookToBank/$1';
    $appRoutes['cashbookToBankMatch'] = 'ReconciliationController::cashbookToBankMatch';



    // regions, districts, wards and postal codes
    $appRoutes['fetchRegions'] = 'Miscellaneous::fetchRegions';
    $appRoutes['fetchDistricts'] = 'Miscellaneous::fetchDistricts';
    $appRoutes['fetchWards'] = 'Miscellaneous::fetchWards';
    $appRoutes['fetchPostCodes'] = 'Miscellaneous::fetchPostCodes';

    //=================CENTER SUMMARY====================


    $appRoutes['collectionSummary'] = 'CollectionSummaryController::index';
    $appRoutes['activitiesSummary/(:any)/(:any)'] = 'CollectionSummaryController::activitiesSummary/$1/$2';
    $appRoutes['downloadCentersSummary'] = 'CollectionSummaryController::downloadCentersSummary';



    // ================PETROLEUM IMPORT==============

    //vessels
    $appRoutes['vessels'] = 'PetroleumImportController::vessels';
    $appRoutes['addVessel'] = 'PetroleumImportController::addVessel';

    //importers

    $appRoutes['importers'] = 'PetroleumImportController::importers';
    $appRoutes['addImporter'] = 'PetroleumImportController::addImporter';


    //petroleum data
    $appRoutes['petroleumData'] = 'PetroleumImportController::petroleumData';
    $appRoutes['addPetroleumData'] = 'PetroleumImportController::addPetroleumData';


    //COQ
    $appRoutes['certificateOfQuantity'] = 'CoqController::index';
    $appRoutes['addCertificateOfQuantity'] = 'CoqController::addCertificateOfQuantity';
    $appRoutes['getVesselCertificate/(:any)'] = 'CoqController::getVesselCertificate/$1';



    //sailing report
    $appRoutes['vesselSailingReport'] = 'SailingReportController::index';
    $appRoutes['addSailingReport'] = 'SailingReportController::addSailingReport';
    $appRoutes['getSailingReport/(:any)'] = 'SailingReportController::getSailingReport/$1';

    //outturn report
    $appRoutes['vesselOutturnReport'] = 'OutturnReportController::index';
    $appRoutes['addOutturnReport'] = 'OutturnReportController::addOutturnReport';
    $appRoutes['getOutturnReport/(:any)'] = 'OutturnReportController::getOutturnReport/$1';




    $routes->map($appRoutes);
});


$routes->get('goBack', 'Miscellaneous::goBack');
//Routes specifically for users belonging to officer group
// $routes->group('', function ($routes, $officerRoutes = []) {



//     $routes->map($officerRoutes);
// });
//Routes specifically for users belonging to Manager group
$routes->group('', ['filter' => ['ManagerFilter', 'AuthFilter']], function ($routes, $managerRoutes = []) {
    // ================Manager==============
    $managerRoutes['managerChart'] = 'Manager::analytics';
    $managerRoutes['manager'] = 'Manager::index';
    $managerRoutes['managerProfile'] = 'Manager::managerProfile';
    $managerRoutes['managerDashboard'] = 'Manager::index';
    $managerRoutes['addGroup'] = 'Manager::addGroup';
    $managerRoutes['createTask'] = 'Manager::createTask';
    $managerRoutes['assignToGroup'] = 'Manager::assignToGroup';
    $managerRoutes['assignTask'] = 'Manager::assignTask';
    $managerRoutes['assignToIndividual'] = 'Manager::assignToIndividual';
    $managerRoutes['viewTasks'] = 'Manager::viewTasks';
    $managerRoutes['listAllScales'] = 'Manager::listAllScales';
    $managerRoutes['service-requests'] = 'Manager::serviceRequests';
    $managerRoutes['license-applications'] = 'Manager::licenseApplications';
    $managerRoutes['view-application/(:any)'] = 'Manager::applicationDetails/$1';
    $managerRoutes['download-application/(:any)'] = 'Manager::downloadApplication/$1';
    $managerRoutes['confirm-service-request/(:any)'] = 'Manager::confirmServiceRequests/$1';
    $managerRoutes['download-service-request/(:any)'] = 'Manager::downloadServiceRequests/$1';


    $routes->map($managerRoutes);
});

//Routes specifically for users belonging to to upper management level group
// $routes->group('', ['filter' => 'TopLevelFilter'], function ($routes, $topLevelRoutes = []) {


//     $topLevelRoutes['collectionSummary'] = 'CollectionSummaryController::index';
//     $topLevelRoutes['activitiesSummary/(:any)/(:any)'] = 'CollectionSummaryController::activitiesSummary/$1/$2';
//     $topLevelRoutes['downloadCentersSummary'] = 'CollectionSummaryController::downloadCentersSummary';

//     $routes->map($topLevelRoutes);
// });


$routes->group('admin', ['filter' => 'AdminFilter'], function ($routes, $adminRoutes = []) {
    $adminRoutes['createUserAccount'] = 'Admin::createUserAccount';
    $adminRoutes['createBackup'] = 'BackupController::createBackup';
    $adminRoutes['backup'] = 'BackupController::index';
    // $adminRoutes['dashboard'] = 'Admin::index';
    $adminRoutes['users'] = 'Admin::usersPage';
    $adminRoutes['getUsers'] = 'Admin::getUsers';
    $adminRoutes['changeStatus'] = 'Admin::changeStatus';
    $adminRoutes['getAllUsers'] = 'Admin::getAllUsers';
    $adminRoutes['activateAccount/(:any)'] = 'Admin::activateAccount/$1';
    $adminRoutes['deactivateAccount/(:any)'] = 'Admin::deactivateAccount/$1';
    $adminRoutes['getSingleUser'] = 'Admin::getSingleUser';
    $adminRoutes['updateUser'] = 'Admin::updateUser';
    $adminRoutes['resetPassword'] = 'Admin::resetPassword';
    $adminRoutes['setting'] = 'SettingsController::index';
    $adminRoutes['settle'] = 'DataTestController::settle';
    // $routes->get('settle', 'DataTestController::settle');

    //=================LOGS====================
    $adminRoutes['logs'] = 'LogsController::index';
    $adminRoutes['activityLogs'] = 'LogsController::activityLogs';
    //=================POS MANAGEMENT ====================
    $adminRoutes['posManagement'] = 'PosManagement::index';
    $adminRoutes['addPos'] = 'PosManagement::addPos';
    $adminRoutes['editPos'] = 'PosManagement::editPos';
    $adminRoutes['deletePos'] = 'PosManagement::deletePos';
    $adminRoutes['updatePos'] = 'PosManagement::updatePos';


    $adminRoutes['updateMissingControlNumber'] = 'DataDevController::updateMissingControlNumber';




    $routes->map($adminRoutes);
});




$routes->get('newForm', 'ContactController2::index');
$routes->post('addForm', 'ContactController2::create');
$routes->post('editRecord', 'ContactController2::editRecord');
$routes->post('updateRecord', 'ContactController2::updateRecord');

service('auth')->routes($routes);



$routes->set404Override(function () {

    $data = [
        "title" => "Not Found",

    ];

    echo view('Pages/404', $data);
});

// Metrological routes are defined above in the main metrological group

// API routes with no debug toolbar
$routes->group('api', ['filter' => 'AuthFilter'], function ($routes) {
    $routes->get('document-verification/(:num)', '\\App\\Modules\\MetrologicalSupervision\\Controllers\\MaritimeController::getDocumentVerificationsApi/$1');
    $routes->post('document-verification/save', '\\App\\Modules\\MetrologicalSupervision\\Controllers\\MaritimeController::saveDocumentVerificationApi');
});
