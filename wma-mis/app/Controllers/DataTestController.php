<?php

namespace App\Controllers;



use Exception;
use App\Models\VtcModel;
use App\Models\BillModel;
use PHPUnit\Util\Printer;
use App\Models\AdminModel;


use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Libraries\SmsLibrary;
use App\Models\CustomerModel;
use App\Models\DashboardModel;
use App\Models\PrePackageModel;
use PHPMailer\PHPMailer\PHPMailer;
use App\Controllers\BaseController;

use Ifsnop\Mysqldump as IMysqldump;
use Intervention\Image\ImageManager;
use CodeIgniter\Database\SQLite3\Table;
use Intervention\Image\Drivers\Gd\Driver;

class DataTestController extends BaseController
{

    protected $admin;
    protected $billModel;
    protected $sms;

    public function __construct()
    {
        $this->admin = new AdminModel();
        $this->billModel = new BillModel();
        $this->sms = new SmsLibrary();
    }

    public function updateVtv()
    {
        $db = \Config\Database::connect();
        $qr = $db->query("SELECT bi.BillItemRef as id, vt.hash,vt.task,vt.visualInspection,vt.testing,vt.region,vt.gfCode,vt.registration_date,vt.next_calibration,vt.tin_number,vt.supervisor,vt.supervisor_phone,vt.driver_name,vt.driver_license,vt.hose_plate_number,vt.trailer_plate_number,vt.sticker_number,vt.include_sticks,vt.remark,vt.hasPenalty,vt.penaltyAmount,vt.status,vt.capacity,vt.skipChart,vt.repairDeadline,vt.latitude,vt.longitude,vt.unique_id,vt.data_id as original_id FROM `vehicle_tanks` vt JOIN `temp_data` td ON vt.id = td.itemId JOIN ( SELECT SUBSTRING_INDEX(BillItemRef, '_', 2) AS itemId, BillItemRef FROM `bill_items` WHERE GfsCode = '142101210003' ) bi ON vt.id = bi.itemId WHERE MONTH(vt.created_at) = '3' AND td.activity = '142101210003' ORDER BY vt.id DESC; ")->getResult();

        $vtv = $db->table('calibrated_tanks')->insertBatch($qr);


        if ($vtv) {
            echo 'done';
        } else {
            echo 'err';
        }
    }
    public function updateSbl()
    {
        $db = \Config\Database::connect();
        // $qr = $db->query("SELECT bi.BillItemRef AS id, vt.hash, vt.task, vt.visualInspection, vt.testing,vt.amount, vt.region, vt.gfCode, vt.registration_date, vt.next_calibration, vt.tin_number, vt.supervisor, vt.supervisor_phone, vt.driver_name, vt.driver_license, vt.plate_number, vt.sticker_number, vt.remark, vt.hasPenalty, vt.penaltyAmount, vt.status, vt.capacity, vt.repairDeadline, vt.latitude, vt.longitude, vt.unique_id, vt.data_id AS original_id FROM `lorries` vt JOIN `temp_data` td ON vt.id = td.itemId JOIN( SELECT BillItemRef AS itemId, BillItemRef FROM `bill_items` WHERE GfsCode = '142101210035' ) bi ON vt.id = bi.itemId WHERE MONTH(vt.created_at) = '3' AND td.activity = '142101210035' ORDER BY vt.id DESC; ")->getResult();

        $del = $db->query("DELETE  FROM `temp_data` WHERE activity = '142101210035' AND itemId IN (SELECT BillItemRef FROM bill_items WHERE GfsCode = '142101210035'); ");

        // Printer($qr);

        // exit;


        //    $sbl = $db->table('verified_lorries')->insertBatch($qr);


        //    if($sbl){
        //     echo 'sbl done';
        //    }else{
        //     echo 'err';
        //    }
        if ($del) {
            echo 'deleted';
        } else {
            echo 'err';
        }
    }



