<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProfileModel;

class ApiProfileController extends BaseController
{
    use ResponseTrait;
    protected $helpers = ['setting'];

    public function __construct(
        protected $profileModel = new ProfileModel(),

    ) {
    }


    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {
        
        helper('setting');
        $userIid = auth()->user()->unique_id;
        $user = $this->profileModel->getUser($userIid);
        return  $this->response->setJSON([
            'status' => 1,
            'data' => [
                'username' => $user->username,
                'email' => $user->email,
                'centerName' => $user->centerName,
                'centerCode' => $user->centerNumber,
                'SpCode' => 419,
                'subSpCode' => setting('Bill.subSpCode'),
                'SubSpName' => 'Surveillance',
                'ServiceProvider' => 'Weights And Measures Agency',

            ],
        ]);
    }




    public function moon(){
        if($this->request->getMethod() == 'POST'){
            return $this->response->setJSON([
                'status' => $this->getVariable('status'),
                'data' => [2345,4567,8976],
                
              ]);
        }
    }


    
}
