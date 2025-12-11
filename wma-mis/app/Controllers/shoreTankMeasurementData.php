<?php 
// namespace App\Controllers;

// use App\Libraries\CommonTasksLibrary;
// use App\Models\PortModel;
// use App\Models\ProfileModel;

// class ShoreTankMeasurementData extends BaseController
// {
//     public $uniqueId;
//     public $managerId;
//     public $role;
//     public $city;
//     public $portUnitModel;
//     public $session;
//     public $profileModel;
//     public $CommonTasks;

//     public $sessionExpiration;

//     public $variable;
//     private $token;

//     public $appRequest;

//     public function __construct()
//     {
//         $this->appRequest = service('request');
//         $this->portUnitModel = new PortModel();
//         $this->profileModel = new ProfileModel();
//         $this->session = session();

//         $this->uniqueId = $this->session->get('loggedUser');
//         $this->managerId = $this->session->get('manager');
//         $this->role = $this->profileModel->getRole($this->uniqueId)->role;
//         $this->city = $this->session->get('city');
//         $this->CommonTasks = new CommonTasksLibrary();

//         $this->token = csrf_hash();
//         helper(['form', 'array', 'regions', 'date', 'documents','image']);

//     }

//     public function getVariable($var)
//     {
//         return $this->appRequest->getVar($var, FILTER_SANITIZE_STRING);
//     }

//     public function index()
//     {

       

//         $uniqueId = $this->uniqueId;
//         $role = $this->role;
//         $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
//         $data['role'] = $role;
//         $data['page'] = [
//             "title" => "Shore Tank Measurement Data",
//             "heading" => "Shore Tank Measurement Data",
//         ];

//         $data['measurementParticulars'] = $this->portUnitModel->getMeasurementParticulars();
//         $data['sealPositions'] = $this->portUnitModel->getSealPositions();

//         return view('Pages/Port/shoreTankData', $data);
//     }

//     public function addShoreTank()
//     {

//         if ($this->request->getMethod() == 'POST') {
//             $shipId = $this->getVariable('shipId');
//             $tankNumber = $this->getVariable('tankNumber');
//             $tankData = [
//                 'ship_id' => $shipId,
//                 'terminal' => $this->getVariable('terminal'),
//                 'product' => $this->getVariable('product'),
//                 'tank_number' => $tankNumber,
//                 'tank_number' => $this->getVariable('tankNumber'),
//                 'before_loading' => $this->getVariable('beforeLoading'),
//                 'after_loading' => $this->getVariable('afterLoading'),
//                 'date' => $this->getVariable('date'),
//                 'time' => $this->getVariable('time'),
//                 'unique_id' => $this->uniqueId,

//             ];

//             // echo json_encode($tankData);
//             // exit;
//             $request = $this->portUnitModel->addShoreTank($tankData);
//             if ($request) {
//                 echo json_encode([
//                     'message' => 'Added',
//                     'lastTank' => $this->portUnitModel->getSingleTank($tankNumber),
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'Failed',
//                 ]);
//             }

//         }

//     }

//     public function getTankDetails()
//     {
       

//         $tankId = $this->getVariable('tankId');
//         $request = $this->portUnitModel->getSingleTank($tankId);

//         if ($request) {

//             echo json_encode([

//                 'tank' => $request,
//                 'token' => $this->token,

//             ]);
//         }

//     }

//     public function checkTanks()
//     {
       

//         if ($this->request->getMethod() == 'POST') {

//             $shipId = $this->getVariable('shipId');

//             $request = $this->portUnitModel->getShoreTanks($shipId);
//             if ($request) {
//                 echo json_encode([

//                     'shoreTanks' => $request,
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'nothing',
//                     'token' => $this->token,
//                 ]);
//             }

//         }
//     }

//     public function addMeasurementData()
//     {
       

//         if ($this->request->getMethod() == 'POST') {

//             $tankId = $this->getVariable('tankId');
//             $particularId = $this->getVariable('particularId');
//             function msg($pId)
//             {
//                 switch ($pId) {
//                     case '1':
//                         return 'Reference Dip (mm) Already Exist';
//                         break;
//                     case '2':
//                         return 'Product Dip (mm) Already Exist';
//                         break;
//                     case '3':
//                         return 'Product Volume (L) Already Exist';
//                         break;
//                     case '4':
//                         return 'Free Water Dip (mm) Already Exist';
//                         break;
//                     case '5':
//                         return 'Free Water Volume (L) Already Exist';
//                         break;
//                     case '6':
//                         return 'Tank Temperature (ºC) Already Exist';
//                         break;
//                     case '7':
//                         return 'Specific Gravity (SG) Already Exist';
//                         break;
//                     case '8':
//                         return 'Sample Temperature (ºC) Already Exist';
//                         break;
//                 }

//             }

//             $check = $this->portUnitModel->checkExistingMeasurement($tankId, $particularId);
//             $measurements = $this->portUnitModel->getMeasurementData($tankId);
//             if ($check) {

