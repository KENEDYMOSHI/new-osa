<?php

namespace App\Controllers;

use App\Libraries\ArrayLibrary;
use LSS\XML2Array;
use App\Models\BillModel;
use App\Libraries\SmsLibrary;

class Home extends BaseController
{
    protected $billModel;
    protected $sms;
    public function __construct()
    {
        $this->billModel = new BillModel();
        $this->sms = new SmsLibrary();
    }
    public function index(): string
    {

        $db = \Config\Database::connect();
        $data['page'] = [
            "title" => "Payments Simulation",
            "heading" => "Payments Simulation",
        ];

        $data['bills'] = $db->table('wma_bill')->select()->whereIn('PaymentStatus', ['Pending', 'Partial'])->where('IsCancelled', 'No')->orderBy('id', 'DESC')->limit(300)->get()->getResult();
        return view('Pages/Transactions/payments', $data);
    }


    public function billPaymentSimulation()
    {
        //get data from the callback
        try {

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


            $paymentOption = $data['BillPayOpt'];


            //the bill amount
            $billedAmount =  $data['BillAmt'];

            //calculating the amount of debt left.
            $debt = $billedAmount - $updatedAmount;
            $receiptNumber = $data['PspReceiptNumber'];
            $payerNumber = $data['PyrCellNum'];





            $payment = [
                'TrxId' => $data['TrxId'],
                'SpCode' => $data['SpCode'],
                'PayRefId' => $data['PayRefId'],
                'BillId' => $billId,
                'PayCtrNum' => $data['PayCtrNum'],
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





            // get collection center number from the bill using billId.
            // $center = $this->billModel->getCollectionCenter($billId)->CollectionCenter;
            $center = 'Wakala Wa Vipimo';
            // $centerName = (new ProfileModel())->findCollectionCenter($center)->centerName;
            $centerName = $center;


            $billData = $this->billModel->getAmountPaidAndCenter($controlNumber);
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

                $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));
            }



