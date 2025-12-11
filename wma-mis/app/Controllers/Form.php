<?php

namespace App\Controllers;

//use CodeIgniter\Controller;
class Form extends BaseController
{

        public function __construct()
        {
                helper(['form']);
        }
        public function index()
        {
                $data = [];
                $data['validation'] = null;




                if ($this->request->getMethod() == 'POST') {
                        $rules = [
                                "firstname"     => "required",
                                "lastname"      => "required",


                        ];

                        if ($this->validate($rules)) {
                                echo 'form is ready';
                        } else {
                                $data['validation'] = $this->validator;
                        }
                }
                return view('testform', $data);
        }
}