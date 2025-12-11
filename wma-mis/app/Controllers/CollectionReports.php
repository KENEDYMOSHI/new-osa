<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\PortModel;
use PHPUnit\Util\Printer;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\ArrayLibrary;
use App\Libraries\RenderReport;
use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Libraries\ReportLibrary;
use App\Libraries\DownloadReport;
use App\Models\MiscellaneousModel;
use App\Controllers\BaseController;
use App\Libraries\PrePackageLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Models\ReportModel;

// use PrePackageLibrary;

class CollectionReports extends BaseController
{



    public $sessionExpiration;

    public $variable;
    public $reportModel;

    public $renderReport;
    public $downloadReport;

    private $token;
    protected $report;
    protected $collectionCenter;
    protected $billModel;
    protected $commonTasks;
    protected $reportLibrary;
    protected $pdfLibrary;
    protected $profileModel;
    protected $session;
    protected $vtcModel;
    protected $waterMeterModel;
    protected $uniqueId;
    protected $user;

    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->user = auth()->user();

        $this->profileModel = new ProfileModel();
        $this->session = session();
        $this->token = csrf_hash();
        $this->report = 'Report For  Financial Year';

        $this->renderReport = new RenderReport();
        $this->downloadReport = new DownloadReport();
        $this->commonTasks = new CommonTasksLibrary();

        $this->vtcModel = new VtcModel();
        $this->reportLibrary = new ReportLibrary();
        $this->pdfLibrary = new PdfLibrary();
        $this->billModel = new BillModel();

        $this->reportModel = new ReportModel();
        $this->waterMeterModel = new WaterMeterModel();

