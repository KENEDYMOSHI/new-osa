<?php

namespace App\Controllers;

use DateTime;
use stdClass;
use DateInterval;
use LSS\Array2XML;
use BillProcessing;
use App\Models\AppModel;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\SmsLibrary;
use App\Libraries\VtvLibrary;

use App\Models\CustomerModel;
use App\Libraries\ArrayLibrary;
use function App\Helpers\button;
use App\Libraries\StickerLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Libraries\CertificateLibrary;
use App\Libraries\ActivityBillProcessing;
use App\Models\UsersModel;
use CodeIgniter\Shield\Models\UserModel;
use PHPUnit\TextUI\Output\Printer;

//use \CodeIgniter\Models\VtcModel;

class VehicleTankCalibration extends BaseController
{
    protected $uniqueId;
    protected $managerId;

    protected $VtcModel;
    protected $session;
    protected $profileModel;
    protected $CommonTasks;
    protected $token;
    protected $customersModel;
    protected $vtvLibrary;
    protected $GfsCode;
    protected $penaltyGfsCode;
    protected $collectionCenter;
    protected $user;
    protected $appModel;
    protected $sms;
    protected $table;


    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->GfsCode = setting('Gfs.vtv');
        $this->penaltyGfsCode = setting('Gfs.fine');
        $this->VtcModel = new VtcModel();
        $this->vtvLibrary = new VtvLibrary();
        $this->profileModel = new ProfileModel();
        // $this->session = session();
        $this->token = csrf_hash();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;

