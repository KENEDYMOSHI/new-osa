<?php

namespace App\Controllers;

use App\Models\TechniciansRegistryModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class TechniciansRegistryController extends ResourceController
{
    protected $modelName = 'App\Models\TechniciansRegistryModel';
    protected $format = 'json';

    /**
     * Get all registry records for the authenticated user
     */
    public function index(): ResponseInterface
    {
        try {
            $userId = auth()->user()->id ?? null;
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $model = new TechniciansRegistryModel();
            $records = $model->getRegistryByUser($userId);

            return $this->respond([
                'status' => 'success',
                'data' => $records
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Failed to fetch records: ' . $e->getMessage());
        }
    }

    /**
     * Create a new registry record
     */
    public function create(): ResponseInterface
    {
        try {
            $userId = auth()->user()->id ?? null;
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $model = new TechniciansRegistryModel();
            
            $data = $this->request->getJSON(true);
            $data['user_id'] = $userId;

            if (!$model->insert($data)) {
                return $this->failValidationErrors($model->errors());
            }

            $insertId = $model->getInsertID();
            $record = $model->find($insertId);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Record created successfully',
                'data' => $record
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Failed to create record: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing registry record
     */
    public function update($id = null): ResponseInterface
    {
        try {
            $userId = auth()->user()->id ?? null;
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $model = new TechniciansRegistryModel();
            
            // Verify the record belongs to the user
            $record = $model->find($id);
            if (!$record) {
                return $this->failNotFound('Record not found');
            }

            if ($record['user_id'] != $userId) {
                return $this->failForbidden('You do not have permission to update this record');
            }

            $data = $this->request->getJSON(true);
            unset($data['user_id']); // Prevent changing user_id

            if (!$model->update($id, $data)) {
                return $this->failValidationErrors($model->errors());
            }

            $updatedRecord = $model->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Record updated successfully',
                'data' => $updatedRecord
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Failed to update record: ' . $e->getMessage());
        }
    }

    /**
     * Delete a registry record
     */
    public function delete($id = null): ResponseInterface
    {
        try {
            $userId = auth()->user()->id ?? null;
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $model = new TechniciansRegistryModel();
            
            // Verify the record belongs to the user
            $record = $model->find($id);
            if (!$record) {
                return $this->failNotFound('Record not found');
            }

            if ($record['user_id'] != $userId) {
                return $this->failForbidden('You do not have permission to delete this record');
            }

            if (!$model->delete($id)) {
                return $this->failServerError('Failed to delete record');
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Failed to delete record: ' . $e->getMessage());
        }
    }
}
