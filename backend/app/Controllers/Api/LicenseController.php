<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LicenseApplicationModel;
use App\Models\LicenseApplicationItemModel;
use App\Models\LicenseApplicationAttachmentModel;
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
        $mimeType = $file->getMimeType();

        $attachmentModel = new LicenseApplicationAttachmentModel();
        $docType = $this->request->getPost('documentType');
        $applicationId = $this->request->getPost('applicationId');

        // If applicationId is provided, we are updating a specific application's document
        if ($applicationId && $applicationId !== 'null' && $applicationId !== 'undefined') {
            // Verify application belongs to user (optional but recommended security check)
            $appModel = new LicenseApplicationModel();
            $app = $appModel->where('id', $applicationId)->where('user_id', $user->id)->first();
            
            if (!$app) {
                return $this->failForbidden('Invalid application ID');
            }

            // Check if a document of this type already exists for this application
            $existingDoc = $attachmentModel->where('application_id', $applicationId)
                                           ->where('document_type', $docType)
                                           ->first();
            
            if ($existingDoc) {
                // Delete the existing document to replace it
                $attachmentModel->delete($existingDoc->id);
            }
        } else {
            // Draft mode: Check if a document of this type already exists for the user (unattached)
            $existingDoc = $attachmentModel->where('user_id', $user->id)
                                           ->where('document_type', $docType)
                                           ->where('application_id', null)
                                           ->first();
    
            if ($existingDoc) {
                // Delete the existing document
                $attachmentModel->delete($existingDoc->id);
            }
            $applicationId = null; // Ensure it's null for insertion
        }

        $id = md5(uniqid(rand(), true));

        $data = [
            'id'             => $id,
            'user_id'        => $user->id,
            'application_id' => $applicationId,
            'document_type'  => $docType,
            'file_path'      => null, // Not using file system
            'original_name'  => $file->getClientName(),
            'mime_type'      => 'application/pdf', // Enforced by validation
            'file_content'   => $fileContent // Storing as BLOB
        ];

        try {
            if (!$attachmentModel->insert($data)) {
                return $this->failServerError('Failed to insert record: ' . json_encode($attachmentModel->errors()));
            }
        } catch (\Exception $e) {
            return $this->failServerError('Exception: ' . $e->getMessage());
        }

        return $this->respondCreated([
            'message' => 'File uploaded successfully',
            'id' => $id,
            'fileName' => $file->getClientName(),
            'status' => 'Uploaded',
            'date' => date('d/m/Y')
        ]);
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
                log_message('error', 'getUserDocuments: keeping ' . $doc->document_type . ' (ID: ' . $doc->id . ')');
                
                // Remove file_content to reduce payload size
                unset($doc->file_content);
                $latestDocs[] = $doc;
                $seenTypes[] = $doc->document_type;
            } else {
                log_message('error', 'getUserDocuments: skipping duplicate ' . $doc->document_type);
            }
        }

        // Check for active licenses (Approved by CEO within the last year) OR In-Progress applications
        $licenseTypes = [
            'Class A - Verification',
            'Class B - Repair',
            'Class C - Manufacturing',
            'Class D - Import/Export'
        ];

        $db = \Config\Database::connect();
        $availableLicenseTypes = [];
        $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));

        foreach ($licenseTypes as $type) {
            // 1. Check for Active/In-Progress Applications (Block these regardless of date)
            $inProgressExists = $db->table('license_application_items')
                ->select('license_application_items.id')
                ->join('license_applications', 'license_applications.id = license_application_items.application_id')
                ->where('license_applications.user_id', $user->id)
                ->where('license_application_items.license_type', $type)
                ->whereIn('license_applications.status', [
                    'Submitted', 
                    'Pending', 
                    'Approved_Stage_1', 
                    'Approved_Stage_2', 
                    'Approved_Stage_3',
                    'Returned' 
                ])
                ->countAllResults();

            // 2. Check for Valid Approved Licenses (Block if approved within last 1 year)
            $approvedExists = $db->table('license_application_items')
                ->select('license_application_items.id')
                ->join('license_applications', 'license_applications.id = license_application_items.application_id')
                ->where('license_applications.user_id', $user->id)
                ->where('license_application_items.license_type', $type)
                ->whereIn('license_applications.status', ['Approved_CEO', 'License_Generated']) 
                ->where('license_applications.updated_at >=', $oneYearAgo)
                ->countAllResults();

            if ($inProgressExists === 0 && $approvedExists === 0) {
                 $availableLicenseTypes[] = [
                    'name' => $type,
                    'type' => 'New', // Default, could be refined
                    'date' => null
                 ];
            }
        }

        // Fetch the most recent active/submitted application for this user
        // This is crucial for restoring the "Initial Application" state in the frontend
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
            'availableLicenseTypes' => $availableLicenseTypes 
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

        // Only allow deleting drafts (application_id is null)
        // If it's attached to an application, we shouldn't delete it as it's part of history
        if ($doc->application_id !== null) {
             return $this->failForbidden('Cannot delete a document that is attached to a submitted application.');
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

        // Here we could update a status field if we had one, e.g., 'submitted'
        // For now, we'll just acknowledge the request as a success
        // You might want to add a 'status' column to the attachments table later

        return $this->respond(['message' => 'Document submitted successfully']);
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
        $data = $this->request->getJSON(true);

        $rules = [
            // 'applicationType' => 'required|in_list[New,Renewal]', // Frontend might not send this yet, defaulting to New
            // 'totalAmount'     => 'required|numeric', // Frontend might not send this yet
            // 'declaration'     => 'required',
        ];

        // Relax validation for now to get it working with current frontend
        // if (!$this->validateData($data, $rules)) {
        //     return $this->failValidationErrors($this->validator->getErrors());
        // }

        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('User not logged in or invalid token');
        }
        $userId = $user->id;

        $applicationType = $data['applicationType'] ?? 'New';
        $totalAmount     = $data['totalAmount'] ?? 0;
        
        // Handle licenseTypes: could be array of strings or objects
        $licenseTypesInput = $data['licenseTypes'] ?? [];
        // If it's a JSON string, decode it (frontend sends array, getJSON handles it, but check)
        if (is_string($licenseTypesInput)) {
            $licenseTypesInput = json_decode($licenseTypesInput, true);
        }

        $previousLicenses = $data['previousLicenses'] ?? [];
        $qualifications   = $data['qualifications'] ?? [];
        $experiences      = $data['experiences'] ?? [];
        $tools            = $data['tools'] ?? [];

        // Start Transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Create Application
            $appModel = new LicenseApplicationModel();
            $appId = $this->generateUuid(); // Standard UUID v4
            
            $appData = [
                'id'                => $appId,
                'user_id'           => $userId,
                'application_type'  => $applicationType,
                'status'            => 'Submitted', // Changed from Pending to Submitted to match validation
                'total_amount'      => $totalAmount,
                'previous_licenses' => json_encode($previousLicenses),
                'qualifications'    => json_encode($qualifications),
                'experiences'       => json_encode($experiences),
                'tools'             => json_encode($tools),
            ];
            
            if ($appModel->insert($appData) === false) {
                 $errors = $appModel->errors();
                 throw new \Exception('Failed to create application: ' . implode(', ', $errors));
            }

            // 2. Create Items (License Types)
            $itemModel = new LicenseApplicationItemModel();
            $billItems = [];

            if (is_array($licenseTypesInput)) {
                foreach ($licenseTypesInput as $item) {
                    $licenseName = '';
                    $fee = 0;

                    if (is_string($item)) {
                        $licenseName = $item;
                        $fee = 100000; // Default fee for now
                    } elseif (is_array($item)) {
                        $licenseName = $item['name'] ?? 'Unknown';
                        $fee = $item['fee'] ?? 0;
                        // Check if 'selected' key exists and is false, skip
                        if (isset($item['selected']) && !$item['selected']) {
                            continue;
                        }
                    }

                    if ($licenseName) {
                        $itemModel->insert([
                            'id'             => $this->generateUuid(),
                            'application_id' => $appId,
                            'license_type'   => $licenseName,
                            'fee'            => $fee,
                            'application_type' => $applicationType // 'New' or 'Renewal'
                        ]);

                        $billItems[] = (object) [
                            'itemName' => $licenseName,
                            'itemAmount' => $fee
                        ];
                    }
                }
            }

            // 3. Link Documents (Drafts)
            $attachmentModel = new LicenseApplicationAttachmentModel();
            $attachmentModel->where('user_id', $userId)
                            ->where('application_id', null)
                            ->set(['application_id' => $appId])
                            ->update();


            // 5. Generate Bill using BillLibrary (handles API and fallback)
            $billLibrary = new \App\Libraries\BillLibrary();
            $billLibrary->setUser($user); // Ensure user is set

            // Format items for BillLibrary
            // BillLibrary expects object with itemName and itemAmount
            $libItems = [];
            foreach ($billItems as $item) {
                $libItems[] = (object) [
                    'itemName' => $item->itemName,
                    'itemAmount' => (float)$item->itemAmount
                ];
            }

            $billResponse = $billLibrary->generateBill($appId, $libItems, 1); // 1 = Application Fee

            if ($billResponse->status !== 1) {
                // If bill generation fails (even with fallback), rollback
                 throw new \Exception('Bill generation failed: ' . ($billResponse->message ?? 'Unknown error'));
            }
            
            // Extract data for response
            $generatedBillData = $billResponse->billData;

            $billResponseMock = (object) [
                'billData' => (object) [
                    'controlNumber' => $generatedBillData->controlNumber,
                    'amount' => $generatedBillData->amount,
                    'billId' => $appId, 
                    'expiryDate' => $generatedBillData->expireDate,
                    'billDesc' => 'License Application Fee' // Or from response
                ]
            ];

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Transaction failed');
            }

            return $this->respondCreated([
                'message' => 'Application submitted successfully',
                'id' => $appId,
                'billData' => $billResponseMock->billData
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[LicenseSubmission] Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
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
            if ($feeType === 'Application Fee') {
                $builder->where('osabill.bill_type', 1);
            } elseif ($feeType === 'License Fee') {
                $builder->where('osabill.bill_type', 2);
            }
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
                'billType' => $bill['bill_type'] // 1 or 2
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
        $builder->select('license_applications.*, license_bill.amount as bill_amount, license_bill.control_number, license_applications.status, practitioner_personal_infos.nationality, interview_assessments.result as interview_result, interview_assessments.scores as interview_scores, interview_assessments.comments as interview_comments, interview_assessments.interview_date, interview_assessments.panel_names, app_fee_bill.amount as application_fee_amount');
        
        // Join for License Fee
        $builder->join('osabill as license_bill', 'license_bill.bill_id = license_applications.id', 'left');
        
        // Join for Application Fee (from Initial Application)
        $builder->join('osabill as app_fee_bill', 'app_fee_bill.bill_id = license_applications.initial_application_id', 'left');
        
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
            $itemBuilder->where('application_id', $app['id']);
            $items = $itemBuilder->get()->getResultArray();
            
            $licenseTypes = array_column($items, 'license_type');
            $licenseClass = !empty($licenseTypes) ? implode(', ', $licenseTypes) : 'N/A';
            
            // Determine progress and steps based on status and current_stage
            $currentStage = isset($app['current_stage']) ? (int)$app['current_stage'] : 1;
            $status = $app['status'];

            $steps = [
                ['number' => 1, 'title' => 'Regional Manager', 'subtitle' => 'Initial Review', 'status' => 'pending', 'approver' => $app['approver_stage_1'] ?? null],
                ['number' => 2, 'title' => 'Surveillance', 'subtitle' => 'Compliance', 'status' => 'pending', 'approver' => $app['approver_stage_2'] ?? null],
                ['number' => 3, 'title' => 'Technical Director', 'subtitle' => 'Technical Review', 'status' => 'pending', 'approver' => $app['approver_stage_3'] ?? null],
                ['number' => 4, 'title' => 'CEO', 'subtitle' => 'Final Approval', 'status' => 'pending', 'approver' => $app['approver_stage_4'] ?? null]
            ];

            $progress = 0;

            if ($status === 'Approved') {
                $progress = 100;
                foreach ($steps as &$step) {
                    $step['status'] = 'completed';
                }
            } elseif ($status === 'Returned') {
                $progress = 10;
                $steps[0]['status'] = 'current'; 
            } else {
                // Pending/In Progress
                // If currentStage is 1, Step 1 is current.
                // If currentStage is 2, Step 1 is completed, Step 2 is current.
                
                // Calculate progress roughly
                $progress = ($currentStage - 1) * 25; 
                // if ($currentStage == 1) $progress = 10; // Removed to start at 0%

                for ($i = 0; $i < 4; $i++) {
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
            if ($status === 'Approved') $statusColor = 'bg-green-500';
            if ($status === 'Pending') $statusColor = 'bg-yellow-500';
            if ($status === 'Returned') $statusColor = 'bg-red-500';
            // Add a blue for in-progress if not just pending
            if ($status === 'Pending' && $currentStage > 1) $statusColor = 'bg-blue-500';

            // Application Fee Logic
            // User Request: "ANGALIA total amount kwenye table ya license_applications ndio fee ya application inapaswa kua desplayed hapo"
            $nationality = $app['nationality'] ?? 'Tanzanian';
            $isTanzanian = strcasecmp($nationality, 'Tanzanian') === 0;
            
            // Use total_amount from license_applications table as the Application Fee
            $applicationFee = $app['total_amount'];
            
            // Fallback for License Fee to bill_amount only, as total_amount is now Application Fee
            $licenseFee = $app['bill_amount'] ?? 0;

            // Format text
            $applicationFeeText = number_format($applicationFee, 2);
            if ($isTanzanian) {
                 $applicationFeeText .= ' (Tanzanian)';
            } else {
                 $applicationFeeText .= ' (Non-Tanzanian)';
            }


            $data[] = [
                'id' => $app['control_number'] ? $app['control_number'] : 'APP-' . substr($app['id'], 0, 8),
                'original_id' => $app['id'],
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
                    'result' => $app['interview_result'] ?? 'Pending',
                    'scores' => $app['interview_scores'],
                    'comments' => $app['interview_comments'],
                    'date' => $app['interview_date'],
                    'panel' => $app['panel_names']
                ]
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
}
