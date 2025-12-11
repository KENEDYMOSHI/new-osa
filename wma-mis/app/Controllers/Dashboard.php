<?php

namespace App\Controllers;

//use App\Models\ScaleModel;

use App\Libraries\PrePackageLibrary;
use App\Models\BillModel;
use \CodeIgniter\Validation\Rules;
use App\Models\ProfileModel;
use App\Models\scaleModel;
use App\Models\FuelPumpModel;
use App\Models\PrePackageModel;
use App\Models\ManagerModel;
use App\Models\LorriesModel;
use App\Models\VtcModel;
use App\Models\BulkStorageTankModel;
use App\Models\FixedStorageTankModel;
use App\Models\FlowMeterModel;
use App\Models\WaterMeterModel;


class Dashboard extends BaseController
{
        // public $scaleModel;
        public $session;
        public $uniqueId;
        public  $role;
        public $profileModel;
        public $scaleModel;
        public $fuelPumpModel;
        public $PrePackageModel;
        public $ManagerModel;
        public $lorriesModel;
        public $vtcModel;
        public $bstModel;
        public $fstModel;
        public $flowMeterModel;
        public $waterMeterModel;
        public $PrePackageLibrary;
        public $collectionCenter;
        public $billModel;


        public function __construct()
        {
                $this->session = session();
                $this->profileModel = new ProfileModel();
                $this->PrePackageLibrary = new PrePackageLibrary();
                // $this->fuelPumpModel          = new FuelPumpModel();
                $this->PrePackageModel = new PrePackageModel();
                $this->ManagerModel           = new ManagerModel();
                $this->lorriesModel           = new LorriesModel();
                $this->vtcModel               = new VtcModel();
                $this->billModel               = new BillModel();
                // $this->bstModel               = new BulkStorageTankModel();
                // $this->fstModel               = new FixedStorageTankModel();
                // $this->flowMeterModel         = new FlowMeterModel();
                $this->waterMeterModel        = new WaterMeterModel();
                $this->uniqueId = auth()->user()->unique_id;
                $this->collectionCenter = auth()->user()->collection_center;
                $this->role = auth()->user()->role;
        }

        public function index()
        {

                $data['page'] = [
                        "title"   => "Home | Dashboard",
                        "heading" => "Dashboard",

                ];

                $data['role'] = $this->role;
                $data['user'] = auth()->user();

                $params = [
                        'wma_bill.UserId' => $this->uniqueId,
                        'PayCntrNum !=' => '',
                        'wma_bill.CreatedAt >=' => financialYear()->startDate,
                        'wma_bill.CreatedAt <=' => financialYear()->endDate
                ];



                $collection = array_map(function ($data) {
                        $report = $data;
                        $report->billItems = $this->billModel->fetchBillItems($data->BillId);
                        return $report;
                }, $this->billModel->getReportData($params));



                $data['vtv'] = array_filter($collection, fn ($data) => $data->Activity == 'vtv',);
                $data['sbl'] = array_filter($collection, fn ($data) => $data->Activity == 'sbl',);
                $data['waterMeter'] = array_filter($collection, fn ($data) => $data->Activity == 'waterMeter',);
                $data['prePackage'] = array_filter($collection, fn ($data) => $data->Activity == 'prepackage',);

                return view('Pages/dashBoardEntry', $data);
                // return view('Pages/officerDashboard', $data);
        }

        public function logout()
        {
                $this->session->remove('loggedUser');
                $this->session->remove('role');
                $this->session->destroy();
                return redirect()->to(\base_url() . '/login');
        }

        // public function totalScales()
        // {

        //         $uniqueId = $this->uniqueId;
        //         $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        //         $data['total'] = $this->scaleModel->totalScales($uniqueId);
        //         return view('Pages/dashboard', $data);
        // }
}
