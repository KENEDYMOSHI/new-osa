<?php

namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\LorriesModel;
use App\Models\MiscellaneousModel;
use App\Models\PortModel;
use App\Models\ProfileModel;
use App\Models\VtcModel;
use App\Models\WaterMeterModel;

class ControlNumber extends BaseController
{
    private $uniqueId;
    //   public $uniqueId;
    private $role;
    private $city;
    private $portUnitModel;
    private $session;
    private $profileModel;
    private $CommonTasks;
    private $contacts;

    private $vtcModel;
    private $lorriesModel;
    private $waterMeterModel;

    public $sessionExpiration;

    public $variable;
    public $appRequest;

    public function __construct()
    {
        $this->appRequest = service('request');
        $this->portUnitModel = new PortModel();
        $this->profileModel = new ProfileModel();
        $this->session = session();

        $this->vtcModel = new VtcModel();
        $this->lorriesModel = new LorriesModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->contacts = new MiscellaneousModel();

        $this->uniqueId = $this->session->get('loggedUser');
        // $this->uniqueId = $this->session->get('loggedUser');
        $this->role = $this->profileModel->getRole($this->uniqueId)->role;
        $this->city = $this->session->get('city');
        $this->CommonTasks = new CommonTasksLibrary();
        helper(['form', 'array', 'regions', 'date', 'documents', 'image']);
    }

    public function getVariable($var)
    {
        return $this->appRequest->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {


        $uniqueId = $this->uniqueId;
        $role = $this->role;

        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);

        $data['role'] = $role;
        $data['page'] = [
            "title" => "Reports",
            "heading" => "Reports",
        ];

        $data['role'] = $this->role;
 $data['user'] = auth()->user();
        $data['userLocation'] = $this->city;
        return view('Pages/collectionReports/wmaReport', $data);
    }
}
