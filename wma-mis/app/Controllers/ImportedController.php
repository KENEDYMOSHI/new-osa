<?php

namespace App\Controllers;

use DateTime;
use App\Libraries\PdfMake;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\SmsLibrary;
use App\Libraries\BillLibrary;
use App\Models\PrePackageModel;
use App\Libraries\PrePackageLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Libraries\ActivityBillProcessing;


class ImportedController extends BaseController
{
    protected $uniqueId;
    protected $managerId;
    protected $user;
    protected $city;
    protected $PrePackageModel;
    protected $session;
    protected $profileModel;
    protected $CommonTasks;
    protected $appRequest;
    protected $prePackageLibrary;
    protected $billLibrary;
    protected $token;
    protected $collectionCenter;
    protected $GfsCode;
    protected $sms;

    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->GfsCode = setting('Gfs.prePackage');
        $this->PrePackageModel = new PrePackageModel();
        $this->profileModel = new ProfileModel();
        $this->prePackageLibrary = new PrePackageLibrary();
        $this->billLibrary = new BillLibrary();
        $this->session = session();
        $this->token = csrf_hash();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;
        $this->user = auth()->user();
        $this->CommonTasks = new CommonTasksLibrary();
        $this->sms = new SmsLibrary();
        helper(['form', 'array', 'regions', 'date', 'prePackage_helper', 'image', 'url']);
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }







    public function index()
    {
        $db = db_connect();
        $data['page'] = [
            "title" => "Pre Package(Imported)",
            "heading" => "Pre Package(Imported)",
        ];



        $data['user'] = $this->user;

        $where = [
            'bill_items.createdAt >=' => financialYear()->startDate,
            'bill_items.createdAt <=' => financialYear()->endDate,
            'CollectionCenter' => $this->user->inGroup('manager', 'officer','accountant') ? $this->collectionCenter : '',
            'fob != ' => NULL,
            'tansardNumber !=' => NULL,
            'GfsCode' => '142101210007',
        ];

        $params = array_filter($where, fn($param) => $param !== '' || $param != null);

        $imported = $this->PrePackageModel->getImported($params);



        $profileModal = new ProfileModel();
        $data['centers'] = $profileModal->getCollectionCenters();






        // Printer($imported);
        // exit;
        $data['imported'] = $imported;

        return view('Pages/Prepackage/Imported', $data);
    }
    public function downloadImportedReport($year, $quarter, $month, $dateFrom, $dateTo, $region)
    {
        $data['page'] = [
            "title" => "Pre Package(Imported)",
            "heading" => "Pre Package(Imported)",
        ];

        $data['title'] = 'Pre Package(Imported) ' . ($region == 'osa' ? 'All Regions' : str_replace('Wakala Wa Vipimo', '', wmaCenter($region)->centerName));
        $data['center'] = wmaCenter($region);

        $years = explode('_', $year);

        $startYear = !empty($year) ? $years[0] : '';
        $endYear = !empty($year) ? $years[1] : '';






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


                break;
        }


        // return $this->response->setJSON([
        //     'startYear' =>  $startYear,
        //     'endYear' =>  $endYear,
        //      'datefrom' => $dateFrom,
        //      'dateto' => $dateTo,
        //     'quarter' => $quarter,
        //     'month' => $month,
        //     'token' => $this->token,

        //     'startDate'=> $startDate,
        //     'endDate'=> $endDate,
        //   ]);

        //   exit;






        if ($quarter == 'osa' && $month == 'osa' && $dateFrom == 'osa' && $year != 'osa') {
            $startDate = $startYear . '-07-01';
            $endDate    = $endYear . '-06-30 23:59:59';
            $period = 'Annual';
        } else if ($quarter == 'osa' && $month == 'osa' && $dateFrom != '' && $year == 'osa') {
            $startDate = $dateFrom;
            $endDate    = $dateTo;
            $period = dateFormatter($startDate) . ' To ' . dateFormatter($dateTo);
        } else if ($month != 'osa' && $startYear != 'osa') {
            $startDate = 'osa';
            $endDate    = 'osa';

            $period = $month . ' ' . $startYear;
        }
        $ppgRegion =  $this->user->inGroup('manager','officer','accountant') ? $this->collectionCenter : ($region != '' ? $region : 'osa');

        $params = [
            'CollectionCenter' => $region,
            'MONTH(bill_items.createdAt)' => $month,
            'YEAR(bill_items.createdAt)' => $startYear,
            'DATE(bill_items.createdAt) >=' => $startDate,
            'DATE(bill_items.createdAt) <=' => $endDate,
            'fob != ' => NULL,
            'tansardNumber !=' => NULL,
            'GfsCode' => '142101210007',

        ];

        //create url with query string from params
        $filteredParams = array_filter($params, fn($param) => $param != 'osa');

        // Printer($filteredParams);
        // exit;






        $imported  = $this->PrePackageModel->getImported($filteredParams);





        // Printer($imported);
        // exit;
        $data['imported'] =  $imported;


        if (count($imported) > 1300) {
            return view('ReportTemplates/importedPrint', $data);
        } else {
            $orientation = 'L';

            $pdfLibrary = new PdfLibrary();
            $pdfLibrary->renderPdf(orientation: $orientation, view: 'ReportTemplates/importedPdf', data: $data, title: $data['title']);
        }
    }



    public function filterImportedReport()
    {
        try {

            $year = $this->getVariable('year');
            $quarter = $this->getVariable('quarter') ?? '';
            $month = $this->getVariable('month') ?? '';
            $years = explode('/', $year);
            $dateFrom = $this->getVariable('dateFrom') ?? '';
            $dateTo = $this->getVariable('dateTo') ?? '';
            $region = $this->getVariable('region') ?? '';







            $startYear = !empty($this->getVariable('year')) ? $years[0] : '';
            $endYear = !empty($this->getVariable('year')) ? $years[1] : '';

            $center = empty($region) ? 'osa' : $region;
            $yr = empty($year) ? 'osa' :  str_replace('/', '_', $year);
            $qtr = empty($quarter) ? 'osa' : $quarter;
            $mth = empty($month) ? 'osa' : $month;
            $dateOne = empty($dateFrom) ? 'osa' : str_replace('-', '_', $dateFrom);
            $dateTwo = empty($dateTo) ? 'osa' : str_replace('-', '_', $dateTo);
            $url = base_url("downloadImportedReport/$yr/$qtr/$mth/$dateOne/$dateTwo/$center");


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


                    break;
            }

            //  return $this->response->setJSON([
            //    'status' => 0,
            //    'datefrom' => $dateFrom,
            //    'dateto' => $dateTo,
            //    'quarter' => $quarter,
            //    'month' => $month,
            //    'token' => $this->token
            //  ]);

            //  exit;





            if ($quarter == '' && $month == '' && $dateFrom == '' && $year != '') {
                $startDate = $startYear . '-07-01';
                $endDate    = $endYear . '-06-30 23:59:59';
                $period = 'Annual';
            } else if ($quarter == '' && $month == '' && $dateFrom != '' && $year == '') {
                $startDate = $dateFrom;
                $endDate    = $dateTo;
                $period = dateFormatter($startDate) . ' To ' . dateFormatter($dateTo);
            } else if ($month != '' && $startYear != '') {
                $startDate = '';
                $endDate    = '';
                $mth = DateTime::createFromFormat('n', $month)->format('M'); 
                $period =  $mth . ' ' . $startYear;
            }
            $ppgRegion =  $this->user->inGroup('manager','officer','accountant') ? $this->collectionCenter : ($region != '' ? $region : '');

            $params = [
                'CollectionCenter' => $ppgRegion,
                'MONTH(bill_items.createdAt)' => $month,
                'YEAR(bill_items.createdAt)' => $startYear,
                'DATE(bill_items.createdAt) >=' => $startDate,
                'DATE(bill_items.createdAt) <=' => $endDate,
                'fob != ' => NULL,
                'tansardNumber !=' => NULL,
                'GfsCode' => '142101210007',

            ];

            //create url with query string from params
            $filteredParams = array_filter($params, fn($param) => $param !== '' || $param != null);


            //  return $this->response->setJSON([
            //    'status' => 0,
            //    'data' =>  $filteredParams,
            //    'region' =>  $region,
            //    'url' =>  $url,
            //    'token' => $this->token
            //  ]);



            $imported  = $this->PrePackageModel->getImported($filteredParams);












           $title = 'Pre Package(Imported) ' . ($region == '' ? 'All Regions' : str_replace('Wakala Wa Vipimo', '', wmaCenter($ppgRegion)->centerName));

            $response = [
                'aa' => $imported,
                'status' => 1,
                'imported' => $this->renderReport($imported),
                'title' =>  $period . $title,
                'Imported Report',
                'url' => $url,
                'params' => $filteredParams
            ];
        } catch (\Throwable $th) {
            $response = [
                'quarter' =>  $quarter,
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token,
                // 'params' => $filteredParams
            ];
        }
        return $this->response->setJSON($response);
    }


    public function renderReport($imported)
    {

        $tr = '';
        foreach ($imported as $item) {
            $date = dateFormatter($item->createdAt);
            $amount = number_format($item->amount);
            $products = wordwrap($item->product, 50, '<br>');
            $region = str_replace('Wakala Wa Vipimo', '', wmaCenter($item->center)->centerName);
            $tr .= <<<HTML
                <tr>
                    <td>$date</td>
                    <td>$region</td>
                    <td>$item->customer </td>
                    <td>$products </td>
                    <td>$item->tansardNumber </td>
                    <td>$item->fob </td>
                    <td>$amount </td>
                    <td>$item->controlNumber </td>
                    <td>$item->PaymentStatus </td>
                    <td>$item->phoneNumber </td>
                    <td>$item->Status </td>
                 </tr>       
         HTML;
        }
        return $tr;
    }
}
