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
  private $licenseTypeModel;
  private $billModel; // Added property

  public function __construct()
  {
    
          helper(['form', 'array', 'regions', 'date']);
          $this->session         = session();
          $this->token         = csrf_hash();
          $this->searchModel        = new SearchModel();
          $this->licenseModel       = new \App\Models\LicenseModel();
          $this->licenseTypeModel   = new \App\Models\LicenseTypeModel();
          $this->billModel          = new \App\Models\BillModel(); // Injected BillModel
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
    $data['licenseTypes'] = $this->licenseTypeModel->findAll();

    return view('Pages/Osa/LicenseReport', $data);
}

public function licenseBillReport()
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

    // Extract Control Numbers
    $controlNumbers = [];
    foreach ($licenses as $l) {
        if (!empty($l->control_number)) { 
             // Ensure robust cleaning: trim and remove spaces
             $cn = str_replace(' ', '', trim((string)$l->control_number));
             $l->control_number = $cn; // Update object property
             $controlNumbers[] = $cn;
        }
    }
    
    // Fetch Bill Data local
    $bills = [];
    if (!empty($controlNumbers)) {
        $bills = $this->billModel->getBillsByControlNumbers(array_unique($controlNumbers));
    }
    
    // Index bills by control number for easy lookup
    $billsByCn = [];
    foreach ($bills as $b) {
        // Ensure cleaning on keys from DB as well
        $dbCn = str_replace(' ', '', trim((string)$b->PayCntrNum));
        $billsByCn[$dbCn] = $b;
    }

    // Merge Bill Data into Licenses
    foreach ($licenses as $l) {
        // Set payer_name to Applicant Name by default (User Request)
        $l->payer_name = trim(($l->first_name ?? '') . ' ' . ($l->last_name ?? ''));

        $cn = isset($l->control_number) ? str_replace(' ', '', trim((string)$l->control_number)) : '';
        
        if ($cn && isset($billsByCn[$cn])) {
            $bill = $billsByCn[$cn];
            $l->bill_amount = $bill->BillAmt;
            $l->paid_amount = $bill->PaidAmount;
            $l->payment_status = $bill->PaymentStatus;
            $l->outstanding_balance = $bill->BillAmt - $bill->PaidAmount;
            
            // Determine Fee Type based on Description text first, fallback to BillTyp
            $desc = strtolower($bill->BillDesc ?? '');
            if (strpos($desc, 'application') !== false) {
                $l->fee_type = 'Application Fee';
            } elseif (strpos($desc, 'license') !== false) {
                $l->fee_type = 'License Fee';
            } else {
                $l->fee_type = ($bill->BillTyp == 1) ? 'Application Fee' : 'License Fee';
            }

            $l->bill_date = isset($bill->BillGenDt) ? date('d M, Y', strtotime($bill->BillGenDt)) : 'N/A';
            // Overwrite payer_name if bill has one? No, user explicitly asked for applicant name.
            // But if bill name is different significantly? 
            // The user request "payer name ni aapplicant name" implies they want the applicant name in that column.
            // So I will keep the applicant name constructed above.
        } else {
             // Default if no local bill found (or no CN)
             $l->bill_amount = 0;
             $l->paid_amount = 0;
             $l->payment_status = 'N/A';
             $l->outstanding_balance = 0;
             $l->fee_type = 'N/A';
             $l->bill_date = 'N/A';
             // payer_name is already set above
        }
    }

    $data['page'] = [
        'title' => 'License Bill Report',
        'heading' => 'License Bill Report',
    ];

    $data['user'] = $this->user;
    $data['licenses'] = $licenses;
    $data['filters'] = $filters;
    $data['licenseTypes'] = $this->licenseTypeModel->findAll();

    return view('Pages/Osa/LicenseBillReport', $data);
}

public function exportLicenses()
{
    // Get filters from request
    $filters = [
        'name' => $this->request->getVar('name'),
        'region' => $this->request->getVar('region'),
        'license_type' => $this->request->getVar('license_type'),
        'year' => $this->request->getVar('year'),
        'dateRange' => $this->request->getVar('dateRange'),
        // 'company_name' removed as per recent changes
    ];

    // Fetch data from API
    $licenses = $this->licenseModel->getIssuedLicensesFromApi($filters);

    // Set headers for download
    $filename = 'licenses_report_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Header Row
    fputcsv($output, ['License Number', 'Applicant Name', 'License Type', 'Region', 'Issue Date', 'Expiry Date', 'Status']);
    
    // Data Rows
    if (!empty($licenses)) {
        foreach ($licenses as $l) {
            $status = (date('Y-m-d', strtotime($l->expiry_date)) < date('Y-m-d')) ? 'Expired' : 'Active';
            fputcsv($output, [
                $l->license_number,
                $l->applicant_name ?? ($l->first_name . ' ' . $l->last_name),
                $l->license_type,
                $l->region ?? 'N/A',
                date('d M Y', strtotime($l->created_at)),
                date('d M Y', strtotime($l->expiry_date)),
                $status
            ]);
        }
    }
    
    fclose($output);
    exit;
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