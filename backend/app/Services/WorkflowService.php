<?php

namespace App\Services;

use App\Models\InitialApplicationModel;
use App\Models\LicenseApplicationModel;
use App\Models\ApplicationReviewModel;
use CodeIgniter\I18n\Time;

class WorkflowService
{
    protected $initialAppModel;
    protected $licenseAppModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->initialAppModel = new InitialApplicationModel();
        $this->licenseAppModel = new LicenseApplicationModel();
        $this->reviewModel     = new ApplicationReviewModel();
    }

    /**
     * Module 1: Submit Initial Application
     */
    public function submitInitialApp($userId, $data)
    {
        // 1. Validate logic (e.g. check if user has pending app)
        // ... (Skipped for brevity)

        // 2. Prepare Data
        $appId = (string) \CodeIgniter\Uuid::uuid4();
        $insertData = [
            'id' => $appId,
            'user_id' => $userId,
            'application_type' => $data['applicationType'] ?? 'New',
            'status' => 'Submitted', // Jump straight to submitted if user clicks Submit
            'workflow_stage' => 1, // Ready for Regional Manager
            'control_number' => $this->generateControlNumber(),
        ];

        // 3. Insert
        $this->initialAppModel->insert($insertData);

        // 4. Link Attachments (Draft -> Linked)
        $attachmentModel = new \App\Models\LicenseApplicationAttachmentModel();
        $attachmentModel->where('user_id', $userId)
                        ->where('application_id', null)
                        ->set(['application_id' => $appId])
                        ->update();

        return $appId;
    }

    /**
     * Module 1 Approval: Regional (Stage 1) -> Surveillance (Stage 2)
     */
    public function approveInitialApp($appId, $approverId, $stage, $action, $comments = '')
    {
        $app = $this->initialAppModel->find($appId);
        if (!$app) {
            throw new \Exception("Application not found");
        }

        $status = 'Draft';
        $nextStage = $app['workflow_stage'];

        // Audit Log
        $this->logReview($appId, 'Initial', $approverId, $stage, $action, $comments);

        if ($action === 'Rejected') {
            $status = 'Rejected';
            $nextStage = 0; // Reset or handled as strict rejection
        } else {
            // Approval Logic
            if ($stage === 'Regional') {
                $status = 'Approved_Regional';
                $nextStage = 2; // Move to Surveillance
            } elseif ($stage === 'Surveillance') {
                $status = 'Approved_Surveillance';
                $nextStage = 3; // Completed Module 1
                
                // Trigger Module 2 Unlock
                $this->enableModule2($appId, $app['user_id']);
            }
        }

        $this->initialAppModel->update($appId, [
            'status' => $status,
            'workflow_stage' => $nextStage
        ]);

        return $status;
    }

    /**
     * Unlock Module 2: Create Draft License Application
     */
    protected function enableModule2($initialAppId, $userId)
    {
        $licenseAppId = (string) \CodeIgniter\Uuid::uuid4();
        $this->licenseAppModel->insert([
            'id' => $licenseAppId,
            'initial_application_id' => $initialAppId,
            'user_id' => $userId,
            'status' => 'Draft',
            'workflow_stage' => 0,
        ]);
        return $licenseAppId;
    }

    /**
     * Module 2: Submit License Application (with details)
     */
    public function submitLicenseApp($licenseAppId, $data)
    {
        // 1. Update main record
        $this->licenseAppModel->update($licenseAppId, [
            'status' => 'Submitted',
            'workflow_stage' => 1, // Ready for DTS
        ]);

        // 2. Save Normalized Data (Qualifications & Tools) - handled by controller or separate service calls normally
        // Returning true to indicate success
        return true;
    }

    /**
     * Module 2 Approval: DTS (Stage 1) -> CEO (Stage 2)
     */
    public function approveLicenseApp($licenseAppId, $approverId, $stage, $action, $comments = '')
    {
        $app = $this->licenseAppModel->find($licenseAppId);
        if (!$app) {
            throw new \Exception("License Application not found");
        }

        $status = $app['status'];
        $nextStage = $app['workflow_stage'];

        $this->logReview($licenseAppId, 'License', $approverId, $stage, $action, $comments);

        if ($action === 'Rejected') {
            $status = 'Rejected';
            $nextStage = 0;
        } else {
            if ($stage === 'DTS') {
                $status = 'Approved_DTS';
                $nextStage = 2; // Move to CEO
            } elseif ($stage === 'CEO') {
                $status = 'Approved_CEO'; // Ready for generation
                $nextStage = 3; 

                // Generate License
                $this->generateLicense($licenseAppId);
                $status = 'License_Generated';
            }
        }

        $this->licenseAppModel->update($licenseAppId, [
            'status' => $status,
            'workflow_stage' => $nextStage
        ]);

        return $status;
    }

    protected function generateLicense($licenseAppId)
    {
        // Generate License Number
        $year = date('Y');
        $random = rand(100, 999);
        $licenseNo = "WMA/{$year}/L/{$random}";

        $this->licenseAppModel->update($licenseAppId, [
            'license_number' => $licenseNo,
            'valid_from' => date('Y-m-d'),
            'valid_to' => date('Y-m-d', strtotime('+1 year')),
            'status' => 'License_Generated'
        ]);
    }

    protected function logReview($appId, $type, $approverId, $stage, $status, $comments)
    {
        $this->reviewModel->insert([
            'id' => (string) \CodeIgniter\Uuid::uuid4(),
            'application_id' => $appId,
            'application_type' => $type,
            'approver_id' => $approverId,
            'stage' => $stage,
            'status' => $status,
            'comments' => $comments,
        ]);
    }

    private function generateControlNumber()
    {
        return '99' . rand(1000000000, 9999999999);
    }
}
