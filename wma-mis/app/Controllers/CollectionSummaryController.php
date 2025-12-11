<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\AdminModel;
use App\Models\scaleModel;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Models\DirectorModel;
use App\Models\FuelPumpModel;
use App\Models\FlowMeterModel;
use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Controllers\BaseController;
use App\Libraries\PrePackageLibrary;
use App\Models\BulkStorageTankModel;
use App\Libraries\CommonTasksLibrary;
use App\Models\FixedStorageTankModel;
use PHPUnit\Util\Printer;

class CollectionSummaryController extends BaseController
{

    protected $session;
    protected $profileModel;

    protected $billModel;

    protected $prePackageModel;
    protected $DirectorModel;
    protected $lorriesModel;
    protected $vtcModel;

    protected $flowMeterModel;
    protected $waterMeterModel;
    protected $commonTasks;
    protected $admin;
    protected $adminModel;



    protected $prePackageCollection;
    protected $vehicleTankCollection;
    protected $lorriesCollection;


    protected $waterMeterCollection;
    protected $appRequest;
    protected $email;
    protected $token;
    protected $collectionCenter;
    protected $user;



    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->token = csrf_hash();
        $this->email = \Config\Services::email();
        $this->appRequest = service('request');
        helper(['format', 'bill', 'form', 'array', 'regions', 'date', 'emailTemplate', 'image']);
        $this->commonTasks     = new CommonTasksLibrary;
        $this->session         = session();
        $this->adminModel    = new AdminModel();
        $this->profileModel    = new ProfileModel();
        $this->billModel      = new BillModel();
        $this->prePackageModel = new prePackageModel();

