<?php

namespace App\Controllers;

use App\Models\PersonalDetailsModel;


class PersonalDetails extends BaseController
{
  public $personalDetailsModel;
  public $appRequest;
  private $token;

  public function __construct()
  {
    $this->appRequest = service('request');
    $this->token = csrf_hash();
    $this->personalDetailsModel = new PersonalDetailsModel();
  }

   public function getVariable($var)
  {
   return $this->appRequest->getVar($var,FILTER_SANITIZE_SPECIAL_CHARS);
  }

  public function newCustomer()
  {

    if ($this->request->getMethod() == 'POST') {
      $customerData = [
        "hash" => md5(str_shuffle('abcdefghijklmnopqqrtuvwzyz0123456789')),
        'name' => $this->getVariable('name'),
        'region' => $this->getVariable('region'),
        'district' => $this->getVariable('district'),
        'ward' => $this->getVariable('ward'),
        'village' => $this->getVariable('village'),
        'postal_code' => $this->getVariable('postalCode'),
        'postal_address' => $this->getVariable('postalAddress'),
        'physical_address' => $this->getVariable('physicalAddress'),
        'phone_number' => $this->getVariable('phoneNumber'),

      ];


      // echo json_encode($customerData);
      // exit;

      if ($this->personalDetailsModel->registerCustomer($customerData)) {
        return $this->response->setJSON([
          'status'=> 1,
          'msg'=> 'Customer registered',
          'lastCustomer' => $this->personalDetailsModel->getLastCustomer(),
          'token'=> $this->token,

        ]);
      
      } else {
        return $this->response->setJSON([
          'status' => 0,
          'msg' => 'Something went wrong',
          'token' => $this->token,

        ]);
      }
    }
  }

  public function searchExistingCustomer()
  {
    $customers = $this->personalDetailsModel->findMatch();
    return json_encode($customers);
  }

  public function selectCustomer()
  {
    if ($this->request->getMethod() == 'POST') {
      $customerHash = $this->getVariable('customerHash');
      $query = $this->personalDetailsModel->customerDetails($customerHash);

      if ($query) {
        //echo 'Match found';
        return $this->response->setJSON([
          'data'=> $query,
          'token'=> $this->token
        ]);
      } else {
       
        return $this->response->setJSON([
          'data' => 'Noting Found',
          'token' => $this->token
        ]);
      }
    }
  }

  public function updateCustomer(){
    if($this->request->getMethod() == 'POST'){
     $hash = $this->getVariable('hash');
      $customer = [
        'name' => $this->getVariable('name'),
        'ward' => $this->getVariable('ward'),
        'region' => $this->getVariable('region'),
        'phone_number' => $this->getVariable('phoneNumber'),
        'physical_address' => $this->getVariable('physicalAddress'),
        'postal_address' => $this->getVariable('postalAddress'),
      ];

      // echo json_encode($customer);
      // exit;

      $request = $this->personalDetailsModel->updateCustomer($hash,$customer);
      if($request){
        return $this->response->setJSON([
          'status'=>1,
          'msg'=>'updated',
          'token'=>$this->token,
        ]);
   
      }
    }
  }
}