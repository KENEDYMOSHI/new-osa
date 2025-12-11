<?php

namespace App\Jobs;

use LSS\XML2Array;
use App\Models\BillModel;
use App\Libraries\SmsLibrary;
use App\Libraries\Acknowledgement;



class Payment 
{
    protected $billModel;
    protected $acknowledgement;
    protected $sms;
    public function __construct(){
        $this->billModel = new BillModel();
        $this->acknowledgement =  new Acknowledgement();
        $this->sms = new SmsLibrary();
    }
    public function processPayment($data)
    {
        $response = $data['payments'];
        //get data from the callback
       // $response = file_get_contents('php://input');
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
          // $this->billModel->savePayment($payment);




           // update bill status and paid amount
            $this->billModel->updateBill($controlNumber, [
                'PaymentStatus' => $PaymentStatus,
                'PaidAmount' => $updatedAmount,
            ]);

        }
        
        
        
      
      $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));
    }
}
