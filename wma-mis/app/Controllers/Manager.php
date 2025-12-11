<?php

namespace App\Controllers;

//use App\Models\ScaleModel;
use Dompdf\Dompdf;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\PhoneModel;
use App\Models\scaleModel;
use App\Models\LicenseModel;
use App\Models\LorriesModel;
use App\Models\ManagerModel;
use App\Models\ProfileModel;
use App\Models\ServiceModel;
use App\Libraries\PdfLibrary;
use App\Models\FuelPumpModel;
use App\Models\FlowMeterModel;
use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Controllers\BaseController;
use App\Models\BulkStorageTankModel;
use App\Libraries\CommonTasksLibrary;
use App\Models\FixedStorageTankModel;



// use App\Models\UserModel;

class Manager extends BaseController
{
    // public $scaleModel;
    public $session;
    public $uniqueId;

    public $city;
    public $profileModel;
    public $billModel;
    public $fuelPumpModel;
    public $prePackageModel;
    public $ManagerModel;
    public $lorriesModel;
    public $vtcModel;
    public $bstModel;
    public $fstModel;
    public $flowMeterModel;
    public $serviceModel;
    public $waterMeterModel;
    public $commonTask;
    public $token;
    public $licenseModel;
    protected $collectionCenter;
    protected $user;

    public function __construct()
    {
        helper(['alert', 'form', 'array', 'regions', 'date', 'image', 'inflector', 'format']);
        $this->session = session();
        $this->commonTask = new CommonTasksLibrary;
        $this->profileModel = new ProfileModel();
        $this->serviceModel = new ServiceModel();
        $this->user = auth()->user();

        $this->licenseModel = new LicenseModel();

        $this->billModel = new BillModel();
        $this->fuelPumpModel = new FuelPumpModel();
        $this->prePackageModel = new prePackageModel();
        $this->ManagerModel = new ManagerModel();
        $this->lorriesModel = new LorriesModel();
        $this->vtcModel = new VtcModel();
        $this->bstModel = new BulkStorageTankModel();
        $this->fstModel = new FixedStorageTankModel();
        $this->flowMeterModel = new FlowMeterModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->uniqueId = $this->user->unique_id;
        $this->collectionCenter = $this->user->collection_center;
       
        $this->token = csrf_hash();
    }

    public function getVariable($var)
    {
        return $this->request->getPost($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    public function analytics()
    {
        $data = [];

        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.CreatedAt >=' => financialYear()->startDate,
            'wma_bill.CreatedAt <=' => financialYear()->endDate,
            'wma_bill.CollectionCenter ' => $this->collectionCenter,
        ];


        $collection = array_map(function ($data) {
            $report = $data;
            $report->billItems = $this->billModel->fetchBillItems($data->BillId);
            return $report;
        }, $this->billModel->getReportData($params,'',[]));



        $vtv = array_filter($collection, fn ($data) => $data->Activity == 'vtv',);
        $sbl = array_filter($collection, fn ($data) => $data->Activity == 'sbl',);
        $waterMeter = array_filter($collection, fn ($data) => $data->Activity == 'waterMeter',);
        $prePackage = array_filter($collection, fn ($data) => $data->Activity == 'prepackage',);

        // echo json_encode($params);
        // exit;


        array_push($data, $vtv, $prePackage, $waterMeter, $sbl);

        if ($data) {


            return  $this->response->setJSON($data);
        } else {
            return  $this->response->setJSON(['No data found']);
            // return $this->failNotFound(');
        }
    }

    public function index()
    {

        $data['page'] = [
            "title" => "Home | Dashboard",
            "heading" => "Dashboard",
        ];

       
        $data['user'] = auth()->user();
     

        $uniqueId = $this->uniqueId;
        // $location = $this->region;
        // $data['location'] = $this->region;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);


        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.CreatedAt >=' => financialYear()->startDate,
            'wma_bill.CreatedAt <=' => financialYear()->endDate,
            'wma_bill.CollectionCenter ' => $this->collectionCenter,
        ];





        $collection = array_map(function ($data) {
            $report = $data;
            $report->billItems = $this->billModel->fetchBillItems($data->BillId);
            return $report;
        }, $this->billModel->getReportData($params,'',[]));



        $data['vtv'] = array_filter($collection, fn ($data) => $data->Activity == 'vtv',);
        $data['sbl'] = array_filter($collection, fn ($data) => $data->Activity == 'sbl',);
        $data['waterMeter'] = array_filter($collection, fn ($data) => $data->Activity == 'waterMeter',);
        $data['prePackage'] = array_filter($collection, fn ($data) => $data->Activity == 'prepackage',);


        return view('Pages/Manager/managerDashboard', $data);
        // return view('pages/dashboard', $data);
    }


