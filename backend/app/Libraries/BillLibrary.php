<?php

namespace App\Libraries;

class BillLibrary
{
    protected $user;
    protected $apiToken;
    protected $collectionCenter;

    public function __construct()
    {
        helper(['setting', 'form', 'array', 'text', 'inflector']);
        $this->user = auth()->user();
        $this->apiToken = session()->get('apiToken');
        
        if ($this->user) {
             $this->collectionCenter =  getCollectionCenter($this->user->region);
        }
    }

    public function setUser($user) {
        $this->user = $user;
        // Assuming getCollectionCenter helper is available or we need to handle it
        if (function_exists('getCollectionCenter') && isset($user->region)) {
            $this->collectionCenter = getCollectionCenter($user->region);
        } else {
            $this->collectionCenter = '001'; // Default or handle error
        }
    }




    public function generateBill($billId, $items,$billType)
    {


      //bill id is random sring


        $billItems = array_map(fn($item) => [
            "BillItemAmt" =>  $item->itemAmount, // billItemamt inatoka license fee 
            "Capacity" => 1, // leave as it is
            "GfsCode" => "140202", // leave as it is
            "ItemName" => $item->itemName, // name of license
            "ItemUnit" => "License",  // number of license 
            "Status" => "Pass", //leave as it is
            "StickerNumber" => "", //leave as it is
            "Task" => "Others", //leave as it is
            "SingleItemAmount" =>  $item->itemAmount, //amount of single license
            "ItemQuantity" => 1 // leave as it is
        ], $items);

        $billAmount =   array_reduce($items, fn($x, $y) => $x + $y->itemAmount);

        // Concatenate item names for BillDesc
        $itemNames = array_map(fn($item) => $item->itemName, $items);
        
        $billDescription = implode(', ', $itemNames);

        // Fetch Practitioner Personal Info
        $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
        // Assuming user has uuid, if not we might need to fetch it or use id if model supports it. 
        // But AuthController sets uuid. Let's check if $this->user has uuid.
        // If $this->user is from auth()->user(), it might be the Shield User entity.
        // We might need to fetch the UUID from the users table if it's not on the entity.
        
        $db = \Config\Database::connect();
        $userRecord = $db->table('users')->where('id', $this->user->id)->get()->getRow();
        $uuid = $userRecord->uuid ?? null;
        
        $personalInfo = null;
        if ($uuid) {
            $personalInfo = $personalInfoModel->where('user_uuid', $uuid)->first();
        }

        $payerName = $this->user->username; // Default fallback
        $payerPhone = '0700000000'; // Default fallback
        
        if ($personalInfo) {
            $payerName = $personalInfo->first_name . ' ' . $personalInfo->last_name;
            $payerPhone = $personalInfo->phone;
        }


        $payload = [
            "PyrName" => $payerName, 
            "BillAmt" =>  $billAmount, //total amount of lisence 
            "BillDesc" => $billDescription, //name of license
            "BillExprDt" => date('Y-m-d', strtotime('+180 days')), 
            "BillPayOpt" => 3, // leave as it is
            "PyrCellNum" => $payerPhone, 
            "deviceId" => "P200W305511", // leave as it is
            "latitude" => '', //leave as it is
            "longitude" => '',//leave as it is
            "BillItems" => $billItems, 
            'CollectionCenterCode' => '001',
            'collectionCenter' => '001',
            'billId' => $billId
        ];

        log_message('info', 'PAYLOAD' . json_encode($payload));

        $client = \Config\Services::curlrequest();

        //IMPORTANT : if the endpoint is unreachable when sending the request, then simulate response and add a the controll number starting with 1890 then follows 8 random digits toral of 12 digits

        $billData = null;

        try {
            // Send the POST request with the JSON payload
            $request = $client->post('https://training.wma.go.tz/service/serviceBillRequest', [
                'headers' => [
                    'Content-Type' => 'application/json', // Set content type to JSON
                ],
                'verify' => false, // Disable SSL verification for development/training
                'body' => json_encode($payload), // Pass the payload as JSON string
                'timeout' => 10, // Add timeout to fail faster if unreachable
                'connect_timeout' => 10
            ]);

            $response = json_decode($request->getBody());
            $billData = $response->data;
            log_message('error', 'Raw API Response: ' . json_encode($billData));

        } catch (\Exception $e) {
            // Fallback: Simulate response if endpoint is unreachable
            log_message('error', 'Bill API Unreachable/Failed: ' . $e->getMessage() . '. Generating offline bill.');

            $mockControlNumber = '1890' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            
            $billData = (object) [
                'trxStsCode' => '7101',
                'msg' => 'Success (Simulated Offline)',
                'controlNumber' => $mockControlNumber,
                'amount' => $payload['BillAmt'],
                'expireDate' => $payload['BillExprDt']
            ];
        }

        // Process the bill data (whether from API or simulated)
        if (empty($billData) || (isset($billData->trxStsCode) && $billData->trxStsCode !== '7101')) {
            $msg = $billData->msg ?? 'Unknown error';
            return (object)[
                'status' => 0,
                'billData' => [],
                'message' =>  $msg,
                'trace' =>  $msg,
            ];
        }

        try {
            $controlNumber = $billData->controlNumber;

            $billInfo = (object)[
                'billId' => $billId,
                'billType' => $billType, // 1 for application fee, 2 for license fee
                'controlNumber' =>  $controlNumber,
                'amount' => (int) preg_replace('/\D/', '', $billData->amount),
                'billExpiryDate' => $billData->expireDate,
                'collectionCenter' => '001',//region code
                'applicantId' => $this->user->id,
                'paymentStatus' => 'Pending',
                'items' => json_encode(array_map(fn($item) => ['itemName' => $item->itemName, 'itemAmount' => $item->itemAmount], $items)),// you can save the items in saparate table
            ];

            // Save bill info to osabill table
            $osabillModel = new \App\Models\OsabillModel();
            
            // Determine fee_type text
            $feeType = ($billType == 1) ? 'Application Fee' : 'License Fee';
            
            $osabillData = [
                'id' => strtoupper(md5(uniqid(rand(), true))),
                'bill_id' => $billId,
                'control_number' => $controlNumber,
                'amount' => (int) preg_replace('/\D/', '', $billData->amount),
                'bill_type' => $billType,
                'fee_type' => $feeType,
                'payer_name' => $payerName,
                'payer_phone' => $payerPhone, 
                'bill_description' => $billDescription,
                'bill_expiry_date' => $billData->expireDate,
                'collection_center' => '001',
                'user_id' => $this->user->id,
                'payment_status' => 'Pending',
                'items' => json_encode(array_map(fn($item) => ['itemName' => $item->itemName, 'itemAmount' => $item->itemAmount], $items)),
            ];

            //log osa bill data
            log_message('info', 'OSABILL DATA' . json_encode($osabillData));
            $osabillModel->insert($osabillData);

            // here update license table and add control number

            // Handle the response as needed
            return (object)[
                'status' => 1,
                'billData' => $billData,
            ];

        } catch (\Exception $e) {
             return (object)[
                'status' => 0,
                'billData' => [],
                'message' =>  $e->getMessage(),
                'trace' =>  $e->getTrace(),
            ];
        }
    }
}