//                 echo json_encode([
//                     'message' => 'Exists',
//                     'exist' => msg($particularId),
//                     'token' => $this->token,
//                     'measurements' => $measurements,

//                 ]);

//             } else {

//                 $data = [
//                     'tank_id' => $tankId,
//                     'particular_id' => $particularId,
//                     'measurement1' => $this->getVariable('measurement1'),
//                     'measurement2' => $this->getVariable('measurement2'),
//                     'measurement3' => $this->getVariable('measurement3'),
//                     'average' => $this->getVariable('average'),
//                     'unique_id' => $this->uniqueId,
//                 ];
//                 $request = $this->portUnitModel->addMeasurementData($data);
//                 if ($request) {
//                     echo json_encode([
//                         'message' => 'Added',
//                         'token' => $this->token,
//                         'measurements' => $measurements,

//                     ]);
//                 } else {
//                     echo json_encode('error');
//                 }

//             }

//         }

//     }

//     public function getTankMeasurements()
//     {
       

//         if ($this->request->getMethod() == 'POST') {

//             $tankId = $this->getVariable('tankId');
//             $request = $this->portUnitModel->getMeasurementData($tankId);

//             if ($request) {
//                 echo json_encode([

//                     'measurements' => $request,
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'nothing',
//                     'token' => $this->token,
//                 ]);
//             }

//         }

//     }

//     public function addSealPosition()
//     {
//         if ($this->request->getMethod() == 'POST') {
//             $tankId = $this->getVariable('tankId');
//             $tankId = $this->getVariable('tankId');
//             $data = [
//                 'tank_id' => $tankId,
//                 'seal_position_id' => $this->getVariable('sealPosition'),
//                 'seal_number' => $this->getVariable('sealNumber'),
//                 'unique_id' => $this->uniqueId,

//             ];

//             // echo json_encode($data);
//             // exit;
//             $request = $this->portUnitModel->addSealPosition($data);
//             if ($request) {
//                 echo json_encode([
//                     'message' => 'Added',
//                     'seals' => $this->portUnitModel->getSeals($tankId),
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'Failed',
//                 ]);
//             }

//         }

//     }
//     public function getSealPositions()
//     {
       

//         if ($this->request->getMethod() == 'POST') {

//             $tankId = $this->getVariable('tankId');
//             $request = $this->portUnitModel->getSeals($tankId);

//             if ($request) {
//                 echo json_encode([

//                     'seals' => $request,
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'nothing',
//                     'token' => $this->token,
//                 ]);
//             }

//         }

//     }

//     public function addStatus()
//     {
//         if ($this->request->getMethod() == 'POST') {
//             $tankId = $this->getVariable('tankId');
//             $data = [
//                 'tank_id' => $tankId,
//                 'status' => $this->getVariable('status'),
//                 'product' => $this->getVariable('product'),
//                 'verified' => $this->getVariable('verified'),
//                 'unique_id' => $this->uniqueId,

//             ];

//             // echo json_encode($data);
//             // exit;
//             $request = $this->portUnitModel->addStatus($data);
//             if ($request) {
//                 echo json_encode([
//                     'message' => 'Added',
//                     'status' => $this->portUnitModel->getStatus($tankId),
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'Failed',
//                 ]);
//             }

//         }

//     }

//     public function getStatus()
//     {
       

//         if ($this->request->getMethod() == 'POST') {

//             $tankId = $this->getVariable('tankId');
//             $request = $this->portUnitModel->getStatus($tankId);

//             if ($request) {
//                 echo json_encode([

//                     'status' => $request,
//                     'token' => $this->token,

//                 ]);
//             } else {
//                 echo json_encode([
//                     'message' => 'nothing',
//                     'token' => $this->token,
//                 ]);
//             }

//         }

//     }

//     //=================Download ====================
//     // public function downloadShoreTankMeasurementData($shipId, $tankId)
//     // {
       

//     //     $date = date('d-M,Y h:i:s ');
//     //     $dompdf = new \Dompdf\Dompdf();
//     //     $options = new \Dompdf\Options();

//     //     $title = 'Shore TankMeasurement Data';

//     //     $data['tank'] = $this->portUnitModel->getSingleTank($tankId);
//     //     $data['measurements'] = $this->portUnitModel->getMeasurementData($tankId);
//     //     $data['seals'] = $this->portUnitModel->getSeals($tankId);
//     //     $data['status'] = $this->portUnitModel->getStatus($tankId);

//     //     $dompdf->loadHtml(view('PortUnitTemplates/shoreTankMeasurementDataPdf', $data));
//     //     $dompdf->setPaper('A4', 'portrait');
//     //     $options->set('isRemoteEnabled', true);

//     //     $dompdf->render();

//     //     $dompdf->stream($title . ':' . $date . '.pdf', array('Attachment' => 0));

//     // }

// }