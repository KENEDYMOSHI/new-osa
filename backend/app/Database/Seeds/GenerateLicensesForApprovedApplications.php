<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\LicenseModel;

class GenerateLicensesForApprovedApplications extends Seeder
{
    public function run()
    {
        $licenseModel = new LicenseModel();
        $db = \Config\Database::connect();

        // Get all fully approved applications
        $approvedApplications = $db->table('license_applications')
            ->where('status_stage_1', 'Approved')
            ->where('status_stage_2', 'Approved')
            ->where('status_stage_3', 'Approved')
            ->where('status_stage_4', 'Approved')
            ->get()
            ->getResult();

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($approvedApplications as $application) {
            // Check if license already exists
            $existingLicense = $licenseModel->where('application_id', $application->id)->first();
            
            if ($existingLicense) {
                echo "License already exists for application: {$application->id}\n";
                $skipCount++;
                continue;
            }

            // Use current date as payment date for existing applications
            // In production, you might want to use actual payment date if available
            $paymentDate = date('Y-m-d');

            // Create license
            $license = $licenseModel->createLicense($application->id, $paymentDate);

            if ($license) {
                echo "✓ Created license {$license['license_number']} for application {$application->id}\n";
                $successCount++;
            } else {
                echo "✗ Failed to create license for application {$application->id}\n";
                $errorCount++;
            }
        }

        echo "\n=== Summary ===\n";
        echo "Total approved applications: " . count($approvedApplications) . "\n";
        echo "Licenses created: {$successCount}\n";
        echo "Skipped (already exists): {$skipCount}\n";
        echo "Errors: {$errorCount}\n";
    }
}
