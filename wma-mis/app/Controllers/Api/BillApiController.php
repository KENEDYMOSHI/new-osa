<?php

namespace App\Controllers\Api;

use DateTime;
use DateInterval;
use LSS\Array2XML;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Models\WmaBillModel;
use App\Libraries\SmsLibrary;
use App\Libraries\XmlLibrary;
use App\Libraries\GepgProcess;
use App\Libraries\ArrayLibrary;
use App\Libraries\StickerLibrary;
use App\Libraries\WmaGepgProcess;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\CertificateLibrary;
use CodeIgniter\RESTful\ResourceController;



class BillApiController extends ResourceController
{
    protected $billModel;
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

    use ResponseTrait;

    public function __construct()
    {


        helper('setting');
        helper(setting('App.helpers'));
        $this->billModel = new WmaBillModel();
        $this->collectionCenters = $this->billModel->getCollectionCenters();
        $this->xmlLibrary = new XmlLibrary();
        $this->GepGpProcess = new WmaGepgProcess();
        $this->profileModel = new profileModel();
        $this->uniqueId = auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;
        $this->sms = new SmsLibrary();
        $this->SpCode = 'SP419'; //setting('Bill.spCode');
        $this->subSpCode = '1002'; // setting('Bill.subSpCode');
        $this->systemId = 'LWMA002';
        $this->extendedExpiryDate = (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s');
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }






    //bill submission request to GePG

    //bill submission request to GePG
    public function billSubmissionRequest()
    {
        //use ResponseTrait;








        try {
            if ($this->request->getMethod() == 'POST') {


                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => $this->request->getVar('BillEqvAmt'),
                //     'token' => randomString()
                // ]);

                // exit;
                $BillItemsObj = $this->request->getVar('BillItems');

                $items =  json_decode(json_encode($BillItemsObj), true);


                $BillId = randomString();
                $gfsCode  = $this->getVariable('GfsCode');
                $PhysicalLocation  = $this->getVariable('PhysicalLocation');
                $billAmount   = (float)str_replace(',', '', $this->getVariable('BillAmt'));

                $currentDate = date("Y-m-d\TH:i:s");
                $expiryDate  = $this->getVariable('BillExprDt');
                $xpDate = $expiryDate . '23:59:59';
                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+7 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));





                // return  $this->response->setJSON([
                //   'data' => $items,
                // ]);


                $payer = $this->getVariable('PyrName');
                $phoneNumber = $this->getVariable('PyrCellNum');

                $billDetailsArray = [

                    'BillId' => $BillId,
                    'RequestId' => $BillId,
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
                    'BillGenBy' =>   auth()->user()->username,
                    'CollectionCenter' =>   $this->collectionCenter,
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

                ];




                // return  $this->response->setJSON([
                //     'data' => $items,
                // ]);

                // exit;




                $reqItems = (new ArrayLibrary($items))->map(function ($item) use ($BillId) {
                    $amount = str_replace(',', '',  $item['BillItemAmt']);
                    $item['BillItemRef'] =  randomString();
                    $item['UseItemRefOnPay'] = 'N';
                    $item['BillItemMiscAmt'] = 0.00;
                    $item['BillItemAmt'] = $amount;
                    $item['BillItemEqvAmt'] = $amount;
                    $item['UserId'] = $this->uniqueId;
                    $item['BillId'] = $BillId;
                    $item['center'] = $this->collectionCenter;
                    $item['ItemName'] =   $item['ItemName'] . ' ' .  $item['Capacity'] . ' ' .  $item['ItemUnit'];


                    return $item;
                })->get();

                $condemned = (new ArrayLibrary($reqItems))->filter(fn($item) => $item['Status'] == 'Condemned')->map(fn($item) => [
                    'customer' => $this->getVariable('PyrName'),
                    'task' => $item['Task'],
                    'item' => $item['ItemName'] . ' ' .  $item['Capacity'] . ' ' .  $item['ItemUnit'],
                    'activity' => $item['GfsCode'],
                    'status' => $item['Status'],
                    'collectionCenter' => $this->collectionCenter,
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
                $CenterArr = [];

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
                    $CenterArr[] = $item['center'];
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
                    'center' => $CenterArr,

                ];

                $items =  multiDimensionArray($itemsArray);



                // return $this->response->setJSON([
                //     'status' => 0,
                //     'env' => setting('System.env'),
                //     'items' => arrayExcept($items, ['ItemName', 'UserId', 'Task', 'Status', 'ItemQuantity', 'SingleItemAmount', 'BillId','RequestId', 'NextVerification']),
                //     'billDetailsArray' => $billDetailsArray,
                // ]);


                // exit;



                $xml = Array2XML::createXML('BillItems', ['BillItem' => arrayExcept($items, ['center','ItemName', 'UserId', 'Task', 'Status', 'ItemQuantity', 'SingleItemAmount', 'BillId','RequestId', 'NextVerification'])])->saveXML();
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



                $submission = $this->GepGpProcess->billSubmission(formatXml($content), $params);

                $response = XML2Array::createArray($submission->resultCurlPost);


                $code = $response['Gepg']['gepgBillSubReqAck']['TrxStsCode'];
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
                    $this->billModel->saveBill($billDetailsArray); //save bill details
                    $this->billModel->saveBillItems($items); // save bill items

                    $stickerLib = new StickerLibrary();
                    while ($attempt < $maxAttempts) { // Assuming $callbackData is the data received from the callback $billRes=$this->
                        $billRes =  $this->billModel->getBillResponse($BillId);

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

                                $updatedBillItems = $stickerLib->attachSticker($items,  $controlNumber);

                                // ================certificates=================================
                                $certificateData = (object)[

                                    'customer' => $billDetails->PyrName,
                                    'activity' => json_encode($GfsCodeArr),
                                    'mobile' => $billDetails->PyrCellNum,
                                    'address' => 'P O Box',
                                    'items' =>  json_encode($ItemNameArr),
                                    'controlNumber' =>  $controlNumber,

                                ];
                                //adding certificate data
                                (new CertificateLibrary())->createCertificateData($certificateData);
                                if (!empty($updatedBillItems)) $this->billModel->updateBillItems($updatedBillItems);




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
                                    'payOption' => $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                                    'expireDate' => dateFormatter($bill->BillExprDt),
                                    'controlNumber' => $bill->PayCntrNum,
                                    'posCenter' => centerName(),
                                    'mobileNumber' => wmaCenter()->mobileNumber,
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
                                $this->sms->sendSms(recipient: $phoneNumber, message: billTextTemplate($textParams));
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







    //bill submission request to GePG
    public function billRenewRequest()
    {
        //use ResponseTrait;

        //exit;

        try {
            if ($this->request->getMethod() == 'POST') {
                $billId = $this->getVariable('billId');
                $percentage = 1;
                $currentDate = new DateTime(); // Current date and time

                // Fetch bill and bill items from the model
                $bill = $this->billModel->fetchBill($billId);
                $billItems = $this->billModel->fetchBillItems($billId);

                if (empty($bill)) return $this->response->setJSON([
                    'status' => 0,
                    'data' => [],
                    'msg' => 'Invalid Bill ID'
                ])->setStatusCode(500);

                if (empty($billItems)) return $this->response->setJSON([
                    'status' => 0,
                    'data' => [],
                    'msg' => 'Invalid Bill Items'
                ])->setStatusCode(500);

                $oldBillAmount = (float)$bill->BillAmt;

                $generatedDate = new DateTime($bill->BillGenDt);
                $expiredDate = new DateTime($bill->BillExprDt);

                $oldBillDays = $generatedDate->diff($expiredDate)->days;

                $daysPassed = $currentDate->diff($expiredDate)->days;


                $billDays = $oldBillDays == 0 ? 1 : $oldBillDays;
                // Calculate new percentage based on the ratio of days passed
                $newPercentage = intval($daysPassed / $billDays) * $percentage;



                // Calculate penalty amount based on the new percentage
                $penaltyAmount = ($newPercentage / 100) * $oldBillAmount;


                $newBillAmount = $penaltyAmount + $oldBillAmount;

                // Calculate new expiration date by adding the old days to the current date
                $newExpireDate = $currentDate->add(new DateInterval("P{$oldBillDays}D"))->format('Y-m-d\TH:i:s');

                // Format the current date as the new generated date
                $newGeneratedDate = (new DateTime())->format('Y-m-d\TH:i:s');

                $newBillId = randomString();



                // return $this->response->setJSON([
                // 'newPercentage' => $newPercentage,
                // 'oldBillDays' => $billDays,
                // 'daysPassed' => $daysPassed,
                // 'og amount' => $bill->BillAmt,
                // 'penaltyAmount' => $penaltyAmount,
                // // 'THE BILL' => $bill,
                // 'BILL ITEMS' => $billItems,
                // ]);

                // exit;





                $billDetailsArray = [

                    'BillId' => $newBillId,
                    'BillAmt' => $newBillAmount,
                    'BillAmt' => $newBillAmount,
                    'BillAmtWords' => toWords($newBillAmount),
                    'BillExprDt' => $newExpireDate,
                    'BillGenDt' => $newGeneratedDate,
                    'BillGenBy' => auth()->user()->username,


                ];

                $itemCount = count($billItems);
                $billItemAmount = $newBillAmount / $itemCount;

                $items = (new ArrayLibrary($billItems))->map(fn($item) => [
                    'id' => $item->id,
                    'BillId' => $newBillId,
                    'BillItemRef' => $item->BillItemRef,
                    'UseItemRefOnPay' => $item->UseItemRefOnPay,
                    'BillItemAmt' => $item->BillItemAmt,
                    'BillItemEqvAmt' => $item->BillItemAmt,
                    'BillItemMiscAmt' => $item->BillItemMiscAmt,
                    'GfsCode' => $item->GfsCode,

                ])->get();










                // return $this->response->setJSON([
                //     'ID' => $bill->theId,
                //     // 'daysPassed' => $daysPassed,
                //     // 'newExpireDate' => $newExpireDate,
                //     // 'oldBillDays' => $oldBillDays,
                //     // 'newPercentage' => $newPercentage,
                //     // 'penaltyAmount' => $penaltyAmount,
                //     //  'bill' => $billDetailsArray,
                //     'billItems' => $items,
                // ]);


                // exit;

                $xml = Array2XML::createXML('BillItems', ['BillItem' => arrayExcept($items, ['id', 'BillId'])])->saveXML();
                $BillItems = ltrim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml));


                //$content = "";
                $content = "<gepgBillSubReq>
                    <BillHdr>
                        <SpCode>$this->SpCode</SpCode>
                        <RtrRespFlg>true</RtrRespFlg>
                    </BillHdr>
                    <BillTrxInf>
                        <BillId>$newBillId</BillId>
                        <SubSpCode>$this->subSpCode</SubSpCode>
                        <SpSysId>$this->systemId</SpSysId>
                        <BillAmt>$newBillAmount</BillAmt>
                        <MiscAmt>0.00</MiscAmt>
                        <BillExprDt>$this->extendedExpiryDate</BillExprDt>
                        <PyrId>$bill->PyrId</PyrId>
                        <PyrName>$bill->PyrName</PyrName>
                        <BillDesc>$bill->BillDesc</BillDesc>
                        <BillGenDt>$bill->BillGenDt</BillGenDt>
                        <BillGenBy>$bill->BillGenBy</BillGenBy>
                        <BillApprBy>$bill->BillApprBy</BillApprBy>
                        <PyrCellNum>$bill->PyrCellNum</PyrCellNum>
                        <PyrEmail>$bill->PyrEmail</PyrEmail>
                        <Ccy>$bill->Ccy</Ccy>
                        <BillEqvAmt>$newBillAmount</BillEqvAmt>
                        <RemFlag>$bill->RemFlag</RemFlag>
                        <BillPayOpt>$bill->BillPayOpt</BillPayOpt>
                        <PayCntrNum>$bill->PayCntrNum</PayCntrNum>
                        " . $BillItems . "
                    </BillTrxInf>
                </gepgBillSubReq>";





                //switching uri and request headers based on type of request being sent
                $uri = "bill/sigqrequest_reuse";
                $GepgCom = "Gepg-Com:reusebill.sp.in";






                $params = (object)[
                    "dataTag" => "gepgBillSubReqAck",
                    "uri" => $uri,
                    'GepgCom' => $GepgCom,
                ];

                $submission = $this->GepGpProcess->billSubmission(formatXml($content), $params);

                $response = XML2Array::createArray($submission->resultCurlPost);


                $code = $response['Gepg']['gepgBillSubReqAck']['TrxStsCode'];
                // return $this->response->setJSON([
                // // 'billId'=> $BillId,
                // 'res' => $response
                // ]);
                // exit;
                if ($code == '7101') {

                    $maxAttempts = 60; // Set a maximum number of attempts to avoid an infinite loop
                    $attempt = 0;
                    $startTime = microtime(true); // Record the start time
                    while ($attempt < $maxAttempts) {
                        $billRes = $this->billModel->getBillResponse($newBillId);

                        if (!empty($billRes)) {
                            $gepgResponseCode = $billRes->resCode;

                            //get first TnxCode in case api returns multiple codes
                            $TrxStsCode = substr($billRes->resCode, 0, 4);


                            if (strlen($gepgResponseCode) == 4 && $gepgResponseCode == '7101') {

                                // $itemsArray['BillId'] = fillArray($count, $BillId);
                                $billDetailsArray['PayCntrNum'] = $billRes->PayCntrNum;
                                $billDetailsArray['TrxStsCode'] = $TrxStsCode;
                                $this->billModel->updateBill($billRes->PayCntrNum, $billDetailsArray);
                                // $saveBill = true;

                                $this->billModel->updateBillItems($items);
                                $bill = $this->billModel->fetchBill($billDetailsArray['BillId']);

                                $billItemsArray = array_map(
                                    fn($item) => $item->ItemName,
                                    $this->billModel->fetchBillItems($billDetailsArray['BillId'])
                                );

                                $billItems = implode(', ', $billItemsArray);


                                $billData = [
                                    'oldAmount' => 'TZS ' . number_format($oldBillAmount),
                                    'interest' => $newPercentage . '%',
                                    'billItems' => $billItems,
                                    'billRef' => $bill->BillRef,
                                    'payerName' => $bill->PyrName,
                                    'payerPhone' => $bill->PyrCellNum,
                                    'billItems' => $billItems,
                                    'amount' => 'TZS ' . number_format($bill->BillAmt),
                                    'payOption' => $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                                    'expireDate' => dateFormatter($bill->BillExprDt),
                                    'controlNumber' => $bill->PayCntrNum,
                                    'posCenter' => centerName(),
                                    'mobileNumber' => wmaCenter()->mobileNumber,
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
                                    'trxStsCode' => $TrxStsCode,
                                    'msg' => 'Bill Renewed Successfully',

                                ];


                                return $this->response->setJSON([
                                    'status' => 1,
                                    'data' => $billData




                                ]);
                            } else {
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
                            sleep(1);
                            $attempt++;
                        }
                    }


                    // If the loop completes without receiving the expected data, return an error
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Timeout: Unable to Process Bill Try Again',
                    ])->setStatusCode(500);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'data' => [
                            'msg' => tnxCode($code),
                            'TrxStsCode' => $code,
                        ]

                    ]);
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






























































    //=================################################====================









    //canceling generated bill
    public function billCancellation()
    {


        try {
            if ($this->request->getMethod() == 'POST') {

                $billId = $this->getVariable('billId');
                $cancelReason = $this->getVariable('reason');

                $req = [
                    'BillId' => $billId,
                    'CanclReasn' => $cancelReason,
                    'CanceledBy' => auth()->user()->username,
                ];
                $content =
                    "<gepgBillCanclReq>
            <SpCode>$this->SpCode</SpCode>
            <SpSysId>$this->SpSysId</SpSysId>
            <CanclReasn>$cancelReason</CanclReasn>
            <BillId>$billId</BillId>
        </gepgBillCanclReq>";

                // $this->billModel->saveCancellationRequest($req);

                // return $this->response->setJSON([
                // $req
                // ]);
                // exit;

                $params = (object)[
                    "dataTag" => "gepgBillCanclResp",
                    "uri" => "bill/sigcancel_request",
                    'GepgCom' => 'Gepg-Com:default.sp.in',
                ];
                $submission = $this->GepGpProcess->billSubmission(formatXml($content), $params);

                if ($submission->status == 1) {
                    $array = XML2Array::createArray($submission->resultCurlPost);

                    $data = $array['Gepg']['gepgBillCanclResp']['BillCanclTrxDt'];
                    $txCode = $data['TrxStsCode'];
                    $id = $data['BillId'];

                    if ($txCode == '7283') {
                        $this->billModel->saveCancellationRequest($req);
                        $this->billModel->updateCancellationStatus($id, ['IsCancelled' => 'Yes']);
                        return $this->response->setJSON([
                            'status' => 1,
                            'data' => [
                                'msg' => 'Bill Has Been Cancelled',
                                'billId' => $id,
                                'trxStsCode' => $txCode,
                            ]
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'status' => 0,
                            'data' => [
                                'msg' => tnxCode($txCode),
                                'billId' => $id,
                                'trxStsCode' => $txCode,
                            ]

                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'data' => [
                            'token' => $this->token,
                            'msg' => $submission->msg,
                        ]


                    ])->setStatusCode(500);
                }

                // return $this->response->setJSON([$submission]);
                // exit;
                //converting gepg xml response to array

            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]
            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);
    }








    public function searchBill()
    {
        try {
            $keyword = $this->getVariable('keyword');
            $paymentStatus = $this->getVariable('paymentStatus');
            $date = $this->getVariable('date');


            $billParams = [
                'PaymentStatus' => $paymentStatus,
                'CollectionCenter' => $this->collectionCenter,
                'DATE(wma_bill.CreatedAt)' => $date != '' ? date("Y-m-d", strtotime($date)) : '',
            ];

            foreach ($billParams as $key => $value) {
                if ($value == '') {
                    unset($billParams[$key]);
                }
            }


            $request = $this->billModel->searchBillApi($billParams, $keyword);
            // return $this->response->setJSON([$billParams]);
            // exit;




            if ($request) {


                $bills = array_map(function ($bill) {
                    $billItemsArray = array_map(fn($item) => $item->ItemName, $this->billModel->fetchBillItems($bill->BillId));
                    $expirationDate = $bill->BillExprDt;
                    $billItems = implode(', ', $billItemsArray);
                    $currentDate = new DateTime();
                    $expDate = new DateTime($expirationDate);
                    return [
                        'billId' => $bill->BillId,
                        'billRef' => $bill->BillRef,
                        'billItems' => $billItems,
                        'date' => dateFormatter($bill->CreatedAt),
                        'payerName' => $bill->PyrName,
                        'payerPhone' => $bill->PyrCellNum,
                        'paymentStatus' => $bill->PaymentStatus,
                        'billDescription' => $bill->BillDesc,
                        'amount' => 'TZS ' . number_format($bill->BillAmt),
                        'paidAmount' => 'TZS ' . number_format($bill->PaidAmount),
                        'payOption' => $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                        'controlNumber' => $bill->PayCntrNum,
                        'posCenter' => centerName(),
                        'mobileNumber' => wmaCenter()->mobileNumber,
                        'printedOn' => dateFormatter(date('Y-m-d')),
                        'userCanCancel' => $bill->UserId == $this->uniqueId ? 1 : 0,
                        'isExpired' => $currentDate > $bill->extendedExpiryDate ? 1 : 0,


                        'expireDate' => dateFormatter($expirationDate),
                        'qrCodeObject' => [
                            'opType' => '2',
                            'shortCode' => '001001',
                            'billReference' => $bill->PayCntrNum,
                            'amount' => $bill->BillAmt,
                            'billCcy' => 'TZS',
                            'billExprDt' => $expirationDate,
                            'billPayOpt' => $bill->BillPayOpt,
                            'billRsv01' => "Weights And Measures Agency|$bill->PyrName"
                        ],
                    ];
                }, $request);


                return $this->response->setJSON([

                    'status' => 1,
                    'data' => $bills
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 1,
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                    'trace' => $th->getTrace(),
                ]

            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);


        // return $this->response->setJSON([$bill]);
    }
    public function searchPaymentReceipt()
    {
        try {
            $keyword = $this->getVariable('keyword');
            $paymentStatus = $this->getVariable('paymentStatus');
            $date = $this->getVariable('date');


            $billParams = [
                'IsCancelled' => 'No',
                'PaymentStatus' => $paymentStatus,
                'wma_bill.CollectionCenter' => $this->collectionCenter,
                'DATE(wma_bill.CreatedAt)' => $date != '' ? date("Y-m-d", strtotime($date)) : '',
            ];

            foreach ($billParams as $key => $value) {
                if ($value == '') {
                    unset($billParams[$key]);
                }
            }



            $request = $this->billModel->searPaymentApi($billParams, $keyword);




            if ($request) {


                $payments = array_map(function ($payment) {
                    $billItemsArray = array_map(fn($item) => $item->ItemName, $this->billModel->fetchBillItems($payment->BillId));

                    $billItems = implode(', ', $billItemsArray);
                    return [
                        'billId' => $payment->BillId,
                        'paymentReference' => $payment->PayRefId,
                        'paymentReceipt' => $payment->PspReceiptNumber,
                        'billItems' => $billItems,
                        'date' => dateFormatter($payment->TrxDtTm),
                        'payerName' => $payment->PyrName,
                        'payerPhone' => $payment->PyrCellNum,
                        'billedAmount' => 'TZS ' . number_format($payment->BillAmt),
                        'paidAmount' => 'TZS ' . number_format($payment->PaidAmt),
                        'paymentStatus' => $payment->PaymentStatus,
                        'outstanding' => 'TZS ' . number_format($payment->BillAmt - $payment->PaidAmt),
                        'controlNumber' => $payment->PayCntrNum,



                    ];
                }, $request);


                return $this->response->setJSON([

                    'status' => 1,
                    'data' => $payments

                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 1,
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]


            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);


        // return $this->response->setJSON([$bill]);
    }


    public function selectBill()
    {
        try {
            $billId = $this->getVariable('billId');




            $bill = $this->billModel->fetchBill($billId);


            if (!empty($bill)) {
                $billItems = $this->billModel->fetchBillItems($billId);


                $billItemsArray = array_map(fn($item) => $item->ItemName, $this->billModel->fetchBillItems($billId));

                $billItems = implode(', ', $billItemsArray);

                $billData = [
                    'payerName' => $bill->PyrName,
                    'billRef' => $bill->BillRef,
                    'payerPhone' => $bill->PyrCellNum,
                    'billItems' => $billItems,
                    'billDescription' => $bill->BillDesc,
                    'amount' => 'TZS ' . number_format($bill->BillAmt),
                    'payOption' => $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                    'expireDate' => dateFormatter($bill->BillExprDt),
                    'controlNumber' => $bill->PayCntrNum,
                    'posCenter' => centerName(),
                    'mobileNumber' => wmaCenter()->mobileNumber,
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
                    ]

                ];




                return $this->response->setJSON([
                    'status' => 1,
                    'data' => $billData


                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 1,
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]

            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);
    }
    public function billRenewRequest5()
    {
        try {
            $billId = $this->getVariable('billId');




            $bill = $this->billModel->fetchBill($billId);


            if (!empty($bill)) {
                $billItems = $this->billModel->fetchBillItems($billId);


                $billItemsArray = array_map(fn($item) => $item->ItemName, $this->billModel->fetchBillItems($billId));

                $billItems = implode(', ', $billItemsArray);

                $billData = [
                    'payerName' => $bill->PyrName,
                    'billRef' => $bill->BillRef,
                    'payerPhone' => $bill->PyrCellNum,
                    'billItems' => $billItems,
                    'billDescription' => 'Renew: ' . $bill->BillDesc,
                    'amount' => 'TZS ' . number_format($bill->BillAmt + 125000),
                    'payOption' => $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                    'expireDate' => dateFormatter($bill->BillExprDt),
                    'controlNumber' => $bill->PayCntrNum,
                    'posCenter' => centerName(),
                    'mobileNumber' => wmaCenter()->mobileNumber,
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
                    ]

                ];




                return $this->response->setJSON([
                    'status' => 1,
                    'data' => $billData


                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 1,
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]

            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);
    }
    public function selectPaymentReceipt()
    {
        try {
            $paymentRef = $this->getVariable('paymentRef');


            $payment = $this->billModel->fetchPayment($paymentRef);

            if (!empty($payment)) {


                $billItemsArray = array_map(fn($item) => $item->ItemName, $this->billModel->fetchBillItems($payment->BillId));

                $billItems = implode(', ', $billItemsArray);

                $receipt = [
                    'billId' => $payment->BillId,
                    'paymentReference' => $payment->PayRefId,
                    'billItems' => $billItems,
                    'paymentReceipt' => $payment->PspReceiptNumber,
                    'date' => dateFormatter($payment->TrxDtTm),
                    'payerName' => $payment->PyrName,
                    'payerPhone' => $payment->PyrCellNum,
                    'billedAmount' => 'TZS ' . number_format($payment->BillAmt),
                    'paidAmount' => 'TZS ' . number_format($payment->PaidAmt),
                    'outstanding' => 'TZS ' . number_format($payment->BillAmt - $payment->PaidAmt),
                    'controlNumber' => $payment->PayCntrNum,

                ];




                return $this->response->setJSON([
                    'status' => 1,
                    'data' => $receipt
                    // 'logo' => getImage('assets/images/wma1.png')


                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'data' => [
                        'receipt' => []
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]

            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);
    }

    public function billCancellationRequest()
    {
        try {
            $billId = $this->getVariable('billId');

            $data = [
                'billId' => $billId,
                'reason' => $this->getVariable('reason'),
                'requestBy' => auth()->user()->username,
                'centerNumber' => auth()->user()->collection_center,
                'centerName' => centerName(),
                'userId' => auth()->user()->unique_id,
            ];




            $request = $this->billModel->saveCancellationRequest($data);

            if ($request) {
                return $this->response->setJSON([
                    'status' => 1,
                    'data' => [
                        'msg' => 'Bill Cancellation Request Sent',
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'data' => [
                        'msg' => 'Something Went Wrong'
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]

            ];

            return $this->response->setJSON($response)->setStatusCode(500);
        }
        // return $this->response->setJSON($response);
    }

    public function revenueSources()
    {

        $gfsCodes = [
            ['name' => 'Vehicle Tank Verification (VTV)', 'code' => '142101210003'],
            ['name' => 'Wb - Weighbridge', 'code' => '142101210004'],
            ['name' => 'FST – Fixed Storage Tank', 'code' => '142101210005'],
            ['name' => 'Bulk Storage Tank (BST)', 'code' => '142101210006'],
            ['name' => 'Pre-packages', 'code' => '142101210007'],
            ['name' => 'WGT – Wagon Tank', 'code' => '142101210008'],
            ['name' => 'Fuel pump', 'code' => '142101210009'],
            ['name' => 'CNG filling Station', 'code' => '142101210010'],
            ['name' => 'F/M – Flow Meter', 'code' => '142101210011'],
            ['name' => 'Ch/p - check pump', 'code' => '142101210012'],
            ['name' => 'Meter', 'code' => '142101210013'],
            ['name' => 'Metrological Supervision (On Board & Shore Tanks)', 'code' => '142101210014'],
            ['name' => 'Pressure gauges', 'code' => '142101210015'],
            ['name' => 'Proving Tank', 'code' => '142101210016'],
            ['name' => 'Taximeter', 'code' => '142101210017'],
            ['name' => 'MR - Metre Rule', 'code' => '142101210018'],
            ['name' => 'TM - Tape Measure', 'code' => '142101210019'],
            // ['name' => 'M. LE - Measures of Length', 'code' => '142101210020'],
            ['name' => 'BRIM - Brim Measure system', 'code' => '142101210021'],
            ['name' => 'S/y – Steelyard', 'code' => '142101210022'],
            ['name' => 'SDw -Suspended Digital Ware', 'code' => '142101210023'],
            ['name' => 'C/S - Counter scale', 'code' => '142101210024'],
            ['name' => 'P/s - Platform scale', 'code' => '142101210025'],
            ['name' => 'S/B - Spring Balance', 'code' => '142101210026'],
            ['name' => 'Bal - Balance', 'code' => '142101210027'],
            ['name' => 'Kor - Koroboi', 'code' => '142101210028'],
            ['name' => 'Vib – Vibaba', 'code' => '142101210029'],
            ['name' => 'Pis – Pishi', 'code' => '142101210030'],
            ['name' => 'Ax/w - Weigher', 'code' => '142101210031'],
            ['name' => 'Au/W - Automatic Weigher', 'code' => '142101210032'],
            ['name' => 'B/S - Beam Scale', 'code' => '142101210033'],
            ['name' => 'S/y – Steelyard', 'code' => '142101210034'],
            ['name' => 'Sandy & Ballast lorry (SBL)', 'code' => '142101210035'],
            ['name' => 'E/m- Electricity meter', 'code' => '142101210036'],
            ['name' => 'OMI - Other Measuring Instrument', 'code' => '142101210037'],
            ['name' => 'OML - Other Measures of Length', 'code' => '142101210038'],
            ['name' => 'DM - Domestic gas meter', 'code' => '142101210039'],
            ['name' => 'WT - Weights', 'code' => '142101210040'],
            ['name' => 'Miscellaneous Receipts', 'code' => '142201611278'],
            ['name' => 'Fines, Penalties and Forfeitures', 'code' => '142202080006'],
        ];

        // $except = ['142101210035', '142101210013', '142101210007', '142101210003'];
        $except = [setting('Gfs.vtv'), setting('Gfs.sbl'), setting('Gfs.waterMeter'), setting('Gfs.prePackage')];

        $codes = array_filter($gfsCodes, function ($value) use ($except) {
            return !in_array($value['code'], $except);
        });



        return $this->response->setJSON([
            'data' => array_values($codes),
        ]);
    }
    public function units()
    {

        $units = [
            ['name' => 'Milliliter', 'symbol' => 'ml'],
            ['name' => 'Liter', 'symbol' => 'l'],
            ['name' => 'Milligram', 'symbol' => 'mg'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Millimeter', 'symbol' => 'mm'],
            ['name' => 'Centimeter', 'symbol' => 'cm'],
            ['name' => 'Meter', 'symbol' => 'm'],
            ['name' => 'Cubic Meter', 'symbol' => 'm3'],
            ['name' => 'Cubic Centimeter', 'symbol' => 'cm3'],
            ['name' => 'Cubic Centimeter', 'symbol' => 'cm3'],
            ['name' => 'Pieces', 'symbol' => 'Pieces'],
            ['name' => 'Items', 'symbol' => 'Items'],
            ['name' => 'Square Centimeter', 'symbol' => 'cm2'],
            ['name' => 'Square Centimeter', 'symbol' => 'cm2'],

        ];


        return $this->response->setJSON([
            'data' => $units,
        ]);
    }



    public function tokyo()
    {
        $billId = $this->getVariable('billId');
        $percentage = 5;
        // $currentDate = new DateTime(); // Current date and time
        // $bill = [];
        // // Fetch bill and bill items from the model
        // // $bill = $this->billModel->fetchBill($billId);
        // // $billItems = $this->billModel->fetchBillItems($billId);


        return $this->response->setJSON([
            'status' => $billId,
            'data' => [],
            'msg' => 'Invalid Bill ID'
        ]);

        // echo $billId;
    }
}
