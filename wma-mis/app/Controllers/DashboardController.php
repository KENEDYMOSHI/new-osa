<?php

namespace App\Controllers;

use DateTime;
use stdClass;
use App\Models\VtcModel;
use App\Models\BillModel;
use PHPUnit\Util\Printer;
use App\Models\ReportModel;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Models\DashboardModel;
use App\Models\EstimatesModel;
use App\Libraries\ArrayLibrary;
use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Libraries\PrePackageLibrary;
use App\Libraries\CommonTasksLibrary;

class DashboardController extends BaseController
{
        // public $scaleModel;
        protected $session;
        protected $uniqueId;
        protected $profileModel;
        protected $prePackageModel;
        protected $prePackageLibrary;
        protected $lorriesModel;
        protected $vtcModel;
        protected $bstModel;
        protected $waterMeterModel;
        protected $commonTasks;

        // ================Global variables to store Amount collected in all rg==============



        protected $collectionCenter;
        protected $billModel;
        protected $reportModel;
        protected $user;


        public function __construct()
        {
                helper(['setting', 'date', 'auth']);
                helper('App.helpers');
                $this->user = auth()->user();
                $this->commonTasks     = new CommonTasksLibrary;
                $this->prePackageLibrary     = new PrePackageLibrary();
                $this->session         = session();
                $this->profileModel    = new ProfileModel();
                $this->prePackageModel = new prePackageModel();
                $this->lorriesModel    = new LorriesModel();
                $this->vtcModel        = new VtcModel();
                $this->billModel        = new BillModel();
                $this->waterMeterModel = new WaterMeterModel();
                $this->uniqueId        = $this->user->unique_id;
                $this->collectionCenter = $this->user->collection_center;
                $this->reportModel     = new ReportModel();

                // ============================== 


        }
        // ================get all data for an Api==============
        public function dataChart()
        {

                $queryParams = [

                        'DATE(wma_bill.CreatedAt) >=' =>  financialYear()->startDate,
                        'DATE(wma_bill.CreatedAt) <=' =>  financialYear()->endDate . ' 23:59:59',
                        'wma_bill.CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->user->collection_center : '',
                        'IsCancelled' => 'No',
                ];

                // $queryParams = [

                //         'wma_bill.CreatedAt >=' => $this->user->inGroup('manager','accountant', 'officer') ? '' : financialYear()->startDate,
                //         'wma_bill.CreatedAt <=' => $this->user->inGroup('manager','accountant', 'officer') ? '' : financialYear()->endDate.' 23:59:59',
                //         "MONTH(wma_bill.CreatedAt)" => $this->user->inGroup('manager','accountant', 'officer') ?  date('m') : '',
                //         'CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->user->collection_center : '',
                //         // 'wma_bill.UserId' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                //         'IsCancelled' => 'No',
                // ];


                $params = array_filter($queryParams, fn($param) => $param !== '' || $param != null);

                $prmArr = [

                        'DATE(bill_payment.TrxDtTm) >=' =>  financialYear()->startDate,
                        'DATE(bill_payment.TrxDtTm) <=' =>  financialYear()->endDate . ' 23:59:59',
                        'CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->user->collection_center : '',
                        'IsCancelled' => 'No',
                ];


                $prm = array_filter($prmArr, fn($param) => $param !== '' || $param != null);

                $dataSource =  $this->billModel->getPaymentCollection($prm);


                // return  $this->response->setJSON([
                //         // 'months' =>  array_keys((array)$obj),
                //         // 'amounts' =>  array_values((array)$obj),
                //         // 'total' =>   'TZS ' . number_format($totalAmount),
                //         // 'paid' =>  'TZS ' . number_format($paid + $partialPaid),
                //         // 'pending' =>   'TZS ' . number_format($pending),
                //         // 'partial' =>  'TZS ' . number_format($partial),
                //         'PARAMS' =>  $params,
                // ]);


                // exit;


                $collection = array_map(function ($data) {
                        return $data;
                }, $this->billModel->getReportData($params));



                $months = [
                        (object)['month' => 'Jan', 'value' => 1],
                        (object)['month' => 'Feb', 'value' => 2],
                        (object)['month' => 'Mar', 'value' => 3],
                        (object)['month' => 'Apr', 'value' => 4],
                        (object)['month' => 'May', 'value' => 5],
                        (object)['month' => 'Jun', 'value' => 6],
                        (object)['month' => 'Jul', 'value' => 7],
                        (object)['month' => 'Aug', 'value' => 8],
                        (object)['month' => 'Sep', 'value' => 9],
                        (object)['month' => 'Oct', 'value' => 10],
                        (object)['month' => 'Nov', 'value' => 11],
                        (object)['month' => 'Dec', 'value' => 12],

                ];


                $payments = (new ArrayLibrary($collection))->map(fn($p) => [
                        'date' => $p->CreatedAt,
                        'paymentStatus' => $p->PaymentStatus,
                        'amount' => $p->amount,
                        'paidAmount' => $p->PaidAmount,
                ])->get();

                $totalAmount = (new ArrayLibrary($payments))->map(fn($pay) => $pay['amount'])->reduce(fn($x, $y) => $x + $y)->get();
                // $paidOnly = (new ArrayLibrary($dataSource))->filter(fn ($pay) => $pay['paymentStatus'] === 'Paid')->get();
                $paid = (new ArrayLibrary($payments))->filter(fn($pay) => $pay['paymentStatus'] === 'Paid')->map(fn($p) => $p['amount'])->reduce(fn($x, $y) => $x + $y)->get();
                $pending = (new ArrayLibrary($payments))->filter(fn($pay) => $pay['paymentStatus'] === 'Pending')->map(fn($p) => $p['amount'])->reduce(fn($x, $y) => $x + $y)->get();

                $partial = (new ArrayLibrary($payments))
                        ->filter(fn($pay) => $pay['paymentStatus'] === 'Partial')->map(fn($p) => $p['amount'] - $p['paidAmount'])
                        ->reduce(fn($x, $y) => $x + $y)->get();


                $partialPaid = (new ArrayLibrary($payments))
                        ->filter(fn($pay) => $pay['paymentStatus'] === 'Partial')->map(fn($p) => $p['paidAmount'])
                        ->reduce(fn($x, $y) => $x + $y)->get();



                $obj = new stdClass;
                // Map each payment data to month and amount
                $monthlyPayments = array_map(function ($payment) use ($months) {
                        $date = new DateTime($payment->CreatedAt);
                        $month = $months[$date->format('n') - 1]->month;
                        $amount = round($payment->amount);
                        $dt = $payment->CreatedAt;
                        return ['month' => $month, 'amount' => $amount, 'date' => $dt];
                }, $dataSource);

                // Group the monthly payments by month and calculate the total amount for each month
                $monthlyTotals = array_reduce($monthlyPayments, function ($result, $payment) {
                        $month = $payment['month'];
                        $amount = $payment['amount'];
                        $dt = $payment['date'];
                        if (!isset($result[$month])) {
                                $result[$month] = 0;
                        }
                        $result[$month] += $amount;
                        return $result;
                }, []);

                // Print the monthly totals
                foreach ($months as $month) {
                        $monthName = $month->month;
                        $total = isset($monthlyTotals[$monthName]) ? $monthlyTotals[$monthName] : 0;
                        $obj->$monthName = $total;
                }



                return  $this->response->setJSON([
                        'months' =>  array_keys((array)$obj),
                        'amounts' =>  array_values((array)$obj),
                        'total' =>   'TZS ' . number_format($totalAmount),
                        'paid' =>  'TZS ' . number_format($paid + $partialPaid),
                        'pending' =>   'TZS ' . number_format($pending),
                        'partial' =>  'TZS ' . number_format($partial),
                        'PARAMS' =>  $params,
                ]);
        }





