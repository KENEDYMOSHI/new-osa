<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ScaleModel;
use App\Models\LorriesModel;
use App\Models\FuelPumpModel;

class Api extends ResourceController
{
    protected $session;
    public $uniqueId;
    public $role;
    public $city;
    protected $scales;
    protected $lorries;
    protected $fuelPumps;
    protected $format    = 'json';


    public function __construct()
    {
        $this->session   = session();
        $this->scales    = new  ScaleModel();
        $this->lorries   = new  LorriesModel();
        $this->fuelPumps = new  FuelPumpModel();
        // ================Session Variables==============
        $this->uniqueId               = $this->session->get('manager');
        $this->role                   = $this->session->get('role');
        $this->city                   = 'Arusha';
    }


    public function index()
    {
        $data = [];
        $scale = $this->scales->getData($this->city);
        $lorries = $this->lorries->getData($this->city);
        array_push($data, $lorries);
        array_push($data, $scale);



        if ($data) {

            return $this->respond($data);
        } else {
            return $this->failNotFound('No data found');
        }

        return view('apiview');
    }

    public function filter()
    {
        helper('form');
        
        
        if ($this->request->getMethod() == 'POST') {
        
            
            $query = $this->request->getVar('keyWord');

            // echo $query;
            // exit;

            $result =  $this->fuelPumps->search($query);
           if ($result){
                
            //return $result;
             echo 'We found a match';
            //    echo json_encode($result);
            }else{
                echo 'oops try again';  
            }

        }
        return view('search');

    }
}