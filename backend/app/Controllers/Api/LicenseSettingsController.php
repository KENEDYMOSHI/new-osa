<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LicenseTypeModel;

class LicenseSettingsController extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\LicenseTypeModel';
    protected $format    = 'json';

    // GET /api/admin/license-types
    public function index()
    {
        $data = $this->model->orderBy('name', 'ASC')->findAll();
        return $this->respond($data);
    }

    // POST /api/admin/license-types
    public function create()
    {
        $data = $this->request->getJSON(true);
        
        // Generate UUID using helper function
        helper('text');
        $data['id'] = bin2hex(random_bytes(16)); // Generate 32-character hex string

        if ($this->model->insert($data)) {
            return $this->respondCreated([
                'id' => $data['id'],
                'message' => 'License Type created successfully'
            ]);
        } else {
             return $this->failValidationErrors($this->model->errors());
        }
    }

    // PUT /api/admin/license-types/(:segment)
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        
        if (!$this->model->find($id)) {
            return $this->failNotFound('License Type not found');
        }

        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'License Type updated successfully']);
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }

    // DELETE /api/admin/license-types/(:segment)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('License Type not found');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'License Type deleted successfully']);
        } else {
             return $this->failServerError('Failed to delete license type');
        }
    }
}