        $this->uniqueId =   $this->user->unique_id;
        $this->collectionCenter =  $this->user->collection_center;
        helper(['report', 'form', 'array', 'regions', 'date', 'documents', 'image']);
    }

    public function getVariable($var)
    {
        $input  = $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
        if ($input === null) {
            return '';
        } else {
            return $input;
        }
    }



    public function index()
    {



        $data['page'] = [
            "title" => "Reports",
            "heading" => "Reports",
        ];



        $data['user'] = auth()->user();
        $data['userLocation'] = centerName();
        $data['centers'] = $this->commonTasks->collectionCenters();
        return view('Pages/collectionReports/wmaReport', $data);
    }

    public function getCollectionReport()
    {
        try {
            $user = auth()->user();
            $center = $this->collectionCenter;

            $title = '';


            $year = $this->getVariable('year');
            $selectedCenter = $this->getVariable('collectionCenter');
            $month = $this->getVariable('month');
            $dateFrom = $this->getVariable('dateFrom');
            $dateTo = $this->getVariable('dateTo');
            $quarter = $this->getVariable('quarter');
            $activity = $this->getVariable('activity');
            $task = $this->getVariable('task');
            $paymentStatus = $this->getVariable('paymentStatus');

            $years = explode('/', $year);
            $startYear = $years[0];
            $endYear = $years[1];

            $period = '';



            switch ($quarter) {
                case 'Q1':
                    $startDate = $startYear . '-07-01';
                    $endDate = $startYear . '-09-30 23:59:59';
                    $period = 'Quarter One';
                    break;
                case 'Q2':
                    $startDate = $startYear . '-10-01';
                    $endDate = $startYear . '-12-30 23:59:59';
                    $period = 'Quarter Two';
                    break;
                case 'Q3':
                    $startDate = ($endYear) . '-01-01';
                    $endDate = ($endYear) . '-03-30 23:59:59';
                    $period = 'Quarter Three';
                    break;
                case 'Q4':
                    $startDate = ($endYear) . '-04-01';
                    $endDate = ($endYear) . '-06-30 23:59:59';
                    $period = 'Quarter Four';
                    break;

                case 'Annually':

                    $startDate = ($startYear) . '-07-01';
                    $endDate = ($endYear) . '-06-30 23:59:59';
                    $period =  'Annual';
                    // $currentMonth = date('m');

                    // if ($currentMonth >= 7) {
                    //     $startDate = date('Y-07-01');
                    //     $endDate = date('Y-06-30', strtotime('+1 year'));
                    // } else {
                    //     $startDate = date('Y-07-01', strtotime('-1 year'));
                    //     $endDate = date('Y-06-30');
                    // }

                    break;
            }

            $payment = $paymentStatus != '' ?  $paymentStatus : '';
            $rgn = $selectedCenter == '' ? wmaCenter()->centerName : wmaCenter($selectedCenter)->centerName;

            $region = str_replace('Wakala Wa Vipimo', '', $rgn);

            $taskType = $task != '' ? $task : '';
            //get the activity name based on gfs code
            $source = $region . ' ' . $taskType . activityName($activity);

            $currentMonth = date('m');




            if ($currentMonth >= 7) {
                $initialDate = new DateTime("$startYear-07-01");
                $finalDate = new DateTime("$startYear-06-30");
                $finalDate->add(new DateInterval('P1Y')); // Add 1 year
            } else {
                $initialDate = new DateTime("$startYear-07-01");
                $initialDate->sub(new DateInterval('P1Y')); // Subtract 1 year
                $finalDate = new DateTime("$startYear-06-30");
            }

            $initialDate = $initialDate->format('Y-m-d');
            $finalDate = $finalDate->format('Y-m-d');


            //=================getting appropriate title fot the report====================
            if ($month != '') {
                $title = $source . ' ' . date("F", mktime(0, 0, 0, $month, 1)) . " " . $startYear . " Collection Report $payment";
            } elseif ($dateFrom != '' && $dateTo != '') {
                $title = $source . ' ' . dateFormatter($dateFrom) . ' - ' . dateFormatter($dateTo) . " Collection Report $payment";
            } elseif ($quarter != '') {

                $title = $source . ' ' . "$period Collection Report Of Financial Year " . $startYear . '|' . ($endYear) . ' ' . $payment;
            } else {
                $title = $source . ' ' . "$period Collection  Report Of Financial Year " . date('Y', strtotime($initialDate)) . '|' . date('Y', strtotime($finalDate)) . ' ' . $payment;
            }




            //    if($month == '') $title = $source . ' ' . "$period Collection  Report Of Financial Year " . date('Y', strtotime($initialDate)) . '|' . date('Y', strtotime($finalDate)) . ' ' . $payment;




            $dateTo = $dateTo != '' ? $dateTo . ' 23:59:59' : '';



            $paymentParams = [
                'CenterNumber' => $user->inGroup('officer', 'manager','accountant') ? $center : $selectedCenter,
                'GfsCode' => $activity,
                'Task' => $task,
                'DATE(bill_payment.TrxDtTm)>=' => $month == '' ? ($dateFrom != '' ? $dateFrom : ($quarter ? $startDate : $initialDate)) : '',
                'DATE(bill_payment.TrxDtTm)<=' => $month == '' ? ($dateTo  != '' ? $dateTo : ($quarter ? $endDate : $finalDate)) : '',
                'MONTH(bill_payment.TrxDtTm)' => $month,
                'YEAR(bill_payment.TrxDtTm)' => $month != '' ? $startYear  : '',


            ];
            $unpaidParams = [
                'wma_bill.CollectionCenter' => $user->inGroup('officer', 'manager','accountant') ? $center : $selectedCenter,
                'GfsCode' => $activity,
                'bill_items.Task' => $task,
                'DATE(wma_bill.CreatedAt)>=' => $month == '' ? ($dateFrom != '' ? $dateFrom : ($quarter ? $startDate : $initialDate)) : '',
                'DATE(wma_bill.CreatedAt)<=' => $month == '' ? ($dateTo  != '' ? $dateTo : ($quarter ? $endDate : $finalDate)) : '',

                'MONTH(wma_bill.CreatedAt)' => $month,
                'YEAR(wma_bill.CreatedAt)' => $month != '' ? $startYear  : '',
                'PaymentStatus' => $paymentStatus,



            ];


            $params = array_filter($paymentParams, fn ($param) => $param !== '' || $param != null);
            $params2 = array_filter($unpaidParams, fn ($param) => $param !== '' || $param != null);

            //get app payments exact and partial
            $allPaid  = $paymentStatus == '' || $paymentStatus == 'Paid'  ? $this->reportModel->getPaidPayments($params) : [];
            //isolate exact payments
            $exactPaid = (new ArrayLibrary($allPaid))->filter(fn ($item) => $item->BillPayOpt == 3)->get();
            //isolate partial payments
            $partialPaid = $this->reportLibrary->partialsPaid($allPaid);
            //combine exact and partial payments
            $wmaPayments = array_merge($exactPaid, $partialPaid);

            $pendingPartial =  $paymentStatus == '' || $paymentStatus == 'Pending' || $paymentStatus == 'Partial' ? $this->reportModel->getPendingPartialPayments($params2, $paymentStatus) : [];

            $pending = (new ArrayLibrary($pendingPartial))->filter(fn ($item) =>  $item->PaymentStatus == 'Pending')->get();

            $partials = $this->reportLibrary->partialsUnpaid($pendingPartial);

            $unpaid = array_merge($pending, $partials);


            $collection = array_merge($wmaPayments, $unpaid);

            // return $this->response->setJSON([
            //     'status' => 0,
            //     'data' => $pending,
            //     'token' => $this->token
            // ]);
            // exit;

            //  return $this->response->setJSON([
            //    'status' => 0,
            //    'partial ARR' => $ppA,
            //    'partial TOTAL' => number_format($pp),
            //    'partial' => $partial,
            //    'pending' => $pending,
            //    'paid' => $allPaid,
            //    'token' => $this->token
            //  ]);

            //  exit;






            $link = '';
            //render selected data into html template using helper
            $template = $activity == '' ? $this->reportLibrary->reportTemplateAllActivities($collection) : $this->reportLibrary->reportTemplate($collection);

            foreach ($unpaidParams as $value) {
                if ($value !== '') {
                    $link .= str_replace(':', '+', $value) . '/';
                } else {
                    $link .= "wma/";
                }
            }


            $cleanLink =  str_replace(':', ';', $link);



            return $this->response->setJSON([
                'items' => $template->itm,
                'report' => $template->report,
                'summary' => $template->summary,
                'params' => $params2,
                'activity' => $activity,
                'link' => base_url('downloadReportData/') . $link . underscore($cleanLink),
                'title' =>  $title,
                'token' => $this->token,
                //'params' => $clearedPartial,



            ]);
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }



    public function downloadReportData($selectedCenter, $activity, $task, $dateFrom, $dateTo, $month, $year, $paymentStatus, $title)
    {

        $data = [
            'wma_bill.CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->collectionCenter : $selectedCenter,
            'wma_bill.IsCancelled' => 'No',
            'Activity' => $activity,
            'wma_bill.Task' => $task,
            'DATE(wma_bill.CreatedAt)>=' => $dateFrom,
            'DATE(wma_bill.CreatedAt)<=' => str_replace('23 59 59', '23:59:59', $dateTo),
            'MONTH(wma_bill.CreatedAt)' => $month,
            'YEAR(wma_bill.CreatedAt)' => $year,
            'PaymentStatus' => $paymentStatus,
            'PayCntrNum !=' => ''


        ];



        $paymentParams = [
            'CenterNumber' => $this->user->inGroup('officer', 'manager','accountant') ? $this->collectionCenter : $selectedCenter,
            'GfsCode' => $activity,
            'Task' => $task,
            'DATE(bill_payment.TrxDtTm)>=' =>  $dateFrom,
            'DATE(bill_payment.TrxDtTm)<=' =>str_replace('23 59 59', '23:59:59', $dateTo),
            'MONTH(bill_payment.TrxDtTm)' => $month,
            'YEAR(bill_payment.TrxDtTm)' => $year,


        ];
        $unpaidParams = [
            'wma_bill.CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->collectionCenter : $selectedCenter,
   
            'GfsCode' => $activity,
            'bill_items.Task' => $task,
            'DATE(wma_bill.CreatedAt)>=' =>  $dateFrom,
            'DATE(wma_bill.CreatedAt)<=' => str_replace('23 59 59', '23:59:59', $dateTo),

            'MONTH(wma_bill.CreatedAt)' => $month,
            'YEAR(wma_bill.CreatedAt)' => $year,
            'PaymentStatus' => $paymentStatus,



        ];


        $params = array_filter($paymentParams, fn ($param) => $param != 'wma');
        $params2 = array_filter($unpaidParams, fn ($param) => $param != 'wma');








       $paymentStatus == 'wma' ? $paymentStatus = '' : $paymentStatus;




        // $clearedPartial = 0;

        // echo '<pre>';
        // print_r($params);
        // print_r($params2);
        // echo $paymentStatus;
        // echo '</pre>';
        //  exit;
        // if ($activity == 'others') unset($data['Activity']);
        //  $params = array_filter($data, fn ($param) => $param !== 'wma');

        //get app payments exact and partial
        $allPaid  = $paymentStatus == '' || $paymentStatus == 'Paid'  ? $this->reportModel->getPaidPayments($params) : [];

        // Printer($allPaid);
        // exit;
        //isolate exact payments
        $exactPaid = (new ArrayLibrary($allPaid))->filter(fn ($item) => $item->BillPayOpt == 3)->get();
        //isolate partial payments
        $partialPaid = $this->reportLibrary->partialsPaid($allPaid);
        //combine exact and partial payments
        $wmaPayments = array_merge($exactPaid, $partialPaid);

        $pendingPartial =  $paymentStatus == '' || $paymentStatus == 'Pending' || $paymentStatus == 'Partial' ? $this->reportModel->getPendingPartialPayments($params2, $paymentStatus) : [];

        $pending = (new ArrayLibrary($pendingPartial))->filter(fn ($item) =>  $item->PaymentStatus == 'Pending')->get();

        $partials = $this->reportLibrary->partialsUnpaid($pendingPartial);

        $unpaid = array_merge($pending, $partials);


        $collection = array_merge($wmaPayments, $unpaid);


        // Printer($data);
        // Printer($params);

       



        $output['title'] = humanize($title);
        $output['template'] = $activity == 'wma' ? $this->reportLibrary->reportTemplateAllActivities($collection) : $this->reportLibrary->reportTemplate($collection);
        // $dsd['template'] = $this->reportLibrary->reportTemplateAllActivities($collection);


        //rendering a pdf using Mpdf 
        $this->pdfLibrary->renderPdf(orientation: 'P', view: 'ReportTemplates/reportView', data: $output, title: $title);
    }




    //check if params containing either $activity = ['vtv ','sbl','waterMeter','prepackage'] if contain any of  values in x return the value else return ''
    //check if params containing two dates and return small date  else return ''





}
