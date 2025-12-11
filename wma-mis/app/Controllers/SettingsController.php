<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\ProfileModel;

class SettingsController extends BaseController
{
    public $session;
    public $setting;
    public $uniqueId;
    public $profileModel;
  
    public $appRequest;
    public function __construct()
    {
        $this->appRequest = service('request');
        $this->setting = new SettingsModel();
        $this->profileModel = new ProfileModel();
        
        $this->session = session();
        $this->uniqueId = $this->session->get('loggedUser');

        // $this->admin = $this->session->get('SuperUser');
      
        helper(['form', 'url', 'array', 'date']);
    }

    public function getVariable($var)
    {
        return $this->appRequest->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    public function index()
    {
        $uniqueId = $this->uniqueId;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
     

        $data['page'] = [
            'title' => 'Settings',
            'heading' => 'Settings',
        ];
        return view('Pages/admin/Setting',$data);
    }
}
