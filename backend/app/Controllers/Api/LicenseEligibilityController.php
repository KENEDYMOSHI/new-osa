<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class LicenseEligibilityController extends ResourceController
{
    use ResponseTrait;

    public function checkEligibility()
    {
        $user = auth()->user();
        $userId = $user->id;

        $db = \Config\Database::connect();

        // Get latest initial application to determine application type
        $initialApp = $db->table('initial_applications')
                         ->where('user_id', $userId)
                         ->orderBy('created_at', 'DESC')
                         ->get()->getRow();
        
        if (!$initialApp) {
            return $this->respond([
                'canApply' => false,
                'isRenewal' => false,
                'reason' => 'No initial application found. Please submit an initial application first.'
            ]);
        }

        // Determine if this is a renewal or new application
        $applicationType = $initialApp->application_type ?? 'New';
        $isRenewal = (strcasecmp($applicationType, 'Renewal') === 0);
        
        $canApply = false;
        $reason = '';

        // Check approval status from initial_applications.status field
        // Status values: 'Draft', 'Submitted', 'Approved_Regional', 'Approved_Surveillance', 'Rejected'
        $status = $initialApp->status ?? 'Draft';
        
        // Check if both Manager (Regional) and Surveillance have approved
        // For both to be approved, status should be 'Approved_Surveillance' (which means both approved)
        $bothApproved = ($status === 'Approved_Surveillance');

        // Both Manager AND Surveillance must approve before License Application module is accessible
        if (!$bothApproved) {
            $canApply = false;
            if ($status === 'Draft' || $status === 'Submitted') {
                $reason = 'Awaiting Regional Manager approval.';
            } elseif ($status === 'Approved_Regional') {
                $reason = 'Awaiting Surveillance approval.';
            } elseif ($status === 'Rejected') {
                $reason = 'Application has been rejected.';
            } else {
                $reason = 'Application is still under review.';
            }
            
            return $this->respond([
                'canApply' => $canApply,
                'isRenewal' => $isRenewal,
                'reason' => $reason
            ]);
        }

        // At this point, both Manager and Surveillance have approved
        
        if ($isRenewal) {
            // --- RENEWAL LOGIC ---
            // For renewals, show License Application module immediately after Manager + Surveillance approval
            // No interview requirement
            $canApply = true;
        } else {
            // --- NEW LICENSE LOGIC ---
            // For new applications, also require Interview PASS
            $interview = $db->table('interview_assessments')
                            ->where('application_id', $initialApp->id)
                            ->orderBy('created_at', 'DESC')
                            ->get()->getRow();
            
            if ($interview && strtoupper($interview->result) === 'PASS') {
                $canApply = true;
            } else {
                $canApply = false;
                if (!$interview || !$interview->result) {
                    $reason = 'Awaiting interview results.';
                } else {
                    $reason = 'Interview not passed. Cannot proceed to License Application.';
                }
            }
        }

        return $this->respond([
            'canApply' => $canApply,
            'isRenewal' => $isRenewal,
            'applicationType' => $applicationType,
            'reason' => $reason
        ]);
    }
}
