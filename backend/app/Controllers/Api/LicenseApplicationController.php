<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Services\WorkflowService;
use App\Models\LicenseApplicationModel;
use App\Models\ApplicationQualificationModel;
use App\Models\ApplicationToolModel;

class LicenseApplicationController extends ResourceController
{
    protected $workflowService;
    protected $model;

    public function __construct()
    {
        $this->workflowService = new WorkflowService();
        $this->model = new LicenseApplicationModel();
    }

    // GET /api/license-applications
    public function index()
    {
        $data = $this->model->findAll();
        return $this->respond($data);
    }

    // POST /api/license-applications/(:segment)/submit
    public function submit($id = null)
    {
        $data = $this->request->getJSON(true);
        
        // Save Details First (Qualifications & Tools)
        $quals = $data['qualifications'] ?? [];
        $tools = $data['tools'] ?? [];

        $qualModel = new ApplicationQualificationModel();
        foreach ($quals as $q) {
            $q['id'] = (string) \CodeIgniter\Uuid::uuid4();
            $q['license_application_id'] = $id;
            $qualModel->insert($q);
        }

        $toolModel = new ApplicationToolModel();
        foreach ($tools as $t) {
            $t['id'] = (string) \CodeIgniter\Uuid::uuid4();
            $t['license_application_id'] = $id;
            $toolModel->insert($t);
        }

        try {
            $this->workflowService->submitLicenseApp($id, $data);
            return $this->respond(['message' => 'License Application Submitted']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // POST /api/license-applications/(:segment)/approve
    public function approve($id = null)
    {
        $data = $this->request->getJSON(true);
        $approverId = $data['approver_id'] ?? 'system';
        $stage = $data['stage']; // DTS or CEO
        $action = $data['action']; // Approved or Rejected
        $comments = $data['comments'] ?? '';

        try {
            $newStatus = $this->workflowService->approveLicenseApp($id, $approverId, $stage, $action, $comments);
            return $this->respond(['status' => $newStatus, 'message' => 'Review processed']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
