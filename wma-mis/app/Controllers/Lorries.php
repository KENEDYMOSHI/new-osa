<?php

namespace App\Controllers;

use DateTime;
use LSS\Array2XML;
use App\Models\AppModel;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\SmsLibrary;
use App\Models\CustomerModel;
use App\Libraries\ArrayLibrary;
use App\Libraries\StickerLibrary;
use App\Libraries\CertificateLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Libraries\ActivityBillProcessing;
use App\Models\CertificateModel;

//use \CodeIgniter\Models\lorryModel;

class Lorries extends BaseController
{
    protected $uniqueId;
    protected $managerId;


    protected $lorryModel;
    protected $session;
    protected $profileModel;
    protected $CommonTasks;
    protected $customersModel;
    protected $token;
    protected $GfsCode;
    protected $penaltyGfsCode;
    protected $collectionCenter;
    protected $user;
    protected $sms;


    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->GfsCode = setting('Gfs.sbl');
        $this->penaltyGfsCode = setting('Gfs.fine');
        $this->lorryModel = new LorriesModel();
        $this->profileModel = new ProfileModel();
        $this->customersModel = new CustomerModel();
        $this->session = session();
        $this->token = csrf_hash();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;

        $this->CommonTasks = new CommonTasksLibrary();
        $this->user = auth()->user();
        $this->sms = new SmsLibrary();
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function searchSbl()
    {
        $hash = $this->getVariable('hash');
        $plateNumber = $this->getVariable('licensePlate');
        $request = $this->lorryModel->findMatch($hash, $plateNumber);
        if ($request) {
            return $this->response->setJSON([
                'status' => 1,
                'data' => $request,
                'token' => $this->token
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => '',
                'token' => $this->token
            ]);
        }
    }

