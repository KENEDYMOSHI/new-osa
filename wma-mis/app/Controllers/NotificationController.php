<?php

namespace App\Controllers;

use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\LorriesModel;
use App\Libraries\SmsLibrary;
use App\Models\PrePackageModel;
use App\Controllers\BaseController;

class NotificationController extends BaseController
{
    
    public function verificationAlert()
    {
        $db = \Config\Database::connect();

        $currentDate = date('Y-m-d');

        $twoWeeksLater = date('Y-m-d', strtotime($currentDate . ' +2 week'));



    
        $sbl = (new LorriesModel())->nextVerification($currentDate, $twoWeeksLater);
        $vtv = (new VtcModel())->nextVerification($currentDate, $twoWeeksLater);
        $prepackage = (new PrePackageModel())->nextVerification($currentDate, $twoWeeksLater);
        $others = (new BillModel())->nextVerification($currentDate, $twoWeeksLater);

        $data = array_merge($sbl,$vtv,$prepackage,$others);

      


        $sms = new SmsLibrary();
        if (!empty($data)) {
            foreach ($data as $result) {
                $textParams = (object)[
                    'name' => $result->name,
                    'activity' => $result->activity,
                    'center' => 'Wakala wa vipimo',
                    'nextVerification' =>  $result->nextVerification,
                    'item' =>  $result->item ?? activityName($result->activity),


                ];
                // echo  $result->item  ?? activityName($result->activity).'<br>';
                $sms->sendSms(recipient: $result->phoneNumber, message: verificationReminderText($textParams));
                $db->table($result->table)->set(['notified' => 1])->where(['id' => $result->id])->update();
            }
        }
        
    }
     
     




    public function debtAlert()
    {
        $db = \Config\Database::connect();
        // Assuming you have loaded the database library in your controller/model
        $builder = $db->table('gepg');

        // Get the current date
        $currentDate = date('Y-m-d');

        // Calculate the date 2 days from today
        $twoDaysAhead = date('Y-m-d', strtotime($currentDate . ' + 2 days'));

        // Calculate the date 1 day from today
        $oneDayAhead = date('Y-m-d', strtotime($currentDate . ' + 1 days'));

        $params = [
            'BillExprDt >=' => $oneDayAhead,
            'BillExprDt <=' => $twoDaysAhead,

        ];

        $bills = (new BillModel())->getExpiredBills($params);

        $newData = array_reduce($bills, function ($result, $item) {
            $billId = $item->BillId;

            $amount = $item->status == 'Partial' ? $item->amount - $item->paidAmount : $item->amount;

            // If the key doesn't exist in $result, create it
            if (!array_key_exists($billId, $result)) {
                $result[$billId] = (object)[
                    'phoneNumber' => $item->phoneNumber,
                    'payer' => $item->payer,
                    'region' => wmaCenter($item->region)->centerName,
                    'controlNumber' => $item->controlNumber,
                    'amount' => $amount,
                    'expiryDate' => $item->expiryDate,
                    'items' => $item->ItemName, // Initialize 'items' with the first ItemName
                ];
            } else {
                // Add the ItemName to the 'items' array with a comma
                $result[$billId]->items .= ',' . $item->ItemName;
            }

            return $result;
        }, []);


        // Convert the associative array back to indexed array
        $results = array_values($newData);

        Printer($results);
        exit;

        $sms = new SmsLibrary();
        foreach ($results as $result) {
            $textParams = (object)[
                'payer' => $result->payer,
                'center' => $result->region,
                'amount' =>  $result->amount,
                'items' =>  $result->items,
                'expiryDate' => date('d/m/Y', strtotime($result->expiryDate)),
                'controlNumber' => $result->controlNumber,

            ];
            $sms->sendSms(recipient: $result->phoneNumber, message: billTextTemplate($textParams));
        }

        // Get the result
        //  Printer($result);

        // You can now use $result to access the retrieved data

    }

}
