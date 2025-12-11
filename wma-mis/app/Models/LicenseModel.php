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
            
            $app->applied_date = $item->created_at ?? date('Y-m-d H:i:s');
            $app->control_number = $item->control_number ?? '';
            
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
            $app->business_region = $apiData->company_info->region ?? '';
            $app->business_district = $apiData->company_info->district ?? '';
        }
        
        // Attachments (combine required and qualification)
        $app->attachments = [];
        if (isset($apiData->required_attachments)) {
            $app->attachments = array_merge($app->attachments, $apiData->required_attachments);
        }
        if (isset($apiData->qualification_documents)) {
            $app->attachments = array_merge($app->attachments, $apiData->qualification_documents);
        }
        
        // Qualifications
        $app->qualifications = $apiData->qualifications ?? [];
        
        // License Items
        $app->license_items = $apiData->license_items ?? [];
        
        // Approval History
        $app->approvals = $apiData->approval_history ?? [];
        
        // Get approval statuses from the list data (fallback)
        $applications = $this->getFilteredApplications([]);
        foreach ($applications as $listApp) {
            if ((isset($listApp->id) && $listApp->id == $id) || 
                (isset($listApp->application_id) && $listApp->application_id == $id)) {
                $app->region_manager_status = $listApp->region_manager_status ?? 'Pending';
                $app->surveillance_status = $listApp->surveillance_status ?? 'Pending';
                break;
            }
        }
        
        return $app;
    }
}
