<?php

namespace App\Controllers;


use stdClass;
use App\Models\BillModel;
use App\Models\ProfileModel;
use App\Libraries\XmlLibrary;
use App\Libraries\GepgProcess;
use App\Controllers\BaseController;

class ReconciliationController extends BaseController
{
    protected $billModel;
    protected $uniqueId;

    protected $role;
    protected $city;

    protected $session;
    protected $profileModel;
    protected $CommonTasks;

    protected $billLibrary;
    protected $xmlLibrary;
    protected $GepGpProcess;
    protected $token;

    protected $SpCode;
    protected $SpSysId;
    protected $collectionCenters;
    protected $collectionCenter;



    public function __construct()
    {

        helper(setting('App.helpers'));
        $this->session = session();
        $this->token = csrf_hash();
        $this->billModel = new BillModel();
        $this->collectionCenters = $this->billModel->getCollectionCenters();
        $this->xmlLibrary = new XmlLibrary();
        $this->GepGpProcess = new GepgProcess();
        $this->profileModel = new ProfileModel();
        $this->uniqueId = auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;

        $this->role = auth()->user()->role;
        $this->SpCode = setting('App.spCode');
        $this->SpSysId = setting('App.spSysId');
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function test()
    {
        return $this->response->setJSON([40]);
    }




    public function index()
    {
        $currentPage =  url_is('billManagement') ? "Bill Management" : "Payments";
        $data['page'] = [
            "title" => $currentPage,
            "heading" =>  $currentPage,
        ];
        $month = date('m');
        $year = date('Y');
        $reconTable = $this->reconcilePayments(month:$month,year:$year);





        $data['profile'] = $this->profileModel->getLoggedUserData($this->uniqueId);
        $data['role'] = $this->role;
        $data['reconTable'] = $reconTable;

        return view('Pages/Transactions/paymentReconciliation', $data);
    }


    public function processRecon()
    {
        $month = $this->getVariable('month');
        $year = $this->getVariable('year');

        $table = $this->reconcilePayments(month: $month, year: $year);
        return  $this->response->setJSON([
            'token' => $this->token,
            'table' => $table,

        ]);
    }
    public function reconcilePayments($month, $year)
    {


        $date = dateFormatter(date("Y-m-t", strtotime("$year-$month-01")));



        $params = [
            'MONTH(CreatedAt)' => $month,
            'YEAR(CreatedAt)' => $year,
        ];

        $reconData = $this->billModel->getReconData($params);



        $reconciliation = [];
        //removing duplicates from recon data
        foreach ($reconData as $transaction) {
            $payRefId = $transaction->PayRefId;
            if (!array_key_exists($payRefId, $reconciliation)) {
                $recon[$payRefId] = $transaction;
            }
        }

        $reconciliation = array_values($reconciliation); //reset keys


        $payments = array_map(fn ($payment) => [
            'transactionId' => $payment->TrxId,
            'bank' => $payment->PspName,
            'account' => $payment->CtrAccNum,
            'paymentRef' => $payment->PayRefId,
            'controlNumber' => $payment->PayCtrNum,
            'amount' => $payment->PaidAmt
        ], $this->billModel->getPaymentData($params));





        $reconciliations = array_map(fn ($recon) => [
            'transactionId' => $recon->pspTrxId,
            'bank' => $recon->PspName,
            'account' => $recon->CtrAccNum,
            'paymentRef' => $recon->PayRefId,
            'controlNumber' => $recon->BillCtrNum,
            'amount' => $recon->PaidAmt
        ], $reconData);


        $matches = array_reduce($payments, function ($acc, $payment) use ($reconciliations) {
            $matches = array_filter($reconciliations, function ($recon) use ($payment) {
                return $recon['transactionId'] === $payment['transactionId'] &&
                    $recon['paymentRef'] === $payment['paymentRef'];
            });
            if (count($matches) > 0) {
                $match = reset($matches);
                $acc[] = [
                    'transactionId' => $match['transactionId'],
                    'bank' => $match['bank'],
                    'account' => $match['account'],
                    'paymentRef' => $match['paymentRef'],
                    'controlNumber' => $match['controlNumber'],
                    'amount' => $match['amount'],

                ];
            }
            return $acc;
        }, []);








        $notInPayments = array_filter($reconciliations, function ($recon) use ($payments) {
            foreach ($payments as $payment) {
                if (
                    $recon['transactionId'] === $payment['transactionId'] &&
                    $recon['paymentRef'] === $payment['paymentRef'] &&
                    $recon['amount']  === $payment['amount'] &&
                    $recon['controlNumber'] === $payment['controlNumber']
                ) {
                    return false;
                }
            }
            return true;
        });




        $notInReconciliations = array_filter($payments, function ($payment) use ($reconData) {
            foreach ($reconData as $recon) {
                if (
                    $recon->pspTrxId === $payment['transactionId'] &&
                    $recon->PayRefId === $payment['paymentRef'] &&
                    $recon->PaidAmt === $payment['amount'] &&
                    $recon->BillCtrNum === $payment['controlNumber']
                ) {
                    return false;
                }
            }
            return true;
        });


        // return  $this->response->setJSON([
        //     'data' => $notInReconciliations,
        //   ]);

        //   exit;




        // $bankAccounts = ['GEPG0123456', '0150357660600', '20301000002'];
        $bankAccounts = [
            (object)['accountNumber' => '992526001', 'accountName' => 'COMMISSIONER FOR WEIGHTS AND MEASURES-REV'],
            (object)['accountNumber' => '995265324', 'accountName' => 'COMMISSIONER FOR WEIGHTS AND MEASURES-EXP'],
            (object)['accountNumber' => '20301000002', 'accountName' => 'COMMISSIONER FOR WEIGHTS AND MEASURES'],
            (object)['accountNumber' => '0150357660600', 'accountName' => 'COMMISSIONER FOR WEIGHTS AND MEASURES- CRDB'],
        ];

        $results = [];

        foreach ($bankAccounts as $account) {
            $matches_filtered = array_filter($matches, function ($match) use ($account) {
                return $match['account'] === $account->accountNumber;
            });

            $notInPayments_filtered = array_filter($notInPayments, function ($payment) use ($account) {
                return $payment['account'] === $account->accountNumber;
            });

            $notInReconciliations_filtered = array_filter($notInReconciliations, function ($recon) use ($account) {
                return $recon['account'] === $account->accountNumber;
            });

            $results[] = (object)[
                'accountName' => $account->accountName,
                'accountNumber' => $account->accountNumber,
                'matches' => count($matches_filtered),
                'manual' => count([]),
                'notInReconciliations' => count($notInReconciliations_filtered),
                'notInPayments' => count($notInPayments_filtered),
            ];
        }


        $html = <<<HTML
       
        HTML;

        $table = '';

        foreach ($results as $result) {
            $table .= <<<HTML
               <tr>
                  
                        <td>$result->accountNumber</span></td>
                        <td>$result->accountName</span></td>
                        <td>$date</td>
                        <td><span class="badge badge-pill badge-primary">$result->matches</span></td>
                        <td><span class="badge badge-pill badge-primary">$result->manual</span></td>
                        <td><span class="badge badge-pill badge-secondary">$result->notInReconciliations</span></td>
                        <td><span class="badge badge-pill badge-secondary">$result->notInPayments</span></td>

                        <td>
                            <div class="card-tools">
                        <div class="btn-group show">
                         <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                           Action
                         </button>
                         <div class="dropdown-menu dropdown-menu-right hide" role="menu" x-placement="bottom-end" style="position: absolute; will-change: transform;top: 0px; left: 0px; transform: translate3d(-124px, 19px, 0px); overflow:hidden;z-index:654564">
                           <a href="cashbookToBank/$result->accountNumber" class="dropdown-item">Cash Book To Bank Matching</a>
                           <a href="cashToCash/$result->accountNumber" class="dropdown-item">Cash Book To Cash Book Matching</a>
                           <a href="bankToBank/$result->accountNumber" class="dropdown-item">Bank To Bank Matching</a>
                           <!-- <a class="dropdown-divider"></a> -->
                           
                         </div>
                        </div>
                        </div>

                        </td>
               </tr>
            HTML;
        }





        return  $table;
    }

    public function cashbookToBank($accountNumber)
    {
        $data['page'] = [
            'title' => 'Cash Book To Bank Matching',
            'heading' => 'Cash Book To Bank Matching',
        ];

        $accountName = '';
        switch ($accountNumber) {
            case '992526001':
                $accountName .= 'COMMISSIONER FOR WEIGHTS AND MEASURES-REV';
                break;
            case '995265324':
                $accountName .= 'COMMISSIONER FOR WEIGHTS AND MEASURES-EXP';
                break;
            case '20301000002':
                $accountName .= 'COMMISSIONER FOR WEIGHTS AND MEASURES';
                break;
            case '0150357660600':
                $accountName .= 'COMMISSIONER FOR WEIGHTS AND MEASURES- CRDB';
                break;

            default:
                # code...
                break;
        }

        $data['role'] = $this->role;
        $data['accountNumber'] = $accountNumber;
        $data['accountName'] = $accountName;
        return view('Pages/Transactions/CashbookToBank', $data);
    }


    public function cashbookToBankMatch()
    {
        $accountNumber = $this->getVariable('accountNumber');
        $file = $this->request->getFile('csvFile');

        $csvData = importCsv($file);
        $bankData  = array_slice($csvData, 0, 20);

        $params = [
            'CtrAccNum' => $accountNumber
        ];

        //get payment data
        $payments = $this->billModel->getPaymentData($params);
        $cashBook = array_map(fn ($cash) => [
            'date' => dateFormatter($cash->TrxDtTm),
            'amount' => $cash->PaidAmt,
            'transactionReference' => $cash->TrxId,
            'controlNumber' => $cash->PayCtrNum,
        ], $payments);
        return  $this->response->setJSON([
            'cashBookTable' => $this->reconTable($cashBook),
            'bankTable' => $this->reconTable($bankData),
            'token' => $this->token,
        ]);
    }

    public function reconTable($tableData)
    {
        $tr = '';
        foreach ($tableData as $data1) {
            $data = (object)$data1;
            $date = dateFormatter($data->date);
            $amount = number_format($data->amount);
            $tr .= <<<HTML
             <tr>
                <td>
                 <div class="icheck-primary d-inline">
                  <input type="checkbox" class="drCheck" id="$data->transactionReference" value="$data->transactionReference">
                  <label for="$data->transactionReference"></label>
                   </div>
                </td>
                <td>$date</td>
                <td>$data->transactionReference</td>
                <td>$data->controlNumber</td>
                <td>$amount</td>
            </tr>
           HTML;
        }
        $table = <<<HTML
         <thead class="thead-dark">
            <tr>
                <th></th>
                <th>Date</th>
                <th>Transaction Reference</th>
                <th>Control Number</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
          $tr
        </tbody>
     HTML;

        return $table;
    }
}