    public function deleteLorry()
    {
        try {
            $vehicleId = $this->getVariable('vehicleId');

            $request = $this->lorryModel->deleteLorry($vehicleId);
            if ($request) {
                $statusCode = 200;
                $response = [
                    'status' => 1,
                    'msg' => 'Record Deleted Successfully',
                    'token' => $this->token
                ];
            }
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

    // ================Adding customer lorry information to database ==============

    public function registerLorry()
    {

        //=================Checking the last id its available====================

        try {
            if ($this->request->getMethod() == 'POST') {

                //=================Checking the last Id before incrementing it====================
                $IdValue = $this->lorryModel->checkLastId();

                foreach ($IdValue as $theTd) {
                    $lastId = $theTd->id;
                }

                if (count($IdValue) < 1) {
                    $instrumentId = 'SBL1';
                } else {
                    $instrumentId = substr($lastId, 3);
                    $instrumentId = intval($instrumentId);
                    $instrumentId = 'SBL' . ($instrumentId + 1);
                }


                $trailerPlate = $this->getVariable('trailerPlate');




                $width = $this->getVariable('width');
                $height = $this->getVariable('height');
                $depth = $this->getVariable('depth');

                $trailerWidth = $this->getVariable('trailerWidth');
                $trailerHeight = $this->getVariable('trailerHeight');
                $trailerDepth = $this->getVariable('trailerDepth');

                $trailerCapacity = $this->getVariable('trailerCapacity');

                $mainCapacity = $this->getVariable('lorryCapacity');
                $totalCapacity =  !empty($trailerCapacity) ? array_sum($trailerCapacity) + $mainCapacity : $mainCapacity;

                $visualInspection = $this->getVariable('visualInspection');
                $testing = $this->getVariable('testing');
                $penaltyAmount = (float)str_replace(',', '', $this->getVariable('penaltyAmount'));
                $amount = $visualInspection == 'Pass' && $testing == 'Pass' ? ($totalCapacity * 15000) + $penaltyAmount : $penaltyAmount ?? 0;
                $hash = $this->getVariable('sblCustomerHash');
                $date = date('Y-m-d');
                $lorryDetails = [
                    'id' => $instrumentId,
                    'hash' => $hash,
                    'task' => $this->getVariable('task'),
                    'visualInspection' => $visualInspection,
                    'testing' =>   $testing,
                    'gfCode' => $this->GfsCode,
                    'registration_date' => dateFormatter($date),
                    "next_calibration" =>  date('Y-m-d', strtotime(date('Y-m-d') . ' +1 year')),
                    'region' => $this->user->collection_center,
                    'tin_number' => $this->getVariable('tinNumber'),
                    'driver_name' => $this->getVariable('driverName'),
                    'driver_license' => $this->getVariable('driverLicense'),
                    'type' => $this->getVariable('type'),
                    'model' => $this->getVariable('model'),
                    'vehicle_brand' => $this->getVariable('vehicleBrand'),
                    'plate_number' => $this->getVariable('plateNumber'),
                    'width' => $width,
                    'height' => $height,
                    'depth' => $depth,
                    'capacity' => $totalCapacity,
                    'mainCapacity' => $mainCapacity,
                    'amount' => $amount,
                    'hasPenalty' => $this->getVariable('hasPenalty') == "on" ? 1 : 0,
                    'penaltyAmount' => $penaltyAmount,
                    'remark' => $this->getVariable('remark'),
                    'status' =>  $visualInspection == 'Pass' && $testing == 'Pass' ? 'Pass' : $visualInspection,
                    'repairDeadline' => $this->getVariable('repairDeadline'),
                    'latitude' => $this->getVariable('latitude'),
                    'longitude' => $this->getVariable('longitude'),
                    'unique_id' => $this->uniqueId,

                ];



                // return  $this->response->setJSON([
                //     'data' => $lorryDetails,
                //     'trailerDetails' => $trailerDetails ?? '',
                //     'token' => $this->token,
                // ]);
                // exit;


                $request = $this->lorryModel->registerLorry($lorryDetails);

                if ($request) {
                    (new AppModel)->createTempId([
                        'itemId' => $instrumentId,
                        'customerId' => $hash,
                        'activity' => $this->GfsCode,
                        'collectionCenter' => $this->collectionCenter
                    ]);
                    if (!empty($trailerCapacity && $trailerPlate)) {
                        $trailerDetails = multiDimensionArray([
                            'vehicleId' => fillArray(count($trailerPlate), $instrumentId),
                            'trailerWidth' => $trailerWidth,
                            'trailerHeight' => $trailerHeight,
                            'trailerDepth' => $trailerDepth,
                            'trailerPlate' => $trailerPlate,
                            'trailerCapacity' => $trailerCapacity,
                        ]);

                        $this->lorryModel->registerTrailers($trailerDetails);
                    }
                    return $this->response->setJSON([
                        'status' => 1,
                        'msg' => 'Lorry Added Successfully',
                        'token' => $this->token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Something Went Wrong',
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'token' => $this->token,

            ]);
        }
    }

    //=================check if customer has any unpaid Lorry====================
    public function getUnpaidLorries()
    {
        try {
            if ($this->request->getMethod() == 'POST') {
                $hashValue = $this->getVariable('hashString');
                $taskName = $this->getVariable('taskName');

                $params = [
                    'customerId' => $hashValue,
                    'activity' => $this->GfsCode,
                    'collectionCenter' => $this->collectionCenter
                ];


                $lories = $this->lorryModel->getAllUnpaidLorries($params, $taskName);


                $request = array_filter($lories, function ($truck) {
                    if (
                        ($truck->task == 'Inspection'  && $truck->visualInspection == 'Pass' && $truck->testing == 'Pass') ||
                        ($truck->visualInspection == 'Condemned' || $truck->testing == 'Condemned')
                    ) {
                        return false; // Exclude the item from the filtered array
                    }

                    return true; // Include the item in the filtered array
                });

                $total = 0;
                $billAmount = 0;
                $vehicle = (new ArrayLibrary($request))->map(function ($lorry) use ($total) {
                    $amount  = $lorry->task  == 'Inspection' || (($lorry->task  == 'Verification' || $lorry->task  == 'Reverification') && ($lorry->visualInspection == 'Rejected' || $lorry->testing == 'Rejected')) ? 0 : $lorry->amount;
                    $total += $amount;
                    $id = $lorry->id . '_' . randomString();




                    $id = $lorry->id . '_' . randomString();

                    $isRejected = ($lorry->visualInspection == 'Rejected' || $lorry->testing == 'Rejected') ? true : false;
                    $isCondemned = $lorry->visualInspection == 'Condemned' || $lorry->testing == 'Condemned' ? true : false;



                    if (!$isRejected  && ($lorry->task == 'Verification' || $lorry->task == 'Reverification')) {
                        $amount  =  number_format($lorry->amount);
                        $readonly = 'readonly';
                        $status = 'Pass';
                        $rejectionNote =  '';
                    } elseif ($isRejected && ($lorry->task == 'Verification' || $lorry->task == 'Reverification')) {
                        $amount = '';
                        $readonly = '';
                        $status = 'Rejected';



                        $customer = (new CustomerModel)->selectCustomer($lorry->hash);
                        $activity = 'Sand And Ballast Lorries Verification';
                        $instrument = $lorry->vehicle_brand . ' ' . $lorry->plate_number;
                        $address =  $customer->postal_address == '' ? 'P.O Box' : $customer->postal_address;

                        $deadline = !$lorry->repairDeadline ? 'None' : $lorry->repairDeadline;
                        $link = "rejectionNote/$customer->name/$address/$deadline/$activity/$instrument";


                        $rejectionNote =  <<<HTML
                        <a  data-toggle="tooltip" data-placement="top" title="Rejection Note" href="$link" target="_blank" class="btn btn-primary btn-sm" ><i class="far fa-download"></i></a> 
                        HTML;
                    } else {
                        $amount = '';
                        $readonly = '';
                        $status = 'Rejected';
                        $rejectionNote =  '';
                    }

                    // $amountLabel = number_format($totalAmount);


                    return <<<"HTML"
                <tr id="$lorry->id">
                    <td>
                        $lorry->vehicle_brand
                        <input type="text" name="vehicleId[]" value="$lorry->id" class="form-control" hidden>
                       
                    </td>
                    <td>$lorry->plate_number</td>
                    <td>$lorry->capacity m<sup>3</sup></td>
                    <td>$status</sup></td>
                    <td><input class="form-control lorryAmount" id="$id" type="text" name="itemAmount[]" required $readonly  oninput="getItemAmount(this)" value="$amount"></td>
                    <td>
                    <button data-toggle="tooltip" data-placement="top" title="Remove Lorry" type="button" class="btn btn-dark btn-sm" onclick="clearRow('$lorry->id')"><i class="far fa-ban"  ></i></button>     
                    <button data-toggle="tooltip" data-placement="top" title="Edit Lorry" type="button" class="btn btn-success btn-sm" onclick="editLorry('$lorry->id')"><i class="far fa-pen"  ></i></button>     
                    
                    <button data-toggle="tooltip" data-placement="top" title="Delete Lorry" type="button" class="btn btn-danger btn-sm" onclick="deleteLorry('$lorry->id')">
                    <i class="far fa-trash-alt pulse"></i>
                    </button>  
                    $rejectionNote   
                    </td>
                </tr>
                HTML;
                })->get();


                //calculate Total amount for each lorry
                $billAmount = (new ArrayLibrary($lories))
                    ->filter(fn ($tank) => ($tank->task  == 'Verification' || $tank->task  == 'Reverification') && ($tank->visualInspection !== 'Condemned' && $tank->testing !== 'Condemned'))
                    ->map(function ($tank) {
                        $isRejected = $tank->visualInspection == 'Rejected' || $tank->testing == 'Rejected' ? true : false;

                        if (!$isRejected && ($tank->task  == 'Verification' || $tank->task  == 'Reverification')) {
                            return  $tank->amount;
                        }
                    })->reduce(fn ($a, $b) => $a + $b)->get();




                $lorryData = '';
                foreach ($vehicle as $lorryItem) {
                    $lorryData .= $lorryItem;
                }



                $html = <<<"HTML"
                   <table class="table table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Vehicle Brand</th>
                                <th>Plate Number</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        $lorryData
                        </tbody>
                    </table>
                 HTML;

                $h5 = <<<HTML
          <h5 class="text-center">No Records Found</h5>    
        HTML;
                return $this->response->setJSON([
                    'lorries' => !empty($lories) ? $html : $h5,
                    'vehicles' => !empty($lories) ? 1 : 0,
                    'billedAmount' => number_format($billAmount),
                    'token' => $this->token,
                    'AMT' => $billAmount,

                ]);

                exit;

                if ($request) {
                    return $this->response->setJSON([
                        'status' => 1,
                        'paidVehiclesId' => $request,
                        'token' => $this->token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 1,
                        'data' => '',
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'token' => $this->token,

            ]);
        }
    }

    //=================publish lorry info to transaction table====================
    public function publishLorryData()
    {
        try {
            if ($this->request->getMethod() == 'POST') {
                $vehicleIds = $this->getVariable('vehicleId');
                $itemAmount = $this->getVariable('itemAmount');

                $customerId = $this->getVariable('customerId');
                $billAmount = (float)str_replace(',', '', $this->getVariable('billedAmount'));
                $SwiftCode = $this->getVariable('SwiftCode');
                $method = $this->getVariable('method');
                $billId = 'WMA'.randomString();
                $taskName =   $this->getVariable('taskName');
                // $userId = $this->uniqueId;

                // $transaction = [];

                $expiryDate = $this->getVariable('BillExprDt');
                $currentDate = date("Y-m-d\TH:i:s");
                $xpDate = $expiryDate . '23:59:59';
                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+7 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));

                $paymentOption = (int)$this->getVariable('BillPayOpt');

                $requestId = $billId;// 'WMAREQ'.numString(10);





                $customer = $this->customersModel->selectCustomer($customerId);
                $vehicles = array_map(function ($id, $amount) use ($vehicleIds) {
                    $vehicle = $this->lorryModel->findVehicle($id);
                    return [

                        'id' => $id,
                        'hash' => $vehicle->hash,
                        'original_id' => $vehicle->data_id,
                        'task' => $vehicle->task,
                        'visualInspection' =>   $vehicle->visualInspection,
                        'testing' =>   $vehicle->testing,
                        'gfCode' => $this->GfsCode,
                        'registration_date' => date('Y-m-d'),
                        "next_calibration" =>   date('Y-m-d', strtotime(date('Y-m-d') . ' +1 year')),
                        'tin_number' => $vehicle->tin_number,
                        'region' => $this->user->collection_center,

                        'driver_name' => $vehicle->driver_name,
                        'driver_license' => $vehicle->driver_license,
                        'vehicle_brand' => $vehicle->vehicle_brand,
                        'plate_number' => $vehicle->plate_number,
                        'type' => $vehicle->type,
                        'model' => $vehicle->model,
                        'width' => $vehicle->width,
                        'height' => $vehicle->height,
                        'depth' => $vehicle->depth,
                        'capacity' => $vehicle->capacity,
                        'mainCapacity' => $vehicle->mainCapacity,
                        // 'amount' => (float)$vehicle->amount,
                        'amount' => str_replace(',', '', $amount),

                        'remark' => $vehicle->remark,
                        'hasPenalty' => $vehicle->hasPenalty,
                        'penaltyAmount' => $vehicle->penaltyAmount,
                        'status' => $vehicle->status,
                        'unique_id' => $this->uniqueId,
                    ];
                }, $vehicleIds, $itemAmount);

                $task = $vehicles[0]['task'];
                $vehicleCount = count($vehicles);
                $count = count($vehicles);

                $itemsArray = array_map(function ($vehicle) use ($billId, $taskName, $billAmount, $vehicleCount,$count,$requestId) {
                    $itemName = $vehicle['vehicle_brand'] . ' ' . $vehicle['plate_number'] . ' ' . $vehicle['capacity'];
                    $billItemAmt = $taskName != 'Inspection' ? (float)str_replace(',', '', $vehicle['amount']) : ($billAmount / $vehicleCount);
                    // $gfsCode = $taskName == 'Inspection' ? $this->penaltyGfsCode : $this->GfsCode;
                    $gfsCode = $this->GfsCode;

                    $theItems = [];

                    $itemAmount = $billItemAmt - (float)$vehicle['penaltyAmount'];

                    $items = [
                        // 'fine' => $vehicle['hasPenalty'],
                        // 'RefBillId' => $billId,
                        // 'SubSpCode' =>  setting('Bill.wmaSubSpCode'),
                        // 'CollSp' => setting('Bill.wmaSpCode'),
                        'BillItemRef' => $vehicle['id'],
                        'UseItemRefOnPay' => 'N',
                        'BillItemAmt' => $itemAmount,
                        'BillItemEqvAmt' => $itemAmount,
                        'BillItemMiscAmt' => 0.00,
                        'GfsCode' => $gfsCode,
                        'BillId' => $billId,
                        'RequestId' => $requestId,
                        'ItemName' => $itemName,
                        'PayerId' => $vehicle['hash'],
                        'UserId' => $vehicle['unique_id'],
                        'Status' => $vehicle['visualInspection'] == 'Pass' && $vehicle['testing'] ? 'Pass' : 'Rejected',
                        'ItemQuantity' => 1,
                    ];


                    $theItems[] = $items;



                    // Check if there is a penalty
                    if ($vehicle['hasPenalty'] == 1) {
                        $penaltyItem = [
                            //'RefBillId' =>  $billId,
                            //'SubSpCode' => setting('Bill.wmaSubSpCode'),
                           // 'CollSp' => setting('Bill.wmaSpCode'),
                            'BillItemRef' => $vehicle['id'].time(),
                            'UseItemRefOnPay' => 'N',
                            'BillItemAmt' =>  (float)$vehicle['penaltyAmount'],
                            'BillItemEqvAmt' => (float)$vehicle['penaltyAmount'],
                            'BillItemMiscAmt' => 0.00,
                            'GfsCode' => $this->penaltyGfsCode,
                            'BillId' => $billId,
                            'ItemName' => 'Fine For ' . $vehicle['vehicle_brand'] . ' ' . $vehicle['plate_number'],
                            'RequestId' => $requestId,
                            'PayerId' => $vehicle['hash'],
                            'UserId' => $vehicle['unique_id'],
                            'Status' => 'Rejected',
                            'ItemQuantity' => 1,
                        ];


                        $theItems[] = $penaltyItem;
                    }

                    return $theItems;
                }, $vehicles);

                $itemsArray = array_merge(...$itemsArray);




                ///$penaltyAmounts = (new ArrayLibrary($itemsArray))->filter(fn ($itm) => $itm['GfsCode'] == $this->penaltyGfsCode)->reduce(fn ($x, $y) => $x + $y['BillItemAmt'])->get() ?? 0;


                //=================data for bill submission====================
                // $billedAmount = array_sum($total);
      
                $groupBillId = 'GRP'.numString(10);
                $centerDetails = wmaCenter($this->collectionCenter);
                $collectionCenterCode =  $centerDetails->collectionCenterCode; //'CC1015000199419';
                $groupBillId = 'GRP'.numString(10);
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
                    'BillAmt' => $billAmount,
                    'BillAmtWords' => toWords($billAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' =>  $BillExprDt,
                    'extendedExpiryDate' => (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s'),
                    'PyrId' =>  $customer->hash,
                    'PyrName' =>  $customer->name,
                    'BillDesc' =>  'Sandy And Ballast Lorries Verification',
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>   $this->getUser()->name,
                    'CollectionCenter' =>   $this->collectionCenter,
                    'BillApprBy' =>   'WMAHQ',
                    'PyrCellNum' =>  $customer->phone_number,
                    'PyrEmail' =>   $customer->email,
                    'Ccy' =>  'TZS',
                    'BillEqvAmt' => $billAmount,
                    'RemFlag' =>  $this->getVariable('RemFlag') == "on" ? 'true' : 'false',
                    'BillPayOpt' =>  $paymentOption,
                    'method' =>  $method,
                    'Task' =>  $task,
                    'UserId' =>  $this->uniqueId,
                    'SwiftCode' =>  $SwiftCode != '' ? $SwiftCode : '',

                ];

                //    $activityItems = [];

                //     if ($paymentOption == 2) {
                //         $combinedAmount = (new ArrayLibrary($itemsArray))->reduce(fn($x, $y) => $x + $y['BillItemAmt'])->get();

                //         $item = [
                //             'BillItemRef' => $billId,
                //             'UseItemRefOnPay' => 'N',
                //             'BillItemAmt' => $combinedAmount,
                //             'BillItemEqvAmt' => $combinedAmount,
                //             'BillItemMiscAmt' => 0.00,
                //             'GfsCode' => $itemsArray[0]['GfsCode'],

                //         ];
                //         array_push($activityItems, $item);
                //     } else {
                //         $activityItems = $itemsArray;
                //     }
                // $items = arrayExcept($itemsArray, ['ItemName', 'BillId', 'UserId', 'PayerId', 'Status','Task']);

                // return $this->response->setJSON([
                //     'status' => 0,
                //     // 'bill' => $billDetailsArray,
                //     'items' => ($itemsArray),
                //     'token' => $this->token,
                //     'paymentOption' => $paymentOption,
                // ]);

                // exit;





                $activityBill = new ActivityBillProcessing();

                $response = $activityBill->processBill($billDetailsArray, $itemsArray, $this->getUser()->name);


                //  return $this->response->setJSON([
                //    'status' => 0,
                //    'data' =>  $response,
                //    'token' => $this->token
                //  ]);

                // // return  $this->response->setJSON($response);

                // exit;

                $trucks = (new ArrayLibrary($vehicles))->map(fn ($v) =>  $v['vehicle_brand'] . '-' . $v['plate_number'])->get();
                if ($response->status == 1) {

                    $cn = $response->controlNumber;
                    $stickerLib = new StickerLibrary();

                    $updatedItems = array_map(fn ($item) => [
                        'id' => $item['BillItemRef'],

                    ], $stickerLib->attachSticker($itemsArray, $cn));


                    $updatedVehicles = array_map(function ($item) use ($updatedItems) {
                        $id = $item['id'];

                        return $item;
                    }, $vehicles);


                    $request1 = $this->lorryModel->registerVerifiedLorry($updatedVehicles);



                    $textParams = (object)[
                        'payer' => $customer->name,
                        'center' => wmaCenter($this->collectionCenter)->centerName,
                        'amount' => $billAmount,
                        'items' => (string)implode(',', $trucks),
                        'expiryDate' => $expiryDate,
                        'controlNumber' => $cn,

                    ];



                    if ($request1) {
                        (new AppModel())->disposeItems($vehicleIds);
                        $this->sms->sendSms(recipient: $customer->phone_number, message: billTextTemplate($textParams));




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









                        return $this->response->setJSON([
                            'status' => 1,
                            'TrxStsCode' => $response->TrxStsCode,
                            'msg' => 'Bill Created Successfully',
                            'bill' => $response->bill,
                            'qrCodeObject' => $response->qrCodeObject,
                            'heading' => $response->heading,
                            'token' => $this->token
                        ]);
                    }
                    //  sending sms notification to customer

                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'TrxStsCode' => $response->TrxStsCode,
                        'msg' => !empty($response->TrxStsCode)  ? tnxCode($response->TrxStsCode) : $response->msg,
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
            return $this->response->setJSON($response);
        }
    }

    public function getUser(): object
    {
        return (object)[
            'name' => auth()->user()->username,
            'collectionCenter' => centerName()

        ];
    }

    public function grabLastLorry()
    {
        $lastVehicle = $this->lorryModel->grabTheLastVehicle();
        echo json_encode($lastVehicle);
    }

    // ================Adding Lorry information to database ==============
    public function addLorry()
    {

        $data = [];

        $data['page'] = [
            "title" => "Sandy & Ballast Lorries",
            "heading" => "Sandy & Ballast Lorries",
        ];


        $data['user'] = $this->user;

        return view('Pages/Lorries/addLorry', $data);
    }

    public function listRegisteredLorries($collectionCenter)
    {

        try {

            $data['page'] = [
                "title" => " Registered Sandy & Ballast lorries",
                "heading" => "Registered Sandy & Ballast lorries",
            ];

            $table = 'verified_lorries';
            if ($this->request->getMethod() == 'POST') {

                $year = $this->getVariable('years');
                $years = explode('_', $year);
                $queryParams = [

                    // $table . '.unique_id' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                    $table . '.created_at>=' =>  "$years[0]-07-01",
                    $table . '.created_at<=' =>  "$years[1]-06-30",
                    $table . '.region' => $collectionCenter == 'all' ? '' :  $collectionCenter,
                    'wma_bill.IsCancelled' => 'No',
                    //'wma_bill.PaymentStatus' => 'Paid',
                ];

                $params = array_filter($queryParams, fn ($param) => $param !== '' || $param != null);
                $data['lorryResults'] = $this->lorryModel->verifiedSbl($params);

                $data['year'] = str_replace('_', '/', $year);
                return view('Pages/Lorries/listLorries', $data);
            }


            $data['user'] = $this->user;




            $queryParams = [
                // $table . '.unique_id' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                $table . '.created_at>=' =>  financialYear()->startDate,
                $table . '.created_at<=' =>  financialYear()->endDate,
                'CollectionCenter' => $collectionCenter == 'all' ? ''  :  $collectionCenter,
                'wma_bill.IsCancelled' => 'No',
                // 'wma_bill.PaymentStatus' => 'Paid',
            ];

            $params = array_filter($queryParams, fn ($param) => $param !== '' || $param != null);
            $params['PayCntrNum !='] = '';


            $data['lorryResults'] = $this->lorryModel->verifiedSbl($params);

            $data['year'] = date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
            return view('Pages/Lorries/listLorries', $data);
        } catch (\Throwable $th) {

            // echo $th->getMessage();
            // printer($th->getTrace());
        }
    }

    public function downloadLorryChart($id)
    {
        $lorry = $this->lorryModel->verifiedSbl([
            'verified_lorries.id' => $id
        ])[0];
        $certificate = (new CertificateModel())->getLastCorrectnessCertificate([
            'controlNumber' => $lorry->PayCntrNum
        ]);

        $title =  $lorry->PyrName . ' SBL CHART ' . time();

        // printer($lorry);
        $data['lorry'] = $lorry;
        $certificateNumber = empty($certificate) ? '' : $certificate->certificateNumber;
        $data['certificateOfCorrectness'] = $certificateNumber;

        $data['qrCode'] = QRCode([
            'cn' => $lorry->PayCntrNum,
            'sticker' => $lorry->stickerNumber,
            'certificate' => $certificateNumber,
            'plateNumber' => $lorry->plate_number,
        ]);

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'P', view: 'Pages/Lorries/lorryChart', data: $data, title: $title);
    }



    public function editLorry()
    {
        $vehicleId = $this->getVariable('vehicleId');
        $lorry = $this->lorryModel->findVehicle($vehicleId);
        $lorryTrailers = $this->lorryModel->getTrailers($vehicleId);

        $row = <<<HTML
             <div class="row">
                <div class="col-md-12">
                <input type="text" name="vehicleId" value="$vehicleId" class="form-control" hidden>
                </div>
                <div class="form-group col-md-6">
                    <label class="must">Vehicle Plate Number </label>
                    <input type="text" class="form-control" value="$lorry->plate_number" name="plateNumber" id="plateNumber" placeholder="Enter Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="must">Lorry Capacity in m<sup>3</sup></label>
                    <input type="number" class="form-control " value="$lorry->mainCapacity" name="lorryCapacity" placeholder="Enter  lorry Capacity in Cubic Meter" required>

                </div>
               
                 </div>
        HTML;

        $trailers = '';

        foreach ($lorryTrailers as $trailer) {
            $idNo = numString(5);
            $trailers .= <<<HTML
                <div class="row p-2 elevation-0 mb-2" style="border:1px solid #e7e5e5; border-radius:4px">

                <div class="col-md-12">
                <input type="number" name="id[]" value="$trailer->id" class="form-control" hidden>
                    <button type="button" class="btn btn-outline-secondary btn-sm" style="float: right;" onclick="this.parentNode.parentNode.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Trailer Plate Number</label>
                        <input type="text" name="trailerPlate[]" id="" value="$trailer->trailerPlate" class="form-control" placeholder="Trailer Plate Number" oninput="this.value = this.value.toUpperCase().replaceAll(/\s/g,'')">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Trailer  Capacity</label>
                        <input type="number" name="trailerCapacity[]" value="$trailer->trailerCapacity" class="form-control" placeholder="Trailer Plate Capacity">

                    </div>
                </div>
               

                </div>
           HTML;
        }






        if ($lorry) {
            return $this->response->setJSON([
                'status' => 1,
                'lorry' => $row,

                'trailers' => $trailers,
                'token' => $this->token
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => '',
                'token' => $this->token
            ]);
        }
    }

    // ================Update lorry Details==============

    public function updateLorryData9()
    {
        $vehicleId = $this->getVariable('vehicleId');

        return $this->response->setJSON([
            'status' => 1,
            'id' => $vehicleId,
            'token' => $this->token
        ]);
    }
    public function updateLorryData()
    {
        try {


            // return $this->response->setJSON([
            //     'status' => 1,
            //     'id' => $this->request->getPost('vehicleId'),
            //     'vehicle' => $_POST,
            //     'token' => $this->token
            // ]);

            // exit;


            $vehicleId = $this->getVariable('vehicleId');
            $trailerId = $this->getVariable('id');
            $trailerPlate = $this->getVariable('trailerPlate');
            $trailerCapacity = $this->getVariable('trailerCapacity');

            $mainCapacity = $this->getVariable('lorryCapacity');
            $totalCapacity = !empty($trailerCapacity) ? array_sum($trailerCapacity) + $mainCapacity : $mainCapacity;

            $amount = ($totalCapacity * 15000);

            $vehicle = [
                'capacity' => $totalCapacity,
                'mainCapacity' => $mainCapacity,
                'amount' => $amount,
                'plate_number' => $this->getVariable('plateNumber'),
            ];



            $request = $this->lorryModel->updateLorry($vehicle, $vehicleId);

            if ($request) {
                if (!empty($trailerCapacity && $trailerPlate)) {
                    $trailerDetails = multiDimensionArray([
                        'id' => $trailerId,
                        'trailerPlate' => $trailerPlate,
                        'trailerCapacity' => $trailerCapacity,
                    ]);

                    $this->lorryModel->updateTrailers($trailerDetails);
                }

                $response = [
                    'status' => 1,
                    'msg' => 'Lorry Updated Successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'token' => $this->token
                ];
            }
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
}
