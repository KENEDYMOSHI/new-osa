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
  $data['apiKey'] = 'osa_approval_api_key_12345';

  return view('Pages/Osa/ApplicationDetail',$data);
}

public function viewCompletedApplication($id)
{
  // Fetch application details from API
  $application = $this->licenseModel->getApplicationById($id);
  
  if (!$application) {
    return redirect()->to('completedApplications')->with('error', 'Application not found');
  }

  $data['page']=[
    'title' => 'Application Profile',
    'heading' => 'Application Profile',
  ];

  $data['user']= $this->user;
  $data['application'] = $application;

  return view('Pages/Osa/applicationCV',$data);
}

public function osaDashboard()
{
  $data['page']=[
    'title' => 'OSA Dashboard',
    'heading' => 'OSA Dashboard',
  ];
  $data['user']= $this->user;
  
  // Fetch real data from backend API
  $data['dashboard_stats'] = $this->licenseModel->getDashboardStats();

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

public function completedApplications()
{
    $filters = [
        'name' => $this->request->getVar('name'),
        'region' => $this->request->getVar('region'),
        'license_type' => $this->request->getVar('license_type'),
        'year' => $this->request->getVar('year'),
        'dateRange' => $this->request->getVar('dateRange'),
        'status' => 'Approved' // Enforce completed status
    ];

    $applications = $this->licenseModel->getFilteredApplications($filters);

    $data['page'] = [
        'title' => 'Completed Applications',
        'heading' => 'Completed Applications'
    ];
    $data['user'] = $this->user;
    $data['applications'] = $applications;
    $data['filters'] = $filters;
    
    return view('Pages/Osa/completedApplications', $data);
}
public function examRemark()
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
        'title' => 'Exam Remark',
        'heading' => 'Exam Remark',
    ];

    $data['user']= $this->user;
    $data['applications'] = $applications;
    $data['filters'] = $filters;


    return view('Pages/Osa/ExamRemark',$data);
}

public function licenseReport()
{
    $db = \Config\Database::connect();
    
    // Get filters from request
    $filters = [
        'name' => $this->request->getVar('name'),
        'region' => $this->request->getVar('region'),
        'license_type' => $this->request->getVar('license_type'),
        'year' => $this->request->getVar('year'),
        'dateRange' => $this->request->getVar('dateRange'),
        'company_name' => $this->request->getVar('company_name')
    ];

    // Build query for licenses
    // Fetch from API
    $licenses = $this->licenseModel->getIssuedLicensesFromApi($filters);

    $data['page'] = [
        'title' => 'License Report',
        'heading' => 'Issued Licenses Report',
    ];

    $data['user'] = $this->user;
    $data['licenses'] = $licenses;
    $data['filters'] = $filters;

    return view('Pages/Osa/LicenseReport', $data);
}

public function saveExamRemark()
{
    $application_id = $this->request->getVar('application_id');
    $theory_score = $this->request->getVar('theory_score');
    $practical_score = $this->request->getVar('practical_score');
    
    $updated = $this->licenseModel->updateExamScores($application_id, [
        'theory_score' => $theory_score,
        'practical_score' => $practical_score
    ]);

    if ($updated) {
        return redirect()->to('examRemark')->with('success', 'Exam scores updated successfully');
    } else {
        return redirect()->to('examRemark')->with('error', 'Failed to update exam scores');
    }
}

public function approveApplication()
{
    $applicationId = $this->request->getVar('application_id');
    $comment = $this->request->getVar('comment') ?? '';
    
    // Determine stage based on user group
    $stage = 0;
    if ($this->user->inGroup('manager')) {
        $stage = 1;
    } elseif ($this->user->inGroup('surveillance')) {
        $stage = 2;
    } elseif ($this->user->inGroup('dts')) { // Technical Director
        $stage = 3;
    } elseif ($this->user->inGroup('ceo')) { // CEO
        $stage = 4;
    } elseif ($this->user->inGroup('admin', 'superadmin')) {
        $app = $this->licenseModel->getApplicationById($applicationId);
        if ($app) {
            if (($app->region_manager_status ?? 'Pending') === 'Pending') {
                $stage = 1;
            } elseif (($app->surveillance_status ?? 'Pending') === 'Pending') {
                $stage = 2;
            } elseif (($app->dts_status ?? 'Pending') === 'Pending') {
                $stage = 3;
            } elseif (($app->ceo_status ?? 'Pending') === 'Pending') {
                $stage = 4;
            }
        }
    } else {
        return redirect()->back()->with('error', 'Unauthorized access');
    }

    $updated = $this->licenseModel->updateApplicationStatus($applicationId, 'Approved', $stage, $comment);

    if ($updated) {
        return redirect()->back()->with('success', 'Application approved successfully');
    } else {
        return redirect()->back()->with('error', 'Failed to approve application');
    }
}

public function rejectApplication()
{
    $applicationId = $this->request->getVar('application_id');
    $comment = $this->request->getVar('comment') ?? '';

    // Determine stage based on user group
    $stage = 0;
    if ($this->user->inGroup('manager')) {
        $stage = 1;
    } elseif ($this->user->inGroup('surveillance')) {
        $stage = 2;
    } elseif ($this->user->inGroup('dts')) { // Technical Director
        $stage = 3;
    } elseif ($this->user->inGroup('ceo')) { // CEO
        $stage = 4;
    } elseif ($this->user->inGroup('admin', 'superadmin')) {
        $app = $this->licenseModel->getApplicationById($applicationId);
        if ($app) {
            if (($app->region_manager_status ?? 'Pending') === 'Pending') {
                $stage = 1;
            } elseif (($app->surveillance_status ?? 'Pending') === 'Pending') {
                $stage = 2;
            } elseif (($app->dts_status ?? 'Pending') === 'Pending') {
                $stage = 3;
            } elseif (($app->ceo_status ?? 'Pending') === 'Pending') {
                $stage = 4;
            }
        }
    } else {
        return redirect()->back()->with('error', 'Unauthorized access');
    }

    $updated = $this->licenseModel->updateApplicationStatus($applicationId, 'Rejected', $stage, $comment);

    if ($updated) {
        return redirect()->back()->with('success', 'Application rejected successfully');
    } else {
        return redirect()->back()->with('error', 'Failed to reject application');
    }
}




}