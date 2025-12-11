<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\AdminModel;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Controllers\BaseController;
use App\Libraries\ArrayLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Models\TargetModel;

class InspectionReportController extends BaseController
{

    protected $session;
    protected $profileModel;

    protected $billModel;

    protected $prePackageModel;
    protected $DirectorModel;
    protected $lorriesModel;
    protected $vtcModel;

    protected $targetModel;
    protected $waterMeterModel;
    protected $commonTasks;
    protected $user;
    protected $adminModel;



    public $prePackageCollection;
    public $vehicleTankCollection;
    public $lorriesCollection;


    public $waterMeterCollection;
    public $appRequest;
    public $email;
    public $token;



    public function __construct()
    {
        $this->token = csrf_hash();
        $this->email = \Config\Services::email();
        $this->appRequest = service('request');
        helper(['format', 'form', 'array', 'regions', 'date', 'emailTemplate', 'image']);
        $this->commonTasks     = new CommonTasksLibrary;
        $this->session         = session();
        $this->adminModel    = new AdminModel();
        $this->profileModel    = new ProfileModel();
        $this->billModel      = new BillModel();
        $this->prePackageModel = new prePackageModel();
       
        $this->lorriesModel    = new LorriesModel();
        $this->vtcModel        = new VtcModel();
        $this->targetModel  = new TargetModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->user = auth()->user();
    }

   

    public function rejected()
    {
        $data['page'] = [
            'title' => 'Inspection Report',
            'heading' => 'Inspection Report(Rejected)',
        ];

        $params = [
            'task' => 'Inspection',
            'created_at>=' => financialYear()->startDate,
            'created_at<=' => financialYear()->endDate,
            // 'deletedAt !=' => ''




        ];

        $params2 = [
            'task' => 'Inspection',
            'CreatedAt>=' => financialYear()->startDate,
            'CreatedAt<=' => financialYear()->endDate,
            // 'deletedAt !=' => ''




        ];
        $title = 'Inspection Report (Rejected Instruments)';
        $status = 'Rejected';

        $data['user'] = auth()->user();
        $instruments =  $this->instrumentsByCenters($params, $params2, $title, $status);
        $data['instruments'] =  $instruments;
        $data['dataTable'] = $this->renderReport($instruments);
        // $data['dataTable'] = '';
        $data['financialYear'] = date('Y', strtotime(financialYear()->startDate)) . '|' . date('Y', strtotime(financialYear()->endDate));

        // printer($instruments);
        // exit;
        return view('Pages/InspectionReport', $data);
    }
    public function condemned()
    {
        $data['page'] = [
            'title' => 'Inspection Report',
            'heading' => 'Inspection Report(Condemned)',
        ];

        $params = [
            'task' => 'Inspection',
            'created_at>=' => financialYear()->startDate,
            'created_at<=' => financialYear()->endDate,
            // 'deletedAt !=' => ''




        ];

        $params2 = [
            'task' => 'Inspection',
            'CreatedAt>=' => financialYear()->startDate,
            'CreatedAt<=' => financialYear()->endDate,
            // 'deletedAt !=' => ''




        ];
        $title = 'Inspection Report (Rejected Instruments)';
        $status = 'Condemned';

        $data['user'] = auth()->user();
        $instruments =  $this->instrumentsByCenters($params, $params2, $title, $status);
        $data['instruments'] =  $instruments;
        $data['dataTable'] = $this->renderReport($instruments);
        // $data['dataTable'] = '';
        $data['financialYear'] = date('Y', strtotime(financialYear()->startDate)) . '|' . date('Y', strtotime(financialYear()->endDate));

        // printer($instruments);
        // exit;
        return view('Pages/InspectionReport', $data);
    }


    public function adjustment()
    {
        $data['page'] = [
            'title' => 'Inspection Report',
            'heading' => 'Inspection Report(Adjustment)',
        ];

        $params = [
            'task' => 'Inspection',
            'created_at>=' => financialYear()->startDate,
            'created_at<=' => financialYear()->endDate,
            // 'deletedAt !=' => ''




        ];

        $params2 = [
            'task' => 'Inspection',
            'CreatedAt>=' => financialYear()->startDate,
            'CreatedAt<=' => financialYear()->endDate,
            // 'deletedAt !=' => ''




        ];
        $title = 'Inspection Report (Rejected Instruments)';
        $status = 'Condemned';

        $data['user'] = auth()->user();
        $instruments =  $this->instrumentsByCenters($params, $params2, $title, $status);
        $data['instruments'] =  $instruments;
        $data['dataTable'] = $this->renderReport($instruments);
        // $data['dataTable'] = '';
        $data['financialYear'] = date('Y', strtotime(financialYear()->startDate)) . '|' . date('Y', strtotime(financialYear()->endDate));

        // printer($instruments);
        // exit;
        return view('Pages/InspectionReport', $data);
    }


