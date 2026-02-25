<?php

namespace App\Controllers;


use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\AdminModel;
use App\Models\LorriesModel;
use App\Models\ProfileModel;
use App\Models\DirectorModel;
use App\Models\FuelPumpModel;
use App\Models\FlowMeterModel;

use App\Models\PrePackageModel;
use App\Models\WaterMeterModel;
use App\Models\BulkStorageTankModel;
use App\Libraries\CommonTasksLibrary;
use App\Libraries\SmsLibrary;
use App\Models\FixedStorageTankModel;
use App\Models\UsersModel;

use CodeIgniter\Shield\Controllers\RegisterController;
use CodeIgniter\Shield\Exceptions\ValidationException;



class Admin extends RegisterController
{
    // public $scaleModel;
    protected $session;
    protected $uniqueId;
    protected $user;
    protected $city;
    protected $profileModel;
    protected $scaleModel;
    protected $billModel;
    protected $fuelPumpModel;
    protected $prePackageModel;
    protected $DirectorModel;
    protected $lorriesModel;
    protected $vtcModel;
    protected $bstModel;
    protected $fstModel;
    protected $flowMeterModel;
    protected $waterMeterModel;
    protected $commonTasks;
    protected $admin;
    protected $adminModel;


    // ================Global variables to store Amount collected in all regions==============

    protected $scalesCollection;
    protected $fuelPumpCollection;
    protected $prePackageCollection;
    protected $vehicleTankCollection;
    protected $lorriesCollection;
    protected $bulkStorageTankCollection;
    protected $fixedStorageTankCollection;
    protected $flowMeterCollection;
    protected $waterMeterCollection;
    protected $appRequest;
    protected $email;
    protected $token;
    protected $collectionCenter;

    protected $userModel;
    protected $sms;




    public function __construct()
    {
        $this->token = csrf_hash();
        $this->email = \Config\Services::email();
        $this->appRequest = service('request');
        // helper(['text','format', 'form', 'array', 'regions', 'date', 'emailTemplate', 'image']);
        $this->commonTasks     = new CommonTasksLibrary;
        $this->session         = session();
        $this->adminModel    = new AdminModel();
        $this->profileModel    = new ProfileModel();
        // $this->scaleModel      = new scaleModel();
        $this->billModel      = new BillModel();
        $this->fuelPumpModel   = new FuelPumpModel();
        $this->prePackageModel = new prePackageModel();

        $this->lorriesModel    = new LorriesModel();
        $this->vtcModel        = new VtcModel();
        $this->bstModel        = new BulkStorageTankModel();
        $this->fstModel        = new FixedStorageTankModel();
        $this->flowMeterModel  = new FlowMeterModel();
        $this->waterMeterModel = new WaterMeterModel();
        // $this->uniqueId        = $this->session->get('loggedUser');
        $this->uniqueId        = auth()->getUser()->unique_id;

        $this->collectionCenter = $this->session->get('collectionCenter');
        $this->user = auth()->user();
        $this->city            = $this->session->get('city');

        $this->userModel = new UsersModel();
        $this->sms = new SmsLibrary();

        helper(setting('App.helpers'));
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    public function index()
    {


        $data['page'] = [
            'title'   => 'Users',
            'heading' => 'Users'
        ];


        $data['user'] = auth()->user();





        $data['admin'] = $this->admin;
        $data['location'] = $this->city;
        $location = $this->city;
        $uniqueId = $this->uniqueId;



        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.CreatedAt >=' => financialYear()->startDate,
            'wma_bill.CreatedAt <=' => financialYear()->endDate
        ];

        $collection = array_map(function ($data) {
            $report = $data;
            $report->billItems = $this->billModel->fetchBillItems($data->BillId);
            return $report;
        }, $this->billModel->getReportData($params, '', []));



        $data['vtv'] = array_filter($collection, fn($data) => $data->Activity == 'vtv',);
        $data['sbl'] = array_filter($collection, fn($data) => $data->Activity == 'sbl',);
        $data['waterMeter'] = array_filter($collection, fn($data) => $data->Activity == 'waterMeter',);
        $data['prePackage'] = array_filter($collection, fn($data) => $data->Activity == 'prepackage',);

        return view('Pages/dashBoardEntry', $data);
        // return view('Pages/dashboard', $data);
    }

