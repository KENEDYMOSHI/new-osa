<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Demo extends BaseController
{
    public function index()
    {
        $message = $this->request->getVar('message');
        return $this->response->setJSON(['status' => 200,'message' => $message]);
    }

    public function user()
    {
       
        return $this->response->setJSON([ 'name' => 'John', 'age' => 30,'status' => 200]);
    }
}
