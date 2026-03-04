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
        $builder = $this->model->builder();
        $builder->select('form_d_requests.*, license_users.first_name as inspector_first_name, license_users.last_name as inspector_last_name');
        $builder->join('license_users', 'license_users.id = form_d_requests.inspector_id', 'left');
        $builder->where('form_d_requests.user_id', $userId);
        $builder->orderBy('form_d_requests.created_at', 'DESC');
        
        $requests = $builder->get()->getResultArray();
        return $this->respond($requests);
    }

    public function approve($id)
    {
        $data = $this->request->getJSON(true);
        $inspectorId = $data['inspector_id'] ?? null;

        if (!$inspectorId) {
            return $this->failValidationError('Inspector ID is required for approval.');
        }

        $updateData = [
            'status' => 'Approved',
            'inspector_id' => $inspectorId,
            'approved_by' => 1, // Placeholder: auth()->id() if available
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => null
        ];

        if ($this->model->update($id, $updateData)) {
             return $this->respond(['status' => 'success', 'message' => 'Request approved successfully.']);
        }

        return $this->failServerError('Failed to approve request.');
    }

    public function reject($id)
    {
        $data = $this->request->getJSON(true);
        $reason = $data['reason'] ?? null;

        if (!$reason) {
            return $this->failValidationError('Rejection reason is required.');
        }

        $updateData = [
            'status' => 'Rejected',
            'rejection_reason' => $reason,
            'approved_by' => null,
            'approved_at' => null,
            'inspector_id' => null
        ];

        if ($this->model->update($id, $updateData)) {
            return $this->respond(['status' => 'success', 'message' => 'Request rejected successfully.']);
        }

         return $this->failServerError('Failed to reject request.');
    }

    public function getInspectors()
    {
        // Placeholder mechanism to get inspectors. 
        // Ideally: return $this->userModel->inGroup('inspector')->findAll();
        // For now, returning a dummy list or querying users table if possible.
        // Assuming there is a users table.
        
        $db = \Config\Database::connect();
        try {
            // Attempt to fetch users who might be inspectors. 
            // If groups are used:
            // $query = $db->table('license_users')->join('auth_groups_users', 'users.id = auth_groups_users.user_id')->where('group', 'inspector')->get();
            // Fallback: return all users or just empty list to be populated by frontend mock if needed.
             $query = $db->table('license_users')->select('id, username, email')->limit(50)->get();
             return $this->respond($query->getResultArray());
        } catch (\Exception $e) {
            // If table doesn't exist or other error, return empty
            return $this->respond([]);
        }
    }
}
