<?php

namespace App\Controllers\Api;

use DateTime;
use DateInterval;
use LSS\Array2XML;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\SmsLibrary;
use App\Libraries\XmlLibrary;
use App\Libraries\GepgProcess;
use App\Libraries\WmaGepgProcess;
use App\Libraries\ArrayLibrary;
use App\Libraries\StickerLibrary;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\CertificateLibrary;
use App\Models\WmaBillModel;
use CodeIgniter\RESTful\ResourceController;



class ServiceBillProcessorApi extends ResourceController
{
    protected $billModel;
    protected $wmaBillModel;
    protected $uniqueId;
    protected $managerId;
    protected $role;

    protected $session;
    protected $profileModel;
    protected $CommonTasks;

    protected $billLibrary;
    protected $xmlLibrary;
    protected $GepGpProcess;
    protected $token;

    protected $SpCode;
    protected $subSpCode;
    protected $SpSysId;
    protected $systemId;
    protected $collectionCenters;
    protected $collectionCenter;
    protected $sms;
    protected $extendedExpiryDate;
    protected $apiKey;
    protected $wmaGepGpProcess;

    use ResponseTrait;

    public function __construct()
    {


        helper('setting');
        helper(setting('App.helpers'));
        $this->billModel = new BillModel();
        $this->wmaBillModel = new WmaBillModel();
        $this->collectionCenters = $this->billModel->getCollectionCenters();
        $this->xmlLibrary = new XmlLibrary();
        $this->GepGpProcess = new GepgProcess();
        $this->wmaGepGpProcess = new WmaGepgProcess();
        $this->profileModel = new profileModel();
        $this->uniqueId = 'e1x6mGYHdvWARtqpQgqKIErN8Mumei60';
        $this->collectionCenter = '001';
        $this->sms = new SmsLibrary();

        // $this->SpCode = setting('Bill.spCode');
        // $this->subSpCode = setting('Bill.subSpCode');
        // $this->systemId = setting('Bill.systemId');/

        $this->SpCode = 'SP419'; //setting('Bill.spCode');
        $this->subSpCode = '1002'; // setting('Bill.subSpCode');
        $this->systemId = 'LWMA002';
        $this->extendedExpiryDate = (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s');
        $this->apiKey = env('API_KEY');
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }


    private function validateApiKey($requestApiKey)
    {


        if (empty($requestApiKey) || $requestApiKey != $this->apiKey) {
            return false;
        }

        return true; // API key is valid
    }




    public function findUser($params)
    {
        $db = db_connect();
        return $db->table('users')->select('users.id,username,collection_center,unique_id')->where($params)
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->get()->getRow();
    }






    public function serviceBillRequest()
    {
        //use ResponseTrait;




        try {
            if ($this->request->getMethod() == 'POST') {


                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => $this->request->getVar('BillEqvAmt'),
                //     'token' => randomString().'TOKEN TOKEN'
                // ]);

                // exit;
                $BillItemsObj = $this->request->getVar('BillItems');

                $items =  json_decode(json_encode($BillItemsObj), true);


                $BillId = $this->getVariable('billId');
                $gfsCode  = $this->getVariable('GfsCode');
                $collectionCenter  = $this->getVariable('collectionCenter');
                $PhysicalLocation  = $this->getVariable('PhysicalLocation');
                $billAmount   = (float)str_replace(',', '', $this->getVariable('BillAmt'));

                $currentDate = date("Y-m-d\TH:i:s");
                $expiryDate  = $this->getVariable('BillExprDt');
                $xpDate = $expiryDate . '23:59:59';
                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+7 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));


                $manager = $this->findUser(['collection_center' => $collectionCenter, 'group' => 'manager']);

                $managerId = $manager->unique_id;
                $managerName = $manager->username;


                // return  $this->response->setJSON([
                //   'data' => $items,
                //   'BillId' => $BillId,
                // ]);

                // exit;


                $payer = $this->getVariable('PyrName');
                $phoneNumber = $this->getVariable('PyrCellNum');

                $billDetailsArray = [

                    'BillId' => $BillId,
                    'RequestId' => $BillId,
                    'Activity' =>  '',
                    'Task' =>  $items[0]['Task'],
                    'BillRef' => numString(10),
                    'BillAmt' => (float)  $billAmount,
                    'BillAmtWords' => toWords($billAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' => $BillExprDt,
                    'extendedExpiryDate' => $this->extendedExpiryDate,
                    'PyrId' => randomString(),
                    'PyrName' => ' OSA BILL TEST ' . preg_replace('/[\'"]/', '', $this->request->getVar('PyrName')),
                    'BillDesc' =>  $this->getVariable('BillDesc'),
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>  $managerName,
                    'CollectionCenter' =>  $collectionCenter ?? '001', // $this->collectionCenter,
                    'BillApprBy' =>  'wma-hq',
                    'PyrCellNum' => '255' . substr($this->getVariable('PyrCellNum'), 1),
                    'PyrEmail' =>  '',
                    // 'Ccy' =>  $this->getVariable('Ccy'),
                    'Ccy' =>  'TZS',
                    'BillEqvAmt' => (float)   $billAmount,
                    'RemFlag' =>  $this->getVariable('RemFlag') == "on" ? 'true' : 'false',
                    'BillPayOpt' =>  (int)$this->getVariable('BillPayOpt'),
                    'UserId' =>  $managerId,
                    'PhysicalLocation' =>  $PhysicalLocation,
                    'latitude' => $this->getVariable('latitude'),
                    'longitude' => $this->getVariable('longitude'),
                    'deviceId' => $this->getVariable('deviceId'),

                ];




                // log_message('BILL INFO', json_encode($billDetailsArray));


                $reqItems = (new ArrayLibrary($items))->map(function ($item) use ($BillId, $managerId) {
                    $amount = str_replace(',', '',  $item['BillItemAmt']);
                    $item['BillItemRef'] =  randomString();
                    $item['UseItemRefOnPay'] = 'N';
                    $item['BillItemMiscAmt'] = 0.00;
                    $item['BillItemAmt'] = $amount;
                    $item['BillItemEqvAmt'] = $amount;
                    $item['UserId'] =  $managerId;
                    $item['BillId'] = $BillId;
                    $item['ItemName'] =   $item['ItemName'] . ' ' .  $item['Capacity'] . ' ' .  $item['ItemUnit'];


                    return $item;
                })->get();

                $condemned = (new ArrayLibrary($reqItems))->filter(fn($item) => $item['Status'] == 'Condemned')->map(fn($item) => [
                    'customer' => $this->getVariable('PyrName'),
                    'task' => $item['Task'],
                    'item' => $item['ItemName'] . ' ' .  $item['Capacity'] . ' ' .  $item['ItemUnit'],
                    'activity' => $item['GfsCode'],
                    'status' => $item['Status'],
                    'collectionCenter' =>  $collectionCenter, // $this->collectionCenter,
                    'userId' => $this->uniqueId,
                ])->get();

                //creating condemned instruments if any found
                if (!empty($condemned)) $this->billModel->saveCondemnedItems(array_values($condemned));


                $passAndRejected = (new ArrayLibrary($reqItems))->filter(fn($item) => $item['Status'] != 'Condemned')->get();

                // Initialize arrays for each key
                $BillItemRefArr = [];
                $UseItemRefOnPayArr = [];
                $BillItemMiscAmtArr = [];
                $BillItemAmtArr = [];
                $BillItemEqvAmtArr = [];
                $GfsCodeArr = [];
                $ItemNameArr = [];
                $UserIdArr = [];
                $StatusArr = [];
                // $StickerNumberArr = [];
                $TasksArr = [];
                $BillIdArr = [];
                $SingleItemAmountArr = [];
                $ItemQuantityArr = [];

                // Loop through each sub-array and populate the arrays
                foreach (array_values($passAndRejected) as $item) {
                    $BillItemAmtArr[] = $item['BillItemAmt'];
                    $BillItemMiscAmtArr[] = $item['BillItemMiscAmt'];
                    $BillItemRefArr[] = $item['BillItemRef'];
                    $BillItemEqvAmtArr[] = $item['BillItemEqvAmt'];
                    $GfsCodeArr[] = $item['GfsCode'];
                    $ItemNameArr[] = $item['ItemName'];
                    $UseItemRefOnPayArr[] = $item['UseItemRefOnPay'];
                    $UserIdArr[] = $item['UserId'];
                    $StatusArr[] = $item['Status'];
                    // $StickerNumberArr[] = !isset($item['StickerNumber']) ||$item['StickerNumber'] == null ? '' : $item['StickerNumber'];
                    $TasksArr[] = $item['Task'];
                    $SingleItemAmountArr[] = $item['SingleItemAmount'];
                    $ItemQuantityArr[] = $item['ItemQuantity'];
                    $BillIdArr[] = $item['BillId'];
                }

                //calculating next verification date for each item
                $nextVerification = array_map(fn($gfs) => nextVerification($gfs), $GfsCodeArr);

                $itemsArray = [
                    'BillItemRef' => $BillItemRefArr,
                    'UseItemRefOnPay' => $UseItemRefOnPayArr,
                    'BillItemAmt' =>  $BillItemAmtArr,
                    'BillItemEqvAmt' =>  $BillItemEqvAmtArr,
                    'BillItemMiscAmt' => $BillItemMiscAmtArr,
                    'GfsCode' =>  $GfsCodeArr,
                    'ItemName' => $ItemNameArr,
                    'NextVerification' => $nextVerification,
                    'Task' => $TasksArr,
                    'Status' => $StatusArr,
                    'UserId' => $UserIdArr,
                    'BillId' => $BillIdArr,
                    'RequestId' => $BillIdArr,
                    'ItemQuantity' => $ItemQuantityArr,
                    'SingleItemAmount' => $SingleItemAmountArr,

                ];

                $items =  multiDimensionArray($itemsArray);



                // return $this->response->setJSON([
                //     'status' => 0,
                //     'env' => setting('System.env'),
                //     'items' => arrayExcept($items, ['ItemName', 'UserId', 'Task', 'Status', 'ItemQuantity', 'SingleItemAmount', 'BillId','RequestId', 'NextVerification']),
                //     'billDetailsArray' => $billDetailsArray,
                // ]);


                // exit;



                $xml = Array2XML::createXML('BillItems', ['BillItem' => arrayExcept($items, ['ItemName', 'UserId', 'Task', 'Status', 'ItemQuantity', 'SingleItemAmount', 'BillId', 'RequestId', 'NextVerification'])])->saveXML();
                $BillItems = ltrim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml));

                $billDetails = (object)$billDetailsArray;
                //$content = "";
                $content = "<gepgBillSubReq>
                <BillHdr>
                    <SpCode>$this->SpCode</SpCode>
                    <RtrRespFlg>true</RtrRespFlg>
                </BillHdr>
                <BillTrxInf>
                    <BillId>$BillId</BillId>
                    <SubSpCode>$this->subSpCode</SubSpCode>
                    <SpSysId>$this->systemId</SpSysId>
                    <BillAmt>$billDetails->BillEqvAmt</BillAmt>
                    <MiscAmt>$billDetails->MiscAmt</MiscAmt>
                    <BillExprDt>$this->extendedExpiryDate</BillExprDt>
                    <PyrId>$billDetails->PyrId</PyrId>
                    <PyrName>$billDetails->PyrName</PyrName>
                    <BillDesc>$billDetails->BillDesc</BillDesc>
                    <BillGenDt>$billDetails->BillGenDt</BillGenDt>
                    <BillGenBy>$billDetails->BillGenBy</BillGenBy>
                    <BillApprBy>$billDetails->BillApprBy</BillApprBy>
                    <PyrCellNum>$billDetails->PyrCellNum</PyrCellNum>
                    <PyrEmail>$billDetails->PyrEmail</PyrEmail>
                    <Ccy>$billDetails->Ccy</Ccy>
                    <BillEqvAmt>$billDetails->BillEqvAmt</BillEqvAmt>
                    <RemFlag>$billDetails->RemFlag</RemFlag>
                    <BillPayOpt>$billDetails->BillPayOpt</BillPayOpt>
                    " . $BillItems . "
                </BillTrxInf>
            </gepgBillSubReq>";





                //switching uri and request headers based on type of request being sent
                $uri = "bill/sigqrequest";
                $GepgCom = "Gepg-Com:default.sp.in";

                $params = (object)[
                    "dataTag" => "gepgBillSubReqAck",
                    "uri" => $uri,
                    'GepgCom' => $GepgCom,
                ];



                $submission = $this->wmaGepGpProcess->billSubmission(formatXml($content), $params);

                $response = XML2Array::createArray($submission->resultCurlPost);


                $code = $response['Gepg']['gepgBillSubReqAck']['TrxStsCode'];
                // return $this->response->setJSON([
                //     'billId' => $BillId,
                //     'data' => $response
                // ]);
                // exit;
                if ($code == '7101') {
                    $maxAttempts = 120; // Set a maximum number of attempts to avoid an infinite loop
                    $attempt = 0;
                    $startTime = microtime(true); // Record the start time
                    $billDetailsArray['TrxStsCode'] = $code;
                    $this->wmaBillModel->saveBill($billDetailsArray); //save bill details
                    $this->wmaBillModel->saveBillItems($items); // save bill items

                    $stickerLib = new StickerLibrary();
                    while ($attempt < $maxAttempts) { // Assuming $callbackData is the data received from the callback $billRes=$this->
                        $billRes =  $this->wmaBillModel->getBillResponse($BillId);

                        if (!empty($billRes)) {
                            $gepgResponseCode = $billRes->resCode;

                            // Check if the status code returned to the callback response code is 7101
                            if (strlen($gepgResponseCode) == 4 && $gepgResponseCode == '7101') {
                                // Your existing code for processing successful response

                                // Save bill details and items
                                // $billDetailsArray['TrxStsCode'] = $gepgResponseCode;

                                // $updatedBillData = [
                                //     'PayCntrNum' => $billRes->PayCntrNum,
                                //     'TrxStsCode' => $gepgResponseCode,
                                // ];
                                // $this->billModel->updateBill($billRes->PayCntrNum, $updatedBillData);
                                // $endTime = microtime(true); // Record the start time
                                // return $this->response->setJSON([
                                //     'status' => 1,
                                //     'data' => $billDetailsArray,
                                //     'time' => $endTime - $startTime
                                // ]);
                                // exit;



                                // Fetch bill data
                                $controlNumber = $billRes->PayCntrNum;

                                // $updatedBillItems = $stickerLib->attachSticker($items,  $controlNumber);

                                // ================certificates=================================

                                // if (!empty($updatedBillItems)) $this->billModel->updateBillItems($updatedBillItems);




                                $billDetailsArray['PayCntrNum'] =  $controlNumber;
                                $bill = (object)$billDetailsArray;
                                $billItemsArray = array_map(fn($item) => $item['ItemName'], $items);
                                $billItems = implode(', ', $billItemsArray);

                                // Prepare SMS parameters
                                $center = wmaCenter($this->collectionCenter)->centerName;
                                $textParams = (object)[
                                    'payer' => $payer,
                                    'center' => $center,
                                    'amount' => $billAmount,
                                    'items' => $billItems,
                                    'expiryDate' => $expiryDate,
                                    'controlNumber' => (int)$controlNumber,
                                ];

                                $this->billModel->updateControlNumber($BillId, ['PayCntrNum' => $controlNumber]);

                                // Prepare bill data for JSON response
                                $billData = [
                                    'billItems' => $billItems,
                                    'billRef' => $bill->BillRef,
                                    'payerName' => $bill->PyrName,
                                    'payerPhone' => $bill->PyrCellNum,
                                    'amount' => 'TZS ' . number_format($bill->BillAmt),
                                    'payOption' => 3,
                                    'expireDate' => dateFormatter($bill->BillExprDt),
                                    'controlNumber' => $bill->PayCntrNum,
                                    'posCenter' => 'wma',
                                    'mobileNumber' => '0',
                                    'printedOn' => dateFormatter(date('Y-m-d')),
                                    'qrCodeObject' => [
                                        'opType' => '2',
                                        'shortCode' => '001001',
                                        'billReference' => $bill->PayCntrNum,
                                        'amount' => $bill->BillAmt,
                                        'billCcy' => 'TZS',
                                        'billExprDt' => $bill->BillExprDt,
                                        'billPayOpt' => $bill->BillPayOpt,
                                        'billRsv01' => "Weights And Measures Agency|$bill->PyrName"
                                    ],
                                    'trxStsCode' => $gepgResponseCode,
                                    'msg' => 'Bill Created Successfully',
                                    'attempt' => $attempt,
                                ];
                                $endTime = microtime(true); // Record the start time
                                // Return JSON response


                                return  $this->response->setJSON([
                                    'status' => 1,
                                    'data' => $billData,
                                    'DEBUG'
                                ]);

                                exit;

                                // log_message('BILL INFO', json_encode($billData));

                                function textTemplate($textParams)
                                {
                                    $billAmount = number_format($textParams->amount);
                                    $date = date('d/m/Y H:i:s');
                                    return  "$textParams->center inakutaarifu $textParams->payer kulipa deni lako Tsh $billAmount linalohusu maombi ya $textParams->items  kabla ya tarehe  $textParams->expiryDate kupitia control number $textParams->controlNumber . Tafadhali lipa sasa , BILL HII NI NOTISI (piga 0800110097 bure). $date";
                                }
                                // Return JSON response
                                $this->sms->sendSms(recipient: $phoneNumber, message: textTemplate($textParams));
                                return $this->response->setJSON([
                                    'status' => 1,
                                    'data' => $billData,
                                    'time' => $endTime - $startTime
                                ]);
                                // Send SMS
                            } else {
                                // If gepg response code is not 7101, return error codes and messages
                                // If gepg response code is not 7101, return error codes and messages
                                $errorCode = substr($gepgResponseCode, 0, 4);
                                return $this->response->setJSON([
                                    'status' => 0,
                                    'data' => [
                                        'msg' => tnxCode($errorCode),
                                        'trxStsCode' => $errorCode,
                                    ]

                                ]);
                            }
                            break;
                        } else {
                            // If $billRes is empty, wait for a short period before the next attempt
                            sleep(1);
                            $attempt++;
                        }
                    }

                    // If the loop completes without receiving the expected data, return an error
                    return $this->response->setJSON([
                        'status' => 0,
                        'attempts' => $attempt,
                        'msg' => 'Timeout: Try to search the bill if it is  created ,if not then submit again',
                    ])->setStatusCode(500);
                } else {
                    // Return the response for $code not equal to '7101'
                    return $this->response->setJSON([
                        'status' => 0,
                        'data' => [
                            'msg' => tnxCode($code),
                            'TrxStsCode' => $code,
                        ],
                    ])->setStatusCode(500);
                }
            }
        } catch (\Throwable $th) {

            return $this->response->setJSON([
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                    'trace' => $th->getTrace(),
                ]

            ])->setStatusCode(500);
        }
    }




    public function verifyPayment()
    {
        $apiToken = $this->request->getVar('apiToken');
        $controlNumbers = $this->request->getVar('controlNumbers');
        $payments = $this->billModel->getPayments($controlNumbers);
        return $this->response->setJSON([
            'status' => 1,
            'data' => $payments
        ]);
    }















































    //bill submission request to GePG

    public function serviceBillRequestTr()
    {
        //use ResponseTrait;

        //  return $this->response->setJSON([
        //    'status' => 0,
        //    'data' => [],
        //    'token' => randomString()
        //  ]);

        // //  exit;
        // $apiKey = $this->request->getHeaderLine('BILL_API_KEY');


        try {
            if ($this->request->getMethod() == 'POST') {

                $BillItemsObj = $this->request->getVar('BillItems');

                $items =  json_decode(json_encode($BillItemsObj), true);


                $BillId = $this->getVariable('billId');
                $gfsCode  = $this->getVariable('GfsCode');
                $PhysicalLocation  = $this->getVariable('PhysicalLocation');
                $billAmount   = (float)str_replace(',', '', $this->getVariable('BillAmt'));

                $currentDate = date("Y-m-d\TH:i:s");
                $expiryDate  = $this->getVariable('BillExprDt');
                $collectionCenter  = $this->getVariable('collectionCenter');
                $xpDate = $expiryDate . '23:59:59';
                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+360 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));

                $requestId = 'OSAREQ' . numString(10);



                // return  $this->response->setJSON([
                //     'data' => $items,
                // ]);

                // exit;
                $payer = $this->getVariable('PyrName');
                $phoneNumber = $this->getVariable('PyrCellNum');

                $billDetailsArray = [
                    'BillTyp' => 2,
                    'RequestId' => $requestId,
                    'CollCentCode' => 'CC1000000799419',
                    'CustId' => numString(5),
                    'CustIdTyp' =>  5,
                    'CustTin' => '',
                    'GrpBillId' => 'GRP' . numString(10),
                    'BillId' => $BillId,
                    'Activity' =>  implode(',', array_map(fn($item) => $item['GfsCode'], $items)),
                    'Task' =>  $items[0]['Task'],
                    'BillRef' => numString(10),
                    'BillAmt' => (float)  $billAmount,
                    'BillAmtWords' => toWords($billAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' => $BillExprDt,
                    'extendedExpiryDate' => $this->extendedExpiryDate,
                    'PyrId' => randomString(),
                    'PyrName' => preg_replace('/[\'"]/', '', $this->request->getVar('PyrName')),
                    'BillDesc' =>  $this->getVariable('BillDesc'),
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>  $payer,
                    'CollectionCenter' =>  $collectionCenter, //  $this->collectionCenter,
                    'BillApprBy' =>  'wma-hq',
                    'PyrCellNum' => '255' . substr($this->getVariable('PyrCellNum'), 1),
                    'PyrEmail' =>  '',
                    // 'Ccy' =>  $this->getVariable('Ccy'),
                    'Ccy' =>  'TZS',
                    'BillEqvAmt' => (float)   $billAmount,
                    'RemFlag' =>  $this->getVariable('RemFlag') == "on" ? 'true' : 'false',
                    'BillPayOpt' =>  (int)$this->getVariable('BillPayOpt'),
                    'UserId' =>  $this->uniqueId,
                    'PhysicalLocation' =>  $PhysicalLocation,
                    'latitude' => $this->getVariable('latitude'),
                    'longitude' => $this->getVariable('longitude'),
                    'deviceId' => $this->getVariable('deviceId'),
                    'method' =>   'MobileTransfer',
                    'UserId' =>  $this->uniqueId,
                    'SwiftCode' =>  '',

                ];

                $reqItems = (new ArrayLibrary($items))->map(function ($item) use ($BillId) {
                    $amount = str_replace(',', '',  $item['BillItemAmt']);
                    $item['BillItemRef'] =  randomString();
                    $item['UseItemRefOnPay'] = 'N';
                    $item['BillItemMiscAmt'] = 0.00;
                    $item['BillItemAmt'] = $amount;
                    $item['BillItemEqvAmt'] = $amount;
                    $item['UserId'] = $this->uniqueId;
                    $item['BillId'] = $BillId;
                    $item['ItemName'] =   $item['ItemName'] . ' ' .  $item['Capacity'] . ' ' .  $item['ItemUnit'];


                    return $item;
                })->get();



                //creating condemned instruments if any found


                // Initialize arrays for each key
                $BillItemRefArr = [];
                $UseItemRefOnPayArr = [];
                $BillItemMiscAmtArr = [];
                $BillItemAmtArr = [];
                $BillItemEqvAmtArr = [];
                $GfsCodeArr = [];
                $ItemNameArr = [];
                $UserIdArr = [];
                $StatusArr = [];
                // $StickerNumberArr = [];
                $TasksArr = [];
                $BillIdArr = [];
                $SingleItemAmountArr = [];
                $ItemQuantityArr = [];

                // Loop through each sub-array and populate the arrays
                foreach (array_values($reqItems) as $item) {
                    $BillItemAmtArr[] = $item['BillItemAmt'];
                    $BillItemMiscAmtArr[] = $item['BillItemMiscAmt'];
                    $BillItemRefArr[] = $item['BillItemRef'];
                    $BillItemEqvAmtArr[] = $item['BillItemEqvAmt'];
                    $GfsCodeArr[] = $item['GfsCode'];
                    $ItemNameArr[] = $item['ItemName'];
                    $UseItemRefOnPayArr[] = $item['UseItemRefOnPay'];
                    $UserIdArr[] = $item['UserId'];
                    $StatusArr[] = $item['Status'];
                    // $StickerNumberArr[] = !isset($item['StickerNumber']) ||$item['StickerNumber'] == null ? '' : $item['StickerNumber'];
                    $TasksArr[] = $item['Task'];
                    $SingleItemAmountArr[] = $item['SingleItemAmount'];
                    $ItemQuantityArr[] = $item['ItemQuantity'];
                    $BillIdArr[] = $item['BillId'];
                }

                //calculating next verification date for each item
                $nextVerification = array_map(fn($gfs) => nextVerification($gfs), $GfsCodeArr);
                $count = count($BillItemAmtArr);
                $itemsArray = [
                    'center' => fillArray($count, $this->collectionCenter),
                    'RefBillId' => fillArray($count, $BillId),
                    'SubSpCode' => fillArray($count, setting('Bill.wmaSubSpCode')),
                    'GfsCode' =>  fillArray($count, '142202080006'),
                    'BillItemRef' => $BillItemRefArr,
                    'UseItemRefOnPay' => $UseItemRefOnPayArr,
                    'BillItemAmt' =>  $BillItemAmtArr,
                    'BillItemEqvAmt' =>  $BillItemEqvAmtArr,
                    'CollSp' => fillArray($count, setting('Bill.wmaSpCode')),
                    'ItemName' => $ItemNameArr,
                    'NextVerification' => $nextVerification,
                    'Task' => $TasksArr,
                    'Status' => $StatusArr,
                    'UserId' => $UserIdArr,
                    'BillId' => $BillIdArr,
                    'RequestId' => fillArray($count, $requestId),
                    'ItemQuantity' => $ItemQuantityArr,
                    'SingleItemAmount' => $SingleItemAmountArr,

                ];

                $items =  multiDimensionArray($itemsArray);

                $billType = 2;
                $items85Percent = array_map(function ($item) use ($billType) {
                    $amount = $billType == 2 ? $item['BillItemAmt'] * 0.85 : $item['BillItemAmt'];
                    $item['BillItemAmt'] = $amount;
                    $item['BillItemEqvAmt'] = $amount;
                    return $item;
                }, $items);



                $wmaBill = billDataArray($billDetailsArray, 'wma');
                $trBill = billDataArray($billDetailsArray, 'tr');
                $wmaBill['BillItems'] = $items85Percent;
                $trBill['BillItems'] =  $items85Percent;
                $content = combinedBillContent($wmaBill, $trBill);
                file_put_contents('billXml.xml', formatXml($content));



                // return $this->response->setJSON([
                //  'status' => 0,
                //  'env' => setting('System.env'),
                // 'items' => $items,
                //  'bill' => $billDetailsArray,
                //  'wma bill' => $wmaBill,
                // ]);

                // exit;




                //switching uri and request headers based on type of request being sent
                $uri = "bill/20/submission";
                $GepgCom = "Gepg-Com:default.sp.in";


                $params = (object)[
                    "dataTag" => "gepgBillSubReqAck",
                    "uri" => $uri,
                    'GepgCom' => $GepgCom,
                    'spGroupCode' =>  setting('Bill.spGroupCodeCombined')
                ];



                $submission = $this->GepGpProcess->billSubmission(($content), $params);

                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => $submission,
                //     'token' => $this->token
                // ]);
                // exit;


                $responseAck = XML2Array::createArray($submission->resultCurlPost);
                $response = json_decode(json_encode($responseAck));
                $code = $response->Gepg->billSubReqAck->AckStsCode;
                // return $this->response->setJSON([
                // 'billId'=> $BillId,
                // 'res' => $response
                // ]);
                // exit;
                if ($code == '7101') {
                    $maxAttempts = 120; // Set a maximum number of attempts to avoid an infinite loop
                    $attempt = 0;
                    $startTime = microtime(true); // Record the start time
                    $billDetailsArray['TrxStsCode'] = $code;
                    $this->billModel->saveBill($wmaBill);
                    $this->billModel->saveTrBill($trBill);
                    //save bill items to database
                    $this->billModel->saveBillItems($items);
                    $this->billModel->saveTrBillItems($items);

                    $stickerLib = new StickerLibrary();
                    while ($attempt < $maxAttempts) { // Assuming $callbackData is the data received from the callback $billRes=$this->
                        $billRes = filterResponse($this->billModel->getBillResponse($requestId))[0];

                        if (!empty($billRes)) {
                            $statusCode = $billRes->billStatusCode;

                            // Check if the status code returned to the callback response code is 7101
                            if (strlen($statusCode) == 4 && $statusCode == '7101') {
                                // Your existing code for processing successful response

                                // Save bill details and items
                                // $billDetailsArray['TrxStsCode'] = $statusCode;

                                // $updatedBillData = [
                                //     'PayCntrNum' => $billRes->PayCntrNum,
                                //     'TrxStsCode' => $statusCode,
                                // ];
                                // $this->billModel->updateBill($billRes->PayCntrNum, $updatedBillData);
                                // $endTime = microtime(true); // Record the start time
                                // return $this->response->setJSON([
                                //     'status' => 1,
                                //     'data' => $billDetailsArray,
                                //     'time' => $endTime - $startTime
                                // ]);
                                // exit;

                                //// Fetch bill data
                                $bill = (object)$billDetailsArray;
                                $controlNumber = $billRes->controlNumber;

                                $updatedBillItems = $stickerLib->attachSticker($items,  $controlNumber);

                                // ================certificates=================================






                                $billDetailsArray['PayCntrNum'] =  $controlNumber;

                                $billItemsArray = array_map(fn($item) => $item['ItemName'], $items);
                                $billItems = implode(', ', $billItemsArray);

                                // Prepare SMS parameters
                                $center = 'Wakala Wa Vipimo';
                                $textParams = (object)[
                                    'payer' => $payer,
                                    'center' => $center,
                                    'amount' => $billAmount,
                                    'items' => $billItems,
                                    'expiryDate' => $expiryDate,
                                    'controlNumber' => (int)$controlNumber,
                                ];



                                // Prepare bill data for JSON response
                                $billData = [
                                    'billItems' => $billItems,
                                    'billRef' => $bill->BillRef,
                                    'payerName' => $bill->PyrName,
                                    'payerPhone' => $bill->PyrCellNum,
                                    'amount' => 'TZS ' . number_format($bill->BillAmt),
                                    'payOption' => $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                                    'expireDate' => dateFormatter($bill->BillExprDt),
                                    'controlNumber' => $controlNumber,
                                    // 'posCenter' => ',
                                    // 'mobileNumber' => wmaCenter()->mobileNumber,
                                    'mobileNumber' => $bill->PyrCellNum,

                                    'trxStsCode' => $statusCode,
                                    'msg' => 'Bill Created Successfully',
                                    'attempt' => $attempt,
                                ];
                                $endTime = microtime(true); // Record the start time


                                function textTemplate($textParams)
                                {
                                    $billAmount = number_format($textParams->amount);
                                    $date = date('d/m/Y H:i:s');
                                    return  "$textParams->center inakutaarifu $textParams->payer kulipa deni lako Tsh $billAmount linalohusu maombi ya $textParams->items  kabla ya tarehe  $textParams->expiryDate kupitia control number $textParams->controlNumber . Tafadhali lipa sasa , BILL HII NI NOTISI (piga 0800110097 bure). $date";
                                }
                                // Return JSON response
                                $this->sms->sendSms(recipient: $phoneNumber, message: textTemplate($textParams));
                                return $this->response->setJSON([
                                    'status' => 1,
                                    'data' => $billData,
                                    'time' => $endTime - $startTime
                                ]);
                                // Send SMS
                            } else {
                                // If gepg response code is not 7101, return error codes and messages
                                // If gepg response code is not 7101, return error codes and messages
                                $errorCode = substr($statusCode, 0, 4);
                                return $this->response->setJSON([
                                    'status' => 0,
                                    'requestId' => $requestId,
                                    'data' => [
                                        'msg' => tnxCode($errorCode),
                                        'trxStsCode' => $errorCode,
                                    ]

                                ]);
                            }
                            break;
                        } else {
                            // If $billRes is empty, wait for a short period before the next attempt
                            sleep(1);
                            $attempt++;
                        }
                    }

                    // If the loop completes without receiving the expected data, return an error
                    return $this->response->setJSON([
                        'status' => 0,
                        'requestId' => $requestId,
                        'attempts' => $attempt,
                        'msg' => 'Timeout: Try to search the bill if it is  created ,if not then submit again',
                    ])->setStatusCode(500);
                } else {
                    // Return the response for $code not equal to '7101'
                    return $this->response->setJSON([
                        'status' => 0,
                        'requestId' => $requestId,
                        'data' => [
                            'msg' => tnxCode($code),
                            'TrxStsCode' => $code,
                        ],
                    ])->setStatusCode(500);
                }
            }
        } catch (\Throwable $th) {

            return $this->response->setJSON([
                'status' => 0,
                'err' => true,
                'data' => [
                    'msg' => $th->getMessage(),
                    'trace' => $th->getTrace(),
                    'RESPONSE' => $response,
                ]

            ])->setStatusCode(500);
        }
    }


    public function osaActivation()
    {

        try {

            $apiKey = $this->request->getVar('apiKey');

            if (!$this->validateApiKey($apiKey)) {

                $error =   [
                    'message' => 'Invalid API KEY'
                ];
                return $this->response->setJSON($error)->setStatusCode(401);
            }



            $userId = $this->request->getVar('userId');
            $email = $this->request->getVar('email');
            $name = $this->request->getVar('name');

            //  return $this->response->setJSON([
            //    'status' => 0,
            //    'data' => [$email,$name],
            //    'token' => $this->token
            //  ]);


            $token = randomString(10);

            $emailService  = \Config\Services::email();

            $data['link'] = "https://osa.wma.go.tz/activateAcount/$userId/$token";
            $data['name'] = $name;
            // $emailService->setFrom('info@wma.co.tz', 'OSA');
            $emailService->setTo($email);
            $emailService->setSubject('OSA Account Activation');
            // $message = view('osaActivation', $data);
            $emailService->setMessage('Osa Account Activation ');


            if ($emailService->send()) {
                return $this->response->setJSON([
                    'status' => 1,
                    'msg' => 'Email Sent',
                    //    'token' => $this->token
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Error',
                    //    'token' => $this->token
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
}
