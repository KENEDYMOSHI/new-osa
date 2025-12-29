<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseModel extends Model
{
    protected $licenseTable;
    protected $users;
    protected $applicantParticulars;
    protected $applicantQualifications;
    protected $db;
    protected $tempId;
    protected $licenseType;
    protected $tools;
    protected $attachments;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->licenseTable = $this->db->table('license');
        $this->users = $this->db->table('service_users');
        $this->applicantParticulars = $this->db->table('users');
        $this->applicantQualifications = $this->db->table('applicant_qualifications');
        $this->tempId = $this->db->table('temporary_id');
        $this->licenseType = $this->db->table('license_type');
        $this->tools = $this->db->table('tools');
        $this->attachments = $this->db->table('attachments');
    }


    public function getApplicantParticulars($params)
    {
        return $this->applicantParticulars->select()->where($params)->get()->getRow();
    }
    public function getLicenseApplicationsInRegion($params)
    {
        return $this->applicantParticulars->select()->where($params)
        ->join('license_type', 'license_type.user_id = users.unique_id')
        ->get()
        ->getResult();
    }
    public function getApplicantQualifications($params)
    {
        return $this->applicantQualifications->select()->where($params)->get()->getResult();
    }
    public function addApplicantParticulars($data)
    {
        return $this->applicantParticulars->insert($data);
    }
    public function createApplicationId($data)
    {
        return $this->tempId->insert($data);
    }
    public function getUser($hash)
    {
        return $this->users->select()->where(['hash' => $hash])->get()->getRow();
    }



    public function updateApplicantParticulars($id, $data)
    {
        return $this->applicantParticulars->set($data)->where(['user_id' => $id])->update();
    }

    public function getApplicationId($id)
    {
        return $this->tempId->select()->where(['user_id' => $id])->get()->getRow();
    }

    //=================log the user in====================
    public function login()
    {
    }

    //=================Activating user account====================
    public function addQualification($data)
    {
        return $this->applicantQualifications->insert($data);
    }
    public function getQualifications($params)
    {
        return $this->applicantQualifications->select()->where($params)->get()->getResult();
    }
    public function deleteQualification($params)
    {
        return $this->applicantQualifications->where($params)->delete();
    }
   




    //=====================================
    public function addLicense($data)
    {
        return $this->licenseType->insert($data);
    }
    public function getLicenseType($params)
    {
        return $this->licenseType->select()->where($params)->get()->getResult();
    }

    public function deleteLicense($params)
    {
        return $this->licenseType->where($params)->delete();;
    }



    //=====================================
    public function addTool($data)
    {
        return $this->tools->insert($data);
    }
    public function getTools($params)
    {
        return $this->tools->select()->where($params)->get()->getResult();
    }

    public function deleteTool($params)
    {
        return $this->tools->where($params)->delete();;
    }


    //=====================================
    public function addAttachment($data)
    {
        return $this->attachments->insert($data);
    }
    public function getAttachments($params)
    {
        return $this->attachments->select()->where($params)->get()->getResult();
    }

    public function deleteAttachment($params)
    {
        return $this->attachments->where($params)->delete();
    }
    public function editAttachment($params)
    {
        return $this->attachments->select()->where($params)->get()->getRow();
    }
    public function updateAttachment($id, $data)
    {
        return $this->attachments->set($data)->where(['id' => $id])->update();
    }


    public function submitApplication($applicationId)
    {
        $value = 1;
        $tables = ['applicant_qualifications', 'license_type', 'tools', 'attachments'];
        $data = [];
        foreach ($tables as $table) {
            $sql = "UPDATE $table SET submission=$value WHERE application_id='$applicationId'";
            if($this->db->query($sql)){

                array_push($data, $table);
            }
        }
        if ($tables == $data) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteApplicationId($id)
    {
        return $this->tempId->where(['user_id' => $id])->delete();
    }
    public function getFilteredApplications($filters = [])
    {
        // Fetch data from OSA backend API
        $apiUrl = 'http://localhost:8080/api/approval/applications';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            log_message('error', 'Failed to fetch OSA applications from API. HTTP Code: ' . $httpCode);
            return [];
        }
        
        $apiData = json_decode($response);
        if (!$apiData || !is_array($apiData)) {
            log_message('error', 'Invalid response from OSA API');
            return [];
        }
        
        // Map API data to expected format
        $applications = [];
        foreach ($apiData as $item) {
            $app = new \stdClass();
            $app->id = $item->id ?? 0;
            $app->application_id = $item->application_id ?? '';
            
            // Parse applicant name
            $fullName = $item->applicant_name ?? 'Unknown';
            $nameParts = explode(' ', $fullName, 2);
            $app->first_name = $nameParts[0] ?? '';
            $app->last_name = $nameParts[1] ?? '';
            
            $app->license_type = $item->license_type ?? 'N/A';
            $app->region = $item->region ?? 'N/A';
            $app->company_name = $item->company_name ?? '';
            
            // Map approval statuses
            $app->region_manager_status = $this->mapApprovalStatus($item, 1);
            $app->surveillance_status = $this->mapApprovalStatus($item, 2);
            $app->dts_status = $this->mapApprovalStatus($item, 3);
            $app->ceo_status = $this->mapApprovalStatus($item, 4);
            
            $app->applied_date = $item->created_at ?? date('Y-m-d H:i:s');
            $app->control_number = $item->control_number ?? '';

            // Map exam scores
            $app->theory_score = $item->theory_score ?? null;
            $app->practical_score = $item->practical_score ?? null;
            $app->total_score = $item->total_score ?? null;
            
            $applications[] = $app;
        }
        
        // Apply filters
        $filtered = array_filter($applications, function($app) use ($filters) {
            // Filter by name
            if (!empty($filters['name'])) {
                $searchName = strtolower($filters['name']);
                $fullName = strtolower($app->first_name . ' ' . $app->last_name);
                if (strpos($fullName, $searchName) === false && 
                    strpos(strtolower($app->first_name), $searchName) === false && 
                    strpos(strtolower($app->last_name), $searchName) === false) {
                    return false;
                }
            }
            
            // Filter by region
            if (!empty($filters['region']) && $app->region !== $filters['region']) {
                return false;
            }
            
            // Filter by license type
            if (!empty($filters['license_type']) && $app->license_type !== $filters['license_type']) {
                return false;
            }
            
            // Filter by year
            if (!empty($filters['year'])) {
                $appYear = date('Y', strtotime($app->applied_date));
                if ($appYear != $filters['year']) {
                    return false;
                }
            }
            
            // Filter by date range
            if (!empty($filters['dateRange'])) {
                $dates = explode(' - ', $filters['dateRange']);
                if (count($dates) == 2) {
                    $startDate = strtotime($dates[0]);
                    $endDate = strtotime($dates[1]);
                    $appDate = strtotime($app->applied_date);
                    if ($appDate < $startDate || $appDate > $endDate) {
                        return false;
                    }
                }
            }

            // Filter by status (Strict 4-stage approval for 'Approved'/'Completed')
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'Approved' || $filters['status'] === 'Completed') {
                    // MUST be approved by ALL 4 stages
                    if ($app->region_manager_status !== 'Approved' || 
                        $app->surveillance_status !== 'Approved' || 
                        $app->dts_status !== 'Approved' || 
                        $app->ceo_status !== 'Approved') {
                        return false;
                    }
                } elseif ($filters['status'] === 'Pending') {
                    // Any stage pending
                     if ($app->region_manager_status === 'Pending' || 
                        $app->surveillance_status === 'Pending' || 
                        $app->dts_status === 'Pending' || 
                        $app->ceo_status === 'Pending') {
                        // It is pending somewhere
                    } else {
                        return false; // Not pending (either fully approved or rejected)
                    }
                }
            }
            
            return true;
        });
        
        return array_values($filtered);
    }
    
    private function mapApprovalStatus($item, $stage)
    {
        $statusField = "status_stage_{$stage}";
        $status = $item->$statusField ?? null;
        
        if ($status === 'Approved') {
            return 'Approved';
        } elseif ($status === 'Rejected') {
            return 'Rejected';
        } else {
            return 'Pending';
        }
    }
    
    public function getApplicationById($id)
    {
        // Call the new OSA backend API endpoint to get complete application details
        $apiUrl = 'http://localhost:8080/api/approval/application/' . $id;
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            log_message('error', 'Failed to fetch application details from OSA API. HTTP Code: ' . $httpCode);
            return null;
        }
        
        $apiData = json_decode($response);
        if (!$apiData) {
            log_message('error', 'Invalid response from OSA application details API');
            return null;
        }
        
        // Map API response to expected object structure
        $app = new \stdClass();
        
        // Application Info
        if (isset($apiData->application_info)) {
            $app->id = $apiData->application_info->id ?? $id;
            $app->application_id = $apiData->application_info->id ?? $id;
            $app->application_type = $apiData->application_info->application_type ?? 'New';
            $app->status = $apiData->application_info->status ?? 'Submitted';
            $app->control_number = $apiData->application_info->control_number ?? '';
            $app->license_number = $apiData->application_info->license_number ?? '';
            $app->applied_date = $apiData->application_info->created_at ?? date('Y-m-d');
        }
        
        // Personal Information
        if (isset($apiData->personal_info) && $apiData->personal_info) {
            $app->first_name = $apiData->personal_info->first_name ?? '';
            $app->middle_name = $apiData->personal_info->middle_name ?? '';
            $app->last_name = $apiData->personal_info->last_name ?? '';
            $app->identity_number = $apiData->personal_info->nida ?? '';
            $app->nationality = $apiData->personal_info->nationality ?? '';
            $app->gender = $apiData->personal_info->gender ?? '';
            $app->dob = $apiData->personal_info->dob ?? null;
            $app->phone_number = $apiData->personal_info->phone_number ?? '';
            $app->email = $apiData->personal_info->email ?? '';
            $app->region = $apiData->personal_info->region ?? '';
            $app->district = $apiData->personal_info->district ?? '';
            $app->ward = $apiData->personal_info->ward ?? '';
            $app->street = $apiData->personal_info->street ?? '';
            $app->postal_address = $apiData->personal_info->postal_address ?? '';
        }
        
        // Company Information
        if (isset($apiData->company_info)) {
            $app->company_name = $apiData->company_info->company_name ?? '';
            $app->tin_number = $apiData->company_info->tin_number ?? '';
            $app->registration_number = $apiData->company_info->registration_number ?? '';
            $app->company_phone = $apiData->company_info->company_phone ?? '';
            $app->company_email = $apiData->company_info->company_email ?? '';
            $app->business_region = $apiData->company_info->region ?? '';
            $app->business_district = $apiData->company_info->district ?? '';
            $app->business_ward = $apiData->company_info->ward ?? '';
            $app->postal_code = $apiData->company_info->postal_code ?? '';
            $app->business_street = $apiData->company_info->street ?? '';
        }
        
        // Attachments (combine required and qualification)
        $app->attachments = [];
        
        // Document code to name mapping
        $docMap = [
            'tin' => 'Tax Payer Identification Number (TIN)',
            'businessLicense' => 'Business License',
            'bl' => 'Business License', // Fallback
            'taxClearance' => 'Certificate Of Tax Clearance',
            'tax_clearance' => 'Certificate Of Tax Clearance', // Fallback
            'brela' => 'Certificate of Registration/Incorporation from BRELA',
            'cert_inc' => 'Certificate of Registration/Incorporation from BRELA', // Fallback
            'identity' => 'Identity Card (National ID / Driver\'s License / Voter ID)',
            'id_card' => 'Identity Card (National ID / Driver\'s License / Voter ID)', // Fallback
            
            // Qualifications
            'psle' => 'Primary School Leaving Certificate (PSLE)',
            'csee' => 'Certificate of Secondary Education Examination (CSEE)',
            'acsee' => 'Advanced Certificate of Secondary Education Examination (ACSEE)',
            'veta' => 'Basic Certificate - Vocational Education and Training Authority (VETA)',
            'nta4' => 'Basic Certificate (NTA Level 4)',
            'nta5' => 'Technician Certificate (NTA Level 5)',
            'nta6' => 'Ordinary Diploma (NTA Level 6)',
            'specialized' => 'Other Specialized Certificates',
            'bachelor' => 'Bachelor\'s Degree',
            
            // Legacy/Other
            'lease' => 'Lease Agreement/Title Deed',
            'osha' => 'OSHA Certificate',
            'inspection' => 'Site Inspection Report',
            'tech_staff_cv' => 'Technical Staff CV',
            'tech_staff_cert' => 'Technical Staff Certificates',
            'equip_list' => 'List of Equipment',
            'workshop_layout' => 'Workshop Layout'
        ];

        if (isset($apiData->required_attachments)) {
            foreach($apiData->required_attachments as $att) {
                if(isset($att->document_type) && isset($docMap[$att->document_type])) {
                    $att->document_type = $docMap[$att->document_type];
                }
                $app->attachments[] = $att;
            }
        }
        if (isset($apiData->qualification_documents)) {
             foreach($apiData->qualification_documents as $att) {
                if(isset($att->document_type) && isset($docMap[$att->document_type])) {
                    $att->document_type = $docMap[$att->document_type];
                }
                $app->attachments[] = $att;
            }
        }
        
        // Qualifications
        $app->qualifications = $apiData->qualifications ?? [];
        
        // License Items
        $app->license_items = $apiData->license_items ?? [];
        
        // Approval History
        $app->approvals = $apiData->approval_history ?? [];
        $app->approval_logs = $apiData->approval_history ?? [];
        
        // Completion Data (Tools, Qualifications, etc) - From API Response
        $app->tools_list = $apiData->tools_list ?? [];
        $app->qualifications_list = $apiData->qualifications_list ?? [];
        $app->experience_list = $apiData->experience_list ?? [];
        $app->previous_licenses_list = $apiData->previous_licenses_list ?? [];
        
        // Map status for view compatibility
        $app->application_status = $app->status;

        // Parse approval statuses from history
        // Parse approval statuses from history
        $app->region_manager_status = 'Pending';
        $app->surveillance_status = 'Pending';
        $app->dts_status = 'Pending';
        $app->ceo_status = 'Pending';

        if (!empty($app->approvals)) {
            foreach ($app->approvals as $approval) {
                if (($approval->stage ?? '') === 'Manager') {
                    $app->region_manager_status = $approval->status ?? 'Pending';
                }
                if (($approval->stage ?? '') === 'Surveillance') {
                    $app->surveillance_status = $approval->status ?? 'Pending';
                }
                if (($approval->stage ?? '') === 'DTS') {
                    $app->dts_status = $approval->status ?? 'Pending';
                }
                if (($approval->stage ?? '') === 'CEO') {
                    $app->ceo_status = $approval->status ?? 'Pending';
                }
            }
        }
        
        return $app;
    }
    public function getLicenseTypesFromApi()
    {
        $apiUrl = 'http://localhost:8080/api/approval/license-types';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
             log_message('error', 'Failed to fetch License Types from OSA API');
             return [];
        }
        
        return json_decode($response);
    }

    public function updateExamScores($id, $scores)
    {
        $apiUrl = 'http://localhost:8080/api/approval/update-exam-scores';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $data = [
            'application_id' => $id,
            'theory_score' => $scores['theory_score'],
            'practical_score' => $scores['practical_score']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return true;
        } else {
            log_message('error', 'Failed to update exam scores via API. HTTP Code: ' . $httpCode);
            return false;
        }
    }

    public function updateApplicationStatus($id, $status, $stage, $comment = '')
    {
        $apiUrl = 'http://localhost:8080/api/approval/update-status';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $action = ($status === 'Approved') ? 'Approve' : (($status === 'Rejected') ? 'Reject' : 'Pending');
        
        $data = [
            'appId' => $id,
            'action' => $action,
            'stage' => $stage, // Legacy, kept for reference
            'comment' => $comment,
            'approver_id' => auth()->user()->id ?? 0 // Pass current user ID
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return true;
        } else {
            log_message('error', 'Failed to update application status via API. HTTP Code: ' . $httpCode . ' Response: ' . $response);
            return false;
        }
    }

    /**
     * Get OSA Dashboard Statistics from API
     */
    public function getDashboardStats()
    {
        $apiUrl = 'http://localhost:8080/api/approval/dashboard/osa-stats';
        $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if ($data) {
                return $data;
            }
        }
        
        // Return default/empty stats if API fails
        log_message('error', 'Failed to fetch OSA dashboard stats from API. HTTP Code: ' . $httpCode);
        return [
            'total_applications' => 0,
            'approved_applications' => 0,
            'pending_applications' => 0,
            'rejected_applications' => 0,
            'active_licenses' => 0,
            'expired_licenses' => 0,
            'license_stats' => [],
            'regions' => [],
            'financials' => [
                'total_amount' => 0,
                'application_fee' => 0,
                'license_fee' => 0,
                'paid_fee' => 0,
                'pending_fee' => 0
            ],
            'monthly_data' => []
        ];
    }
}
