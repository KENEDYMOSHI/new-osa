<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\AdminModel;

use App\Models\LorriesModel;

use App\Libraries\PdfLibrary;



use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Controllers\BaseController;
use App\Libraries\ArrayLibrary;
use App\Libraries\CommonTasksLibrary;
use PHPUnit\TextUI\Output\Printer as OutputPrinter;
use PHPUnit\Util\Printer;

class ReceivableController extends BaseController
{

    protected $session;
    protected $uniqueId;
    protected $profileModel;
    protected $billModel;
    protected $prePackageModel;
    protected $lorriesModel;
    protected $vtcModel;
    protected $waterMeterModel;
    protected $commonTasks;
    protected $admin;
    protected $adminModel;
    protected $appRequest;
    protected $email;
    protected $token;
    protected $collectionCenter;
    protected $user;



    public function __construct()
    {
        $this->token = csrf_hash();
        $this->email = \Config\Services::email();
        $this->appRequest = service('request');
        helper(['format', 'form', 'array', 'regions', 'date', 'emailTemplate', 'image']);
        $this->commonTasks     = new CommonTasksLibrary;
        $this->session         = session();
        $this->adminModel    = new AdminModel();
        $this->billModel      = new BillModel();
        $this->prePackageModel = new prePackageModel();
        $this->lorriesModel    = new LorriesModel();
        $this->vtcModel        = new VtcModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->user = auth()->user();
    }

