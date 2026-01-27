<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\FormDRequest;

class FormDController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new FormDRequest();
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->save($data)) {
            return $this->failValidationError($this->model->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Form D Request submitted successfully',
            'data' => $data
        ]);
    }

    public function index()
    {
        $requests = $this->model->findAll();
        return $this->respond($requests);
    }
    
    public function getUserRequests($userId)
    {
        $requests = $this->model->where('user_id', $userId)->findAll();
        return $this->respond($requests);
    }
}
