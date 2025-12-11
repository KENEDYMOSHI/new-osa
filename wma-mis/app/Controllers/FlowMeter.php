<?php

namespace App\Controllers;

use App\Models\FlowMeterModel;
use App\Models\ProfileModel;
use \CodeIgniter\Validation\Rules;
use App\Libraries\CommonTasksLibrary;
//use \CodeIgniter\Models\FlowMeterModel;


class FlowMeter extends BaseController
{
        public $uniqueId;
        public $managerId;
        public $role;
        public $city;
        public $FlowMeterModel;
        public $session;
        public $profileModel;
        public $CommonTasks;
        public function __construct()
        {
                $this->FlowMeterModel = new FlowMeterModel();
                $this->profileModel = new ProfileModel();
                $this->session = session();
                $this->uniqueId = $this->session->get('loggedUser');
                $this->managerId = $this->session->get('manager');
                $this->role = $this->profileModel->getRole($this->uniqueId)->role;
                $this->city = $this->session->get('city');
                $this->CommonTasks = new CommonTasksLibrary();
                helper(['form', 'array', 'date', 'regions']);
        }
        // ================Adding FlowMeter information to database ==============
        public function addFlowMeter()
        {

                $data = [];
                $data['validation'] = null;
                $rules = [
                        "firstname"  => "required|min_length[3]|max_length[15]",
                        "lastname"   => "required|min_length[3]|max_length[15]",
                        "gender"     => "required",
                        "city"       => "required",
                        "ward"       => "required",
                        "postal"     => "required",
                        "phone"      => "required",
                        "date"       => "required",
                        // "oilcompany" => "required",
                        // "metertype"  => "required",
                        // "model"      => "required",
                        // "serial"     => "required",
                        // "flowrate"   => "required",
                        // "product"    => "required",
                        // "capacity"   => "required",
                        // "model"      => "required",
                        // "status"     => "required",
                        // "controlnumber"  => "required",
                        // "amount"         => "required",



                ];
                $data['page'] = [
                        "title"   => "Flow Meter",
                        "heading" => "Flow Meter"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];
                $data['products'] = ['Gas', 'Oil'];
                $data['meterTypes'] = ['Volumetric Flow Meter', 'Mass Flow Meter'];


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
                                              
                                        

                                        default:
                                                # code...
                                                break;
                                }