    // ================get all data for an Api==============
    public function analytics()
    {

        $data = [];
        $params = [
            'PayCntrNum !=' => '',
            'wma_bill.CreatedAt >=' => financialYear()->startDate,
            'wma_bill.CreatedAt <=' => financialYear()->endDate
        ];



        $collection = array_map(function ($data) {
            $report = $data;
            $report->billItems = $this->billModel->fetchBillItems($data->BillId);
            return $report;
        }, $this->billModel->getReportData($params, '', []));



        $vtv = array_filter($collection, fn($data) => $data->Activity == 'vtv',);
        $sbl = array_filter($collection, fn($data) => $data->Activity == 'sbl',);
        $waterMeter = array_filter($collection, fn($data) => $data->Activity == 'waterMeter',);
        $prePackage = array_filter($collection, fn($data) => $data->Activity == 'prepackage',);

        // echo json_encode($params);
        // exit;

        array_push($data, $vtv, $prePackage, $waterMeter, $sbl);




        if ($data) {

            return $this->response->setJson($data);
        } else {
            return $this->response->setJson('No data found');
        }
    }






    public function getSingleUser()
    {
        $id = $this->getVariable('id');
        $user = $this->userModel->where(['unique_id' => $id])->first();

        return $this->response->setJSON([
            'data' => $user,
            'email' => $user->email,
            'group' => $user->getGroups()[0],
            'permissions' => $user->getPermissions(),
            'token' => $this->token
        ]);
    }

