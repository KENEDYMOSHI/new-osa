<?php

namespace App\Libraries;

use LSS\Array2XML;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\WmaBillModel;
use App\Libraries\GepgProcess;
use PHPUnit\TextUI\Output\Printer;

class ActivityBillProcessing
{
    protected $billModel;
    public function __construct()
    {
        $this->billModel = new WmaBillModel();
    }

    public function processBill($billDetailsArray, $itemsArray, $user)
    {
        $billModel = new WmaBillModel();
        $GepGpProcess = new WmaGepgProcess();
       

        $spCode = 'SP419'; //setting('Bill.spCode');
        $subSpCode = '1002'; // setting('Bill.subSpCode');
        $systemId = 'LWMA002';

        $paymentOption = $billDetailsArray['BillPayOpt'];


        $activityItems = [];

        if ($paymentOption == 2) {
            $combinedAmount = (new ArrayLibrary($itemsArray))->reduce(fn($x, $y) => $x + $y['BillItemAmt'])->get();
        
            $item = [
                'BillItemRef' => $billDetailsArray['BillId'],
                'UseItemRefOnPay' => 'N',
                'BillItemAmt' => $combinedAmount,
                'BillItemEqvAmt' => $combinedAmount,
                'BillItemMiscAmt' => 0.00,
                'GfsCode' => $itemsArray[0]['GfsCode'],
                
            ];
            array_push($activityItems, $item);
        } else {
            $activityItems = $itemsArray;
        }

        $items = arrayExcept($activityItems, ['ItemName','ItemQuantity', 'BillId','RequestId', 'UserId', 'PayerId', 'Status','Task','center']);
        $xml = Array2XML::createXML('BillItems', ['BillItem' => $items])->saveXML();
        $BillItems = ltrim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml));
        $extendedExpiryDate = date("Y-m-d\TH:i:s", strtotime("+360 days"));

        $billDetails = (object)$billDetailsArray;
        $BillId = $billDetails->BillId;
        $content = "<gepgBillSubReq>
                     <BillHdr>
                        <SpCode>$spCode</SpCode>
                        <RtrRespFlg>true</RtrRespFlg>
                     </BillHdr>
                     <BillTrxInf>
                       <BillId>$billDetails->BillId</BillId>
                       <SubSpCode>$subSpCode</SubSpCode>
                       <SpSysId>$systemId</SpSysId>
                       <BillAmt>$billDetails->BillEqvAmt</BillAmt>
                       <MiscAmt>$billDetails->MiscAmt</MiscAmt>
                        <BillExprDt>$extendedExpiryDate</BillExprDt>
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
                       <BillPayOpt>$paymentOption</BillPayOpt>
                        " . $BillItems . "
                     </BillTrxInf>
               </gepgBillSubReq>";


        //    echo formatXml($content);
        //    exit;

        // $billItems = array_map(function ($item) {
        //     $item['center'] = auth()->user()->collection_center;
        //     return $item;
        //  }, $itemsArray);

        // file_put_contents('billXml.xml', print_r($itemsArray));

        // exit;


        //switching uri and request headers based on type of request being sent
        $uri = "bill/sigqrequest";
        $GepgCom = "Gepg-Com:default.sp.in";


        $params = (object)[
            "dataTag" => "gepgBillSubReqAck",
            "uri" => $uri,
            'GepgCom' => $GepgCom,
        ];


        $submission = $GepGpProcess->billSubmission(formatXml($content), $params);
        $response = XML2Array::createArray($submission->resultCurlPost);
        $code = $response['Gepg']['gepgBillSubReqAck']['TrxStsCode'];

        // return (object)[
        //     'status' => 0,
        //     // 'msg'=> 'xx',
        //     'msg' => [
        //         'res' => $items,
        //         'billDetailsArray' => $billDetailsArray,
        //     ],
        //     // 'msg' => XML2Array::createArray($submission->resultCurlPost),

        // ];
        // exit;

        if ($submission->status == 1 && $code == '7101') {

          



            if ($code == '7101') {
                $billDetailsArray['TrxStsCode'] = $code;
                $billModel->saveBill($billDetailsArray);
                $billModel->saveBillItems($itemsArray);
                if($paymentOption == 2){
                    $billModel->savePartialReference($activityItems);

                }
                //check if the ststus code returned to callback response code is 7101

                // return (object)[
                //     'status'=> 0,
                //     'billId' => $BillId,
                //     'submission' => $response,
                //     'msg' =>  'xx' ,
                //     'TrxStsCode' =>  $code,
                //     'billId' => $BillId
                // ];
                // exit;

                $maxAttempts = 300; // Set a maximum number of attempts to avoid an infinite loop
                $attempt = 0;
                // $startTime = microtime(true); // Record the start time

                while ($attempt < $maxAttempts) {

                    $billRes = $this->billModel->getBillResponse($BillId);
                    if (!empty($billRes)) {
                        $gepgResponseCode = $billRes->resCode;
                        if (strlen($gepgResponseCode) == 4 && $gepgResponseCode == '7101') {
                            // $billDetailsArray['PayCntrNum'] = $billRes->PayCntrNum;
                            // $billDetailsArray['TrxStsCode'] = $gepgResponseCode;
                            $updatedBillData = [
                                'PayCntrNum' => $billRes->PayCntrNum,
                                'TrxStsCode' => $gepgResponseCode,
                            ];
                            $billModel->updateBill($billRes->PayCntrNum, $updatedBillData);

                            $currentBill = $this->billModel->getBillDetails($BillId);
                            $controlNumber =  $currentBill->PayCntrNum;
                            $method =  $currentBill->method;
                            $SwiftCode =  $currentBill->SwiftCode;
                            $accountNumber = '';
                            $bank = '';

                            switch ($SwiftCode) {
                                case 'NMIBTZTZ':
                                    $accountNumber .= '20301000002';
                                    $bank .= 'National Microfinance Bank';
                                    break;
                                case 'CORUTZTZ':
                                    $accountNumber .= '0150357660600';
                                    $bank .= 'CRDB Bank';
                                    break;
                                case 'TANZTZTX':
                                    $accountNumber .= '9925261001';
                                    $bank .= 'Bank Of Tanzania (BOT)';
                                    break;
                            }

                            $bill = $billModel->fetchBill($BillId);
                            $billData = (object)[
                                'bill' => $bill,
                                'billItems' => $billModel->fetchBillItems($BillId),
                                'printedBy' => $user,
                                'printedOn' => dateFormatter(date('Y-m-d')),
                                'bank' => $bank,
                                'accountNumber' => $accountNumber,
                            ];
                            $qrCodeObject = (object)[
                                'opType' => '2',
                                'shortCode' => '001001',
                                'billReference' => $bill->PayCntrNum,
                                'amount' => $bill->BillAmt,
                                'billCcy' => 'TZS',
                                'billExprDt' => $bill->BillExprDt,
                                'billPayOpt' => $bill->BillPayOpt,
                                'billRsv01' => "Weights And Measure Agency|$bill->PyrName"
                            ];


                            return (object)[
                                'status' => 1,
                                'controlNumber' => $controlNumber,
                                'TrxStsCode' =>  $gepgResponseCode,
                                'bill' => $method == 'MobileTransfer' ? normalBill($billData) : transferBill($billData),
                                'qrCodeObject' =>  $qrCodeObject,
                                'msg' => 'Bill Created Successfully',
                                'heading' => $method == 'BankTransfer' ? "Order Form For Electronic Fund Transfer To $bank " : "Government Bill",

                            ];
                        } else {
                            //if gepg response code is not 7101 return error codes and messages
                            $errorCode = substr($gepgResponseCode, 0, 4);
                            $transactionStatus = substr($gepgResponseCode, -4);
                            return (object)[
                                'status' => 0,
                                'msg' => tnxCode($errorCode),
                                'TrxStsCode' =>  $errorCode,
                                'billId' => $BillId
                            ];
                        }
                        break;
                    } else {
                        sleep(1);
                        $attempt++;
                    }
                }
            }
            // If the loop completes without receiving the expected data, return an error
            return (object)[
                'status' => 0,
                'msg' => 'Timeout: Unable to Process Bill Try Again',
            ];
        } else {
            return (object)[
                'status' => 0,
                'msg' => tnxCode($code),
                'TrxStsCode' =>  $code,
            ];
        }
    }
}