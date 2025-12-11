<?php

namespace App\Controllers;

use stdClass;
use LSS\Array2XML;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\ArrayLibrary;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BillPlaygroundController extends BaseController
{
    protected $billModel;
    protected $profileModel;
    protected $CommonTasks;
    protected $arrayLibrary;

    public function __construct(){
        $this->billModel = new BillModel();
        $this->profileModel = new ProfileModel();
       // $this->CommonTasks = new CommonTasks();
        $this->arrayLibrary = new ArrayLibrary();
      
    }
    
    public function billTest()
    {
        $xmlResponse = file_get_contents('billXml.xml');
        $dataArray = XML2Array::createArray($xmlResponse);

        $data = json_decode(json_encode($dataArray));
        $paymentResponse = $data->Gepg->pmtSpNtfReq;
        $header = $paymentResponse->PmtHdr;
        $payments = $paymentResponse->PmtDtls->PmtTrxDtl;



        $ackId = 'ACK' . numString(10);

        $params = (object)[
            "dataTag" =>  "pmtSpNtfReq",
            "content" => "<pmtSpNtfReqAck>
              <AckId>$ackId</AckId>
              <ReqId>$header->ReqId</ReqId>
              <AckStsCode>7101</AckStsCode>
          </pmtSpNtfReqAck>"
        ];

        $queueData = [
            'requestId' => $header->ReqId,
            'controlNumber' => $header->CustCntrNum,
            'payments' => $xmlResponse
        ];

        //  $this->queueService->push('payment', 'processPayment', $queueData);

        // echo 'Payment Received';

        //signing ack and send back 
        // echo $this->acknowledgement->acknowledgementProcessing($xmlResponse, $params);

        // ob_flush();
        // flush();


        $billedAmount = (new ArrayLibrary($payments))->map(fn($payment) => $payment->BillAmt)
            ->reduce(fn($x, $y) => $x + $y)
            ->get();
        $paymentAmount = (new ArrayLibrary($payments))->map(fn($payment) => $payment->PaidAmt)
            ->reduce(fn($total, $payment) => $total + $payment)
            ->get();



        $paymentData = array_map(function ($payment) use ($header) {
            $payment->ReqId = $header->ReqId;
            $payment->GrpBillId = $header->GrpBillId;
            $payment->SpGrpCode = $header->SpGrpCode;
            $payment->EntryCnt = $header->EntryCnt;
            $payment->PayCtrNum = $header->CustCntrNum;
            $payment->BillControlNumber = $payment->BillCtrNum;
            $payment->PspReceiptNumber = $payment->TrdPtyTrxId;
            unset($payment->BillCtrNum, $payment->Rsv1, $payment->Rsv2, $payment->Rsv3, $payment->QtRefId, $payment->TrdPtyTrxId, $payment->NtfDtTm);

            return $payment;
        }, is_array($payments) ? $payments : [$payments]);


        $wmaSpCode = setting('Bill.wmaSpCode'); // '';
        $trSpCode = setting('Bill.trSpCode'); // '';



        $paymentCount = $header->EntryCnt;
        $wma =  array_filter($paymentData, fn($payment) => $payment->SpCode == $wmaSpCode);
        $wmaPayment = json_decode(json_encode($wma[0]));
        $paymentOption = $wmaPayment->BillPayOpt;
        // $billPaymentAmount =   $paymentAmount;


        $controlNumber = $header->CustCntrNum;
        //get amount already paid for partial payments
        $getPaidSum = $this->billModel->getBillPaymentAmounts($controlNumber);

        $alreadyPaid = $getPaidSum[0]->PaidAmt ?? 0;
        //current paid amount from the user
        $currentPayment = $paymentAmount;
        //sum up amount already paid and the current paid amount
        $updatedAmount = $alreadyPaid +  $paymentAmount;


        // $billedAmount =   $wmaPayment->BillAmt;

        //calculating the amount of debt left.
        $debt = $billedAmount - $updatedAmount;
        $receiptNumber = $wmaPayment->PspReceiptNumber;
        $payerNumber = $wmaPayment->PyrCellNum;

        $billData = $this->billModel->getAmountPaidAndCenter($controlNumber);
        $wmaPayment->CenterNumber = $billData->CollectionCenter;
        $wmaPayment->clearedAmount = $updatedAmount;

        $wmaPayment->PaidAmt = $paymentAmount; //set paid amount to be 100%
        $wmaPayment->BillAmt = $billedAmount; //set bill amount to be 100%




        if ($paymentOption == 2) {
            //get available amount and add the amount paid to it
            $amount = $billData->PaidAmount +  ($paymentAmount * 0.15);

            if ($amount ==  $billedAmount || $amount >  $billedAmount) {
                $paymentStatus = 'Paid';
            } else {
                $paymentStatus = 'Partial';
            }
        } else {

            $paymentStatus =  $billedAmount ==  $paymentAmount ? 'Paid' : 'Partial';
        }
        $center = 'WMA';
        $textParams = (object)[
            'center' => $center,
            'amount' => $currentPayment,
            'debt' => $debt < 0 ? 0 : $debt,
            'controlNumber' => (int)$controlNumber,
            'receiptNumber' => $receiptNumber

        ];

        $paymentExist = $this->billModel->verifyPaymentExistence(['PayRefId' =>  $wmaPayment->PayRefId]);




        if (!$paymentExist) {

            //save payment to the database from GEPG

            if ($paymentCount == 2) {

                $tr =  array_filter($paymentData, fn($payment) => $payment->SpCode == $trSpCode);

                $trPayment = json_decode(json_encode($tr[1]));
                $trPayment->CenterNumber = $billData->CollectionCenter;
                $this->billModel->saveTrPayment($trPayment,  $billedAmount);
            }

            $this->billModel->savePayment($wmaPayment);

            $billData = [
                'PaymentStatus' => $paymentStatus,
                'PaidAmount' => $updatedAmount,
            ];



            //update bill status and paid amount
            $this->billModel->updateBill($controlNumber, $billData);
        }


        // Printer($wmaPayment);
        // exit;
    }





    public function billTesting()
    {


        $xmlResponse = file_get_contents('res.xml');
        $dataArray = XML2Array::createArray($xmlResponse);

        $data = json_decode(json_encode($dataArray));
        $paymentResponse = $data->Gepg->sucSpPmtRes;
        $header = $paymentResponse->BatchHdr;
        $payments = $paymentResponse->PmtDtls->PmtTrxDtl;

        $paymentData = array_map(function ($payment) use ($header) {
            $paymentData = new stdClass;
            $paymentData->GrpBillId = $payment->GrpBillId;
            $paymentData->PayRefId = $payment->PayRefId;
            $paymentData->SpBillId = $payment->BillId;
            $paymentData->SpReconcReqId = $header->ReqId;
            $paymentData->SpGrpCode = $header->SpGrpCode;
            $paymentData->BillCtrNum = $payment->CustCntrNum;
            $paymentData->BillControlNumber = $payment->BillCtrNum;
            $paymentData->PspReceiptNumber = $payment->TrdPtyTrxId;
            $paymentData->CtrAccNum = $payment->CollAccNum;
            $paymentData->DptCellNum = $payment->PyrCellNum;
            $paymentData->TrxId = $payment->TrxId;
            $paymentData->BillAmt = $payment->BillAmt;
            $paymentData->PaidAmt = $payment->PaidAmt;
            $paymentData->TrxDtTm = $payment->TrxDtTm;
            $paymentData->PspName = $payment->PspName;
            $paymentData->UsdPayChnl = $payment->UsdPayChnl;
            $paymentData->PyrName = $payment->PyrName;
            $paymentData->BillPayOpt = $payment->BillPayOpt;


            return (array)$paymentData;
        }, is_array($payments) ? $payments : [$payments]);

        $this->billModel->saveReconciliation($paymentData);
        printer($paymentData);
        exit;











        // ================PAYMENT TEST==============
        $xmlResponse = file_get_contents('payment.xml');
        $dataArray = XML2Array::createArray($xmlResponse);

        $data = json_decode(json_encode($dataArray));
        $paymentResponse = $data->Gepg->pmtSpNtfReq;
        $header = $paymentResponse->PmtHdr;
        $payments = $paymentResponse->PmtDtls->PmtTrxDtl;

        $paymentData = array_map(function ($payment) use ($header) {
            $payment->ReqId = $header->ReqId;
            $payment->GrpBillId = $header->GrpBillId;
            $payment->SpGrpCode = $header->SpGrpCode;
            $payment->EntryCnt = $header->EntryCnt;
            $payment->PayCtrNum = $header->CustCntrNum;
            $payment->BillControlNumber = $payment->BillCtrNum;
            $payment->PspReceiptNumber = $payment->TrdPtyTrxId;
            unset($payment->BillCtrNum, $payment->Rsv1, $payment->Rsv2, $payment->Rsv3, $payment->QtRefId, $payment->TrdPtyTrxId);
            // unset($payment->Rsv1);
            // unset($payment->Rsv2);
            // unset($payment->Rsv3);
            return $payment;
        }, is_array($payments) ? $payments : [$payments]);

        $wmaPayment = $paymentData[0];
        $requestId = $wmaPayment->ReqId;
        $paymentOption = $wmaPayment->BillPayOpt;
        $billPaymentAmount =  $wmaPayment->PaidAmt;


        $controlNumber = $header->CustCntrNum;
        //get amount already paid for partial payments
        $getPaidSum = $this->billModel->getBillPaymentAmounts($controlNumber);

        $alreadyPaid = $getPaidSum[0]->PaidAmt ?? 0;
        //current paid amount from the user
        $currentPayment = $billPaymentAmount;
        //sum up amount already paid and the current paid amount
        $updatedAmount = $alreadyPaid + $currentPayment;


        $billedAmount =   $wmaPayment->BillAmt;

        //calculating the amount of debt left.
        $debt = $billedAmount - $updatedAmount;
        $receiptNumber = $wmaPayment->PspReceiptNumber;
        $payerNumber = $wmaPayment->PyrCellNum;

        //get collection center number from the bill using billId.

        $center = 'Wakala Wa Vipimo';



        // $billData = $this->billModel->getAmountPaidAndCenter($controlNumber);
        $billData = new stdClass;
        $billData->PaidAmount = 200;
        $billData->CollectionCenter = 005;
        if ($paymentOption == 26) {
            //get available amount and add the amount paid to it
            $amount = $billData->PaidAmount +  $billPaymentAmount;

            if ($amount ==  $billedAmount || $amount >  $billedAmount) {
                $paymentStatus = 'Paid';
            } else {
                $paymentStatus = 'Partial';
            }
        } else {

            $paymentStatus =  $billedAmount ==  $billPaymentAmount ? 'Paid' : 'Partial';
        }

        $textParams = (object)[
            'center' => $center,
            'amount' => $currentPayment,
            'debt' => $debt < 0 ? 0 : $debt,
            'controlNumber' => (int)$controlNumber,
            'receiptNumber' => $receiptNumber

        ];

        $paymentExist = $this->billModel->verifyPaymentExistence([
            'PayRefId' =>  $wmaPayment->PayRefId,


        ]);




        if (empty($paymentExist)) {

            //save payment to the database from GEPG
            $payment['CenterNumber'] = $billData->CollectionCenter;
            $this->billModel->savePayment($payment);





            //update bill status and paid amount
            $this->billModel->updateBill($controlNumber, [
                'PaymentStatus' => $paymentStatus,
                'PaidAmount' => $updatedAmount,
            ]);
        }


        $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));

        printer($paymentStatus);
        printer($paymentData);

        // $db = db_connect();
        // $db->table('billTest')->insert(['response'=> $xmlResponse]);
        // $this->billModel->savePayments($paymentData);
    }




    public function billTests()
    {






        $spGroupCode = 'SPG1121';
        $wmaSpCode = 'SP19960';
        $wmaSubSpCode = '1001';
        $systemCode = setting('Bill.systemCode');
        $requestId = numString(10);
        $billId = randomString();
        $treasureSpCode = 'SP19966';
        $treasureSubSpCode = '1001';
        $billAmount = 30000.00;
        $fifteenPercent =  number_format((0.15 * $billAmount), 2, '.', '');
        $customerId = numString(5);

        $xmlResponse = file_get_contents('response.xml');
        $dataArray = XML2Array::createArray($xmlResponse);

        $data = json_decode(json_encode($dataArray));
        $billResponse = $data->Gepg->billSubRes;
        $billHeader = $billResponse->BillHdr;
        $billComponents = $billResponse->BillDtls->BillDtl;
        $controlNumbers = array_map(fn($billDetail) => [
            'responseStatus' => $billHeader->ResStsCode,
            'responseDesc' => $billHeader->ResStsDesc,
            'requestId' => $billHeader->ReqId,
            'responseId' => $billHeader->ResId,
            'billGroupId' => $billHeader->GrpBillId,
            'controlNumber' => $billHeader->CustCntrNum,
            'billId' => $billDetail->BillId,
            'billControlNumber' => $billDetail->BillCntrNum,
            'billStatusCode' => $billDetail->BillStsCode,
            'billStatusDesc' => $billDetail->BillStsDesc
        ], is_array($billComponents) ? $billComponents : [$billComponents]);

        $data = json_decode(json_encode($dataArray));
        $billResponse = $data->Gepg->billSubRes;
        $billHeader = $billResponse->BillHdr;
        $billComponents = $billResponse->BillDtls->BillDtl;


        $db = db_connect();
        $db->table('billTest')->insert(['response' => $xmlResponse]);

        // printer($billComponents);
        exit;

        $submission = $this->GepGpProcess->billSubmission('', []);
        return $this->response->setJSON(XML2Array::createArray($submission->resultCurlPost));
        exit;

        //printer($controlNumbers);
        // $this->billModel->addControlNumber($controlNumbers);







        // return $this->response->setJSON([
        //     'status' => 0,
        //     'data' => $submission,
        //     'token' => $this->token
        // ]);
        // exit;


        // file_put_contents('billXml.xml', formatXml($wmaBillItems));

        // exit;

        $wmaBillItems = [
            'BillItem' => [
                [
                    'RefBillId' => $billId,
                    'SubSpCode' => $wmaSubSpCode,
                    'GfsCode' => '140206',
                    'BillItemRef' => numString(10),
                    'UseItemRefOnPay' => 'N',
                    'BillItemAmt' => '10000.00',
                    'BillItemEqvAmt' => '10000.00',
                    'CollSp' => $wmaSpCode
                ],

                [
                    'RefBillId' => $billId,
                    'SubSpCode' => $wmaSubSpCode,
                    'GfsCode' => '140202',
                    'BillItemRef' => numString(10),
                    'UseItemRefOnPay' => 'N',
                    'BillItemAmt' => '20000.00',
                    'BillItemEqvAmt' => '20000.00',
                    'CollSp' => $wmaSpCode
                ]

            ]
        ];
        $trBillItems = [
            'BillItem' => [
                'RefBillId' => $billId,
                'SubSpCode' => $treasureSubSpCode,
                'GfsCode' => '142201660008',
                'BillItemRef' => numString(10),
                'UseItemRefOnPay' => 'N',
                'BillItemAmt' => $fifteenPercent,
                'BillItemEqvAmt' => $fifteenPercent,
                'CollSp' => $treasureSpCode
            ]


        ];
        $billSubReq = [

            'BillHdr' => [
                'ReqId' => $requestId,
                'SpGrpCode' => $spGroupCode,
                'SysCode' => $systemCode,
                'BillTyp' => '2',
                'PayTyp' => '2',
                'GrpBillId' => $billId,
            ],
            'BillDtls' => [
                [
                    'BillDtl' => [

                        [
                            'BillId' => $billId,
                            'SpCode' => $wmaSpCode,
                            'CollCentCode' => 'HQ',
                            'BillDesc' => 'Verification of Engine',
                            'CustTin' => '',
                            'CustId' => $customerId,
                            'CustIdTyp' => '5',
                            'CustAccnt' =>  $customerId,
                            'CustName' => 'Apex Inc',
                            'CustCellNum' => '255659851709',
                            'CustEmail' => 'payer@yahoo.com',
                            'BillGenDt' => '2024-07-31T10:00:00',
                            'BillExprDt' => '2024-10-11T10:00:30',
                            'BillGenBy' => 'Allen Scott',
                            'BillApprBy' => 'Leon Legend',
                            'BillAmt' => '30000.00',
                            'BillEqvAmt' => '30000.00',
                            'MinPayAmt' => '0.01',
                            'Ccy' => 'TZS',
                            'ExchRate' => '1.00',
                            'BillPayOpt' => '1',
                            'PayPlan' => '1',
                            'PayLimTyp' => '1',
                            'PayLimAmt' => '0.00',
                            'CollPsp' => '',
                            'BillItems' => $wmaBillItems
                        ],
                        [
                            'BillId' => $billId,
                            'SpCode' => $treasureSpCode,
                            'CollCentCode' => 'HQ',
                            'BillDesc' => 'Verification of Engine',
                            'CustTin' => '',
                            'CustId' =>  $customerId,
                            'CustIdTyp' => '5',
                            'CustAccnt' =>  $customerId,
                            'CustName' => 'Apex Inc',
                            'CustCellNum' => '255659851709',
                            'CustEmail' => 'payer@yahoo.com',
                            'BillGenDt' => '2024-07-31T10:00:00',
                            'BillExprDt' => '2024-10-11T10:00:30',
                            'BillGenBy' => 'Allen Scott',
                            'BillApprBy' => 'Leon Legend',
                            'BillAmt' => $fifteenPercent,
                            'BillEqvAmt' => $fifteenPercent,
                            'MinPayAmt' => '0.01',
                            'Ccy' => 'TZS',
                            'ExchRate' => '1.00',
                            'BillPayOpt' => '1',
                            'PayPlan' => '1',
                            'PayLimTyp' => '1',
                            'PayLimAmt' => '0.00',
                            'CollPsp' => '',
                            'BillItems' => $trBillItems
                        ],



                    ]
                ],


            ],

        ];


        $wmaBill =  $billSubReq['BillDtls'][0];

        // $trBill =  $billSubReq['BillDtls']['BillDtl'][1];
        printer($wmaBill);
        exit;




        $content = "";



        $uri = "bill/20/submission";
        $GepgCom = "Gepg-Com:default.sp.in";

        $xml = Array2XML::createXML('billSubReq', $billSubReq)->saveXML();
        $xmlPayload = ltrim(str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml));
        // file_put_contents('billXml.xml', formatXml($xmlPayload));
        //  exit;
        $params = (object)[
            "dataTag" => "gepgBillSubReqAck",
            "uri" => $uri,
            'GepgCom' => $GepgCom,
        ];

        $submission = $this->GepGpProcess->billSubmission(formatXml($xmlPayload), $params);




        return $this->response->setJSON([
            'status' => 1,
            'requestId' => $requestId,
            'billId' => $billId,
            // 'data' => XML2Array::createArray($submission->resultCurlPost),
            'data' => $submission,
            'response' => $submission,
            'token' => $this->token
        ]);
    }


    public function paymentXml($payment)
    {


        $wmaBillId = $payment->wmaBillId;
        $trBillId = $payment->trBillId;
        $wmaCN = $payment->wmaBillId;
        $trCN = $payment->trBillId;
        $payRef = numString(10);
        $trnxId = randomString(10);

        $date = date("Y-m-d H:i:s");

        $xml = "
     <?xml version='1.0' encoding='UTF-8' standalone='yes'?>
    <Gepg>
    <pmtSpNtfReq>
        <PmtHdr>
            <ReqId>$payment->ReqId</ReqId>
            <GrpBillId>$payment->GrpBillId</GrpBillId>
            <SpGrpCode>SPG1103</SpGrpCode>
            <CustCntrNum>$payment->controlNumber</CustCntrNum>
            <EntryCnt>2</EntryCnt>
        </PmtHdr>
        <PmtDtls>
            <PmtTrxDtl>
                <SpCode>SP19960</SpCode>
                <BillId>$wmaBillId</BillId>
                <BillCtrNum>$wmaCN</BillCtrNum>
                <PspCode>PSP047</PspCode>
                <PspName>UAT Simulator</PspName>
                <TrxId>$trnxId</TrxId>
                <PayRefId>$payRef</PayRefId>
                <BillAmt>$payment->wmaAmt</BillAmt>
                <PaidAmt>$payment->wmaAmt</PaidAmt>
                <BillPayOpt>2</BillPayOpt>
                <Ccy>TZS</Ccy>
                <CollAccNum>GEPG0123456</CollAccNum>
                <TrxDtTm>$date</TrxDtTm>
                <NtfDtTm>$date</NtfDtTm>
                <UsdPayChnl>CD</UsdPayChnl>
                <TrdPtyTrxId></TrdPtyTrxId>
                <QtRefId></QtRefId>
                <PyrCellNum>255659851709</PyrCellNum>
                <PyrEmail></PyrEmail>
                <PyrName>$payment->PyrName</PyrName>
                <Rsv1></Rsv1>
                <Rsv2></Rsv2>
                <Rsv3></Rsv3>
            </PmtTrxDtl>
            <PmtTrxDtl>
                <SpCode>SP19966</SpCode>
                <BillId>$trBillId</BillId>
                <BillCtrNum>$trCN</BillCtrNum>
                <PspCode>PSP047</PspCode>
                <PspName>UAT Simulator</PspName>
                <TrxId>$trnxId</TrxId>
                <PayRefId>$payment->PyrName</PayRefId>
                <BillAmt>$payment->trAmt</BillAmt>
                <PaidAmt>$payment->trAmt</PaidAmt>
                <BillPayOpt>2</BillPayOpt>
                <Ccy>TZS</Ccy>
                <CollAccNum>GEPG0123456</CollAccNum>
                <TrxDtTm>$date</TrxDtTm>
                <NtfDtTm>$date</NtfDtTm>
                <UsdPayChnl>CD</UsdPayChnl>
                <TrdPtyTrxId></TrdPtyTrxId>
                <QtRefId></QtRefId>
                <PyrCellNum>255659851709</PyrCellNum>
                <PyrEmail></PyrEmail>
                <PyrName>$payment->PyrName</PyrName>
                <Rsv1></Rsv1>
                <Rsv2></Rsv2>
                <Rsv3></Rsv3>
            </PmtTrxDtl>
        </PmtDtls>
    </pmtSpNtfReq>
    <signature>IGM8kfLWZ34TUDUtEXx15zgSFoxF0AJeBwhUSzOp4CcsayQf/rHDGKYE9Tyhnos2HVewFTg1xQsXTg/vgTH+8EtxA2vv9ORsKTvFhkP1EVncz4X9ki08qD2iiBWemnY8zNfzN6NFZVkOBc3waHzmJI+7PaDlxiwfOuWLu0RNskNzifYo/BqvI6/mmGIou3WLHZZKYSCjWpSWAHOHJy6AaiC/53z77eQGqt/zKdHro2k+pVNtHpak82S5d8X/qZixbNl7tQqizV+V9YT6jEraI8geGMOZ6On+wGitytE76CLkirCOORKWHtrgXFUpUVn+F0fGriPEuRlFsPUovoFirg==</signature>
</Gepg>
     ";

        // file_put_contents('billXml.xml', $xml);
    }


}
