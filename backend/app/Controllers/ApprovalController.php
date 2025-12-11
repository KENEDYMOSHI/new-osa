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
            osabill.payer_name as applicant_name, 
            osabill.control_number
        ');
        
        // Join with Parent Application
        $builder->join('license_applications', 'license_applications.id = license_application_items.application_id');
        
        // Join with Bill Details
        $builder->join('osabill', 'osabill.bill_id = license_applications.id', 'left');
        
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
            
            // Fallback for name
            if (empty($item['applicant_name'])) {
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
        
        // 1. Get main application data
        $appBuilder = $db->table('license_applications');
        $appBuilder->select('
            license_applications.*,
            osabill.payer_name,
            osabill.control_number
        ');
        $appBuilder->join('osabill', 'osabill.bill_id = license_applications.id', 'left');
        $appBuilder->where('license_applications.id', $id);
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
            // Get user's UUID and email (email from account creation)
            $userBuilder = $db->table('users');
            $user = $userBuilder->select('uuid, email')->where('id', $application->user_id)->get()->getRow();
            
            if ($user && isset($user->uuid)) {
                // Now get personal info using user_uuid
                $personalBuilder = $db->table('practitioner_personal_infos');
                $personalBuilder->where('user_uuid', $user->uuid);
                $personalInfo = $personalBuilder->get()->getRow();
            }
        }
        
        // 3. Get company information (if exists in separate table, otherwise from license_applications)
        // For now, we'll extract company info from license_applications
        $companyInfo = [
            'company_name' => $application->company_name ?? '',
            'tin_number' => $application->tin_number ?? '',
            'registration_number' => $application->registration_number ?? '',
            'region' => $application->region ?? '',
            'district' => $application->district ?? ''
        ];
        
        // 4. Get all attachments
        $attachmentBuilder = $db->table('license_application_attachments');
        $attachmentBuilder->where('application_id', $id);
        $attachments = $attachmentBuilder->get()->getResult();
        
        // Separate attachments by category
        $requiredAttachments = [];
        $qualificationAttachments = [];
        foreach ($attachments as $attachment) {
            if (isset($attachment->category) && $attachment->category === 'qualification') {
                $qualificationAttachments[] = $attachment;
            } else {
                $requiredAttachments[] = $attachment;
            }
        }
        
        // 5. Get qualifications (if separate table exists)
        $qualifications = [];
        if ($db->tableExists('applicant_qualifications')) {
            $qualBuilder = $db->table('applicant_qualifications');
            // Try by application_id first
            if ($db->fieldExists('application_id', 'applicant_qualifications')) {
                $qualBuilder->where('application_id', $id);
            } elseif ($db->fieldExists('user_id', 'applicant_qualifications') && isset($application->user_id)) {
                $qualBuilder->where('user_id', $application->user_id);
            }
            $qualifications = $qualBuilder->get()->getResult();
        }
        
        // 6. Get license items
        $itemBuilder = $db->table('license_application_items');
        $itemBuilder->where('application_id', $id);
        $licenseItems = $itemBuilder->get()->getResult();
        
        // 7. Get approval history
        $approvalHistory = [];
        // Build approval history from the stage columns
        for ($stage = 1; $stage <= 4; $stage++) {
            $approverField = "approver_stage_{$stage}";
            $statusField = "status_stage_{$stage}";
            
            if (isset($application->$approverField) && !empty($application->$approverField)) {
                $stageName = ['', 'Manager', 'Surveillance', 'DTS', 'CEO'][$stage];
                $approvalHistory[] = [
                    'stage' => $stageName,
                    'approver' => $application->$approverField,
                    'status' => $application->$statusField ?? 'Pending',
                    'date' => $application->updated_at ?? $application->created_at
                ];
            }
        }
        
        // 8. Build comprehensive response
        $response = [
            'application_info' => [
                'id' => $application->id,
                'application_type' => $application->application_type ?? 'New',
                'status' => $application->status ?? 'Submitted',
                'control_number' => $application->control_number ?? '',
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
                'ward' => mb_convert_encoding($personalInfo->town ?? '', 'UTF-8', 'UTF-8'),
                'street' => mb_convert_encoding($personalInfo->street ?? '', 'UTF-8', 'UTF-8'),
                'postal_address' => ''
            ] : null,
            'company_info' => $companyInfo,
            'required_attachments' => $requiredAttachments,
            'qualification_documents' => $qualificationAttachments,
            'qualifications' => $qualifications,
            'license_items' => $licenseItems,
            'approval_history' => $approvalHistory
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
        $itemId = $json->appId ?? null;
        $action = $json->action ?? 'Approve'; // Approve, Reject, Pending
        
        if (!$itemId) {
            return $this->fail('Missing Item ID');
        }
        
        $itemModel = new \App\Models\LicenseApplicationItemModel();
        $item = $itemModel->find($itemId);
        
        if (!$item) {
            return $this->failNotFound('License Item not found');
        }
        
        $currentStage = $item->approval_stage ?? 'Manager';
        $nextStage = $currentStage;
        $newStatus = $item->status; 
        
        if ($action === 'Reject') {
            $newStatus = 'Rejected';
            // Stage stays same or moves to a "Rejected" bin? Let's keep stage but mark status.
        } elseif ($action === 'Pending') {
             $newStatus = 'Submitted'; // Reset to default?
             // Maybe reset stage? Or just status? 
             // "Pending" usually means "Not yet acted on". 
             $newStatus = 'Pending';
        } else {
            // Default: Approve / Next
            switch ($currentStage) {
                case 'Manager':
                    $nextStage = 'Surveillance';
                    $newStatus = 'Approved_Manager';
                    break;
                case 'Surveillance':
                    $nextStage = 'DTS';
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
        
        $data = [
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
            // Get current logged-in user's name (assuming session has user data)
            // For this example, we'll use a placeholder or 'Admin'
            $approverName = session()->get('username') ?? 'Admin'; 
            
            // Save Approver Name for the current stage
            $data["approver_stage_{$numericStage}"] = $approverName;
            
            // Save Status Decision for this stage
            $statusDecision = ($action === 'Pending') ? 'Pending' : (($action === 'Reject') ? 'Rejected' : 'Approved');
            $data["status_stage_{$numericStage}"] = $statusDecision;
        }
        
        $itemModel->update($itemId, $data);
        
        return $this->respond(['message' => 'Status updated successfully', 'stage' => $nextStage, 'status' => $newStatus]);
    }
}
