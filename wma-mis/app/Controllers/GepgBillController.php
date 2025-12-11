<?php

namespace App\Controllers;

use LSS\XML2Array;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\SmsLibrary;
use App\Libraries\Acknowledgement;
use PHPUnit\Util\Printer;

class GepgBillController extends BaseController
{

    protected $billModel;
    protected $acknowledgement;
    protected $sms;
    public function __construct()
    {
        $this->billModel = new BillModel();
        $this->acknowledgement = new Acknowledgement();
        $this->sms = new SmsLibrary();
    }



    public function controlNumber()
    {
        $response = file_get_contents('php://input');

        //converting xml response to array
        $array =   XML2Array::createArray($response);

        $data = $array['Gepg']['gepgBillSubResp']['BillTrxInf'];
        $TrxStsCode = $data['TrxStsCode'];
        $BillId = $data['BillId'];
        $PayCntrNum = $data['PayCntrNum'];


        // $file = base_url().'Res/res/txt';
        //    file_put_contents($file, $response);
        $this->billModel->updateControlNumber($BillId, ['PayCntrNum' => $PayCntrNum]);
        //checking if request is successful
        if ($TrxStsCode == '7101') {

            //bill response data to store in the database when callback executes successfully  
            $data = [
                'PayCntrNum ' => $PayCntrNum,
                'BillId' => $BillId,
                'TrxStsCode' => $TrxStsCode,
            ];

            //updating control number using bill id.
            $this->billModel->updateControlNumber($BillId, ['PayCntrNum' => $PayCntrNum]);
           
        } else {
            $data = [
                'PayCntrNum ' => $PayCntrNum,
                'BillId' => $BillId,
                'TrxStsCode' => $TrxStsCode
            ];
        }
        //saving control number to the database
        $this->billModel->saveControlNumber($data);

        //params to process ack

        $params = (object)[
            "dataTag" =>  "gepgBillSubResp",
            "content" => "<gepgBillSubRespAck><TrxStsCode>7101</TrxStsCode></gepgBillSubRespAck>"
        ];

        //signing ack and send back to GePG
        return $this->acknowledgement->acknowledgementProcessing($response, $params);
    }



    //================= GePG PAYMENT POSTING ====================

    public function billPayment()
    {
        //get data from the callback
        $response = file_get_contents('php://input');
        //convert xml response to array
        $array =   XML2Array::createArray($response);

        $data = $array['Gepg']['gepgPmtSpInfo']['PymtTrxInf'];
        $billId = $data['BillId'];
        $controlNumber = $data['PayCtrNum'];
        //get amount already paid for partial payments
        $getPaidSum = $this->billModel->getBillPaymentAmounts($controlNumber);
        //if no amount paid make already paid 0
        $alreadyPaid = $getPaidSum[0]->PaidAmt ?? 0;
        //current paid amount from the user
        $currentPayment = $data['PaidAmt'];
        //sum up amount already paid and the current paid amount
        $updatedAmount = $alreadyPaid + $currentPayment;


        echo 'payment received';

        exit;
 

        $paymentOption = $data['BillPayOpt'];


        //the bill amount
        $billedAmount =  $data['BillAmt'];

        //calculating the amount of debt left.
        $debt = $billedAmount - $updatedAmount;
        $receiptNumber = $data['PspReceiptNumber'];
        $payerNumber = $data['PyrCellNum'];



    

        $payment = [
            'ReqId' => $billId,
            'GrpBillId' => $billId,
            'TrxId' => $data['TrxId'],
            'TrdPtyTrxId' => $data['TrxId'],
            'EntryCnt' => '1',
            'PspCode' => $data['SpCode'],
            'SpCode' => $data['SpCode'],
            'PayRefId' => $data['PayRefId'],
            'BillId' => $billId,
            'PayCtrNum' => $data['PayCtrNum'],
            'BillControlNumber' => $data['PayCtrNum'],
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
            'CollAccNum' => $data['CtrAccNum'],
        ];





        // get collection center number from the bill using billId.
       // $center = $this->billModel->getCollectionCenter($billId)->CollectionCenter;
        $center = 'Wakala Wa Vipimo';
       // $centerName = (new ProfileModel())->findCollectionCenter($center)->centerName;
        $centerName = $center;


        $billData = $this->billModel->getAmountPaidAndCenter($controlNumber,'');
        if ($paymentOption == 2) {
            //get available amount and add the amount paid to it
            $amount = $billData->PaidAmount +  $data['PaidAmt'];

            if ($amount == $data['BillAmt'] || $amount > $data['BillAmt']) {
                $PaymentStatus = 'Paid';
            } else {
                $PaymentStatus = 'Partial';
            }
        } else {

            $PaymentStatus =  $billedAmount == $data['PaidAmt'] ? 'Paid' : 'Partial';
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
            'PayRefId' => $payment['PayRefId'],
           // 'PspReceiptNumber' => $receiptNumber,

        ]);


  
     
