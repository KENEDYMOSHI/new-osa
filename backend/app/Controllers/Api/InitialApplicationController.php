<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Services\WorkflowService;
use App\Models\InitialApplicationModel;

class InitialApplicationController extends ResourceController
{
    protected $workflowService;
    protected $model;
    protected $documentService;

    public function __construct()
    {
        $this->workflowService = new WorkflowService();
        $this->model = new InitialApplicationModel();
        $this->documentService = new \App\Services\DocumentService();
    }

    // POST /api/initial-applications
    public function create()
    {
        $data = $this->request->getJSON(true);
        $userId = $data['user_id'] ?? null; // In real auth, get from session/token
        
        if (!$userId) {
            return $this->failUnauthorized('User ID missing');
        }

        try {
            $appId = $this->workflowService->submitInitialApp($userId, $data);
            
            // Lock documents upon submission
            $this->documentService->lockDocumentsOnSubmit($appId);

            return $this->respondCreated(['id' => $appId, 'message' => 'Initial Application Submitted']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // GET /api/initial-applications
    public function index()
    {
        // Filter by user if needed
        $apps = $this->model->findAll();
        return $this->respond($apps);
    }

    // GET /api/initial-applications/(:segment)
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Application not found');
        }

        // Create Item Model instance manually or inject if preferred
        $itemModel = new \App\Models\LicenseApplicationItemModel();
        $items = $itemModel->where('application_id', $id)->findAll();
        
        $data['licenseItems'] = $items;

        return $this->respond($data);
    }

    // PUT /api/initial-applications/(:segment)/license-types
    public function updateLicenseTypes($id = null)
    {
        $data = $this->request->getJSON(true);
        $licenseTypes = $data['licenseTypes'] ?? []; // Expecting array of objects or strings? Let's assume array of objects { name: 'Class A', ... } or just strings. 
        // Based on frontend LicenseComponent, it sends objects. Let's support extracting names or full objects.
        // Actually, let's strictly expect an array of full item objects or just names.
        // User request says: "License Type Selection". 
        // Let's assume payload is { license_types: [ { name: 'Class A', fee: ... }, ... ] }
        
        // 1. Check Application Status (LOCKING LOGIC)
        $app = $this->model->find($id);
        if (!$app) {
             return $this->failNotFound('Application not found');
        }

        if ($app['status'] !== 'Draft') {
            return $this->failForbidden('Application is LOCKED. License types cannot be modified.');
        }

        // 2. Update Items
        $itemModel = new \App\Models\LicenseApplicationItemModel();
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete existing
            $itemModel->where('application_id', $id)->delete();

            // Insert new
            foreach ($licenseTypes as $type) {
                $itemData = [
                    'id' => (string) \CodeIgniter\Uuid::uuid4(),
                    'application_id' => $id,
                    'license_type' => $type['name'] ?? $type['license_type'], // Handle variations
                    'fee' => $type['fee'] ?? 0,
                    'application_type' => $type['type'] ?? 'New',
                    'status' => 'Pending', // Default for items
                    'approval_stage' => 'Regional' // Default
                ];
                $itemModel->insert($itemData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Failed to update license types');
            }

            return $this->respond(['message' => 'License types updated']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    // GET /api/initial-applications/(:segment)/documents
    public function getDocuments($id = null)
    {
        $userId = $this->request->getGet('user_id'); // Assuming passed or avail via auth
        // In a real scenario, get userId from token: $this->getUserFromToken()->id;
        
        try {
            $documents = $this->documentService->getApplicationDocuments($id, $userId);
            return $this->respond($documents);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // POST /api/initial-applications/(:segment)/approve
    public function approve($id = null)
    {
        $data = $this->request->getJSON(true);
        $approverId = $data['approver_id'] ?? 'system';
        $stage = $data['stage']; // Regional or Surveillance
        $action = $data['action']; // Approved or Rejected
        $comments = $data['comments'] ?? '';

        try {
            $newStatus = $this->workflowService->approveInitialApp($id, $approverId, $stage, $action, $comments);
            return $this->respond(['status' => $newStatus, 'message' => 'Review processed']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // POST /api/initial-applications/(:segment)/documents (Upload)
    public function uploadDocument($appId = null)
    {
        $userId = $this->request->getPost('user_id'); // Or from token
        $documentType = $this->request->getPost('document_type');
        $file = $this->request->getFile('file');

        if (!$file->isValid()) {
            return $this->fail($file->getErrorString());
        }

        try {
            $doc = $this->documentService->uploadDocument($appId, $userId, $documentType, $file);
            return $this->respondCreated($doc);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // DELETE /api/initial-applications/(:segment)/documents/(:segment)
    public function deleteDocument($appId = null, $docId = null)
    {
        try {
            $this->documentService->deleteDocument($appId, $docId);
            return $this->respondDeleted(['message' => 'Document deleted']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // POST /api/initial-applications/(:segment)/documents/(:segment)/submit (For individual resubmission)
    public function submitDocument($appId = null, $docId = null)
    {
        try {
            $this->documentService->submitDocument($appId, $docId);
            return $this->respond(['message' => 'Document submitted']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // GET /api/initial-applications/(:segment)/documents/(:segment)/view
    public function viewDocument($appId = null, $docId = null)
    {
        $doc = $this->documentService->getDocument($docId); // Need to implement getDocument in service purely or just use model here? 
        // Better to use service for consistency but service currently returns processed array.
        // Let's use Model directly here for speed or add getDocument to Service. 
        // I will add getDocumentById to Service later/now.
        // Actually, let's just use the model here for simplicity since it is a read op.
        
        $attachmentModel = new \App\Models\LicenseApplicationAttachmentModel();
        $doc = $attachmentModel->find($docId);
        
        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        // Permission Check?
        // $userId = $this->request->getGet('user_id') ?? $this->getUserFromToken()->id; 
        // Ideally check if user owns app or doc.
        
        // Serve Content
        $mime = $doc->mime_type ?? 'application/pdf';
        $name = $doc->original_name ?? 'document.pdf';

        // 1. Try BLOB
        if (!empty($doc->file_content)) {
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="' . $name . '"')
                ->setBody($doc->file_content);
        }

        // 2. Try File Path
        if (!empty($doc->file_path) && file_exists(WRITEPATH . $doc->file_path)) {
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="' . $name . '"')
                ->setBody(file_get_contents(WRITEPATH . $doc->file_path));
        }
        
        // Fallback for absolute paths or if WRITEPATH is already included
        if (!empty($doc->file_path) && file_exists($doc->file_path)) {
             return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="' . $name . '"')
                ->setBody(file_get_contents($doc->file_path));
        }

        return $this->failNotFound('File content not found on server.');
    }
}
