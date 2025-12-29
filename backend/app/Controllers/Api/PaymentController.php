<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\BillModel;

class PaymentController extends BaseController
{
    use ResponseTrait;

    // TODO: Move to .env
    private $apiKey = 'osa_approval_api_key_12345';

    public function getPaymentCollection()
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }

        $json = $this->request->getJSON(true);
        $params = $json['params'] ?? [];
        
        $model = new BillModel();
        $data = $model->getPaymentCollection($params);
        
        return $this->respond($data);
    }

    public function getReportData()
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }

        $json = $this->request->getJSON(true);
        $params = $json['params'] ?? [];
        
        $model = new BillModel();
        $data = $model->getReportData($params);
        
        return $this->respond($data);
    }
}
