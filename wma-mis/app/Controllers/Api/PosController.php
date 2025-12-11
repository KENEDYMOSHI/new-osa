<?php

namespace App\Controllers\Api;

use DateTime;
use DateInterval;
use LSS\Array2XML;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\XmlLibrary;
use App\Libraries\GepgProcess;
use App\Libraries\ArrayLibrary;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;



class PosController extends ResourceController
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
    protected $SpSysId;
    protected $collectionCenters;
    protected $collectionCenter;

    use ResponseTrait;

    public function __construct()
    {


        $this->billModel = new BillModel();
        $this->collectionCenters = $this->billModel->getCollectionCenters();
        $this->xmlLibrary = new XmlLibrary();
        $this->GepGpProcess = new GepgProcess();
        $this->profileModel = new profileModel();
        $this->uniqueId = auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;

        $this->SpCode = 'SP19960';
        $this->SpSysId = 'WMAT001';
        helper('setting');
        helper(setting('App.helpers'));
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }




    //bill submission request to GePG
    public function billSubmissionRequest()
    {
        //use ResponseTrait;

        try {
            if ($this->request->getMethod() == 'POST') {

                $BillItemsObj = $this->getVariable('BillItems');

                $items =  json_decode(json_encode($BillItemsObj), true);


                $BillId = randomString();
                $gfsCode  = $this->getVariable('GfsCode');
                $PhysicalLocation  = $this->getVariable('PhysicalLocation');
                $billAmount   = (float)str_replace(',', '', $this->getVariable('BillAmt'));

                $BillExprDt = date("Y-m-d\TH:i:s", strtotime($this->getVariable('BillExprDt')));

                // return  $this->response->setJSON([
                //   'data' => $items,
                // ]);




                $billDetailsArray = [

                    'BillId' => $BillId,
                    'Activity' =>  $items[0]['GfsCode'],
                    'Task' =>  $items[0]['Task'],
                    'BillRef' => numString(10),
                    'BillAmt' => (float)  $billAmount,
                    'BillAmtWords' => toWords($billAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' => date('Y-m-d', strtotime($this->getVariable('BillExprDt'))),
                    'PyrId' => randomString(),
                    'PyrName' =>  $this->getVariable('PyrName'),
                    'BillDesc' =>  $this->getVariable('BillDesc'),
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>   auth()->user()->username,
                    'CollectionCenter' =>   $this->collectionCenter,
                    'BillApprBy' =>  'wma-hq',
                    'PyrCellNum' => '255' . substr($this->getVariable('PyrCellNum'), 1),
                    'PyrEmail' =>  $this->getVariable('PyrEmail'),
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

                $condemned = (new ArrayLibrary($reqItems))->filter(fn ($item) => $item['Status'] == 'Condemned')->map(fn ($item) => [
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


                $passAndRejected = (new ArrayLibrary($reqItems))->filter(fn ($item) => $item['Status'] != 'Condemned')->get();

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
                $StickerNumberArr = [];
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
                    $StickerNumberArr[] = !isset($item['StickerNumber']) ||$item['StickerNumber'] == null ? '' : $item['StickerNumber'];
                    $TasksArr[] = $item['Task'];
                    $SingleItemAmountArr[] = $item['SingleItemAmount'];
                    $ItemQuantityArr[] = $item['ItemQuantity'];
                    $BillIdArr[] = $item['BillId'];
                }


                $itemsArray = [
                    'BillItemRef' => $BillItemRefArr,
                    'UseItemRefOnPay' => $UseItemRefOnPayArr,
                    'BillItemAmt' =>  $BillItemAmtArr,
                    'BillItemEqvAmt' =>  $BillItemEqvAmtArr,
                    'BillItemMiscAmt' => $BillItemMiscAmtArr,
                    'GfsCode' =>  $GfsCodeArr,
                    'ItemName' => $ItemNameArr,
                    'StickerNumber' => $StickerNumberArr,
                    'Task' => $TasksArr,
                    'Status' => $StatusArr,
                    'UserId' => $UserIdArr,
                    'BillId' => $BillIdArr,
                    'ItemQuantity' => $ItemQuantityArr,
                    'SingleItemAmount' => $SingleItemAmountArr,
                  
                ];

                $items =  multiDimensionArray($itemsArray);


                // return $this->response->setJSON([
                //     'status' => 0,
                //     'data' => array_values($condemned),
                //     'items' => $items,
                //     'billDetailsArray' => $billDetailsArray,
                // ]);


                // exit;

              

                $xml = Array2XML::createXML('BillItems', ['BillItem' => arrayExcept($items, ['ItemName', 'StickerNumber', 'UserId', 'Task', 'Status', 'ItemQuantity', 'SingleItemAmount','BillId'])])->saveXML();
                $BillItems = ltrim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml));

                $billDetails = (object)$billDetailsArray;
                //$content = "";
                $content = "<gepgBillSubReq>
                         <BillHdr>
                            <SpCode>SP19960</SpCode>
                            <RtrRespFlg>true</RtrRespFlg>
                         </BillHdr>
                         <BillTrxInf>
                           <BillId>$BillId</BillId>
                           <SubSpCode>1001</SubSpCode>
                           <SpSysId>WMAT001</SpSysId>
                           <BillAmt>$billDetails->BillEqvAmt</BillAmt>
                           <MiscAmt>$billDetails->MiscAmt</MiscAmt>
                            <BillExprDt>$BillExprDt</BillExprDt>
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
                $uri = "/api/bill/sigqrequest";
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
                //     'billId'=> $BillId,
                //     'res' => $billRes
                // ]);
                // exit;
                if ($code == '7101') {


                    $updated = true;

                    while ($updated) {
                        $billRes = $this->billModel->getBillResponse($BillId);

                        if ($billRes != null || $billRes != []) {

                            //get first TnxCode in case api returns multiple codes
                            $TrxStsCode = substr($billRes->resCode, 0, 4);


                            if ($TrxStsCode == '7101') {

                                // $itemsArray['BillId'] = fillArray($count, $BillId);
                                $billDetailsArray['PayCntrNum'] = $billRes->PayCntrNum;
                                $billDetailsArray['TrxStsCode'] = $TrxStsCode;
                                $this->billModel->saveBill($billDetailsArray);
                                // $saveBill = true;

                                $this->billModel->saveBillItems($items);
                                $bill = $this->billModel->fetchBill($billDetailsArray['BillId']);

                                $billItemsArray = array_map(fn ($item) => $item->ItemName, $this->billModel->fetchBillItems($billDetailsArray['BillId']));

                                $billItems = implode(', ', $billItemsArray);

                                $billData = [
                                    'billItems' => $billItems,
                                    'billRef' => $bill->BillRef,
                                    'payerName' => $bill->PyrName,
                                    'payerPhone' => $bill->PyrCellNum,
                                    'billItems' => $billItems,
                                    'amount' => 'TZS ' . number_format($bill->BillAmt),
                                    // 'amount' => 'TZS ' . $bill->BillAmt,
                                    'payOption' =>  $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                                    'expireDate' => dateFormatter($bill->BillExprDt),
                                    'controlNumber' => $bill->PayCntrNum,
                                    'posCenter' => centerName(),
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
                                    'trxStsCode' =>  $TrxStsCode,
                                    'msg' => 'Bill Created Successfully',

                                ];

                              
                                return $this->response->setJSON([
                                    'status' => 1,
                                    'data' => $billData




                                ]);
                            } else {
                                return $this->response->setJSON([
                                    'status' => 0,
                                    'data' => [
                                        'msg' => tnxCode($TrxStsCode),
                                        'trxStsCode' =>  $TrxStsCode,
                                    ]

                                ]);
                            }



                            $updated = false;
                            break;
                        } else {
                            sleep(1);
                        }
                    }

                    // }
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'data' => [
                            'msg' => tnxCode($code),
                            'TrxStsCode' =>  $code,
                        ]

                    ])->setStatusCode(500);
                }
            }
        } catch (\Throwable $th) {

            return $this->response->setJSON([
                'status' => 0,
                'data' => [
                    'msg' => $th->getMessage(),
                ]

            ])->setStatusCode(500);
        }
    }





    public function searchBill()
    {
        try {
            $keyword = $this->getVariable('keyword');
            $paymentStatus = $this->getVariable('paymentStatus');
            $date = $this->getVariable('date');


            $billParams = [
                'IsCancelled' => 'No',
                'PaymentStatus' => $paymentStatus,
                'CollectionCenter' => $this->collectionCenter,
                'DATE(wma_bill.CreatedAt)' => $date != '' ? date("Y-m-d", strtotime($date)) : '',
            ];

            foreach ($billParams as $key => $value) {
                if ($value == '') {
                    unset($billParams[$key]);
                }
            }


            $request =  $this->billModel->searchBillApi($billParams, $keyword);
            // return $this->response->setJSON([$billParams]);
            // exit;
            $user = auth()->user();



            if ($request) {


                $bills = array_map(function ($bill) {
                    $billItemsArray = array_map(fn ($item) => $item->ItemName, $this->billModel->fetchBillItems($bill->BillId));

                    $billItems = implode(', ', $billItemsArray);
                    return  [
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
                        'payOption' =>  $bill->BillPayOpt == 1 ? 'Full' : ($bill->BillPayOpt == 2 ? 'Partial' : 'Exact'),
                        'controlNumber' => $bill->PayCntrNum,
                        'posCenter' => centerName(),
                        'mobileNumber' =>  wmaCenter()->mobileNumber,
                        'printedOn' => dateFormatter(date('Y-m-d')),


                        'expireDate' => dateFormatter($bill->BillExprDt),
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



            $request =  $this->billModel->searPaymentApi($billParams, $keyword);




            if ($request) {


                $payments = array_map(function ($payment) {
                    $billItemsArray = array_map(fn ($item) => $item->ItemName, $this->billModel->fetchBillItems($payment->BillId));

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


   
}
