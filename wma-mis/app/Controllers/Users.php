<?php

namespace App\Controllers;

use \App\Models\Users_model;

//use CodeIgniter\Config\Config;

class Users extends BaseController
{

        public function index()
        {
                $userModel = new Users_model();
                $data['subjects'] = $userModel->getData();
                return view('data_view', $data);
        }
}








