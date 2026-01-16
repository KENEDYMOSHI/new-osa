<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LicenseApplicationModel;
use App\Models\LicenseApplicationItemModel;
use App\Models\LicenseApplicationAttachmentModel;
use App\Models\LicenseCompletionModel;
use App\Models\ApplicationTypeFeeModel;
use App\Models\InitialApplicationModel;
use CodeIgniter\Shield\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LicenseController extends ResourceController
{
    use ResponseTrait;

    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function getUserFromToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        } else {
            return null;
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            $users = model(UserModel::class);
            return $users->findById($decoded->uid);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function upload()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $rules = [
            'file' => 'uploaded[file]|max_size[file,2048]|mime_in[file,application/pdf]',
            'documentType' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->fail('Invalid file');
        }

        // Read file content for BLOB storage
        $fileContent = file_get_contents($file->getTempName());
        
        $attachmentModel = new LicenseApplicationAttachmentModel();
        $docType = $this->request->getPost('documentType');
        $applicationId = $this->request->getPost('applicationId');
        $category = $this->request->getPost('category');

        $existingDoc = null;
        $currentApp = null;

        // 1. Identify Existing Document and Application
        if ($applicationId && $applicationId !== 'null' && $applicationId !== 'undefined') {
            // Verify application belongs to user
            $appModel = new LicenseApplicationModel();
            $app = $appModel->where('id', $applicationId)->where('user_id', $user->id)->first();
            
            if (!$app) {
                return $this->failForbidden('Invalid application ID');
            }
            $currentApp = $app;

            // Check if a document of this type already exists for this application
            $existingDoc = $attachmentModel->where('application_id', $applicationId)
                                           ->where('document_type', $docType)
                                           ->first();
        } else {
            // Draft mode: Check if a document of this type already exists for the user (unattached)
            $existingDoc = $attachmentModel->where('user_id', $user->id)
                                           ->where('document_type', $docType)
                                           ->where('application_id', null)
                                           ->first();
            $applicationId = null; // Ensure null for insertion
        }

        // 2. Handle Existing Document (Update if Returned, Delete otherwise)
        if ($existingDoc) {
            // Preserve Category if not provided in new request
            if ((!$category || $category === 'null' || $category === 'undefined') && !empty($existingDoc->category)) {
                $category = $existingDoc->category;
            }

            // CHECK: Is it a Returned document?
            if ($existingDoc->status === 'Returned') {
                // UPDATE LOGIC: Update existing record, preserve ID
                $id = $existingDoc->id;
                $status = 'Uploaded';

                // Cleanup: Delete old file from disk if it exists
                if (!empty($existingDoc->file_path)) {
                    $oldFullPath = WRITEPATH . $existingDoc->file_path;
                    if (file_exists($oldFullPath)) {
                        unlink($oldFullPath);
                    }
                }

                // Move new file to uploads directory
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);
                $filePath = 'uploads/' . $newName;

                $data = [
                    'original_name'    => $file->getClientName(),
                    'file_content'     => null, // Clear BLOB content
                    'file_path'        => $filePath, // Set file path
                    'status'           => $status,
                    'rejection_reason' => null,
                    // Ensure category is updated/preserved
                    'category'         => $category,
                    // Ensure app link is preserved (should vary rarely change here)
                    'application_id'   => $applicationId
                ];

                try {
                    if (!$attachmentModel->update($id, $data)) {
                        return $this->failServerError('Failed to update record: ' . json_encode($attachmentModel->errors()));
                    }

                    // Send Notification for Re-upload
                    $this->sendReuploadNotification($currentApp, $user, $docType);

                    return $this->respondCreated([
                        'message' => 'File re-uploaded successfully',
                        'id' => $id,
                        'fileName' => $file->getClientName(),
                        'status' => $status,
                        'date' => date('d/m/Y')
                    ]);

                } catch (\Exception $e) {
                    return $this->failServerError('Exception during update: ' . $e->getMessage());
                }
            } else {
                // DELETE LOGIC: Not returned, simple overwrite (Delete Old, Insert New)
                // Use ID to delete specifically this document to be safe
                $attachmentModel->delete($existingDoc->id);
            }
        }

        // 3. Insert New Document (If we didn't update above)
        $id = md5(uniqid(rand(), true));
        $status = 'Draft';

        // Move file to uploads directory
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = 'uploads/' . $newName;
        
        $data = [
            'id'             => $id,
            'user_id'        => $user->id,
            'application_id' => $applicationId,
            'document_type'  => $docType,
            'category'       => $category,
            'file_path'      => $filePath,
            'original_name'  => $file->getClientName(),
            'mime_type'      => 'application/pdf',
            'file_content'   => null, // No BLOB
            'status'         => $status,
            'rejection_reason' => null 
        ];

        try {
            if (!$attachmentModel->insert($data)) {
                return $this->failServerError('Failed to insert record: ' . json_encode($attachmentModel->errors()));
            }
        } catch (\Exception $e) {
             return $this->failServerError('Exception during insert: ' . $e->getMessage());
        }

        return $this->respondCreated([
            'message' => 'File uploaded successfully',
            'id' => $id,
            'fileName' => $file->getClientName(),
            'status' => 'Uploaded',
            'date' => date('d/m/Y')
        ]);
    }

    /**
     * Helper to send notification when document is re-uploaded
     */
    private function sendReuploadNotification($currentApp, $user, $docType) {
        if (!$currentApp) return;

        $db = \Config\Database::connect();
        
        // Determine the approver based on current stage
        $currentStage = $currentApp->current_stage ?? 1;
        $approverColumn = 'approver_stage_' . $currentStage;
        $approverId = $currentApp->$approverColumn ?? null;

        // Only send notification if an approver is assigned
        if ($approverId) {
            $notifId = md5(uniqid(rand(), true));
            // Get applicant name
            $personalInfo = $db->table('practitioner_personal_infos')->where('user_uuid', $user->uuid)->get()->getRow();
            $applicantName = $personalInfo ? ($personalInfo->first_name . ' ' . $personalInfo->last_name) : 'Applicant';

            $controlNumber = $currentApp->control_number ?? 'Unknown';

            $notifData = [
                'id' => $notifId,
                'user_id' => $approverId, 
                'title' => 'Document Re-uploaded',
                'message' => "Applicant {$applicantName} has re-uploaded '{$docType}' for Application {$controlNumber}.",
                'type' => 'document_reuploaded',
                'related_entity_id' => $currentApp->id,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('notifications')->insert($notifData);
        }
    }

    public function getUserDocuments()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $attachmentModel = new LicenseApplicationAttachmentModel();
        
        // Fetch all documents for the user, ordered by creation date descending
        // This ensures that if we have duplicates, the newest comes first
        $allDocs = $attachmentModel->where('user_id', $user->id)
                                   ->orderBy('created_at', 'DESC')
                                   ->findAll();

        log_message('error', 'getUserDocuments: User ID ' . $user->id . ' has ' . count($allDocs) . ' documents.');

        // Filter to keep only the latest document for each document_type
        $latestDocs = [];
        $seenTypes = [];

        foreach ($allDocs as $doc) {
            if (!in_array($doc->document_type, $seenTypes)) {
                // Remove file_content to reduce payload size
                unset($doc->file_content);
                $latestDocs[] = $doc;
                $seenTypes[] = $doc->document_type;
            }
        }

        // Logic for "The system shall display the license(s) that an applicant has already applied for and approved."
        // And "Prevent re-application for 1 year from CEO approval date"

        $db = \Config\Database::connect();
        $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));

        // Fetch ALL valid license types from database to check against
        $allLicenseTypes = model('App\Models\LicenseTypeModel')->findAll();
        
        $availableLicenseTypes = [];
        $submittedLicenseTypes = [];

        foreach ($allLicenseTypes as $licenseType) {
            $type = $licenseType['name'];

            // 1. Check for Active/In-Progress Applications (Block these regardless of date)
            // Statuses where application is still "alive" and not fully rejected or finalized/expired
            $inProgressExists = $db->table('license_application_items')
                ->select('license_application_items.id')
                ->join('license_applications', 'license_applications.id = license_application_items.application_id')
                ->where('license_applications.user_id', $user->id)
                ->where('license_application_items.license_type', $type)
                ->whereIn('license_applications.status', [
                    'Submitted', 
                    'Pending', 
                    'Approved_Manager',
                    'Approved_Surveillance',
                    'Applicant_Submission',
                    'DTS',
                    'Approved_DTS',
                    'Recommend_DTS',
                    'Approved_CEO',
                    'License_Generated',
                    'Approved',
                    'Returned',
                    'Draft'
                ])
                ->countAllResults();

            // 2. Check for Valid Approved Licenses (Block if approved within last 1 year)
            // "The one-year restriction period shall start counting from the date the license is approved by the CEO"
            // We use 'updated_at' when status became 'Approved_CEO' as a proxy, or 'valid_from' if available.
            // Using updated_at for now as simplest proxy for approval time for existing records.
            $approvedExists = $db->table('license_application_items')
                ->select('license_application_items.id')
                ->join('license_applications', 'license_applications.id = license_application_items.application_id')
                ->where('license_applications.user_id', $user->id)
                ->where('license_application_items.license_type', $type)
                ->whereIn('license_applications.status', ['Approved_CEO', 'License_Generated']) 
                ->where('license_applications.updated_at >=', $oneYearAgo)
                ->countAllResults();

            if ($inProgressExists > 0 || $approvedExists > 0) {
                 // Fetch the actual details for the in-progress/approved item
                 $latestItem = $db->table('license_application_items')
                    ->select('license_application_items.*, osabill.control_number, osabill.payment_status, osabill.amount as bill_amount, license_applications.status as app_status')
                    ->join('license_applications', 'license_applications.id = license_application_items.application_id')
                    ->join('osabill', 'osabill.bill_id = license_applications.id', 'left')
                    ->where('license_applications.user_id', $user->id)
                    ->where('license_application_items.license_type', $type)
                    ->orderBy('license_applications.created_at', 'DESC')
                    ->get(1)
                    ->getRow();

                 $submittedLicenseTypes[] = [
                    'license_type' => $type,
                    'status' => $latestItem ? $latestItem->app_status : (($approvedExists > 0) ? 'Restricted (1 Year)' : 'In Progress'),
                    'control_number' => $latestItem ? $latestItem->control_number : null,
                    'payment_status' => $latestItem ? $latestItem->payment_status : null,
                    'bill_amount' => $latestItem ? $latestItem->bill_amount : null,
                    'application_fee' => $latestItem ? $latestItem->application_fee : null
                 ];
            } else {
                 // Check if it is an older license (Renewal candidates)
                 $historicalExists = $db->table('license_application_items')
                    ->select('license_application_items.id')
                    ->join('license_applications', 'license_applications.id = license_application_items.application_id')
                    ->where('license_applications.user_id', $user->id)
                    ->where('license_application_items.license_type', $type)
                    ->whereIn('license_applications.status', ['Approved_CEO', 'License_Generated']) 
                    ->where('license_applications.updated_at <', $oneYearAgo) // Older than 1 year
                    ->countAllResults();

                 $typeName = ($historicalExists > 0) ? 'Renewal' : 'New';

                 $availableLicenseTypes[] = [
                    'name' => $type,
                    'type' => $typeName, 
                    'date' => null
                 ];
            }
        }

        // Fetch the most recent active/submitted application for this user (for dashboard/wizard context)
        $appModel = new LicenseApplicationModel();
        $latestApp = $appModel->where('user_id', $user->id)
                              ->orderBy('created_at', 'DESC')
                              ->first();

        $appId = null;
        $appStatus = null;
        $currentLicenseItems = [];

        if ($latestApp) {
            $appId = $latestApp['id'];
            $appStatus = $latestApp['status'];

            // Fetch items for this specific application
            $itemModel = new LicenseApplicationItemModel();
            $currentLicenseItems = $itemModel->where('application_id', $appId)->findAll();
        }

        return $this->respond([
            'documents' => $latestDocs,
            'applicationId' => $appId,
            'applicationStatus' => $appStatus,
            'licenseItems' => $currentLicenseItems,
            'availableLicenseTypes' => $availableLicenseTypes,
            'submittedLicenseTypes' => $submittedLicenseTypes
        ]);
    }

    public function deleteDocument($id = null)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $attachmentModel = new LicenseApplicationAttachmentModel();
        $doc = $attachmentModel->find($id);

        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        if ($doc->user_id != $user->id) {
            return $this->failForbidden('You are not allowed to delete this document');
        }

        // Allow deletion in these cases:
        // 1. Document is a draft (application_id is null)
        // 2. Document status is 'Returned' (needs to be re-uploaded)
        if ($doc->application_id !== null && $doc->status !== 'Returned') {
             return $this->failForbidden('Cannot delete a document that is attached to a submitted application.');
        }

        // Delete the physical file if it exists
        if (!empty($doc->file_path) && file_exists($doc->file_path)) {
            unlink($doc->file_path);
        }

        $attachmentModel->delete($id);

        return $this->respondDeleted(['message' => 'Document deleted successfully']);
    }

    public function submitDocument($id = null)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $attachmentModel = new LicenseApplicationAttachmentModel();
        $doc = $attachmentModel->find($id);

        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        if ($doc->user_id != $user->id) {
            return $this->failForbidden('You are not allowed to submit this document');
        }

        if ($doc->status === 'Submitted') {
            return $this->respond(['message' => 'Document submitted successfully', 'status' => 'Submitted']);
        }
        
        // If document is Resubmitted (re-uploaded after return), keep it as Resubmitted until Admin accepts it.
        if ($doc->status === 'Resubmitted') {
            return $this->respond(['message' => 'Document submitted for review', 'status' => 'Resubmitted']);
        }

        // Update status to Submitted
        $result = $attachmentModel->update($id, ['status' => 'Submitted']);
        
        if ($result === false) {
            log_message('error', 'Failed to update document status for ID: ' . $id);
            return $this->fail('Failed to submit document. Update rejected.');
        }
        
        return $this->respond(['message' => 'Document submitted successfully', 'status' => 'Submitted']);
    }

    public function view($id)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $attachmentModel = new LicenseApplicationAttachmentModel();
        $doc = $attachmentModel->find($id);

        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        if ($doc->user_id != $user->id) {
            return $this->failForbidden('You are not allowed to view this document');
        }

        // Serve from BLOB
        if (!empty($doc->file_content)) {
            return $this->response
                ->setHeader('Content-Type', $doc->mime_type)
                ->setHeader('Content-Disposition', 'inline; filename="' . $doc->original_name . '"')
                ->setBody($doc->file_content);
        }

        // Serve from File System
        if (!empty($doc->file_path)) {
            $fullPath = WRITEPATH . $doc->file_path;
            
            // Debugging Logs
            log_message('error', 'Attempting to view file: ' . $fullPath);
            
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath);
                return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', 'inline; filename="' . $doc->original_name . '"')
                    ->setBody(file_get_contents($fullPath));
            } else {
                log_message('error', 'File NOT FOUND at: ' . $fullPath);
            }
        } else {
            log_message('error', 'No file_path or file_content for doc ID: ' . $id);
        }
        
        return $this->failNotFound('File content not found');
    }

    public function submit()
    {
        log_message('error', '[LicenseSubmission] Request received.'); // Debug log
        $data = $this->request->getJSON(true);

        $user = $this->getUserFromToken();
        if (!$user) {
            log_message('error', '[LicenseSubmission] User unauthorized.');
            return $this->failUnauthorized('User not logged in or invalid token');
        }
        $userId = $user->id;
        log_message('error', '[LicenseSubmission] User ID: ' . $userId);

        $applicationType = $data['applicationType'] ?? 'New';
        // Total amount passed from frontend is aggregated, but for separate apps we need individual fees.
        // We will recalculate fees per license type.
        
        // Handle licenseTypes: could be array of strings or objects
        $licenseTypesInput = $data['licenseTypes'] ?? [];
        if (is_string($licenseTypesInput)) {
            $licenseTypesInput = json_decode($licenseTypesInput, true);
        }

        if (empty($licenseTypesInput)) {
            return $this->fail('No license types selected');
        }

        $previousLicenses = $data['previousLicenses'] ?? [];
        $qualifications   = $data['qualifications'] ?? [];
        $experiences      = $data['experiences'] ?? [];
        $tools            = $data['tools'] ?? [];
        $declaration      = $data['declaration'] ?? false;

        // Fetch attachments to process (Explicit list or Fallback to drafts)
        $attachmentModel = new LicenseApplicationAttachmentModel();
        $attachmentsToProcess = [];
        
        if (!empty($data['attachments']) && is_array($data['attachments'])) {
             // Fetch specifically requested attachments (Drafts + Shared Existing Docs)
             $attachmentsToProcess = $attachmentModel->whereIn('id', $data['attachments'])->findAll();
        } else {
             // Legacy/Fallback: Fetch only drafts
             $attachmentsToProcess = $attachmentModel->where('user_id', $userId)
                                                     ->where('application_id', null)
                                                     ->findAll();
        }

        // Start Transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Generate a Batch ID for this submission session to link independent licenses
        $batchId = $this->generateUuid();
        if (!method_exists($this, 'generateUuid')) {
             $batchId = md5(uniqid(rand(), true));
        }

        try {
            $createdApplications = [];
            $generatedBills = [];

            // Helper to get license details
            $licenseTypeModel = model('App\Models\LicenseTypeModel');
            
            // Initialize models for completion and application updates
            $completionModel = new LicenseCompletionModel();
            $appModel = new LicenseApplicationModel();
            $initAppModel = new InitialApplicationModel();

            // Create Initial Application Record (Parent)
            $initAppData = [
                'id' => $batchId,
                'user_id' => $userId,
                'application_type' => $applicationType ?? 'New',
                'status' => 'Submitted',
                'workflow_stage' => 'Manager' // Default stage
            ];
            
            // Check if we already have this batch (unlikely with UUID but safe)
            if (!$initAppModel->find($batchId)) {
                $initAppModel->insert($initAppData);
            }

            $lastBillData = null;

            foreach ($licenseTypesInput as $item) {
                // Ensure ID exists (link to approved app)
                if (is_string($item) || !isset($item['id'])) continue;
                
                $appId = $item['id'];
                $licenseName = $item['name'] ?? 'License';
                
                
                // Fetch actual License Fee from license_types table (Strict Database Lookup)
                $licenseTypeData = $db->table('license_types')->where('name', $licenseName)->get()->getRow();
                
                if (!$licenseTypeData) {
                    log_message('error', '[LicenseSubmission] License Type not found in DB: ' . $licenseName);
                    // Fallback or Error? Proceeding with item fee but logging error.
                    $fee = isset($item['fee']) ? (float)$item['fee'] : 100000;
                } else {
                    $fee = (float)$licenseTypeData->fee;
                } 
                
                // If it is a completion, try to reuse the existing initial_application_id
                $existingApp = $db->table('license_applications')->select('initial_application_id')->where('id', $appId)->get()->getRow();
                if ($existingApp && !empty($existingApp->initial_application_id)) {
                    $batchId = $existingApp->initial_application_id;
                }                
                // Check Eligibility
                $checkBuilder = $db->table('license_applications');
                $checkBuilder->join('application_reviews as manager_review', "manager_review.application_id = license_applications.id AND manager_review.stage = 'Manager' AND manager_review.status = 'Approved'");
                $checkBuilder->join('application_reviews as surveillance_review', "surveillance_review.application_id = license_applications.id AND surveillance_review.stage = 'Surveillance' AND surveillance_review.status = 'Approved'");
                $checkBuilder->join('interview_assessments', 'interview_assessments.application_id = license_applications.id', 'left');
                $checkBuilder->where('license_applications.id', $appId);
                $checkBuilder->where('license_applications.user_id', $userId);
                $checkBuilder->groupStart();
                    $checkBuilder->where('license_applications.application_type', 'Renewal');
                    $checkBuilder->orGroupStart();
                        $checkBuilder->where('license_applications.application_type !=', 'Renewal');
                        $checkBuilder->where('interview_assessments.result', 'PASS');
                    $checkBuilder->groupEnd();
                $checkBuilder->groupEnd();
                
                $isEligible = ($checkBuilder->countAllResults() > 0);
                
                $billType = 'License Fee';
                $appFee = 50000; // Default Citizen
                
                // Fetch Nationality for Fee Calculation
                $userInfo = $db->table('users')->select('uuid')->where('id', $userId)->get()->getRow();
                $nationality = 'Tanzanian';
                if ($userInfo) {
                    $pInfo = $db->table('practitioner_personal_infos')->where('user_uuid', $userInfo->uuid)->get()->getRow();
                    if ($pInfo && !empty($pInfo->nationality)) {
                        $nationality = $pInfo->nationality;
                    }
                }
                
                $isCitizen = (stripos($nationality, 'Tanzania') !== false);
                
                // Determine Fee Amount based on Citizenship and Application Type
                $feeModel = new ApplicationTypeFeeModel();
                $feeCategory = $isCitizen ? 'Citizen' : 'Non-Citizen';
                $defaultFee = $isCitizen ? 50000 : 200000;
                
                // Fetch configured fee (Use 'New' as default application type if not specified or specific type not found)
                // Note: application_type in DB might match $applicationType variable (New, Renewal, etc.)
                $feeRecord = $feeModel->where('application_type', $applicationType)
                                      ->where('nationality', $feeCategory)
                                      ->first();
                
                // If not found for specific type, try generic 'New' as fallback if current is not 'New'
                if (!$feeRecord && $applicationType !== 'New') {
                     $feeRecord = $feeModel->where('application_type', 'New')
                                      ->where('nationality', $feeCategory)
                                      ->first();
                }

                $appFee = $feeRecord ? (float)$feeRecord['amount'] : $defaultFee;


                if (!$isEligible) {
                    // NEW APPLICATION (Phase 1)
                     log_message('error', '[LicenseSubmission] New Application for: ' . $licenseName . ' (Fee: ' . $appFee . ')');
                     
                     $newAppId = $this->generateUuid(); // Use built-in generateUuid if available or md5/uniqid
                     if (!method_exists($this, 'generateUuid')) {
                        $newAppId = md5(uniqid(rand(), true));
                     } else {
                        $newAppId = $this->generateUuid();
                     }

                     $appData = [
                        'id' => $newAppId,
                        'initial_application_id' => $batchId, // Link independent licenses via Batch ID
                        'user_id' => $userId,
                        'status' => 'Pending',
                        'approval_stage' => 'Manager', // Start flow
                        'application_type' => $applicationType,
                        'total_amount' => $appFee,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                     ];
                     $db->table('license_applications')->insert($appData);
                     
                     // Insert Item
                     $selectedInstruments = $item['selectedInstruments'] ?? [];
                     
                     $itemData = [
                        'id' => md5(uniqid(rand(), true)),
                        'application_id' => $newAppId,
                        'license_type' => $licenseName,
                        'fee' => $appFee, 
                        'selected_instruments' => !empty($selectedInstruments) ? json_encode($selectedInstruments) : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                     ];
                     $db->table('license_application_items')->insert($itemData);

                     // Copy Attachments (Phase 1)
                     if (!empty($attachmentsToProcess)) {
                        foreach ($attachmentsToProcess as $draft) {
                            $attId = md5(uniqid(rand(), true));
                            $attData = [
                               'id' => $attId,
                               'user_id' => $userId,
                               'application_id' => $newAppId,
                               'document_type' => $draft->document_type,
                               'category' => $draft->category, // Preserve category
                               'file_path' => $draft->file_path,
                               'original_name' => $draft->original_name,
                               'mime_type' => $draft->mime_type,
                               'file_content' => $draft->file_content, // Make sure to copy BLOB
                               'status'    => 'Uploaded'
                            ];
                            $attachmentModel->insert($attData);
                        }
                    }
                     
                     // Use NEW ID for Bill and reference
                     $appId = $newAppId;
                     $fee = $appFee;
                     $billType = 'Application Fee';

                } else {
                    // EXISTING APPLICATION (Phase 2 - Completion)
                    log_message('error', '[LicenseSubmission] Completing Eligible App ID: ' . $appId);
                    
                    // Update existing LicenseCompletion record
                    $existingCompletion = $completionModel->where('application_id', $appId)->first();
                
                    // Safely Handle Completion Record
                    if ($existingCompletion) {
                        $completionModel->update($existingCompletion['id'], [
                            'previous_licenses' => $previousLicenses,
                            'qualifications'    => $qualifications,
                            'experiences'       => $experiences,
                            'tools'             => $tools,
                            'declaration'       => $declaration ? 1 : 0
                        ]);
                    } else {
                         // Insert Completion
                         $newCompId = md5(uniqid(rand(), true));
                         $completionModel->insert([
                            'id'                => $newCompId,
                            'application_id'    => $appId,
                            'user_id'           => $userId,
                            'license_type'      => $licenseName,
                            'previous_licenses' => $previousLicenses,
                            'qualifications'    => $qualifications,
                            'experiences'       => $experiences,
                            'tools'             => $tools,
                            'declaration'       => $declaration ? 1 : 0
                        ]);
                    }
                    
                    // Update Status
                    $appModel->update($appId, [
                        'status' => 'Applicant_Submission',
                        'approval_stage' => 'DTS' 
                    ]);
                    
                    // Copy Attachments (Phase 2)
                    if (!empty($attachmentsToProcess)) {
                        foreach ($attachmentsToProcess as $draft) {
                            $attId = md5(uniqid(rand(), true));
                            $attData = [
                               'id' => $attId,
                               'user_id' => $userId,
                               'application_id' => $appId,
                               'document_type' => $draft->document_type,
                               'category' => $draft->category,
                               'file_path' => $draft->file_path,
                               'original_name' => $draft->original_name,
                               'mime_type' => $draft->mime_type,
                               'file_content' => $draft->file_content, // Ensure content is copied
                               'status'    => 'Uploaded'
                            ];
                            $attachmentModel->insert($attData);
                        }
                    }
                    // Fee is determined by input (License Fee)
                    // $fee already set from $item['fee']
                }
                
 
 
                    // 4. Generate Bill (ONLY FOR NEW APPLICATIONS)
                    if (!$isEligible) {
                        $billId = md5(uniqid(rand(), true));
                        $cn = '99' . rand(1000000000, 9999999999);
                        
                        // Map numeric bill type for backward compatibility
                        $billTypeInt = ($billType === 'Application Fee') ? 1 : 2;

                        $billData = [
                            'id' => $billId,
                            'bill_id' => $appId, // Link bill to application
                            'control_number' => $cn,
                            'amount' => $fee,
                            'bill_type' => $billTypeInt,
                            'fee_type' => $billType, // "Application Fee" or "License Fee"
                            'payer_name' => $user->username,
                            'payer_phone' => 'N/A',
                            'bill_description' => $licenseName, // Only license name
                            'bill_expiry_date' => date('Y-m-d', strtotime('+30 days')),
                            'collection_center' => 'Headquarters',
                            'user_id' => $userId,
                            'payment_status' => 'Pending',
                            'items' => json_encode([['name' => $licenseName, 'amount' => $fee]]),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        $db->table('osabill')->insert($billData);
                        
                        $generatedBills[] = [
                            'license' => $licenseName,
                            'controlNumber' => $cn,
                            'amount' => $fee
                        ];
                        
                        $lastBillData = (object)[
                            'controlNumber' => $cn,
                            'amount' => $fee,
                            'billId' => $billId
                        ];
                    }

                $createdApplications[] = $appId;
            }

            // Delete Draft Attachments after successful usage
            if (!empty($attachmentsToProcess)) {
                 foreach ($attachmentsToProcess as $draft) {
                     // Delete the database record ONLY if it was a draft.
                     // Existing shared documents must be preserved.
                     if ($draft->application_id === null) {
                        $attachmentModel->delete($draft->id);
                     }
                 }
            }

            if (empty($createdApplications)) {
                throw new \Exception('No applications were created. Please check selected licenses.');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Transaction failed during separated submission.');
            }

            // Return success. 
            // If multiple applications, do NOT return billData, so frontend redirects to dashboard for individual payment.
            // If single application, return billData to show the modal immediately.
            $responseData = [
                'message' => 'Applications submitted successfully',
                'count' => count($createdApplications),
                'ids' => $createdApplications,
                'bills' => $generatedBills
            ];

            if (count($createdApplications) === 1 && isset($lastBillData)) {
                // Return the last (and only) bill data for the modal
                $responseData['id'] = $createdApplications[0];
                $responseData['billData'] = $lastBillData;
            }

            return $this->respondCreated($responseData);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[LicenseSubmission] Separatation Error: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }
    public function getBill($billId)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $osabillModel = new \App\Models\OsabillModel();
        $bill = $osabillModel->where('bill_id', $billId)->first();

        if (!$bill) {
            return $this->failNotFound('Bill not found');
        }

        // Verify ownership (optional, depending on requirements)
        if ($bill['user_id'] != $user->id) {
             return $this->failForbidden('You are not allowed to view this bill');
        }

        // Convert amount to words (Simple implementation or use a library if available)
        $amountInWords = $this->numberToWords($bill['amount']) . ' Tanzanian Shillings Only.';

        // Prepare response data
        $response = [
            'controlNumber' => $bill['control_number'],
            'spCode' => 'SP419',
            'payer' => [
                'name' => $bill['payer_name'],
                'phone' => $bill['payer_phone'],
            ],
            'billDescription' => $bill['bill_description'], // Or derive from bill_type if needed
            'qrCode' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $bill['control_number'], // Simple QR generation
            'items' => json_decode($bill['items']),
            'totalAmount' => $bill['amount'],
            'amountInWords' => $amountInWords,
            'expiresOn' => date('d/m/Y', strtotime($bill['bill_expiry_date'])),
            'preparedBy' => 'IPA', // System identifier
            'collectionCenter' => 'Kinondoni Wakala Wa Vipimo',
            'printedOn' => date('d M Y'),
        ];

        return $this->respond($response);
    }

    public function getUserBills()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        $builder = $db->table('osabill');
        $builder->select('osabill.*, license_applications.status as application_status');
        $builder->join('license_applications', 'license_applications.id = osabill.bill_id', 'left'); // Assuming bill_id links to application_id
        $builder->where('osabill.user_id', $user->id);

        // Apply Filters
        $controlNumber = $this->request->getGet('controlNumber');
        if (!empty($controlNumber)) {
            $builder->like('osabill.control_number', $controlNumber);
        }

        $licenseType = $this->request->getGet('licenseType');
        if (!empty($licenseType)) {
            $builder->like('osabill.bill_description', $licenseType);
        }

        $feeType = $this->request->getGet('feeType');
        if (!empty($feeType)) {
            // Use the fee_type column directly instead of bill_type
            $builder->where('osabill.fee_type', $feeType);
        }

        $paymentStatus = $this->request->getGet('paymentStatus');
        if (!empty($paymentStatus)) {
            $builder->where('osabill.payment_status', $paymentStatus);
        }

        $fromDate = $this->request->getGet('fromDate');
        if (!empty($fromDate)) {
            $builder->where('osabill.created_at >=', $fromDate . ' 00:00:00');
        }


        $toDate = $this->request->getGet('toDate');
        if (!empty($toDate)) {
            $builder->where('osabill.created_at <=', $toDate . ' 23:59:59');
        }

        $builder->orderBy('osabill.created_at', 'DESC');
        $bills = $builder->get()->getResultArray();

        $data = [];
        foreach ($bills as $bill) {
            $data[] = [
                'id' => $bill['id'],
                'billId' => $bill['bill_id'],
                'controlNumber' => $bill['control_number'],
                'amount' => $bill['amount'],
                'paymentStatus' => $bill['payment_status'],
                'billDescription' => $bill['bill_description'],
                'date' => $bill['created_at'],
                'licenseType' => $bill['bill_description'],
                'billType' => $bill['bill_type'], // Keep for backward compatibility
                'feeType' => $bill['fee_type'] ?? 'N/A' // Add fee_type from database
            ];
        }

        return $this->respond($data);
    }

    public function getUserApplications()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        $builder = $db->table('license_applications');
        $builder->select('
            license_applications.*, 
            license_bill.amount as license_fee_amount, 
            license_bill.control_number as license_control_number, 
            license_bill.payment_status as license_payment_status,
            app_fee_bill.amount as application_fee_amount, 
            app_fee_bill.control_number as application_control_number,
            app_fee_bill.payment_status as application_payment_status,
            practitioner_personal_infos.nationality, 
            practitioner_personal_infos.first_name, 
            practitioner_personal_infos.last_name, 
            interview_assessments.result as interview_result, 
            interview_assessments.total_score,
            interview_assessments.comments as interview_comments, 
            interview_assessments.interview_date, 
            interview_assessments.panel_names, 
            interview_assessments.scores as interview_scores, 
            interview_assessments.theory_score, 
            interview_assessments.practical_score
        ');
        
        // Join for License Fee (bill_type = 2)
        $builder->join('osabill as license_bill', 'license_bill.bill_id = license_applications.id AND license_bill.bill_type = 2', 'left');
        
        // Join for Application Fee (bill_type = 1) - Linked via initial_application_id or direct id if it was a standalone
        $builder->join('osabill as app_fee_bill', '(app_fee_bill.bill_id = license_applications.id OR app_fee_bill.bill_id = license_applications.initial_application_id) AND app_fee_bill.bill_type = 1', 'left');
        
        // Join to get nationality
        $builder->join('users', 'users.id = license_applications.user_id', 'left');
        $builder->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = users.uuid', 'left');
        
        // Join to get interview result
        $builder->join('interview_assessments', 'interview_assessments.application_id = license_applications.id', 'left');

        $builder->where('license_applications.user_id', $user->id);
        $builder->orderBy('license_applications.created_at', 'DESC');
        
        $applications = $builder->get()->getResultArray();
        
        $data = [];
        foreach ($applications as $app) {
            // Get license items for this application
            $itemBuilder = $db->table('license_application_items');
            $itemBuilder->select('license_application_items.*, license_types.fee as type_fee');
            $itemBuilder->join('license_types', 'license_types.name = license_application_items.license_type', 'left');
            $itemBuilder->where('application_id', $app['id']);
            $items = $itemBuilder->get()->getResultArray();
            
            $licenseTypes = array_column($items, 'license_type');
            $licenseClass = !empty($licenseTypes) ? implode(', ', $licenseTypes) : 'N/A';

            // Calculate Fees from Items
            $totalLicenseFee = 0;
            $totalAppFee = 0;
            foreach ($items as $item) {
                $totalLicenseFee += (float)($item['type_fee'] ?? 0);
                $totalAppFee += (float)($item['fee'] ?? 0); 
            }
            
            // Determine progress and steps based on status and approval_stage
            $stageMap = [
                'Manager' => 1,
                'Surveillance' => 2,
                'DTS' => 3,
                'CEO' => 4,
                'Completed' => 5
            ];
            $dbStage = $app['approval_stage'] ?? ($app['current_stage'] ?? 'Manager'); 
            $currentStage = is_numeric($dbStage) ? (int)$dbStage : ($stageMap[$dbStage] ?? 1);
            
            $status = $app['status'];
            
            // Get interview result not just for display but logic
            $interviewResult = $app['interview_result'] ?? null;
            
            // Show advanced stages (DTS & CEO) only if stage is beyond Surveillance (3+)
            // This happens after applicant submits the License Application Module
            $showAdvancedStages = ($currentStage >= 3);
            
            // Applicant Action: Fill License Application Module
            // Unlocked when Surveillance Approves (and implicity Exam Passed)
            $canFillLicenseApp = ($status === 'Approved_Surveillance');

            // Base steps (always shown): Regional Manager and Surveillance
            $steps = [
                ['number' => 1, 'title' => 'Regional Manager', 'subtitle' => 'Initial Review', 'status' => 'pending', 'approver' => $app['approver_stage_1'] ?? null],
                ['number' => 2, 'title' => 'Surveillance', 'subtitle' => 'Compliance', 'status' => 'pending', 'approver' => $app['approver_stage_2'] ?? null],
            ];
            
            // Only add Technical Director and CEO stages if advanced stages are active
            if ($showAdvancedStages) {
                $steps[] = ['number' => 3, 'title' => 'Technical Director', 'subtitle' => 'Technical Review', 'status' => 'pending', 'approver' => $app['approver_stage_3'] ?? null];
                $steps[] = ['number' => 4, 'title' => 'CEO', 'subtitle' => 'Final Approval', 'status' => 'pending', 'approver' => $app['approver_stage_4'] ?? null];
            }
            
            $totalSteps = count($steps); // Will be 2 or 4 depending on interview result

            $progress = 0;

            // normalize status for easier logic
            $normalizedStatus = $status;
            if (in_array($status, ['Approved_CEO', 'License_Generated', 'Approved'])) {
                $normalizedStatus = 'Approved';
            } elseif (in_array($status, ['Submitted', 'Approved_DTS', 'Pending', 'Approved_Stage_1', 'Approved_Stage_2', 'Approved_Stage_3'])) {
                $normalizedStatus = 'Pending';
            }

            if ($normalizedStatus === 'Approved') {
                $progress = 100;
                foreach ($steps as &$step) {
                    $step['status'] = 'completed';
                }
            } elseif ($status === 'Returned') {
                $progress = 10;
                $steps[0]['status'] = 'current'; 
            } else {
                // Pending/In Progress
                // Calculate progress based on actual number of steps
                $progressPerStep = 100 / $totalSteps;
                $progress = ($currentStage - 1) * $progressPerStep;

                for ($i = 0; $i < $totalSteps; $i++) {
                    $stepNum = $i + 1;
                    if ($stepNum < $currentStage) {
                        $steps[$i]['status'] = 'completed';
                    } elseif ($stepNum == $currentStage) {
                        // If it's the very first stage, show as pending (gray) as per user request
                        if ($currentStage == 1) {
                            $steps[$i]['status'] = 'pending';
                        } else {
                            $steps[$i]['status'] = 'current';
                        }
                    } else {
                        $steps[$i]['status'] = 'pending';
                    }
                }
            }
            
            // Map status color
            $statusColor = 'bg-gray-500';
            if ($normalizedStatus === 'Approved') $statusColor = 'bg-green-500';
            if ($normalizedStatus === 'Pending') $statusColor = 'bg-yellow-500';
            if ($status === 'Returned') $statusColor = 'bg-red-500';
            // Add a blue for in-progress if not just pending
            if ($normalizedStatus === 'Pending' && $currentStage > 1) $statusColor = 'bg-blue-500 text-white';
            // Status Tag text fix if necessary (Backend returns specific string, frontend displays it)
            // If user wants 'Approved' instead of 'Approved_CEO' display, we could override $app['status'] or create a new display field.
            // But frontend typically uses {{ app.status }}. 
            // If the user wants "Approved" to be shown, we should update $status variable passed to frontend.
            if ($normalizedStatus === 'Approved') $status = 'Approved';

            // Application Fee Logic
            // User Request: "ANGALIA total amount kwenye table ya license_applications ndio fee ya application inapaswa kua desplayed hapo"
            $nationality = $app['nationality'] ?? 'Tanzanian';
            $isTanzanian = strcasecmp($nationality, 'Tanzanian') === 0;
            
            // Use calculated values from items
            $applicationFee = $totalAppFee;
            $licenseFee = $totalLicenseFee;

            // Format text
            $applicationFeeText = number_format($applicationFee, 2);
            if ($isTanzanian) {
                 $applicationFeeText .= ' (Tanzanian)';
            } else {
                 $applicationFeeText .= ' (Non-Tanzanian)';
            }

            // Check bill status for license fee workflow
            // Safely get control number - check license fee first (Phase 2), then application fee, then application table
            if (!empty($app['license_control_number'])) {
                $controlNumber = $app['license_control_number'];
                $billStatus = $app['license_payment_status'];
            } elseif (!empty($app['application_control_number'])) {
                $controlNumber = $app['application_control_number'];
                $billStatus = $app['application_payment_status'];
            } elseif (!empty($app['control_number'])) {
                $controlNumber = $app['control_number'];
                $billStatus = null; // Status from app table itself if it exists, otherwise null
            } else {
                $controlNumber = null;
                $billStatus = null;
            }

            $data[] = [
                'id' => $controlNumber ? $controlNumber : 'APP-' . substr($app['id'], 0, 8),
                'original_id' => $app['id'],
                'applicant_name' => trim(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')),
                'status' => $status,
                'statusColor' => $statusColor,
                'licenseClass' => $licenseClass,
                'date' => date('M d, Y', strtotime($app['created_at'])),
                'licenseFee' => $licenseFee,
                'applicationFee' => $applicationFee,
                'applicationFeeText' => $applicationFeeText, // For UI display
                'nationality' => $nationality,
                'progress' => $progress,
                'steps' => $steps,
                'interview' => [
                    'result' => $interviewResult,
                    'total' => $app['total_score'] ?? 0,
                    'theory' => $app['theory_score'] ?? 0,
                    'practical' => $app['practical_score'] ?? 0,
                    'comment' => $app['interview_comments'] ?? '',
                    'date' => $app['interview_date'],
                    'panel' => $app['panel_names']
                ],
                'canFillLicenseApp' => $canFillLicenseApp,
                'bill_status' => $billStatus, // For license fee workflow
                'control_number' => $controlNumber,
                // Explicitly return license specific bill details for "Request Control Number" logic
                'licenseControlNumber' => $app['license_control_number'] ?? null,
                'licensePaymentStatus' => $app['license_payment_status'] ?? null
            ];
        }

        return $this->respond($data);
    }

    private function numberToWords($number) {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Forty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );
    
        if (!is_numeric($number)) {
            return false;
        }
    
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }
    
        if ($number < 0) {
            return $negative . $this->numberToWords(abs($number));
        }
    
        $string = $fraction = null;
    
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
    
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->numberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->numberToWords($remainder);
                }
                break;
        }
    
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
    
        return $string;
    }
    public function getApplicationFees()
    {
        $feeModel = model('App\Models\ApplicationTypeFeeModel');
        $fees = $feeModel->findAll();
        return $this->respond($fees);
    }

    public function getLicenseTypes()
    {
        $model = model('App\Models\LicenseTypeModel');
        $types = $model->findAll();
        return $this->respond($types);
    }
    public function checkEligibility()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        
        // Check if there is ANY application in 'Approved_Surveillance' status
        // This unlocks the License Application Module for the applicant to complete details.
        $builder = $db->table('license_applications');
        $builder->where('license_applications.user_id', $user->id);
        $builder->where('license_applications.status', 'Approved_Surveillance');
        
        $count = $builder->countAllResults();
        
        return $this->respond([
            'canApply' => $count > 0,
            'message' => 'Checked eligibility based on Surveillance approval.'
        ]);
    }

    public function getEligibleApplications()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        
        $builder = $db->table('license_applications');
        $builder->select('license_applications.id, license_applications.status, license_applications.application_type, license_application_items.license_type as name, license_application_items.fee, license_application_items.selected_instruments'); 
        
        // 1. Join Application Items (for name/fee)
        $builder->join('license_application_items', 'license_application_items.application_id = license_applications.id');
        
        // 2. Verified Manager Approval (Must exist and be Approved)
        $builder->join('application_reviews as manager_review', "manager_review.application_id = license_applications.id AND manager_review.stage = 'Manager' AND manager_review.status = 'Approved'");
        
        // 3. Verified Surveillance Approval (Must exist and be Approved)
        $builder->join('application_reviews as surveillance_review', "surveillance_review.application_id = license_applications.id AND surveillance_review.stage = 'Surveillance' AND surveillance_review.status = 'Approved'");

        // 4. Join Exam Results (Left join because Renewals don't need it, but we filter later)
        $builder->join('interview_assessments', 'interview_assessments.application_id = license_applications.id', 'left');
        
        $builder->where('license_applications.user_id', $user->id);
        
        // Exclude applications that have already been submitted or processed further
        // STRICT FILTER: Only allow applications that are explicitly at the 'Approved_Surveillance' stage
        // This is the ONLY stage where an applicant can "complete" the form (add qualifications, tools, etc.)
        $builder->where('license_applications.status', 'Approved_Surveillance');
        
        // 6. Strict Logic for New vs Renewal
        // If Renewal: Just the approvals above are enough (implied by the joins).
        // If New: Must ALSO have interview_assessments.result = 'PASS' (uppercase enum)
        $builder->groupStart();
            $builder->where('license_applications.application_type', 'Renewal');
            $builder->orGroupStart();
                $builder->where('license_applications.application_type !=', 'Renewal'); 
                $builder->where('interview_assessments.result', 'PASS');
            $builder->groupEnd();
        $builder->groupEnd();

        // 7. Grouping to avoid duplicates from joins
        $builder->groupBy('license_applications.id, license_application_items.license_type, license_application_items.fee, license_application_items.selected_instruments');
        
        $query = $builder->get();
        $applications = $query->getResult();
        
        // Map to format expected by frontend (name, type, etc.)
        $eligible = [];
        foreach ($applications as $app) {
             $instruments = [];
             if (!empty($app->selected_instruments)) {
                 try {
                     $instruments = json_decode($app->selected_instruments, true);
                     if (!is_array($instruments)) $instruments = [];
                 } catch (\Exception $e) {
                     $instruments = [];
                 }
             }

             $eligible[] = [
                 'id' => $app->id, // The application ID
                 'name' => $app->name, // License Type Name (e.g., Class A)
                 'fee' => $app->fee,
                 'type' => $app->application_type ?? 'New', // Defaulting to New
                 'manager_approval' => 'Approved', // Guaranteed by Join
                 'surveillance_approval' => 'Approved', // Guaranteed by Join
                 'interview_status' => (strcasecmp($app->application_type ?? '', 'Renewal') === 0) ? 'N/A' : 'PASS', // Guaranteed by filter for New
                 'selected_instruments' => $instruments,
                 'date' => null
             ];
        }

        return $this->respond($eligible);
    }

    /**
     * Generate license fee and control number for approved application
     * POST /api/license/generate-fee/{applicationId}
     */
    public function generateLicenseFee($applicationId)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $appModel = new LicenseApplicationModel();
        $billModel = new \App\Models\LicenseBillModel();

        // Verify application exists and belongs to user
        $application = $appModel->where('id', $applicationId)
                                ->where('user_id', $user->id)
                                ->first();

        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        // Check if application is approved
        if ($application['status'] !== 'Approved' && $application['status'] !== 'Approved_CEO') {
            return $this->fail('Application must be approved before generating license fee');
        }

        // Check if bill already exists
        $existingBill = $billModel->getBillByApplicationId($applicationId);
        if ($existingBill) {
            return $this->respond([
                'message' => 'Bill already exists',
                'bill' => $existingBill
            ]);
        }

        // Calculate license fee (from master license_types table)
        $itemModel = new LicenseApplicationItemModel();
        $licenseTypeModel = new \App\Models\LicenseTypeModel();
        
        $items = $itemModel->where('application_id', $applicationId)->findAll();

        $licenseFee = 0;
        // Strictly only calculating from these items, no extra application fee additions here
        
        // Prepare simplified items array for BillLibrary
        $billItems = [];

        foreach ($items as $item) {
            $name = $item->license_type ?? 'License Fee';
            // Find authoritative fee
            $type = $licenseTypeModel->where('name', $name)->first();
            $fee = $type ? $type['fee'] : ($item->fee ?? 0);
            
            $licenseFee += $fee;
            
            $billItems[] = (object)[
                'itemName' => $name,
                'itemAmount' => $fee
            ];
        }

        if ($licenseFee <= 0) {
             return $this->fail('Total license fee amount is zero. Cannot generate bill.');
        }

        // Use BillLibrary to generate GePG Bill
        $billLib = new \App\Libraries\BillLibrary();
        
        // Ensure user is set correctly in library (constructor does it via auth(), but just in case)
        $billLib->setUser($user);
        
        // Generate bill (Type 2 = License Fee)
        $response = $billLib->generateBill($applicationId, $billItems, 2);
        
        if ($response->status == 1) {
            // Bill generated successfully and saved by library
            return $this->respondCreated([
                'message' => 'License fee generated successfully',
                'bill' => $response->billData
            ]);
        } else {
             return $this->fail($response->message ?? 'Failed to generate bill via GePG');
        }
    }

    /**
     * Check payment status for an application
     * GET /api/license/payment-status/{applicationId}
     */
    public function checkPaymentStatus($applicationId)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $appModel = new LicenseApplicationModel();
        $billModel = new \App\Models\LicenseBillModel();

        // Verify application belongs to user
        $application = $appModel->where('id', $applicationId)
                                ->where('user_id', $user->id)
                                ->first();

        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        // Get bill
        $bill = $billModel->getBillByApplicationId($applicationId);

        if (!$bill) {
            return $this->respond([
                'has_bill' => false,
                'payment_completed' => false,
                'message' => 'No bill generated yet'
            ]);
        }

        return $this->respond([
            'has_bill' => true,
            'payment_completed' => $bill['payment_status'] === 'Paid',
            'bill' => $bill
        ]);
    }

    /**
     * View license (only if payment is completed)
     * GET /api/license/view/{applicationId}
     */
    public function viewLicense($applicationId)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $appModel = new LicenseApplicationModel();
        $billModel = new \App\Models\LicenseBillModel();

        // Verify application belongs to user
        $application = $appModel->where('id', $applicationId)
                                ->where('user_id', $user->id)
                                ->first();

        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        // Check payment status
        if (!$billModel->isPaymentCompleted($applicationId)) {
            $bill = $billModel->getBillByApplicationId($applicationId);
            return $this->respond([
                'status' => 402,
                'error' => 'Payment must be completed before viewing license',
                'bill' => $bill,
                'message' => 'Please complete payment to view your license'
            ], 402);
        }

        // Return license details (you can expand this to include actual license document)
        // Get or Create License
        $licenseModel = new \App\Models\LicenseModel();
        
        // Check if license exists
        $license = $licenseModel->where('application_id', $applicationId)->first();
        
        if (!$license) {
            // Create license if it doesn't exist
            // Assuming payment just completed or checking now
            $paymentDate = date('Y-m-d'); // Should ideally come from bill payment time
            $license = $licenseModel->createLicense($applicationId, $paymentDate);
            
            if (!$license) {
                 return $this->failServerError('Failed to create license record');
            }
        }
        
        // Generate License Image
        $generator = new \App\Libraries\LicenseGenerator();
        
        // Prepare data object for generator
        $licenseData = (object)[
            'licenseType' => $license['license_type'],
            'licenseNumber' => $license['license_number'],
            'createdAt' => date('d M Y', strtotime($license['created_at'])), // Issuing Date
            'expiryDate' => date('d M Y', strtotime($license['expiry_date'])),
            'applicantName' => $license['applicant_name'],
            'company' => $license['company_name'],
            'address' => $license['address'],
            'licenseToken' => $license['license_token']
        ];
        
        try {
            $licenseUrl = $generator->generateLicense($licenseData);
            
            return $this->respond([
                'message' => 'License generated successfully',
                'license_url' => $licenseUrl,
                'license_number' => $license['license_number']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'License Generation Exception: ' . $e->getMessage());
            return $this->failServerError('Failed to generate license image: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to generate control number
     */
    private function generateControlNumber()
    {
        // Generate a 12-digit control number starting with 99 (Standard Format)
        // Format: 99 + 10 random digits
        return '99' . rand(1000000000, 9999999999);
    }

    /**
     * Get full application details for CV view
     * GET /api/license/details/{applicationId}
     */
    public function getApplicationDetails($applicationId)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        
        // 1. Fetch Basic Application Data
        $appBuilder = $db->table('license_applications');
        $appBuilder->select('license_applications.*, practitioner_personal_infos.*, license_applications.id as app_id, license_applications.status as app_status, license_applications.created_at as app_date');
        $appBuilder->join('users', 'users.id = license_applications.user_id');
        $appBuilder->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = users.uuid', 'left');
        $appBuilder->where('license_applications.id', $applicationId);
        
        // Security: Ensure it belongs to the user
        $appBuilder->where('license_applications.user_id', $user->id);
        
        $application = $appBuilder->get()->getRowArray();
        
        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        // 2. Fetch License Items (License Name, Fee)
        $itemsBuilder = $db->table('license_application_items');
        $itemsBuilder->where('application_id', $applicationId);
        $items = $itemsBuilder->get()->getResultArray();
        
        // 3. Fetch Completion Data (Qualifications, Experience, Tools, Previous Licenses)
        $compBuilder = $db->table('license_completions');
        $compBuilder->where('application_id', $applicationId);
        $completion = $compBuilder->get()->getRowArray();
        
        // Decode JSON fields if they exist
        $qualifications = isset($completion['qualifications']) ? json_decode($completion['qualifications'], true) : [];
        $experiences = isset($completion['experiences']) ? json_decode($completion['experiences'], true) : [];
        $tools = isset($completion['tools']) ? json_decode($completion['tools'], true) : [];
        $previousLicenses = isset($completion['previous_licenses']) ? json_decode($completion['previous_licenses'], true) : [];
        
        // 4. Fetch Attachments (shared across all applications for this user)
        $attBuilder = $db->table('license_application_attachments');
        $attBuilder->where('user_id', $application['user_id']);
        $attBuilder->orderBy('created_at', 'DESC');
        $attachments = $attBuilder->get()->getResultArray();

        // 5. Fetch Interview Results
        $interviewBuilder = $db->table('interview_assessments');
        $interviewBuilder->where('application_id', $applicationId);
        $interview = $interviewBuilder->get()->getRowArray();

        // Assemble the response
        $response = [
            'personal_info' => [
                'first_name' => $application['first_name'],
                'middle_name' => $application['middle_name'],
                'last_name' => $application['last_name'],
                'email' => $application['email'],
                'phone' => $application['phone_number'],
                'address' => $application['postal_address'],
                'nationality' => $application['nationality'],
                'dob' => $application['date_of_birth'],
                'region' => $application['region'],
                'district' => $application['district'],
                'ward' => $application['ward'],
                'street' => $application['street'],
                'postal_code' => $application['postal_code'],
                'place_of_domicile' => $application['place_of_domicile']
            ],
            'application_info' => [
                'id' => $application['app_id'],
                'control_number' => $application['control_number'],
                'license_number' => $application['license_number'] ?? '',
                'status' => $application['app_status'],
                'date' => $application['app_date'],
                'licenses' => $items,
                'type' => $application['application_type']
            ],
            'qualifications' => $qualifications,
            'experiences' => $experiences,
            'tools' => $tools,
            'previous_licenses' => $previousLicenses,
            'attachments' => $attachments,
            'interview' => $interview
        ];

        return $this->respond($response);
    }

    /**
     * Get user's approved licenses with CEO approval dates and restriction status
     * GET /api/license/approved-licenses
     */
    public function getApprovedLicenses()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        
        // Fetch all submitted or approved licenses for this user
        $builder = $db->table('license_applications');
        $builder->select('license_applications.id, license_applications.updated_at, license_applications.status, license_application_items.license_type');
        $builder->join('license_application_items', 'license_application_items.application_id = license_applications.id');
        $builder->where('license_applications.user_id', $user->id);
        
        // Include ALL statuses that should restrict re-application:
        // 1. Pending/In Progress
        // 2. Approved
        $restrictedStatuses = [
            'Submitted',
            'Pending',
            'Approved_Manager',
            'Approved_Surveillance',
            'Applicant_Submission', 
            'DTS', 
            'Approved_DTS', 
            'Recommend_DTS',
            'Approved_CEO', 
            'License_Generated', 
            'Approved',
            'Returned'
        ];
        
        $builder->whereIn('license_applications.status', $restrictedStatuses);
        $builder->orderBy('license_applications.updated_at', 'DESC');
        
        $applications = $builder->get()->getResultArray();
        
        $result = [];
        foreach ($applications as $app) {
            $isApproved = in_array($app['status'], ['Approved_CEO', 'License_Generated', 'Approved']);
            $isPending = !$isApproved;
            
            $isRestricted = false;
            $daysRemaining = 0;
            $availableDate = null;
            $restrictionType = null; // 'approved_1yr' or 'pending'

            if ($isApproved) {
                // 1-Year Restriction Logic for Approved Licenses
                $approvalDate = new \DateTime($app['updated_at']);
                $now = new \DateTime();
                $oneYearLater = clone $approvalDate;
                $oneYearLater->modify('+1 year');
                
                if ($now < $oneYearLater) {
                    $isRestricted = true;
                    $daysRemaining = $now->diff($oneYearLater)->days;
                    $availableDate = $oneYearLater->format('Y-m-d');
                    $restrictionType = 'approved_1yr';
                }
            } else {
                // Pending Logic - Always restricted while in progress
                $isRestricted = true;
                $restrictionType = 'pending';
            }
            
            if ($isRestricted && $isApproved) {
                $result[] = [
                    'license_type' => $app['license_type'],
                    'status' => $app['status'],
                    'restriction_type' => $restrictionType, // 'approved_1yr' or 'pending'
                    'ceo_approved_at' => $isApproved ? $app['updated_at'] : null,
                    'is_restricted' => true,
                    'days_remaining' => $daysRemaining,
                    'available_date' => $availableDate
                ];
            } else if (!$isApproved) {
                // Return for "Applied" status check but NOT restricted (Red)
                // This helps frontend know it's in progress but show it as Green
                $result[] = [
                    'license_type' => $app['license_type'],
                    'status' => $app['status'],
                    'is_restricted' => false,
                    'restriction_type' => 'pending'
                ];
            }
        }
        
        return $this->respond($result);
    }

    /**
     * Get all documents for a specific application
     * GET /api/license/application/{applicationId}/documents
     */
    public function getApplicationDocuments($applicationId)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $attachmentModel = new LicenseApplicationAttachmentModel();
        
        // Fetch all documents for this application
        $documents = $attachmentModel->where('application_id', $applicationId)
                                     ->where('user_id', $user->id)
                                     ->findAll();

        return $this->respond($documents);
    }
    /**
     * View License Image (On-Demand Generation)
     * GET /api/license/view-image/{licenseNumber}
     */
    public function viewLicenseImage($licenseNumber)
    {
        if (empty($licenseNumber)) {
            return $this->failNotFound('License number is required');
        }

        $licenseNumber = urldecode($licenseNumber); // Decode in case of special chars

        // 1. Calculate deterministic path
        $filename = md5($licenseNumber) . '.jpg';
        $filepath = FCPATH . 'certificates/' . $filename;

        // 2. Check if file exists
        if (!file_exists($filepath)) {
            // Regeneration Logic
            $db = \Config\Database::connect();
            $license = $db->table('licenses')->where('license_number', $licenseNumber)->get()->getRow();

            if (!$license) {
                return $this->failNotFound('License not found in database');
            }

            // Prepare data for generator
            // We need to map DB columns to what LicenseGenerator expects ($data object)
            // LicenseGenerator expects: licenseNumber, licenseType, applicantName, company, address
            
            // Fetch Applicant Name and Company
            // Note: licenses table has applicant_name, company_name, address
            
            $genData = (object) [
                'licenseNumber' => $license->license_number,
                'licenseType' => $license->license_type,
                'applicantName' => $license->applicant_name,
                'company' => $license->company_name,
                'address' => $license->postal_address // or region based on generator logic
            ];
            
            // If address is empty, try to fetch from user info (similar to how createLicense did it)
            if (empty($genData->address) || empty($genData->applicantName)) {
                 // Try to fetch from relation if needed, but 'licenses' table should have snapshot.
                 // Let's rely on licenses table snapshot for consistency.
            }

            $generator = new \App\Libraries\LicenseGenerator();
            $generatedUrl = $generator->generateLicense($genData); // This saves the file to certificates/md5.jpg
            
            // Re-check file existence
            if (!file_exists($filepath)) {
                 return $this->failServerError('Failed to generate license image');
            }
        }

        // 3. Serve the file
        $mime = mime_content_type($filepath);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