    public function checkEmail()
    {
        $email =  $this->appRequest->getVar('email');
        $request =  $this->profileModel->checkEmail($email);

        if ($request) {
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Email Is Already Taken!!',
                'token' => $this->token,

            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'msg' => 'Email Available',
                'token' => $this->token,

            ]);
        }
    }

    public function createUserAccount()
    {

        $uniqueId = randomString();
        $permissions =  $this->getVariable('permissions') ?? ['bill.access'];
        $group = $this->getVariable('userGroup');

        $firstName = $this->getVariable('first_name');
        $lastName = $this->getVariable('last_name');




        // Validate here first, since some things,
        // like the password, can only be validated properly here.
        $rules = $this->getValidationRules();
        $userData = [
            'first_name' => $this->getVariable('first_name'),
            'last_name' => $this->getVariable('last_name'),
            'username' => $firstName . ' ' . $lastName,
            'collection_center' => $this->getVariable('collection_center'),
            'email' => $this->getVariable('email'),
            'password' => randomString(),
            'unique_id' => $uniqueId,



        ];



        if (!$this->validate($rules)) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' => 'Something Went Wrong',
                'errors' => $this->validator->getErrors(),
                'token' => $this->token

            ]);
        }




        $users = new UsersModel();



        // Save the user
        $user  = $this->getUserEntity();



        //  $user->fill($userData);
        $user->fill($userData);







        try {
            $users->save($user);
        } catch (ValidationException $e) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' => 'Something Went Wrong',
                'errors' => $users->errors(),
                'token' => $this->token

            ]);
        }



        // To get the complete user object with ID, we need to get from the database
        $user = $users->findById($users->getInsertID());

        // Add to default group
        $user->addGroup($group);
        // Grant permissions
        if (!empty($permissions))  $user->addPermission(...$permissions);

        //sending password update email
        $userId = $user->unique_id;
        $resetToken = randomString();

        $data['user'] = (object)[
            'id' => $userId,
            'name' => $user->username,
            'contact' => 1,
            'resetToken' => $resetToken,
            'greetings' => 'Welcome',
            'msg' => 'Your account has been created',
        ];
        $message = view('Pages/EmailTemplate', $data);
        $subject = 'SETTING UP ACCOUNT PASSWORD';

        // ================Email configurations==============
        $this->email->setTo($user->email);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);
        if ($this->email->send()) {
            return  $this->response->setJSON([
                'status' => 1,
                'msg' => 'User Registered Successfully',
                'errors' => [],
                'token' => $this->token,
                'email' => $user->email

            ]);
        } else {
            $link = base_url("user/activateAccount/0/$resetToken/$userId");
            $text = "Hello $user->username  Please use the link to activate your account, the link will expire in 5 minutes. $link  ";
            (new SmsLibrary())->sendSms($user->phone_number, $text);
        }


        $this->userModel->storeToken([
            'userId' => $userId,
            'token' => $resetToken
        ]);
    }


    public function updateUser()
    {

        if ($this->request->getMethod() == 'POST') {

            $id = $this->getVariable('id');
            $user = $this->userModel->where(['unique_id' => $id])->first();
            $permissions = $this->getVariable('permissions');
            $userGroup = $this->getVariable('userGroup');
            $userData = [

                'first_name' => $this->getVariable('firstName'),
                'last_name' => $this->getVariable('lastName'),
                'username' => $this->getVariable('firstName') . ' ' . $this->getVariable('lastName'),
                'collection_center' => $this->getVariable('collectionCenter'),

                'email' => $this->getVariable('email'),

            ];



            // return  $this->response->setJSON([
            //     'id' => $id,
            //     'user' => $user,
            //     'group' => $userGroup,
            //     'permissions' => $permissions,
            // ]);

            // exit;


            if (!empty($permissions)) $user->syncPermissions(...$permissions);
            $user->syncGroups($userGroup);


            $user->fill($userData);
            $request = $this->userModel->save($user);
            if ($request) {


                return $this->response->setJSON([
                    'status' => 'ok',
                    'msg' => 'User Updated Successfully',
                    'token' => $this->token,

                ]);
            }
        }
    }


    public function usersPage()
    {
        $data = [];
        $data['validation'] = null;
        $data['page'] = [
            'title'   => 'Users',
            'heading' => 'Admin | Users'
        ];




        $data['user'] = auth()->user();
        $data['admin'] = $this->admin;
        $data['location'] = $this->city;
        $uniqueId = $this->uniqueId;


        // WMA-MIS Users (Practitioners/Admin) - From Default DB (vessel_discharge)
        $data['users'] = $this->userModel->select('users.*, centerName')
            ->join('collectioncenter', 'collectioncenter.centerNumber = users.collection_center', 'left')
            ->where('user_type', 'practitioner')
            ->findAll();

        // Connect to the OSA App database for other users
        $osaDb = \Config\Database::connect('osa');

        // OSA Users (License Applicants) - JOIN with personal info and auth_identities for full data
        $data['osaUsers'] = $osaDb->table('license_users lu')
            ->select('lu.id, lu.uuid, lu.username, lu.user_type, lu.phone_number, lu.region, lu.collection_center, lu.active,
                      CONCAT(COALESCE(ppi.first_name,""), " ", COALESCE(ppi.last_name,"")) AS full_name,
                      ai.secret AS email')
            ->join('practitioner_personal_infos ppi', 'ppi.user_uuid = lu.uuid', 'left')
            ->join('auth_identities ai', 'ai.user_id = lu.id AND ai.type = "email_password"', 'left')
            ->get()->getResult();
            
        // Pattern Users - all data is already in pattern_users table
        $data['patternUsers'] = $osaDb->table('pattern_users pu')
            ->select('pu.*, pu.username AS full_name')
            ->get()->getResult();
            
        // Customer Users - all data is already in customer_users table
        $data['customerUsers'] = $osaDb->table('customer_users cu')
            ->select('cu.*, cu.username AS full_name')
            ->get()->getResult();
        $data['permissions'] = array_keys(setting('AuthGroups.permissions'));
        $data['groups'] = setting('AuthGroups.groups');



        // printer($grant);


        $data['centers'] = $this->commonTasks->collectionCenters();



        return view('Pages/admin/users', $data);
    }

    public function getUsers()
    {
        return $this->response->setJSON([
            'users' => $this->userModel->findAll(),
            'token' => $this->token
        ]);
    }


    public function changeStatus()
    {
        $id = $this->getVariable('id');
        $status = $this->getVariable('status');
        $userStatus = '';

        if ($status == 'active') {
            $userStatus .= 'inactive';
        } else if ($status == 'inactive') {
            $userStatus .= 'active';
        }

        $request = $this->adminModel->changeStatus($id, $status);

        if ($request) {
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Account Status Changed',
                'token' => $this->token,

            ]);
        }
    }



    public function activateAccount($id)
    {
        $osaDb = \Config\Database::connect('osa');

        // Check OSA database tables first (license_users, pattern_users, customer_users)
        $tables = ['license_users', 'pattern_users', 'customer_users'];
        $found  = false;
        foreach ($tables as $table) {
            $count = $osaDb->table($table)->where('uuid', $id)->countAllResults();
            if ($count > 0) {
                $osaDb->table($table)->where('uuid', $id)->update([
                    'active'     => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            // Fall back to WMA-MIS userModel (vessel_discharge.users)
            $user = $this->userModel->where(['unique_id' => $id])->first();
            if ($user) {
                $user->activate();
            }
        }

        $this->session->setFlashdata('Success', 'Account Activated Successfully');
        return redirect()->to('/admin/users');
    }

    public function deactivateAccount($id)
    {
        $osaDb = \Config\Database::connect('osa');

        // Check OSA database tables first
        $tables = ['license_users', 'pattern_users', 'customer_users'];
        $found  = false;
        foreach ($tables as $table) {
            $count = $osaDb->table($table)->where('uuid', $id)->countAllResults();
            if ($count > 0) {
                $osaDb->table($table)->where('uuid', $id)->update([
                    'active'     => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            // Fall back to WMA-MIS userModel (vessel_discharge.users)
            $user = $this->userModel->where(['unique_id' => $id])->first();
            if ($user) {
                $user->deactivate();
            }
        }

        $this->session->setFlashdata('Success', 'Account Deactivated Successfully');
        return redirect()->to('/admin/users');
    }



    // ====================================================
    // OSA App User Edit Methods (operate on osa_app DB)
    // ====================================================

    /**
     * GET /admin/editOsaUser/:uuid — Returns user data as JSON for the edit modal
     */
    public function editOsaUser($uuid)
    {
        $osaDb = \Config\Database::connect('osa');
        $user = $osaDb->table('license_users lu')
            ->select('lu.id, lu.uuid, lu.username, lu.phone_number, lu.region, lu.active, ai.secret AS email,
                      ppi.first_name, ppi.second_name, ppi.last_name, ppi.nationality, ppi.identity_number,
                      ppi.gender, ppi.dob, ppi.district, ppi.ward, ppi.street, ppi.phone')
            ->join('auth_identities ai', 'ai.user_id = lu.id AND ai.type = "email_password"', 'left')
            ->join('practitioner_personal_infos ppi', 'ppi.user_uuid = lu.uuid', 'left')
            ->where('lu.uuid', $uuid)
            ->get()->getRow();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }
        return $this->response->setJSON($user);
    }

    /**
     * POST /admin/saveOsaUser — Save edited OSA user fields
     */
    public function saveOsaUser()
    {
        $osaDb = \Config\Database::connect('osa');
        $uuid  = $this->request->getPost('uuid');
        $user  = $osaDb->table('license_users')->where('uuid', $uuid)->get()->getRow();

        if (!$user) {
            $this->session->setFlashdata('Error', 'User not found.');
            return redirect()->to('/admin/users#osa-users');
        }

        // Update license_users (phone + region)
        $osaDb->table('license_users')->where('uuid', $uuid)->update([
            'phone_number' => $this->request->getPost('phone_number'),
            'region'       => $this->request->getPost('region'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        // Update email in auth_identities
        $newEmail = $this->request->getPost('email');
        if ($newEmail) {
            $osaDb->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->update(['secret' => $newEmail]);
        }

        // Update practitioner_personal_infos
        $personalExists = $osaDb->table('practitioner_personal_infos')->where('user_uuid', $uuid)->countAllResults();
        $personalData = [
            'first_name'      => $this->request->getPost('first_name'),
            'second_name'     => $this->request->getPost('second_name'),
            'last_name'       => $this->request->getPost('last_name'),
            'nationality'     => $this->request->getPost('nationality'),
            'identity_number' => $this->request->getPost('identity_number'),
            'gender'          => $this->request->getPost('gender'),
            'dob'             => $this->request->getPost('dob') ?: null,
            'region'          => $this->request->getPost('region'),
            'district'        => $this->request->getPost('district'),
            'ward'            => $this->request->getPost('ward'),
            'street'          => $this->request->getPost('street'),
            'phone'           => $this->request->getPost('phone_number'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        if ($personalExists) {
            $osaDb->table('practitioner_personal_infos')->where('user_uuid', $uuid)->update($personalData);
        } else {
            $personalData['user_uuid'] = $uuid;
            $personalData['created_at'] = date('Y-m-d H:i:s');
            $osaDb->table('practitioner_personal_infos')->insert($personalData);
        }

        $this->session->setFlashdata('Success', 'User updated successfully.');
        return redirect()->to('/admin/users#osa-users');
    }

    /**
     * GET /admin/editPatternUser/:uuid — Returns pattern user data as JSON
     */
    public function editPatternUser($uuid)
    {
        $osaDb = \Config\Database::connect('osa');
        $user  = $osaDb->table('pattern_users')->where('uuid', $uuid)->get()->getRow();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }
        return $this->response->setJSON($user);
    }

    /**
     * POST /admin/savePatternUser — Save edited Pattern user fields
     */
    public function savePatternUser()
    {
        $osaDb = \Config\Database::connect('osa');
        $uuid  = $this->request->getPost('uuid');

        $osaDb->table('pattern_users')->where('uuid', $uuid)->update([
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'region'       => $this->request->getPost('region'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->session->setFlashdata('Success', 'Pattern user updated successfully.');
        return redirect()->to('/admin/users#pattern-users');
    }

    /**
     * GET /admin/editCustomerUser/:uuid — Returns customer user data as JSON
     */
    public function editCustomerUser($uuid)
    {
        $osaDb = \Config\Database::connect('osa');
        $user  = $osaDb->table('customer_users')->where('uuid', $uuid)->get()->getRow();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }
        return $this->response->setJSON($user);
    }

    /**
     * POST /admin/saveCustomerUser — Save edited Customer user fields
     */
    public function saveCustomerUser()
    {
        $osaDb = \Config\Database::connect('osa');
        $uuid  = $this->request->getPost('uuid');

        $osaDb->table('customer_users')->where('uuid', $uuid)->update([
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'region'       => $this->request->getPost('region'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->session->setFlashdata('Success', 'Customer user updated successfully.');
        return redirect()->to('/admin/users#customer-users');
    }

    public function resetPassword()
    {


        try {
            $id = $this->getVariable('id');
            $user = $this->userModel->where(['unique_id' => $id])->first();
            $resetToken = randomString();


            $to = $user->email;
            $subject = 'RESETTING ACCOUNT  PASSWORD';

            // $message = emailTemplate($name, $id, 'Hello', 'Your password has been reset');
            $data['user'] = (object)[
                'id' => $id,
                'name' => $user->username,
                'contact' => 0,
                'resetToken' => $resetToken,
                'greetings' => 'Hello',
                'msg' => 'Your password has been reset',
            ];

            $message = view('Pages/EmailTemplate', $data);
            //$message = 'hello world';


            // ================Email configurations==============
            $this->email->setTo($to);
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            if ($this->email->send()) {
                $this->userModel->storeToken([
                    'userId' => $id,
                    'token' => $resetToken
                ]);
                return $this->response->setJSON([
                    'status' => 1,
                    'msg' => 'Password Is Reset Successfully',
                    'token' => $this->token,


                ]);

            } else {
                $userId = $user->unique_id;

                $link = base_url("user/activateAccount/0/$resetToken/$userId");
                $text = "Hello $user->username  Please use the link to activate your account, the link will expire in 5 minutes. $link  ";
                (new SmsLibrary())->sendSms($user->phone_number, $text);

                $this->userModel->storeToken([
                    'userId' => $userId,
                    'token' => $resetToken
                ]);
              
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Email  Not Sent, A Message Sent Instead',
                    'token' => $this->token,
                ]);
            }

           
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response)->setStatusCode(500);
    }


    public function settlePayments()
    {
        $db = \Config\Database::connect();



        // Get reconciliation data
        $recon = $db->table('reconciliation')
            ->select('reconciliation.*,SpBillId as BillId, BillCtrNum as PayCtrNum, TrxDtTm as date')
            ->get()
            ->getResultArray(); // Ensure results are in array format

        // Get payment data
        $payments = $db->table('bill_payment')
            ->select('BillId, PayCtrNum, TrxDtTm as date')

            ->get()
            ->getResultArray(); // Ensure results are in array format



        // Custom comparison function based on 'BillId' and 'PayCtrNum'
        $compareFunction = function ($a, $b) {
            // if ($a['BillId'] != $b['BillId']) {
            //     return $a['BillId'] <=> $b['BillId'];
            // } else {
            // }
            return $a['PayCtrNum'] <=> $b['PayCtrNum'];
        };


        // Get elements in $recon that are not in $payments
        $unsettledPayments = array_udiff($recon, $payments, $compareFunction);

        $paymentData = array_map(function ($payment) {
            $bill = (new BillModel())->getBill($payment['BillCtrNum']);
            $transaction =  [
                'BillId' => $bill->BillId,
                'PayCtrNum' => $payment['BillCtrNum'],
                'TrxId' => $payment['pspTrxId'],
                'SpCode' => 'SP419',
                'PayRefId' => $payment['PayRefId'],
                'BillAmt' =>  $bill->BillAmt,
                'PaidAmt' =>   $payment['PaidAmt'],
                'BillPayOpt' => $bill->BillPayOpt,
                'CCy' => $bill->Ccy,
                'TrxDtTm' => $payment['TrxDtTm'],
                'UsdPayChnl' => $payment['UsdPayChnl'],
                'PyrCellNum' =>  $bill->PyrCellNum,
                'PyrEmail' => $bill->PyrEmail,
                'PyrName' => $bill->PyrName,
                'PspReceiptNumber' => $payment['pspTrxId'],
                'PspName' => $payment['PspName'],
                'CtrAccNum' => $payment['CtrAccNum'],
            ];
            return $transaction;
        }, $unsettledPayments);

        foreach ($paymentData as $data) {
            $this->processPayment($data);
        }

        $numbers = '255659851709,255767991300,255629273164';
        $qty = count($paymentData);
        $date = date('d-m-Y H:i:s');

        if ($qty > 0) {
            $msg = "($qty) Unsettled Transactions Found  And Settled  Date: $date";
            $this->sms->sendSms($numbers, $msg);
        } else {
            $msg = "($qty) NO Transactions Found   Date: $date";
            $this->sms->sendSms('255659851709', $msg);
        }
        echo $msg;
    }




    public function processPayment($data)
    {


        // $data = $array['Gepg']['gepgPmtSpInfo']['PymtTrxInf'];
        $billId = $data['BillId'];
        //get amount already paid for partial payments
        $getPaidSum = $this->billModel->getPaymentAmounts($billId);
        //if no amount paid make already paid 0
        $alreadyPaid = $getPaidSum[0]->PaidAmt ?? 0;
        //current paid amount from the user
        $currentPayment = $data['PaidAmt'];
        //sum up amount already paid and the current paid amount
        $updatedAmount = $alreadyPaid + $currentPayment;


        $paymentOption = $data['BillPayOpt'];


        //the bill amount
        $billedAmount =  $data['BillAmt'];

        //calculating the amount of debt left
        $debt = $billedAmount - $updatedAmount;
        $receiptNumber = $data['PspReceiptNumber'];
        $payerNumber = $data['PyrCellNum'];



        $controlNumber = $data['PayCtrNum'];

        $payment = [
            'TrxId' => $data['TrxId'],
            'SpCode' => $data['SpCode'],
            'PayCtrNum' => $data['PayCtrNum'],
            'PayRefId' => $data['PayRefId'],
            'BillId' => $billId,
            'BillAmt' =>  $billedAmount,
            'PaidAmt' =>   $currentPayment,
            'clearedAmount' =>   $updatedAmount,
            'BillPayOpt' => $paymentOption,
            'CCy' => $data['CCy'],
            'TrxDtTm' => $data['TrxDtTm'],
            'UsdPayChnl' => $data['UsdPayChnl'],
            'PyrCellNum' =>  $payerNumber,
            'PyrEmail' => $data['PyrEmail'],
            'PyrName' => $data['PyrName'],
            'PspReceiptNumber' => $receiptNumber,
            'PspName' => $data['PspName'],
            'CtrAccNum' => $data['CtrAccNum'],
        ];





        // get collection center number from the bill using billId
        // get collection center number from the bill using billId
        // $center = $this->billModel->getCollectionCenter($billId)->CollectionCenter;
        $center = 'Wakala Wa Vipimo';
        // $centerName = (new ProfileModel())->findCollectionCenter($center)->centerName;
        $centerName = $center;


        $billData = $this->billModel->getAmountPaid($controlNumber);
        if ($paymentOption == 2) {
            //get available amount and add the amount paid to it
            $amount = $billData->PaidAmount +  $data['PaidAmt'];

            if ($amount == $data['BillAmt'] || $amount > $data['BillAmt']) {
                $PaymentStatus = 'Paid';
            } else {
                $PaymentStatus = 'Partial';
            }
        } else {

            $PaymentStatus =   $data['PaidAmt'] >= $billedAmount  ? 'Paid' : 'Partial';
        }


        //parameter for sms notification
        $textParams = (object)[
            'center' => $centerName,
            'amount' => $currentPayment,
            'debt' => $debt < 0 ? 0 : $debt,
            'controlNumber' => (int)$controlNumber,
            'receiptNumber' => $receiptNumber

        ];

        $paymentExist = $this->billModel->verifyPaymentExistence([
            'PayRefId' => $data['PayRefId'],
            'PspReceiptNumber' => $receiptNumber,

        ]);




        if (empty($paymentExist)) {

            //save payment to the database from GEPG
            $payment['CenterNumber'] = $billData->CollectionCenter;
            $this->billModel->savePayment($payment);





            //update bill status and paid amount
            $this->billModel->updateBill($controlNumber, [
                'PaymentStatus' => $PaymentStatus,
                'PaidAmount' => $updatedAmount,
            ]);
        }


        // $this->sms->sendSms($payerNumber, paymentTextTemplate($textParams));

        //signing ack and send back to GePG

    }
}
