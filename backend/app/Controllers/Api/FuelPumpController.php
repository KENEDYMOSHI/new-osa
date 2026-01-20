<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class FuelPumpController extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\FuelPumpModel';
    protected $format    = 'json';

    /**
     * Get all fuel pump applications for the authenticated user
     */
    public function index()
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $applications = $this->model
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $applications
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching fuel pump applications: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch applications');
        }
    }

    /**
     * Get a single fuel pump application
     */
    public function show($id = null)
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $application = $this->model
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$application) {
                return $this->failNotFound('Application not found');
            }

            // Decode JSON fields
            $jsonFields = ['display_location', 'power_supply', 'software_protection_method', 'seal_type', 'intended_installation'];
            foreach ($jsonFields as $field) {
                if (isset($application[$field]) && is_string($application[$field])) {
                    $application[$field] = json_decode($application[$field], true) ?? [];
                }
            }

            return $this->respond([
                'status' => 'success',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching fuel pump application: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch application');
        }
    }

    /**
     * Create a new fuel pump application (save draft)
     */
    public function create()
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $data = $this->request->getJSON(true);
            
            // Add user_id
            $data['user_id'] = $userId;
            $data['status'] = 'draft';

            // Encode JSON fields
            $jsonFields = ['display_location', 'power_supply', 'software_protection_method', 'seal_type', 'intended_installation'];
            foreach ($jsonFields as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }

            // Handle file uploads (files are already uploaded, just save paths)
            $documentFields = [
                'calibration_manual',
                'user_manual',
                'pump_exterior_photo',
                'nameplate_photo',
                'display_photo',
                'sealing_points_photo',
                'type_examination_cert',
                'software_documentation'
            ];

            $applicationId = $this->model->insert($data);

            if (!$applicationId) {
                return $this->failServerError('Failed to create application');
            }

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Draft saved successfully',
                'data' => ['id' => $applicationId]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error creating fuel pump application: ' . $e->getMessage());
            return $this->failServerError('Failed to create application: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing fuel pump application
     */
    public function update($id = null)
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            // Check if application exists and belongs to user
            $existing = $this->model
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$existing) {
                return $this->failNotFound('Application not found');
            }

            // Don't allow updates to submitted applications
            if ($existing['status'] !== 'draft') {
                return $this->failForbidden('Cannot update submitted application');
            }

            $data = $this->request->getJSON(true);

            // Encode JSON fields
            $jsonFields = ['display_location', 'power_supply', 'software_protection_method', 'seal_type', 'intended_installation'];
            foreach ($jsonFields as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }

            $updated = $this->model->update($id, $data);

            if (!$updated) {
                return $this->failServerError('Failed to update application');
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Application updated successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error updating fuel pump application: ' . $e->getMessage());
            return $this->failServerError('Failed to update application');
        }
    }

    /**
     * Submit fuel pump application
     */
    public function submit($id = null)
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            // Check if application exists and belongs to user
            $application = $this->model
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$application) {
                return $this->failNotFound('Application not found');
            }

            // Check if already submitted
            if ($application['status'] !== 'draft') {
                return $this->failForbidden('Application already submitted');
            }

            // Generate application number
            $applicationNumber = 'FP-' . date('Y') . '-' . str_pad($id, 6, '0', STR_PAD_LEFT);

            // Update status to submitted
            $updated = $this->model->update($id, [
                'status' => 'submitted',
                'submitted_at' => date('Y-m-d H:i:s'),
                'application_number' => $applicationNumber
            ]);

            if (!$updated) {
                return $this->failServerError('Failed to submit application');
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_number' => $applicationNumber
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error submitting fuel pump application: ' . $e->getMessage());
            return $this->failServerError('Failed to submit application');
        }
    }

    /**
     * Delete a draft application
     */
    public function delete($id = null)
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            // Check if application exists and belongs to user
            $application = $this->model
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$application) {
                return $this->failNotFound('Application not found');
            }

            // Only allow deletion of drafts
            if ($application['status'] !== 'draft') {
                return $this->failForbidden('Cannot delete submitted application');
            }

            $deleted = $this->model->delete($id);

            if (!$deleted) {
                return $this->failServerError('Failed to delete application');
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Application deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error deleting fuel pump application: ' . $e->getMessage());
            return $this->failServerError('Failed to delete application');
        }
    }

    /**
     * Upload document for fuel pump application
     */
    public function uploadDocument()
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return $this->failUnauthorized('User not authenticated');
            }

            $file = $this->request->getFile('file');
            $documentType = $this->request->getPost('document_type');
            $applicationId = $this->request->getPost('application_id');

            if (!$file || !$file->isValid()) {
                return $this->failValidationError('Invalid file upload');
            }

            // Validate file type
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->failValidationError('Invalid file type. Only PDF and images are allowed');
            }

            // Validate file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                return $this->failValidationError('File size exceeds 10MB limit');
            }

            // Create upload directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads/fuel_pump_documents/' . $userId . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $newName = $documentType . '_' . time() . '.' . $file->getExtension();
            
            // Move file
            $file->move($uploadPath, $newName);

            // Get relative path for database
            $relativePath = 'fuel_pump_documents/' . $userId . '/' . $newName;

            // Update application if ID provided
            if ($applicationId) {
                $this->model->update($applicationId, [
                    $documentType => $relativePath
                ]);
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'data' => [
                    'path' => $relativePath,
                    'filename' => $newName
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error uploading document: ' . $e->getMessage());
            return $this->failServerError('Failed to upload document');
        }
    }
}