        $this->lorriesModel    = new LorriesModel();
        $this->vtcModel        = new VtcModel();
        $this->flowMeterModel  = new FlowMeterModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->user = auth()->user();
    }

    public function collectionByCenters()
    {


        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.CreatedAt >=' => financialYear()->startDate,
            'wma_bill.CreatedAt <=' => financialYear()->endDate,
            'wma_bill.IsCancelled' => 'No',
        ];


        $dataSource =  $this->billModel->getReportData($params, '', []);


        $centers = array_map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->billModel->getCollectionCenters());

        $collections = array_map(function ($center) use ($dataSource) {


            $centerCode = $center[0];

            $centerData = array_filter($dataSource, function ($data) use ($centerCode) {
                return $data->CollectionCenter === $centerCode;
            });

            $totalAmount = array_reduce($centerData, function ($carry, $data) {
                return $carry + $data->amount;
            }, 0);

          

            $totalPaid = array_reduce($centerData, function ($carry, $data) {
                return $data->PaymentStatus === 'Paid'   ? $carry + $data->amount : $carry;
            }, 0);

            $totalPending = array_reduce($centerData, function ($carry, $data) {
                return $data->PaymentStatus === 'Pending' ? $carry + $data->amount : $carry;
            }, 0);

           
            $totalPartial = array_reduce($centerData, function ($carry, $data) {
                return $data->PaymentStatus === 'Partial' ? $carry + ($data->amount - $data->PaidAmount) : $carry;
            }, 0);

            $totalPartialPaid = array_reduce($centerData, function ($carry, $data) {
                return $data->PaymentStatus === 'Partial' ? $carry + ($data->PaidAmount) : $carry;
            }, 0);

            return (object) [
                'center' => $center[0],
                'centerName' => $center[1],
                'total' => $totalAmount,
                'paid' => $totalPaid + $totalPartialPaid,
                'pending' => $totalPending,
                'partial' => $totalPartial,
            ];
        }, $centers);


        return array_filter($collections, fn ($data) => $data->total != 0);
    }

    public function activitiesSummary($center, $action)
    {

        $centerName = str_replace('Wakala Wa Vipimo', '', wmaCenter($center)->centerName);

        $data['page'] = [
            'title' => 'Collection ' . $centerName,
            'heading' => 'Collection: ' . $centerName,
        ];
        $data['user'] = auth()->user();




        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.CreatedAt >=' => financialYear()->startDate,
            'wma_bill.CreatedAt <=' => financialYear()->endDate,
            'CollectionCenter' => $center,
            'wma_bill.IsCancelled' => 'No',
        ];


        $dataSource =  $this->billModel->getReportData($params, '', []);


        // Printer($dataSource);
        // exit;

        $activities = [
            [
                'code' => setting('Gfs.vtv'),
                'name' => 'Vehicle Tank Verification',
                'url' => 'listVehicleTanks/' . $center
            ],

            [
                'code' => setting('Gfs.weighBridge'),
                'name' => 'Weigh Bridge',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],

            [
                'code' => setting('Gfs.fst'),
                'name' => 'Fixed Storage Tank',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],

            [
                'code' => setting('Gfs.bst'),
                'name' => 'Bulk Storage Tank',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.prePackages'),
                'name' => 'Pre Packages',
                'url' => 'registeredPrepackages', 'url' => 'registeredPrepackages'
            ],
            [
                'code' => setting('Gfs.wagonTank'),
                'name' => 'Wagon Tank',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.fuelPump'),
                'name' => 'Fuel Pump',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.cngFillingStation'),
                'name' => 'CNG Filling Station',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.flowMeter'),
                'name' => 'Flow Meter',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.checkPump'),
                'name' => 'Check Pump',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.waterMeter'),
                'name' => 'Water Meter',
                'url' => 'WaterMeterList/' . $center
            ],
            [
                'code' => setting('Gfs.metrological'),
                'name' => 'Metrological',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.pressureGauges'),
                'name' => 'Pressure Gauges',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.provingTank'),
                'name' => 'Proving Tank',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.taxiMeter'),
                'name' => 'Taxi Meter',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.metreRule'),
                'name' => 'Metre Rule',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.tapeMeasure'),
                'name' => 'Tape Measure',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.measuresOfLength'),
                'name' => 'Measures of Length',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.brimMeasureSystem'),
                'name' => 'Brim Measure System',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.steelYard'),
                'name' => 'Steel Yard', 'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.suspendedDigitalWare'), 'name' => 'Suspended Digital Ware',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.counterScale'),
                'name' => 'Counter Scale', 'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.platformScale'),
                'name' => 'Platform Scale',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.springBalance'),
                'name' => 'Spring Balance',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.balance'),
                'name' => 'Balance',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.koroboi'),
                'name' => 'Koroboi',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.vibaba'),
                'name' => 'Vibaba',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.pishi'),
                'name' => 'Pishi',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.weigher'),
                'name' => 'Weigher',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.automaticWeigher'),
                'name' => 'Automatic Weigher',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.beamScale'),
                'name' => 'Beam Scale',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.sbl'),
                'name' => 'Sand & Ballast Lorries',
                'url' => 'listLorries/' . $center
            ],
            [
                'code' => setting('Gfs.electricityMeter'),
                'name' => 'Electricity Meter',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.otherMeasuringInstrument'),
                'name' => 'Other Measuring Instrument',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.otherMeasuresOfLength'),
                'name' => 'Other Measures of Length',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.domesticGasMeter'),
                'name' => 'Domestic Gas Meter',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.weights'),
                'name' => 'Weights',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.miscellaneousReceipts'),
                'name' => 'Miscellaneous Receipts',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
            [
                'code' => setting('Gfs.fine'),
                'name' => 'Fine & Penalty',
                'url' => 'activitiesSummary/' . $center . '/view'
            ],
        ];



        usort($activities, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });


        $results = array_map(function ($activityCode) use ($dataSource) {

            $activity = $activityCode['code'];
            $name = $activityCode['name'];
            $url = $activityCode['url'];
            $totalAmount = array_reduce($dataSource, function ($carry, $data) use ($activity) {

                return $data->Activity === $activity ? $carry + $data->amount : $carry;
            }, 0);

            $totalPaid = array_reduce($dataSource, function ($carry, $data) use ($activity) {
                return $data->Activity === $activity && $data->PaymentStatus === 'Paid' ? $carry + $data->amount : $carry;
            }, 0);

            $totalPending = array_reduce($dataSource, function ($carry, $data) use ($activity) {
                return $data->Activity === $activity && $data->PaymentStatus === 'Pending' ? $carry + $data->amount : $carry;
            }, 0);
            $totalPartial = array_reduce($dataSource, function ($carry, $data) use ($activity) {
                return $data->Activity === $activity && $data->PaymentStatus === 'Partial' ? $carry + ($data->amount- $data->PaidAmount) : $carry;
            }, 0);
            $totalPartialPaid = array_reduce($dataSource, function ($carry, $data) use ($activity) {
                return $data->Activity === $activity && $data->PaymentStatus === 'Partial' ? $carry + $data->PaidAmount : $carry;
            }, 0);

            return (object) [

                'activity' => $name,
                'url' => $url,
                'total' => $totalAmount,
                'paid' => $totalPaid + $totalPartialPaid,
                'pending' => $totalPending,
                'partial' => $totalPartial,
            ];
        }, $activities);


        $paidSum = array_reduce(array_map(fn ($collection) => $collection->paid, $results), fn ($x, $y) => $x + $y);
        $pendingSum = array_reduce(array_map(fn ($collection) => $collection->pending, $results), fn ($x, $y) => $x + $y);
        $partialSum = array_reduce(array_map(fn ($collection) => $collection->partial, $results), fn ($x, $y) => $x + $y);
        $total = array_reduce(array_map(fn ($collection) => $collection->total, $results), fn ($x, $y) => $x + $y);

        $centerName =  $dataSource[0]->CenterName;
        $data['collectionData'] = $results;
        $data['paidSum'] = $paidSum;
        $data['pendingSum'] = $pendingSum;
        $data['partialSum'] = $partialSum;
        $data['total'] = $total;
        $data['centerName'] = $centerName;
        $data['centerCode'] = $dataSource[0]->CollectionCenter;

        if ($action == 'view') {
            return view('Pages/activitiesSummary', $data);
        } else {
            $pdfLibrary = new PdfLibrary();
            $pdfLibrary->renderPdf(orientation: 'P', view: 'ReportTemplates/activitiesSummaryPdf', data: $data, title: $centerName);
        }
    }
    public function index()
    {
        if ($this->user->hasPermission('report.collectionSummary')) {
            return redirect()->route('dashboard');
        }
        $data['page'] = [
            'title' => 'Collection Centers',
            'heading' => 'Collection Centers',
        ];
        $data['user'] = auth()->user();
        $collectionCenters = $this->collectionByCenters();
        $paidSum = array_reduce(array_map(fn ($collection) => $collection->paid, $collectionCenters), fn ($x, $y) => $x + $y);
        $pendingSum = array_reduce(array_map(fn ($collection) => $collection->pending, $collectionCenters), fn ($x, $y) => $x + $y);
        $partialSum = array_reduce(array_map(fn ($collection) => $collection->partial, $collectionCenters), fn ($x, $y) => $x + $y);
        $total = array_reduce(array_map(fn ($collection) => $collection->total, $collectionCenters), fn ($x, $y) => $x + $y);


        $data['collectionData'] = $collectionCenters;
        $data['paidSum'] = $paidSum;
        $data['pendingSum'] = $pendingSum;
        $data['partialSum'] = $partialSum;
        $data['total'] = $total;


        return view('Pages/centerSummary', $data);
    }

    public function downloadCentersSummary()
    {
        $collectionCenters = $this->collectionByCenters();
        $paidSum = array_reduce(array_map(fn ($collection) => $collection->paid, $collectionCenters), fn ($x, $y) => $x + $y);
        $pendingSum = array_reduce(array_map(fn ($collection) => $collection->pending, $collectionCenters), fn ($x, $y) => $x + $y);
        $partialSum = array_reduce(array_map(fn ($collection) => $collection->partial, $collectionCenters), fn ($x, $y) => $x + $y);
        $total = array_reduce(array_map(fn ($collection) => $collection->total, $collectionCenters), fn ($x, $y) => $x + $y);

        $title = 'COLLECTION CENTERS';
        $data['collectionData'] = $collectionCenters;
        $data['paidSum'] = $paidSum;
        $data['pendingSum'] = $pendingSum;
        $data['partialSum'] = $partialSum;
        $data['total'] = $total;
        $data['title'] = $title;

        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'P', view: 'ReportTemplates/collectionSummaryPdf', data: $data, title: $title);
    }
}
