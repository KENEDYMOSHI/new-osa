<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;
use App\Libraries\SmsLibrary;

class BillModel extends Model
{
    protected $billTable;
    protected $trBillTable;
    protected $billItems;
    protected $trBillItems;
    protected $db;
    protected $gepg;
    protected $ack;
    protected $res;
    protected $billPayment;
    protected $trPayment;
    protected $reconRequest;
    protected $reconciliations;
    protected $cancellation;
    protected $cancellationRequest;
    protected $reconBatchInfo;
    protected $gfsCode;
    protected $collectionCenter;
    protected $condemned;
    protected $ppgData;
    protected $partialReference;
    protected $controlNumberResponse;
    protected $paymentBackup;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->billTable = $this->db->table('wma_bill');
        $this->trBillTable = $this->db->table('tr_bill');
        $this->billItems = $this->db->table('bill_items');
        $this->trBillItems = $this->db->table('tr_bill_items');
        $this->gepg = $this->db->table('gepg');
        $this->ack = $this->db->table('ack');
        $this->res = $this->db->table('res');
        $this->billPayment = $this->db->table('bill_payment');
        $this->trPayment = $this->db->table('tr_payment');
        $this->reconRequest = $this->db->table('bill_reconciliation');
        $this->reconciliations = $this->db->table('reconciliation');
        $this->reconBatchInfo = $this->db->table('recon_batch_info');
        $this->cancellation = $this->db->table('bill_cancellation');
        $this->cancellationRequest = $this->db->table('bill_cancellation_request');
        $this->gfsCode = $this->db->table('gfscode');
        $this->collectionCenter = $this->db->table('collectioncenter');
        $this->condemned = $this->db->table('condemned');
        $this->ppgData = $this->db->table('ppg_imported ');
        $this->partialReference = $this->db->table('partial_reference ');
        $this->controlNumberResponse = $this->db->table('control_number');
        $this->paymentBackup = $this->db->table('paid');

