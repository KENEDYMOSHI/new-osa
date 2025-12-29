<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ApplicationTypeFeeModel;

class ApplicationTypeFeeController extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\ApplicationTypeFeeModel';
    protected $format    = 'json';

    // GET /api/admin/application-type-fees
    public function index()
    {
        $data = $this->model->orderBy('application_type', 'ASC')->findAll();
        return $this->respond($data);
    }

    // POST /api/admin/application-type-fees
    public function create()
    {
        $data = $this->request->getJSON(true);
        
        // Generate UUID using helper function
        helper('text');
        $data['id'] = bin2hex(random_bytes(16)); // Generate 32-character hex string

        if ($this->model->insert($data)) {
            return $this->respondCreated([
                'id' => $data['id'],
                'message' => 'Application Type Fee created successfully'
            ]);
        } else {
             return $this->failValidationErrors($this->model->errors());
        }
    }

    // PUT /api/admin/application-type-fees/(:segment)
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        
        if (!$this->model->find($id)) {
            return $this->failNotFound('Application Type Fee not found');
        }

        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Application Type Fee updated successfully']);
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }

    // DELETE /api/admin/application-type-fees/(:segment)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Application Type Fee not found');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Application Type Fee deleted successfully']);
        } else {
             return $this->failServerError('Failed to delete application type fee');
        }
    }
}
