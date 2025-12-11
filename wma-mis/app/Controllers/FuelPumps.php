<?php

namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\FuelPumpModel;
use App\Models\ProfileModel;
use \CodeIgniter\Validation\Rules;
//use \CodeIgniter\Models\PumpModel;


class FuelPumps extends BaseController
{
        public $uniqueId;
        public $managerId;
        public $role;
        public $city;
        public $pumpModel;
        public $session;
        public $profileModel;
        public $CommonTasks;
        public function __construct()
        {
                $this->pumpModel = new FuelPumpModel();
                $this->profileModel = new ProfileModel();
                $this->session = session();
                $this->uniqueId = $this->session->get('loggedUser');
                $this->managerId = $this->session->get('manager');
                $this->role = $this->profileModel->getRole($this->uniqueId)->role;
                $this->city = $this->session->get('city');
                $this->CommonTasks
                        = new CommonTasksLibrary();
                helper(['form', 'array', 'regions']);
        }
        // A method for Pumps
        public function newPump()
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                        
                      
                        "petrolstation"      => "required",
                        "product"            => "required",
                        "pumptype"           => "required",
                        "pumpcapacity"       => "required|numeric",
                        "numberofdispensers" => "required|numeric",
                        "status"             => "required",
                        // "stickernumber"      => "required",
                        // "controlnumber"      => "required",

                ];
                $data['page'] = [
                        "title"   => "Pump",
                        "heading" => "New Pump"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];
                $data['products'] = [
                        'Petrol And Diesel',
                        'Petrol',
                        'Diesel',
                        'Kerosene',
                        'Other Oil',
                ];



                $data['pumps'] = $this->CommonTasks->fuelPumps();

                if ($this->request->getMethod() == 'POST') {
                        if ($this->validate($rules)) {


                                $results = $this->request->getVar('status', FILTER_SANITIZE_STRING);
                                $amount = 0;
                                $controlNumber = '';
                                $filePath = '';
                                $report = '';
                                $payment = '';
                                switch ($results) {
                                        case 'Pass':
                                                $amount += $this->request->getVar('passamount', FILTER_SANITIZE_NUMBER_INT);
                                                $controlNumber .= $this->request->getVar('passcontrolnumber', FILTER_SANITIZE_STRING);
                                                $payment .= $this->request->getVar('pass-payment', FILTER_SANITIZE_STRING);
                                               

                                                break;
                                        case 'Rejected':
                                                $amount += $this->request->getVar('rejectedamount', FILTER_SANITIZE_NUMBER_INT);
                                                $controlNumber .= $this->request->getVar('rejectedcontrolnumber', FILTER_SANITIZE_STRING);
                                                $payment .= $this->request->getVar('rejection-payment', FILTER_SANITIZE_STRING);
                                              

                                                break;
                                        case 'Condemned':
                                              
                                                break;

                                        default:
                                                # code...
                                                break;
                                }
                                $pumpData = [
                                        "hash" => md5(str_shuffle('abcdefghijklmnopqqrtuvwzyz0123456789')),
                                        "customer_hash" => $this->request->getVar('customer_hash', FILTER_SANITIZE_STRING),
                                      

                                        "petrol_station" => $this->request->getVar('petrolstation', FILTER_SANITIZE_STRING),
                                        "product" => $this->request->getVar('product', FILTER_SANITIZE_STRING),
                                        "pump_type" => $this->request->getVar('pumptype', FILTER_SANITIZE_STRING),
                                        "capacity" => $this->request->getVar('pumpcapacity', FILTER_SANITIZE_STRING),
                                        "dispensers" => $this->request->getVar('numberofdispensers', FILTER_SANITIZE_STRING),
                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "sticker_number" => $this->request->getVar('stickernumber', FILTER_SANITIZE_STRING),
                                        "control_number" => $controlNumber,
                                        "amount" => $amount,
                                        "payment" => $payment,
                                      
                                       
                                        "unique_id" => $this->uniqueId
                                ];
                                $status =   $this->pumpModel->savePumpData($pumpData);

                                if ($status) {
                                        $this->session->setFlashdata('success', 'Data Inserted Successfully <i class="fal fa-smile-wink"></i>');
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
                $managerId = $this->managerId;
                $role = $this->role;
                $city = $this->city;

                $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                $data['role'] = $role;
                return view('pages/FuelPumps/newPump', $data);
        }

        public function listRegisteredFuelPumps()
        {

                $data['page'] = [
                        "title"   => " Fuel Pump List",
                        "heading" => "Registered Fuel Pumps"
                ];



                $uniqueId = $this->uniqueId;
                $managerId = $this->managerId;
                $role = $this->role;
                $city = $this->city;

                if ($role == 1) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                        $data['pumpResults'] = $this->pumpModel->getRegisteredPumps($uniqueId);
                        $data['role'] = $role;
                        return view('pages/FuelPumps/PumpsList', $data);
                } elseif ($role == 2) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($managerId);
                        $data['pumpResults'] = $this->pumpModel->getAllPumps($city);
                        $data['role'] = $role;
                        return view('pages/FuelPumps/PumpsList', $data);
                }
        }

        // delete a record from a database
        public function deleteFuelPump($id)
        {

                $this->pumpModel->deleteRecord($id);
                $this->session->setFlashdata('Success', 'Record Deleted Successfully');
                return redirect()->to('/listFuelPumps');
        }

        // Edit a record from a database
        public function editFuelPump($id)
        {
                $data = [];
                $data['record'] =  $this->pumpModel->editRecord($id);
                $data['validation'] = null;

                $data['page'] = [
                        "title"   => "Edit Fuel Pump Record",
                        "heading" => "Edit Fuel Pump Record "
                ];
                $data['statusResult'] = ['Pass', 'Rejected', 'Condemned'];
                $data['genderValues'] = ['Male', 'Female'];
                $data['payments'] = ['Paid', 'Pending'];
                $data['products'] = [
                        'Petrol And Diesel',
                        'Petrol',
                        'Diesel',
                        'Kerosene',
                        'Other Oil',
                ];



                $data['pumps'] = [
                        'Gilbarco',
                        'ChangLong',
                        'Tokheim',
                        'Tatsuno',
                        'EgoStar',
                        'Piusi',
                        'Zecheng',
                        'L & T',
                        'Mekser',

                ];

                $uniqueId = $this->uniqueId;
                $managerId = $this->managerId;
                $role = $this->role;
                $city = $this->city;
                $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);

                if ($role == 1) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                        $data['role'] = $role;
                        return view('pages/FuelPumps/editPump', $data);
                } elseif ($role == 2) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($managerId);
                        $data['role'] = $role;
                        return view('pages/FuelPumps/editPump', $data);
                }
        }

        public function updateFuelPump($id)
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                       
                        "petrolstation"      => "required",
                        "product"            => "required",
                        "pumptype"           => "required",
                        "pumpcapacity"       => "required|numeric",
                        "numberofdispensers" => "required|numeric",
                        "status"             => "required",

                        // "stickernumber"      => "required",
                        // "controlnumber"      => "required",
                        'condemnationnote' => 'max_size[condemnationnote,3072]|ext_in[condemnationnote,pdf,jpg,png,jpeg]'


                ];
                $data['page'] = [
                        "title"   => "Pump",
                        "heading" => "New Pump"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];
                $data['products'] = [
                        'Petrol And Diesel',
                        'Petrol',
                        'Diesel',
                ];

                $data['pumps'] = $this->CommonTasks
                        ->fuelPumps();



                // ================Updating==============
                if ($this->request->getMethod() == 'POST') {
                        $results = $this->request->getVar('status', FILTER_SANITIZE_STRING);
                        $amount = 0;
                        $controlNumber = '';
                       
                        $payment = '';
                        switch ($results) {
                                case 'Pass':
                                        $amount += $this->request->getVar('passamount', FILTER_SANITIZE_NUMBER_INT);
                                        $controlNumber .= $this->request->getVar('passcontrolnumber', FILTER_SANITIZE_STRING);
                                        $payment .= $this->request->getVar('pass-payment', FILTER_SANITIZE_STRING);
                                      

                                        break;
                                case 'Rejected':
                                        $amount += $this->request->getVar('rejectedamount', FILTER_SANITIZE_NUMBER_INT);
                                        $controlNumber .= $this->request->getVar('rejectedcontrolnumber', FILTER_SANITIZE_STRING);
                                        $payment .= $this->request->getVar('rejection-payment', FILTER_SANITIZE_STRING);
                                      

                                        break;
                                case 'Condemned':
                                        break;

                                default:
                                        # code...
                                        break;
                        }
                        if ($this->validate($rules)) {
                                // return redirect()->to('dashboard');
                                $pumpData = [
                                    

                                        "petrol_station" => $this->request->getVar('petrolstation', FILTER_SANITIZE_STRING),
                                        "product" => $this->request->getVar('product', FILTER_SANITIZE_STRING),
                                        "pump_type" => $this->request->getVar('pumptype', FILTER_SANITIZE_STRING),
                                        "capacity" => $this->request->getVar('pumpcapacity', FILTER_SANITIZE_STRING),
                                        "dispensers" => $this->request->getVar('numberofdispensers', FILTER_SANITIZE_STRING),
                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "sticker_number" => $this->request->getVar('stickernumber', FILTER_SANITIZE_STRING),
                                        "control_number" => $controlNumber,
                                        "amount" => $amount,
                                        "payment" => $payment,
                                        
                                        // "unique_id" => $this->uniqueId
                                ];
                                $status =   $this->pumpModel->updatePumpData($pumpData, $id);

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




                return redirect()->to('/listFuelPumps');
        }
}