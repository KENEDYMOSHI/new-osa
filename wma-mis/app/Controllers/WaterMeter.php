<?php

namespace App\Controllers;


use DateTime;
use App\Models\AppModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\SmsLibrary;
use App\Models\CustomerModel;
use App\Libraries\ArrayLibrary;
use App\Models\WaterMeterModel;
use App\Libraries\CommonTasksLibrary;
use App\Libraries\CertificateLibrary;
use App\Libraries\ActivityBillProcessing;

//use \CodeIgniter\Models\waterMeterModel;

class WaterMeter extends BaseController
{
    protected $uniqueId;
    protected $managerId;
    protected $waterMeterModel;
    protected $session;
    protected $profileModel;
    protected $CommonTasks;
    protected $appModel;
    protected $token;
    protected $customersModel;
    protected $GfsCode;
    protected $collectionCenter;
    protected $user;
    protected $sms;



    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->GfsCode = setting('Gfs.waterMeter');
        $this->appModel = new AppModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->profileModel = new ProfileModel();
        $this->customersModel = new CustomerModel();
        $this->session = session();
        $this->token = csrf_hash();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;

        $this->CommonTasks
            = new CommonTasksLibrary();
        $this->user = auth()->user();
        $this->sms = new SmsLibrary();
    }



    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    // ================Adding Vtc information to database ==============


    public function addExtraWaterMeters()
    {


        try {
            if ($this->request->getMethod() == 'POST') {
                //=================Checking the last Id before incrementing it====================


                $batchId =   $this->getVariable('batchId');
                $decision =   $this->getVariable('decision');
                $hash =  $this->getVariable('customerId');

                $meter = $this->waterMeterModel->findMeter(['batch_id' => $batchId]);




                if (empty($decision)) {
                    return  $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Please Add At least 1 Meter',
                        'token' => $this->token
                    ]);
                }



                $count = count($decision);
                $qty = $meter->quantity;

                $newQty = $qty + $count;


                $itemAmounts = array_map(fn ($d) => $d == 'PASS' ? 10000 : 5000, $decision);
                $amount = array_sum($itemAmounts);
                $updatedAmount = $meter->amount + $amount;
                $this->waterMeterModel->updateMeterAmount($batchId, ['amount' => $updatedAmount, 'quantity' => $newQty]);

                // return  $this->response->setJSON([
                //     'data' => $meter ,
                //     'token' => $this->token,
                //     'id' => $batchId,
                //     'qty' => $newQty,
                //     'p AMT' => $meter->amount,
                //     'amt' => $updatedAmount,
                // ]);


                // exit;

                $data = [

                    'serial_number' =>   $this->getVariable('serialNumber'),
                    'item_amount' => $itemAmounts,
                    'gfCode' => fillArray($count, $this->GfsCode),
                    'amount' =>  fillArray($count, $updatedAmount),
                    'quantity' =>  fillArray($count, $newQty),
                    'decision' =>   $decision,
                    'tag' =>   $this->getVariable('tag'),
                    'initial_reading' =>   $this->getVariable('initialReading'),
                    'final_reading' =>   $this->getVariable('finalReading'),
                    'indicated_volume' =>   $this->getVariable('indicatedVolume'),
                    'actual_volume' =>   $this->getVariable('actualVolume'),
                    'error' =>   $this->getVariable('error'),
                    'batch_id' => fillArray($count, $batchId),
                    'hash' =>   fillArray($count, $meter->hash),
                    'task' =>   fillArray($count, $meter->task),
                    'region' =>   fillArray($count, $this->user->collection_center),
                    'category' =>   fillArray($count, $meter->category),
                    'meter_size' =>   fillArray($count, $meter->meter_size),
                    'brand' =>   fillArray($count, $meter->brand),
                    'flow_rate' =>   fillArray($count, $meter->flow_rate),
                    'rate' =>   fillArray($count, $meter->rate),
                    'class' =>   fillArray($count, $meter->class),
                    'lab' =>   fillArray($count, $meter->lab),
                    'verifier' =>   fillArray($count, $meter->verifier),
                    'testing_method' => fillArray($count, $meter->testing_method),
                    'unique_id' => fillArray($count, $this->uniqueId),

                ];



                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => $data,
                //     'token' => $this->token
                //   ]);

                //   exit;

                $meters = multiDimensionArray($data);



                // return $this->response->setJSON([
                //     'status' => 0,
                //     // 'data' => $WaterMeterData,
                //     'data' => $meters,
                //     'token' => $this->token
                // ]);
                //  exit;
                $req = $this->waterMeterModel->registerWaterMeter($meters);

                if ($req) {
                    $billed = $this->waterMeterModel->filterCustomersPaidWaterMeters($data['hash'][0]);
                    $ids = $billed != [] ? array_map(fn ($meter) => $meter->BillItemRef, $billed) : ['_'];
                    $notBilled = $this->waterMeterModel->getAllUnpaidWaterMeters($data['hash'][0], $ids);
                    return $this->response->setJSON([
                        'status' => 1,
                        // 'meter' => $this->waterMeterModel->getMetersByBatchId($data['hash'][0]),
                        'meters' => $notBilled,
                        'msg' => 'Meters Added',
                        'token' => $this->token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Something Went Wrong!',
                        'meters' => [],
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $e) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' => $e->getMessage(),
                // 'trace' => $e->getTrace(),
                'token' => $this->token
            ]);
        }
    }





































    public function registerWaterMeter()
    {


        try {
            if ($this->request->getMethod() == 'POST') {
                //=================Checking the last Id before incrementing it====================


                $decision =   $this->getVariable('decision');
                $hash =  $this->getVariable('customerId');




                if (empty($decision)) {
                    return  $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Please Add At least 1 Meter',
                        'token' => $this->token
                    ]);
                }


                $count = count($decision);
                // return  $this->response->setJSON([
                //     'data' => $decision ,
                //     'token' => $this->token,
                //     'count' => $count
                // ]);


                // exit;


                $amount = array_map(fn ($d) => $d == 'PASS' ? 10000 : 5000, $decision);
                $batchId = numString(10);

                $data = [

                    'serial_number' =>   $this->getVariable('serialNumber'),
                    'item_amount' => $amount,
                    'gfCode' => fillArray($count, $this->GfsCode),
                    'amount' =>  fillArray($count, array_sum($amount)),
                    'quantity' =>  fillArray($count, $count),
                    'decision' =>   $decision,
                    'tag' =>   $this->getVariable('tag'),
                    'initial_reading' =>   $this->getVariable('initialReading'),
                    'final_reading' =>   $this->getVariable('finalReading'),
                    'indicated_volume' =>   $this->getVariable('indicatedVolume'),
                    'actual_volume' =>   $this->getVariable('actualVolume'),
                    'error' =>   $this->getVariable('error'),
                    'batch_id' => array_fill(0, $count, $batchId),
                    'hash' =>   fillArray($count, $hash),
                    'task' =>   fillArray($count, $this->getVariable('task')),
                    'region' =>   fillArray($count, $this->user->collection_center),
                    'category' =>   fillArray($count, $this->getVariable('category')),
                    'meter_size' =>   fillArray($count, $this->getVariable('meterSize')),
                    'brand' =>   fillArray($count, $this->getVariable('brandName')),
                    'flow_rate' =>   fillArray($count, $this->getVariable('flowRate')),
                    'rate' =>   fillArray($count, $this->getVariable('rate')),
                    'class' =>   fillArray($count, $this->getVariable('class')),
                    'lab' =>   fillArray($count, $this->getVariable('testingLab')),
                    'verifier' =>   fillArray($count, $this->getVariable('verifier')),
                    'testing_method' => fillArray($count, $this->getVariable('testMethod')),
                    'unique_id' => fillArray($count, $this->uniqueId),

                ];




                $meters = multiDimensionArray($data);

                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => $meters,
                //     'token' => $this->token
                //   ]);

                //   exit;


                // return $this->response->setJSON([
                //     'status' => 1,
                //     // 'data' => $WaterMeterData,
                //     'data' => $meters,
                //     'token' => $this->token
                // ]);
                // exit;
                $req = $this->waterMeterModel->registerWaterMeter($meters);

                if ($req) {
                    $this->appModel->createTempId([
                        'itemId' => $batchId,
                        'customerId' => $hash,
                        'activity' => $this->GfsCode,
                        'collectionCenter' => $this->collectionCenter
                    ]);
                    // $billed = $this->waterMeterModel->filterCustomersPaidWaterMeters($data['hash'][0]);
                    // $ids = $billed != [] ? array_map(fn ($meter) => $meter->BillItemRef, $billed) : ['_'];
                    $idz = $this->appModel->getItemIds([
                        'customerId' => $hash,
                        'activity' => $this->GfsCode,
                    ]);
                    $notBilled = $this->waterMeterModel->getAllUnpaidWaterMeters($data['hash'][0], $idz);
                    return $this->response->setJSON([
                        'status' => 1,
                        // 'meter' => $this->waterMeterModel->getMetersByBatchId($data['hash'][0]),
                        'meters' => $notBilled,
                        'msg' => 'Meters Added',
                        'token' => $this->token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Something Went Wrong!',
                        'meters' => [],
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $e) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' => $e->getMessage(),
                // 'trace' => $e->getTrace(),
                'token' => $this->token
            ]);
        }
    }

    //=================check if customer has any unpaid WaterMeter====================
    public function getUnpaidWaterMeters()
    {
        if ($this->request->getMethod() == 'POST') {
            $hash =   $this->getVariable('customerId');
            $billed = $this->waterMeterModel->filterCustomersPaidWaterMeters($hash);
            // $ids = $billed != [] ? array_map(fn ($meter) => $meter->BillItemRef, $billed) : ['_'];
            $idz = $this->appModel->getItemIds([
                'customerId' => $hash,
                'activity' => $this->GfsCode,
            ]);
            $notBilled = $this->waterMeterModel->getAllUnpaidWaterMeters($hash, $idz);

            $meters = '';

            foreach ($notBilled as $meter) {
                $domId = 'MT' . $meter->batch_id;
                $amount = number_format($meter->amount);
                $link = base_url('printMeterChart/' . $meter->batch_id);
                $meters .= <<<"HTML"
                  <tr id="$domId">
                    <td>$meter->brand
                        <input type="text" value="$meter->batch_id"  name="batchId[]" hidden>
                    </td>
                    <td>$meter->meter_size mm</td>
                    <td>$meter->rate m<sup>3</sup>/h</td>
                    <td>$meter->class</td>
                    <td>$meter->quantity Meters</td>
                    <td>$amount Tsh
                        <input type="text" value="$meter->amount" class="itemAmount" hidden>
                    </td>
                    <td>

                        <a data-toggle="tooltip" data-placement="top" title="Print Chart" href="$link" target="_blank" class="btn btn-primary btn-sm"><i class="far fa-print"></i></a>
                        <button data-toggle="tooltip" data-placement="top" title="Remove Item"  type="button" onclick="clearRow('$domId')" class="btn btn-dark btn-sm"><i class="far fa-times"></i></button>
                        <button data-toggle="tooltip" data-placement="top" title="Add Extra Meters" type="button" onclick="addMeters('$meter->batch_id','$meter->brand','$meter->rate','$meter->class','$meter->actual_volume')" class="btn btn-dark btn-sm" style='background:#22ae32;color:white;border:1px solid #22ae32;'><i class="far fa-plus"></i></button>
                    </td>
                    
                  </tr>
                 HTML;
            }


            $html = <<<"HTML"
               <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Meter Size</th>
                                <th>Flow Rate</th>
                                <th>Class</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Chart</th>
                            </tr>
                        </thead>
                        <tbody>
                            $meters
                        </tbody>
                    </table>
             HTML;

            if ($notBilled != []) {
                return $this->response->setJSON([
                    'status' => 1,
                    'meters' =>  $html,
                    'meters count' => count($notBilled),
                    'unbilled' => $notBilled,
                    'token' => $this->token
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'meters' => '<p>No Data Found</p>',
                    'msg' => 'No Data Found !',
                    'token' => $this->token
                ]);
            }
        }
    }

    //=================Publishing Meter In transaction table====================

    public function publishWaterMeterData()
    {
        try {
            if ($this->request->getMethod() == 'POST') {

                $hash = $this->getVariable('customerId');
                $batchId = $this->getVariable('batchId');
                $SwiftCode = $this->getVariable('SwiftCode');
                $billedAmount = (float)$this->getVariable('totalAmount');
                $method = $this->getVariable('method');
                $expiryDate = $this->getVariable('BillExprDt');
                $currentDate = date("Y-m-d\TH:i:s");
                $xpDate = $expiryDate . '23:59:59';
                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+7 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));


                $billId ='WMA'.randomString();
                $requestId = $billId;// 'WMAREQ'.numString(10);


                $userId = $this->uniqueId;

                $count = count($batchId);

                $itemsArray = array_map(function ($id) use ($billId,$requestId) {
                    $meter = $this->waterMeterModel->getVerifiedMetersByBatch($id);
                    for ($i = 0; $i < count($meter); $i++) {

                        return [
                            //'RefBillId' =>  $billId,
                            //'SubSpCode' => setting('Bill.wmaSubSpCode'),
                           // 'CollSp' => setting('Bill.wmaSpCode'),
                           'BillItemRef' => $id,
                           'UseItemRefOnPay' => 'N',
                           'BillItemAmt' => (float)$meter[$i]->amount,
                           'BillItemEqvAmt' => (float)$meter[$i]->amount,
                           'BillItemMiscAmt' => 0.00,
                           'GfsCode' => $this->GfsCode,
                            'ItemName' => $meter[$i]->brand . ' ' . $meter[$i]->meter_size . ' mm ' . $meter[$i]->flow_rate . 'm3/h',
                            'PayerId' =>  $meter[$i]->hash,
                            'UserId' =>  $meter[$i]->unique_id,
                            'BillId' =>  $billId,
                            'RequestId' => $requestId,
                            'ItemQuantity' => $meter[$i]->quantity,
                            'Task' => $meter[$i]->task,

                        ];
                    }
                }, $batchId);

                $billedMeters = array_map(function ($id) use ($billId) {
                    $meter = $this->waterMeterModel->getVerifiedMetersByBatch($id);
                    for ($i = 0; $i < count($meter); $i++) {

                        return  $meter[$i]->brand . ' ' . $meter[$i]->meter_size . ' mm ';
                    }
                }, $batchId);





                $customer = $this->customersModel->selectCustomer($hash);

                //=================data for bill submission====================
                $groupBillId = 'GRP'.numString(10);

                
                $centerDetails = wmaCenter($this->collectionCenter);
                $collectionCenterCode =  $centerDetails->collectionCenterCode; //'CC1015000199419';
                $groupBillId = 'GRP'.numString(10);
                $centerDetails = wmaCenter($this->collectionCenter);
                $collectionCenterCode =  $centerDetails->collectionCenterCode; //'CC1015000199419';
                $groupBillId = 'GRP'.numString(10);
                $billDetailsArray = [
                    'BillTyp' => 2,
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
                    'BillAmt' => $billedAmount,
                    'BillAmtWords' => toWords($billedAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' =>  $BillExprDt,
                    'extendedExpiryDate' => (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s'),
                    'PyrId' =>  $customer->hash,
                    'PyrName' =>  $customer->name,
                    'BillDesc' =>  'Meters Verification',
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>   $this->getUser()->name,
                    'CollectionCenter' =>   $this->collectionCenter,
                    'BillApprBy' =>   'WMAHQ',
                    'PyrCellNum' =>  $customer->phone_number,
                    'PyrEmail' =>   $customer->email,
                    'Ccy' =>  'TZS',
                    'BillEqvAmt' => $billedAmount,
                    'RemFlag' =>  $this->getVariable('RemFlag') == "on" ? 'true' : 'false',
                    'BillPayOpt' =>  (int)$this->getVariable('BillPayOpt'),
                    'method' =>  $method,
                    'Task' =>  'Verification',
                    'UserId' =>  $this->uniqueId,
                    'SwiftCode' =>  $SwiftCode != '' ? $SwiftCode : '',

                ];

                // return $this->response->setJSON([
                //     'status' => 0,
                //     'items' => $itemsArray,
                //     'bill' => $billDetailsArray,
                //     'token' => $this->token
                // ]);

                // exit;


                $activityBill = new ActivityBillProcessing();

                $response = $activityBill->processBill($billDetailsArray, $itemsArray, $this->getUser()->name);



                if ($response->status == 1) {


                    $textParams = (object)[
                        'payer' => $customer->name,
                        'center' => wmaCenter($this->collectionCenter)->centerName,
                        'amount' => $billedAmount,
                        'items' => (string)implode(',', $billedMeters),
                        'expiryDate' => $expiryDate,
                        'controlNumber' => $response->controlNumber,

                    ];

                    $this->appModel->disposeItems($batchId);

                    //sending sms notification to customer
                    $this->sms->sendSms(recipient: $customer->phone_number, message: billTextTemplate($textParams));

                        $meterItems = array_map(fn ($meter) => $meter['ItemName'],  $itemsArray);
                         // ================certificates=================================
                         $certificateData = (object)[

                            'customer' => $customer->name,
                            'activity' => json_encode($this->GfsCode),
                            'mobile' => $customer->phone_number,
                            'address' => $customer->postal_address,
                            'items' =>  json_encode($meterItems),
                            'controlNumber' =>  $response->controlNumber,

                        ];
                        //adding certificate data
                        (new CertificateLibrary())->createCertificateData($certificateData);


                    return $this->response->setJSON([
                        'status' => 1,
                        'msg' => 'Bill Created Successfully',
                        'bill' => $response->bill,
                        'qrCodeObject' => $response->qrCodeObject,
                        'heading' => $response->heading,
                        'token' => $this->token,
                        'TrxStsCode' => $response->TrxStsCode,
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'TrxStsCode' => $response->TrxStsCode,
                        'msg' => !empty($response->TrxStsCode)  ? tnxCode($response->TrxStsCode) : $response->msg,
                        'token' => $this->token
                    ]);
                }

                // $request = true;

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
        $user = $this->profileModel->getLoggedUserData($this->uniqueId);
        return (object)[
            'name' => $user->first_name . ' ' . $user->last_name,
            'collectionCenter' => centerName()

        ];
    }

    public function addWaterMeter()
    {

        $data = [];
        $data['validation'] = null;

        $data['page'] = [
            "title" => "Meter",
            "heading" => "Meter",
        ];
        $data['statusResult'] = ['Pass', 'Rejected'];
        $data['genderValues'] = ['Male', 'Female'];


        $data['user'] = $this->user;
        return view('Pages/WaterMeter/addWaterMeter', $data);
    }

    public function listRegisteredWaterMeters($collectionCenter)
    {


        try {
            $data['page'] = [
                "title" => "Verified  Meters",
                "heading" => "Verified  Meters",
            ];


            $table = 'water_meters';



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
 
                $data['year'] = str_replace('_', '/', $year);
                $WaterMeterResults = $this->waterMeterModel->verifiedWaterMeters($params);
                $data['WaterMeterResults'] = $WaterMeterResults;
                $data['numberOfMeters'] = (new ArrayLibrary($WaterMeterResults))->map(fn ($meter) => $meter->quantity)->reduce(fn ($x, $y) => $x + $y)->get();
                return view('Pages/WaterMeter/WaterMeterList', $data);
            }


            $data['user'] = auth()->user();

            $currentMonth = date('m');
            if ($currentMonth >= 7) {
                $initialDate = date('Y-07-01');
                $finalDate = date('Y-06-30', strtotime('+1 year'));
            } else {
                $initialDate = date('Y-07-01', strtotime('-1 year'));
                $finalDate = date('Y-06-30');
            }


            // echo $initialDate;
            // echo $finalDate;
            // exit;

            $queryParams = [
                // $table . '.decision' => 'PASS',
                // $table . '.unique_id' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                $table . '.created_at>=' =>  financialYear()->startDate,
                $table . '.created_at<=' =>  financialYear()->endDate,
                'CollectionCenter' => $collectionCenter == 'all' ? '' : $collectionCenter,
                'wma_bill.IsCancelled' => 'No',
            ];

            $params = array_filter($queryParams, fn ($param) => $param !== '' || $param != null);
            $params['PayCntrNum !='] = '';
            // printer($this->waterMeterModel->verifiedWaterMeters($params));
            // exit;
            $WaterMeterResults = $this->waterMeterModel->verifiedWaterMeters($params);
            $data['WaterMeterResults'] = $WaterMeterResults;

            $data['year'] = date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
            $data['numberOfMeters'] = (new ArrayLibrary($WaterMeterResults))->map(fn ($meter) => $meter->quantity)->reduce(fn ($x, $y) => $x + $y)->get();

            return view('Pages/WaterMeter/WaterMeterList', $data);
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ];
        }
        printer($response);
    }

    public function printMeterChart($batchId)
    {
        $meters = $this->waterMeterModel->getMetersByBatch($batchId);

        $data['report'] = $meters;
        $data['center'] = wmaCenter();
        $title = $meters[0]->name . '' . numString(5);

        $data['passedMeters'] = count((new ArrayLibrary($meters))->filter(fn ($meter) => $meter->decision == 'PASS')->get());
        $data['failedMeters'] = count((new ArrayLibrary($meters))->filter(fn ($meter) => $meter->decision == 'FAIL')->get());


        return view('Pages/WaterMeter/WaterMeterChartPrint', $data);
    }
    public function downloadMeterChart($batchId)
    {
        try {
            $meters = $this->waterMeterModel->getMetersByBatch($batchId);


            $data['report'] = $meters;
            $title = $meters[0]->name . '' . numString(5);

            $data['passedMeters'] = count((new ArrayLibrary($meters))->filter(fn ($meter) => $meter->decision == 'PASS')->get());
            $data['failedMeters'] = count((new ArrayLibrary($meters))->filter(fn ($meter) => $meter->decision == 'FAIL')->get());



            $pdfLibrary = new PdfLibrary();
            $pdfLibrary->renderPdf(orientation: 'L', view: 'Pages/WaterMeter/WaterMeterChart', data: $data, title: $title);
        } catch (\Throwable $th) {

            $link = base_url('printMeterChart/' . $batchId);
            return redirect()->to($link);
        }
    }
}