            $params = (object)[
                "dataTag" =>  "gepgPmtSpInfo",
                "responseContentAck" => "<gepgPmtSpInfoAck><TrxStsCode>7101</TrxStsCode></gepgPmtSpInfoAck>"
            ];

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Payment received successfully',

            ]);
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),

            ];
            return $this->response->setJSON($response);
        }
    }


    public function sortPartials($payments)
    {
        $partials = (new ArrayLibrary($payments))->filter(fn ($item) => $item->BillPayOpt == 2)->get();
        $consolidated = array_reduce($partials, function ($carry, $item) {
            $key = $item->controlNumber . '|' . $item->BillPayOpt;
            if ($item->BillPayOpt == 2) {
                if (!isset($carry[$key])) {
                    $carry[$key] = clone $item;
                    $carry[$key]->amount = $item->PaidAmt;
                } else {
                    $carry[$key]->PaidAmt += $item->PaidAmt;
                    $carry[$key]->amount += $item->PaidAmt;
                }
            } else {
                $carry[] = $item;
            }
            return $carry;
        }, []);
        $consolidated = array_values($consolidated);
        return $consolidated;
    }



    public function partialsPaid1($collection)
    {
        $partials = (new ArrayLibrary($collection))->filter(fn ($item) => $item->BillPayOpt == 2)->get();
        $groupedItems = [];
        foreach ($partials as $item) {
            $groupedItems[$item->controlNumber][] = $item;
        }

        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $totalPaidAmt = 0;

            // Calculate the total PaidAmt
            foreach ($items as $item) {
                $totalPaidAmt += $item->PaidAmt;
            }


            // Divide the total PaidAmt by the item count
            $amountPerItem =  ($totalPaidAmt / $itemCount);

            // Update the amount for each item
            foreach ($items as $item) {

                if ($item->clearedAmount == $item->BillAmt) {
                    $item->amount =  $item->amount;
                    $item->status =  'Completed';
                } else {
                    $item->amount = $amountPerItem;
                    $item->status =  'NOT Completed';
                }
            }
        }

        return  array_merge(...array_values($groupedItems));
    }



    public function partialsPaid3($collection)
    {
        $partials = (new ArrayLibrary($collection))->filter(fn ($item) => $item->BillPayOpt == 2)->get();
        $groupedItems = [];
        foreach ($partials as $item) {
            $groupedItems[$item->controlNumber][] = $item;
        }

        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $totalPaidAmt = 0;
            $paidAmounts = [];

            // Calculate the total PaidAmt and collect PaidAmt values
            foreach ($items as $item) {
                $totalPaidAmt += $item->PaidAmt;
                $paidAmounts[] = $item->PaidAmt;
            }

            // Divide the total PaidAmt by the item count
            $amountPerItem = $totalPaidAmt / $itemCount;

            // Update the amount and status for each item and add c_Number key
            foreach ($items as $item) {
                if ($item->clearedAmount == $item->BillAmt) {
                    $item->amount = $item->amount;
                    $item->status = 'Completed';
                } else {
                    $item->amount = $amountPerItem;
                    $item->status = 'NOT Completed';
                }
                $item->BillPaid = $paidAmounts;
            }
        }

        return array_merge(...array_values($groupedItems));
    }


    public function partialsPaid5($collection)
    {
        $partials = (new ArrayLibrary($collection))->filter(fn($item) => $item->BillPayOpt == 2)->get();
        $groupedItems = [];
        $uniqueBillItemRefs = [];
        $paidAmountsPerControl = [];
    
        foreach ($partials as $item) {
            if (!isset($paidAmountsPerControl[$item->controlNumber])) {
                $paidAmountsPerControl[$item->controlNumber] = [];
            }
            $paidAmountsPerControl[$item->controlNumber][] = $item->PaidAmt;
    
            if (!isset($uniqueBillItemRefs[$item->controlNumber])) {
                $uniqueBillItemRefs[$item->controlNumber] = [];
            }
    
            if (!in_array($item->BillItemRef, $uniqueBillItemRefs[$item->controlNumber])) {
                $uniqueBillItemRefs[$item->controlNumber][] = $item->BillItemRef;
                $groupedItems[$item->controlNumber][] = $item;
            }
        }
    
        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $totalPaidAmt = array_sum($paidAmountsPerControl[$controlNumber]);
            $paidAmounts = $paidAmountsPerControl[$controlNumber];
    
            // Divide the total PaidAmt by the item count
            $amountPerItem = $totalPaidAmt / $itemCount;
    
            // Update the amount and status for each item and add c_Number key
            foreach ($items as $item) {
                if ($item->clearedAmount == $item->BillAmt) {
                    $item->amount = $item->amount;
                    $item->status = 'Completed';
                } else {
                    $item->amount = $amountPerItem;
                    $item->status = 'NOT Completed';
                }
                $item->c_Number = $paidAmounts;
            }
        }
    
        return array_merge(...array_values($groupedItems));
    }
    

    public function partialsPaid($collection)
    {
        $partials = (new ArrayLibrary($collection))->filter(fn($item) => $item->BillPayOpt == 2)->get();
        $groupedItems = [];
        $uniqueBillItemRefs = [];
        $paidAmountsPerControl = [];
        $uniquePayRefIds = [];
    
        foreach ($partials as $item) {
            if (!isset($paidAmountsPerControl[$item->controlNumber])) {
                $paidAmountsPerControl[$item->controlNumber] = [];
                $uniquePayRefIds[$item->controlNumber] = [];
            }
    
            // Only add the PaidAmt if the PayRefId is unique for this controlNumber
            if (!in_array($item->PayRefId, $uniquePayRefIds[$item->controlNumber])) {
                $paidAmountsPerControl[$item->controlNumber][] = $item->PaidAmt;
                $uniquePayRefIds[$item->controlNumber][] = $item->PayRefId;
            }
    
            if (!isset($uniqueBillItemRefs[$item->controlNumber])) {
                $uniqueBillItemRefs[$item->controlNumber] = [];
            }
    
            if (!in_array($item->BillItemRef, $uniqueBillItemRefs[$item->controlNumber])) {
                $uniqueBillItemRefs[$item->controlNumber][] = $item->BillItemRef;
                $groupedItems[$item->controlNumber][] = $item;
            }
        }
    
        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $totalPaidAmt = array_sum($paidAmountsPerControl[$controlNumber]);
         //   $paidAmounts = $paidAmountsPerControl[$controlNumber];
    
            // Divide the total PaidAmt by the item count
            $amountPerItem = $totalPaidAmt / $itemCount;
    
            // Update the amount and status for each item and add c_Number key
            foreach ($items as $item) {
                if ($item->clearedAmount == $item->BillAmt) {
                    $item->amount = $item->amount;
                    $item->status = 'Completed';
                } else {
                    $item->amount = $amountPerItem;
                    $item->status = 'NOT Completed';
                }
              //  $item->paidAmountsInCn = $paidAmounts;
            }
        }
    
        return array_merge(...array_values($groupedItems));
    }
    

    public function partialsUnpaid($collection)
    {
        $pendingPartials = (new ArrayLibrary($collection))->filter(fn ($item) => $item->BillPayOpt == 2 && $item->PaymentStatus == 'Partial')->get();
        $groupedItems = [];
        foreach ($pendingPartials as $item) {
            $groupedItems[$item->controlNumber][] = $item;
        }

        // Update amount for each group
        foreach ($groupedItems as $controlNumber => $items) {
            $itemCount = count($items);
            $remainingAmount = $items[0]->BillAmt - $items[0]->PaidAmount;
            $amountPerItem = $remainingAmount / $itemCount;

            foreach ($items as $item) {
                $item->amount = $amountPerItem;
            }
        }



        return array_merge(...array_values($groupedItems));
    }



    public function region()
    {
        try {
            $db = \Config\Database::connect();

            $paymentStatus = 'all';

            // $status = ['Partial', 'Pending'];

            // if ($paymentStatus == 'Partial') {
            //     $key = array_search('Pending', $status);
            //     if ($key !== false) {
            //         unset($status[$key]);
            //     }
            // }
            // $pending = [
            //     'MONTH(wma_bill.CreatedAt)' => '4',
            //     'YEAR(wma_bill.CreatedAt)' => '2024',
            //     'CollectionCenter' => '0030',
            //     //  'GfsCode' => '142101210037'
            //     // 'controlNumber' => '994191514237'

            // ];
            // $pendingPartials = $db->table('wma_bill')->select('PyrName as customer,controlNumber,CollectionCenter as region,GfsCode,ItemName,PaidAmount,BillAmt,BillItemAmt  as amount,wma_bill.CreatedAt,BillPayOpt, PaymentStatus')
            //     ->where($pending)
            //     ->whereIn('PaymentStatus', $status)
            //     ->limit(30)
            //     ->join('bill_items', 'wma_bill.PayCntrNum = bill_items.controlNumber', 'inner')
            //     ->get()
            //     ->getResult();


            // // $pending = (new ArrayLibrary($pendingPartials))->filter(fn ($item) =>  $item->PaymentStatus == 'Pending')->get();
            // // $partial = $this->partialsUnpaid($pendingPartials);

            // // echo '<br>';
            // // // printer($status);
            // // printer($pending);

            // // exit();










            $params = [
               // 'MONTH(bill_payment.TrxDtTm)' => '4',
                'YEAR(bill_payment.TrxDtTm)' => '2024',
                // 'bill_payment.CenterNumber' => '0003',
                // 'GfsCode' => '142101210035',
                //'controlNumber' => '994191491964',
                //    'controlNumber' => '994191487877',
            ];

            $collection = $db->table('bill_payment')->select('BillItemRef,PayRefId,PyrName as customer,controlNumber,CenterNumber as region,GfsCode,ItemName,PaidAmt,clearedAmount,BillAmt,BillItemAmt  as amount,TrxDtTm,BillPayOpt,"Paid" as PaymentStatus')
                ->where($params)
                // ->limit(20)
                ->join('bill_items', 'bill_payment.PayCtrNum = bill_items.controlNumber', 'inner')
                ->get()
                ->getResult();




            $full = (new ArrayLibrary($collection))->filter(fn ($item) => $item->BillPayOpt == 3)->get();
            // $partials = $this->sortPartials($collection);
            $partials = $this->partialsPaid($collection);





            $allPayments = array_merge($partials);

            // // Step 2: Extract values to ensure keys are sequential

            $total = array_reduce($allPayments, function ($carry, $item) {
                $carry += $item->amount;
                return $carry;
            }, 0);

            echo '<br>';
            echo 'TOTAL AMOUNT ' . number_format($total);
            echo '<br>';
            // // echo 'All partials' . count($partials);
            // echo '<br>';
            // echo '<br>';
            // // echo 'Sorted partials' . count($consolidated);
            // echo '<br>';
            printer($allPayments);
            // echo '<br>';
            // echo '<br>';


            exit;




            $billData  = $db->table('bill_payment')->select('bill_payment.id,CollectionCenter as CenterNumber')
                ->join('wma_bill', 'bill_payment.PayCtrNum = wma_bill.PayCntrNum')
                ->where(['CenterNumber =' => NULL])->limit()->get()->getResult();

            $count = $db->table('bill_payment')->select()->where(['CenterNumber =' => NULL])->get()->getResult();
            $count2 = $db->table('bill_payment')->selectCount('id')->get()->getResult();

            $update = $db->table('bill_payment')->updateBatch($billData, 'id');

            if ($update) {
                $response = [
                    'status' => 1,
                    'msg' => 'Center Updated',

                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Something went wrong',

                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),

            ];
        }
        return $this->response->setJSON($response);
    }
    public function regions()
    {
        try {
           echo (new SmsLibrary())->sendSms(recipient: '0659851709', message: 'Hello');
            exit;
            $db = \Config\Database::connect();

            $fileUrl = base_url('search');

            // Redirect to a new tab using JavaScript
            echo '<script type="text/javascript">
            window.onload = function() {
                window.open("phind.com", "_blank");
            };
          </script>';
          //  echo '<script>alert("hello world");</script>';
            return;
           // echo "text";

            // exit;
    


            // $billData  = $db->table('bill_payment')->select('bill_payment.id,CollectionCenter as CenterNumber')
            //     ->join('wma_bill', 'bill_payment.PayCtrNum = wma_bill.PayCntrNum')
            //     ->where(['CenterNumber =' => NULL])->get()->getResult();

            // $count = $db->table('bill_payment')->select()->where(['CenterNumber =' => NULL])->get()->getResult();
            // $count2 = $db->table('bill_payment')->selectCount('id')->get()->getResult();

            // $update = $db->table('bill_payment')->updateBatch($billData, 'id');

            // if ($update) {
            //     $response = [
            //         'status' => 1,
            //         'msg' => 'Center Updated',

            //     ];
            // } else {
            //     $response = [
            //         'status' => 0,
            //         'msg' => 'Something went wrong',

            //     ];
            // }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),

            ];
        }
        return $this->response->setJSON($response);
    }
}