<?php

namespace App\Libraries;

use LSS\Array2XML;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Libraries\GepgProcess;

class TrActivityBillProcessing
{
    protected $billModel;
    public function __construct()
    {
        $this->billModel = new BillModel();
    }


    function formatItems($items)
    {
        $items = (new ArrayLibrary($items))->map(fn($item) => [
            'RefBillId' => $item->RefBillId,
            'SubSpCode' => $item->SubSpCode,
            'GfsCode' => $item->GfsCode,
            'BillItemRef' => $item->BillItemRef,
            'UseItemRefOnPay' => $item->UseItemRefOnPay,
            'BillItemAmt' => $item->BillItemAmt,
            'BillItemEqvAmt' => $item->BillItemEqvAmt,
            'CollSp' => $item->CollSp,
        ])->get();

        return $items;
    }


    public function processBill($billDetailsArray, $itemsArray, $user)
    {
        $billModel = new BillModel();
        $GepGpProcess = new GepgProcess();

        $items = json_decode(json_encode($itemsArray));


        $spCode = setting('Bill.spCode');
        $subSpCode = setting('Bill.subSpCode');
        $systemId = setting('Bill.systemId');

        // $paymentOption = $billDetailsArray['BillPayOpt'];
        $paymentOption = 3;

        $billType = $billDetailsArray['BillTyp'];
        $activityItems = [];

        if ($paymentOption == 2) {
            $combinedAmount = (new ArrayLibrary($items))->reduce(fn($x, $y) => $x + $y->BillItemAmt)->get();

            $item = [
                'RefBillId' => $items->RefBillId,
                'SubSpCode' => $items->SubSpCode,
                'GfsCode' => $items->GfsCode,
                'BillItemRef' => $billDetailsArray['BillId'],
                'UseItemRefOnPay' => 'N',
                'BillItemAmt' => $combinedAmount,
                'BillItemEqvAmt' => $combinedAmount,
                'CollSp' => $items->CollSp,

            ];
            array_push($activityItems, $item);
        } else {
            $activityItems = $this->formatItems($items);
        }

        $items85Percent = array_map(function ($item) use($billType) {
            $amount = $billType == 2 ? $item['BillItemAmt'] * 0.85 : $item['BillItemAmt'];
            $item['BillItemAmt'] = $amount;
            $item['BillItemEqvAmt'] = $amount;
            return $item;
        }, $activityItems);



        $wmaBill = billDataArray($billDetailsArray, 'wma');
        $trBill = billDataArray($billDetailsArray, 'tr');
        $wmaBill['BillItems'] = $items85Percent;
        $trBill['BillItems'] = $items85Percent;
        $content = combinedBillContent($wmaBill, $trBill);

        file_put_contents('billXml.xml', $content);



        $requestId = $billDetailsArray['RequestId'];



        //switching uri and request headers based on type of request being sent
        $uri = "bill/20/submission";
        $GepgCom = "Gepg-Com:default.sp.in";
   


        $params = (object)[
            "dataTag" => "gepgBillSubReqAck",
            "uri" => $uri,
            'GepgCom' => $GepgCom,
            'spGroupCode' => $billType == 2 ? setting('Bill.spGroupCodeCombined') : setting('Bill.spGroupCodeSingle'),
        ];

        $submission = $GepGpProcess->billSubmission(formatXml($content), $params);

        $responseAck = XML2Array::createArray($submission->resultCurlPost);
        $response = json_decode(json_encode($responseAck));
        $code = $response->Gepg->billSubReqAck->AckStsCode;

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

                $this->billModel->saveTrBill($trBill);
                $this->billModel->saveTrBillItems($items);


                if ($paymentOption == 2) {
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

                    $billRes = filterResponse($this->billModel->getBillResponse($requestId))[0];
                    if (!empty($billRes)) {
                        $statusCode = $billRes->billStatusCode;
                        if (strlen($statusCode) == 4 && $statusCode == '7101') {
                            // $billDetailsArray['PayCntrNum'] =  $controlNumber;
                            // $billDetailsArray['TrxStsCode'] = $statusCode;

                            $controlNumber = $billRes->controlNumber;
                            $updatedBillData = [
                                'PayCntrNum' =>  $controlNumber,
                                'TrxStsCode' => $statusCode,
                            ];
                            $billModel->updateBill($controlNumber, $updatedBillData);
                            $billModel->updateTrBill($controlNumber, $updatedBillData);

                            $currentBill = $this->billModel->getBillDetails($requestId);
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

                            $bill = $billModel->fetchBill($requestId);
                            $billData = (object)[
                                'bill' => $bill,
                                'billItems' => $billModel->fetchBillItems($requestId),
                                'printedBy' => $user,
                                'printedOn' => dateFormatter(date('Y-m-d')),
                                'bank' => $bank,
                                'accountNumber' => $accountNumber,
                            ];
                            $qrCodeObject = (object)[
                                'opType' => '2',
                                'shortCode' => '001001',
                                'billReference' =>  $controlNumber,
                                'amount' => $bill->BillAmt,
                                'billCcy' => 'TZS',
                                'billExprDt' => $bill->BillExprDt,
                                'billPayOpt' => $bill->BillPayOpt,
                                'billRsv01' => "Weights And Measure Agency|$bill->PyrName"
                            ];


                            return (object)[
                                'status' => 1,
                                'controlNumber' => $controlNumber,
                                'TrxStsCode' =>  $statusCode,
                                'bill' => $method == 'MobileTransfer' ? normalBill($billData) : transferBill($billData),
                                'qrCodeObject' =>  $qrCodeObject,
                                'msg' => 'Bill Created Successfully',
                                'heading' => $method == 'BankTransfer' ? "Order Form For Electronic Fund Transfer To $bank " : "Government Bill",

                            ];
                        } else {
                            //if gepg response code is not 7101 return error codes and messages
                            $errorCode = substr($statusCode, 0, 4);
                            $transactionStatus = substr($statusCode, -4);
                            return (object)[
                                'status' => 0,
                                'msg' => tnxCode($errorCode),
                                'TrxStsCode' =>  $errorCode,
                                'requestId' => $requestId
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
