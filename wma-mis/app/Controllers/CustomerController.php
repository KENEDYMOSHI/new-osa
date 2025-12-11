<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $token;
    protected $uniqueId;
    protected $session;
    public function __construct()
    {
        $this->session = session();
        $this->token = csrf_hash();
        $this->customerModel = new CustomerModel();
        $this->uniqueId = $this->session->get('loggedUser');
    }
    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }


    public function addCustomer()
    {

     try {
        if ($this->request->getMethod() == 'POST') {

            $phone = substr($this->getVariable('phoneNumber'),1);
            $hash = randomString();
            $data = [
                "hash" => $hash,
                "name" => $this->getVariable('name'),
                "region" => $this->getVariable('region'),
                "district" => $this->getVariable('district'),
                "ward" => $this->getVariable('ward'),
                "location" => $this->getVariable('location'),
                "village" => $this->getVariable('village'),
                "physical_address" => $this->getVariable('physicalAddress'),
                "postal_address" => $this->getVariable('postalAddress'),
                "postal_code" => $this->getVariable('postalCode'),
                "phone_number" => '255' . $phone,
                "latitude" => $this->getVariable('latitude')?? '',
                "longitude" => $this->getVariable('longitude') ?? '',


                //"unique_id" => $this->uniqueId,

            ];

            // return $this->response->setJSON([
            //     'data' => $data,
            //     'token' => $this->token,

            // ]);
            // exit;
            $request = $this->customerModel->createCustomer($data);
            
            if ($request) {
                $customer = $this->customerModel->selectCustomer($hash); 
                return $this->response->setJSON([
                    'status' => 1,
                    'customer' => $this->customerTemplate($customer) ,
                    'msg' => 'Customer Added',
                    'token' => $this->token,

                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Something went wrong',
                    'token' => $this->token,

                ]);
            }
        }
        } catch (\Throwable $th) {
            return  $this->response->setJSON([
                'status' => 0,
                'msg' =>  $th->getMessage(),
                'token' => $this->token,
    
            ]);
        }
    }



    public function customerTemplate($customer){
       return <<<HTML
           <input type="text" class="form-control" name="" id="customerId" value="$customer->hash" hidden>
      
        <tr>
            <td> <b>Name</b></td>
                        <td>$customer->name</td>
                    </tr>
                    <tr>
                        <td> <b>Region </b></td>
                        <td>$customer->region</td>
                    </tr>
                    <tr>
                        <td> <b>District </b></td>
                        <td>$customer->district</td>
                    </tr>
                    <tr>
                        <td> <b>Ward </b></td>
                        <td>$customer->ward</td>
                    </tr>
                    <tr>
                        <td> <b>Physical Address </b></td>
                        <td>$customer->physical_address</td>
                    </tr>
                    <tr>
                        <td> <b>Location </b></td>
                        <td>$customer->location</td>
                    </tr>
                    
                    <tr>
                        <td><b>Postal Code</b></td>
                        <td>$customer->postal_code</td>
                    </tr>
                    <tr>
                        <td><b>Postal Address</b></td>
                        <td>$customer->postal_address</td>
                    </tr>
                    <tr>
                        <td><b>Phone Number</b></td>
                        <td>+$customer->phone_number</td>
                    </tr>
                    <tr>
                        <td><b>Map</b></td>
                        <td><button class="btn btn-primary btn-sm" onclick="openMap('$customer->latitude','$customer->longitude','$customer->name')">
                        <i class="fal fa-map-marked-alt"></i>
                         Show Map</button></td>
                    </tr>

       HTML;
    }


    public function selectCustomer()
    {
        $hash = $this->getVariable('hash');
        $request = $this->customerModel->selectCustomer($hash);

        if ($request) {
            return $this->response->setJSON([
                'status' => 1,
                'customer' =>$this->customerTemplate($request),
                'token' => $this->token,

            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'msg' => 'Something went wrong',
                'customer' => [],
                'token' => $this->token,

            ]);
        }
    }



    //=================searching existing customer====================
    public function searchCustomer()
    {

        $keyword = $this->getVariable('keyword');
        $request = $this->customerModel->searchCustomer($keyword);
        if (count($request) > 0) {
            return $this->response->setJSON([
                'status' => 1,
                'data' => $request,
                'token' => $this->token
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => [],
                'token' => $this->token
            ]);
        }
    }
}
