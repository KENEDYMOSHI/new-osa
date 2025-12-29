<?php

namespace App\Controllers;

use App\Models\LicenseModel;

class PrintApplicationController extends BaseController
{
    public function printApplication($applicationId)
    {
        $licenseModel = new LicenseModel();
        
        // Fetch complete application data
        $application = $licenseModel->getApplicationById($applicationId);
        
        if (!$application) {
            return redirect()->to('/initialApplicationApproval')->with('error', 'Application not found');
        }
        
        // Pass data to print view
        $data = [
            'application' => $application,
            'title' => 'Print Application - ' . ($application->control_number ?? 'N/A')
        ];
        
        return view('Pages/Osa/printApplication', $data);
    }
}
