<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Shield\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminController extends ResourceController
{
    use ResponseTrait;

    public function getApplications()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('license_applications');
        $builder->select('license_applications.*, practitioner_personal_infos.first_name, practitioner_personal_infos.last_name, practitioner_personal_infos.phone, practitioner_personal_infos.identity_number, osabill.control_number, osabill.amount as bill_amount, (SELECT COUNT(*) FROM license_application_attachments WHERE license_application_attachments.application_id = license_applications.id) as attachment_count');
        
        // Join users to bridge ID and UUID
        $builder->join('users', 'users.id = license_applications.user_id', 'left');
        // Join personal info using UUID from users table
        $builder->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = users.uuid', 'left');
        
        // Join with osabill to get bill details. Use INNER JOIN to ensure we only get applications with bills.
        $builder->join('osabill', 'osabill.bill_id = license_applications.id', 'left');
        $builder->orderBy('license_applications.created_at', 'DESC');
        
        $query = $builder->get();
        $results = $query->getResultArray();
        
        return $this->respond($results);
    }

    public function getApplicants()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('practitioner_personal_infos');
        $builder->select('practitioner_personal_infos.*, practitioner_business_infos.company_name, practitioner_business_infos.tin, practitioner_business_infos.company_email');
        $builder->join('practitioner_business_infos', 'practitioner_business_infos.user_uuid = practitioner_personal_infos.user_uuid', 'left');
        $builder->orderBy('practitioner_personal_infos.created_at', 'DESC');
        
        $query = $builder->get();
        $results = $query->getResultArray();
        
        return $this->respond($results);
    }

    public function getApplicationDetails($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Main Application Data
        $builder = $db->table('license_applications');
        $builder->select('license_applications.*, practitioner_personal_infos.first_name, practitioner_personal_infos.last_name, practitioner_personal_infos.phone, practitioner_personal_infos.identity_number, practitioner_personal_infos.nationality, practitioner_personal_infos.gender, practitioner_personal_infos.dob, practitioner_personal_infos.region, practitioner_personal_infos.district, practitioner_personal_infos.town, practitioner_personal_infos.street, practitioner_business_infos.company_name, osabill.control_number, osabill.amount as bill_amount');
        
        // Join users to bridge ID and UUID
        $builder->join('users', 'users.id = license_applications.user_id', 'left');
        // Join personal info using UUID from users table
        $builder->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = users.uuid', 'left');
        // Join business info using UUID from users table
        $builder->join('practitioner_business_infos', 'practitioner_business_infos.user_uuid = users.uuid', 'left');
        
        $builder->join('osabill', 'osabill.bill_id = license_applications.id', 'left'); // Changed to left join just in case, but inner is fine if we only want billed
        $builder->where('license_applications.id', $id);
        $app = $builder->get()->getRowArray();

        if (!$app) {
            return $this->failNotFound('Application not found');
        }

        // 2. Attachments (exclude file_content to avoid JSON encoding issues with binary data)
        $attachmentBuilder = $db->table('license_application_attachments');
        $attachmentBuilder->select('id, user_id, application_id, document_type, category, file_path, original_name, mime_type, status, rejection_reason, created_at, updated_at');
        $attachmentBuilder->where('application_id', $id);
        $attachments = $attachmentBuilder->get()->getResultArray();

        // 3. Items (License Types)
        $itemBuilder = $db->table('license_application_items');
        $itemBuilder->where('application_id', $id);
        $items = $itemBuilder->get()->getResultArray();

        $data = [
            'application' => $app,
            'attachments' => $attachments,
            'items' => $items
        ];

        return $this->respond($data);
    }

    public function viewDocument($id)
    {
        $db = \Config\Database::connect();
        $attachmentModel = new \App\Models\LicenseApplicationAttachmentModel();
        $doc = $attachmentModel->find($id);

        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        // Admin can view any document, so no user_id check needed here (assuming route is protected by admin guard/middleware)

        if (!empty($doc->file_content)) {
            return $this->response
                ->setHeader('Content-Type', $doc->mime_type)
                ->setHeader('Content-Disposition', 'inline; filename="' . $doc->original_name . '"')
                ->setBody($doc->file_content);
        }
        
        return $this->failNotFound('File content not found');
    }

    private function getUserFromToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) {
            log_message('error', 'Admin: No Authorization header found.');
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        } else {
            log_message('error', 'Admin: Invalid header format: ' . $header);
            return null;
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            $users = model(UserModel::class);
            $user = $users->findById($decoded->uid);
            
            if (!$user) {
                log_message('error', 'Admin: User not found for ID: ' . $decoded->uid);
            }
            
            return $user;
        } catch (\Exception $e) {
            log_message('error', 'Admin: Token error: ' . $e->getMessage());
            return null;
        }
    }

    public function approveApplication($id)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid or expired token. Please login again.');
        }

        $model = new \App\Models\LicenseApplicationModel();
        $app = $model->find($id);
        
        if (!$app) {
            return $this->failNotFound('Application not found');
        }

        // Get current stage from DB (default to 1 if not set)
        $currentStage = isset($app->current_stage) ? (int)$app->current_stage : 1;
        
        // Logic: Increment stage
        // 1 (Manager) -> 2 (Surveillance)
        // 2 (Surveillance) -> 3 (DTS)
        // 3 (DTS) -> 4 (CEO)
        // 4 (CEO) -> Status Approved
        
        $nextStage = $currentStage + 1;
        $data = ['current_stage' => $nextStage];
        
        // Save Approver Name
        // Fallback to email if username is not available
        $approverName = $user->username ?? $user->email ?? 'Admin'; 
        
        $data["approver_stage_{$currentStage}"] = $approverName;

        $message = 'Application moved to next stage';

        if ($currentStage >= 4) {
            $data['status'] = 'Approved';
            $data['current_stage'] = 4; // Cap at 4
            $message = 'Application fully approved and License Generated';
            // For stage 4, we also save the approver (CEO)
            $data["approver_stage_4"] = $approverName;
            
            // Update application status first
            $model->update($id, $data);

            // Create License using the LicenseModel (Single Source of Truth)
            // This will handle license number generation and insertion into 'licenses' table
            $licenseModel = new \App\Models\LicenseModel();
            $license = $licenseModel->createLicense($id);

            if (!$license) {
                // Log warning but don't fail the request significantly
                log_message('error', 'Failed to auto-create license record for application: ' . $id);
                $message .= ' (Warning: License record creation failed)';
            }
        } else {
             // For non-final stages, just update the application
             $model->update($id, $data);
        }
        
        return $this->respond(['message' => $message, 'next_stage' => $nextStage]);
    }

    /**
     * Temporary endpoint to backfill license records for existing approved applications
     * GET /api/admin/backfill-license-numbers
     */
    public function backfillLicenseNumbers()
    {
        // Skip authentication check for this maintenance task or ensure admin
        if (ENVIRONMENT !== 'development') {
             $user = $this->getUserFromToken();
             if (!$user) return $this->failUnauthorized();
        }

        $db = \Config\Database::connect();
        $builder = $db->table('license_applications');
        $licenseModel = new \App\Models\LicenseModel();
        
        // Find approved applications
        // We want to ensure all approved applications have a corresponding record in the licenses table
        $query = $builder->groupStart()
                            ->where('status', 'Approved')
                            ->orWhere('status', 'Approved_CEO')
                         ->groupEnd()
                         ->orderBy('created_at', 'ASC')
                         ->get();

        $apps = $query->getResultArray();
        $count = 0;
        $updates = [];

        foreach ($apps as $app) {
            // Check if license already exists via model check (it does duplicate check internally)
            // But we can also check here to report status
            $existing = $licenseModel->where('application_id', $app['id'])->first();
            
            if (!$existing) {
                $license = $licenseModel->createLicense($app['id']);
                if ($license) {
                     $updates[] = [
                        'id' => $app['id'],
                        'license_number' => $license['license_number'],
                        'status' => 'Created'
                    ];
                    $count++;
                    usleep(50000); // Small pause
                } else {
                     $updates[] = [
                        'id' => $app['id'],
                        'status' => 'Failed'
                    ];
                }
            } else {
                 $updates[] = [
                    'id' => $app['id'],
                    'license_number' => $existing['license_number'],
                    'status' => 'Skipped (Exists)'
                ];
            }
        }

        return $this->respond([
            'message' => "Processed " . count($apps) . " applications. Created {$count} new license records.",
            'details' => $updates
        ]);
    }

    public function returnDocument()
    {
        // Skip authentication in development mode
        if (ENVIRONMENT !== 'development') {
            $user = $this->getUserFromToken();
            if (!$user) {
                return $this->failUnauthorized('Invalid or expired token. Please login again.');
            }
        }

        $json = $this->request->getJSON();
        $documentId = $json->document_id ?? null;
        $rejectionReason = $json->rejection_reason ?? null;

        if (!$documentId || !$rejectionReason) {
            return $this->fail('Document ID and rejection reason are required');
        }

        try {
            log_message('info', 'Return Document ID: ' . $documentId . ' Reason: ' . $rejectionReason);

            $db = \Config\Database::connect();
            
            // First, fetch the document to get user_id and document_type
            $doc = $db->table('license_application_attachments')->where('id', $documentId)->get()->getRow();
            
            if (!$doc) {
                log_message('error', 'Document not found: ' . $documentId);
                return $this->failNotFound('Document not found');
            }
            
            $data = [
                'status' => 'Returned',
                'rejection_reason' => $rejectionReason,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update ALL documents of the same type for this user across all applications
            $builder = $db->table('license_application_attachments');
            $builder->where('user_id', $doc->user_id);
            $builder->where('document_type', $doc->document_type);
            
            if (!$builder->update($data)) {
                log_message('error', 'Update failed: ' . json_encode($db->error()));
                return $this->fail('Failed to update document status in database');
            }
            
            // Log how many documents were updated
            $affectedRows = $db->affectedRows();
            log_message('info', "Updated {$affectedRows} '{$doc->document_type}' document(s) for user {$doc->user_id} to 'Returned'");


            if (!isset($doc->user_id) || !$doc->user_id) {
                log_message('error', 'User ID missing for document: ' . $documentId);
                // Proceed without notification or fail? Let's log and proceed to avoid blocking
                // But typically we want the notification. 
            } else {
                $notifBuilder = $db->table('notifications');
                // Generate UUID manually
                $uuid = md5(uniqid(rand(), true));
                
                $notifData = [
                    'id' => $uuid,
                    'user_id' => $doc->user_id,
                    'title' => 'Document Returned',
                    'message' => "Your document '{$doc->document_type}' was returned. Reason: {$rejectionReason}",
                    'type' => 'document_returned',
                    'related_entity_id' => $doc->application_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if (!$notifBuilder->insert($notifData)) {
                    log_message('error', 'Notification insert failed: ' . json_encode($db->error()));
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in returnDocument: ' . $e->getMessage());
            return $this->fail('Server Error: ' . $e->getMessage());
        }

        return $this->respond(['message' => 'Document returned successfully', 'status' => 'success']);
    }

    public function acceptDocument($id)
    {
        // Skip authentication in development mode
        if (ENVIRONMENT !== 'development') {
            $user = $this->getUserFromToken();
            if (!$user) {
                return $this->failUnauthorized('Invalid or expired token. Please login again.');
            }
        }

        $db = \Config\Database::connect();
        $builder = $db->table('license_application_attachments');
        
        $doc = $builder->where('id', $id)->get()->getRow();
        
        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        // Update status to Submitted
        $data = [
            'status' => 'Submitted',
            'rejection_reason' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $builder->where('id', $id);
        if (!$builder->update($data)) {
            return $this->fail('Failed to update document status');
        }

        return $this->respond(['message' => 'Document accepted successfully', 'status' => 'success']);
    }
}