        public function parameters($table)
        {

                $queryParams = [
                        // $table . '.unique_id' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                        $table . '.created_at>=' => $this->user->inGroup('manager','accountant', 'officer') ? '' : financialYear()->startDate,
                        $table . '.created_at<=' => $this->user->inGroup('manager','accountant', 'officer') ? '' : financialYear()->endDate,
                        "MONTH($table.created_at)" => $this->user->inGroup('manager','accountant', 'officer') ?  date('m') : '',
                        "YEAR($table.created_at)" => $this->user->inGroup('manager','accountant', 'officer') ?  date('Y') : '',
                        'CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ?  $this->collectionCenter : '',
                        'IsCancelled' => 'No',
                ];

                // if($table == 'water_meters') $queryParams['decision'] = 'PASS';

                return  array_filter($queryParams, fn($param) => $param !== '' || $param != null);
        }




        public function index()
        {

                $data['page'] = [
                        "title"   => "Home | Dashboard",
                        "heading" => 'Dashboard'
                ];


                $data['user'] = $this->user;



                $queryParams = [

                        'DATE(wma_bill.CreatedAt)>=' => $this->user->inGroup('manager','accountant', 'officer') ? '' : financialYear()->startDate,
                        'DATE(wma_bill.CreatedAt) <=' => $this->user->inGroup('manager','accountant', 'officer') ? '' : financialYear()->endDate,
                        "MONTH(wma_bill.CreatedAt)" => $this->user->inGroup('manager','accountant', 'officer') ?  date('m') : '',
                        "YEAR(wma_bill.CreatedAt)" => $this->user->inGroup('manager','accountant', 'officer') ?  date('Y') : '',
                        'CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->user->collection_center : '',
                        // 'wma_bill.UserId' => $this->user->inGroup('officer') ? $this->user->unique_id : '',
                        'IsCancelled' => 'No',
                        // 'PaymentStatus' => 'Paid',
                ];



                $params = array_filter($queryParams, fn($param) => $param !== '' || $param != null);
                $param['PayCntrNum !='] = '';





                $data['vtv'] = (new DashboardModel)->vtv($params);
                $data['sbl'] = (new DashboardModel)->sbl($params);
                $data['waterMeter'] = (new DashboardModel)->waterMeters($params);
                $data['prePackage'] = (new DashboardModel())->ppg($params);

                //  Printer($params);
                //  exit;
                // // Printer( (new DashboardModel)->sbl($params));

                //  $data['vtv'] =[];
                //        $data['sbl'] = [];
                //     $data['waterMeter'] = [];
                // $data['prePackage'] = [];
                // $data['others'] = [];
                $data['others'] = (new DashboardModel())->others($params);

                // $data['others'] = (new DashboardModel())->othersPaidOnly();



                $currentDate = new DateTime();

                // Get the first date of the current month
                $startDate = $currentDate->format('Y-m-01');

                // Use the current date as the end date
                $endDate = $currentDate->format('Y-m-d');


                // Printer( $data['waterMeter']);
                // exit;

                $varianceParams = [
                        'DATE(bill_payment.TrxDtTm)>=' => $startDate,
                        'DATE(bill_payment.TrxDtTm) <=' => $endDate,
                        'CollectionCenter' => $this->user->inGroup('manager','accountant', 'officer')  ? $this->collectionCenter : ''
                ];

                $paramsVariance = array_filter($varianceParams, fn($param) => $param !== '' || $param != null);

                // Collection data source,
                $dataSource =  $this->billModel->getPaymentCollection($paramsVariance);
                $x = [
                        'DATE(bill_payment.TrxDtTm)>=' => financialYear()->startDate,
                        'DATE(bill_payment.TrxDtTm) <=' => financialYear()->endDate,
                        'CollectionCenter' => $this->user->inGroup('manager','accountant', 'officer')  ? $this->collectionCenter : ''
                ];
                $annualParams = array_filter($x, fn($param) => $param !== '' || $param != null);
                $annualAccumulated =  $this->billModel->getPaymentCollection($annualParams);

                $annualTotal = (new ArrayLibrary($annualAccumulated))->reduce(fn($x, $y) => $x + $y->amount)->get();

                // printer($annualParams);
                // printer($annualAccumulated);
                // exit;


                //accumulated income from start of month to current date
                $accumulated = (new ArrayLibrary($dataSource))->reduce(fn($x, $y) => $x + $y->amount)->get();

                //filter current date income
                $today = (new ArrayLibrary($dataSource))->filter(function ($data) {
                        $date = date('Y-m-d');
                        $recordDate = date('Y-m-d', strtotime($data->CreatedAt));
                        return $recordDate === $date;
                })->reduce(fn($x, $y) => $x + $y->amount)->get();

                $month = date('m', strtotime($endDate));
                $year = date('Y', strtotime($endDate));

                $filterEstimates = [
                        'region' => $this->user->inGroup('manager','accountant', 'officer')  ? $this->collectionCenter : '',
                        'month' => $month,
                        'year' => $year,
                ];


                $estimateParams = array_filter($filterEstimates, fn($param) => $param !== '' || $param != null);
                //get the estimate based on region
                if ($this->user->inGroup('manager','accountant', 'officer')) {

                        $estimate = (new EstimatesModel())->getEstimate($estimateParams)->amount ?? 0;
                } else {

                        $estimates = (new EstimatesModel())->getEstimates($estimateParams);
                        $estimate = (new ArrayLibrary($estimates))->reduce(fn($x, $y) => $x + $y->amount)->get();
                }
                $estimates = (new EstimatesModel())->getEstimates($estimateParams);
                // echo "Estimate ". number_format($estimate);
                // printer($estimates);
                // exit;
                //calculating variance 
                $variance = $accumulated - $estimate;

                //calculating variance  percentage
                $percent =  round($estimate == 0 ? 0 : ($variance / $estimate) * 100);






                $debtParams = [


                        'CollectionCenter' => $this->user->inGroup('officer', 'manager','accountant') ? $this->user->collection_center : '',

                ];



                $debt = array_filter($debtParams, fn($param) => $param !== '' || $param != null);


                $pendingAndPartial = $this->reportModel->getPendingAndPartial($debt);

                $partialUnpaid = (new ArrayLibrary($pendingAndPartial))->filter(fn($item) => $item->PaymentStatus == 'Partial')
                        ->map(fn($bill) => $bill->BillAmt - $bill->PaidAmount)
                        ->reduce(fn($x, $y) => $x + $y)
                        ->get();
                $pendingUnpaid = (new ArrayLibrary($pendingAndPartial))->filter(fn($item) => $item->PaymentStatus == 'Pending')
                        ->map(fn($bill) => $bill->BillAmt)
                        ->reduce(fn($x, $y) => $x + $y)
                        ->get();


                //         echo date('Y-m-d');
                //        // echo count($accumulated);
                //                 Printer($today);
                //                 echo '------------------------------------------------';
                // Printer($debt);
                // // 
                // exit;


                $data['partialUnpaid'] = number_format($partialUnpaid);
                $data['pendingUnpaid'] = number_format($pendingUnpaid);
                $data['annualTotal'] = number_format($annualTotal);
                $data['all'] = number_format($annualTotal + $partialUnpaid + $pendingUnpaid);
                $data['accumulated'] = $accumulated;
                $data['today'] = $today;
                $data['estimate'] = $estimate;
                $data['variance'] = $variance;
                $data['percent'] = $percent;
                $data['monthYear'] = date('M Y', strtotime($endDate));


                $token = randomString();
                $link = site_url()."user/passwordChange/$token/$this->uniqueId";


                // if($this->user->requiresPasswordReset()){
                //         echo $link;
                // }else{
                //         return view('Pages/MainDashboard', $data);
                // }

               return view('Pages/MainDashboard', $data);
                // return view('pages/dashboard', $data);
        }


        public function index2()
        {
                $data['page'] = [
                        "title"   => "Home | Dashboard",
                        "heading" => 'Dashboard'
                ];


                $data['user'] = $this->user;

                return view('Pages/MainDashboard', $data);
        }
}
