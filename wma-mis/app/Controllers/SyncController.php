<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class SyncController extends BaseController
{
    use ResponseTrait;

    public function syncApplicantData($applicationId)
    {
        // Connect to Source DB (WMA - vessel_discharge) - Default connection
        $dbSource = \Config\Database::connect('default');

        // Connect to Target DB (OSA - osa_app) - Custom connection
        $dbTarget = \Config\Database::connect('osa');

        // 1. Fetch Data from Source Tables
        
        // Tools: Try 'application_tools' first (Newer migration), then 'tools' (Legacy)
        $tools = [];
        // Check application_tools with license_application_id
        $checkTools = $dbSource->table('application_tools')->where('license_application_id', $applicationId)->get()->getResultArray();
        if (!empty($checkTools)) {
            $tools = $checkTools;
        } else {
            // Check tools with application_id
            $tools = $dbSource->table('tools')->where('application_id', $applicationId)->get()->getResultArray();
        }

        // Map Tools to View format (name, type, calibration)
        $mappedTools = [];
        foreach ($tools as $t) {
            $mappedTools[] = [
                'name' => $t['name'] ?? $t['tool_name'] ?? '-',
                'type' => $t['capacity'] ?? $t['model'] ?? $t['type'] ?? '-',
                'calibration' => $t['serial_number'] ?? $t['calibration'] ?? '-'
            ];
        }

        // Qualifications: Try 'application_qualifications' then 'applicant_qualifications'
        $qualifications = [];
        $checkQuals = $dbSource->table('application_qualifications')->where('license_application_id', $applicationId)->get()->getResultArray();
        if (!empty($checkQuals)) {
            $qualifications = $checkQuals;
        } else {
             $qualifications = $dbSource->table('applicant_qualifications')->where('application_id', $applicationId)->get()->getResultArray();
        }

        // Map Qualifications to View format (name, institution, year)
        $mappedQuals = [];
        foreach ($qualifications as $q) {
            $mappedQuals[] = [
                'name' => $q['award'] ?? $q['qualification'] ?? $q['name'] ?? '-',
                'institution' => $q['institution'] ?? '-',
                'year' => $q['year'] ?? '-'
            ];
        }

        // Previous Licenses
        // Try 'license_type' (Legacy?) or 'previous_licenses'?
        $previousLicenses = $dbSource->table('license_type')
                                     ->where('application_id', $applicationId)
                                     ->get()
                                     ->getResultArray();
        
        $mappedLicenses = [];
        foreach ($previousLicenses as $l) {
            $mappedLicenses[] = [
                'number' => $l['license_number'] ?? $l['number'] ?? '-',
                'type' => $l['license_type'] ?? $l['type'] ?? '-',
                'issued' => $l['issued_date'] ?? $l['issued'] ?? '-',
                'expiry' => $l['expiry_date'] ?? $l['expiry'] ?? '-'
            ];
        }

        // Experience
        $experiences = []; 
        // $experiences = $dbSource->table('applicant_experience')->where('application_id', $applicationId)->get()->getResultArray();

        // 2. Prepare Data for license_completions
        $completionData = [
            'application_id' => $applicationId,
            'tools' => json_encode($mappedTools),
            'qualifications' => json_encode($mappedQuals),
            'previous_licenses' => json_encode($mappedLicenses),
            'experiences' => json_encode($experiences),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // 3. Upsert into license_completions in Target DB
        $builder = $dbTarget->table('license_completions');
        $exists = $builder->where('application_id', $applicationId)->get()->getRow();

        if ($exists) {
            // Update
            $builder->where('id', $exists->id)->update($completionData);
            $message = 'Completion data updated successfully';
        } else {
            // Insert
            // We need user_id from the application to link it properly
            $app = $dbTarget->table('license_applications')->where('id', $applicationId)->get()->getRow();
            
            if (!$app) {
                return $this->failNotFound('Application not found in Target DB (license_applications)');
            }

            $completionData['id'] = $this->guidv4();
            $completionData['user_id'] = $app->user_id ?? 0;
            $completionData['created_at'] = date('Y-m-d H:i:s');
            $completionData['license_type'] = 'Class A'; // Default or fetch?
            
            $builder->insert($completionData);
            $message = 'Completion data created and synced successfully';
        }
        
        // Also update submission status in source tables to '1' if not already?
        // LicenseModel::submitApplication does this.

        return $this->respond([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'tools_count' => count($tools),
                'qualifications_count' => count($qualifications),
                'previous_licenses_count' => count($previousLicenses)
            ]
        ]);
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