    public function filterInspected()
    {

        $year = $this->getVariable('year') ?? date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
        $month = $this->getVariable('month');
        $quarter = $this->getVariable('quarter') ?? 'Annually';
        $status = $this->getVariable('status');
        // $q = 'Q2';
        // $quarter = $q ?? 'Annually';

        $years = explode('/', $year);
        $startYear = $years[0];
        $endYear = $years[1];




        $period = '';

        switch ($quarter) {
            case 'Q1':
                $startDate = $startYear . '-07-01';
                $endDate = $startYear . '-09-30';
                $period = 'Quarter One';
                break;
            case 'Q2':
                $startDate = $startYear . '-10-01';
                $endDate = $startYear . '-12-30';
                $period = 'Quarter Two';
                break;
            case 'Q3':
                $startDate = ($endYear) . '-01-01';
                $endDate = ($endYear) . '-03-30';
                $period = 'Quarter Three';
                break;
            case 'Q4':
                $startDate = ($endYear) . '-04-01';
                $endDate = ($endYear) . '-06-30';
                $period = 'Quarter Four';
                break;

            case 'Annually':

                $startDate = ($startYear) . '-07-01';
                $endDate = ($endYear) . '-06-30';
                $period = 'Annual';


                break;
        }


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
            $title =  date("F", mktime(0, 0, 0, $month, 1)) . " " . $startYear . " $status Instruments Report";
        } elseif ($quarter != '') {

            $title =  "$period $status Instruments Report Of Financial Year " . $startYear . '|' . ($endYear) . ' ';
        } else {
            $title =  "$period $status Instruments  Report Of Financial Year " . date('Y', strtotime($initialDate)) . '|' . date('Y', strtotime($finalDate)) . ' ';
        }


        $data = [
            'task' => 'Inspection',
            'created_at>=' => $month == '' ?  ($quarter ? $startDate : '') : '',
            'created_at<=' => $month == '' ? ($quarter ? $endDate : '') : '',
            'MONTH(created_at)' => $month ?? '',
            'YEAR(created_at)' => $month != '' ? $startYear  : '',



        ];

        $data2 = [
            'task' => 'Inspection',
            'CreatedAt>=' => $month == '' ?  ($quarter ? $startDate : '') : '',
            'CreatedAt<=' => $month == '' ? ($quarter ? $endDate : '') : '',
            'MONTH(CreatedAt)' => $month ?? '',
            'YEAR(CreatedAt)' => $month != '' ? $startYear  : '',

        ];

        // return  $this->response->setJSON([
        //   'data' => $data,
        //   'data2' => $data2,
        //   'token' => $this->token
        // ]);


        $params = array_filter($data, fn ($param) => $param !== '' || $param != null);

        // return $params1;
        // exit;





        $params2 = array_filter($data2, fn ($param) => $param !== '' || $param != null);

        // $vtv = $this->inspectionData($this->vtcModel->vtvInspection($params));

        // return  $this->response->setJSON([
        //     'status' => 1,

        //     'report' => $vtv,
        //     'token' => $this->token,


        // ]);
        // exit;

        $instrumentsData = $this->instrumentsByCenters($params, $params2, $title, $status);

        $date1 = $month == '' ?  ($quarter ? $startDate : '00') : '00';
        $date2 = $month == '' ? ($quarter ? $endDate : '00') : '00';
        $theMonth =  $month ?? '00';
        $theYear =   $month != '' ? $startYear  : '00';
        $reportTitle = strtoupper($title);
        $link = base_url("downloadInspected/$date1/$date2/$theMonth/$theYear/$reportTitle/$status");

