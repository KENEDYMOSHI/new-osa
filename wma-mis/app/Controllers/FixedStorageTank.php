<?php

namespace App\Controllers;

use App\Models\FixedStorageTankModel;
use App\Models\ProfileModel;
use \CodeIgniter\Validation\Rules;
use App\Libraries\CommonTasksLibrary;
//use \CodeIgniter\Models\FixedStorageTankModel;


class FixedStorageTank extends BaseController
{
        public $uniqueId;
        public $managerId;
        public $role;
        public $city;
        public $FixedStorageTankModel;
        public $session;
        public $profileModel;
        public $CommonTasks;
        public function __construct()
        {
                $this->FixedStorageTankModel = new FixedStorageTankModel();
                $this->profileModel = new ProfileModel();
                $this->session = session();
                $this->uniqueId = $this->session->get('loggedUser');
                $this->managerId = $this->session->get('manager');
                $this->role = $this->profileModel->getRole($this->uniqueId)->role;
                $this->city = $this->session->get('city');
                $this->CommonTasks = new CommonTasksLibrary();
                helper(['form', 'array', 'date', 'regions']);
        }
        // ================Adding FixedStorageTank information to database ==============
        public function addFixedStorageTank()
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                    
                        "fillingstation" => "required",
                        "numberoftanks"  => "required",
                        "tankcapacity"   => "required",
                        "status"         => "required",
                        // "controlnumber"  => "required",
                        // "amount"         => "required",



                ];
                $data['page'] = [
                        "title"   => "Fixed Storage Tanks",
                        "heading" => "Fixed Storage Tanks"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];


                if ($this->request->getMethod() == 'POST') {
                        if ($this->validate($rules)) {
                                $results = $this->request->getVar('status', FILTER_SANITIZE_STRING);
                                $amount = 0;
                                $controlNumber = '';
                               
                                $payment = '';
                                switch ($results) {
                                        case 'Valid':
                                                $amount += $this->request->getVar('passamount', FILTER_SANITIZE_NUMBER_INT);
                                                $controlNumber .= $this->request->getVar('passcontrolnumber', FILTER_SANITIZE_STRING);
                                                $payment .= $this->request->getVar('pass-payment', FILTER_SANITIZE_STRING);
                                                

                                                break;


                                        default:
                                                # code...
                                                break;
                                }



                                $FixedStorageTankData = [
                                        "hash" => md5(str_shuffle('abcdefghijklmnopqqrtuvwzyz0123456789')),
                                        "customer_hash" => $this->request->getVar('customer_hash', FILTER_SANITIZE_STRING),
                                       

                                        "filling_station" => $this->request->getVar('fillingstation', FILTER_SANITIZE_STRING),
                                        "number_of_tanks" => $this->request->getVar('numberoftanks', FILTER_SANITIZE_STRING),
                                        "capacity" => implode(',', $this->request->getVar('tankcapacity', FILTER_SANITIZE_STRING)),
                                        "product" => implode(',', $this->request->getVar('product[]', FILTER_SANITIZE_STRING)),
                                        "calibrators" => "WMA",
                                        "next_calibration" => $this->CommonTasks->nextFiveYears($this->request->getVar('date')),
                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "remark" => $this->request->getVar('remark', FILTER_SANITIZE_STRING),
                                        "amount" => $amount,
                                        "payment" => $payment,
                                        "control_number" => $controlNumber,
                                       
                                        "unique_id" => $this->uniqueId
                                ];
                                $status = $this->FixedStorageTankModel->saveFixedStorageTankData($FixedStorageTankData);

                                if ($status) {
                                        $this->session->setFlashdata('Success', 'Data Inserted Successfully <i class="fal fa-smile-wink"></i>');
                                        return redirect()->to(current_url());
                                        // echo "<script>alert('Data Inserted');</script>";
                                } else {
                                        $this->session->setFlashdata('error', 'Fail To Insert Data Try Again');
                                }
                        } else {
                                $data['validation'] = $this->validator;
                        }
                }



                $uniqueId = $this->uniqueId;
                $role = $this->role;
                $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                $data['role'] = $role;
                return view('Pages/FixedStorageTank/addFixedStorageTank', $data);
        }

        public function listRegisteredFixedStorageTanks()
        {

                $data['page'] = [
                        "title"   => "Registered Fixed Storage Tanks",
                        "heading" => "Registered Fixed Storage Tanks"
                ];

                $uniqueId = $this->uniqueId;
                $managerId = $this->managerId;
                $role = $this->role;
                $city = $this->city;

                if ($role == 1) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                        $data['role'] = $role;
                        $data['FixedStorageTankResults'] = $this->FixedStorageTankModel->getRegisteredFixedStorageTank($uniqueId);
                } elseif ($role == 2) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($managerId);
                        $data['role'] = $role;
                        $data['FixedStorageTankResults'] = $this->FixedStorageTankModel->getAllFixedStorageTank($city);
                }

                $data['role']  = $this->role;

                return view('Pages/FixedStorageTank/listFixedStorageTank', $data);
        }

        // delete a record from a database
        public function deleteFixedStorageTank($id)
        {

                $this->FixedStorageTankModel->deleteRecord($id);
                $this->session->setFlashdata('Success', 'Record Deleted Successfully');
                return redirect()->to('/listFixedStorageTanks');
        }

        // Edit a record from a database
        public function editFixedStorageTank($id)
        {
                $data = [];
                $data['record'] = $this->FixedStorageTankModel->editRecord($id);
                $data['validation'] = null;

                $data['page'] = [
                        "title"   => "Edit FixedStorageTank Record",
                        "heading" => "Edit FixedStorageTank Record "
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];

                $uniqueId = $this->uniqueId;
                $data['role']  = $this->role;
                $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                return view('Pages/FixedStorageTank/editFixedStorageTank', $data);
        }

        public function updateFixedStorageTank($id)
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                        
                        "fillingstation" => "required",
                        "numberoftanks"  => "required",
                        "tankcapacity"   => "required",
                        "status"         => "required",
                        // "controlnumber" => "required",
                        // "amount"        => "required",
                ];
                $data['page'] = [
                        "title"   => "FixedStorageTank",
                        "heading" => "Update FixedStorageTank Details"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];



                if ($this->request->getMethod() == 'POST') {
                        if ($this->validate($rules)) {
                                $results = $this->request->getVar('status', FILTER_SANITIZE_STRING);
                                $amount = 0;
                                $controlNumber = '';
                               
                                $payment = '';
                                switch ($results) {
                                        case 'Valid':
                                                $amount += $this->request->getVar('passamount', FILTER_SANITIZE_NUMBER_INT);
                                                $controlNumber .= $this->request->getVar('passcontrolnumber', FILTER_SANITIZE_STRING);
                                                $payment .= $this->request->getVar('pass-payment', FILTER_SANITIZE_STRING);
                                          

                                                break;


                                        default:
                                                # code...
                                                break;
                                }
                                $FixedStorageTankData = [
                                      

                                        "filling_station" => $this->request->getVar('fillingstation', FILTER_SANITIZE_STRING),
                                        "number_of_tanks" => $this->request->getVar('numberoftanks', FILTER_SANITIZE_STRING),
                                        "capacity" => $this->request->getVar('tankcapacity', FILTER_SANITIZE_STRING),
                                        "calibrators" => "WMA",
                                        "next_calibration" => $this->CommonTasks->nextFiveYears($this->request->getVar('date')),
                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "remark" => $this->request->getVar('remark', FILTER_SANITIZE_STRING),
                                        "amount" => $amount,
                                        "payment" => $payment,
                                        "control_number" => $controlNumber,
                                       

                                ];
                                $status =   $this->FixedStorageTankModel->updateFixedStorageTankData($FixedStorageTankData, $id);

                                if ($status) {
                                        $this->session->setFlashdata('Success', 'Data Updated Successfully <i class="fal fa-smile-wink"></i>');

                                        // echo "<script>alert('Data Inserted');</script>";
                                } else {
                                        $this->session->setFlashdata('error', 'Fail To Update Data Try Again');
                                }
                        } else {
                                $data['validation'] = $this->validator;
                        }
                }


                $data['role']  = $this->role;

                return redirect()->to('/listFixedStorageTanks');
        }
}