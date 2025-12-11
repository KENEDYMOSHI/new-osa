<?php

namespace App\Controllers;


use App\Models\Users_model;

class Store extends BaseController
{
    public function index()
    {
        $user_model = new Users_model();
       $data['modules']= $user_model->getData();
       return view('myview',$data);
    }
    



    //--------------------------------------------------------------------

}