    public function settle()
    {
        $db = \Config\Database::connect();


        // $builder = $db->table('reconciliation');
        // $builder->select('reconciliation.*');
        // $builder->join('bill_payment', 'reconciliation.BillCtrNum = bill_payment.PayCtrNum', 'left');
        // $builder->where('bill_payment.PayRefId', '');

        // $query = $builder->get();

        // $result = $query->getResult();
        $db = \Config\Database::connect();
        $payments = $db->table('bill_payment')
            ->select('PayRefId as controlNumber')
            ->get()
            ->getResult();

        $reconciliation = $db->table('reconciliation')
            ->select('PayRefId as controlNumber')
            ->get()
            ->getResult();


        $unpaid = array_diff(array_map('json_encode', $reconciliation), array_map('json_encode', $payments));
        $unpaid = array_map('json_decode', $unpaid);

        // print_r($unpaid);
        $paidCn = array_map(fn($p) => $p->controlNumber, $payments);
        $reconCn = array_map(fn($p) => $p->controlNumber, $reconciliation);



        $diff = array_diff($reconCn, $paidCn);
        $match  = array_intersect($reconCn, $paidCn);


        $cn = array_map(fn($p) => $p->PayCtrNum, $payments);


        $paid = ['994191447899', '994191447833'];
        $recon = ['994191447899', '994191447833', '994191445453', '994197097833'];

        $paid = [
            (object)['controlNumber' => '9900033454'],
            (object)['controlNumber' => '9900033460'],
            (object)['controlNumber' => '9900033499'],
        ];
        $recon = [
            (object)['controlNumber' => '9900033454'],
            (object)['controlNumber' => '9900033460'],
            (object)['controlNumber' => '9900033499'],
            (object)['controlNumber' => '1999777011'],
            (object)['controlNumber' => '1999777053'],
            (object)['controlNumber' => '1999777027'],
        ];



        $builder = $db->table('reconciliation');
        $builder->whereNotIn('BillCtrNum', $cn);
        // $result = $builder->get()->getResult();


        printer($diff);
        echo count($paidCn) . ' payments <br>';
        echo count($reconCn) . ' recon <br>';
        echo count($diff) . ' un match <br>';
        echo count($match) . ' match <br>';

        exit;

        // Printer($result);
        // exit;

        // Now $result contains the transaction data in reconciliation table but not in payments table




        // Get reconciliation data
        $recon = $db->table('reconciliation')
            ->select('reconciliation.*,SpBillId as BillId, BillCtrNum as PayCtrNum, TrxDtTm as date')
            // ->limit(200)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray(); // Ensure results are in array format

        // Get payment data
        $payments = $db->table('bill_payment')
            ->select('BillId, PayCtrNum, TrxDtTm as date')
            // ->limit(200)
            ->orderBy('id', 'DESC')
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
                // 'PyrCellNum' =>  '255659851709',
                'PyrEmail' => $bill->PyrEmail,
                'PyrName' => $bill->PyrName,
                'PspReceiptNumber' => $payment['pspTrxId'],
                'PspName' => $payment['PspName'],
                'CtrAccNum' => $payment['CtrAccNum'],
            ];
            return $transaction;
        }, $unsettledPayments);


        Printer($paymentData);
        exit;



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





        // get collection center number from the bill using billId
        //  $center = $this->billModel->getCollectionCenter($billId)->CollectionCenter;
        $centerName = 'Wakala Wa Vipimo';


        if ($paymentOption == 2) {
            //get available amount and add the amount paid to it
            $availableAmount = $this->billModel->getPaidAmount($data['BillId'])->PaidAmount;
            $amount = $availableAmount +  $data['PaidAmt'];

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


        //    $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));
        // $this->billModel->savePayment($payment);
        if (empty($paymentExist)) {

            //save payment to the database from GEPG
            $this->billModel->savePayment($payment);

            //sending payment notification to customer
            $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));



            //update bill status and paid amount
            $this->billModel->updateBill($controlNumber, [
                'PaymentStatus' => $PaymentStatus,
                'PaidAmount' => $updatedAmount,
            ]);
        }




        //signing ack and send back to GePG

    }






































    public function sticker()
    {
        $title = date('d-m-Y H-i-s') . '-Backup';
        try {
            $dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=vipimo', 'root', '');
            $dump->start("public/database/$title.sql");
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
        // return view('printing');
    }




    public function veri()
    {
        $db = \Config\Database::connect();

        $currentDate = date('Y-m-d');

        $twoWeeksLater = date('Y-m-d', strtotime($currentDate . ' +2 week'));




        $sbl = (new LorriesModel())->nextVerification($currentDate, $twoWeeksLater);
        $vtv = (new VtcModel())->nextVerification($currentDate, $twoWeeksLater);
        $prepackage = (new PrePackageModel())->nextVerification($currentDate, $twoWeeksLater);
        $others = (new BillModel())->nextVerification($currentDate, $twoWeeksLater);

        $data = array_merge($sbl, $vtv, $prepackage, $others);




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
        $sms->sendSms(recipient: '255659851709', message: 'Notifications Sent');
    }




    public function log()
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



    public function arr()
    {
        $db = \Config\Database::connect();

        $data = [

            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'cardNumber' => '2345672201',
                'salary' => 60000,
                'companyId' => '23456'
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'cardNumber' => '3456789012',
                'salary' => 70000,
                'companyId' => '34567'
            ],
            // Add more data sets as needed...
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
                'cardNumber' => '4567890123',
                'salary' => 80000,
                'companyId' => '45678'
            ],
            [
                'name' => 'Eva Brown',
                'email' => 'eva.brown@example.com',
                'cardNumber' => '5678901234',
                'salary' => 90000,
                'companyId' => '56789'
            ],
            [
                'name' => 'Chris White',
                'email' => 'chris.white@example.com',
                'cardNumber' => '6789012345',
                'salary' => 100000,
                'companyId' => '67890'
            ],
            [
                'name' => 'Grace Miller',
                'email' => 'grace.miller@example.com',
                'cardNumber' => '7890123456',
                'salary' => 110000,
                'companyId' => '78901'
            ],
            [
                'name' => 'Daniel Lee',
                'email' => 'daniel.lee@example.com',
                'cardNumber' => '8901234567',
                'salary' => 120000,
                'companyId' => '89012'
            ],
            [
                'name' => 'Olivia Davis',
                'email' => 'olivia.davis@example.com',
                'cardNumber' => '9012345678',
                'salary' => 130000,
                'companyId' => '90123'
            ],
            [
                'name' => 'Michael Wilson',
                'email' => 'michael.wilson@example.com',
                'cardNumber' => '5012345678',
                'salary' => 140000,
                'companyId' => '10123'
            ],
            [
                'name' => 'bbbb cccc',
                'email' => 'salim@example.com',
                'cardNumber' => '9922245670852',
                'salary' => 140000,
                'companyId' => '20770000'
            ]

        ];


        $uniqueKeys = ['cardNumber', 'companyId'];

        $inserts = [];

        foreach ($data as $entry) {
            $whereClause = [];
            foreach ($uniqueKeys as $key) {
                $whereClause[$key] = $entry[$key];
            }

            // Check if the record already exists
            $existingRecord = $db->table('sales')->where($whereClause)->get()->getRow();

            if (!$existingRecord) {
                // Record doesn't exist, add it to inserts
                $inserts[] = $entry;
            }
        }

        // Batch insert new records
        if (!empty($inserts)) {
            $db->table('sales')->insertBatch($inserts);
            echo 'inserted ' . count($inserts) . 'records';
        } else {
            echo 'no records to insert';
        }
    }


    public function index()
    {
        $this->admin->getAllUsers();
        $role = 3;
        $userRegion = 'Ilala';
        $billModel = new BillModel();
        $customer = new CustomerModel();
        $year = 2021;
        $region = '';
        $month = '';
        $dateFrom = '';
        $dateTo = '';
        $quarter = 3;
        switch ($quarter) {
            case '1':
                $startDate = $year . '-07-01';
                $endDate = $year . '-09-30';
                break;
            case '2':
                $startDate = $year . '-10-01';
                $endDate = $year . '-12-30';
                break;
            case '3':
                $startDate = ($year + 1) . '-01-01';
                $endDate = ($year + 1) . '-03-30';
                break;
            case '4':
                $startDate = ($year + 1) . '-04-01';
                $endDate = ($year + 1) . '-06-30';
                break;

            default:
                $currentMonth = date('m');

                if ($currentMonth >= 7) {
                    $startDate = date('Y-07-01');
                    $endDate = date('Y-06-30', strtotime('+1 year'));
                } else {
                    $startDate = date('Y-07-01', strtotime('-1 year'));
                    $endDate = date('Y-06-30');
                }

                break;
        }


        $params = [
            'region' => $role == 1 || $role == 2 ? $userRegion : $region,
            'created_at>=' => $dateFrom ? $dateFrom : $startDate,
            'created_at<=' => $dateTo ? $dateTo : $endDate,
            'created_at<=' => $dateTo ? $dateTo : $endDate,
            'MONTH(created_at)' => $month,
            'YEAR(created_at)' => $month != '' ? $year : '',

        ];

        $arr = array_filter($params, fn($param) => $param !== '');

        $data = $customer->getData($arr);
        $x = count($data);
        echo "<h2>$x</h2>";
        printer($arr);
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        printer($data);

        // $params =  [$param1,$param2,$param3,$param4,$param5,$param6,$param7];
        // $opt = [
        //     'month' => '',
        //     'year' => '',
        //     'minDate' => '',
        //     'maxDate' => '',
        //     'activity' => '',
        //     'task' => '',
        //     'payment' => '',

        // ];
        // in opt array for month key check if params has values between 1 and 12 and assign a value to it else assign "**".
        // in year key check if params has valid year and assign to it else assign "**".
    }




    public function image()
    {


        $imageManager = new ImageManager(new Driver());
        $data = (object)[
            'stickerNumber' => str_shuffle('012345678ABCD'),
            'controlNumber' => '19960002541',
            'instrument' => 'Beam Scale',
            'verificationDate' => '15 Jan 2022',
            'dueDate' => '14 Jan 2023',
            'qrCode' => ['id' => 001],
            'stickerId' => randomString(),
            'activity' => 'scale',
        ];

        $qrCodeData = [
            'stickerId' => $data->stickerId,
            // 'stickerNumber' => $data->stickerNumber,
            // 'controlNumber' => $data->controlNumber,
        ];

        $fontRegular = 'assets/fonts/Roboto.ttf';
        $fontLight = 'assets/fonts/Roboto-Light.ttf';
        $x = 550;
        $y = 140;
        $fontSize = 50;
        $logoSize = 140;
        // Load the background image
        $background = $imageManager->read('assets/images/card1.jpg');
        // Create a new image instance
        $img = $imageManager->create(1800, 900, '#ffffff'); // Width, Height, Background Color
        //    $img = $imageManager->create(1000, 600, '#ffffff'); // Width, Height, Background Color

        // Insert the background image
        $img->place($background, 'top-left');

        // Load the logo image
        $logo = $imageManager->read(('assets/images/wma-logo.png')); // Replace with the path to your logo image

        // Resize the logo to fit on the top left corner
        $logo->resize($logoSize, $logoSize);


        // Insert the logo at the top left corner
        $img->place($logo, 'top-right', 20, 25);

        ///************************************************* */

        // Load the logo image
        $coatOfArm = $imageManager->read('assets/images/emblem.png'); // Replace with the path to your logo image

        // Resize the logo to fit on the top left corner
        $coatOfArm->resize($logoSize, $logoSize);

        // Insert the logo at the top left corner
        $img->place($coatOfArm, 'top-left', 560, 25);


        //Tittle
        $img->text('WEIGHTS AND MEASURES AGENCY', 800, 120, function ($font) use ($fontRegular, $fontSize) {
            $font->size(45);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });

        $img->text('Instrument: ' . activityName($data->activity), $x, $y * 2, function ($font) use ($fontLight, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontLight);
            $font->color('#333333');
        });

        $img->text('Sticker Number: ' . $data->stickerNumber, $x, $y * 3, function ($font) use ($fontLight, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontLight);
            $font->color('#333333');
        });
        $img->text('Verification Date: ' . dateFormatter($data->verificationDate), $x, $y * 4, function ($font) use ($fontLight, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontLight);
            $font->color('#333333');
        });
        $img->text('Reverification Before: ' . dateFormatter($data->dueDate), $x, $y * 5, function ($font) use ($fontLight, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontLight);
            $font->color('#333333');
        });



        $qrCode = QRCode($qrCodeData);

        // Load the QR code image and resize it
        $qrCodeImage = $imageManager->read($qrCode);
        $qrCodeImage->resize(300, 300);

        // Insert the QR code at the bottom right corner
        $img->place($qrCodeImage, 'bottom-right', 10, 10);

        $title = "$data->controlNumber-$data->activity-$data->stickerNumber-" . randomString() . '.jpg';
        $savePath = 'public/stickers/' . $title;
        // $savePath = 'stickers/' . $title;
        $img->toJpeg()->save($savePath);

        $imgPath = base_url($savePath);
        // Redirect to the preview page with the image path


        // Output the image to the browser
        //  header('Content-Type: image/jpeg');

        $imgPath;

        return view('alpine', [
            'img' => $imgPath
        ]);
    }



    public function sms()
    {
        echo (new SmsLibrary())->sendSms('0659851709', "SMS Test " . date('H:i:s'));

        $user = auth()->user();

        // $queryParams = [

        //     'DATE(wma_bill.CreatedAt)>=' => $user->inGroup('manager', 'officer') ? '' : financialYear()->startDate,
        //     'DATE(wma_bill.CreatedAt) <=' => $user->inGroup('manager', 'officer') ? '' : financialYear()->endDate,
        //     "MONTH(wma_bill.CreatedAt)" => $user->inGroup('manager', 'officer') ?  date('m') : '',
        //     'CollectionCenter' => $user->inGroup('officer', 'manager') ? $user->collection_center : '',
        //     // 'wma_bill.UserId' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
        //     'IsCancelled' => 'No',
        //     // 'PaymentStatus' => 'Paid',
        // ];



        // $params = array_filter($queryParams, fn($param) => $param !== '' || $param != null);
        // $param['PayCntrNum !='] = '';



        
        
        
        // // $data['vtv'] = (new DashboardModel)->vtv($params);
        // $data = (new DashboardModel)->sbl($params);
        // // $data['waterMeter'] = (new DashboardModel)->waterMeters($params);
        // // $data['prePackage'] = (new DashboardModel())->ppg($params);
        
        
        
        // echo "<pre>";
        // print_r($params);
        // echo "</pre>";
        // echo "<br><br><br>";
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit;

















        //     $date =  date('H:i:s');
        //     $smsLibrary = new SmsLibrary();
        //    // echo $smsLibrary->sendSms('0659851709', "This is Cron Jon Running ,$date");

        //     // Start time
        //     $start_time = microtime(true);

        //     // Database connection
        //     $db = db_connect();

        //     // Query to fetch users
        //     $users = $db->table('users')->select()->get()->getResult();

        //     // End time
        //     $end_time = microtime(true);

        //     // Calculate execution time in seconds
        //     $execution_time = $end_time - $start_time;

        //     // Print the result and execution time

        //     echo "Query executed in: " . number_format($execution_time, 6) . " seconds\n";
        //     Printer($users);  // Assuming Printer is a custom function for output


        // $email = \Config\Services::email();

        // // $email->setFrom('purposemany@gmail.com', 'wma');
        // $email->setTo('purposemany@gmail.com');


        // $email->setSubject('Email Test');
        // $email->setMessage('Testing the email class.');

        // if ($email->send()) {
        //     echo 'sent';
        // } else {
        //     echo 'err';
        // }
        // exit;


        // try {
        //     $email = \Config\Services::email();
        //     $email->setTo('cassims44@gmail.com');
        //     $email->setSubject('test');
        //     $email->setMessage('Hello World');
        //     if ($email->send()) {
        //         // return $this->response->setJSON([
        //         //     'status' => 1,
        //         //     'msg' => 'Password Is Reset Successfully',
        //         //     // 'token' => $this->token,


        //         // ]);
        //         echo 'sent';
        //     } else {
        //         echo 'err';
        //         return $this->response->setJSON([
        //             'status' => 0,
        //             'msg' => 'Email Was Not Sent',
        //             // 'token' => $this->token,
        //         ]);
        //     }
        // } catch (\Throwable $th) {
        //     echo $th->getMessage();


        // $response = [
        //     'status' => 0,
        //     'msg' => $th->getMessage(),

        // ];
        // return $this->response->setJSON($response);
        // }
        // exit;


        // $date =  date('Y-m-d\TH:i:s');

        // // echo $date;
        // // exit;


        // $smsLibrary = new SmsLibrary();
        // $response = $smsLibrary->sendSms('0659851709', "Hello There,date $date");
        // echo $response;
    }







    public function validateForm()
    {

        $optional = [];
        $conditions = [
            (object)['key' => 'email', 'condition' => '|valid_email'],
            (object)['key' => 'phone', 'condition' => '|min_length[10]|max_length[10]'],
        ];

        $rules = formValidation(conditions: $conditions, optionalFields: $optional);
        // return $this->response->setJSON([
        //     $rules
        // ]);

        // exit;

        $rls = [
            'phone' => 'required'
        ];


        if ($this->validate($rules)) {
            return $this->response->setJSON([
                'msg' => 'Form is ok',
                'validation' => []
            ]);
        } else {

            return $this->response->setJSON([
                'validation' => $this->validator->getErrors()
            ]);
        }
    }

    public function reconTest1()
    {

        // Dataset 1
        $paymentData = [
            (object)['transactionId' => 1, 'controlNumber' => '1996000001', 'date' => '2022-01-01', 'amount' => 100],
            (object)['transactionId' => 2, 'controlNumber' => '1996000021', 'date' => '2022-02-01', 'amount' => 200],
            (object)['transactionId' => 3, 'controlNumber' => '1996000225', 'date' => '2022-03-01', 'amount' => 300],
        ];

        // Dataset 2
        $bankRecords = [
            (object)['transactionId' => 1, 'controlNumber' => '1996000001', 'date' => '2022-01-01', 'amount' => 254],
            (object)['transactionId' => 2, 'controlNumber' => '1996000021', 'date' => '2022-02-01', 'amount' => 200],
            (object)['transactionId' => 3, 'controlNumber' => '1996000225', 'date' => '2022-03-01', 'amount' => 300],
            (object)['transactionId' => 4, 'controlNumber' => '1996000227', 'date' => '2022-04-01', 'amount' => 400],
        ];

        $recon = [
            (object)[
                'TnxId' => '045232',
                'PayRefId' => '496516045232',
            ],
            (object)[
                'TnxId' => '456456',
                'PayRefId' => '894646467946',
            ],
            (object)[
                'TnxId' => '785455',
                'PayRefId' => '200012455',
            ],
        ];
        $payment = [
            (object)[
                'TnxId' => '045232',
                'PayRefId' => '496516045232',
            ],
            (object)[
                'TnxId' => '456456',
                'PayRefId' => '894646467946',
            ],
        ];

        // Sort both datasets based on transactionId
        usort($dataset1, function ($a, $b) {
            return $a->transactionId - $b->transactionId;
        });

        usort($dataset2, function ($a, $b) {
            return $a->transactionId - $b->transactionId;
        });



        $comparedData = array_map(function ($transaction1) use ($dataset2) {
            $matchedTransaction = array_values(array_filter($dataset2, function ($transaction2) use ($transaction1) {
                return $transaction1->transactionId === $transaction2->transactionId
                    && $transaction1->controlNumber === $transaction2->controlNumber
                    && $transaction1->amount === $transaction2->amount;
            }));

            if (empty($matchedTransaction)) {
                return (object)['transactionId' => $transaction1->transactionId, 'status' => 'ERR'];
            } else {
                return (object)['transactionId' => $transaction1->transactionId, 'status' => 'OK'];
            }
        }, $dataset1);

        $data['dataset1'] = $dataset1;
        $data['dataset2'] = $dataset2;
        $data['comparedData'] = $comparedData;

        return view('reconTest', $data);
    }


    public function reconTest()
    {


        $recon = [
            (object)[
                'TnxId' => '045232',
                'PayRefId' => '496516045232',
                'amount' => 100.5
            ],
            (object)[
                'TnxId' => '456456',
                'PayRefId' => '894646467946',
                'amount' => 200
            ],
            (object)[
                'TnxId' => '785455',
                'PayRefId' => '200012455',
                'amount' => 300
            ],
        ];
        $payment = [
            (object)[
                'TnxId' => '045232',
                'PayRefId' => '496516045232',
                'amount' => 100
            ],
            (object)[
                'TnxId' => '456456',
                'PayRefId' => '894646467946',
                'amount' => 200
            ],
        ];
        $matched = array_reduce($recon, function ($acc, $reconObj) use ($payment) {
            $matchFound = array_filter($payment, function ($paymentObj) use ($reconObj) {
                return $reconObj->TnxId == $paymentObj->TnxId && $reconObj->PayRefId == $paymentObj->PayRefId &&  $reconObj->amount == $paymentObj->amount;
            });
            if (!empty($matchFound)) {
                $acc[] = $reconObj;
            }
            return $acc;
        }, []);

        $unmatchedRecon = array_filter($recon, function ($reconObj) use ($matched) {
            return !in_array($reconObj, $matched);
        });

        $unmatchedPayment = array_filter($payment, function ($paymentObj) use ($matched) {
            $matchFound = array_filter($matched, function ($matched_obj) use ($paymentObj) {
                return $matched_obj->TnxId == $paymentObj->TnxId && $matched_obj->PayRefId == $paymentObj->PayRefId;
            });
            return empty($matchFound);
        });

        $data['matched'] = $matched;
        $data['unmatchedRecon'] = $unmatchedRecon;
        $data['unmatchedPayment'] = $unmatchedPayment;

        return view('reconTest', $data);
    }




    public function map()
    {
        return view('sky');
    }



    public function csvData()
    {
        $file = $this->request->getFile('file');

        // return  $this->response->setJSON([
        //   'data' => $file->getExtension(),
        // ]);
        // exit;
        if ($file->isValid() && $file->getExtension() == 'csv') {
            $csv = array_map('str_getcsv', file($file->getPathname()));
            $keys = array_shift($csv);
            $data = array();
            foreach ($csv as $i => $row) {
                $data[$i] = array_combine($keys, $row);
            }

            $csvData = array_map(fn($item) => [
                'date' => $item['STATEMENT DATE'],
                'amount' => (float)str_replace(',', '', $item['CREDIT AMOUNT']),
                'transactionReference' => $item['TRANSACTION REFFERENCE NUMBER'],
                'controlNumber' => $this->controlNumber($item['NAME / DESCRIPTION']),
            ], $data);
            return $this->response->setJSON($csvData);
        } else {
            return $this->response->setJSON(['msg' => 'Invalid file format']);
        }
    }

    function controlNumber($billString)
    {
        // $billString = 'TMS GEPG BIL:994191180244 REC:923032157281899 NESHAL INVESTM REF:FH244201675228364';
        $pattern = '/BIL:(\d{12})/'; // The regular expression pattern
        preg_match($pattern, $billString, $matches); // Search for the pattern in the string

        if (count($matches) > 1) {
            $result = $matches[1]; // The 12 digits after "BIL:" are captured in group 1
            return $result; // Output: 994191180244
        } else {
            return '';
        }
    }







    // public function csvDatax()
    // {
    //     $file = $this->request->getFile('file');

    //     if ($file->isValid() && in_array($file->getExtension(), ['csv', 'xls', 'xlsx'])) {
    //         if ($file->getExtension() == 'csv') {
    //             $csv = array_map('str_getcsv', file($file->getPathname()));
    //             $keys = array_shift($csv);
    //             $data = array();
    //             foreach ($csv as $i => $row) {
    //                 $data[$i] = array_combine($keys, $row);
    //             }
    //         } else {
    //             $reader = [];
    //             // $reader->setReadDataOnly(true);
    //             // $spreadsheet = $reader->load($file->getPathname());
    //     //         $worksheet =[];
    //     //         $data = array();
    //     //         $keys = array();
    //     //         foreach ($worksheet->getRowIterator() as $row) {
    //     //             $cellIterator = $row->getCellIterator();
    //     //             $cellIterator->setIterateOnlyExistingCells(FALSE);
    //     //             $row_data = array();
    //     //             foreach ($cellIterator as $cell) {
    //     //                 if ($keys) {
    //     //                     $row_data[] = $cell->getValue();
    //     //                 } else {
    //     //                     $keys[] = $cell->getValue();
    //     //                 }
    //     //             }
    //     //             if ($keys) {
    //     //                 $data[] = $keys;
    //     //             }
    //     //         }
    //     //     }
    //     //     return $this->response->setJSON($data[0]);
    //     // } else {
    //     //     return $this->response->setJSON(['msg' => 'Invalid file format']);
    //     // }
    // }

    public function generateQRCode()
    {
        $data = [
            'id' => randomString(),
            'activity' => 'vtv',
            'url' => 'https://yts.mx/',
            'center' => 'Ilala',
            'amount' => 520000,
        ];

        echo QRCode($data);
    }

    public function geoLocation()
    {
        return  $this->response->setJSON([
            'data' => $this->request->getPost(),
            'token' => csrf_hash()
        ]);
    }


    public function form()
    {
        // formValidation();
        $conditions = [
            ['key' => 'email', 'condition' => '|email'],
            ['key' => 'age', 'condition' => '|numeric|min[2]|max[10]'],
            // (object) ['key' => 'password', 'condition' => '|min:8|max:20'],
        ];
        printer(formValidation(conditions: $conditions));

        return view('form');

        $codes = ['7101', '7101', '7204'];
        $messages = ['success', 'success', 'Insufficient balance'];
    }
}
