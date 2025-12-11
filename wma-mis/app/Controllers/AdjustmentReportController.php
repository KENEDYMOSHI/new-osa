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


class AdjustmentReportController extends BaseController
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



    protected $prePackageCollection;
    protected $vehicleTankCollection;
    protected $lorriesCollection;


    protected $waterMeterCollection;
   
    protected $email;
    protected $token;
    protected $collectionCenter;



    public function __construct()
    {
        $this->token = csrf_hash();
        $this->email = \Config\Services::email();
       
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

    public function adjusted()
    {
        $data['page'] = [
            'title' => 'Adjustment Report',
            'heading' => 'Adjustment Report',
        ];

        $params = [
            // 'users.collection_center' => $centerCode,
            'created_at>=' => financialYear()->startDate,
            'created_at<=' => financialYear()->endDate,
            'status' => 'Adjusted'


        ];

        $params2 = [
            // 'users.collection_center' => $centerCode,
            'bill_items.CreatedAt>=' => financialYear()->startDate,
            'bill_items.CreatedAt<=' => financialYear()->endDate,
            'Status' => 'Adjusted'




        ];
        $title = 'Adjusted';

        $data['user'] = auth()->user();
        $instruments =  $this->instrumentsByCenters($params, $params2, $title);

        // Printer($instruments);
        // exit;
        $data['instruments'] =  $instruments;
        $data['dataTable'] = $this->renderReport($instruments);
        // $data['dataTable'] = '';
        $data['financialYear'] = date('Y', strtotime(financialYear()->startDate)) . '|' . date('Y', strtotime(financialYear()->endDate));
        return view('Pages/instrumentsReportAdjusted', $data);
    }

    public function filterAdjustedInstruments()
    {

        try {
            $year = $this->getVariable('year') ?? date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
            $month = $this->getVariable('month');
            $quarter = $this->getVariable('quarter') ?? 'Annually';
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
                $title =  date("F", mktime(0, 0, 0, $month, 1)) . " " . $startYear . " Instruments Report";
            } elseif ($quarter != '') {

                $title =  "$period Instruments Report Of Financial Year " . $startYear . '|' . ($endYear) . ' ';
            } else {
                $title =  "$period Instruments  Report Of Financial Year " . date('Y', strtotime($initialDate)) . '|' . date('Y', strtotime($finalDate)) . ' ';
            }


            $data = [
                // 'users.collection_center' => $centerCode,
                'created_at>=' => $month == '' ?  ($quarter ? $startDate : '') : '',
                'created_at<=' => $month == '' ? ($quarter ? $endDate : '') : '',
                'MONTH(created_at)' => $month ?? '',
                'YEAR(created_at)' => $month != '' ? $startYear  : '',
                'status' => 'Adjusted'



            ];

            $data2 = [
                // 'users.collection_center' => $centerCode,
                'bill_items.CreatedAt>=' => $month == '' ?  ($quarter ? $startDate : '') : '',
                'bill_items.CreatedAt<=' => $month == '' ? ($quarter ? $endDate : '') : '',
                'MONTH(bill_items.CreatedAt)' => $month ?? '',
                'YEAR(bill_items.CreatedAt)' => $month != '' ? $startYear  : '',
                'Status' => 'Adjusted'

            ];




            $params = array_filter($data, fn ($param) => $param !== '' || $param != null);

            // return $params1;
            // exit;





            $params2 = array_filter($data2, fn ($param) => $param !== '' || $param != null);

            $instrumentsData = $this->instrumentsByCenters($params, $params2, $title);
            //    $x = $this->billModel->getInstrumentsCount($params2, setting('Gfs.automaticWeigher'));

            // return  $this->response->setJSON([
            //     'a' => setting('Gfs.automaticWeigher'),
            //     'data' => $params,
            //     'data2' => $params2,
            //     'instrumentsData' => $x,
            //     'token' => $this->token
            // ]);

            // exit;

            $date1 = $month == '' ?  ($quarter ? $startDate : '00') : '00';
            $date2 = $month == '' ? ($quarter ? $endDate : '00') : '00';
            $theMonth =  $month ?? '00';
            $theYear =   $month != '' ? $startYear  : '00';
            $reportTitle = strtoupper($title);
            $link = base_url("downloadAdjustedInstruments/$date1/$date2/$theMonth/$theYear/$reportTitle");

            return  $this->response->setJSON([
                'status' => 1,
                'report' => $this->renderReport($instrumentsData),
                'token' => $this->token,
                'link' => $link,
                'title' => $reportTitle,
                'data2' => $data2,

            ]);
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }

    public function instrumentsByCenters($params, $params2, $title)
    {

        try {


            $centers = array_map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->billModel->getCollectionCenters());
            $instruments = array_map(function ($center) use ($params, $params2, $title) {

                // return $years;
                // exit;

                $centerCode = $center[0];
                $params['region'] =  $centerCode;
                $params2['CollectionCenter'] =  $centerCode;


                $counterScale = $this->billModel->getInstrumentsCount($params2, setting('Gfs.counterScale'));
                // $counterScale = 400;
                $platformScale = $this->billModel->getInstrumentsCount($params2, setting('Gfs.platformScale'));
                $springBalance = $this->billModel->getInstrumentsCount($params2, setting('Gfs.springBalance'));

                $weighBridge = $this->billModel->getInstrumentsCount($params2, 'Gfs.weighBridge');
                $weigher = $this->billModel->getInstrumentsCount($params2, setting('Gfs.weigher'));
                $automaticWeigher = $this->billModel->getInstrumentsCount($params2, setting('Gfs.automaticWeigher'));
                $automaticFiller = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $suspendedDigitalWare = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $beamScale = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $balance = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $koroboi = $this->billModel->getInstrumentsCount($params2, setting('Gfs.koroboi'));
                $vibaba = $this->billModel->getInstrumentsCount($params2, setting('Gfs.vibaba'));
                $pishi = $this->billModel->getInstrumentsCount($params2, setting('Gfs.pishi'));
                $checkPump = $this->billModel->getInstrumentsCount($params2, setting('Gfs.checkPump'));
                $brimMeasure = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $meterRule = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $tapeMeasure = $this->billModel->getInstrumentsCount($params2, setting('Gfs.tapeMeasure'));
                $weights = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $fuelPump = $this->billModel->getInstrumentsCount($params2, setting('Gfs.fuelPump'));
                $flowMeter = $this->billModel->getInstrumentsCount($params2, setting('Gfs.flowMeter'));
                $scalePlusWeights = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));
                $wagonTank = $this->billModel->getInstrumentsCount($params2, setting('Gfs.wagonTank'));
                $bulkStorageTank = $this->billModel->getInstrumentsCount($params2, setting('Gfs.bst'));
                $fixedStorageTank = $this->billModel->getInstrumentsCount($params2, setting('Gfs.fst'));
                $others = $this->billModel->getInstrumentsCount($params2, setting('Gfs.balance'));

                $vtv = $this->vtcModel->vtvCount($params);
                $sbl = $this->lorriesModel->sblCount($params);
                $waterMeter = $this->waterMeterModel->meterCount($params);



                // $targetParams = $this->targetModel->getTargets([
                //     'region' => $centerCode,
                //     'created_at>=' => financialYear()->startDate,
                //     'created_at<=' => financialYear()->endDate,

                // ]);

              
               


                return (object) [
                    'center' => $center[0],
                    'title' => $title,

                    'counterScale' => $counterScale->quantity,
                    'scalePlusWeights' => $scalePlusWeights->quantity,
                    'platformScale' => $platformScale->quantity,
                    'springBalance' => $springBalance->quantity,


                    'weighBridge' => $weighBridge->quantity,
                    'weigher' => $weigher->quantity,
                    'automaticWeigher' => $automaticWeigher->quantity,
                    'automaticFiller' => $automaticFiller->quantity,
                    'beamScale' => $beamScale->quantity,
                    'suspendedDigitalWare' => $suspendedDigitalWare->quantity,


                    'balance' => $balance->quantity,
                    'koroboi' => $koroboi->quantity,
                    'vibaba' => $vibaba->quantity,
                    'pishi' => $pishi->quantity,

                    'checkPump' => $checkPump->quantity,
                    'brimMeasure' => $brimMeasure->quantity,
                    'meterRule' => $meterRule->quantity,
                    'tapeMeasure' => $tapeMeasure->quantity,
                    'o' => $others->quantity,

                    'weights' => $weights->quantity,
                    'fuelPump' => $fuelPump->quantity,
                    'flowMeter' => $flowMeter->quantity,




                    'wagonTank' => $wagonTank->quantity,
                    'bulkStorageTank' => $bulkStorageTank->quantity,
                    'fixedStorageTank' => $fixedStorageTank->quantity,


                    'vtv' => $vtv->quantity,
                    'sbl' => $sbl->quantity,
                    'waterMeter' => $waterMeter->quantity,
                    'region' => str_replace('Wakala Wa Vipimo', '', $center[1]),
                    'totalAmount' => $vtv->amount + $sbl->amount + $waterMeter->amount + $counterScale->amount + $scalePlusWeights->amount + $platformScale->amount + $springBalance->amount  + $weighBridge->amount + $weigher->amount + $automaticWeigher->amount +  $automaticFiller->amount + $beamScale->amount + $balance->amount + $suspendedDigitalWare->amount + $koroboi->amount + $vibaba->amount + $pishi->amount + $checkPump->amount + $brimMeasure->amount + $meterRule->amount + $tapeMeasure->amount + $weights->amount + $fuelPump->amount + $flowMeter->amount + $wagonTank->amount + $bulkStorageTank->amount + $fixedStorageTank->amount + $others->amount,
                  

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

    public function instrumentsTarget()
    {
        if ($this->user->inGroup('officer', 'manager')) {
            return redirect()->to('dashboard');
        }

        $data['page'] = [
            'title' => 'Instruments Targets',
            'heading' => 'Instruments Targets',
        ];

        $data['user'] = $this->user;
        $estimates = $this->getTargets();
        $data['targets'] =  $this->renderEstimates($estimates);
        return view('Pages/instrumentsTarget', $data);
    }



    public function renderReport($instruments)
    {


        // Initialize total variables for each column
        $totalCounterScale = 0;
        $totalScalePlusWeights = 0;
        $totalPlatformScale = 0;
        $totalSpringBalance = 0;

        $totalWeighBridge = 0;
        $totalWeigher = 0;
        $totalAutomaticWeigher = 0;
        $totalAutomaticFiller = 0;
        $totalBeamScale = 0;
        $totalBalance = 0;
        $totalSuspendedDigitalWare = 0;
        $totalKoroboi = 0;
        $totalVibaba = 0;
        $totalPishi = 0;
        $totalCheckPump = 0;
        $totalBrimMeasure = 0;
        $totalFuelPump = 0;
        $totalFlowMeter = 0;
        $totalWagonTank = 0;
        $totalWeights = 0;
        $totalBulkStorageTank = 0;
        $totalFixedStorageTank = 0;
        $totalVtv = 0;
        $totalSbl = 0;
        $totalWaterMeter = 0;
        $totalMeterRule = 0;
        $totalTapeMeasure = 0;
        $totalO = 0;
        $totalAmount = 0;
        

        $tr = '';

        // Iterate through instruments array to calculate totals
        foreach ($instruments as $instrument) {
            $totalCounterScale += $instrument->counterScale;
            $totalScalePlusWeights += $instrument->scalePlusWeights;
            $totalPlatformScale += $instrument->platformScale;
            $totalSpringBalance += $instrument->springBalance;
            $totalWeighBridge += $instrument->weighBridge;
            $totalWeigher += $instrument->weigher;
            $totalAutomaticWeigher += $instrument->automaticWeigher;
            $totalAutomaticFiller += $instrument->automaticFiller;
            $totalBeamScale += $instrument->beamScale;
            $totalSuspendedDigitalWare += $instrument->suspendedDigitalWare;
            $totalBalance += $instrument->balance;
            $totalKoroboi += $instrument->koroboi;
            $totalVibaba += $instrument->vibaba;
            $totalPishi += $instrument->pishi;
            $totalCheckPump += $instrument->checkPump;
            $totalBrimMeasure += $instrument->brimMeasure;
            $totalFuelPump += $instrument->fuelPump;
            $totalFlowMeter += $instrument->flowMeter;
            $totalWagonTank += $instrument->wagonTank;
            $totalWeights += $instrument->weights;
            $totalBulkStorageTank += $instrument->bulkStorageTank;
            $totalFixedStorageTank += $instrument->fixedStorageTank;
            $totalVtv += $instrument->vtv;
            $totalSbl += $instrument->sbl;
            $totalWaterMeter += $instrument->waterMeter;
            $totalMeterRule += $instrument->meterRule;
            $totalTapeMeasure += $instrument->tapeMeasure;
            $totalO += $instrument->o;
            $totalAmount += $instrument->totalAmount;

            $total = number_format($totalAmount);
            
            $amt = number_format($instrument->totalAmount);
         
           
            $tr .= <<<HTML
                    <tr>
                        <td>$instrument->region </td>
                        <td>$instrument->counterScale </td>
                        <td>$instrument->scalePlusWeights </td>
                        <td>$instrument->platformScale </td>
                        <td>$instrument->springBalance </td>
                        
                        <td>$instrument->weighBridge </td>
                        <td>$instrument->weigher </td>
                        <td>$instrument->automaticWeigher </td>
                        <td>$instrument->automaticFiller </td>
                        <td>$instrument->beamScale </td>
                        <td>$instrument->balance </td>
                        <td>$instrument->suspendedDigitalWare </td>
                        <td>$instrument->koroboi </td>
                        <td>$instrument->vibaba </td>
                        <td>$instrument->pishi </td>
                        <td>$instrument->checkPump </td>
                        <td>$instrument->brimMeasure </td>
                        <td>$instrument->fuelPump </td>
                        <td>$instrument->flowMeter </td>
                        <td>$instrument->wagonTank </td>
                        <td>$instrument->weights </td>
                        <td>$instrument->bulkStorageTank </td>
                        <td>$instrument->fixedStorageTank </td>
                        <td>$instrument->vtv </td>
                        <td>$instrument->sbl </td>
                        <td>$instrument->waterMeter </td>
                        <td>$instrument->meterRule </td>
                        <td>$instrument->tapeMeasure </td>
                        <td>$instrument->o </td>
                        <td>$amt </td>
                       
                            </tr>                     
                HTML;
        }


        $table = <<<HTML
                      <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th colspan="11" class="text-center">WEIGHT MEASUREMENT</th>
                            <th colspan="14" class="text-center">MEASURE OF CAPACITY</th>
                            <th colspan="3" class="text-center">MEASURE OF LENGTH</th>
                            <th colspan="4" class="text-center">FEES</th>
                        </tr>
                        <tr class="thead-dark">
                            <th>Region</th>
                            <th>C/S</th>
                            <th>C/S + WT</th>
                            <th>P/S</th>
                            <th>S/B</th>
                          
                            <th>W/b</th>
                            <th>Ax/W</th>
                            <th>Au/W</th>
                            <th>Au/F</th>
                            <th>B/s</th>
                            <th>Bal</th>
                            <th>SDW</th>

                            <th>Kor</th>
                            <th>Vib</th>
                            <th>Pis</th>
                            <th>Ch/p</th>
                            <th>Brim</th>
                            <th>FP</th>
                            <th>F/M</th>
                            <th>WGT</th>
                            <th>WT</th>
                            <th>BST</th>
                            <th>FST</th>
                            <th>VTV</th>
                            <th>SBL</th>
                            <th>W/M</th>
                            <th>MR</th>
                            <th>TM</th>
                            <th>O</th>
                            <th>Amount</th>
                           

                        </tr>
                    </thead>
                    <tbody>
                       $tr
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>$totalCounterScale </th>
                            <th>$totalScalePlusWeights </th>
                            <th>$totalPlatformScale </th>
                            <th>$totalSpringBalance </th>
                            
                            <th>$totalWeighBridge </th>
                            <th>$totalWeigher </th>
                            <th>$totalAutomaticWeigher </th>
                            <th>$totalAutomaticFiller </th>
                            <th>$totalBeamScale </th>
                            <th>$totalBalance </th>
                            <th>$totalSuspendedDigitalWare </th>
                            <th>$totalKoroboi </th>
                            <th>$totalVibaba </th>
                            <th>$totalPishi </th>
                            <th>$totalCheckPump </th>
                            <th>$totalBrimMeasure </th>
                            <th>$totalFuelPump </th>
                            <th>$totalFlowMeter </th>
                            <th>$totalWagonTank </th>
                            <th>$totalWeights </th>
                            <th>$totalBulkStorageTank </th>
                            <th>$totalFixedStorageTank </th>
                            <th>$totalVtv </th>
                            <th>$totalSbl </th>
                            <th>$totalWaterMeter </th>
                            <th>$totalMeterRule </th>
                            <th>$totalTapeMeasure </th>
                            <th>$totalO </th>
                            <th>$total </th>
                           
                   
                        </tr>
                    </tfoot>
                </table>
            HTML;

        return $table;
    }

    //=====================================
    public function addInstrumentTarget()
    {
        try {
            $data = [
                'region' => $this->getVariable('region'),
                'activity' => $this->getVariable('activity'),
                'quantity' => $this->getVariable('quantity'),
                'unique_id' => $this->user->unique_id,
            ];

            if ($this->targetModel->addTarget($data)) {
                $estimates = $this->getTargets();

                $response = [
                    'status' => 1,
                    'estimates' => $this->renderEstimates($estimates),
                    'msg' => 'Target Added',
                    'token' => $this->token,
                ];
            } else {
                $response = [
                    'status' => 0,
                    'estimates' => '',
                    'msg' => 'Something Went Wrong',
                    'token' => $this->token,
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'estimates' => '',
                'msg' => $th->getMessage(),
                'token' => $this->token,
            ];
        }

        return $this->response->setJSON($response);
    }


    public function renderEstimates($estimates)
    {
        $tr = '';
        foreach ($estimates as $data) {
            $tr .= <<<HTML
               <tr>
                  <td>$data->region </td>
                  <td>$data->instruments </td> 
               </tr>    
            HTML;
        }
        $html = <<<HTML
                 <table class="table table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>Region</th>
                        <th>Estimate</th>

                    </tr>
                </thead>
                <tbody>
                  $tr
                </tbody>
            </table>     
        HTML;

        return $html;
    }


    public function getTargets()
    {

        $centers = array_map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->billModel->getCollectionCenters());
        $instruments = array_map(function ($center) {

            $centerCode = $center[0];

            $params = [
                'region' => $centerCode,

            ];



            $instruments = $this->targetModel->getTargets($params);
            $target = (new ArrayLibrary($instruments))->map(fn ($inst) => $inst->quantity)->reduce(fn ($x, $y) => $x + $y)->get();


            return (object) [
                'region' => str_replace('Wakala Wa Vipimo', '', $center[1]),
                'center' => $center[0],
                'instruments' => number_format($target) ?? 0,


            ];
        }, $centers);


        return $instruments;
    }


    public function downloadAdjustedInstruments($date1, $date2, $theMonth, $theYear, $title)
    {
        $data = [
            // 'users.collection_center' => $centerCode,
            'created_at>=' => $date1,
            'created_at<=' => $date2,
            'MONTH(created_at)' => $theMonth,
            'YEAR(created_at)' => $theYear,
            'status' => 'Adjusted'



        ];


        $params = array_filter($data, fn ($param) => $param !== '00');



        $data2 = [
            // 'users.collection_center' => $centerCode,
            'bill_items.CreatedAt>=' => $date1,
            'bill_items.CreatedAt<=' => $date2,
            'MONTH(bill_items.CreatedAt)' =>  $theMonth,
            'YEAR(bill_items.CreatedAt)' => $theYear,
            'Status' => 'Adjusted'

        ];
        $params2 = array_filter($data2, fn ($param) => $param !== '00');
        $instruments  = ($this->instrumentsByCenters($params, $params2, ''));

        // Printer($instruments);

        // exit;
        $data['title'] = $title;
        $data['user'] = auth()->user();
        $data['report'] = $this->renderReport($instruments);
        $financialYear = date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
        $data['financialYear'] = $financialYear;
        // $title = 'INSTRUMENT ANALYSIS REPORT OF FINANCIAL YEAR' . $financialYear . '_' . randomString();
        $orientation = 'L';

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: $orientation, view: 'ReportTemplates/adjustedInstrumentsReportPdf', data: $data, title: $title);
    }
}
