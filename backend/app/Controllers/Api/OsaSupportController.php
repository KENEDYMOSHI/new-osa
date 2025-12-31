<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OsaSupportModel;
use CodeIgniter\API\ResponseTrait;

class OsaSupportController extends BaseController
{
    use ResponseTrait;

    protected $osaSupportModel;

    public function __construct()
    {
        $this->osaSupportModel = new OsaSupportModel();
    }

    public function getDetails()
    {
        try {
            $details = $this->osaSupportModel->first();
            
            if (!$details) {
                 // Return empty structure if no details found, to prevent frontend error
                return $this->respond([
                    'address' => '',
                    'phone_label_1' => '', 'phone_number_1' => '',
                    'phone_label_2' => '', 'phone_number_2' => '',
                    'phone_label_3' => '', 'phone_number_3' => '',
                    'email_general' => '', 'email_tech' => '',
                    'website' => ''
                ]);
            }

            return $this->respond($details);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