    public function serviceRequests()
    {

        $data = [];
        $data['page'] = [
            "title" => "Service Requests",
            "heading" => "Service Requests",
        ];
        //

        $uniqueId = $this->uniqueId;

       
        $data['user'] = auth()->user();
   
        $data['requests'] = $this->serviceModel->getServiceRequestsInRegion($this->collectionCenter);
        return view('Pages/Manager/regionalServiceRequests', $data);
    }
    public function licenseApplications()
    {

        $data = [];
        $data['page'] = [
            "title" => "License Applications",
            "heading" => "License Applications",
        ];
        //

        $uniqueId = $this->uniqueId;

       
        $data['user'] = auth()->user();
  
        $params = [
            'region' => $this->collectionCenter
        ];
        $data['applications'] = $this->licenseModel->getLicenseApplicationsInRegion($params);
        return view('Pages/Manager/regionalLicenseApplications', $data);
    }
    public function applicationDetails($applicationId)
    {
        $data['page'] = [
            "title" => "Application Details",
            "heading" => "Application Details",
        ];

        $params = [
            'application_id' => $applicationId,
            'submission' => 1,
        ];


       
        $data['user'] = auth()->user();
       
        $data['particulars'] = $this->licenseModel->getApplicantParticulars(
            [
                'user_id' => $this->licenseModel->getLicenseType($params)[0]->user_id
            ]
        );
        $data['licenseTypes'] = $this->licenseModel->getLicenseType($params);
        $data['tools'] = $this->licenseModel->getTools($params);
        $data['qualifications'] = $this->licenseModel->getQualifications($params);
        $data['attachments'] = $this->licenseModel->getAttachments($params);
        $data['applicationId'] = $applicationId;

        return view('Pages/Manager/LicenseApplicationDetails', $data);
    }
    public function downloadApplication($applicationId)
    {

        $params = [
            'application_id' => $applicationId,
            'submission' => 1,
        ];

        $particulars = $this->licenseModel->getApplicantParticulars(
            [
                'user_id' => $this->licenseModel->getLicenseType($params)[0]->user_id
            ]
        );
        $data['particulars'] = $particulars;
        $data['licenseTypes'] = $this->licenseModel->getLicenseType($params);
        $data['tools'] = $this->licenseModel->getTools($params);
        $data['qualifications'] = $this->licenseModel->getQualifications($params);
        $data['attachments'] = $this->licenseModel->getAttachments($params);
        $data['applicationId'] = $applicationId;






        $title = $particulars->applicant_name . '-' . time();


        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'L', view: 'Pages/Manager/LicenseApplicationPdf', data: $data, title: centerName());
    }

    public function confirmServiceRequests($id)
    {
        $query = $this->serviceModel->confirmServiceRequests($id);
        if ($query) {
            session()->setFlashdata('success', 'Request Status Updated');
            return redirect()->to('service-requests');
        }
    }
    public function downloadServiceRequests($id)
    {
        $customerRequest = $this->serviceModel->getSingleRequest($id);
        $title = $customerRequest->name . '-' . time();
        $data['request'] = $customerRequest;


        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: 'P', view: 'Pages/Manager/ServiceRequestPdf', data: $data, title: centerName());
    }



    public function addGroup()
    {




        if ($this->request->getMethod() == 'POST') {

            $groupId = md5(str_shuffle('abcdefghijklmnopqrstuvwxyz' . time()));
            $officerIds = $this->getVariable('officer');
            $groupName = $this->request->getPost('groupName');

            $groupData = [];
            $groupIdArray = [];
            $groupNameArray = [];
            $uniqueIds = [];


            if ($officerIds == null) {

                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Please Select At Least One officer',
                    'token' => $this->token,

                ]);
            } else {
                for ($i = 0; $i < count($officerIds); $i++) {
                    array_push($groupIdArray,   $groupId);
                    array_push($groupNameArray, $groupName);
                    array_push($uniqueIds, $this->uniqueId);
                }
                $data = [
                    "group_name" => $groupNameArray,
                    // "group_id" => $groupIdArray,
                    'officer_id' => $officerIds,
                    "unique_id" => $uniqueIds,

                ];

                foreach ($data as $key => $value) {
                    for ($i = 0; $i < count($value); $i++) {
                        $groupData[$i][$key] = $value[$i];
                    }
                }


                $status = $this->ManagerModel->saveGroupData($groupData);

                if ($status) {

                    return $this->response->setJSON([
                        'status' => 1,
                        'msg' => 'Group Created Successfully',
                        'groups' => $this->ManagerModel->getGroups($this->uniqueId),
                        'token' => $this->token,

                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Something Went Wrong',
                        'token' => $this->token,

                    ]);
                }
            }
        }
    }

    // ================Assigning officers to  A group ==============
    public function createTask()
    {
        if ($this->request->getMethod() == 'POST') {

            $taskData = [
                "activity" => $this->getVariable('activity'),
                "description" => $this->getVariable('description'),
                "the_group" => $this->getVariable('group'),
                "region" => $this->getVariable('region'),
                "district" => $this->request->getVar('district'),
                "ward" => $this->getVariable('ward'),
                "unique_id" => $this->uniqueId,

            ];
            // return $this->response->setJSON([
            //     $taskData,
            //     // 'status' => 1,
            //     // 'msg' => 'Group Created Successfully',
            //     // 'token' => $this->token,

            // ]);
            // exit;

            $status = $this->ManagerModel->assignTaskToGroup($taskData);


            if ($status) {

                return $this->response->setJSON([
                    'status' => 1,
                    'msg' => 'Task Assigned Successfully',
                    'token' => $this->token,

                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Something Went Wrong',
                    'token' => $this->token,

                ]);
            }
        }
    }

    // ================Assigning a task to a group==============

    public function assignTask()
    {

        $data = [];
        $data['page'] = [
            "title" => "Assign Task To A Group",
            "heading" => "Assign Task To A Group",
        ];
        // ================All regions==============
        $data['regions'] = ['Arusha', 'Dar es Salaam', 'Dodoma', 'Geita', 'Iringa', 'Kagera', 'Katavi', 'Kigoma', 'Kilimanjaro', 'Lindi', 'Manyara', 'Mara', 'Mbeya', 'Morogoro', 'Mtwara', 'Njombe', 'Pemba North', 'Pemba South', 'Pwani', 'Rukwa', 'Ruvuma', 'Shinyanga', 'Simiyu', 'Singida', 'Tabora', 'Tanga', 'Zanzibar'];

        $data['districts'] = ['Arusha City', 'Arusha Rural', 'Karatu', 'Longido', 'Meru', 'Monduli', 'Ngorongoro'];

        // ================Activities==============
        $data['activities'] = [
            'Inspection and verification of scales',
            'Inspection and verification of fuel pumps',
            'Inspection   of fuel pumps industrial packages',
            'Vehicle Tank Calibration',
            'Sandy and ballast lorries calibration',
            'Bulk storage tank calibration',
            'Flow meter inspection',

        ];
        $uniqueId = $this->uniqueId;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        // ================A function to get all officers==============
        $data['officers'] = $this->ManagerModel->getAllOfficers([
            'users.collection_center' => $this->collectionCenter,
            'auth_groups_users.group' => 'officer',
        ]);
        // ================get all the groups created==============
        $data['groups'] = $this->ManagerModel->getGroups($uniqueId);



        // return redirect()->to(current_url());
       
        $data['user'] = auth()->user();
   
        return view('Pages/Manager/assignTaskToGroup', $data);
    }
    public function assignToIndividual()
    {

        $data = [];
        $data['page'] = [
            "title" => "Assign Task To A Individual",
            "heading" => "Assign Task To A Individual",
        ];

        // ================Activities==============
        $data['activities'] = [
            'Inspection and verification of scales',
            'Inspection and verification of fuel pumps',
            'Inspection   of fuel pumps (F/P) ',
            'Pre-packaged Inspections ',
            'Vehicle Tank Calibration (VTC)',
            'Sandy and ballast lorries calibration (SBL)',
            'Bulk Storage Tank calibration (BST)',
            'Fixed Storage Tank (FST)',
            'Flow meter inspection (FM)',

        ];
        $uniqueId = $this->uniqueId;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        // ================A function to get all officers==============
        $data['officers'] = $this->ManagerModel->getAllOfficers($this->collectionCenter);
        // ================get all the groups created==============
        $data['groups'] = $this->ManagerModel->getAllGroups($uniqueId);

        $data['validation'] = null;
        $rules = [
            // "groupname"     => "required|min_length[3]|max_length[15]|is_unique[users_group.group_name]",

            'activity' => [
                'label' => 'Activity',
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must select an activity',

                ],
            ],
            // ==============================
            'group' => [
                'label' => 'Group',
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must select a group',

                ],
            ],
            'region' => [
                'label' => 'Region',
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must select a region',

                ],
            ],
            'district' => [
                'label' => 'district',
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must select a district',

                ],
            ],
            'district' => [
                'label' => 'district',
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must select a district',

                ],
            ],
            'ward' => [
                'label' => 'ward',
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must select a ward',

                ],
            ],
            // ==============================

        ];
        if ($this->request->getMethod() == 'POST') {
            if ($this->validate($rules)) {
                $group = $this->request->getVar('group', FILTER_SANITIZE_SPECIAL_CHARS);
                $taskData = [
                    "activity" => $this->request->getVar('activity', FILTER_SANITIZE_SPECIAL_CHARS),
                    "description" => $this->request->getVar('description', FILTER_SANITIZE_SPECIAL_CHARS),
                    "the_group" => $this->request->getVar('group', FILTER_SANITIZE_SPECIAL_CHARS),
                    "region" => $this->request->getVar('region', FILTER_SANITIZE_SPECIAL_CHARS),
                    "district" => $this->request->getVar('district', FILTER_SANITIZE_SPECIAL_CHARS),
                    "ward" => $this->request->getVar('ward', FILTER_SANITIZE_SPECIAL_CHARS),
                    "unique_id" => $this->uniqueId,

                ];

                $status = $this->ManagerModel->assignTaskToGroup($taskData);

                if ($status) {
                    $this->session->setFlashdata('Success', 'Task assigned to ' . $group . ' group Successfully <i class="fal fa-smile-wink"></i>');
                    return redirect()->to(current_url());
                    // echo "<script>alert('Data Inserted');</script>";
                } else {
                    $this->session->setFlashdata('error', 'Fail To  Add To A  Group Try Again');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }
        // return redirect()->to(current_url());
       
        $data['user'] = auth()->user();
       
        return view('Pages/Manager/assignTaskToIndividual', $data);
    }

    public function viewTasks()
    {

        $data = [];
        $data['page'] = [
            "title" => "View Tasks And Groups",
            "heading" => "View Tasks And Groups",
        ];
        $uniqueId = $this->uniqueId;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
       
        $data['user'] = auth()->user();
      
        //$city = $this->region;
        $data['activities'] = $this->ManagerModel->getAllTasks($uniqueId);
        return view('Pages/Manager/activities', $data);
    }

    public function cool()
    {
        $res = $this->ManagerModel->getGroupAndOfficers();

        //print_r($res);
        echo json_encode($res);
    }


    public function test()
    {
        helper('form');
        $phone = new PhoneModel();
        $data['page'] = [
            "title" => "Task",
            "heading" => "Task",
        ];

        if ($this->request->getMethod() == 'POST') {

            $groupName = $this->request->getVar('groupName', FILTER_SANITIZE_SPECIAL_CHARS);

            $officerId = $this->request->getVar('officer', FILTER_SANITIZE_SPECIAL_CHARS);
            foreach ($officerId as $id) {
                $groupData = [
                    'group_name' => $groupName,
                    'officer_id' => $id,
                    'unique_id' => $this->uniqueId,
                ];

                $this->ManagerModel->addOfficerToGroup($groupData);
            }
        }
        return redirect()->to('/assignTask');
        //return view('Pages/Manager/assignTaskToGroup', $data);
    }
}
