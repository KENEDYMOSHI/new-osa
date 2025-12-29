<?php

namespace App\Controllers;

use App\Models\LicenseTypeModel;

class TestLicenseData extends BaseController
{
    public function index()
    {
        $model = new LicenseTypeModel();
        // Allow public access for debugging
        return $this->response->setJSON($model->findAll());
    }
}
