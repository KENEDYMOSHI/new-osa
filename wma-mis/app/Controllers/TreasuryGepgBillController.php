<?php

namespace App\Controllers;

use stdClass;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\SmsLibrary;
use App\Libraries\Acknowledgement;
use App\Libraries\ArrayLibrary;

class TreasuryGepgBillController extends BaseController
{

    protected $billModel;
    protected $acknowledgement;
    protected $sms;
    protected $queueService;
    public function __construct()
    {
        $this->billModel = new BillModel();
        $this->acknowledgement = new Acknowledgement();
        $this->sms = new SmsLibrary();
        $this->queueService = service('queue');
    }



    public function controlNumber()
    {
        session()->destroy();
        ob_start();
        $xmlResponse = file_get_contents('php://input');

        $dataArray = XML2Array::createArray($xmlResponse);

        $data = json_decode(json_encode($dataArray));
        $billResponse = $data->Gepg->billSubRes;
        $billHeader = $billResponse->BillHdr;
        $billComponents = $billResponse->BillDtls->BillDtl;






        $ackId = 'ACK' . numString(10);
        $params = (object)[
            "dataTag" =>  "billSubRes",
            "content" => "<billSubResAck>
		 <AckId>$ackId</AckId>
		 <ResId>$billHeader->ReqId</ResId>
		 <AckStsCode>7101</AckStsCode>
	   </billSubResAck>"
        ];

        echo $this->acknowledgement->acknowledgementProcessing($xmlResponse, $params);

        ob_flush();
        flush();





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
        $this->billModel->addControlNumber($controlNumbers);

        $billCn = array_map(fn($billDetail) => $billDetail['billControlNumber'], $controlNumbers);



        //converting xml response to array
        $array =   XML2Array::createArray($xmlResponse);

        $responseObject = json_decode(json_encode((object)$array));

        $data = $responseObject->Gepg->billSubRes;
        $billHeader = $data->BillHdr;
        $details = $data->BillDtls;

        $customerControlNumber = $billHeader->CustCntrNum;
        $requestId = $billHeader->ReqId;
        $responseId = $billHeader->ResId;
        $statusCode = $billHeader->ResStsCode;
        $groupBillId = $billHeader->GrpBillId;


        $this->billModel->addControlNumber($controlNumbers);
        // $file = base_url().'Res/res/txt';
        //    file_put_contents($file, $response);

        //checking if request is successful
        if ($statusCode == '7101') {

            //updating control number using bill id
            $this->billModel->updateControlNumber($requestId, ['PayCntrNum' => $customerControlNumber, 'billControlNumber' => implode(',', $billCn)], $data);
        }
        //saving control number to the database

        //params to process ack





        //signing ack and send back to GePG
    }



    //================= GePG PAYMENT POSTING ====================

