<?php

namespace App\Controllers;

use App\Models\BulkStorageTankModel;
use App\Models\ProfileModel;
use \CodeIgniter\Validation\Rules;
use App\Libraries\CommonTasksLibrary;
//use \CodeIgniter\Models\BulkStorageTankModel;


class BulkStorageTank extends BaseController
{
        public $uniqueId;
        public $managerId;
        public $role;
        public $city;
        public $BulkStorageTankModel;
        public $session;
        public $profileModel;
        public $CommonTasks;
        public function __construct()
        {
                $this->BulkStorageTankModel = new BulkStorageTankModel();
                $this->profileModel = new ProfileModel();
                $this->session = session();
                $this->uniqueId = $this->session->get('loggedUser');
                $this->managerId = $this->session->get('manager');
                $this->role = $this->profileModel->getRole($this->uniqueId)->role;
                $this->city = $this->session->get('city');
                $this->CommonTasks = new CommonTasksLibrary();
                helper(['form', 'array', 'date', 'regions']);
        }
        // ================Adding BulkStorageTank information to database ==============
        public function addBulkStorageTank()
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                        "firstname"      => "required|min_length[3]|max_length[15]",
                        "lastname"       => "required|min_length[3]|max_length[15]",
                        "gender"         => "required",
                        "city"           => "required",
                        "ward"           => "required",
                        "postal"         => "required",
                        "phone"          => "required",
                        "date"           => "required",
                        "fillingstation" => "required",
                        "numberoftanks"  => "required",
                        "tankcapacity"   => "required",
                        "status"         => "required",
                        // "controlnumber"  => "required",
                        // "amount"         => "required",



                ];
                $data['page'] = [
                        "title"   => "Bulk Storage Tanks",
                        "heading" => "Bulk Storage Tanks"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];


                if ($this->request->getMethod() == 'POST') {
                      
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



                                $bulkStorageTankData = [
                                        "hash" => md5(str_shuffle('abcdefghijklmnopqqrtuvwzyz0123456789')),
                                        "customer_hash" => $this->request->getVar('customer_hash', FILTER_SANITIZE_STRING),
                                       

                                        "filling_station" => $this->request->getVar('fillingstation', FILTER_SANITIZE_STRING),
                                        "number_of_tanks" => $this->request->getVar('numberoftanks', FILTER_SANITIZE_STRING),
                                        "capacity" => implode(',', $this->request->getVar('tankcapacity', FILTER_SANITIZE_STRING)),
                                        "product" => implode(',', $this->request->getVar('product', FILTER_SANITIZE_STRING)),
                                        "calibrators" => "WMA",
                                        "next_calibration" => $this->CommonTasks->nextFiveYears($this->request->getVar('date')),
                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "remark" => $this->request->getVar('remark', FILTER_SANITIZE_STRING),
                                        "amount" => $amount,
                                        "payment" => $payment,
                                        "control_number" => $controlNumber,
                                       
                                        "unique_id" => $this->uniqueId
                                ];
                                $status = $this->BulkStorageTankModel->saveBulkStorageTankData($bulkStorageTankData);

                                if ($status) {
                                        $this->session->setFlashdata('Success', 'Data Inserted Successfully <i class="fal fa-smile-wink"></i>');
                                        return redirect()->to(current_url());
                                        // echo "<script>alert('Data Inserted');</script>";
                                } else {
                                        $this->session->setFlashdata('error', 'Fail To Insert Data Try Again');
                                }
                        
                }



                $uniqueId = $this->uniqueId;
                $role = $this->role;
                $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                $data['role'] = $role;
                return view('Pages/BulkStorageTank/addBulkStorageTank', $data);
        }

        public function listRegisteredBulkStorageTanks()
        {

                $data['page'] = [
                        "title"   => "Registered Bulk Storage Tanks",
                        "heading" => "Registered Bulk Storage Tanks"
                ];

                $uniqueId = $this->uniqueId;
                $managerId = $this->managerId;
                $role = $this->role;
                $city = $this->city;

                if ($role == 1) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                        $data['role'] = $role;
                        $data['bulkStorageTankResults'] = $this->BulkStorageTankModel->getRegisteredBulkStorageTank($uniqueId);
                } elseif ($role == 2) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($managerId);
                        $data['role'] = $role;
                        $data['bulkStorageTankResults'] = $this->BulkStorageTankModel->getAllBulkStorageTank($city);
                }
                return view('Pages/BulkStorageTank/listBulkStorageTank', $data);
        }

        // delete a record from a database
        public function deleteBulkStorageTank($id)
        {

                $this->BulkStorageTankModel->deleteRecord($id);
                $this->session->setFlashdata('Success', 'Record Deleted Successfully');
                return redirect()->to('/listBulkStorageTanks');
        }

        // Edit a record from a database
        public function editBulkStorageTank($hash)
        {
                $data = [];
                $data['record'] = $this->BulkStorageTankModel->editRecord($hash);
                $data['validation'] = null;

                $data['page'] = [
                        "title"   => "Edit BulkStorageTank Record",
                        "heading" => "Edit BulkStorageTank Record "
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];

                $uniqueId = $this->uniqueId;
                $managerId = $this->managerId;
                $role = $this->role;


                if ($role == 1) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                        $data['role'] = $role;
                } elseif ($role == 2) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($managerId);
                        $data['role'] = $role;
                }
                return view('Pages/BulkStorageTank/editBulkStorageTank', $data);
        }

        public function updateBulkStorageTank($hash)
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                        "firstname"      => "required|min_length[3]|max_length[15]",
                        "lastname"       => "required|min_length[3]|max_length[15]",
                        "gender"         => "required",
                        "city"           => "required",
                        "ward"           => "required",
                        "postal"         => "required",
                        "phone"          => "required",
                        "date"           => "required",
                        // "fillingstation" => "required",
                        // // "numberoftanks"  => "required",
                        // // "tankcapacity"   => "required",
                        // "status"         => "required",
                        // // "controlnumber" => "required",
                        // // "amount"        => "required",
                ];
                $data['page'] = [
                        "title"   => "BulkStorageTank",
                        "heading" => "Update BulkStorageTank Details"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];



                if ($this->request->getMethod() == 'POST') {
                
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
                                $BulkStorageTankData = [
                                      

                                        "filling_station" => $this->request->getVar('fillingstation', FILTER_SANITIZE_STRING),
                                        "number_of_tanks" => $this->request->getVar('numberoftanks', FILTER_SANITIZE_STRING),
                                        "capacity" => implode(',', $this->request->getVar('tankcapacity', FILTER_SANITIZE_STRING)),
                                        "product" => implode(',', $this->request->getVar('product', FILTER_SANITIZE_STRING)),
                                        "calibrators" => "WMA",
                                        "next_calibration" => $this->CommonTasks->nextFiveYears($this->request->getVar('date')),
                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "remark" => $this->request->getVar('remark', FILTER_SANITIZE_STRING),
                                        "amount" => $amount,
                                        "payment" => $payment,
                                        "control_number" => $controlNumber,
                                     

                                ];


                                // print_r($BulkStorageTankData);
                                // exit;


                                $status =   $this->BulkStorageTankModel->updateBulkStorageTankData($BulkStorageTankData, $hash);

                                if ($status) {
                                        $this->session->setFlashdata('Success', 'Data Updated Successfully <i class="fal fa-smile-wink"></i>');

                                        // echo "<script>alert('Data Inserted');</script>";
                                } else {
                                        $this->session->setFlashdata('error', 'Fail To Update Data Try Again');
                                }
                       
                }




                return redirect()->to('/listBulkStorageTanks');
        }
}