        return  $this->response->setJSON([
            'status' => 1,
            'report' => $this->renderReport($instrumentsData),
            // 'report' => $vtv,
            'token' => $this->token,
            'link' => $link,
            'title' => $reportTitle,
            'data2' => $data2,

        ]);
    }

    public  function inspectionData($array, $status)
    {
        //get opposite status for effective filtering
        $except = $status == 'Rejected' ? 'Condemned' : 'Rejected';
        //filter only rejected instruments and remove pass and condemned
        $data = (new ArrayLibrary($array))->filter(fn ($item) => ($item->visualInspection ===  $status && $item->visualInspection !=  $except) || ($item->testing == $status && $item->testing != $except))->map(fn ($item) => (object)[
            'date' => $item->created_at,
            'status' => $item->testing == 'Rejected' ||  $item->visualInspection == 'Rejected' ? 'Rejected' : 'Rejected'
        ])->get();

        return $data;

 
    }


    public function instrumentsByCenters($params, $params2, $title, $status)
    {

        try {

            //mapping each region with corresponding inspection data
            $centers = array_map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->billModel->getCollectionCenters());
            $instruments = array_map(function ($center) use ($params, $params2, $title, $status) {

                $centerCode = $center[0];
                $params['users.collection_center'] =  $centerCode;
                $params2['users.collection_center'] =  $centerCode;


                $vtv = $this->vtcModel->vtvInspection($params);
                $sbl = $this->lorriesModel->sblInspection($params);

                $vtvRejected = $this->inspectionData($vtv,$status);
                $sblRejected = $this->inspectionData($sbl, $status);


                $othersActivities = $this->billModel->getInstruments($params2);
                $others = array_map(fn ($item) => (object)[
                    'date' => $item->CreatedAt,
                    'status' => $item->Status
                ], $othersActivities);




                $othersRejected = (new ArrayLibrary($others))->filter(fn ($instrument) => $instrument->status == $status)->get();

                $allInspected = array_merge($others, $vtv, $sbl);

                $allRejected = array_merge($othersRejected, $vtvRejected, $sblRejected);
              




                return (object) [
                    'center' => $center[0],
                    'region' => str_replace('Wakala Wa Vipimo', '', $center[1]),
                    'title' => $title,
                    'inspected' => count($allInspected),
                    'nonCompliant' => count($allRejected),


                ];
            }, $centers);


            return $instruments;


            // return  $this->response->setJSON([
            //     'data' => $data2,
            //     'token' => $this->token
            // ]);

            // exit;
        } catch (\Throwable $th) {
            return  [
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'trace' =>  $th->getTrace(),
                'token' => $this->token,

            ];

            // echo $th->getMessage();
        }
    }


    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }




    public function renderReport($instruments)
    {


        // return '';

        // exit;

        // Initialize total variables for each column


        $tr = '';

        $sn = 0;
        foreach ($instruments as $instrument) {
            $sn++;
            $tr .= <<<HTML
                <tr>
                 <td>$sn</td>
                  <td style="width: 30%;">$instrument->region</td>
                  <td>$instrument->inspected</td>
                  <td>$instrument->nonCompliant</td>
                </tr>    
           HTML;
        }




        $table = <<<HTML
                     <table class="table table-sm table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>S/N</th>
                        <th style="width: 30%;">Region</th>
                        <th>Measuring Instruments Inspected</th>
                        <th>Total Number Of Non Compliant Instruments </th>
                    </tr>
                </thead>
                <tbody>
                    $tr
                </tbody>
               </table>
            HTML;

        return $table;
    }




    public function downloadInspected($date1, $date2, $theMonth, $theYear, $title,$status)
    {
        $data = [
            'task' => 'Inspection',
            'created_at>=' => $date1,
            'created_at<=' => $date2,
            'MONTH(created_at)' => $theMonth,
            'YEAR(created_at)' => $theYear,



        ];


        $params = array_filter($data, fn ($param) => $param !== '00');



        $data2 = [
            'task' => 'Inspection',
            'CreatedAt>=' => $date1,
            'CreatedAt<=' => $date2,
            'MONTH(CreatedAt)' =>  $theMonth,
            'YEAR(CreatedAt)' => $theYear,

        ];
        $params2 = array_filter($data2, fn ($param) => $param !== '00');

        // Printer($status);

        // exit;
        $data['title'] = $title;
        $data['user'] = auth()->user();
        $data['report'] = $this->renderReport($this->instrumentsByCenters($params, $params2, '',ucfirst($status)));
        $financialYear = date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
        $data['financialYear'] = $financialYear;
        // $title = 'INSTRUMENT ANALYSIS REPORT OF FINANCIAL YEAR' . $financialYear . '_' . randomString();
        $orientation = 'P';

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: $orientation, view: 'ReportTemplates/inspectionReportPdf', data: $data, title: $title);
    }
}