        if (empty($paymentExist)) {

            //save payment to the database from GEPG
            if(isset($billData->CollectionCenter)){
                
                $payment['CenterNumber'] =  $billData->CollectionCenter;
            }
           $this->billModel->savePayment($payment);




            //update bill status and paid amount
            // $this->billModel->updateBill($controlNumber, [
            //     'PaymentStatus' => $PaymentStatus,
            //     'PaidAmount' => $updatedAmount,
            // ]);

        }


        $queueService = service('queue');


        $queueData = [
            'requestId' => $billId,
            'controlNumber' => $controlNumber,
            'payments' => $response
        ];

        $queueService->push('payment', 'processPayment', $queueData,true);
        
        
        
        $params = (object)[
            "dataTag" =>  "gepgPmtSpInfo",
            "content" => "<gepgPmtSpInfoAck><TrxStsCode>7101</TrxStsCode></gepgPmtSpInfoAck>"
        ];
        
        //signing ack and send back to GePG
       return $this->acknowledgement->acknowledgementProcessing($response, $params);
       // $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));
    }





    public function reconciliation()
    {
        $response = file_get_contents('php://input');
        // file_put_contents(WRITEPATH.'recon.txt',$response);
        /// exit;
        $array =   XML2Array::createArray($response);

        $reconResponse = $array['Gepg']['gepgSpReconcResp'];

        // exit;

        $ReconcBatchInfo = $reconResponse['ReconcBatchInfo'];
        $ReconcStsCode = $ReconcBatchInfo['ReconcStsCode'];

        $this->billModel->saveReconInfo([
            'SpReconcReqId' => $ReconcBatchInfo['SpReconcReqId'],
            'ReconcStsCode' => $ReconcStsCode,
            'SpCode' => $ReconcBatchInfo['SpCode'],
        ]);


        $ReconcTrans = $reconResponse['ReconcTrans'];

        $reconData = $ReconcTrans['ReconcTrxInf'] ?? [];

        $recon = array_map(function ($data) use ($reconResponse) {
            $data['SpReconcReqId'] = $reconResponse['ReconcBatchInfo']['SpReconcReqId'];
            $data['reconType'] = 'Single';
            return $data;
        }, $reconData);

        // $uniqueKeys = ['SpBillId', 'pspTrxId', 'BillCtrNum', 'PayRefId'];
        $uniqueKeys = ['PayRefId'];

        $newReconData = [];

        foreach ($recon as $entry) {
            $whereClause = [];
            foreach ($uniqueKeys as $key) {
                $whereClause[$key] = $entry[$key];
            }

            // Check if the record already exists
            $existingRecord = $this->billModel->checkExistingData($whereClause);

            if (!$existingRecord) {
                // Record doesn't exist, add it to inserts
                $newReconData[] = $entry;
            }
        }
        $date = date('d-m-Y H:i:s');
        // $txt = json_encode($newReconData);
        // $count = count($newReconData);
        // $this->sms->sendSms('255659851709', "test,($txt)  $count records added. Received at $date");

        // Batch insert new records to db
       // $numbers = '255659851709,255767991300,255629273164';
        if (!empty($recon)) {
            if (count($newReconData) > 0 && !empty($newReconData)) {
         
                $this->billModel->saveReconciliation($newReconData);
              //  $this->settlePayments();
                
            }
        }




        $params = (object)[
            "dataTag" =>  "gepgSpReconcResp",
            "content" => "<gepgSpReconcRespAck><ReconcStsCode>7101</ReconcStsCode></gepgSpReconcRespAck>"
        ];

        //signing ack and send back to GePG
        return $this->acknowledgement->acknowledgementProcessing($response, $params);
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
        if($qty > 0) $this->sms->sendSms($numbers, "($qty) Unsettled Transactions Found  And Settled  Date: $date");





       
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





        // get collection center number from the bill using billId
           // get collection center number from the bill using billId
       // $center = $this->billModel->getCollectionCenter($billId)->CollectionCenter;
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


    // $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));

        //signing ack and send back to GePG

    }







    


}