                                $FlowMeterData = [
                                        "hash_id" => md5(str_shuffle('abcdefghijklmnopqqrtuvwzyz0123456789')),
                                        "customer_hash" => $this->request->getVar('customer_hash', FILTER_SANITIZE_STRING),
                                        

                                        "oil_company" => $this->request->getVar('oilcompany', FILTER_SANITIZE_STRING),
                                        "flow_meter_type" => $this->request->getVar('metertype', FILTER_SANITIZE_STRING),
                                        "model_number" => $this->request->getVar('model', FILTER_SANITIZE_STRING),
                                        "serial_number" => $this->request->getVar('serial', FILTER_SANITIZE_STRING),
                                        "flow_rate" => $this->request->getVar('flowrate', FILTER_SANITIZE_STRING),
                                        "product" => $this->request->getVar('product', FILTER_SANITIZE_STRING),
                                        "standard_capacity" => $this->request->getVar('capacity', FILTER_SANITIZE_STRING),


                                        // "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        // "sticker_number" => $this->request->getVar('stickernumber', FILTER_SANITIZE_STRING),
                                        "amount" => $this->request->getVar('amount', FILTER_SANITIZE_NUMBER_INT),
                                        "payment" => $this->request->getVar('payment', FILTER_SANITIZE_NUMBER_INT),
                                        "control_number" => $this->request->getVar('controlnumber', FILTER_SANITIZE_NUMBER_INT),
                                       
                                        "unique_id" => $this->uniqueId
                                ];
                                $status = $this->FlowMeterModel->saveFlowMeterData($FlowMeterData);

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
                return view('Pages/FlowMeter/addFlowMeter', $data);
        }

        public function listRegisteredFlowMeters()
        {

                $data['page'] = [
                        "title"   => "Registered Flow Meter",
                        "heading" => "Registered Flow Meter"
                ];

                $uniqueId = $this->uniqueId;
                $managerId = $this->managerId;
                $role = $this->role;
                $city = $this->city;

                if ($role == 1) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
                        $data['role'] = $role;
                        $data['FlowMeterResults'] = $this->FlowMeterModel->getRegisteredFlowMeter($uniqueId);
                } elseif ($role == 2) {
                        $data['profile'] = $this->profileModel->getLoggedUserData($managerId);
                        $data['role'] = $role;
                        $data['FlowMeterResults'] = $this->FlowMeterModel->getAllFlowMeter($city);
                }

                return view('Pages/FlowMeter/FlowMeterList', $data);
        }

        // delete a record from a database
        public function deleteFlowMeter($id)
        {

                $this->FlowMeterModel->deleteRecord($id);
                $this->session->setFlashdata('Success', 'Record Deleted Successfully');
                return redirect()->to('/FlowMeterList');
        }

        // Edit a record from a database
        public function editFlowMeter($id)
        {
                $data = [];
                $data['record'] = $this->FlowMeterModel->editRecord($id);
                $data['validation'] = null;

                $data['page'] = [
                        "title"   => "Edit FlowMeter Record",
                        "heading" => "Edit FlowMeter Record "
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['meterTypes'] = ['Volumetric Flow Meter', 'Mass Flow Meter'];
                $data['products'] = ['Oil', 'Gas'];

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
                return view('Pages/FlowMeter/editFlowMeter', $data);
        }

        public function updateFlowMeter($id)
        {


                // echo 'hello';
                // exit;

                $data = [];
                $data['validation'] = null;
                $rules = [
                        "firstname"  => "required|min_length[3]|max_length[15]",
                        "lastname"   => "required|min_length[3]|max_length[15]",
                        "gender"     => "required",
                        "city"       => "required",
                        "ward"       => "required",
                        "postal"     => "required",
                        "phone"      => "required",
                        "date"       => "required",
                        "oilcompany" => "required",
                        "metertype"  => "required",
                        "model"      => "required",
                        "serial"     => "required",
                        "flowrate"   => "required",
                        "product"    => "required",
                        "capacity"   => "required",
                        "model"      => "required",
                        "status"     => "required",
                        // "controlnumber" => "required",
                        // "amount"        => "required",
                ];



                $data['page'] = [
                        "title"   => "FlowMeter",
                        "heading" => "Update FlowMeter Details"
                ];
                $data['statusResult'] = ['Pass', 'Rejected'];
                $data['genderValues'] = ['Male', 'Female'];




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

                                $FlowMeterData = [
                                       
                                        "oil_company" => $this->request->getVar('oilcompany', FILTER_SANITIZE_STRING),
                                        "flow_meter_type" => $this->request->getVar('metertype', FILTER_SANITIZE_STRING),
                                        "model_number" => $this->request->getVar('model', FILTER_SANITIZE_STRING),
                                        "serial_number" => $this->request->getVar('serial', FILTER_SANITIZE_STRING),
                                        "flow_rate" => $this->request->getVar('flowrate', FILTER_SANITIZE_STRING),
                                        "product" => $this->request->getVar('product', FILTER_SANITIZE_STRING),
                                        "standard_capacity" => $this->request->getVar('capacity', FILTER_SANITIZE_STRING),


                                        "status" => $this->request->getVar('status', FILTER_SANITIZE_STRING),
                                        "sticker_number" => $this->request->getVar('stickernumber', FILTER_SANITIZE_STRING),
                                        "amount" => $amount,
                                        "payment" => $payment,
                                        "control_number" => $controlNumber,
                                     

                                ];
                                // print_r($FlowMeterData);
                                // exit;
                                $status =   $this->FlowMeterModel->updateFlowMeterData($FlowMeterData, $id);

                                if ($status) {
                                        $this->session->setFlashdata('Success', 'Data Updated Successfully <i class="fal fa-smile-wink"></i>');

                                        // echo "<script>alert('Data Inserted');</script>";
                                } else {
                                        $this->session->setFlashdata('error', 'Fail To Update Data Try Again');
                                }
                        
                }




                return redirect()->to('/FlowMeterList');
        }
}