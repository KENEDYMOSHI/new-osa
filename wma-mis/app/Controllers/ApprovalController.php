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

    public function settings()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/approval/login');
        }

        $data['title'] = 'License Settings';
        $data['apiKey'] = $this->apiKey;
        return view('Osa/settings', $data);
    }

    public function getApplications()
    {
        // Use Model/API instead of direct DB
        $model = new \App\Models\LicenseModel();
        
        $filters = [
            'name' => $this->request->getGet('name'),
            'region' => $this->request->getGet('region'),
            'license_type' => $this->request->getGet('license_type'),
            'year' => $this->request->getGet('year'),
            'dateRange' => $this->request->getGet('dateRange')
        ];
        
        $items = $model->getFilteredApplications($filters);
        
        return $this->respond($items);
    }
    
    public function getApplicationDetails($id)
    {
        // Use Model/API instead of direct DB
        $model = new \App\Models\LicenseModel();
        $application = $model->getApplicationById($id);
        
        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        return $this->respond($application);
    }
    
    public function updateApplicationStatus()
    {
        // Proxy to Backend API
        // 1. Validate API Key from header (for WMA-MIS security, though mostly called by frontend with key)
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        if ($requestKey !== $this->apiKey) {
             // In WMA-MIS, maybe we don't enforce this strictly if session is valid? 
             // But let's check key to be safe.
             return $this->failUnauthorized('Invalid API Key');
        }
        
        // 2. Get the JSON payload
        $json = $this->request->getJSON(true); 
        
        // 3. Prepare the backend API URL
        $apiUrl = 'http://localhost:8080/api/approval/update-status';
        
        // 4. Send the request to the backend
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // 5. Handle response
        if ($httpCode !== 200) {
             return $this->response->setStatusCode($httpCode)->setJSON(json_decode($response, true) ?: ['error' => 'Backend Error']);
        }
        
        return $this->response->setJSON(json_decode($response, true));
    }
    
    public function getLicenseTypes()
    {
        // Use Model/API
        $model = new \App\Models\LicenseModel();
        $licenseTypes = $model->getLicenseTypesFromApi(); // New method added to model
        
        return $this->respond($licenseTypes);
    }
}
