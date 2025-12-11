<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\AdminModel;
use App\Models\TargetModel;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Models\EstimatesModel;
use App\Libraries\ArrayLibrary;
use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Controllers\BaseController;
use App\Libraries\CommonTasksLibrary;
use App\Models\EstimateModel;
use App\Models\ReportModel;

class InstrumentReportController extends BaseController
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
    protected $reportModel;



    protected $prePackageCollection;
    protected $vehicleTankCollection;
    protected $lorriesCollection;
    protected $waterMeterCollection;
    protected $appRequest;
    protected $email;
    protected $token;
    protected $collectionCenter;
    protected $estimateModel;



    public function __construct()
    {
        $this->token = csrf_hash();
        $this->email = \Config\Services::email();
        $this->appRequest = service('request');
        helper(['format', 'form', 'array', 'regions', 'date', 'emailTemplate', 'image']);
        $this->commonTasks     = new CommonTasksLibrary;
        $this->session         = session();
        $this->reportModel    = new ReportModel();
        $this->profileModel    = new ProfileModel();
        $this->billModel      = new BillModel();
        $this->prePackageModel = new prePackageModel();

        $this->lorriesModel    = new LorriesModel();
        $this->vtcModel        = new VtcModel();
        $this->targetModel  = new TargetModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->user = auth()->user();
    }

    public function stamped()
    {
        $data['page'] = [
            'title' => 'Instruments Report',
            'heading' => 'Instruments Report',
        ];

        $params = [
            // 'users.collection_center' => $centerCode,
            // 'created_at>=' => financialYear()->startDate,
            // 'created_at<=' => financialYear()->endDate,
            'YEAR(created_at)' =>  date('Y'),
            'MONTH(created_at)' => date('m'),



        ];

        $params2 = [
            // 'users.collection_center' => $centerCode,
            // 'bill_items.CreatedAt>=' => financialYear()->startDate,
            // 'bill_items.CreatedAt<=' => financialYear()->endDate,
            'MONTH(bill_items.CreatedAt)' =>  date('m'),
            'YEAR(bill_items.CreatedAt)' =>  date('Y'),
            'Status' => 'Pass',






        ];
        $title = 'Stamped';

        $data['user'] = auth()->user();

        //   $sbl = $this->lorriesModel->sblCount($params);


        // $vtva = $this->vtcModel->vtvCount($params);
        // // 
        // // echo 'Current Memory Limit: ' . ini_get('memory_limit');
        // Printer(setting('Gfs.counterScale'));
        // echo 'from vtv model';
        // Printer($vtv1);
        // echo 'from Bill Items';
        // Printer($vtv);
        // exit;
        // $dates  = [
        //     // 'users.collection_center' => $centerCode,
        //     'createdAt>=' => financialYear()->startDate,
        //     'createdAt<=' => financialYear()->endDate,
        //     // 'status' => 'Pass'


        // ];

        // $x = $this->processInstrumentCount(instruments:$itemData,gfsCode:setting('Gfs.sbl'));

        // $instrumentsTarget = (new EstimatesModel())->getInstrumentEstimates($dates);
        // $estimate = (new ArrayLibrary($instrumentsTarget))->map(fn ($inst) => $inst->instruments)->reduce(fn ($x, $y) => $x + $y)->get();
        // $estimate =2300;
        // Printer($params2);
        // Printer($x);
        // exit;

        //  $waterMeter = $this->waterMeterModel->meterCount($params);

        $instruments =  $this->instrumentsByCenters($params, $params2, $title);

        // Printer($instruments);
        // exit;



        $currentMonthName = date('F');
        $currentYear = date('Y');
        $reportTitle = strtoupper($currentMonthName) . ' ' . $currentYear;

        $data['instruments'] =  $instruments;
        $data['dataTable'] = $this->renderReport($instruments);
        // $data['dataTable'] = '';
        $data['reportTitle'] = $reportTitle;
        return view('Pages/instrumentsReportStamped', $data);
    }



    public function filterInstruments()
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
                // 'created_at>=' => $month == '' ?  ($quarter ? $startDate : '') : '',
                // 'created_at<=' => $month == '' ? ($quarter ? $endDate : '') : '',
                'MONTH(created_at)' => $month,
                'YEAR(created_at)' => $startYear,
                'status' => 'Pass',




            ];

            $data2 = [
                // 'users.collection_center' => $centerCode,
                // 'bill_items.CreatedAt>=' => $month == '' ?  ($quarter ? $startDate : '') : '',
                // 'bill_items.CreatedAt<=' => $month == '' ? ($quarter ? $endDate : '') : '',
                'MONTH(bill_items.CreatedAt)' => $month,
                'YEAR(bill_items.CreatedAt)' => $startYear,
                'Status' => 'Pass',


            ];




            $params = array_filter($data, fn($param) => $param !== '' || $param != null);

            // return  $this->response->setJSON([
            //     'a' => setting('Gfs.automaticWeigher'),
            //     // 'data' => $params,
            //     // 'data2' => $params2,
            //     // 'instrumentsData' => $x,
            //     'token' => $this->token
            // ]);

            // exit;





            $params2 = array_filter($data2, fn($param) => $param !== '' || $param != null);

            $instrumentsData = $this->instrumentsByCenters($params, $params2, $title);


            // return  $this->response->setJSON([
            //     //'a' => setting('Gfs.automaticWeigher'),
            //     'data' => $params,
            //     'data2' => $params2,
            //     'instrumentsData' => $instrumentsData,
            //     'token' => $this->token
            // ]);

            // exit;

            $date1 = $month == '' ?  ($quarter ? $startDate : '00') : '00';
            $date2 = $month == '' ? ($quarter ? $endDate : '00') : '00';
            $theMonth =  $month ?? '00';
            $theYear =   $month != '' ? $startYear  : '00';
            $reportTitle = strtoupper($title);
            $link = base_url("downloadStampedInstruments/$theMonth/$theYear/$reportTitle");

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

    function processInstrumentCount($instruments, $gfsCode, $collectionCenter)
    {
        $collectedInstruments = array_filter($instruments, fn($instr) => $instr->GfsCode === $gfsCode && $instr->center === $collectionCenter);



        if (!empty($collectedInstruments)) {
            $quantity = (new ArrayLibrary($collectedInstruments))->map(fn($item) => $item->ItemQuantity == 0 ? 1 : $item->ItemQuantity)->reduce(fn($x, $y) => $x + $y)->get();
            $amount = (new ArrayLibrary($collectedInstruments))->map(fn($item) => (int)$item->BillItemAmt)->reduce(fn($x, $y) => $x + $y)->get();
        } else {
            $quantity = 0;
            $amount = 0;
        }

        return (object) [
            'quantity' => $quantity,
            'amount' => $amount,
        ];
    }

    public function instrumentsByCenters($params, $params2, $title)
    {

        try {

            $collectionCenters = $this->reportModel->getCollectionCenters();
            $centers = (new ArrayLibrary($collectionCenters))
                ->filter(fn($region) => $region->centerNumber != '0031')
                ->map(fn($cnt) => [$cnt->centerNumber, $cnt->centerName])->get();


            $instrumentData =  $this->reportModel->getInstrumentsCount($params2);



            $instruments = array_map(function ($center) use ($params, $instrumentData, $title) {

                // return $years;
                // exit;

                $centerCode = $center[0];
                $params['region'] =  $centerCode;
                $params2['center'] =  $centerCode;


                $counterScale = $this->processInstrumentCount($instrumentData, setting('Gfs.counterScale'), $centerCode);

                // $counterScale = 400;
                $platformScale = $this->processInstrumentCount($instrumentData, setting('Gfs.platformScale'), $centerCode);
                $springBalance = $this->processInstrumentCount($instrumentData, setting('Gfs.springBalance'), $centerCode);

                $weighBridge = $this->processInstrumentCount($instrumentData, setting('Gfs.weighBridge'), $centerCode);
                $weigher = $this->processInstrumentCount($instrumentData, setting('Gfs.weigher'), $centerCode);
                $automaticWeigher = $this->processInstrumentCount($instrumentData, setting('Gfs.automaticWeigher'), $centerCode);
                // $automaticFiller =$this->processInstrumentCount($instrumentData,setting('Gfs.balance'));
                $suspendedDigitalWare = $this->processInstrumentCount($instrumentData, setting('Gfs.suspendedDigitalWare'), $centerCode);
                $beamScale = $this->processInstrumentCount($instrumentData, setting('Gfs.beamScale'), $centerCode);
                $balance = $this->processInstrumentCount($instrumentData, setting('Gfs.balance'), $centerCode);
                $koroboi = $this->processInstrumentCount($instrumentData, setting('Gfs.koroboi'), $centerCode);
                $vibaba = $this->processInstrumentCount($instrumentData, setting('Gfs.vibaba'), $centerCode);
                $pishi = $this->processInstrumentCount($instrumentData, setting('Gfs.pishi'), $centerCode);
                $checkPump = $this->processInstrumentCount($instrumentData, setting('Gfs.checkPump'), $centerCode);
                $brimMeasure = $this->processInstrumentCount($instrumentData, setting('Gfs.brimMeasureSystem'), $centerCode);
                $meterRule = $this->processInstrumentCount($instrumentData, setting('Gfs.metreRule'), $centerCode);
                $tapeMeasure = $this->processInstrumentCount($instrumentData, setting('Gfs.tapeMeasure'), $centerCode);
                $weights = $this->processInstrumentCount($instrumentData, setting('Gfs.weights'), $centerCode);
                $fuelPump = $this->processInstrumentCount($instrumentData, setting('Gfs.fuelPump'), $centerCode);
                $flowMeter = $this->processInstrumentCount($instrumentData, setting('Gfs.flowMeter'), $centerCode);
                // $scalePlusWeights =$this->processInstrumentCount($instrumentData,setting('Gfs.balance'),$centerCode);
                $wagonTank = $this->processInstrumentCount($instrumentData, setting('Gfs.wagonTank'), $centerCode);
                $bulkStorageTank = $this->processInstrumentCount($instrumentData, setting('Gfs.bst'), $centerCode);
                $fixedStorageTank = $this->processInstrumentCount($instrumentData, setting('Gfs.fst'), $centerCode);
                $others = $this->processInstrumentCount($instrumentData, setting('Gfs.otherMeasuresOfLength'), $centerCode);


                $vtv = $this->processInstrumentCount($instrumentData, setting('Gfs.vtv'), $centerCode);
                $sbl = $this->processInstrumentCount($instrumentData, setting('Gfs.sbl'), $centerCode);

                // $vtv = $this->vtcModel->vtvCount($params);
                // $sbl = $this->lorriesModel->sblCount($params);
                unset($params['PaymentStatus'], $params['status']);
                $params['decision'] = 'PASS';
                $waterMeter = $this->waterMeterModel->meterCount($params);


                // return $fuelPump;
                // exit;



                // $targetParams = $this->targetModel->getTargets([
                //     'region' => $centerCode,
                //     'DATE(createdAt)>=' => financialYear()->startDate,
                //     'DATE(createdAt)<=' => financialYear()->endDate,

                // ]);


                $month = date('m');
                $year = date('Y');

              

                $month = $params['MONTH(created_at)'];
                $year = $params['YEAR(created_at)'];

                $filter = [
                    'region' => $centerCode,
                    'month' => $month,
                    'year' => $year,
                ];
                ///  $estimate = (new EstimatesModel())->getEstimate($filter)->instruments ?? 0;

                $instrumentsTarget = (new EstimatesModel())->getInstrumentEstimates($filter);
                $estimate = (new ArrayLibrary($instrumentsTarget))->map(fn($inst) => $inst->instruments)->reduce(fn($x, $y) => $x + $y)->get();


                return (object) [
                    'center' => $center[0],
                    'title' => $title,


                    'counterScale' => $counterScale->quantity,
                    // 'scalePlusWeights' => $scalePlusWeights->quantity,
                    'platformScale' => $platformScale->quantity,
                    'springBalance' => $springBalance->quantity,


                    'weighBridge' => $weighBridge->quantity,
                    'weigher' => $weigher->quantity,
                    'automaticWeigher' => $automaticWeigher->quantity,
                    /// 'automaticFiller' => $automaticFiller->quantity,
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
                    'actual' => $vtv->quantity + $waterMeter->quantity + $counterScale->quantity  + $platformScale->quantity + $springBalance->quantity  + $weighBridge->quantity + $weigher->quantity + $automaticWeigher->quantity + $beamScale->quantity + $balance->quantity + $suspendedDigitalWare->quantity + $koroboi->quantity + $vibaba->quantity + $pishi->quantity + $checkPump->quantity + $brimMeasure->quantity + $meterRule->quantity + $tapeMeasure->quantity + $weights->quantity + $fuelPump->quantity + $flowMeter->quantity + $wagonTank->quantity + $bulkStorageTank->quantity + $fixedStorageTank->quantity + $others->quantity,
                    'estimate' => $estimate ?? 0

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
        //  $totalScalePlusWeights = 0;
        $totalPlatformScale = 0;
        $totalSpringBalance = 0;

        $totalWeighBridge = 0;
        $totalWeigher = 0;
        $totalAutomaticWeigher = 0;
        // $totalAutomaticFiller = 0;
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
        $totalActual = 0;
        $totalEstimate = 0;
        $totalVariance = 0;

        $tr = '';

        // Iterate through instruments array to calculate totals
        foreach ($instruments as $instrument) {
            $totalCounterScale += $instrument->counterScale;
            //  $totalScalePlusWeights += $instrument->scalePlusWeights;
            $totalPlatformScale += $instrument->platformScale;
            $totalSpringBalance += $instrument->springBalance;
            $totalWeighBridge += $instrument->weighBridge;
            $totalWeigher += $instrument->weigher;
            $totalAutomaticWeigher += $instrument->automaticWeigher;
            // $totalAutomaticFiller += $instrument->automaticFiller;
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
            $totalActual += $instrument->actual;
            $totalEstimate += $instrument->estimate;
            $totalVariance += $instrument->actual - $instrument->estimate;
            $percent = ceil($instrument->estimate > 0 && $instrument->actual > 0 ? ($instrument->actual - $instrument->estimate) / $instrument->estimate * 100  : 0) . '%';
            $percentFooter = ceil($totalEstimate > 0 && $totalActual > 0 ? ($totalActual - $totalEstimate) / $totalEstimate * 100  : 0) . '%';
            $variance = $instrument->actual - $instrument->estimate;
            $tr .= <<<HTML
                    <tr>
                        <td>$instrument->region </td>
                        <td>$instrument->counterScale </td>
                     
                        <td>$instrument->platformScale </td>
                        <td>$instrument->springBalance </td>
                        
                        <td>$instrument->weighBridge </td>
                        <td>$instrument->weigher </td>
                        <td>$instrument->automaticWeigher </td>
                      
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
                        <td>$instrument->actual </td>
                        <td>$instrument->estimate </td>
                        <td>$variance </td>
                        <td>$percent</td>
                            </tr>                     
                HTML;
        }


        $table = <<<HTML
                      <table class="table table-sm table-bordered table-hover" id="instrumentsTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th colspan="11" class="text-center">WEIGHT MEASUREMENT</th>
                            <th colspan="14" class="text-center">MEASURE OF CAPACITY</th>
                            <th colspan="3" class="text-center">MEASURE OF LENGTH</th>
                            <th colspan="4" class="text-center"></th>
                        </tr>
                        <tr class="thead-dark">
                            <th>Region</th>
                            <th>C/S</th>
                         
                            <th>P/S</th>
                            <th>S/B</th>
                          
                            <th>W/b</th>
                            <th>Ax/W</th>
                            <th>Au/W</th>
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
                            <th>Actual</th>
                            <th>Estimate</th>
                            <th>variance</th>
                            <th>Percentage</th>

                        </tr>
                    </thead>
                    <tbody>
                       $tr
                       <tr style="font-weight: bold">
                            <th>Total</th>
                            <th>$totalCounterScale </th>
                            
                            <th>$totalPlatformScale </th>
                            <th>$totalSpringBalance </th>
                            
                            <th>$totalWeighBridge </th>
                            <th>$totalWeigher </th>
                            <th>$totalAutomaticWeigher </th>
                           
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
                            <th>$totalActual </th>
                            <th>$totalEstimate </th>
                            <th>$totalVariance </th>
                            <th>$percentFooter</th>
                        </tr>
                    </tbody>
                   
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

        $centers = array_map(fn($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->reportModel->getCollectionCenters());
        $instruments = array_map(function ($center) {

            $centerCode = $center[0];

            $params = [
                'region' => $centerCode,

            ];



            $instruments = $this->targetModel->getTargets($params);
            $target = (new ArrayLibrary($instruments))->map(fn($inst) => $inst->quantity)->reduce(fn($x, $y) => $x + $y)->get();


            return (object) [
                'region' => str_replace('Wakala Wa Vipimo', '', $center[1]),
                'center' => $center[0],
                'instruments' => number_format($target) ?? 0,


            ];
        }, $centers);


        return $instruments;
    }


    public function downloadStampedInstruments($theMonth, $theYear, $title)
    {
        $data = [
            // 'users.collection_center' => $centerCode,
            // 'created_at>=' => $date1,
            // 'created_at<=' => $date2,
            'MONTH(created_at)' => $theMonth,
            'YEAR(created_at)' => $theYear,
            'status' => 'Pass'



        ];


        $params = array_filter($data, fn($param) => $param !== '00');



        $data2 = [
            // 'users.collection_center' => $centerCode,
            // 'bill_items.CreatedAt>=' => $date1,
            // 'bill_items.CreatedAt<=' => $date2,
            'MONTH(bill_items.CreatedAt)' =>  $theMonth,
            'YEAR(bill_items.CreatedAt)' => $theYear,
            'Status' => 'Pass'

        ];
        $params2 = array_filter($data2, fn($param) => $param !== '00');

        // Printer($params);

        // exit;
        $data['title'] = $title;
        $data['user'] = auth()->user();
        $data['report'] = $this->renderReport($this->instrumentsByCenters($params, $params2, ''));
        $financialYear = date('Y', strtotime(financialYear()->startDate)) . '/' . date('Y', strtotime(financialYear()->endDate));
        $data['financialYear'] = $financialYear;
        // $title = 'INSTRUMENT ANALYSIS REPORT OF FINANCIAL YEAR' . $financialYear . '_' . randomString();
        $orientation = 'L';

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: $orientation, view: 'ReportTemplates/stampedInstrumentsReportPdf', data: $data, title: $title);
    }
}
