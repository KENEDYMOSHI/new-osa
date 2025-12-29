<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LicenseApplicationModel;
use CodeIgniter\HTTP\ResponseInterface;

class ApplicationReviewController extends BaseController
{
    protected $licenseModel;

    public function __construct()
    {
        $this->licenseModel = new LicenseApplicationModel();
    }

    /**
     * Get list of approved/completed applications with optional filters
     * Returns only applications belonging to the authenticated user
     * 
     * @return ResponseInterface
     */
    public function getApprovedApplications()
    {
        try {
            // Get authenticated user
            $user = $this->getUserFromToken();
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized'
                ]);
            }

            // Get filter parameters from query string
            $name = $this->request->getGet('name');
            $region = $this->request->getGet('region');
            $licenseType = $this->request->getGet('license_type');
            $year = $this->request->getGet('year');
            $dateRange = $this->request->getGet('dateRange');

            // Build query
            $builder = $this->licenseModel->builder();
            
            // Note: User profile info is in practitioner_personal_info table linked via UUID
            // Note: Email is in auth_identities
            $builder->select('license_applications.*, p.first_name, p.last_name, auth_identities.secret as email, p.phone, i.license_type');
            $builder->join('users', 'users.id = license_applications.user_id', 'left');
            $builder->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left');
            $builder->join('practitioner_personal_infos p', 'p.user_uuid = users.uuid', 'left');
            $builder->join('license_application_items i', 'i.application_id = license_applications.id', 'left');
            
            // IMPORTANT: Filter by current user's applications only
            $builder->where('license_applications.user_id', $user->id);
            
            // Filter by approved/completed status
            // Only show applications that have passed all 4 stages (CEO Approved -> Status = Approved)
            $builder->whereIn('license_applications.status', [
                'Approved',
                'Completed'
            ]);

            // Apply filters
            if ($name) {
                $builder->groupStart()
                    ->like('users.first_name', $name)
                    ->orLike('users.last_name', $name)
                    ->orLike('license_applications.company_name', $name)
                    ->groupEnd();
            }

            if ($region) {
                $builder->where('license_applications.region', $region);
            }

            if ($licenseType) {
            if ($licenseType) {
                $builder->where('i.license_type', $licenseType);
            }
            }

            if ($year) {
                $builder->where('YEAR(license_applications.created_at)', $year);
            }

            if ($dateRange) {
                // Parse date range (format: "MM/DD/YYYY - MM/DD/YYYY")
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $startDate = date('Y-m-d', strtotime($dates[0]));
                    $endDate = date('Y-m-d', strtotime($dates[1]));
                    $builder->where('license_applications.created_at >=', $startDate);
                    $builder->where('license_applications.created_at <=', $endDate);
                }
            }

            // Order by most recent first
            $builder->orderBy('license_applications.created_at', 'DESC');

            $applications = $builder->get()->getResult();

            return $this->response->setJSON([
                'success' => true,
                'data' => $applications,
                'count' => count($applications)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching approved applications: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to fetch applications',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get detailed information for a specific application
     * 
     * @param int $id Application ID
     * @return ResponseInterface
     */
    public function getApplicationDetails($id)
    {
        try {
            // Get authenticated user
            $user = $this->getUserFromToken();
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized'
                ]);
            }

            // Get application with user details
            $builder = $this->licenseModel->builder();
            // Note: User profile info is in practitioner_personal_info table linked via UUID
            $builder->select('license_applications.*, p.first_name, p.last_name, auth_identities.secret as email, p.phone, p.street as address, i.license_type');
            $builder->join('users', 'users.id = license_applications.user_id', 'left');
            $builder->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left');
            $builder->join('practitioner_personal_infos p', 'p.user_uuid = users.uuid', 'left');
            $builder->join('license_application_items i', 'i.application_id = license_applications.id', 'left');
            $builder->where('license_applications.id', $id);
            
            // Security Check: Ensure application belongs to the user
            $builder->where('license_applications.user_id', $user->id);
            
            
            $application = $builder->get()->getRow();

            if (!$application) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Application not found'
                ]);
            }

            // Convert to stdClass if it's an array
            if (is_array($application)) {
                $application = (object)$application;
            }

            // Get application items/licenses
            $itemsModel = model('LicenseApplicationItemModel');
            $items = $itemsModel->where('application_id', $id)->findAll();
            $application->items = $items;

            // Get documents
            $attachmentModel = model('LicenseApplicationAttachmentModel');
            $documents = $attachmentModel->select('id, application_id, document_type, file_path, original_name, status, rejection_reason, created_at')
                                       ->where('application_id', $id)
                                       ->findAll();
            $application->documents = $documents;

            // Get qualifications, tools, experience from license_completions table (if exists) or license_applications JSON columns
            $completions = $this->licenseModel->db->table('license_completions')->where('application_id', $id)->get()->getRow();
            
            $application->tools_list = [];
            $application->qualifications_list = [];
            $application->experience_list = [];

            if ($completions) {
                $decodeHelper = function($data) {
                    if (empty($data)) return [];
                    if (is_array($data)) return $data;
                    if (is_object($data)) return (array)$data;
                    $decoded = json_decode($data, true);
                    if (is_string($decoded)) $decoded = json_decode($decoded, true);
                    return is_array($decoded) ? $decoded : [];
                };

                $application->tools_list = $decodeHelper($completions->tools ?? null);
                $application->qualifications_list = $decodeHelper($completions->qualifications ?? null);
                $application->experience_list = $decodeHelper($completions->experiences ?? null);
            } else {
                 // Fallback to JSON columns on main table if they exist
                 $decodeHelper = function($data) {
                    if (empty($data)) return [];
                    if (is_array($data)) return $data;
                    $decoded = json_decode($data, true);
                    return is_array($decoded) ? $decoded : [];
                 };
                 if (isset($application->tools)) $application->tools_list = $decodeHelper($application->tools);
                 if (isset($application->qualifications)) $application->qualifications_list = $decodeHelper($application->qualifications);
                 if (isset($application->experiences)) $application->experience_list = $decodeHelper($application->experiences);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $application
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching application details: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to fetch application details',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get available license types from approved applications
     * Returns distinct license types for the authenticated user's approved applications
     * 
     * @return ResponseInterface
     */
    public function getAvailableLicenseTypes()
    {
        try {
            // Get authenticated user
            $user = $this->getUserFromToken();
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized'
                ]);
            }

            // Get distinct license types from approved applications
            $builder = $this->licenseModel->builder();
            $builder->select('DISTINCT i.license_type');
            $builder->join('license_application_items i', 'i.application_id = license_applications.id', 'inner');
            $builder->where('license_applications.user_id', $user->id);
            $builder->whereIn('license_applications.status', ['Approved', 'Completed']);
            $builder->where('i.license_type IS NOT NULL');
            $builder->where('i.license_type !=', '');
            $builder->orderBy('i.license_type', 'ASC');

            $results = $builder->get()->getResult();
            
            // Extract just the license type values
            $licenseTypes = array_map(function($row) {
                return $row->license_type;
            }, $results);

            return $this->response->setJSON([
                'success' => true,
                'data' => $licenseTypes
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error fetching available license types: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to fetch license types',
                'error' => $e->getMessage()
            ]);
        }
    }
}
