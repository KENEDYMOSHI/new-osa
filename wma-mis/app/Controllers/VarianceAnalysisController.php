<?php

namespace App\Controllers;

use DateTime;
use DateInterval;


use App\Models\BillModel;
use PHPUnit\Util\Printer;

use App\Models\AdminModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\ArrayLibrary;
use App\Controllers\BaseController;
use App\Libraries\CommonTasksLibrary;
use App\Models\EstimatesModel;

class VarianceAnalysisController extends BaseController
{

    protected $session;
    protected $profileModel;

    protected $billModel;

    protected $prePackageModel;

    protected $commonTasks;
    protected $admin;
    protected $adminModel;

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

        helper(['format', 'bill', 'form', 'array', 'regions', 'date', 'emailTemplate', 'image']);
        $this->commonTasks     = new CommonTasksLibrary;
        $this->adminModel    = new AdminModel();
        $this->profileModel    = new ProfileModel();
        $this->billModel      = new BillModel();



        $this->user = auth()->user();
    }

    public function filterEstimates()
    {
        if ($this->request->getMethod() == 'POST') {
            if (!$this->user->hasPermission('report.variance-analysis')) {
                return redirect()->route('dashboard');
            }
            $data['page'] = [
                'title' => 'Variance Analysis Report ',
                'heading' => 'Variance Analysis Report ',
            ];
            $data['user'] = auth()->user();

            $date = $this->request->getVar('date') . ' 23:59:59';



            $currentDate = new DateTime($date); // Replace with your desired date



            // Get the first date of the current month
            $startDate = $currentDate->format('Y-m-01');

            // Use the current date as the end date
            $endDate = $currentDate->format('Y-m-d');


            // echo ' date '.$date.'<br>';
            // echo ' start date '.$startDate.'<br>';
            // echo ' end date '.$endDate.'<br>';
            // exit;

            // printer(['start'=>$startDate,'end'=>$endDate]);
            $collections = $this->collectionByCenters($startDate, $endDate);
            // Printer($collections);

            $monthAndYear = date('M Y', strtotime($endDate));
            $data['currentDate'] = dateFormatter($endDate);
            $data['monthAndYear'] = $monthAndYear;
            $data['currentMonth'] = strtoupper($currentDate->format('F, Y'));

            $data['link'] = base_url("downloadEstimate/$startDate/$endDate");
            $data['collections'] = $collections;

            $data['currentMonth'] = strtoupper($currentDate->format('F, Y'));
            return view('Pages/VarianceAnalysis', $data);
        }
    }


    public function index()
    {

        if (!$this->user->hasPermission('report.variance-analysis')) {
            return redirect()->route('dashboard');
        }
        $data['page'] = [
            'title' => 'Variance Analysis Report ',
            'heading' => 'Variance Analysis Report ',
        ];
        $data['user'] = auth()->user();







        $currentDate = new DateTime();

        // Get the first date of the current month
        $startDate = $currentDate->format('Y-m-01');

        // Use the current date as the end date
        $endDate = $currentDate->format('Y-m-d');






        // exit;

        $collections = $this->collectionByCenters($startDate, $endDate);


        $monthAndYear = date('M Y', strtotime($endDate));

        $data['link'] = base_url("downloadEstimate/$startDate/$endDate");
        $data['collections'] = $collections;
        $data['currentDate'] = dateFormatter($endDate);
        $data['monthAndYear'] = $monthAndYear;
        $data['currentMonth'] = strtoupper($currentDate->format('F, Y'));
        return view('Pages/VarianceAnalysis', $data);
    }

    public function collectionByCenters($startDate, $endDate)
    {
        // if ($startDate === $endDate) {
        //     // Add 1 day to $endDate
        //     $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
        // }

        $params = [

            'DATE(bill_payment.CreatedAt)>=' => $startDate,
            'DATE(bill_payment.CreatedAt)<=' => $endDate,

        ];





        $dataSource =  $this->billModel->getPaymentCollection($params);


       //  $centers = array_map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName], $this->billModel->getCollectionCenters());
        $collectionCenters = $this->billModel->getCollectionCenters();
        $centers = (new ArrayLibrary($collectionCenters))
            ->filter(fn ($region) => $region->centerNumber != '0031')
            ->map(fn ($cnt) => [$cnt->centerNumber, $cnt->centerName])->get();

        $collections = array_map(function ($center) use ($dataSource, $endDate, $startDate) {


            $centerCode = $center[0];

            $centerData = array_filter($dataSource, function ($data) use ($centerCode) {
                return $data->CollectionCenter === $centerCode;
            });

            $all = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->get();
            $accumulated = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->reduce(fn ($x, $y) => $x + $y->amount)->get();
            // $today = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->reduce(fn ($x, $y) => $x + $y->amount)->get();




            $filters = [
                'wma_bill.CollectionCenter' => $centerCode,
                'DATE(bill_payment.CreatedAt)' => $endDate,

            ];
            $currentCollection = $this->billModel->getPaymentCollection($filters);

            $today = (new ArrayLibrary($currentCollection))
                ->reduce(fn ($x, $y) => $x + $y->amount)
                ->get();




            $month = date('m', strtotime($endDate));
            $year = date('Y', strtotime($endDate));

            $filter = [
                'region' => $centerCode,
                'month' => $month,
                'year' => $year,
            ];
            $estimate = (new EstimatesModel())->getEstimate($filter)->amount ?? 0;

            $variance = $accumulated - $estimate;

            // printer($filter);
            // printer($estimate);
            // exit;



            // Use array_filter to get data for the current date
            // $currentCollection = (new ArrayLibrary($centerData))->filter(fn ($collection) => 

            return (object) [
                'center' => $center[0],
                'region' => str_replace('Wakala Wa Vipimo', '', $center[1]),
                'today' => $today,
                'accumulated' => $accumulated,
                'estimate' => $estimate,
                'variance' => $variance,
                'variancePercentage' => $estimate == 0 ? 0 : ($variance / $estimate) * 100,
                'date' => $endDate,
                'filter' => $filter,

            ];
        }, $centers);


        return $collections;

        //return array_filter($collections, fn ($data) => $data->accumulated != 0);
    }




    public function downloadEstimate($startDate, $endDate)
    {




        // $endDate = date('2024-01-08');
        $collections = $this->collectionByCenters($startDate, $endDate);

        $month = $month = strtoupper(date('F', strtotime($endDate)));

        $title = "VARIANCE ANALYSIS  $month ";


        $data['title'] = $title;

        $monthAndYear = date('M Y', strtotime($endDate));

        $data['collections'] = $collections;
        $data['currentDate'] = dateFormatter($endDate);
        $data['monthAndYear'] = $monthAndYear;
        $data['currentMonth'] = strtoupper(date('M Y', strtotime($endDate)));


        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'P', view: 'ReportTemplates/VarianceReport', data: $data, title: $title);
    }
}