        // $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'"); 
    }

    public function addControlNumber($data)
    {
        return $this->controlNumberResponse->insertBatch($data);
    }

    //check if payment was made for a certain instrument 0757 454034
    public function verifyInstrument($params)
    {
        return $this->billItems->select()->where($params)->get()->getRow();
    }

    public function sqlMode()
    {
        $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }


    //creating condemned items
    public function saveCondemnedItems($data)
    {
        return $this->condemned->insertBatch($data);
    }
    //creating new customer
    public function saveBill($data)
    {
        unset($data['BillItems']);
        return $this->billTable->insert($data);
    }
    public function saveTrBill($data)
    {
        unset($data['BillItems']);
        // array_map(function ($item) {

        //     return $item;
        // }, $data);
        $amount =  $data['BillAmt'] * 0.15;
        $data['BillAmt'] = $amount;
        $data['BillEqvAmt'] = $amount;
        return $this->trBillTable->insert($data);
    }

    public function saveResponse($data)
    {
        return $this->res->insert($data);
    }
    //save tnx ack
    public function savePartialReference($data)
    {
        return $this->partialReference->insertBatch($data);
    }
    public function saveAck($data)
    {
        return $this->ack->insert($data);
    }
    //save cancellation req
    public function saveCancellation($data)
    {
        return $this->cancellation->insert($data);
    }
    //save cancellation req
    public function saveCancellationRequest($data)
    {
        return $this->cancellationRequest->insert($data);
    }
    //creating bill items
    public function saveBillItems($data)
    {
        return $this->billItems->insertBatch($data);
    }
    public function saveTrBillItems($data)
    {
        return $this->trBillItems->insertBatch($data);
    }
    public function savePrepackageData($data)
    {
        return $this->ppgData->insert($data);
    }


    public function updateBillControlNumber($requestId, $data) {}


    //updating control number when bill submitted successfully
    public function updateControlNumber($requestId, $data)
    {
        // Start transaction to ensure atomicity
        $this->db->transStart();

        // Prepare control number for use in both billTable and billItems
        $controlNumber = $data['PayCntrNum'];

        // Update billTable and trBillTable using the same data
        $updateData = ['controlNumber' => $controlNumber, 'billControlNumber' => $data['billControlNumber']];

        // Perform updates in both tables
        $this->billTable->where('RequestId', $requestId)->set($data)->update();

        // $this->trBillTable->where('RequestId', $requestId)->set($data)->update();

        // Update control number in billItems table
        $this->billItems->where('RequestId', $requestId)->set($updateData)->update();

        // Complete the transaction
        $this->db->transComplete();

        // Check if the transaction was successful
        if ($this->db->transStatus() === false) {
            // Transaction failed, handle the error
            log_message('error', 'Failed to update control number for RequestId: ' . $requestId);
            return false;
        }

        return true;
    }



    public function updateDate($billId, $data)
    {
        return $this->billTable
            ->where(['BillId' => $billId])
            ->update($data);
    }

    public function updateCancellationStatus($requestId, $data)
    {
        return $this->billTable
            ->where(['RequestId' => $requestId])
            ->update($data);
    }

    public function updateCancellationRequest($requestId, $data)
    {
        return $this->cancellationRequest
            ->where(['RequestId' => $requestId])
            ->update($data);
    }
    public function deleteCancellationRequest($requestId)
    {
        return $this->cancellationRequest
            ->where(['RequestId' => $requestId])
            ->delete();
    }

    public function getBillDetails($RequestId)
    {
        return $this->billTable->select('BillId,PayCntrNum,method,SwiftCode')->where(['RequestId' => $RequestId])->get()->getRow();
    }
    public function getExpiredBills($params)
    {


        // Dynamically build additional conditions based on $params
        foreach ($params as $key => $value) {
            $conditions[$key] = $value;
        }

        return $this->billTable->select(
            'wma_bill.BillId,
            PyrCellNum as phoneNumber,
            PyrName as payer,
            PayCntrNum as controlNumber,
            BillAmt as amount,
            PaymentStatus as status,
            PaidAmount as paidAmount,
            CollectionCenter as region,
            ItemName,
            BillExprDt as expiryDate'
        )
            ->join('bill_items', 'bill_items.BillId = wma_bill.BillId')
            ->where($params)
            ->groupStart()
            ->where(['PaymentStatus' => 'Pending'])
            ->orWhere(['PaymentStatus' => 'Partial'])
            ->groupEnd()
            ->get()
            ->getResult();
    }


    public function getBillResponse($requestId)
    {
        return $this->controlNumberResponse->select()
            ->where(['RequestId' => $requestId])
            ->orderBy('id', 'DESC')
            ->limit(2)
            ->get()
            ->getResult();
    }

    public function verifyPaymentExistence($params)
    {
        $builder = $this->billPayment->select()
            ->where($params)
            ->get()
            ->getRow();

        return $builder;
    }
    public function getControlNumber($billId)
    {
        return $this->gepg->select('PayCntrNum')
            ->where(['BillId' => $billId])
            ->get()
            ->getRow();
    }
    public function getCollectionCenters()
    {
        return $this->collectionCenter->select()
            ->get()
            ->getResult();
    }
    public function fetchBill($RequestId)
    {
        return $this->billTable->select('wma_bill.*,collectioncenter.*,wma_bill.id as theId')
            ->where(['RequestId' => $RequestId])
            ->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter', 'left')
            ->get()
            ->getRow();
    }
    public function fetchTrBill($RequestId)
    {
        return $this->trBillTable->select('tr_bill.*,collectioncenter.*,tr_bill.id as theId')
            ->where(['RequestId' => $RequestId])
            ->join('collectioncenter', 'collectioncenter.CenterNumber = tr_bill.CollectionCenter', 'left')
            ->get()
            ->getRow();
    }
    public function getBill($controlNumber)
    {
        return $this->billTable->select()
            ->where(['PayCntrNum' => $controlNumber])
            ->get()
            ->getRow();
    }
    public function fetchPayment($paymentRef)
    {
        return $this->billTable->select(
            "wma_bill.id,
        wma_bill.BillId,
        BillTyp,
        wma_bill.Ccy,
        wma_bill.PyrName,
        bill_payment.PyrCellNum,
        PaidAmt,
        clearedAmount,
        PayRefId,
        bill_payment.BillAmt,
        PayCntrNum,
        PspReceiptNumber,
        BillDesc,
        TrxDtTm,
        TrxId,
        BillRef,
        BillGenDt,
        BillGenBy
        "
        )
            ->where(['bill_payment.PayRefId' => $paymentRef])
            ->join('bill_payment', 'bill_payment.PayCtrNum = wma_bill.PayCntrNum')

            ->get()
            ->getRow();
    }

    //=================get paid amount for partial bills====================
    public function getPaidAmount($billId)
    {
        return $this->billTable->select('PaidAmount')
            ->where(['BillId' => $billId])
            ->get()
            ->getRow();
    }

    public function getAmountPaidAndCenter($controlNumber, $groupBillId)
    {
        return $this->billTable->select('PaidAmount,CollectionCenter')
            ->where(['PayCntrNum' => $controlNumber])
            ->orWhere(['GrpBillId' => $groupBillId])
            ->get()
            ->getRow();
    }
    public function verifyPayment($controlNumber)
    {
        $payment = $this->billPayment->select('PayCtrNum')
            ->where(['PayCtrNum' => $controlNumber])
            ->get()
            ->getRow();
        if (!empty($payment)) {
            return true;
        } else {
            return false;
        }
    }

    //get payments via control number
    public function getPayments($controlNumbers)
    {
        return $this->billPayment->select('PayCtrNum,PaidAmt,CenterNumber')
            ->whereIn('PayCtrNum', $controlNumbers)
            ->get()
            ->getResult();
    }

    public function updateBillItem($ref, $data)
    {

        return $this->billItems->select()
            ->where(['BillItemRef' => $ref])
            ->set($data)
            ->update();
    }
    public function getBillItems($ref)
    {
        $query = $this->billItems->select()
            ->where(['BillItemRef' => $ref]);


        return $query->get()->getResult();
    }
    public function fetchBillItems($RequestId, $activity = '')
    {
        $query = $this->billItems->select()
            ->where(['RequestId' => $RequestId]);

        if (!empty($activity)) {
            $query->where(['GfsCode' => $activity]);
        }

        return $query->get()->getResult();
    }
    public function fetchTrBillItems($RequestId, $activity = '')
    {
        $query = $this->trBillItems->select()
            ->where(['RequestId' => $RequestId]);

        if (!empty($activity)) {
            $query->where(['GfsCode' => $activity]);
        }

        return $query->get()->getResult();
    }


    public function fetchReceipt($billId)
    {
        return $this->billTable->select('
          wma_bill.BillId,
          wma_bill.PayCntrNum,
          PyrName,
          BillAmt,
          BillExprDt,
          BillDesc,
          BillGenDt,
          BillGenBy,
          BillApprBy,
          PyrCellNum,
          TrxId,
          fob,
          tansardNumber,
          date,
          GfsCode 
          

        ')
            ->where(['BillId' => $billId])
            ->join('bill_payment', 'bill_payment.BillId = wma_bill.BillId')
            ->get()
            ->getResult();
    }
    public function saveControlNumber($data)
    {
        return $this->gepg->insert($data);
    }

    public function getBills()
    {
        return $this->billTable->select()->orderBy('id', 'DESC')->get()->getResult();
    }

    //save payment resp data
    public function savePayment($data)
    {
        $paymentExists = $this->billPayment->select()->where('PayRefId', $data->PayRefId)->get()->getRow();

        if (!$paymentExists) {

            return $this->billPayment->insert($data);
        }
    }
    //save payment resp data
    //save payment resp data
    public function savePaymentData($data)
    {
        // Check for duplicates based on multiple fields if needed
        $paymentExists = $this->billPayment
            ->where('PayRefId', $data['PayRefId'])
            ->where('PaidAmt', $data['PaidAmt'])
            ->where('PayCtrNum', $data['PayCtrNum'])
            ->countAllResults() > 0;

        if (!$paymentExists) {
            return $this->billPayment->insert($data);
        }

        return false;
    }





    public function savePaymentBackup($data)
    {
        return $this->paymentBackup->insert($data);
    }
    public function saveTrPayment($data, $fullBillAmount)
    {
        $this->trPayment->insert($data);

        $controlNumber = $data->PayCtrNum;

        $trPaid = $data->PaidAmt;


        $this->trBillTable
            ->where(['PayCntrNum' => $controlNumber])
            ->set([
                'PaidAmount' => $trPaid,
                'PaymentStatus' => 'Paid',
                'FullPaidAmount' => $fullBillAmount,
                'NetAmount' => $fullBillAmount - $trPaid,
            ])
            ->update();
    }
    public function savePayments($data)
    {
        return $this->billPayment->insertBatch($data);
    }
    //save recon request data
    public function saveReconciliationRequest($data)
    {
        return $this->reconRequest->insert($data);
    }






    //save recon request data
    // Save reconciliation request data
    // Save reconciliation request data
    public function saveReconciliation($data)
    {
        $transactionCount = 0;

        foreach ($data as $item) {
            // Prepare the parameters for the query
            $params = [
                'PayRefId' => $item['PayRefId'],
                'BillCtrNum' => $item['BillCtrNum']
            ];

            $transactionDate = $data[0]['TrxDtTm'];
            $date = date('d M,Y');

            // Check if the record exists
            $existingRecord = $this->reconciliations->where($params)->get()->getRow();

            // If the record does not exist, insert the new data
            if (!$existingRecord) {
                if ($this->reconciliations->insert($item)) {
                    $transactionCount++;
                }
            }
        }
        if ($transactionCount > 0) {
            $numbers = '255659851709,255767991300,255629273164';
            (new SmsLibrary())->sendSms($numbers, "Transaction Date: $transactionDate , Reconciliation Successful,($transactionCount) Transactions inserted. Received at $date");
        }
    }



    // public function saveReconciliation($data)
    // {
    //     // Determine a suitable batch size
    //     $batchSize = 200; // You can adjust this based on your testing and database constraints

    //     // Initialize a variable to track the result of each batch insertion
    //     $allBatchesInserted = true;

    //     // Get the database connection for transaction management
    //     $db = \Config\Database::connect();

    //     // Start a database transaction
    //     $db->transStart();

    //     try {
    //         // Loop through the data array in chunks of the batch size
    //         for ($i = 0; $i < count($data); $i += $batchSize) {
    //             // Slice the data array into batches
    //             $batchData = array_slice($data, $i, $batchSize);

    //             // Insert the batch data and check the result
    //             $result = $this->reconciliations->insertBatch($batchData);

    //             // If any batch fails, mark the operation as failed
    //             if ($result === false) {
    //                 $allBatchesInserted = false;
    //                 break;
    //             }
    //         }

    //         // Complete the transaction based on the success of batch insertions
    //         if ($allBatchesInserted) {
    //             $db->transComplete();

    //             if ($db->transStatus() === false) {
    //                 // If transaction fails, handle the error
    //                 throw new \Exception('Transaction failed during batch insert');
    //             }
    //         } else {
    //             // If any batch failed, rollback the transaction
    //             $db->transRollback();
    //             throw new \Exception('Batch insert failed');
    //         }

    //     } catch (\Exception $e) {
    //         // Rollback the transaction in case of any exception
    //         $db->transRollback();
    //         // Handle the exception as needed
    //         echo $e->getMessage();
    //         return false;
    //     }

    //     // Return true if all batches were inserted successfully
    //     return true;
    // }



    public function checkExistingData($data)
    {
        return  $this->reconciliations->where($data)->get()->getRow();
    }




    //save recon batch info request data
    public function saveReconInfo($data)
    {
        return $this->reconBatchInfo->insert($data);
    }
    //save recon batch info request data
    public function getLastReconStatus($reconId)
    {
        return $this->reconBatchInfo->select()->where(['SpReconcReqId' => $reconId])->orderBy('id', 'DESC')->limit(1)->get()->getResult();
    }

    //get recon request data
    public function fetchReconciliation()
    {
        return $this->reconciliations->select()->groupBy(['id', 'BillCtrNum'])->orderBy('id', 'DESC')->limit(5000)->get()->getResult();
    }

    //get recon data according to params supplied
    public function getReconData($params)
    {
        return $this->reconciliations->select()->where($params)->get()->getResult();
    }

    //save payment data from the params
    public function getPaymentData($params)
    {
        return $this->billPayment->select()->where($params)->get()->getResult();
    }



    //update Bill details
    public function updateBill($controlNumber, $data)
    {
        $bill = $this->billTable->where(['PayCntrNum' => $controlNumber])->get()->getRow();

        if ($bill) {
            $this->billTable
                ->where(['PayCntrNum' => $controlNumber])
                ->set($data)
                ->update();
        } else {
            $gepg = $this->gepg->select()->where(['PayCntrNum' => $controlNumber])->get()->getRow();
            $billId = $gepg->BillId;

            $data['PayCntrNum'] = $controlNumber;

            $this->billTable
                ->where(['BillId' => $billId])
                ->set($data)
                ->update();
        }
    }

    
    public function updateTrBill($controlNumber, $data)
    {

        return  $this->trBillTable
            ->where(['PayCntrNum' => $controlNumber])
            ->set($data)
            ->update();
    }
    public function updateBillPayment($controlNumber, $data)
    {
        $this->billTable
            ->where(['PayCntrNum' => $controlNumber])
            ->set($data)
            ->update();

        //calculate 15% of $data['PaidAmount']
        $paidAmount = (int)$data['PaidAmount'];
        $fifteenPercent = ($paidAmount * 15) / 100;


        $this->trBillTable
            ->where(['PayCntrNum' => $controlNumber])
            ->set([
                'PaidAmount' => $fifteenPercent,
                'PaymentStatus' => 'Paid',
                'FullPaidAmount' => $paidAmount,
                'NetAmount' => $paidAmount - $fifteenPercent,
            ])
            ->update();

        return true;
    }




    public function updateBillItems($data)
    {
        return $this->billItems->updateBatch($data, 'BillItemRef');
    }
    public function updateTrBillItems($data)
    {
        return $this->trBillItems->updateBatch($data, 'BillItemRef');
    }







    //searching existing customer
    public function searchBill($params, $PyrName, $activity)
    {


        $builder = $this->db->table('wma_bill');


        // $builder->join('bill_items', "bill_items.BillId = wma_bill.BillId");
        $builder->select('wma_bill.*, control_number.controlNumber as PayCntrNum');
        $builder->join('control_number', "control_number.requestId = wma_bill.RequestId");
        if ($PyrName != '') $builder->like(['wma_bill.PyrName' => $PyrName]);
        if ($activity != '') $builder->like(['wma_bill.Activity' => $activity]);

        if (count($params) > 0) $builder->where($params);

        // $builder->select();
        $builder->orderBy('id', 'DESC');
        return $builder->get()->getResult();
        // return $builder->getCompiledSelect();
    }


    public function searchBillApi($params, $keyword)
    {
        $builder = $this->db->table('wma_bill');

        $builder->select();

        if ($keyword != '') {
            $builder->groupStart(); // Start grouping conditions
            $builder->like('wma_bill.PyrName', $keyword);
            $builder->orLike('wma_bill.PayCntrNum', $keyword);
            $builder->orLike('wma_bill.PyrCellNum', $keyword);
            $builder->groupEnd(); // End grouping conditions
        }

        if (count($params) > 0) {
            $builder->groupStart(); // Start grouping conditions
            $builder->where($params);
            $builder->groupEnd(); // End grouping conditions
        }

        $builder->groupStart(); // Start grouping conditions
        $builder->where('IsCancelled', 'No');
        $builder->where('PaymentStatus', 'Pending');
        $builder->orWhere('PaymentStatus', 'Partial');
        $builder->groupEnd(); // End grouping conditions

        $builder->orderBy('wma_bill.CreatedAt', 'DESC');
        $builder->limit(100);

        return $builder->get()->getResult();
    }



    public function searchPayment(array $params, string $pyrName)
    {
        $builder = $this->db->table('bill_payment');

        if (!empty($pyrName)) {
            $builder->like('wma_bill.PyrName', $pyrName);
        }

        if (!empty($params)) {
            $builder->where($params);
        }

        $builder->select(
            'wma_bill.id,
    bill_payment.BillId, 
    wma_bill.RequestId,
    PaymentStatus,
    PaidAmount,
    wma_bill.Ccy,
    wma_bill.PyrName,
    bill_payment.PyrCellNum,
    PaidAmt,
    clearedAmount,
    PayRefId,
    PayCtrNum,
    PspReceiptNumber,
    TrxDtTm,
    BillRef,
    PspName,
    UsdPayChnl,
    bill_payment.BillAmt,
    BillGenDt,
    BillGenBy'

        );

        $builder->orderBy('bill_payment.id', 'DESC');

        $builder->join('wma_bill', 'bill_payment.PayCtrNum = wma_bill.PayCntrNum');

        return $builder->get()->getResult();
    }







    public function searchPayment11(array $params, string $pyrName)
    {
        $builder = $this->db->table('wma_bill');

        if (!empty($pyrName)) {
            $builder->like('wma_bill.PyrName', $pyrName);
        }

        if (!empty($params)) {
            $builder->where($params);
        }

        $builder->select(
            'wma_bill.id,
    wma_bill.BillId, 
    PaymentStatus,
    PaidAmount,
    wma_bill.Ccy,
    wma_bill.PyrName,
    bill_payment.PyrCellNum,
    PaidAmt,
    clearedAmount,
    PayRefId,
    PayCntrNum,
    PspReceiptNumber,
    TrxDtTm,
    BillRef,
    PspName,
    UsdPayChnl,
    bill_payment.BillAmt,
    BillGenDt,
    BillGenBy'

        );

        $builder->orderBy('wma_bill.id', 'DESC');

        $builder->join('bill_payment', 'bill_payment.PayCtrNum = wma_bill.PayCntrNum', 'left');

        return $builder->get()->getResult();
    }



    //###################################################
    public function searPayment($params, $PyrName)
    {


        $builder = $this->db->table('wma_bill');


        // $builder->join('bill_items', "bill_items.BillId = wma_bill.BillId");
        if ($PyrName != '') $builder->like(['wma_bill.PyrName' => $PyrName]);

        if (count($params) > 0) $builder->where($params);

        $builder->select(
            "wma_bill.id,
      wma_bill.BillId,
      PaymentStatus,
      PaidAmount,
      wma_bill.Ccy,
      bill_payment.PyrName,
      bill_payment.PyrCellNum,
      PaidAmt,
      PayRefId,
      PayCntrNum,
      PspReceiptNumber,
      TrxDtTm,
      BillRef,
      bill_payment.BillAmt,
      BillGenDt,
      BillGenBy
      "
        );
        // $builder->select();
        $builder->orderBy('wma_bill.id', 'DESC');
        $builder->join('bill_payment', 'bill_payment.BillId = wma_bill.BillId', 'right');
        return $builder->get()->getResult();
        // return $builder->getCompiledSelect();
    }
    public function searPaymentApi($params, $keyword)
    {


        $builder = $this->db->table('wma_bill');


        // $builder->join('bill_items', "bill_items.BillId = wma_bill.BillId");



        $builder->select(
            "wma_bill.id,
      wma_bill.BillId,
      PaymentStatus,
      PaidAmount,
      wma_bill.Ccy,
      wma_bill.PyrName,
      bill_payment.PyrCellNum,
      PaidAmt,
      PayRefId,
      PayCntrNum,
      PspReceiptNumber,
      PaymentStatus,
      TrxDtTm,
      BillRef,
      bill_payment.BillAmt,
      BillGenDt,
      BillGenBy
      "
        );
        if ($keyword != '') {
            $builder->like(['wma_bill.PyrName' => $keyword]);
            $builder->orLike(['wma_bill.PayCntrNum' => $keyword]);
            $builder->orLike(['wma_bill.PyrCellNum' => $keyword]);
            $builder->orLike(['wma_bill.PyrName' => $keyword]);
        }
        if (count($params) > 0) $builder->where($params);
        $builder->orderBy('wma_bill.id', 'DESC');
        $builder->limit(20);
        $builder->join('bill_payment', 'bill_payment.BillId = wma_bill.BillId', 'right');
        return $builder->get()->getResult();
        // //return $builder->getCompiledSelect();
    }

    public function getPaymentAmounts($billId)
    {
        return  $this->billPayment->selectSum('PaidAmt')->where(['BillId' => $billId])->get()->getResult();
    }

    public function getBillPaymentAmounts($controlNumber)
    {
        return  $this->billPayment->selectSum('PaidAmt')->where(['PayCtrNum' => $controlNumber])->get()->getResult();
    }







    //get single Bill 
    public function selectBill($billId)
    {

        $builder = $this->db->table('wma_bill');
        $builder->where(['BillId' => $billId]);
        $builder->select();
        return $builder->get()->getRow();
    }
    //get single Bill 
    public function selectPayment($billId)
    {

        $builder = $this->db->table('wma_bill');
        $builder->select("wma_bill.id,
        wma_bill.BillId,
        PaidAmount,
        wma_bill.Ccy,
        wma_bill.PyrName,
        PaidAmt,
        PayRefId,
        PayCntrNum,
        PspReceiptNumber,
        TrxDtTm,
        BillRef,
        BillGenDt,
        BillGenBy");
        $builder->where(['BillId' => $billId]);
        $builder->join('bill_payment', 'bill_payment.BillId = wma_bill.BillId');
        return $builder->get()->getRow();
    }

    //get single Bill 
    public function getCancelledBills($params)
    {

        return $this->cancellation
            ->select('
      PyrName,
      PayCntrNum,
      BillAmt,
      BillGenDt,
      BillGenBy,
      CanclReasn,
      CanceledBy,
      ApprovedBy,
      CollectionCenter,
      bill_cancellation.CreatedAt as CancelDate

      ')
            ->where($params)
            ->join('wma_bill', 'wma_bill.RequestId = bill_cancellation.RequestId')
            ->get()
            ->getResult();
    }
    public function getBillCancellationRequests()
    {
        $user = auth()->user();
        $params = [
            'approved' => 'No',
            'centerNumber' => $user->inGroup('manager', 'accountant') ? $user->collection_center : ''
        ];
        $filtered = array_filter($params, fn($param) => $param !== '');

        return $this->cancellationRequest
            ->select('
        wma_bill.billId,
        isTrBill,
        wma_bill.RequestId,
        PyrName,
        PayCntrNum,
        BillAmt,
        BillGenDt,
        BillGenBy,
        reason,
        username as requestedBy
      
  
        ')
            ->join('wma_bill', 'bill_cancellation_request.controlNumber = wma_bill.PayCntrNum')
            ->join('users', 'users.unique_id = bill_cancellation_request.userId')
            ->where($filtered)
            ->where('controlNumber != ""')
            ->get()
            ->getResult();
    }

    public function getCollectionCenter($RequestId)
    {
        return $this->billTable
            ->select('CollectionCenter')
            ->where(['RequestId' => $RequestId])
            ->get()
            ->getRow();
    }



    //get latest inserted customer hash 
    public function lastHash()
    {

        return $this->billTable
            ->select('id,hash')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }

    public function getTrCollection($params)
    {
        // Separate 'Activity' from $params if it exists


        $query = $this->trBillTable
            ->select('
                BillId,
                PaymentStatus,
                PaidAmount as amount,
                FullPaidAmount,
                NetAmount,
                CollectionCenter,
                CreatedAt
            ')
            ->where($params);
        // ->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter');

        return $query->get()->getResult();
    }
    public function getPaymentCollection($params)
    {
        // Call Backend API
        $apiUrl = 'http://localhost:8080/api/payment/collection';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['params' => $params]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            log_message('error', 'Failed to fetch Payment Collection from API. HTTP Code: ' . $httpCode);
            return [];
        }
        
        return json_decode($response);
    }

    public function allCollection($params)
    {
         // This method historically queried 'wma_bill' which is now in Backend.
         // Returns empty array to prevent connection errors if called.
         return [];
    }
    
    public function getReportData($params)
    {
        // Call Backend API
        $apiUrl = 'http://localhost:8080/api/payment/report-data';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['params' => $params]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            log_message('error', 'Failed to fetch Report Data from API. HTTP Code: ' . $httpCode);
            return [];
        }
        
        return json_decode($response);
    }

    public function getBillReceivableData($params)
    {


        $query = $this->billTable
            ->select('
                wma_bill.BillId,
              
                PaymentStatus,
                PyrName,
                PyrCellNum,
                BillAmt as amount,
                PaidAmount,
                BillGenDt,
                PayCntrNum,
                BillGenBy,
                BillExprDt,
                CollectionCenter,
                CenterName,
                Ccy,
                wma_bill.CreatedAt
            ')
            // ->where('PayCntrNum !=','')
            ->where($params)
            ->whereIn('PaymentStatus', ['Pending', 'Partial']);



        $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter');
        // $query->groupBy('PayCntrNum');

        return $query->get()->getResult();
    }






















    public function getReportDataPartial($params)
    {
        // Separate 'Activity' from $params if it exists..
        // $activityLike = null;
        // if (isset($params['Activity'])) {
        //     $activityLike = $params['Activity'];
        //     unset($params['Activity']);
        // }

        $query = $this->billPayment
            ->select('
        bill_payment.BillId,
        Activity,
        PaymentStatus,
      
        PaidAmt as amount,
        CollectionCenter,
        CenterName,
      
        bill_payment.CreatedAt
    ')
            /// ->where('PayCntrNum !=','')
            ->where($params);

        // Add 'Activity' condition to LIKE if it's provided
        // if ($activityLike !== null) {
        //     $query->like('Activity', $activityLike);
        // }

        $query->join('wma_bill ', 'wma_bill.PayCntrNum = bill_payment.PayCtrNum');
        // $query->groupBy('PayCntrNum');
        // $query->join('bill_items', 'bill_payment.BillId = bill_items.BillId');
        $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter');

        return $query->get()->getResult();
    }



    public function getReportData2($params)
    {
        $others = [];
        return $this->billTable
            ->select('
            BillId,
            Activity,
            Task,
            PaymentStatus,
            PyrName,
            PyrCellNum,
            BillAmt ,
            PaidAmount,
            PayCntrNum,
            BillGenBy,
            BillExprDt,
            CollectionCenter,
            CenterName,
            Ccy,
            CreatedAt
            
            ')
            ->where($params)
            // ->join('users', 'users.unique_id = wma_bill.UserId')
            ->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter', 'LEFT')
            ->get()
            ->getResult();
    }


    public function getInstrumentsCount($params)
    {
        return  $this->billItems->select('GfsCode,center,BillItemAmt,ItemQuantity,Status,CreatedAt')
            //->where(['GfsCode' => $gfsCode,])
            //    ->limit(320)
            ->where('deletedAt', NULL)
            ->where($params)
            ->get()
            ->getResult();
    }

    public function getInstrumentsCount2($params, $gfsCode)
    {
        $builder = $this->billItems->select('GfsCode,BillItemAmt,bill_items.BillId,ItemQuantity,Status,bill_items.CreatedAt,CollectionCenter,wma_bill.CollectionCenter,PaymentStatus')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->where(['GfsCode' => $gfsCode,])
            //->limit(12)
            ->where(['deletedAt' => null])
            ->where($params)
            ->get()
            ->getResult();


        if (!empty($builder)) {
            $quantity = (new ArrayLibrary($builder))->map(fn($item) => $item->ItemQuantity == 0 ? 1 : $item->ItemQuantity)->reduce(fn($x, $y) => $x + $y)->get();
            $amount = (new ArrayLibrary($builder))->map(fn($item) => (int)$item->BillItemAmt)->reduce(fn($x, $y) => $x + $y)->get();
        } else {
            $quantity = 0;
            $amount = 0;
        }

        return (object) [
            'quantity' => $quantity,
            'amount' => $amount,
        ];
    }




    public function getInstruments($params)
    {
        return $this->billItems->select()
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->where($params)
            ->get()
            ->getResult();
    }

    public function nextVerification($currentDate, $nextVerifiacation)
    {

        $excluded = [
            setting('Gfs.vtv'),
            setting('Gfs.sbl'),
            setting('Gfs.fine'),
            setting('Gfs.waterMeter'),
            setting('Gfs.prePackages'),
            setting('Gfs.metrological'),
            setting('Gfs.miscellaneousReceipts'),
        ];

        return $this->billItems
            ->select('
        bill_items.id,
        BillItemRef as ref,
        PyrName as  name,
        PyrCellNum as phoneNumber,
        "bill_items" as table,
        GfsCode as activity,
        NextVerification as nextVerification
       ')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->where('notified', 0)
            ->where('nextVerification >=', $currentDate)
            ->where('nextVerification <=', $nextVerifiacation)
            ->whereNotIn('GfsCode', $excluded)
            ->get()
            ->getResult();
    }
}
