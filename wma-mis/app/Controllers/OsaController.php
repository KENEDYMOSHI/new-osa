<?php namespace App\Controllers;
use App\Models\SearchModel;

class OsaController extends BaseController
{
  private $searchModel;
  public $session;
  public $uniqueId;
  public $user;


  public $token;
  private $licenseModel;

  public function __construct()
  {
    
          helper(['form', 'array', 'regions', 'date']);
          $this->session         = session();
          $this->token         = csrf_hash();
          $this->searchModel        = new SearchModel();
          $this->licenseModel       = new \App\Models\LicenseModel();
          $this->uniqueId        = auth()->user()->unique_id;
          $this->user = auth()->user();
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
  }
  
public function index()
{
 
  $filters = [
      'name' => $this->request->getVar('name'),
      'region' => $this->request->getVar('region'),
      'license_type' => $this->request->getVar('license_type'),
      'year' => $this->request->getVar('year'),
      'dateRange' => $this->request->getVar('dateRange')
  ];

  $applications = $this->licenseModel->getFilteredApplications($filters);

  $data['page']=[
    'title' => 'Application Approval',
    'heading' => 'Application Approval',
  ];

  $data['user']= $this->user;
  $data['applications'] = $applications;
  $data['filters'] = $filters; // Pass filters back to keep form populated

  return view('Pages/Osa/ApplicationApproval',$data);
}

public function getApplicationsApi()
{
    $filters = [
        'name' => $this->request->getVar('name'),
        'region' => $this->request->getVar('region'),
        'license_type' => $this->request->getVar('license_type'),
        'year' => $this->request->getVar('year'),
        'dateRange' => $this->request->getVar('dateRange')
    ];

    $applications = $this->licenseModel->getFilteredApplications($filters);

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $applications,
        'filters' => $filters
    ]);
}

public function getApplicationDetailsApi($id)
{
    $application = $this->licenseModel->getApplicationById($id);

    if (!$application) {
        return $this->response->setStatusCode(404)->setJSON([
            'status' => 'error',
            'message' => 'Application not found'
        ]);
    }

    // Structure the data to strictly match the requested sections if needed, 
    // or return the whole object which contains them all. 
    // The $application object already has these flat fields and the attachments array.
    // For clarity, we can group them if the user explicitly wants that structure, 
    // but usually returning the full object is more flexible. 
    // Let's return the full object as it contains everything.
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $application
    ]);
}

public function viewApplication($id)
{
  // Fetch application details from API
  $application = $this->licenseModel->getApplicationById($id);
  
  if (!$application) {
    return redirect()->to('initialApplicationApproval')->with('error', 'Application not found');
  }

  $data['page']=[
    'title' => 'Application Details',
    'heading' => 'Application Details',
  ];

  $data['user']= $this->user;
  $data['application'] = $application;

  return view('Pages/Osa/ApplicationDetail',$data);
}

public function osaDashboard()
{
  $data['page']=[
    'title' => 'OSA Dashboard',
    'heading' => 'OSA Dashboard',
  ];
  $data['user']= $this->user;
  // Mock data for dashboard
  $data['dashboard_stats'] = [
      'total_applications' => 450,
      'approved_applications' => 120,
      'pending_applications' => 30,
      'rejected_applications' => 50,
      'active_licenses' => 110,
      'expired_licenses' => 10,
      'regions' => [
          ['name' => 'Dar es Salaam', 'count' => 150, 'percent' => 80, 'color' => 'primary'],
          ['name' => 'Arusha', 'count' => 80, 'percent' => 60, 'color' => 'danger'],
          ['name' => 'Mwanza', 'count' => 60, 'percent' => 50, 'color' => 'success'],
          ['name' => 'Dodoma', 'count' => 40, 'percent' => 40, 'color' => 'warning'],
      ],
      'financials' => [
          'total_amount' => 50000000,
          'application_fee' => 5000000,
          'license_fee' => 40000000,
          'pending_fee' => 2000000,
          'paid_fee' => 48000000,
      ],
      'license_stats' => [
          ['name' => 'Class A License', 'count' => 45, 'percent' => 30, 'color' => 'info'],
          ['name' => 'Class B License', 'count' => 35, 'percent' => 25, 'color' => 'primary'],
          ['name' => 'Class C License', 'count' => 25, 'percent' => 20, 'color' => 'success'],
          ['name' => 'Class D License', 'count' => 15, 'percent' => 15, 'color' => 'warning'],
          ['name' => 'Class E License', 'count' => 10, 'percent' => 10, 'color' => 'danger'],
      ]
  ];

  return view('Pages/Osa/OsaDashboard',$data);
}


public function applicationVerification()
{
 
  $data['page']=[
    'title' => 'Application Verification',
    'heading' => 'Application Verification',
  ];




  $data['user']= $this->user;

  return view('Pages/Osa/ApplicationVerification',$data);
}
public function examRemark()
{
 
  $data['page']=[
    'title' => 'Exam Remark',
    'heading' => 'Exam Remark',
  ];




  $data['user']= $this->user;

  return view('Pages/Osa/ExamRemark',$data);
}




}