    function flattenArray($array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $key;

            if (is_array($value)) {
                $result = array_merge($result, (array)$this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return (object)$result;
    }

    public function groupedData($centerData)
    {
        $groupedData = array_fill_keys(['_1_30', '_31_60', '_61_90', '_91_120', '_121_365', '_above365'], 0);

        $currentDate = new DateTime();

        foreach ($centerData as $item) {
            $createdAt = new DateTime($item->BillGenDt);
            $interval = $currentDate->diff($createdAt);
            $daysDiff = $interval->days;

            $groupKey = '';
            if ($daysDiff >= 0 && $daysDiff <= 30) {
                $groupKey = '_1_30';
            } elseif ($daysDiff >= 31 && $daysDiff <= 60) {
                $groupKey = '_31_60';
            } elseif ($daysDiff >= 61 && $daysDiff <= 90) {
                $groupKey = '_61_90';
            } elseif ($daysDiff >= 91 && $daysDiff <= 120) {
                $groupKey = '_91_120';
            } elseif ($daysDiff >= 121 && $daysDiff <= 365) {
                $groupKey = '_121_365';
            } elseif ($daysDiff > 366) {
                $groupKey = '_above365';
            }

            //get appropriate amount based on payment status
            $debt =  $item->PaymentStatus === 'Pending' ? $item->amount : $item->amount - $item->PaidAmount;
            $groupedData[$groupKey] += $debt;
        }
        return $groupedData;
    }


    public function index()
    {
        if ($this->user->hasPermission('receivables.access')) {
            try {
                // Default parameters
                $params = [
                    'PayCntrNum !=' => '',
                    // 'wma_bill.BillGenDt >=' => financialYear()->startDate,
                    // 'wma_bill.BillGenDt <=' => financialYear()->endDate,
                    'wma_bill.IsCancelled' => 'No',
                    //'PaymentStatus' => 'Pending'
                ];

                // Check if the request is POST
                if ($this->request->getMethod() === 'POST') {
                    // Update parameters based on POST data
                    $years = $this->request->getVar('year', FILTER_SANITIZE_SPECIAL_CHARS);
                    $year = explode('_', $years);
                    $startDate = $year[0] . '-07-01';
                    $endDate = $year[1] . '-06-30';

                    $params = [
                        'PayCntrNum !=' => '',
                        'wma_bill.BillGenDt >=' => $years == '' ? financialYear()->startDate : $startDate,
                        'wma_bill.BillGenDt <=' => $years == '' ? financialYear()->endDate : $endDate,
                        'wma_bill.IsCancelled' => 'No',
                        // 'PaymentStatus' => 'Pending'
                    ];

                    $collectionCenters = $this->collectionByCenters($params);
                    $data = $this->groupDays($collectionCenters);
                    $data['collectionData'] = $collectionCenters;
                    $data['financialYear'] = $year[0] . '/' . $year[1]; // Corrected financial year format
                    $data['page'] = [
                        'title' => 'Receivables',
                        'heading' => 'Receivables',
                    ];

                    // Debugging
                    // Uncomment these lines to debug the data being passed to the view
                    // echo '<pre>';
                    // print_r($data);
                    // echo '</pre>';
                    // exit;

                    return view('Pages/receivableSummary', $data);
                }

                $collectionCenters = $this->collectionByCenters($params);
                $data = $this->groupDays($collectionCenters);
                $data['page'] = [
                    'title' => 'Receivables',
                    'heading' => 'Receivables',
                ];
                $years =  date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
                $data['user'] = auth()->user();
                $data['collectionData'] = $collectionCenters;
                $data['financialYear'] = $years;


                return view('Pages/receivableSummary', $data);
            } catch (\Exception $e) {
                // Log the error message
                echo $e->getMessage();

                // Redirect to a specific view with an error message
                // return redirect()->to('receivableSummary')->with('error', 'An error occurred while processing your request. Please try again later.');
            }
        } else {
            return redirect()->to('dashboard');
        }
    }



    public function collectionByCenters($params)
    {




        $dataSource =  $this->billModel->getBillReceivableData($params);
        $centers = array_map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->billModel->getCollectionCenters());
        $collections = array_map(function ($center) use ($dataSource) {

            $centerCode = $center[0];

            $centerData = array_filter($dataSource, function ($data) use ($centerCode) {

                return $data->CollectionCenter === $centerCode;
            });

            $totalAmount = array_reduce($centerData, function ($carry, $data) {
                if ($data->PaymentStatus == 'Partial') {
                    // For Partial PaymentStatus, use amount - PaidAmount
                    $adjustedAmount = $data->amount - $data->PaidAmount;
                } else {
                    // For Pending PaymentStatus, use the amount as is
                    $adjustedAmount = $data->amount;
                }
                return $carry +  $adjustedAmount;
            }, 0);






            $receivables = [
                'center' => $center[0],
                'total' => $totalAmount,
                'centerName' => $center[1],
                $this->groupedData($centerData)

            ];


            return  $this->flattenArray($receivables);
        }, $centers);


        return $collections;
    }




    public function downloadReceivableSummary($years)
    {
        $year = explode('_', $years);
        $startDate = $year[0] . '-07-01';
        $endDate = $year[1] . '-06-30';

        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.BillGenDt >=' => $years == '' ? financialYear()->startDate : $startDate,
            'wma_bill.BillGenDt <=' => $years == '' ? financialYear()->endDate : $endDate,
            'wma_bill.IsCancelled' => 'No',
            // 'PaymentStatus' => 'Pending'
        ];




        $collectionCenters = $this->collectionByCenters($params);




        $data =  $this->groupDays($collectionCenters);
        $data['collectionData'] = $collectionCenters;
        $data['financialYear'] = $years;
        $title =  'Aged Analysis Of Debtors As at ' . dateFormatter(date('Y-m-d')) . '__' . numString(10);

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'P', view: 'ReportTemplates/receivableSummaryPdf', data: $data, title: $title);
    }

    function sortDates($date, $amount, $dayStart, $dayEnd)
    {
        $currentDate = new DateTime();
        $createdAt = new DateTime($date);
        $interval = $currentDate->diff($createdAt);
        $daysDiff = $interval->days;
        if ($daysDiff >= $dayStart && $daysDiff <= $dayEnd) {
            return $amount;
        } else {
            return 0;
        }
    }

    public function groupDays($centerData)
    {
        $data = [];
        $data['total_1_30'] = array_reduce($centerData, fn ($carry, $item) => $carry + $item->_1_30);
        $data['total_31_60'] = array_reduce($centerData, fn ($carry, $item) => $carry + $item->_31_60);
        $data['total_61_90'] = array_reduce($centerData, fn ($carry, $item) => $carry + $item->_61_90);
        $data['total_91_120'] = array_reduce($centerData, fn ($carry, $item) => $carry + $item->_91_120);
        $data['total_121_365'] = array_reduce($centerData, fn ($carry, $item) => $carry + $item->_121_365);
        $data['total_above365'] = array_reduce($centerData, fn ($carry, $item) => $carry + $item->_above365);

        return $data;
    }

    public function regionDebt($collectionCenter, $years)
    {
        $data = [];


        $year = explode('_', $years);
        $startDate = $year[0] . '-07-01';
        $endDate = $year[1] . '-06-30';

        $params = [
            'PayCntrNum !=' => '',
            'CollectionCenter' => $collectionCenter,
            // 'wma_bill.BillGenDt >=' => $years == '' ? financialYear()->startDate : $startDate,
            // 'wma_bill.BillGenDt <=' => $years == '' ? financialYear()->endDate : $endDate,
            'wma_bill.IsCancelled' => 'No',
            // 'PaymentStatus' => 'Pending'
        ];





        $dataSource =  $this->billModel->getBillReceivableData($params);

        // printer($dataSource);
        // exit;
        // $centerData = array_filter($dataSource, fn ($data) => $data->PaymentStatus === 'Pending');



        $centerData = (new ArrayLibrary($dataSource))->map(
            function ($data) {
                $debt =  $data->PaymentStatus === 'Pending' ? $data->amount : $data->amount - $data->PaidAmount;
                return (object)[
                    'billId' => $data->BillId,
                    'paymentStatus' => $data->PaymentStatus,
                    'centerName' => $data->CenterName,
                    'customer' => $data->PyrName,
                    'amount' => $debt,
                    'controlNumber' => $data->PayCntrNum,
                    'mobile' => '+' . $data->PyrCellNum,
                    '_1_30' => $this->sortDates($data->BillGenDt, $debt, 0, 30),
                    '_31_60' => $this->sortDates($data->BillGenDt, $debt, 31, 60),
                    '_61_90' => $this->sortDates($data->BillGenDt, $debt, 61, 90),
                    '_91_120' => $this->sortDates($data->BillGenDt, $debt, 91, 120),
                    '_121_365' => $this->sortDates($data->BillGenDt, $debt, 121, 365),
                    '_above365' => $this->sortDates($data->BillGenDt, $debt, 365, 500000),
                    'CreatedAt' => $data->BillGenDt,
                ];
            }
        )->get();

        // Printer($centerData);
        // exit;


        $data =  $this->groupDays($centerData);
        $data['collectionData'] = $centerData;
        $data['collectionCenter'] = $collectionCenter;
        $data['centerName'] = array_values($centerData)[0]->centerName;
        $data['financialYear'] = $years;

        $data['page'] = [
            'title' => 'Debtor Analysis',
            'heading' => 'Debtor Analysis',
        ];

        return view('Pages/regionReceivableSummary', $data);
    }

    public function downloadRegionalReceivables($collectionCenter, $years)
    {
        $year = explode('_', $years);
        $startDate = $year[0] . '-07-01';
        $endDate = $year[1] . '-06-30';

        $params = [
            'PayCntrNum !=' => '',
            'CollectionCenter' => $collectionCenter,
            'wma_bill.BillGenDt >=' => $years == '' ? financialYear()->startDate : $startDate,
            'wma_bill.BillGenDt <=' => $years == '' ? financialYear()->endDate : $endDate,
            'wma_bill.IsCancelled' => 'No',
            // 'PaymentStatus' => 'Pending'
        ];



        $dataSource =  $this->billModel->getBillReceivableData($params);
        // $centerData = array_filter($dataSource, fn ($data) => $data->PaymentStatus === 'Pending');



        $centerData = (new ArrayLibrary($dataSource))->map(function($data){
            $debt =  $data->PaymentStatus === 'Pending' ? $data->amount : $data->amount - $data->PaidAmount;
           return (object)[
                'centerName' => $data->CenterName,
                'customer' => $data->PyrName,
                'amount' => $data->amount,
                'controlNumber' => $data->PayCntrNum,
                'mobile' => '+' . $data->PyrCellNum,
                '_1_30' => $this->sortDates($data->BillGenDt, $debt, 1, 30),
                '_31_60' => $this->sortDates($data->BillGenDt, $debt, 31, 60),
                '_61_90' => $this->sortDates($data->BillGenDt, $debt, 61, 90),
                '_91_120' => $this->sortDates($data->BillGenDt, $debt, 91, 120),
                '_121_365' => $this->sortDates($data->BillGenDt, $debt, 121, 365),
                '_above365' => $this->sortDates($data->BillGenDt, $debt, 365, 500000),
                'CreatedAt' => $data->BillGenDt,
           ];
        })->get();



        $data =  $this->groupDays($centerData);
        $data['collectionData'] = $centerData;
        $data['centerName'] = array_values($centerData)[0]->centerName;
        $data['financialYear'] = $years;

        $title =  'Weights And Measures ' . array_values($centerData)[0]->centerName . dateFormatter(date('Y-m-d')) . '__' . numString(10);

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'L', view: 'ReportTemplates/regionalReceivableSummaryPdf', data: $data, title: $title);
    }

    public function getBillDetails()
    {
        $billId = $this->request->getVar('billId');
        $bill = $this->billModel->selectBill($billId);
        $billAmount = number_format($bill->BillAmt);
        $xpDate = dateFormatter($bill->BillExprDt);
        $spCode = setting('Bill.spCode');



        $billItems = $this->billModel->fetchBillItems($billId);
        $items = '';
        $sn = 0;
        foreach ($billItems as $billItem) {
            $amount = number_format($billItem->BillItemAmt);
            $sn++;
            $items .= <<<"HTML"
              <tr style="display:flex">
                <td style="width:5%">$sn</td>
                <td style="width:70%">$billItem->ItemName</td>
                <td style="width:30%">Tsh $amount</td>
            </tr>
         HTML;
        }
        $html = <<<"HTML"
        <div class="row">
                           <div class="col-8">
   
                               <table class="table table-sm table-borderless" id="billCustomer">
                                      <tr style="display:flex">
                                       <td>Control Number:</td>
                                       <td><b>$bill->PayCntrNum</b></td>
                                   </tr>
                                      <tr style="display:flex">
                                       <td>Service Provider Code:</td>
                                       <td><b>$spCode</b></td>
                                   </tr>
   
                                      <tr style="display:flex">
                                       <td>Payer:</td>
                                       <td><b>$bill->PyrName</b></td>
                                   </tr>
                                      <tr style="display:flex">
                                       <td>Payer Phone:</td>
                                       <td>+$bill->PyrCellNum</td>
                                   </tr>
                                      <tr style="display:flex">
                                       <td>Bill Description:</td>
                                       <td>$bill->BillDesc</td>
                                   </tr>
                               </table>
                           </div>
                         
                          
   
                       </div>
   
                       <div class="row">
                           <div class="col-8">
                               <table class="table table-sm">
                                   <thead>
                                          <tr style="display:flex">
                                           <th>#</th>
                                           <th style="width: 70%;">Billed Item</th>
                                           <!-- <th>Details</th> -->
                                           <th style="width: 30%;">Amount</th>
   
                                       </tr>
                                   </thead>
                                   <tbody id="billItems">
                                   $items
                                   </tbody>
                               </table>
   
   
   
   
                               <br>
                               <table class="table table-sm table-borderless">
                                      <tr style="display:flex">
                                       <td style="width:50%;"><b>Total Billed Amount:</b></b></td>
                                       <!-- <td></td> -->
                                       <td style="width:50%"><b id="billTotal"> $billAmount (TZS)</b></td>
                                   </tr>
   
                                      <tr style="display:flex">
                                       <td style="width:50%;">Amount In Words:</b></td>
                                       <!-- <td></td> -->
                                       <td style="width:50%"><span id="billTotalInWords">$bill->BillAmtWords.</span></td>
                                   </tr>
                                      <tr style="display:flex">
                                       <td style="width:50%;"><b>Expires On:</b></b></td>
                                       <!-- <td></td> -->
                                       <td style="width:50%"><b id="expire">$xpDate</td>
                                   </tr>
                                      <tr style="display:flex">
                                       <td style="width:50%;">Prepared By:</b></td>
                                       <!-- <td></td> -->
                                       <td style="width:50%"><span id="preparedBy">$bill->BillGenBy</span></td>
                                   </tr>
                                    
                                      
                                   
   
                               </table>
                           </div>
                       </div>
                      
   
                       
      HTML;
        return  $this->response->setJSON([
            'bill' => $html,
            'token' => $this->token,
        ]);
    }
}