        $this->CommonTasks = new CommonTasksLibrary();
        $this->customersModel = new CustomerModel;
        $this->user = auth()->user();
        $this->appModel = new AppModel();
        $this->sms = new SmsLibrary();
        $this->table = new \CodeIgniter\View\Table();
    }


    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    //=================search vehicle tank if exists====================

    public function searchVtc()
    {
        $hash = $this->getVariable('hash');
        $plateNumber =   $this->getVariable('licensePlate');
        $request = $this->VtcModel->findMatch($hash, $plateNumber);
        if ($request) {
            return  $this->response->setJSON([
                'status' => 1,
                'data' => $request,
                'token' => $this->token,
            ]);
        } else {
            return  $this->response->setJSON([
                'status' => 1,
                'msg' => 'empty',
                'data' => $request,
                'token' => $this->token,
            ]);
        }
    }
    // ================Adding Vtc information to database ==============

    public function newVehicleTank()
    {
        try {
            if ($this->request->getMethod() == 'POST') {
                //=================Checking the last Id before incrementing it====================
                $lastId = $this->VtcModel->checkLastId();
                // $lastId ? $lastId->id : '';
                $instrumentId = '';


                if ($lastId != '') {
                    $instrumentId .= increment_string($lastId->id);
                } else {
                    $instrumentId .= 'VTV_1';
                }



                //=================adding extra charges when applicable====================

                function extractId($string)
                {
                    return  preg_replace('/^([^_]+_[^_]+)_.+$/', '$1', $string);
                }



                $registrationDate =   date('Y-m-d');
                $hash = $this->getVariable('customerId');
                $testing = $this->getVariable('testing');
                $visualInspection = $this->getVariable('visualInspection');

                if ($visualInspection == 'Pass' && $testing == 'Pass') {
                    $status = 'Pass';
                } else if ($visualInspection == 'Pass' && $testing == 'Rejected') {
                    $status = 'Rejected';
                } else if ($visualInspection == 'Rejected' && $testing == 'Pass') {
                    $status = 'Rejected';
                } else if ($visualInspection == 'Rejected' && $testing == 'Rejected') {
                    $status = 'Rejected';
                } else if ($visualInspection == 'Adjusted' || $testing == 'Adjusted') {
                    $status = 'Adjusted';
                } else if ($visualInspection == 'Condemned' || $testing == 'Condemned') {
                    $status = 'Condemned';
                }
                $penaltyAmount = (float)str_replace(',', '', $this->getVariable('penaltyAmount'));

                $plateNumber = $this->getVariable('trailerPlateNumber');
                $oldVehicleId = $this->getVariable('oldVehicleId');

                $vehicleTank = [
                    'id' => $instrumentId,
                    'hash' =>   $hash,
                    'task' =>   $this->getVariable('task'),
                    'visualInspection' =>   $visualInspection,
                    'testing' =>   $testing,
                    'gfCode' => $this->GfsCode,
                    'registration_date' =>   $registrationDate,
                    "next_calibration" =>   date('Y-m-d', strtotime(date('Y-m-d') . ' +1 year')),
                    'tin_number' =>   $this->getVariable('tinNumber'),
                    'region' => $this->user->collection_center,
                    'driver_name' =>   $this->getVariable('driverName'),
                    'driver_license' =>   $this->getVariable('driverLicense'),
                    'vehicle_brand' =>   $this->getVariable('vehicleBrand'),
                    'compartments' =>   $this->getVariable('compartments'),
                    'hose_plate_number' =>   $this->getVariable('hosePlateNumber'),
                    'trailer_plate_number' =>   $this->getVariable('trailerPlateNumber'),

                    'status' =>   $status,
                    'include_sticks' => (int)$this->getVariable('includeSticks')  ?? 0,

                    'remark' =>   $this->getVariable('remark'),
                    'hasPenalty' => $this->getVariable('hasPenalty') == "on" ? 1 : 0,
                    'penaltyAmount' => $penaltyAmount,
                    'skipChart' =>   $this->getVariable('skipChart') ?? 0,
                    'capacity' =>   $this->getVariable('capacity'),
                    'repairDeadline' =>   $this->getVariable('repairDeadline'),
                    'latitude' =>   $this->getVariable('latitude'),
                    'longitude' =>   $this->getVariable('longitude'),
                    'unique_id' => $this->uniqueId,

                ];

                //check if vehicle tank already exist
                // $check = $this->VtcModel->checkPlateNumber($plateNumber);

                // $vehicleId = extractId($check->id);
                // $compartmentData  = $this->VtcModel->getCompartments($vehicleId);

                // return  $this->response->setJSON([
                //     'status' => 0,
                //     'oldVehicleId' => $oldVehicleId,

                //     'token' => $this->token,
                // ]);

                // exit;

                if (!empty($oldVehicleId)) {
                    $compartmentsCount = $this->chartReuse($oldVehicleId, $instrumentId);
                    $vehicleTank['compartments'] = $compartmentsCount;
                }


                $request = $this->VtcModel->registerVehicleTank($vehicleTank);

                if ($request) {
                    $this->appModel->createTempId([
                        'itemId' => $instrumentId,
                        'customerId' => $hash,
                        'activity' => $this->GfsCode,
                        'collectionCenter' => $this->collectionCenter
                    ]);
                    return  $this->response->setJSON([
                        'status' => 1,
                        'vehicles' => $this->VtcModel->getClientVehicles($hash),
                        'msg' => 'Vehicle Added',
                        'token' => $this->token,
                    ]);
                } else {
                    return  $this->response->setJSON([
                        'status' => 1,
                        'msg' => 'Something Went Wrong',
                        'token' => $this->token,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'trace' =>  $th->getTrace(),
                'token' => $this->token,

            ]);
        }
    }

    public function deleteCompartmentData()
    {
        try {
            $id = $this->getVariable('id');
            $query = $this->VtcModel->deleteCompartmentData($id);
            if ($query) {
                return  $this->response->setJSON([
                    'status' => 1,
                    'msg' => 'Data Deleted Successfully',
                    'token' => $this->token,
                ]);
            } else {
                return  $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Something Went Wrong',
                    'token' => $this->token,
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
            return $this->response->setJSON($response);
        }
    }


    public function chartReuse($vehicleId, $newId)
    {
        $letters = range('A', 'L');

        $stamp = '';

        $month = date('n');

        if ($month >= 1 && $month <= 12) {
            $stamp .= $letters[$month - 1] . ':' . date('Y');
        }

        $centerName = centerName();
        $compartmentData  = $this->VtcModel->getCompartments($vehicleId);

        $newCompartments = array_map(function ($compartment) use ($newId, $stamp) {
            return [
                'vehicle_id' => $newId,
                'compartment_number' => $compartment->compartment_number,
                'stamp_number' => $stamp,
                'tank_top' => $compartment->tank_top,
                'litres' => $compartment->litres,
                'millimeters' => $compartment->millimeters,
                'unique_id' => $this->uniqueId,

            ];
        }, $compartmentData);

        $comps = array_map(fn($compartment) => $compartment['compartment_number'], $newCompartments);

        $count = count(array_unique($comps));


        $this->VtcModel->addCompartmentData($newCompartments);


        $centerInitials = 'V:' . strtoupper(substr($centerName, 0, 3)) . '-';
        $chart = $this->VtcModel->checkChartNumber();

        $number = empty($chart) ? '100' : substr($chart->number, 6, -5) + 1;

        $this->VtcModel->createChartNumber([
            'vehicleId' => $newId,
            'number' => $centerInitials . $number . '-' . date('Y'),
            // 'downloadLimit' => $downloadLimit
        ]);

        return $count;
    }


    public function checkPlateNumber()
    {
        try {
            $plateNumber = $this->request->getVar('plateNumber');
            $vehicle = $this->VtcModel->checkPlateNumber($plateNumber);

            $vehicleId =   preg_replace('/^([^_]+_[^_]+)_.+$/', '$1', $vehicle->id ?? time());
            $compartmentData  = $this->VtcModel->getCompartments($vehicleId);



            if ($vehicle && $compartmentData) {
                return  $this->response->setJSON([
                    'status' => 1,
                    'capacity' => $vehicle->capacity,
                    'oldVehicleId' => $vehicleId,
                    // 'compartmentData' =>  $newCompartments,
                    'token' => $this->token,
                ]);
            } else {
                return  $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Vehicle not found',
                    'token' => $this->token,
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }



    //=================Grab last registered vehicle====================

    public function grabLastVehicle()
    {
        $lastVehicle = $this->VtcModel->grabTheLastVehicle();
        echo json_encode($lastVehicle);
    }

    //=================check if customer has any unpaid vehicle====================
    public function getUnpaidVehicles()
    {
        try {
            if ($this->request->getMethod() == 'POST') {
                $hashValue =   $this->getVariable('hashValue');
                $params = [
                    // 'hash' => $hashValue,
                    'customerId' => $hashValue,
                    'activity' => $this->GfsCode,
                    'collectionCenter' => $this->collectionCenter

                ];



                $request = $this->VtcModel->getAllUnpaidVehicles($params, '');

                $vehicles = new ArrayLibrary($request);


                $noOfCompartments = $vehicles->map(function ($vehicle) {
                    $data = $vehicle;
                    $data->compartments = $this->VtcModel->getCompartments($vehicle->id);

                    return
                        "<option value='$vehicle->id'>$vehicle->vehicle_brand |Hose: $vehicle->hose_plate_number | Trailer: $vehicle->trailer_plate_number </option>";
                })->get();




                $options = '';
                foreach ($noOfCompartments as $option) {
                    $options .= $option;
                }

                $dropdown = <<<"HTML"
                  <label>Vehicles</label>
                  <select  class='form-control' onchange='getVehicleDetails(this.value)'>
                    <option selected value="">--Select Vehicle--</option>
                      $options 
                  </select>
                 HTML;



                if ($request) {

                    return  $this->response->setJSON([
                        'status' => 1,
                        'compartmentDropdown' => $dropdown,
                        'data' => $request,
                        'token' => $this->token,
                    ]);
                } else {
                    return  $this->response->setJSON([
                        'status' => 0,
                        'compartmentDropdown' => '<p>No Data Found</p>',
                        'msg' => 'No Data Found',
                        'token' => $this->token,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'trace' =>  $th->getTrace(),
                'token' => $this->token,
                // 'req' => $request,

            ]);
        }
    }
    public function checkCompartments($id): bool
    {
        $check = $this->VtcModel->getCompartments($id);
        if (!empty($check)) {
            return true;
        } else {
            return false;
        }
    }

    public function getVehicleDetails()
    {
        $id = $this->getVariable('vehicleId');
        $vehicle = $this->VtcModel->getVehicleDetails(['id' => $id]);
        return $this->vehicleDetailsProcessing($vehicle);
    }

    public function vehicleDetailsProcessing($vehicle)
    {



        if (!isset($vehicle->id)) {

            return $this->response->setJSON([

                'tankCompartments' => '',
                'status' => 1,
                'vehicle' => '<h5>No Match Found !</h5>',
                'noOfCompartments' => '',
                'chart' => '',
                'chartCount' => 1,
                'data' => [
                    'filledCompartments' => '',
                    'available' => '',
                ],
                // 'data' => $availableCompartments,
                'token' => $this->token,
            ]);
        }
        $id = $vehicle->id;
        // return $this->response->setJSON([$vehicle]);
        $vehicle->hasCompartments = $this->checkCompartments($id);

        $tankCompartments = $this->VtcModel->getCompartments($id);
        $compartmentBlocks = $vehicle->compartments;



        $data = new ArrayLibrary((array)$tankCompartments);
        $availableCompartments =  count(array_unique($data->map(fn($c) => $c->compartment_number)->get()));

        if ($tankCompartments != '') {

            $filledCompartments = $this->groupCompartments((array)$tankCompartments, 'compartment_number');
        } else {
            $filledCompartments = [];
        }



        // $isVehicleOk = ($vehicle->visualInspection === 'Pass' && ($vehicle->testing === 'Pass' || $vehicle->testing === 'Rejected'));
        $isVehicleOk = ($vehicle->visualInspection === 'Pass' && $vehicle->testing === 'Pass');

        $customer = (new CustomerModel)->selectCustomer($vehicle->hash);
        $activity = 'Vehicle Tank Verification';
        $instrument = $vehicle->vehicle_brand . ' ' . $vehicle->trailer_plate_number;
        $address =  $customer->postal_address == '' ? 'P.O Box' : $customer->postal_address;
        $html = '';
        $currentDate = date('Y-m-d');
        $newDate = date('Y-m-d', strtotime($currentDate . ' +10 days'));
        $deadline = !$vehicle->repairDeadline ? $newDate : $vehicle->repairDeadline;
        if ($vehicle != null) {
            $rejectionNote = "rejectionNote/$customer->name/$address/$deadline/$activity/$instrument";
            $html .= <<<"HTML"
           <div class="card">
                                <div class="card-body">
                                     <input type="text" class="form-control" value="$vehicle->id" name="vehicleId" id="vehicleId" hidden>
                                     <input type="text" class="form-control" value="$vehicle->compartments" name="totalCompartments" id="totalCompartments" hidden>
                                    <table class="table table-default table-sm">
                                        <tr>
                                            <td><b>Activity</b></td>
                                            <td> $vehicle->task </td>
                                        </tr>
                                        <tr>
                                            <td><b>Vehicle Brand</b></td>
                                            <td>$vehicle->vehicle_brand</td>
                                        </tr>
                                        <tr>
                                            <td><b>Number Of Compartments</b></td>
                                            <td>$vehicle->compartments Compartments</td>
                                        </tr>
                                        <tr>
                                            <td><b>Hose Plate Number</b></td>
                                            <td>$vehicle->hose_plate_number</td>
                                        </tr>
                                        <tr>
                                            <td><b>trailer Plate Number</b></td>
                                            <td>$vehicle->trailer_plate_number</td>
                                        </tr>
                                        <tr>
                                            <td><b>Driver</b></td>
                                            <td>$vehicle->driver_name</td>
                                        </tr>
                                        <tr>
                                            <td><b>Driver License</b></td>
                                            <td>$vehicle->driver_license</td>
                                        </tr>
                                        <tr>
                                            <td><b>TIN Number</b></td>
                                            <td>$vehicle->tin_number</td>
                                        </tr>
                                         
        HTML;

            $chart = $tankCompartments == []   ? (object)['complete' => false] : $this->vtvLibrary->formatCompartmentData($tankCompartments, $availableCompartments);


            if (!$chart->complete && $isVehicleOk) {
                $html .=  <<<"HTML"
                                        <tr id="chartRow">
                                            <td><b>Chart</b></td>
                                           
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addChart">
                                                    <i class="far fa-plus"></i> Create Chart
                                                </button>
                                             
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
         HTML;
            } else if ($vehicle->visualInspection != 'Pass' && $vehicle->testing != 'Pass') {
                $html .=  <<<"HTML"
                <tr id="chartRow">
                    <td><b>Status</b></td>
                   
                    <td>
                       <p>Vehicle Failed Visual Inspection & Testing</p>
                       <a type="button" href="$rejectionNote" target="_blank" class="btn btn-primary btn-sm" >
                         <i class="far fa-download"></i> Rejection Note
                        </a>
                     
                    </td>
                </tr>
            </table>
        </div>
    </div>
    HTML;
            }
        } else {
            $html = '';
        }

        $noOfCompartments = '<option  disabled>--Select Compartment--</option>';

        $list = array_unique(array_map(fn($c) => preg_replace("/[^0-9]/", "", $c->compartment_number), $tankCompartments));

        for ($i = 1; $i <= $vehicle->compartments; $i++) {
            if (!in_array($i, $list)) {


                $noOfCompartments .=
                    <<<"HTML"
          <option  value='Compartment_$i'>Compartment No $i </option>
            }
          
        HTML;
            }
        }




        return $this->response->setJSON([
            'vehicleId' => $id,
            'tankCompartments' => $tankCompartments,
            'status' => 1,
            'vehicle' => $html,
            'noOfCompartments' => $noOfCompartments,
            'chart' => $tankCompartments == []  ? '' : $chart,
            'chartCount' => count((array)$filledCompartments),
            'data' => [
                'filledCompartments' => array_values($filledCompartments),
                'available' => $list,
            ],
            // 'data' => $availableCompartments,
            'token' => $this->token,
        ]);
    }




    //=================group similar compartments data====================
    public function groupCompartments($array, $key): array
    {
        $result = new stdClass();
        // $result = [];

        foreach ($array as $val) {
            if (isset($key, $val)) {
                $data = $val->$key;
                $result->$data[] = $val;
            } else {
                $data = [''];
                $result->$data[] = $val;
            }
        }
        // foreach ($array as $val) {
        //     if (isset($key, $val)) {
        //         $data = $val[$key];
        //         $result[$data][] = $val;
        //     } else {
        //         $data = [''];
        //         $result[$data][] = $val;
        //     }
        // }

        return (array)$result;
    }

    public function editChart()
    {
        try {
            $vehicleId = $this->getVariable('vehicleId');
            $compartmentNumber = $this->getVariable('compartmentNumber');
            $compartments = $this->VtcModel->getCompartmentData($compartmentNumber, $vehicleId);
            $template = [
                'table_open' => '<table  class="table  table-sm" style="width: 100%;">',
                'tbody_open' => '<tbody id="extraRows">',

            ];


            $chartTable = $this->table;
            $addBtn  = <<<HTML
                <button type="button" class="btn btn-primary btn-sm" onclick="addRow('extraRows')">
                  <i class="far fa-plus"></i>
                </button>
            HTML;
            $chartTable->setHeading('Litre', 'mm', "$addBtn");
            $chartTable->setTemplate($template);
            $tankTop =  $compartments[0]->tank_top;

            foreach ($compartments as $compartment) {
                $id = microtime();
                $removeBtn  = <<<HTML
                <button type="button" class="btn btn-dark btn-sm" onclick="deleteRow(this,'$compartment->id')">
                  <i class="far fa-times"></i>
                </button>
            HTML;
                $liters  = <<<HTML
                <input type="text" name="trailerId[]"  value="$vehicleId"  id="" class="form-control" readonly  hidden>
                <input type="text" name="id[]"  value="$compartment->id"  class="form-control" required hidden>
                <input type="number" name="litres[]"  max="10000000" value="$compartment->litres"  id="litre_$id" class="form-control litre" required>
             HTML;
                $millimeters  = <<<HTML
                <input type="number" name="millimeters[]"   value="$compartment->millimeters"  id="millimeters_$id" class="form-control millimeters" >
             HTML;

                $chartTable->addRow($liters, $millimeters, $removeBtn);
            }

            $statusCode = 200;
            $response = [
                'status' => 1,
                'tankTop' => $tankTop,
                'compartmentNo' => $compartments[0]->compartment_number,
                'vehicleId' => $vehicleId,
                'compartment' => $chartTable->generate(),
                'comp' => $compartment,
                'msg' => '',
                'token' => $this->token
            ];
        } catch (\Throwable $th) {
            $statusCode = 500;
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response)->setStatusCode($statusCode);
    }


    public function updateChart()
    {
        try {
            $trailerId = $this->getVariable('trailerId');
            $id = $this->getVariable('id');
            $compartmentNumber = $this->getVariable('compNumber');
            $totalCompartments = (int)$this->getVariable('totalCompartments');
            $tankTop = $this->getVariable('tankTop');
            $stampNumber = $this->getVariable('stampNumber');
            $millimeters = $this->getVariable('millimeters');
            $litres = $this->getVariable('litres');

            // $totalCompartments = $this->VtcModel->getVehicleDetails(['id' => $trailerId])->compartments;
            $count = count($litres);
            $data = [
                'id' => $id,
                'vehicle_id' => fillArray($count, $trailerId[0]),
                'compartment_number' => fillArray($count, $compartmentNumber),
                'tank_top' => fillArray($count, $tankTop),
                'stamp_number' => fillArray($count, $stampNumber),
                'unique_id' => fillArray($count, $this->uniqueId),
                'litres' => $litres,
                'millimeters' => $millimeters,

            ];

            $compData = multiDimensionArray($data);

            $oldData = (new ArrayLibrary($compData))->filter(fn($comp) => $comp['id'] != '')->get();
            $newData = (new ArrayLibrary($compData))->filter(fn($comp) => $comp['id'] == 'x')->map(function ($comp) {
                unset($comp['id']);
                return $comp;
            })->get();




            $this->VtcModel->updateCompartmentData($oldData);
            if (!empty($newData)) $this->VtcModel->addCompartmentData($newData);
            $tankCompartments = $this->VtcModel->getCompartments($trailerId[0]);

            $comps = new ArrayLibrary((array)$tankCompartments);
            $availableCompartments =  count(array_unique($comps->map(fn($c) => $c->compartment_number)->get()));


            $filledCompartments = $this->groupCompartments((array)$tankCompartments, 'compartment_number');

            return $this->response->setJSON([
                'status' => 1,
                // 'data' => $compData,
                'new' => $newData,
                'chart' => '',
                'msg' => 'Compartment Data Updated',
                'chart' =>  $tankCompartments == []   ? [] : $this->vtvLibrary->formatCompartmentData($tankCompartments, $availableCompartments),
                // 'filled' => count((array)$filledCompartments),
                // 'allFilled' => count((array)$filledCompartments) == $totalCompartments ? 1 : 0,
                'token' => $this->token
            ]);
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }


    //============Publish in transaction table================

    public function createChart()
    {
        try {
            $vehicleId = $this->getVariable('vehicleId');
            $compartmentNumber = $this->getVariable('compNumber');
            $totalCompartments = (int)$this->getVariable('totalCompartments');
            $tankTop = $this->getVariable('tankTop');
            $stampNumber = $this->getVariable('stampNumber');
            $millimeters = $this->getVariable('millimeters');
            $litres = $this->getVariable('litres');


            $tankTopArr = [];
            $stampArr = [];
            $vehicleIdz = [];
            $compartments = [];
            $userId = [];
            for ($i = 0; $i < count($litres); $i++) {
                array_push($tankTopArr, $tankTop);
                array_push($stampArr, $stampNumber);
                array_push($vehicleIdz, $vehicleId);
                array_push($compartments, $compartmentNumber);
                array_push($userId, $this->uniqueId);
            }

            $vehicle = $this->VtcModel->getVehicleDetails(['id' => $vehicleId]);
            $compartmentBlocks = $vehicle->compartments;

            $data = [
                'vehicle_id' => $vehicleIdz,
                'compartment_number' => $compartments,
                'tank_top' => $tankTopArr,
                'stamp_number' => $stampArr,
                'litres' => $litres,
                'millimeters' => $millimeters,
                'unique_id' => $userId,
            ];

            //preparing compartment data for batch insertion
            $compartment = [];
            foreach ($data as $key => $value) {
                for ($i = 0; $i < count($value); $i++) {
                    $compartment[$i][$key] = $value[$i];
                }
            }

            $tankComp = [];
            for ($i = 1; $i <= $totalCompartments; $i++) {
                array_push($tankComp, 'Compartment_' . $i);
            }




            $request = $this->VtcModel->addCompartmentData($compartment);
            $tankCompartments = $this->VtcModel->getCompartments($vehicleId);
            // $request = true;
            if ($request) {

                $centerName = centerName();

                $centerInitials = 'V:' . strtoupper(substr($centerName, 0, 3)) . '-';

                $currentDate = new DateTime();

                // Add 12 hours to the current date and time
                $currentDate->add(new DateInterval('PT10M'));

                // Format and display the updated date and time
                $downloadLimit = $currentDate->format('Y-m-d H:i:s');

                if (substr($compartmentNumber, 12) == $totalCompartments) {
                    // $centerName = centerName();

                    // $centerInitials = 'V:' . strtoupper(substr($centerName, 0, 3)) . '-';
                    $chart = $this->VtcModel->checkChartNumber();

                    $number = empty($chart) ? '100' : substr($chart->number, 6, -5) + 1;

                    $this->VtcModel->createChartNumber([
                        'vehicleId' => $vehicleId,
                        'number' => $centerInitials . $number . '-' . date('Y'),
                        'downloadLimit' => $downloadLimit
                    ]);
                }






                $compartmentsMenu = '<option  disabled>--Select Compartment--</option>';

                $list = array_unique(array_map(fn($c) => preg_replace("/[^0-9]/", "", $c->compartment_number), $tankCompartments));

                for ($i = 1; $i <= $vehicle->compartments; $i++) {
                    if (!in_array($i, $list)) {

                        $compartmentsMenu .=
                            <<<"HTML"
                            <option  value='Compartment_$i'>Compartment No $i </option>
                         HTML;
                    }
                }



                $data = new ArrayLibrary((array)$tankCompartments);
                $availableCompartments =  count(array_unique($data->map(fn($c) => $c->compartment_number)->get()));


                $filledCompartments = $this->groupCompartments((array)$tankCompartments, 'compartment_number');

                return $this->response->setJSON([
                    'status' => 1,
                    'msg' => 'Compartment Data Added',
                    'chart' =>  $tankCompartments == []   ? [] : $this->vtvLibrary->formatCompartmentData($tankCompartments, $availableCompartments),
                    'compartmentsMenu' => $compartmentsMenu,
                    'filled' => count((array)$filledCompartments),
                    'allFilled' => count((array)$filledCompartments) == $totalCompartments ? 1 : 0,
                    'token' => $this->token
                ]);
            }

            //001


            return $this->response->setJSON([
                'status' => 1,
                // 'msg' => 'Compartment Data Added',
                'data' => $compartment,
                'totalCompartments' => $tankComp,
                // 'noOfCompartments' => $noOfCompartments,
                'token' => $this->token
            ]);
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'token' => $this->token,

            ]);
        }
    }



    //=================Get fully calibrated tanks====================

    public function getCalibratedTanks()
    {
        try {
            $customerId =   $this->getVariable('customerId');
            $taskName =   $this->getVariable('taskName');
            // $params = [
            //     'hash' => $customerId,
            //     'task' => $taskName
            // ];
            $params = [
                'customerId' => $customerId,
                'activity' => $this->GfsCode,
                'collectionCenter' => $this->collectionCenter

            ];


            $ids = (new AppModel())->getItemIds($params);

            // return $this->response->setJSON([
            //     'status' => 0,
            //     'params' => $params,
            //     'data' => $ids,
            //     'token' => $this->token
            // ]);

            // exit;

            $requestData = $this->VtcModel->getAllUnpaidVehiclesTanks($params, $taskName);
            // }

            // $requestData = $this->VtcModel->getAllUnpaidVehicles($params, $instrumentIdArray);


            $request = array_filter($requestData, function ($truck) {
                if (
                    ($truck->task == 'Inspection' && $truck->visualInspection == 'Pass' && $truck->testing == 'Pass') ||
                    ($truck->visualInspection == 'Condemned' || $truck->testing == 'Condemned')
                ) {
                    return false; // Exclude the item from the filtered array
                }

                return true; // Include the item in the filtered array
            });



            // $keys = array_keys((array)$request[0]);




            // return $this->response->setJSON([
            //     'xx' => $request,
            //     'status' => 1,
            //     'htmlTable' =>'',
            //     'token' => $this->token,
            // ]);
            // exit;

            $vehicles = new ArrayLibrary($request);


            if ($taskName == 'Inspection') {
                $vehicleTanks = (new ArrayLibrary($request))
                    ->filter(fn($tank) => $tank->task == 'Inspection' && ($tank->visualInspection == 'Rejected' || $tank->testing == 'Rejected' || $tank->visualInspection == 'Adjusted' || $tank->testing == 'Adjusted'))
                    ->map(function ($vehicle) {
                        $id = $vehicle->id . randomString();
                        return <<<"HTML"
                    <tr>
                      
                       <td scope="row">
                           <input type="text" class="form-control" value="$vehicle->id" name="vehicleId[]" hidden >
                           <input type="text" class="form-control" value="$vehicle->hash" name="customerHash[]" hidden >
                           $vehicle->vehicle_brand
                       </td>
                       <td>$vehicle->hose_plate_number</td>
                       <td>$vehicle->trailer_plate_number</td>
                       <td>
                         <input type="text" class="form-control" value="" name="capacity[]" hidden >
                         0
                        </td>
                         
                         <td>Rejected</td>
                       <td>
                             
                       <input class="form-control vehicleAmount" id="$id" type="text"  value="" name="totalAmount[]" required  oninput="getItemAmount(this)"></td>
                           
                       </td>
                       <td>
                           
                           <button type="button" class="btn btn-success btn-xs" onclick="removeItem(this)">
                               <i class="far fa-minus-circle"></i>
                           </button>
                           <!-- <button type="button" class="btn btn-success btn-xs">
                               <i class="fal fa-file-chart-line"></i>
                           </button> -->
                       </td>
                   </tr>
                HTML;
                    })->get();
            } else {
                //this logic will work if the task selected is verification or reverification
                $vehicleTanks = $vehicles->map(function ($vehicle) {
                    //
                    $data = $vehicle;
                    $compartments = $this->VtcModel->getCompartments($vehicle->id);
                    $compartmentBlocks = new ArrayLibrary($compartments);
                    $data->filled = count(array_unique($compartmentBlocks->map(fn($c) => $c->compartment_number)->get()));
                    $data->compartmentNumber = $vehicle->compartments;
                    $data->compartmentData = $compartments;
                    return $data;
                })->filter(fn($v) => (!empty($v->compartmentData) || $v->skipChart == 1) && (($v->filled ==  $v->compartmentNumber) || $v->skipChart == 1) || (($v->visualInspection == 'Rejected' || $v->testing == 'Rejected') || ($v->visualInspection == 'Adjusted' || $v->testing == 'Adjusted') && ($v->visualInspection != 'Condemned' && $v->testing != 'Condemned')))->map(
                    function ($v) {
                        $billAmount = [];
                        $hasCompartments  = !empty($this->VtcModel->getCompartments($v->id)) ? true : false;
                        $capacity = $hasCompartments ? $this->vtvLibrary->formatCompartmentData($this->VtcModel->getCompartments($v->id), $v->compartments)->capacity : $v->capacity;
                        // $capacity =  $v->visualInspection == 'Rejected' || $v->testing == 'Rejected' ? 0 : $litres;

                        $passed = $v->visualInspection == 'Pass' && $v->testing == 'Pass' ? true : false;
                        $isRejected = $v->visualInspection == 'Rejected' || $v->testing == 'Rejected' ? true : false;
                        $isCondemned = $v->visualInspection == 'Condemned' || $v->testing == 'Condemned';
                        $isAdjusted = $v->visualInspection == 'Adjusted' || $v->testing == 'Adjusted';
                        $totalAmount = 0;

                        if ($passed) {
                            $sticksAmount = $v->include_sticks ? $v->compartments * 15000 : 0;
                            $totalAmount =  number_format($capacity * 15  + $sticksAmount + (float)$v->penaltyAmount ?? 0);

                            array_push($billAmount, $totalAmount);
                            $readonly = 'readonly';
                            $status = 'Pass';
                        } elseif ($isRejected) {
                            $totalAmount = '';
                            $readonly = '';
                            $status = 'Rejected';
                        } elseif ($isAdjusted) {
                            $totalAmount = '';
                            $readonly = '';
                            $status = 'Adjusted';
                        }

                        $capacityLabel = number_format($capacity);

                        $id = $v->id . randomString();
                        return <<<"HTML"
                         <tr>
                           
                            <td scope="row">
                                <input type="text" class="form-control" value="$v->id" name="vehicleId[]" hidden >
                                <input type="text" class="form-control" value="$v->hash" name="customerHash[]" hidden >
                                $v->vehicle_brand
                            </td>
                            <td>$v->hose_plate_number</td>
                            <td>$v->trailer_plate_number</td>
                            <td>
                              <input type="text" class="form-control" value="$capacity" name="capacity[]" hidden >  
                            $capacityLabel Litre</td>
                            <td>$status</td>
                            <td>
                                 
                                  <input class="form-control vehicleAmount" id="$id" type="text" $readonly value="$totalAmount" name="totalAmount[]" required  oninput="getItemAmount(this)"></td>
                              
                            </td>
                            <td>
                                
                                <button type="button" class="btn btn-dark btn-sm" onclick="removeItem(this)">
                                    <i class="far fa-minus-circle"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-success btn-xs">
                                    <i class="fal fa-file-chart-line"></i>
                                </button> -->
                            </td>
                            

                        </tr>
                     HTML;
                    }
                )->get();
            }


            $vehicleData = '';

            // if ($taskName == 'Inspection') {
            //     foreach ($inspectedTanks as $compData) {
            //         $vehicleData .= $compData;
            //     }
            // } else {

            // }

            foreach ($vehicleTanks as $compData) {
                $vehicleData .= $compData;
            }

            // $amount = $taskName != 'Inspection' ? (!empty($request) ? array_sum($withCompartments->billAmount) : 0) : 0;


            $readOnly = $taskName != 'Inspection' ? 'readonly' : '';
            $placeHolder = $taskName == 'Inspection' ? 'Enter Amount Figure Here' : '';
            $html = <<<"HTML"
             <table class="table table-sm mb-3">
                <thead>
                    <tr>
                        
                        <th>Brand</th>
                        <th>Hose Plate Number</th>
                        <th>Trailer Plate Number</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                     $vehicleData
    
                </tbody>
               
            </table>
            <div class="form-group">
                <label for="my-input">Total Amount</label>
                <input id="totalAmount" class="form-control" value="" type="text" name="billAmount" $readOnly placeholder="$placeHolder" >
            </div>
     HTML;



            $h5 = <<<HTML
          <h5 class="text-center">No Records Found</h5>    
     HTML;



            return $this->response->setJSON([
                'task' =>  $taskName,
                'vehicles' => !empty($request) ? 1 : 0,
                // 'request' => $requestData,
                'status' => 1,
                'htmlTable' => !empty($request) ? $html : $h5,
                'token' => $this->token,
                // 'inspectedTanks' =>   $inspectedTanks

            ]);
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'trace' =>  $th->getTrace(),
                'token' => $this->token,

            ]);
        }
    }

    public function renewChart($customerId, $chartNumber, $trailerNumber)
    {

        $data['page'] = [
            "title" => "Verification Chart Renew",
            "heading" => "Verification Chart Renew",
        ];
        $data['chart'] = $chartNumber;
        $data['customer'] = (new CustomerModel())->selectCustomer($customerId);


        // Printer($customerId);
        // exit;


        return view('Pages/Transactions/ChartRenew', $data);
    }

    public function downloadCalibrationChart($vehicleId)
    {



        //  return $this->response->setJSON([
        //    'vehicleId' => $vehicleId,
        //    //'data' => $item,
        //    'token' => $this->token
        //  ]);

        // exit;





        $tankCompartments = $this->VtcModel->getCompartments($vehicleId);

        $tankData = new ArrayLibrary((array)$tankCompartments);
        $availableCompartments =  count(array_unique($tankData->map(fn($c) => $c->compartment_number)->get()));
        $vehicle = (new VtcModel)->getVehicleDetails(['id' => $vehicleId]);
        $id = $vehicleId . '-' . randomString();
        $region = $this->collectionCenter;
        $customerId = $vehicle->hash;

        $customer = (new CustomerModel)->selectCustomer($vehicle->hash);
        $url = base_url("verifyCalibrationChart/$customerId/$id/$region");
        $qrData = [
            'activity' => setting('Gfs.vtv'),
            'vehicleId' => '',
        ];

        
        
        $officer = (new UsersModel())->where('unique_id',$tankCompartments[0]->unique_id)->first();
     
        $data['officerName'] = $officer->username;
      




        // Printer($this->vtvLibrary->formatCompartmentData($tankCompartments, $availableCompartments));
        // exit;

        $data['qrCode'] = QRCode($url);

        // Printer( $data['qrCode']);
        // exit;ƒ

        $orientation =   $availableCompartments >= 5 ? 'L' : 'P';
        $title = $customer->name . '-' . $vehicle->trailer_plate_number . '-' . microtime();
        $data['title'] = $title;
        $data['center'] = $this->CommonTasks->getCenterAddress();
        $data['chart'] = $tankCompartments == []   ? [] : $this->vtvLibrary->formatCompartmentData($tankCompartments, $availableCompartments);

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: $orientation, view: 'Pages/Vtc/CalibrationChart', data: $data, title: $title);
    }








    public function publishVtcData()
    {
        try {
            if ($this->request->getMethod() == 'POST') {
                $billAmount =   (int) str_replace(',', '', $this->getVariable('billAmount'));
                $taskName =   $this->getVariable('task');
                $vehicleIdz =   $this->getVariable('vehicleId');
                $hash = $this->getVariable('customerHash');
                $total = array_map(fn($t) => (int)str_replace(',', '', $t), $this->getVariable('totalAmount'));
                $capacity = $this->getVariable('capacity');
                $SwiftCode = $this->getVariable('SwiftCode');
                $method = $this->getVariable('method');
                $currentDate = date("Y-m-d\TH:i:s");
                $expiryDate  = $this->getVariable('BillExprDt');
                $xpDate = $expiryDate . '23:59:59';

                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+7 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));

                $billId = 'WMA' . randomString();
                $requestId = $billId; // 'WMAREQ' . numString(10);



                $newVehicleId = array_map(
                    fn($id) =>
                    $id . '_' . randomString(20),
                    $vehicleIdz
                );



                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => $total,
                //     'token' => $this->token,
                //     'vehicleIdz' =>  $vehicleIdz,
                // ]);

                // exit;


                $vehicles = array_map(function ($id) use ($newVehicleId, $total) {


                    $vehicleId = vehicleId($id);
                    $vehicle = $this->VtcModel->findVehicle($vehicleId);
                    return  [

                        'id' => $id,
                        'hash' => $vehicle->hash,
                        'original_id' => $vehicle->data_id,
                        'task' => $vehicle->task,
                        'visualInspection' =>   $vehicle->visualInspection,
                        'testing' =>   $vehicle->testing,
                        'gfCode' => $this->GfsCode,
                        'registration_date' => $vehicle->registration_date,
                        "next_calibration" => $vehicle->next_calibration,
                        'tin_number' => $vehicle->tin_number,
                        'region' => $this->user->collection_center,
                        'driver_name' => $vehicle->driver_name,
                        'driver_license' => $vehicle->driver_license,
                        'trailer_plate_number' => $vehicle->trailer_plate_number,
                        'trailer_plate_number' => $vehicle->trailer_plate_number,
                        'status' => $vehicle->status,
                        'include_sticks' => $vehicle->include_sticks,
                        'remark' => $vehicle->remark,
                        'hasPenalty' => $vehicle->hasPenalty,
                        'penaltyAmount' => $vehicle->penaltyAmount,
                        'repairDeadline' =>   $vehicle->repairDeadline,
                        'latitude' => $vehicle->latitude,
                        'longitude' => $vehicle->longitude,
                        'vehicle_brand' => $vehicle->vehicle_brand,
                        'unique_id' => $this->uniqueId,
                    ];
                }, $newVehicleId);


                //=====================================



                for ($i = 0; $i < count($newVehicleId); $i++) {
                    $vehicles[$i]['amount'] = str_replace(',', '', $total[$i]);
                    $vehicles[$i]['capacity'] = $capacity[$i];
                }

                $task = $vehicles[0]['task'];


                $customer = $this->customersModel->selectCustomer($hash[0]);




                $vehicleCount = count($vehicles);

                $itemsArray = array_map(function ($vehicle) use ($billId, $taskName, $billAmount, $vehicleCount, $requestId) {
                    $itemName = $vehicle['vehicle_brand'] . ' ' . $vehicle['trailer_plate_number'] . ' ' . $vehicle['capacity'] . ' Liters';
                    $billItemAmt = $taskName != 'Inspection' ? (float)str_replace(',', '', $vehicle['amount']) : ($billAmount / $vehicleCount);
                    $gfsCode = $taskName == 'Inspection' ? $this->penaltyGfsCode : $this->GfsCode;
                    $theItems = [];
                    $itemAmount = $billItemAmt - (float)$vehicle['penaltyAmount'];
                    $items = [
                        'BillItemRef' => $vehicle['id'],
                        'UseItemRefOnPay' => 'N',
                        'BillItemAmt' => $itemAmount,
                        'BillItemEqvAmt' => $itemAmount,
                        'BillItemMiscAmt' => 0.00,
                        'GfsCode' => $gfsCode,
                        'BillId' => $billId,
                        'RequestId' => $billId,
                        'ItemName' => $itemName,
                        'PayerId' => $vehicle['hash'],
                        'UserId' => $vehicle['unique_id'],
                        'Status' => $vehicle['status'],
                        'Task' => $vehicle['task'],
                        'ItemQuantity' => 1,
                        'center' => $this->collectionCenter,


                    ];
                    $theItems[] = $items;

                    // Check if there is a penalty

                    // Check if there is a penalty
                    if ($vehicle['hasPenalty'] == 1) {
                        $penaltyItem = [
                            'BillItemRef' => $vehicle['id'],
                            'UseItemRefOnPay' => 'N',
                            'BillItemAmt' =>  (float)$vehicle['penaltyAmount'],
                            'BillItemEqvAmt' => (float)$vehicle['penaltyAmount'],
                            'BillItemMiscAmt' => 0.00,
                            'GfsCode' => $this->penaltyGfsCode,
                            'BillId' => $billId,
                            'ItemName' => 'Fine For ' . $vehicle['vehicle_brand'] . ' ' . $vehicle['trailer_plate_number'],
                            'PayerId' => $vehicle['hash'],
                            'UserId' => $vehicle['unique_id'],
                            'Status' => 'Rejected',
                            'Task' => $vehicle['task'],
                            'ItemQuantity' => 1,
                            'center' => $this->collectionCenter,
                        ];


                        $theItems[] = $penaltyItem;
                    }

                    return $theItems;
                }, $vehicles);


                $itemsArray = array_merge(...$itemsArray);

                //=================data for bill submission====================
                $billedAmount = $task != 'Inspection' ? str_replace(',', '', array_sum($total)) : $billAmount;
                $centerDetails = wmaCenter($this->collectionCenter);
                $collectionCenterCode =  $centerDetails->collectionCenterCode; //'CC1015000199419';
                $groupBillId = 'GRP' . numString(10);
                $billDetailsArray = [
                    'BillTyp' => 1,
                    'isTrBill' => 'No',
                    'RequestId' => $requestId,
                    'CollCentCode' =>  $collectionCenterCode,
                    'CustId' => numString(5),
                    'CustIdTyp' =>  5,
                    'CustTin' => '',
                    'GrpBillId' => $groupBillId,
                    'BillId' => $billId,
                    'Activity' => $this->GfsCode,
                    'BillRef' => numString(10),
                    'BillAmt' => (float)$billAmount,
                    'BillAmtWords' => toWords($billAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' =>  $BillExprDt,
                    'extendedExpiryDate' => (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s'),
                    'PyrId' =>  $customer->hash,
                    'PyrName' =>  $customer->name,
                    'BillDesc' =>  'Vehicle Tank Verification',
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>   $this->getUser()->name,
                    'CollectionCenter' =>   $this->collectionCenter,
                    'BillApprBy' =>   'WMAHQ',
                    'PyrCellNum' =>  $customer->phone_number,
                    'PyrEmail' =>   $customer->email,
                    'Ccy' =>  'TZS',
                    'BillEqvAmt' => (float)$billAmount,
                    'RemFlag' =>  $this->getVariable('RemFlag') == "on" ? 'true' : 'false',
                    'BillPayOpt' =>  (int)$this->getVariable('BillPayOpt'),
                    'method' =>  $method,
                    'UserId' =>  $this->uniqueId,
                    'Task' => $task,
                    'SwiftCode' =>  $SwiftCode != '' ? $SwiftCode : '',

                ];


                $itemsBlock = arrayExcept($itemsArray, ['ItemName', 'BillId', 'RequestId', 'UserId', 'PayerId', 'Status', 'Task']);
                // $xml = Array2XML::createXML('BillItems', ['BillItem' => $itemsBlock])->saveXML();


           



                // return  $this->response->setJSON([
                //     'status' => 0,
                //     // 'billAmount' => $billAmount,
                //     // 'bill' => $billDetailsArray,
                //     'items' => $itemsArray,
                //     'token' => $this->token,
                //     'Trucks' => $vehicles,
                //     // 'total' => $total,
                // ]);

                // exit;



                //this method processes the data from vtv module and generate a control number
                $activityBill = new ActivityBillProcessing();

                $response = $activityBill->processBill($billDetailsArray, $itemsArray, $this->getUser()->name);
                //creating an array of items to use in sms
                $trucks = (new ArrayLibrary($itemsArray))->map(fn($v) =>  $v['ItemName'])->get();
                if ($response->status == 1) {
                    //control number generated
                    $cn = $response->controlNumber;
                    //sticker_number
                    $stickerLib = new StickerLibrary();

                    $updatedItems = array_map(fn($item) => [
                        'id' => $item['BillItemRef'],
                        'sticker_number' => $item['StickerNumber'],
                    ], $stickerLib->attachSticker($itemsArray, $cn));


                    $updatedVehicles = array_map(function ($item) use ($updatedItems) {
                        $id = $item['id'];
                        $stickerNumber = array_column($updatedItems, 'sticker_number', 'id')[$id] ?? null;
                        $item['sticker_number'] = $stickerNumber;
                        return $item;
                    }, $vehicles);



                    $request1 = $this->VtcModel->registerVehicleTankRecord($updatedVehicles);



                    //sms params to be sent via sms service
                    $textParams = (object)[
                        'payer' => $customer->name,
                        'center' => wmaCenter($this->collectionCenter)->centerName,
                        'amount' => $billAmount,
                        'items' => (string)implode(',', $trucks),
                        'expiryDate' => $expiryDate,
                        'controlNumber' => $response->controlNumber,

                    ];

                    //creating associative array of chart data with control No for payment
                    $chartData = array_map(function ($id) use ($cn) {
                        $vehicleId = vehicleId($id);
                        //get single chart via vehicleId and map the data
                        $chart = $this->VtcModel->getChartIfo($vehicleId);
                        return  [
                            'idNo' => $chart->idNo ?? '',
                            'number' => $chart->number ?? '',
                            'controlNumber' => $cn ?? '',

                        ];
                    }, $newVehicleId);



                    if ($request1) {
                        //send sms to client when request is completed
                        $this->sms->sendSms(recipient: $customer->phone_number, message: billTextTemplate($textParams));
                        //dispose/delete all vehicle ids after they have been processed in bill request
                        $this->appModel->disposeItems($vehicleIdz);





                        // ================certificates=================================
                        $certificateData = (object)[

                            'customer' => $customer->name,
                            'activity' => json_encode($this->GfsCode),
                            'mobile' => $customer->phone_number,
                            'address' => $customer->postal_address,
                            'items' =>  json_encode($trucks),
                            'controlNumber' =>  $cn,

                        ];
                        //adding certificate data
                        (new CertificateLibrary())->createCertificateData($certificateData);







                        //update each chart with data contain control no
                        (new VtcModel)->updateMultipleChartIfo($chartData);

                        return $this->response->setJSON([
                            'status' => 1,
                            'TrxStsCode' => $response->TrxStsCode,
                            'msg' => $response->msg,
                            'bill' => $response->bill,
                            'heading' => $response->heading,
                            'qrCodeObject' => $response->qrCodeObject,
                            'token' => $this->token,
                            'ITEMS' => $itemsBlock
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'TrxStsCode' => '',
                        'msg' => !empty($response->TrxStsCode)  ? tnxCode($response->TrxStsCode) : $response->msg,
                        'token' => $this->token,
                        'billId' => $billId,
                        'ITEMS' => $itemsBlock
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token,
                //'billId' => $billId,
            ];
        }
        return $this->response->setJSON($response)->setStatusCode(500);
    }

    public function getUser(): object
    {
        $user = $this->profileModel->getLoggedUserData($this->uniqueId);
        return (object)[
            'name' => $user->first_name . ' ' . $user->last_name,
            'collectionCenter' => $user->collection_center

        ];
    }

    public  function getStampNumber($month)
    {
        $letters = range('A', 'L');

        if ($month >= 1 && $month <= 12) {
            return $letters[$month - 1] . ':' . date('Y');
        }
    }
    public function addVtc()
    {



        $data = [];


        $data['page'] = [
            "title" => "Vehicle Tank Verification",
            "heading" => "Vehicle Tank Verification",
        ];




        $data['uniqueId'] = $this->uniqueId;



        $data['user'] = $this->user;


        $data['stampNumber'] = $this->getStampNumber(date('m'));

        return view('Pages/Vtc/addVtc', $data);
    }

    public function editVtc()
    {
        $vehicleId =   $this->getVariable('id');
        $request = $this->VtcModel->getVehicle($vehicleId);
        if ($request) {
            return  $this->response->setJSON([
                'status' => 1,
                'data' => $request,
                'token' => $this->token,
            ]);
        } else {
            return  $this->response->setJSON([
                'status' => 1,
                'msg' => 'empty',
                'token' => $this->token,
            ]);
        }
    }

    public function listRegisteredVtc($collectionCenter)
    {


        try {

            $data['page'] = [
                "title" => "Registered Vehicle Tanks",
                "heading" => "Registered Vehicle Tanks",
            ];

            $table = 'calibrated_tanks';

            if ($this->request->getMethod() == 'POST') {

                $year = $this->getVariable('years');
                $years = explode('_', $year);
                $queryParams = [

                    // $table . '.unique_id' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                    $table . '.created_at>=' =>  "$years[0]-07-01",
                    $table . '.created_at<=' =>  "$years[1]-06-30",
                    $table . '.region' => $collectionCenter == 'all' ? '' :  $collectionCenter,
                    'wma_bill.IsCancelled' => 'No',
                    'wma_bill.PaymentStatus' => 'Paid',
                ];

                $params = array_filter($queryParams, fn($param) => $param !== '' || $param != null);
                $data['vtvResults'] = $this->VtcModel->verifiedVtv($params);
                $data['data'] = $params;
                $data['year'] = str_replace('_', '/', $year);
                return view('Pages/Vtc/listVtc', $data);
            }



            $queryParams = [

                // $table . '.unique_id' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                $table . '.created_at>=' =>  financialYear()->startDate,
                $table . '.created_at<=' =>  financialYear()->endDate,
                $table . '.region' => $collectionCenter == 'all' ? '' :  $collectionCenter,
                // 'wma_bill.IsCancelled' => 'No',
                //'wma_bill.PaymentStatus' => 'Paid',
            ];

            $params = array_filter($queryParams, fn($param) => $param !== '' || $param != null);

            $params['PayCntrNum !='] = '';
            $data['vtvResults'] = $this->VtcModel->verifiedVtv($params);

            // printer($queryParams);
            // printer($data['vtvResults']);

            $data['year'] = date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
            return view('Pages/Vtc/listVtc', $data);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    public function updateVehicleTank()
    {
        $amount =   $this->getVariable('tankCapacity') * 15;
        // $registrationDate =    $this->getVariable('createdAt');
        $id =   $this->getVariable('theId');
        $vehicleTank = [

            'task' =>   $this->getVariable('task'),
            //      'registration_date' =>   $this->getVariable('createdAt'),
            //      "next_calibration" => $this->CommonTasks->nextYear($registrationDate),
            'tin_number' =>   $this->getVariable('tinNumber'),

            'driver_name' =>   $this->getVariable('driverName'),
            'driver_license' =>   $this->getVariable('driverLicense'),
            'vehicle_brand' =>   $this->getVariable('vehicleBrand'),
            'plate_number' =>   $this->getVariable('licensePlate'),
            'capacity' =>   $this->getVariable('tankCapacity'),
            'status' =>   $this->getVariable('status'),
            'sticker_number' =>   $this->getVariable('stickerNumber'),
            'amount' => $amount,
            //      'other_charges' =>   $this->getVariable('charges'),
            'remark' =>   $this->getVariable('remark'),
            //      'unique_id' => $this->uniqueId,

        ];

        // echo json_encode($vehicleTank);
        //  exit;
        $request = $this->VtcModel->updateVehicleTank($vehicleTank, $id);

        if ($request) {
            return  $this->response->setJSON([
                'status' => 1,
                'msg' => 'Vehicle Updated',
                'token' => $this->token,
            ]);
        } else {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' => 'Error',
                'token' => $this->token,
            ]);
        }
    }

    //calibration chart

    public function vehicleCalibrationChart()
    {

        $data['page'] = [
            "title" => "Verification Chart",
            "heading" => "Verification Chart",
        ];
        $data['stampNumber'] = $this->getStampNumber(date('m'));
        return view('Pages/Vtc/VehicleCalibrationChart', $data);
    }

    public function searchVehicleTank()
    {
        try {
            $trailerPlateNumber =   $this->getVariable('trailerPlateNumber');

            $params = [
                'trailer_plate_number' => $trailerPlateNumber,
            ];


            // $vehicle = $this->VtcModel->findVehicleTank($params);

            $vehicle = $this->VtcModel->getVehicleDetails($params);

            // return  $this->response->setJSON([
            //             'status' => 1,
            //             'list' => $vehicle,
            //             'token' => $this->token,
            //         ]);

            return $this->vehicleDetailsProcessing($vehicle);


            // if ($vehicle) {
            //     $list = <<<HTML
            //     <li class="list-group-item p-2 m-0" onclick="getVehicleDetails('$vehicle->id')" style="cursor:pointer">
            //       <div>
            //         <b>Owner:</b> $vehicle->name
            //         <br>
            //         <b>Vehicle Brand :</b> $vehicle->vehicle_brand
            //         <br>
            //         <b>Hose Plate Number :</b> $vehicle->hose_plate_number
            //         <br>
            //         <b>Trailer Plate Number :</b> $vehicle->trailer_plate_number
            //         <br>
            //         <b>Capacity :</b> $vehicle->capacity Liters
            //         <br>
            //       </div>

            //      </li> 
            // HTML;
            //     return  $this->response->setJSON([
            //         'status' => 1,
            //         'list' => $list,
            //         'token' => $this->token,
            //     ]);
            // } else {
            //     return  $this->response->setJSON([
            //         'status' => 1,
            //         'msg' => 'empty',
            //         'list' => '<h6>No Match Found, Try Again !</h6>',
            //         'token' => $this->token,
            //     ]);
            // }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response)->setStatusCode(500);
    }
}
