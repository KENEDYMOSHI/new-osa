<?php

namespace App\Controllers;

use DateTime;
use DateInterval;


use App\Models\BillModel;

use App\Models\AdminModel;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\ArrayLibrary;
use App\Controllers\BaseController;
use App\Libraries\CommonTasksLibrary;
use App\Models\EstimatesModel;

class TrAnalysisController extends BaseController
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

    public function filterTrCollection()
    {
        if ($this->request->getMethod() == 'POST') {
            // if (!$this->user->hasPermission('tr-analysis')) {
            //     return redirect()->route('dashboard');
            // }
            $data['page'] = [
                'title' => 'Tr Contribution Report ',
                'heading' => 'Tr Contribution Report ',
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

            $data['link'] = base_url("downloadTrContribution/$startDate/$endDate");
         
            $data['collections'] = $collections;

            $data['currentMonth'] = strtoupper($currentDate->format('F, Y'));
            return view('Pages/TrCollection', $data);
        }
    }


    public function index()
    {

        // if (!$this->user->hasPermission('report.tr-analysis')) {
        //     return redirect()->route('dashboard');
        // }
        $data['page'] = [
            'title' => 'Tr Contribution Report ',
            'heading' => 'Tr Contribution Report ',
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

        $data['link'] = base_url("downloadTrContribution/$startDate/$endDate");
        $data['collections'] = $collections;
        $data['currentDate'] = dateFormatter($endDate);
        $data['monthAndYear'] = $monthAndYear;
        $data['currentMonth'] = strtoupper($currentDate->format('F, Y'));
        return view('Pages/TrCollection', $data);
    }

    public function collectionByCenters($startDate, $endDate)
    {
        // if ($startDate === $endDate) {
        //     // Add 1 day to $endDate
        //     $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
        // }

        $params = [

            'DATE(tr_bill.CreatedAt)>=' => $startDate,
            'DATE(tr_bill.CreatedAt)<=' => $endDate,
            'PaymentStatus' => 'Paid',

        ];





        $dataSource =  $this->billModel->getTrCollection($params);


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
            $trContribution = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->reduce(fn ($x, $y) => $x + $y->amount)->get();
            $wmaNet = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->reduce(fn ($x, $y) => $x + $y->NetAmount)->get();
            $accumulative = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->reduce(fn ($x, $y) => $x + $y->FullPaidAmount)->get();
            // $today = (new ArrayLibrary($dataSource))->filter(fn ($data) => $data->CollectionCenter === $centerCode)->reduce(fn ($x, $y) => $x + $y->amount)->get();




            $filters = [
                'CollectionCenter' => $centerCode,
                'DATE(tr_bill.CreatedAt)' => $endDate,
                'PaymentStatus' => 'Paid',

            ];
            $currentCollection = $this->billModel->getTrCollection($filters);

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
          

            // printer($filter);
            // printer($estimate);
            // exit;



            // Use array_filter to get data for the current date
            // $currentCollection = (new ArrayLibrary($centerData))->filter(fn ($collection) => 

            return (object) [
                'center' => $center[0],
                'region' => str_replace('Wakala Wa Vipimo', '', $center[1]),
                'today' => $today,
                'trContribution' => $trContribution,
                'accumulative' => $accumulative,
                'wmaNet' => $wmaNet,
              
                'date' => $endDate,
                'filter' => $filter,

            ];
        }, $centers);


        return $collections;

        //return array_filter($collections, fn ($data) => $data->accumulated != 0);
    }




    public function downloadTrContribution($startDate, $endDate)
    {




        // $endDate = date('2024-01-08');
        $collections = $this->collectionByCenters($startDate, $endDate);

        $month = $month = strtoupper(date('F', strtotime($endDate)));

        $title = "CONSOLIDATION FUND CONTRIBUTION 15% FOR TR  $month ";


        $data['title'] = $title;

        $monthAndYear = date('M Y', strtotime($endDate));

        $data['collections'] = $collections;
        $data['currentDate'] = dateFormatter($endDate);
        $data['monthAndYear'] = $monthAndYear;
        $data['currentMonth'] = strtoupper(date('M Y', strtotime($endDate)));


        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'P', view: 'ReportTemplates/trCollectionReport', data: $data, title: $title);
    }
}