    public function billPayment()
    {
        session()->destroy();
        ob_start();
        $xmlResponse = file_get_contents('php://input');
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

        $this->queueService->push('payment', 'processPayment', $queueData);

        // echo 'Payment Received';

        //signing ack and send back 
       

        $paymentArray = $header->EntryCnt == 1 ? [$payments] : $payments;

        $billedAmount = (new ArrayLibrary($paymentArray))->map(fn($payment) => $payment->BillAmt)->reduce(fn($x, $y) => $x + $y)->get();
        $paymentAmount = (new ArrayLibrary($paymentArray))->map(fn($payment) => $payment->PaidAmt)->reduce(fn($x, $y) => $x + $y)->get();

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

        $wmaSpCode = 'SP99419';// setting('Bill.wmaSpCode'); // '';
        $trSpCode = 'SP99517';// setting('Bill.trSpCode'); // '';


        $paymentCount = $header->EntryCnt;
        $wma =  array_filter($paymentData, fn($payment) => $payment->SpCode == $wmaSpCode);
        $wmaPayment = json_decode(json_encode($wma));
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

        // $this->billModel->updateBill($controlNumber, [
        //     'PaymentStatus' => 'Paid',
        //     'PaidAmount' => $updatedAmount,
        // ]);


        // $billedAmount =   $wmaPayment->BillAmt;

        //calculating the amount of debt left.
        $debt = $billedAmount - $updatedAmount;
        $receiptNumber = $wmaPayment->PspReceiptNumber;
        $payerNumber = $wmaPayment->PyrCellNum;

        $billData = $this->billModel->getAmountPaidAndCenter($controlNumber,$header->GrpBillId);
        $wmaPayment->CenterNumber = $billData->CollectionCenter;
        $wmaPayment->clearedAmount = $updatedAmount;

        $wmaPayment->PaidAmt = $paymentAmount; //set paid amount to be 100%
        $wmaPayment->BillAmt = $billedAmount; //set bill amount to be 100%

        //get collection center number from the bill using billId.

        $center = 'Wakala Wa Vipimo';



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

        $textParams = (object)[
            'center' => $center,
            'amount' => $currentPayment,
            'debt' => $debt < 0 ? 0 : $debt,
            'controlNumber' => (int)$controlNumber,
            'receiptNumber' => $receiptNumber

        ];



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
        

        echo $this->acknowledgement->acknowledgementProcessing($xmlResponse, $params);

        ob_flush();
        flush();


        $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));
    }





    public function reconciliation()
    {
        session()->destroy(); // Stop session handling for this request

        ob_start();

        $xmlResponse = file_get_contents('php://input');
        $dataArray = XML2Array::createArray($xmlResponse);

        file_put_contents('payment.xml', $xmlResponse);

        $data = json_decode(json_encode($dataArray));
        $paymentResponse = $data->Gepg->sucSpPmtRes;
        $header = $paymentResponse->BatchHdr;


        $statusCode = $header->PayStsCode;

        $ackId = 'ACK' . numString(10);

        $params = (object)[
            "dataTag" =>  "pmtSpNtfReq",
            "content" => "<sucSpPmtResAck>
                    <AckId>$ackId</AckId>
                    <ResId>$header->ResId</ResId>
                    <AckStsCode>7101</AckStsCode>
                </sucSpPmtResAck>"
        ];



        //signing ack and send back to GePG
        echo $this->acknowledgement->acknowledgementProcessing($xmlResponse, $params);
        ob_flush();
        flush();


        $this->billModel->saveReconInfo([
            'SpReconcReqId' =>  $header->ReqId,
            'ReconcStsCode' => $statusCode,
            'message' => $header->PayStsDesc,
            'SpCode' => $header->SpGrpCode,
        ]);
        // file_put_contents('billXml.xml', $xmlResponse);
        if ($statusCode == 7101) {
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
                $paymentData->BillAmt = $payment->BillAmt;
                $paymentData->PaidAmt = $payment->PaidAmt;
                $paymentData->TrxDtTm = $payment->TrxDtTm;
                $paymentData->PspName = $payment->PspName;
                $paymentData->UsdPayChnl = $payment->UsdPayChnl;
                $paymentData->PyrName = $payment->PyrName;
                $paymentData->BillPayOpt = $payment->BillPayOpt;
                $paymentData->pspTrxId = $payment->TrxId;
                $paymentData->PspCode = $payment->PspCode;
                $paymentData->reconType = 'TR';


                return (array)$paymentData;
            }, is_array($payments) ? $payments : [$payments]);


            $this->billModel->saveReconciliation($paymentData);
        }
    }



    public function settlePayments()
    {
        $db = \Config\Database::connect();



        // Get reconciliation data
        $recon = $db->table('reconciliation')
            ->select('reconciliation.*,SpBillId as BillId, BillCtrNum as PayCtrNum, TrxDtTm as date')
            ->get()
            ->getResultArray(); // Ensure results are in array format

        // Get payment data
        $payments = $db->table('bill_payment')
            ->select('BillId, PayCtrNum, TrxDtTm as date')

            ->get()
            ->getResultArray(); // Ensure results are in array format



        // Custom comparison function based on 'BillId' and 'PayCtrNum'
        $compareFunction = function ($a, $b) {
            // if ($a['BillId'] != $b['BillId']) {
            //     return $a['BillId'] <=> $b['BillId'];
            // } else {
            // }
            return $a['PayCtrNum'] <=> $b['PayCtrNum'];
        };


        // Get elements in $recon that are not in $payments
        $unsettledPayments = array_udiff($recon, $payments, $compareFunction);

        $paymentData = array_map(function ($payment) {
            $bill = (new BillModel())->getBill($payment['BillCtrNum']);
            $transaction =  [
                'BillId' => $bill->BillId,
                'PayCtrNum' => $payment['BillCtrNum'],
                'TrxId' => $payment['pspTrxId'],
                'SpCode' => 'SP419',
                'PayRefId' => $payment['PayRefId'],
                'BillAmt' =>  $bill->BillAmt,
                'PaidAmt' =>   $payment['PaidAmt'],
                'BillPayOpt' => $bill->BillPayOpt,
                'CCy' => $bill->Ccy,
                'TrxDtTm' => $payment['TrxDtTm'],
                'UsdPayChnl' => $payment['UsdPayChnl'],
                'PyrCellNum' =>  $bill->PyrCellNum,
                'PyrEmail' => $bill->PyrEmail,
                'PyrName' => $bill->PyrName,
                'PspReceiptNumber' => $payment['pspTrxId'],
                'PspName' => $payment['PspName'],
                'CtrAccNum' => $payment['CtrAccNum'],
            ];
            return $transaction;
        }, $unsettledPayments);

        foreach ($paymentData as $data) {
            $this->precessPayment($data);
        }

        $numbers = '255659851709,255767991300,255629273164';
        $qty = count($paymentData);
        $date = date('d-m-Y H:i:s');
        if ($qty > 0) $this->sms->sendSms($numbers, "($qty) Unsettled Transactions Found  And Settled  Date: $date");
    }




    public function precessPayment($data)
    {


        // $data = $array['Gepg']['gepgPmtSpInfo']['PymtTrxInf'];
        $billId = $data['BillId'];
        //get amount already paid for partial payments
        $getPaidSum = $this->billModel->getPaymentAmounts($billId);
        //if no amount paid make already paid 0
        $alreadyPaid = $getPaidSum[0]->PaidAmt ?? 0;
        //current paid amount from the user
        $currentPayment = $data['PaidAmt'];
        //sum up amount already paid and the current paid amount
        $updatedAmount = $alreadyPaid + $currentPayment;


        $paymentOption = $data['BillPayOpt'];


        //the bill amount
        $billedAmount =  $data['BillAmt'];

        //calculating the amount of debt left
        $debt = $billedAmount - $updatedAmount;
        $receiptNumber = $data['PspReceiptNumber'];
        $payerNumber = $data['PyrCellNum'];



        $controlNumber = $data['PayCtrNum'];

        $payment = [
            'TrxId' => $data['TrxId'],
            'SpCode' => $data['SpCode'],
            'PayCtrNum' => $data['PayCtrNum'],
            'PayRefId' => $data['PayRefId'],
            'BillId' => $billId,
            'BillAmt' =>  $billedAmount,
            'PaidAmt' =>   $currentPayment,
            'clearedAmount' =>   $updatedAmount,
            'BillPayOpt' => $paymentOption,
            'CCy' => $data['CCy'],
            'TrxDtTm' => $data['TrxDtTm'],
            'UsdPayChnl' => $data['UsdPayChnl'],
            'PyrCellNum' =>  $payerNumber,
            'PyrEmail' => $data['PyrEmail'],
            'PyrName' => $data['PyrName'],
            'PspReceiptNumber' => $receiptNumber,
            'PspName' => $data['PspName'],
            'CtrAccNum' => $data['CtrAccNum'],
        ];





        $center = 'Wakala Wa Vipimo';
        // $centerName = (new ProfileModel())->findCollectionCenter($center)->centerName;
        $centerName = $center;


        $billData = $this->billModel->getAmountPaid($controlNumber);
        if ($paymentOption == 2) {
            //get available amount and add the amount paid to it
            $amount = $billData->PaidAmount +  $data['PaidAmt'];

            if ($amount == $data['BillAmt'] || $amount > $data['BillAmt']) {
                $PaymentStatus = 'Paid';
            } else {
                $PaymentStatus = 'Partial';
            }
        } else {

            $PaymentStatus =   $data['PaidAmt'] >= $billedAmount  ? 'Paid' : 'Partial';
        }


        //parameter for sms notification
        $textParams = (object)[
            'center' => $centerName,
            'amount' => $currentPayment,
            'debt' => $debt < 0 ? 0 : $debt,
            'controlNumber' => (int)$controlNumber,
            'receiptNumber' => $receiptNumber

        ];

        $paymentExist = $this->billModel->verifyPaymentExistence([
            'PayRefId' => $data['PayRefId'],
            'PspReceiptNumber' => $receiptNumber,

        ]);




        if (empty($paymentExist)) {

            //save payment to the database from GEPG
            $payment['CenterNumber'] = $billData->CollectionCenter;
            $this->billModel->savePayment($payment);





            //update bill status and paid amount
            $this->billModel->updateBill($controlNumber, [
                'PaymentStatus' => $PaymentStatus,
                'PaidAmount' => $updatedAmount,
            ]);
        }


        $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));

        //signing ack and send back to GePG

    }
}
