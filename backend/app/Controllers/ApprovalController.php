<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class ApprovalController extends BaseController
{
    use ResponseTrait;

    // TODO: Move this to .env
    private $apiKey = 'osa_approval_api_key_12345';

    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/approval/login');
        }

        $data['title'] = 'Approval Page';
        $data['apiKey'] = $this->apiKey; 
        return view('Osa/approval', $data);
    }

    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }
        return view('Osa/login');
    }

    public function processLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if ($username === 'admin' && $password === 'admin') {
            session()->set('is_logged_in', true);
            return redirect()->to('/');
        } else {
            return redirect()->back()->with('error', 'Invalid credentials');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/approval/login');
    }

    public function viewApplication($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/approval/login');
        }

        $data['title'] = 'Application Details';
        $data['apiKey'] = $this->apiKey;
        $data['applicationId'] = $id;
        return view('Osa/application_detail', $data);
    }

    public function getApplications()
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }

        $db = \Config\Database::connect();
        // Query ITEMS primarily now
        $builder = $db->table('license_application_items');
        $builder->select('
            license_application_items.*,
            license_applications.id as application_id,
            license_applications.created_at,
            license_applications.user_id,
            license_applications.approver_stage_1,
            license_applications.approver_stage_2,
            license_applications.approver_stage_3,
            license_applications.approver_stage_4,
            license_applications.status_stage_1,
            license_applications.status_stage_2,
            license_applications.status_stage_3,
            license_applications.status_stage_4,
            osabill.payer_name, 
            osabill.control_number,
            practitioner_personal_infos.first_name,
            practitioner_personal_infos.last_name,
            practitioner_personal_infos.region,
            practitioner_business_infos.company_name
        ');
        
        // Join with Parent Application
        $builder->join('license_applications', 'license_applications.id = license_application_items.application_id');
        
        // Join with Bill Details
        $builder->join('osabill', 'osabill.bill_id = license_applications.id', 'left');
        
        // Join with Interview Assessments for Scores
        // interview_assessments.application_id = license_applications.id
        $builder->select('
            interview_assessments.theory_score,
            interview_assessments.practical_score,
            interview_assessments.total_score,
            interview_assessments.result as interview_result
        ');
        $builder->join('interview_assessments', 'interview_assessments.application_id = license_applications.id', 'left');

        // Join Users and Personal/Business Info
        $builder->join('users', 'users.id = license_applications.user_id', 'left');
        $builder->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = users.uuid', 'left');
        $builder->join('practitioner_business_infos', 'practitioner_business_infos.user_uuid = users.uuid', 'left');
        
        $builder->orderBy('license_applications.created_at', 'DESC');
        
        $items = $builder->get()->getResultArray();

        // Enrich data
        foreach ($items as &$item) {
            // Count attachments for the parent application
            $countBuilder = $db->table('license_application_attachments');
            $countBuilder->where('application_id', $item['application_id']);
            $item['attachment_count'] = $countBuilder->countAllResults();
            
            // Format dates
            $item['created_at_formatted'] = date('M d, Y', strtotime($item['created_at']));
            
            // Construct applicant_name
            if (!empty($item['first_name']) || !empty($item['last_name'])) {
                $item['applicant_name'] = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
            } elseif (!empty($item['payer_name'])) {
                 $item['applicant_name'] = $item['payer_name'];
            } else {
                 // Fallback to username
                 $userBuilder = $db->table('users');
                 $user = $userBuilder->where('id', $item['user_id'])->get()->getRow();
                 $item['applicant_name'] = $user ? ($user->username ?? 'Unknown User') : 'Unknown User';
            }
            
            // Ensure status/stage defaults if null (for legacy/migration compat)
            $item['status'] = $item['status'] ?? 'Submitted';
            $item['approval_stage'] = $item['approval_stage'] ?? 'Manager';
        }

        return $this->respond($items);
    }
    
    public function getApplicationDetails($id)
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }

        $db = \Config\Database::connect();
        
        // 1. Resolve Application ID (Handle Item ID vs App ID)
        // Check if the passed ID is actually an Item ID
        $itemBuilder = $db->table('license_application_items');
        $item = $itemBuilder->where('id', $id)->get()->getRow();
        
        $applicationId = $id; // Default to assuming it's an App ID
        if ($item) {
             $applicationId = $item->application_id;
        }

        // 2. Get main application data using the resolved ID
        $appBuilder = $db->table('license_applications');
        $appBuilder->select('
            license_applications.*,
            osabill.payer_name,
            osabill.control_number,
            osabill.payment_status,
            osabill.amount as bill_amount
        ');
        $appBuilder->join('osabill', 'osabill.bill_id = license_applications.id', 'left');
        $appBuilder->where('license_applications.id', $applicationId);
        $application = $appBuilder->get()->getRow();
        
        if (!$application) {
            return $this->failNotFound('Application not found');
        }
        
        // 2. Get personal information from practitioner_personal_infos
        // This table uses user_uuid, not application_id
        // So we need to: application -> user_id -> user.uuid -> personal_info
        $personalInfo = null;
        $user = null;
        if ($application->user_id) {
            // Get user's UUID
            $userBuilder = $db->table('users');
            $user = $userBuilder->select('uuid, username')->where('id', $application->user_id)->get()->getRow();
            
            if ($user && isset($user->uuid)) {
                 // Get Email from auth_identities
                $identityBuilder = $db->table('auth_identities');
                $identity = $identityBuilder->select('secret as email')->where('user_id', $application->user_id)->where('type', 'email_password')->get()->getRow();
                $user->email = $identity ? $identity->email : '';

                // Now get personal info using user_uuid
                $personalBuilder = $db->table('practitioner_personal_infos');
                $personalBuilder->where('user_uuid', $user->uuid);
                $personalInfo = $personalBuilder->get()->getRow();
            }
        }
        
        // 3. Get company information
        $companyInfo = [
            'company_name' => '', 'tin_number' => '', 'registration_number' => '', 
            'company_phone' => '', 'company_email' => '',
            'region' => '', 'district' => '', 'ward' => '', 'postal_code' => '', 'street' => ''
        ];

        if ($user && isset($user->uuid)) {
            $businessBuilder = $db->table('practitioner_business_infos');
            $business = $businessBuilder->where('user_uuid', $user->uuid)->get()->getRow();
            
            if ($business) {
                $companyInfo = [
                    'company_name' => $business->company_name ?? '',
                    'tin_number' => $business->tin ?? '',
                    'registration_number' => $business->brela_number ?? '',
                    'company_phone' => $business->company_phone ?? '',
                    'company_email' => $business->company_email ?? '',
                    'region' => $business->bus_region ?? '',
                    'district' => $business->bus_district ?? '',
                    'ward' => $business->bus_ward ?? '',
                    'postal_code' => $business->postal_code ?? '',
                    'street' => $business->bus_street ?? ''
                ];
            }
        }
        
        // 4. Get all attachments with category
        $attachmentBuilder = $db->table('license_application_attachments');
        $attachmentBuilder->select('id, user_id, application_id, document_type, original_name as file_name, document_type as type, mime_type, status, category, created_at'); // Include category
        $attachmentBuilder->where('application_id', $applicationId);
        $attachments = $attachmentBuilder->get()->getResult();
        
        // Categorize attachments based on database category field
        $requiredAttachments = [];
        $qualificationAttachments = [];
        
        foreach ($attachments as $attachment) {
            // Use database category if available, otherwise fallback to heuristic
            $category = $attachment->category ?? 'attachment';
            
            if ($category === 'qualification') {
                $qualificationAttachments[] = $attachment;
            } else {
                $requiredAttachments[] = $attachment;
            }
        }
        
        // 5. Get qualifications (from separate table if exists, otherwise empty)
        $qualifications = [];
        // (Skipping separate table check for now as attachments cover docs)
        
        // 6. Get license items
        $itemBuilder = $db->table('license_application_items');
        $itemBuilder->where('application_id', $applicationId);
        $licenseItems = $itemBuilder->get()->getResult();
        
        // Map license items
        foreach ($licenseItems as &$item) {
            $item->license_name = $item->license_type;
            $item->type = $item->license_type;
            $item->amount = $item->fee;
            $item->description = $item->license_type . ' (' . $item->application_type . ')';
            
            // Add Billing Info
            $item->control_number = $application->control_number ?? '-';
            $item->payment_status = $application->payment_status ?? 'Pending';
            $item->application_fee = $application->bill_amount ?? 0;
        }
        
        // 7. Get approval history
        $reviews = $db->table('application_reviews')
                      ->where('application_id', $applicationId)
                      ->get()->getResultArray();
        
        $reviewsByStage = [];
        foreach ($reviews as $r) {
            $reviewsByStage[$r['stage']] = $r;
        }

        $approvalHistory = [];
        // Build approval history from the stage columns
        for ($stage = 1; $stage <= 4; $stage++) {
             $stageName = ['', 'Manager', 'Surveillance', 'DTS', 'CEO'][$stage];
            $approverField = "approver_stage_{$stage}";
            $statusField = "status_stage_{$stage}";
            
            if (isset($application->$approverField) && !empty($application->$approverField)) {
                $review = $reviewsByStage[$stageName] ?? null;
                $approvalHistory[] = [
                    'stage' => $stageName,
                    'approver' => $application->$approverField,
                    'status' => $application->$statusField ?? 'Pending',
                    'date' => $application->updated_at ?? $application->created_at,
                    'comment' => $review ? $review['comments'] : ''
                ];
            }
        }
        
        // 8. Get Completion Data (Tools, Qualifications, etc. from License Completions)
        $completions = $db->table('license_completions')->where('application_id', $applicationId)->get()->getRow();
        
        $toolsList = [];
        $qualificationsList = [];
        $experienceList = [];
        $previousLicensesList = [];

        if ($completions) {
            // Helper to decode JSON safely
            $decodeHelper = function($data) {
                if (empty($data)) return [];
                
                // If it's already an array/object from specific driver behavior
                if (is_array($data) || is_object($data)) return (array)$data;
                
                $decoded = json_decode($data);
                
                // Handle double encoding if necessary
                if (is_string($decoded)) {
                     $decoded = json_decode($decoded);
                }
                
                return json_last_error() === JSON_ERROR_NONE ? ($decoded ?? []) : [];
            };

            $toolsList = $decodeHelper($completions->tools);
            $qualificationsList = $decodeHelper($completions->qualifications);
            $experienceList = $decodeHelper($completions->experiences);
            $previousLicensesList = $decodeHelper($completions->previous_licenses);
        }

        // 9. Build comprehensive response
        $response = [
            'application_info' => [
                'id' => $application->id,
                'application_type' => $application->application_type ?? 'New',
                'status' => $application->status ?? 'Submitted',
                'control_number' => $application->control_number ?? '',
                'license_number' => $application->license_number ?? '',
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at
            ],
            'personal_info' => $personalInfo ? [
                'first_name' => mb_convert_encoding($personalInfo->first_name ?? '', 'UTF-8', 'UTF-8'),
                'middle_name' => mb_convert_encoding($personalInfo->second_name ?? '', 'UTF-8', 'UTF-8'),
                'last_name' => mb_convert_encoding($personalInfo->last_name ?? '', 'UTF-8', 'UTF-8'),
                'nida' => mb_convert_encoding($personalInfo->identity_number ?? '', 'UTF-8', 'UTF-8'),
                'passport_number' => mb_convert_encoding($personalInfo->passport_number ?? '', 'UTF-8', 'UTF-8'),
                'nationality' => mb_convert_encoding($personalInfo->nationality ?? '', 'UTF-8', 'UTF-8'),
                'gender' => mb_convert_encoding($personalInfo->gender ?? '', 'UTF-8', 'UTF-8'),
                'dob' => $personalInfo->dob ?? '',
                'phone_number' => mb_convert_encoding($personalInfo->phone ?? '', 'UTF-8', 'UTF-8'),
                'email' => mb_convert_encoding(isset($user->email) ? $user->email : '', 'UTF-8', 'UTF-8'),
                'region' => mb_convert_encoding($personalInfo->region ?? '', 'UTF-8', 'UTF-8'),
                'district' => mb_convert_encoding($personalInfo->district ?? '', 'UTF-8', 'UTF-8'),
                'ward' => mb_convert_encoding($personalInfo->ward ?? '', 'UTF-8', 'UTF-8'),
                'street' => mb_convert_encoding($personalInfo->street ?? '', 'UTF-8', 'UTF-8'),
                'postal_address' => ''
            ] : null,
            'company_info' => $companyInfo,
            'required_attachments' => $requiredAttachments,
            'qualification_documents' => $qualificationAttachments,
            'qualifications' => $qualifications,
            'license_items' => $licenseItems,
            'approval_history' => $approvalHistory,
            'tools_list' => $toolsList,
            'qualifications_list' => $qualificationsList,
            'experience_list' => $experienceList,
            'previous_licenses_list' => $previousLicensesList
        ];
        
        // Clean all UTF-8 encoding issues before returning JSON
        $response = $this->cleanUtf8($response);
        
        // Manually encode JSON with UTF-8 flags to avoid CodeIgniter's formatter issues
        $json = json_encode($response, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
        
        if ($json === false) {
            // If still failing, return error
            return $this->failServerError('Failed to encode response: ' . json_last_error_msg());
        }
        
        return $this->response
            ->setContentType('application/json')
            ->setBody($json);
    }
    
    /**
     * Recursively clean UTF-8 encoding issues in data
     */
    private function cleanUtf8($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'cleanUtf8'], $data);
        } elseif (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->cleanUtf8($value);
            }
            return $data;
        } elseif (is_string($data)) {
            // More aggressive UTF-8 cleaning - strip invalid characters
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $data);
            return $clean !== false ? $clean : '';
        }
        return $data;
    }
    
    public function updateApplicationStatus()
    {
         $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }
        
        $json = $this->request->getJSON();
        $passedId = $json->appId ?? $json->application_id ?? null;
        $action = $json->action ?? 'Approve'; // Approve, Reject, Pending
        $comment = $json->comment ?? '';
        
        if (!$passedId) {
            return $this->fail('Missing Application ID');
        }
        
        $db = \Config\Database::connect();
        
        // 1. Resolve Parent Application ID
        $parentAppId = $passedId;
        $itemModel = new \App\Models\LicenseApplicationItemModel();
        $item = $itemModel->find($passedId);
        
        if ($item) {
            $parentAppId = $item->application_id;
        } else {
            // Check if it is already the parent ID
            $check = $db->table('license_applications')->select('id')->where('id', $passedId)->get()->getRow();
            if (!$check) {
                 return $this->failNotFound('Application not found');
            }
        }
        
        // 1.5 Check for Returned Documents
        $hasReturnedDocs = $db->table('license_application_attachments')
                              ->where('application_id', $parentAppId)
                              ->where('status', 'Returned')
                              ->countAllResults() > 0;
        
        if ($hasReturnedDocs) {
            return $this->fail('Cannot proceed: Application has documents that need correction (Returned status).');
        }

        // 2. Get Current Status from Parent
        $appBuilder = $db->table('license_applications');
        $app = $appBuilder->where('id', $parentAppId)->get()->getRow();
        
        // Fallback to item stage if parent stage is missing
        $currentStage = $app->approval_stage ?? ($item->approval_stage ?? 'Manager'); 
        
        // 2.5 Surveillance Exam Rule: If Failed, block Approve
        if ($currentStage === 'Surveillance' && $action === 'Approve') {
             $assessment = $db->table('interview_assessments')
                              ->where('application_id', $parentAppId)
                              ->get()->getRow();
             
             if ($assessment && (strtoupper($assessment->result) === 'FAIL')) {
                 return $this->fail('Cannot approve: Applicant has FAILED the exam. Only Rejection is allowed.');
             }
        }
        
        $nextStage = $currentStage;
        $newStatus = $app->status; 
        
        if ($action === 'Reject') {
            $newStatus = 'Rejected';
        } elseif ($action === 'Pending') {
             $newStatus = 'Pending';
        } else {
            // Approve / Next
            switch ($currentStage) {
                case 'Manager':
                    $nextStage = 'Surveillance';
                    $newStatus = 'Approved_Manager';
                    break;
                case 'Surveillance':
                    // Surveillance Approval -> Unlocks Applicant License Module
                    // We DO NOT move to DTS yet. We move to 'Applicant_Submission' or stay at Surveillance but status 'Approved_Surveillance'?
                    // User Request: "ndipo kwenye My Applications zitaongezwa hatua za DTS na CEO"
                    // Meaning the approval STOPS here for the officer side until applicant acts.
                    // Let's set nextStage to 'Applicant' or keep 'Surveillance' but status 'Approved_Surveillance'.
                    // Actually, if we set status 'Approved_Surveillance', the applicant portal can detect this and show the module.
                    
                    $nextStage = 'Surveillance'; // Stay until applicant submits
                    $newStatus = 'Approved_Surveillance';
                    break;
                case 'DTS':
                    $nextStage = 'CEO';
                    $newStatus = 'Approved_DTS';
                    break;
                case 'CEO':
                    $nextStage = 'Completed';
                    $newStatus = 'Approved';
                    break;
                default:
                    $nextStage = 'Completed';
                    $newStatus = 'Approved';
                    break;
            }
        }
        
        $updateData = [
            'approval_stage' => $nextStage,
            'status' => $newStatus
        ];

        // Map string stage to numeric stage for database column
        $numericStage = 0;
        switch ($currentStage) {
            case 'Manager': $numericStage = 1; break;
            case 'Surveillance': $numericStage = 2; break;
            case 'DTS': $numericStage = 3; break;
            case 'CEO': $numericStage = 4; break;
        }

        if ($numericStage > 0) {
            $approverName = session()->get('username') ?? 'Admin'; 
            $updateData["approver_stage_{$numericStage}"] = $approverName;
            
            $statusDecision = ($action === 'Pending') ? 'Pending' : (($action === 'Reject') ? 'Rejected' : 'Approved');
            $updateData["status_stage_{$numericStage}"] = $statusDecision;
            
             // 4. Insert Review Comment
            if (!empty($comment) || $statusDecision !== 'Pending') {
                $reviewData = [
                    'id' => $this->guidv4(),
                    'application_id' => $parentAppId,
                    'application_type' => 'License',
                    'stage' => $currentStage,
                    'status' => $statusDecision,
                    'comments' => $comment,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $db->table('application_reviews')->insert($reviewData);
            }
        }
        
        $appBuilder->where('id', $parentAppId)->update($updateData);
        
        // Update Item as well if it exists
        if ($item) {
             $itemModel->update($item->id, $updateData);
        }
        
        return $this->respond(['message' => 'Status updated successfully', 'stage' => $nextStage, 'status' => $newStatus]);
    }

    public function updateExamScores()
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }
        
        $json = $this->request->getJSON();
        $applicationId = $json->application_id ?? null; // This is likely the LicenseApplicationItem ID from frontend
        $theoryScore = $json->theory_score ?? null;
        $practicalScore = $json->practical_score ?? null;

        if (!$applicationId) {
            return $this->fail('Missing Application ID');
        }

        $db = \Config\Database::connect();
        
        // We need to determine if $applicationId is the Item ID or Main App ID.
        // Frontend uses what getApplications returned.
        // getApplications returned "license_application_items.*" and "license_applications.id as application_id".
        // HOWEVER, wma-mis code uses $app->id. 
        // If wma-mis $app came from getApplications, and that function selects "license_application_items.*" AFTER "license_applications.id as application_id", 
        // the "id" field in the result row would be from license_application_items (because items.* overwrites common columns unless excluded).
        // Let's assume $applicationId is the `license_application_items.id`.
        
        // The `interview_assessments` table likely links to `application_id` (main application) or `item_id`.
        // Let's check `interview_assessments` table definition or assume it links to the parent `license_applications`.
        
        // Step 1: Resolve Item ID to Parent Application ID (if needed) or finding the link.
        $itemModel = new \App\Models\LicenseApplicationItemModel();
        $item = $itemModel->find($applicationId);
        
        if ($item) {
            $parentAppId = $item->application_id;
            // The itemId is indeed $applicationId
        } else {
             // Maybe it WAS the parent app Id? Let's check license_applications
             $appBuilder = $db->table('license_applications');
             $app = $appBuilder->where('id', $applicationId)->get()->getRow();
             if ($app) {
                 $parentAppId = $app->id;
                 // It matches a parent app.
             } else {
                 return $this->failNotFound('Application not found');
             }
        }

        // Now update/insert into interview_assessments
        $assessmentBuilder = $db->table('interview_assessments');
        $exists = $assessmentBuilder->where('application_id', $parentAppId)->get()->getRow();
        
        $totalScore = floatval($theoryScore) + floatval($practicalScore);
        $result = $totalScore >= 50 ? 'PASS' : 'FAIL'; // Enum is uppercase

        $data = [
            'theory_score' => $theoryScore,
            'practical_score' => $practicalScore,
            'total_score' => $totalScore,
            'result' => $result,
            'comments' => 'Score updated via Exam Remark',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($exists) {
            $assessmentBuilder->where('id', $exists->id);
            $updated = $assessmentBuilder->update($data);
        } else {
            // New entry - Generate UUID and set created_at
            $data['id'] = $this->guidv4();
            $data['application_id'] = $parentAppId;
            $data['created_at'] = date('Y-m-d H:i:s');
            
            $updated = $assessmentBuilder->insert($data);
        }

        if ($updated) {
            // Also update the license_application_items status if passed?
            // Optional, but good for consistency.
            return $this->respond(['message' => 'Exam scores updated successfully']);
        } else {
            return $this->fail('Failed to update exam scores');
        }
    }

    /**
     * Generate a UUID v4
     */
    private function guidv4($data = null) {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
