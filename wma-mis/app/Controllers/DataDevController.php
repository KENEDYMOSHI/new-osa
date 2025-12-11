<?php

namespace App\Controllers;

use App\Libraries\EsbLibrary;
use App\Libraries\TRALibrary;
use App\Models\CustomerModel;
use App\Libraries\ArrayLibrary;
use function PHPSTORM_META\type;
use PHPUnit\TextUI\Output\Printer;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DataDevController extends BaseController
{
    protected $token;

    public function __construct()
    {
        $this->token = csrf_hash();
    }
    public function index()
    {
        //
    }

    public function updateWmaBillDates4()
    {
        $batchSize = 5;   // Number of records to update per batch
        $maxRecords = 20; // Maximum number of records to process
        $processedRecords = 0; // Counter for processed records
        $db = \Config\Database::connect();

        $startTime = microtime(true);


        do {
            // Step 1: Fetch 100 unprocessed records from bill_items with BillId and minimum CreatedAt

            $params = [
                // 'updated' => NULL,
                'MONTH(CreatedAt)' => '11',
                ///'center' => '001',
            ];
            $subQuery = $db->table('bill_payment')
                ->select('PayCtrNum as PayCntrNum , CreatedAt')
                ->where($params)
                // ->groupBy('PayCtrNum')
                ->limit($batchSize)
                ->get()
                ->getResult();

            // Prepare data for updating wma_bill and marking bill_items as processed
            $updateData = [];
            $billItemIds = [];

            foreach ($subQuery as $row) {
                $updateData[] = [
                    'PayCntrNum' => $row->PayCntrNum,
                    'CreatedAt' => $row->CreatedAt
                ];
                $billItemIds[] = $row->PayCntrNum;
            }

            // Step 2: Perform batch update on wma_bill table
            if (!empty($updateData)) {
                $db->table('wma_bill')->updateBatch($updateData, 'PayCntrNum');
            }

            // Step 3: Update is_processed flag in bill_items for processed BillIds
            // if (!empty($billItemIds)) {
            //     $db->table('bill_items')
            //         ->whereIn('BillId', $billItemIds)
            //         ->set(['updated' => 1])
            //         ->update();
            // }

            // Update the processed records counter
            $processedRecords += count($updateData);
        } while (count($subQuery) === $batchSize && $processedRecords < $maxRecords);

        $endTime = microtime(true);
        $durationInSeconds = $endTime - $startTime;
        $minutes = floor($durationInSeconds / 60);
        $seconds = $durationInSeconds % 60;

        echo "Update complete! Total records processed: $processedRecords. Time taken: {$minutes} minutes and " . round($seconds, 2) . " seconds.";
    }




    public function updateCn()
    {
        $batchSize = 5;   // Number of records to update per batch
        $maxRecords = 20; // Maximum number of records to process
        $processedRecords = 0; // Counter for processed records
        $db = \Config\Database::connect();

        $startTime = microtime(true);



        // Step 1: Fetch 100 unprocessed records from bill_items with BillId and minimum CreatedAt

        $params = [
            // 'updated' => NULL,
            'DATE(CreatedAt)' => '2024-11-15',

        ];
        $subQuery = $db->table('wma_bill')
            ->select('PayCntrNum,RequestId')
            // ->where($params)
            ->where('PayCntrNum', NULL)
            ->limit(25)
            ->get()
            ->getResult();

        // Prepare data for updating wma_bill and marking bill_items as processed
        $updateData = [];
        $billItemIds = [];

        foreach ($subQuery as $row) {
            $controlNumber = $db->table('control_number')->select()
                ->where('RequestId', $row->RequestId)

                ->get()->getRow();
            $updateData[] = [
                'PayCntrNum' => $controlNumber->controlNumber,
                'RequestId' => $row->RequestId
            ];

            // $db->table('wma_bill')->set('PayCntrNum', $controlNumber->controlNumber)->where('RequestId', $row->RequestId)->update();
            $db->table('bill_items')->set('controlNumber', $controlNumber->controlNumber)->where('RequestId', $row->RequestId)->update();


            //  $billItemIds[] = $row->PayCntrNum;

        }

        Printer($subQuery);

        // Step 2: Perform batch update on wma_bill table
        // if (!empty($updateData)) {
        //     $db->table('wma_bill')->updateBatch($updateData, 'RequestId');
        // }

        // Step 3: Update is_processed flag in bill_items for processed BillIds
        // if (!empty($billItemIds)) {
        //     $db->table('bill_items')
        //         ->whereIn('BillId', $billItemIds)
        //         ->set(['updated' => 1])
        //         ->update();
        // }

        // Update the processed records counter
        $processedRecords += count($updateData);


        $endTime = microtime(true);
        $durationInSeconds = $endTime - $startTime;
        $minutes = floor($durationInSeconds / 60);
        $seconds = $durationInSeconds % 60;

        echo "Update complete! Total records processed: $processedRecords. Time taken: {$minutes} minutes and " . round($seconds, 2) . " seconds.";
    }



    public function updateWmaBillDates()
    {
        $batchSize = 5;   // Number of records to update per batch
        $maxRecords = 50; // Maximum number of records to process
        $processedRecords = 0; // Counter for processed records
        $db = \Config\Database::connect();

        // Record start time to monitor execution duration
        $startTime = microtime(true);

        // Set strict SQL modes for the session
        $db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

        // Use Query Builder to join tables and filter the records
        $builder = $db->table('bill_payment');
        $builder->join('wma_bill', 'wma_bill.PayCntrNum = bill_payment.PayCtrNum');
        $builder->groupBy('PayCtrNum');
        $builder->where('PaymentStatus', 'Pending');
        //  $builder->where('DATE(bill_payment.CreatedAt)', '2024-11-14');

        // Select only the relevant columns from wma_bill
        $builder->select('PayRefId, wma_bill.PyrName, RequestId, PayCntrNum, PaymentStatus, PaidAmt, bill_payment.CreatedAt');
        //  $builder->limit(50); // Limit to 10 records (you can adjust this if needed)

        // Get the result set
        $query = $builder->get();
        $results = $query->getResult();




        //  return $this->response->setJSON([
        //    'status' => 0,
        //    'data' => $results,
        //    'count' => count($results)

        //  ]);

        //  exit;

        // Check if there are any records to process
        if (count($results) > 0) {
            foreach ($results as $record) {
                //Perform the update for each record
                $db->table('wma_bill')
                    ->where('PayCntrNum', $record->PayCntrNum) // Use PayCntrNum as the identifier
                    ->update(['PaymentStatus' => 'Paid', 'PaidAmount' => $record->PaidAmt]);     // Update PaymentStatus to 'Paid'

                // Increment processed record counter
                $processedRecords++;

                // Break the loop if the maximum number of records is reached
                if ($processedRecords >= $maxRecords) {
                    break;
                }
            }
        }
        $cn = array_column($results, 'PayCntrNum');
        $quotedCn = implode(',', array_map(function ($num) {
            return "'" . $num . "'";
        }, $cn));
        // Measure end time
        $endTime = microtime(true);

        // Calculate the time taken
        $executionTime = $endTime - $startTime;

        // Return the processed count, the updated data, and the execution time
        return $this->response->setJSON([
            'allCn' => $quotedCn,
            'processed_records' => $processedRecords,
            //'data' => $results,
            'execution_time_seconds' => $executionTime
        ]);
    }


    public function updateItemsCn()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('wma_bill')->select('PayCntrNum, RequestId');

        // Get 15 records where PayCntrNum is NULL
        $builder->where('PayCntrNum', NULL);
        $result = $builder->get()->getResult();

        if (empty($result)) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'No records found to process.',
                'processedRecords' => 0
            ]);
        }

        // Collect all RequestIds to fetch control numbers in one query
        $requestIds = array_column($result, 'RequestId');

        // Fetch all matching control numbers in one query
        $controlNumbers = $db->table('control_number')
            ->select('controlNumber, requestId')
            ->whereIn('requestId', $requestIds)
            ->get()->getResult();

        // Index control numbers by requestId for easy lookup
        $controlMap = [];
        foreach ($controlNumbers as $control) {
            $controlMap[$control->requestId] = $control->controlNumber;
        }

        $processedRecords = 0;
        $db->transStart(); // Start transaction for safe updates

        foreach ($result as $item) {
            // Check if we have a control number for this RequestId
            if (isset($controlMap[$item->RequestId])) {
                $db->table('wma_bill')
                    ->where('RequestId', $item->RequestId)
                    ->update(['PayCntrNum' => $controlMap[$item->RequestId]]);
                $processedRecords++;
            }
        }

        $db->transComplete(); // Complete transaction

        if ($db->transStatus() === FALSE) {
            // If transaction failed
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'Failed to update records.',
                'processedRecords' => $processedRecords
            ]);
        }

        return $this->response->setJSON([
            'status' => 1,
            'message' => 'Records successfully processed.',
            'processedRecords' => $processedRecords
        ]);
    }


    public function checkBill()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('wma_bill');
        $builder->select();
        $builder->where('PaymentStatus', 'Paid');
        $builder->where('isTrBill', 'Yes');
        $builder->where('DATE(CreatedAt)', '2024-11-15');
        //$builder->limit(7);
        $result = $builder->get()->getResult();

        $notInTr = [];
        $inTr = [];
        $cn = [];
        $billCn = [];
        $reqId = [];
        $trNoCn = [];
        foreach ($result as $bill) {
            $tr = $db->table('tr_bill')
                ->select()
                ->where('RequestId', $bill->RequestId)
                ->get()
                ->getRow();

            $billCn[] = $bill->PayCntrNum;

            $reqId[] = "$bill->RequestId";
            if (!$tr) {
                $cn[] = "$bill->PayCntrNum";
                // Create new object with all properties
                $trBill = (object)[
                    'updatedAt' => $bill->updatedAt,
                    'BillId' => $bill->BillId,
                    'GrpBillId' => $bill->GrpBillId,
                    'BillTyp' => $bill->BillTyp,
                    'RequestId' => $bill->RequestId,
                    'CollCentCode' => $bill->CollCentCode,
                    'CustId' => $bill->CustId,
                    'CustTin' => $bill->CustTin,
                    'CustIdTyp' => $bill->CustIdTyp,
                    'isTrBill' => $bill->isTrBill,
                    'Activity' => $bill->Activity,
                    'Task' => $bill->Task,
                    'BillRef' => $bill->BillRef,
                    'TrxStsCode' => $bill->TrxStsCode,
                    'PayCntrNum' => $bill->PayCntrNum,
                    'PyrName' => $bill->PyrName,
                    'PhysicalLocation' => $bill->PhysicalLocation,
                    // Calculate 15% for BillAmt and PaidAmount
                    'BillAmt' => $bill->BillAmt * 0.15,
                    'TotalBillAmount' => $bill->TotalBillAmount,
                    'PaidAmount' => $bill->TotalBillAmount     * 0.15,
                    'NetAmount' => $bill->TotalBillAmount     - ($bill->TotalBillAmount     * 0.15),
                    'PaymentStatus' => $bill->PaymentStatus,
                    'IsCancelled' => $bill->IsCancelled,
                    'CollectionCenter' => $bill->CollectionCenter,
                    'BillGenDt' => $bill->BillGenDt,
                    'BillExprDt' => $bill->BillExprDt,
                    'extendedExpiryDate' => $bill->extendedExpiryDate,
                    'BillAmtWords' => $bill->BillAmtWords,
                    'Ccy' => $bill->Ccy,
                    'BillPayOpt' => $bill->BillPayOpt,
                    'RemFlag' => $bill->RemFlag,
                    'MiscAmt' => $bill->MiscAmt,
                    'PyrId' => $bill->PyrId,
                    'BillDesc' => $bill->BillDesc,
                    'BillGenBy' => $bill->BillGenBy,
                    'BillApprBy' => $bill->BillApprBy,
                    'PyrCellNum' => $bill->PyrCellNum,
                    'billControlNumber' => $bill->billControlNumber,
                    'PyrEmail' => $bill->PyrEmail,
                    'BillEqvAmt' => $bill->BillEqvAmt * 0.15,
                    'method' => $bill->method,
                    'SwiftCode' => $bill->SwiftCode,
                    'UserId' => $bill->UserId,
                    'latitude' => $bill->latitude,
                    'longitude' => $bill->longitude,
                    'TerminalId' => $bill->TerminalId,
                    'deviceId' => $bill->BillAmt,
                    'CreatedAt' => $bill->CreatedAt
                ];
                $notInTr[] = $trBill;
            } else {
                if ($tr->PayCntrNum == '') {
                    $trNoCn[] = $tr;
                    $tr->PayCntrNum = $bill->PayCntrNum;
                }
            }
        }

        $trUpdate = array_map(function ($tr) {
            $amount = $tr->BillAmt;
            // $amount15Percent = $tr->BillAmt * 0.15;
            return [
                'RequestId' => $tr->RequestId,
                'PayCntrNum' => $tr->PayCntrNum,
                'PaidAmount' => $tr->BillAmt,
                'NetAmount' => $tr->TotalBillAmount - $tr->BillAmt,
                'PaymentStatus' => 'Paid',
            ];
        }, $trNoCn);

        // if (!empty($notInTr)) {
        //     $db->table('tr_bill')->insertBatch($notInTr);
        // }

        if (!empty($trUpdate)) {
            $db->table('tr_bill')->updateBatch($trUpdate, 'RequestId');
        }

        return $this->response->setJSON([
            'status' => 0,
            'tr updt count' => count($trUpdate),
            'tr no cn count' => count($trNoCn),
            // 'bill' => $result,
            'NOT IN TR' => $notInTr,
            'AVAILABLE IN TR' => $inTr,
            'cn' => "SELECT * FROM tr_bill WHERE PayCntrNum IN (" . implode(', ', $cn) . ")",
            // 'bill' => "SELECT * FROM tr_bill WHERE PayCntrNum IN (" . implode(', ', $billCn) . ")",
            // 'req' => "SELECT * FROM tr_bill WHERE RequestId IN (" . implode(', ', $reqId) . ")",
            'tr No CN' => $trNoCn,
            'trUPDATE' => $trUpdate,
        ]);
    }



    public function controlNumber()
    {
        try {
            $requestBody = $this->request->getBody();
            $queueService = service('queue');
            $queueData = [
                'phoneNumber' => '0659851709',
                'message' => 'Control number received from API',
                'xmlPayload' => $requestBody,
            ];
            $push =   $queueService->push('controlnumber', 'processcontrolnumber', $queueData, 'high');
            if ($push) {
                $response = [
                    'status' => 1,
                    'msg' => 'Queue is working',
                    // 'data' => $requestBody,
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Control number Queue did not work',
                    //'data' => $requestBody,
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace()
            ];
        }
        return $this->response->setJSON($response);
    }


    public function updatePaymentStatus()
    {


        $db = \Config\Database::connect();


        $builder = $db->table('wma_bill');


        $builder->select('PayRefId,PayCtrNum,EntryCnt,PaidAmt,wma_bill.RequestId,wma_bill.GrpBillId,wma_bill.PyrName,PaymentStatus,CollectionCenter,wma_bill.CreatedAt as date');
        $builder->join('bill_payment', 'wma_bill.PayCntrNum = bill_payment.PayCtrNum', 'inner');
        $builder->where('wma_bill.PaymentStatus', 'Pending')->limit(20);

        $query = $builder->get();
        $result = $query->getResult();

        $wma = array_map(function ($item) {
            return [
                'RequestId' => $item->RequestId,
                'PaymentStatus' => 'Paid',
                'PaidAmount' => (int)$item->PaidAmt,
                'PaidAmount' => (int)$item->PaidAmt,
                'PayCntrNum' => $item->PayCtrNum,

            ];
        }, $result);

        $tr = array_map(function ($item) {
            $amount = $item->PaidAmt;
            $amount15 = $amount * 0.15;
            return [
                'RequestId' => $item->RequestId,
                'PaymentStatus' => 'Paid',
                'PaidAmount' => $amount15,
                'NetAmount' =>  $item->PaidAmt - $amount15,
                'PayCntrNum' => $item->PayCtrNum,

            ];
        }, $result);


        // Printer($wma);
        // exit;


        //update batch 
        if (!empty($wma)) {
            $update = $db->table('wma_bill')->updateBatch($wma, 'RequestId');

            if ($update) {
                return $this->response->setJSON([
                    'status' => 1,
                    'Bill Updated :' => count($wma),
                    'data' => 'Updated',

                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'data' => 'ERR:Not Updated',

                ]);
            }
        }

        if (!empty($tr)) {
            // $db->table('tr_bill')->updateBatch($tr, 'RequestId');
        }
        // $db->table('wma_bill')->updateBatch($wma, 'RequestId');
        // $db->table('tr_bill')->updateBatch($tr, 'RequestId');



        // return $this->response->setJSON([
        //     'status' => 0,
        //     'count' => count($result),
        //     'bills' => $result,
        //     'wmaUpdate' =>  $wma,
        //     'TRUpdate' =>  $tr,

        // ]);

        //return $result;
    }


    public function setBillControlNumbers()
    {
        $db = \Config\Database::connect();
        $db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
        $bills = $db->table('wma_bill')->select('RequestId,PayCntrNum,billControlNumber,isTrBill,CreatedAt')->where([
            'isTrBill' => 'Yes',
            'billControlNumber' => null,
        ])->limit(300)->get()->getResult();


        $billCn = [];

        foreach ($bills as $bill) {
            $cn = $db->table('control_number')->select()->where('controlNumber', $bill->PayCntrNum)->groupBy('billControlNumber')->get()->getResult();
            if ($cn) {
                $billCn[] = $cn;
            }
        }

        // $arrayLib = new ArrayLibrary($billCn);

        // $filteredBillCn = $arrayLib->map(function ($items) {
        //     $wmaCn = $items[0]->billControlNumber;
        //     $trCn = $items[1]->billControlNumber;
        //     return [
        //         'controlNumber' => $items[0]->controlNumber,
        //         'billControlNumber' => $wmaCn . ',' . $trCn,

        //     ];
        // })->get();

        $arrayLib = new ArrayLibrary($billCn);

        $filteredBillCn = $arrayLib->map(function ($items) {
            $wmaCn = $items[0]->billControlNumber ?? '';
            $trCn = $items[1]->billControlNumber ?? '';

            // Ensure wmaCn starts with 9941 and trCn starts with 9951
            if (strpos($wmaCn, '9941') === 0 && strpos($trCn, '9951') === 0) {
                return [
                    'RequestId' => $items[0]->requestId,
                    'billControlNumber' => $wmaCn . ',' . $trCn,
                ];
            }

            // You can handle cases where the conditions are not met (optional)
            return null; // or you could return a default value, or skip this item
        })->get(); // filter() removes null values, only keeps valid items


        $clean = array_filter($filteredBillCn, function ($item) {
            return $item !== null;
        });


        if (!empty($clean)) {
            $db->table('wma_bill')->updateBatch($clean, 'RequestId');
        }

        //  $db->table('wma_bill')->updateBatch($clean, 'RequestId');



        return $this->response->setJSON([
            'count' => count($bills),
            'status' => 0,
            'data' => $bills,
            //'billCn' => $billCn,
            'filtered' => $clean,

        ]);
    }


    // public function updateMissingBillControlNumbersss()
    // {
    //     $db = \Config\Database::connect();

    //     $noCnBill =  $db->table('tr_bill')->select()->where(['PayCntrNum' => NULL, 'DATE(CreatedAt)' => '2024-11-20'])->get()->getResult();


    //     foreach ($noCnBill as $bill) {
    //         $controlNumber = $db->table('control_number')->select('controlNumber')->where('requestId', $bill->RequestId)->get()->getRow();

    //         if ($controlNumber) {
    //             $db->table('wma_bill')->where('RequestId', $bill->RequestId)->set(['PayCntrNum' => $controlNumber->controlNumber])->update();
    //             $db->table('bill_items')->where('RequestId', $bill->RequestId)->set(['controlNumber' => $controlNumber->controlNumber])->update();
    //             //$db->table('tr_bill')->where('RequestId', $bill->RequestId)->set(['PayCntrNum' => $controlNumber->controlNumber])->update();
    //         }
    //     }

    //     return $this->response->setJSON([
    //         'status' => 0,
    //         'data' => $noCnBill,

    //     ]);
    // }

    public function updateMissingControlNumber()
    {
        $db = db_connect();

        $billDate = date('Y-m-d');

        $noCnBill =  $db->table('wma_bill')->select()->where(['PayCntrNum' => NULL, 'DATE(CreatedAt)' => $billDate])->get()->getResult();

        $updated = [];

        if (!empty($noCnBill)) {
            foreach ($noCnBill as $bill) {
                $controlNumber = $db->table('gepg')->select('PayCntrNum')->where('BillId', $bill->BillId)->get()->getRow();
                array_push($updated, $controlNumber->PayCntrNum);

                if ($controlNumber) {
                    $db->table('wma_bill')->where('BillId', $bill->BillId)->set(['PayCntrNum' => $controlNumber->PayCntrNum])->update();
                    $db->table('bill_items')->where('BillId', $bill->BillId)->set(['controlNumber' => $controlNumber->PayCntrNum])->update();
                }
            }

            return $this->response->setJSON([

                'updated control numbers' => $updated,

            ]);
        }
    }



    public function fixTrBadAmounts()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tr_bill');

        $trBill = $builder->select('RequestId,BillAmt,PaidAmount,NetAmount,TotalBillAmount,PaymentStatus')->where('PaymentStatus', 'Paid')->limit(0)->get()->getResult();

        $wrong = array_filter($trBill, function ($item) {
            return $item->PaidAmount == $item->TotalBillAmount;
        });

        $trFix = array_map(function ($item) {
            return [
                'RequestId' => $item->RequestId,
                'PaidAmount' => $item->BillAmt,
                'NetAmount' => $item->TotalBillAmount - $item->BillAmt,

            ];
        }, $wrong);


        // $db->table('tr_bill')->updateBatch($trFix, 'RequestId');



        return $this->response->setJSON([
            'status' => 0,
            'count' => count($wrong),
            'wrong' => $wrong,
            'trBillFix' => $trFix,

        ]);
    }






    public function billRenew(array $requestIds)
    {

        //loop the id use foreach


        //send a request to another function renewRequest($requestId)
        // but this request takes a while to complete until the response is returned with status 7101

        // so i need i want the loop to complete one cycle then go to another request id




    }

    public function fixBillItems()
    {
        $db = \Config\Database::connect();
        $billWithControlNumberNull = $db->table('bill_items')->select('BillId')
            // ->where('controlNumber', NULL)
            ->where('center', NULL)
            ->limit(200)->get()->getResult();

        $updatedCount = 0; // Counter for updated records

        foreach ($billWithControlNumberNull as $billItem) {
            $bill = $db->table('wma_bill')->select('PayCntrNum, CollectionCenter')
                ->where('BillId', $billItem->BillId)
                ->get()
                ->getRow();

            if ($bill) {
                $updateResult = $db->table('bill_items')
                    ->where('BillId', $billItem->BillId)
                    ->where('center', NULL)
                    ->set(
                        [
                            // 'controlNumber' => $bill->PayCntrNum,
                            'center' => $bill->CollectionCenter
                        ]
                    )
                    ->update();

                // Increment the counter if the update was successful
                if ($updateResult) {
                    $updatedCount++;
                }
            }
        }

        // Echo the number of updated records
        return $this->response->setJSON([
            'status' => 0,
            // 'data' => $billWithControlNumberNull,
            'msg' => 'Updated ' . $updatedCount . ' records.',

        ]);
    }


    protected function toObject($data)
    {
        return json_decode(json_encode($data));
    }


    public function verifyTin()
    {
        try {
            $esbLibrary = new EsbLibrary();
            $tinNo = '100100223';
            $tinVerificationBody = [
                'requestOrganization' => 'wma',
                'requestData' => str_replace('-', '', $tinNo)
            ];
            $tin = $this->getTinData($tinNo);
            if ($tin) {
                return $this->response->setJSON([
                    'res' => 'db',
                    'status' => 1,
                    'tinData' => $tin,
                    'token' => $this->token
                ])->setStatusCode(200);
            }
            $request = json_decode(json_encode($esbLibrary->requestData('BS52MFHO', $tinVerificationBody, 'json', null)));
            $data = $request->esbBody;
            $statusCode = $data->status;

            if ($statusCode == 200) {

                $responseKey = $data->responseKey;
                $responseData = $data->responseData;

                $traLibrary = new TRALibrary();

                $traData = $traLibrary->extractTRAData($responseKey, $responseData);

                $tinData = [
                    'tin' => $traData->TaxpayerId,
                    'type' => $traData->BusinessType,
                    'category' => $traData->TinCategory,
                    'companyName' => $traData->CompanyName,
                    'firstName' => $traData->FirstName,
                    'middleName' => $traData->MiddleName,
                    'lastName' => $traData->LastName,
                    'region' => $traData->Region,
                    'mobile' => $traData->Mobile,
                ];
                $this->storeTinData($tinData);
                $response = [
                    'status' => 1,
                    'msg' => 'Success',
                    'tinData' =>  $tinData,
                    'token' => $this->token
                ];
            } else {
                $message = $data->detail;
                $response = [
                    'status' => 0,
                    'msg' => $message,
                    'tinData' => [],
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $statusCode = 500;
            $response = [
                'status' => 0,
                'tinData' => [],
                'msg' => $th->getMessage(),
                'line' => $th->getLine(),
                'token' => $this->token,
            ];
        }
        return $this->response->setJSON($response)->setStatusCode($statusCode);
    }

    public function storeTinData($tinData)
    {
        $customerModel = new CustomerModel();

        $customerModel->storeTinData($tinData);
    }

    public function getTinData($tin)
    {
        $customerModel = new CustomerModel();

        return  $customerModel->getTinData($tin);
    }



    public function cars()
    {
        $userId = '100100223';
        $brand = ['Corolla', 'Camry', 'Civic'];

        $count  = count($brand);

        $carData = [
            'brand' => $brand,
            'makeYear' => ['2020', '2021', '2022'],
            'userId' => fillArray(3, $userId),
            'city' => ['Dar es Salaam', 'Dodoma', 'Mwanza'],
        ];


        $arr = multiDimensionArray($carData);


        printer($carData);

        printer($arr);
        // return view('cars');
    }



    public function addCar()
    {
        try {
            $name = $this->request->getVar('name');
            $brand = $this->request->getVar('brand');
            $makeYear = $this->request->getVar('makeYear');
            $fuelType = $this->request->getVar('fuelType');
            $city = $this->request->getVar('city');


            $rules = [
                'name' => 'required',
                'brand' => 'required',
                'makeYear' => 'required',
                'fuelType' => 'required',
            ];

            sleep(2);
            if (!$this->validate($rules)) {
                $response = [
                    'status' => 0,
                    'errors' => $this->validator->getErrors(),
                    'token' => $this->token
                ];
                return $this->response->setJSON($response);
            }


            $vehicleData = [
                'name' => $name,
                'brand' => $brand,
                'makeYear' => $makeYear,
                'fuelType' => $fuelType,
                'city' => $city,
            ];



            $response = [
                'status' => 1,
                'msg' => 'Data added successfully',
                'data' => $this->renderData($vehicleData),
                'token' => $this->token
            ];
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }

    public function renderData($data)
    {
        $html = <<<HTML
          <div class="card">
                <div class="card-header">
                    <h4>Data</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Make Year</th>
                                <th>Fuel Type</th>
                                <th>City</th>
                            </tr>
                        </thead>
                        <tbody>           
    HTML;
        return $html;
    }




    public function test()
    {
        $userId = '100100223';
        $brand = ['Corolla', 'Camry', 'Civic'];

        $count  = count($brand);

        $carData = [
            'brand' => $brand,
            'makeYear' => ['2020', '2021', '2022'],
            'userId' => fillArray(3, $userId),
            'city' => ['Dar es Salaam', 'Dodoma', 'Mwanza'],
        ];


        $arr = multiDimensionArray($carData);


        printer($carData);

        printer($arr);
    }
}
