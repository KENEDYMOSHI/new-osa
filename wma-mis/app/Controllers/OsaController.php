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
  private $osabillModel;
  private $serviceRecordsModel;
  private $practitionerPersonalInfoModel;
  private $practitionerBusinessInfoModel;

  public function __construct()
  {
    
          helper(['form', 'array', 'regions', 'date']);
          $this->session         = session();
          $this->token         = csrf_hash();
          $this->searchModel        = new SearchModel();
          $this->licenseModel       = new \App\Models\LicenseModel();
          $this->licenseTypeModel   = new \App\Models\LicenseTypeModel();
          $this->billModel          = new \App\Models\BillModel(); // Injected BillModel
          $this->osabillModel       = new \App\Models\OsabillModel();
          $this->serviceRecordsModel = new \App\Models\ServiceRecordsModel();
          $this->practitionerPersonalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
          $this->practitionerBusinessInfoModel = new \App\Models\PractitionerBusinessInfoModel();
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
  $data['apiKey'] = 'wma_internal_notif_key_9x2z';

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
    
    // Fetch license types for filter
    $data['licenseTypes'] = $this->licenseModel->getLicenseTypesFromApi();

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
        'company_name' => $this->request->getVar('company_name'),
        'control_number' => $this->request->getVar('control_number'),
        'status' => $this->request->getVar('status'),
        'payment_status' => $this->request->getVar('payment_status')
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
        'company_name' => $this->request->getVar('company_name'),
        'control_number' => $this->request->getVar('control_number'),
        'fee_type' => $this->request->getVar('fee_type'),
        'payment_status' => $this->request->getVar('payment_status')
    ];

    // Fetch All Bills directly from osabill
    $bills = $this->osabillModel->getBillsWithFilters($filters);
    
    // Map to view structure
    $mappedBills = [];
    foreach ($bills as $bill) {
        $obj = new \stdClass();
        $obj->payer_name = $bill->payer_name;
        // bill_description contains license type name
        $obj->license_type = $bill->bill_description; 
        
        // Fee type
        $obj->fee_type = $bill->fee_type;
        if (empty($obj->fee_type) || $obj->fee_type === 'N/A') {
             $obj->fee_type = ($bill->bill_type == 1) ? 'Application Fee' : 'License Fee';
        }
        
        $obj->payment_status = $bill->payment_status;
        
        // Paid Amount logic
        if (strtolower($obj->payment_status ?? '') === 'paid') {
            $obj->paid_amount = $bill->amount;
            $obj->outstanding_balance = 0;
        } else {
            $obj->paid_amount = 0;
            $obj->outstanding_balance = $bill->amount;
        }
        
        $obj->bill_date = isset($bill->created_at) ? date('d M, Y', strtotime($bill->created_at)) : 'N/A';
        $obj->control_number = $bill->control_number;
        $obj->bill_amount = $bill->amount;
        
        // Add other fields if necessary for view to avoid undefined errors
        $obj->first_name = ''; 
        $obj->last_name = '';
        
        $mappedBills[] = $obj;
    }
    
    $data['licenses'] = $mappedBills;
    $data['filters'] = $filters;
    $data['page'] = ['title' => 'License Bill Report', 'heading' => 'License Bill Report']; // Re-added title for consistency
    $data['user'] = $this->user; // Added back user data
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

    $result = $this->licenseModel->updateApplicationStatus($applicationId, 'Approved', $stage, $comment);

    if ($result === true) {
        // ---- WMA Notification: notify officers of the next stage ----
        $this->_notifyNextStageOfficers($stage, $applicationId);
        // --------------------------------------------------------------
        return redirect()->back()->with('success', 'Application approved successfully');
    } else {
        $errorMsg = is_string($result) ? $result : 'Failed to approve application';
        return redirect()->back()->with('error', $errorMsg);
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

    $result = $this->licenseModel->updateApplicationStatus($applicationId, 'Rejected', $stage, $comment);

    if ($result === true) {
        return redirect()->back()->with('success', 'Application rejected successfully');
    } else {
        $errorMsg = is_string($result) ? $result : 'Failed to reject application';
        return redirect()->back()->with('error', $errorMsg);
    }
}





    public function licenseStatistics()
    {
        // Get Filter Year (default to current year)
        $filterYear = $this->request->getVar('year') ? $this->request->getVar('year') : date('Y');
        
        // Fetch all statistics from the shared Backend API (Same source as Dashboard)
        $stats = $this->licenseModel->getDashboardStats($filterYear);
        
        // Prepare Data for View
        $data['page'] = [
            'title' => 'License Statistics',
            'heading' => 'License Statistics (' . $filterYear . ')',
        ];

        $data['user'] = $this->user;
        
        // Use data from API
        $data['dashboard_stats'] = $stats; // Pass full object just in case view needs it
        
        $data['licenseStats'] = $stats['license_stats'] ?? [];
        $data['regionStats'] = $stats['regions'] ?? [];
        $data['allRegions'] = $stats['all_regions'] ?? ($stats['regions'] ?? []);
        
        // Map Monthly Stats for Chart
        // API returns [{month: 1, count: 10}, ...]
        $monthlyStats = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'currentYear' => array_fill(0, 12, 0),
            'lastYear' => array_fill(0, 12, 0)
        ];

        if (isset($stats['monthly_data']) && is_array($stats['monthly_data'])) {
            foreach ($stats['monthly_data'] as $row) {
                if (isset($row['month'])) {
                    $monthlyStats['currentYear'][(int)$row['month'] - 1] = (int)$row['count'];
                }
            }
        }
        
        if (isset($stats['previous_year_monthly_data']) && is_array($stats['previous_year_monthly_data'])) {
            foreach ($stats['previous_year_monthly_data'] as $row) {
                if (isset($row['month'])) {
                    $monthlyStats['lastYear'][(int)$row['month'] - 1] = (int)$row['count'];
                }
            }
        }
        
        $data['monthlyStats'] = $monthlyStats;
        $data['years'] = ['current' => $filterYear, 'last' => $filterYear - 1];
        $data['selectedYear'] = $filterYear;
        
        // Financials (if the view uses them separately, though they are in dashboard_stats too)
        $data['financials'] = $stats['financials'] ?? [];

        return view('Pages/Osa/LicenseStatistics', $data);
    }

    public function serviceRecords()
    {
        // Get filters from request
        $filters = [
            'technician_name' => $this->request->getVar('technician_name'),
            'customer_name' => $this->request->getVar('customer_name'),
            'license_number' => $this->request->getVar('license_number'),
            'service_date' => $this->request->getVar('service_date'),
            'year' => $this->request->getVar('year'),
            'region' => $this->request->getVar('region'),
            'date_from' => $this->request->getVar('date_from'),
            'date_to' => $this->request->getVar('date_to'),
            'instrument' => $this->request->getVar('instrument'),
            'sticker_number' => $this->request->getVar('sticker_number'),
        ];

        // Determine User Role and Region
        $userRole = 'other';
        $userRegion = null;

        if ($this->user->inGroup('manager')) {
            $userRole = 'manager';
            $userRegion = $this->user->region;
        } elseif ($this->user->inGroup('ceo') || $this->user->inGroup('dts') || $this->user->inGroup('surveillance') || $this->user->inGroup('admin')) {
            $userRole = 'admin'; // View all
        }

        // Get service records
        $serviceRecords = $this->serviceRecordsModel->getServiceRecords($filters, $userRole, $userRegion);
        
        // Get available years for filter dropdown
        $availableYears = $this->serviceRecordsModel->getAvailableYears();

        // Get Technician Profile Logic
        $technicianProfile = null;
        $targetUserUuid = null;

        // 1. If user is Admin/Manager and filtered by Technician Name
        if ($userRole === 'admin' || $userRole === 'manager') {
            if (!empty($filters['technician_name'])) {
                // Try to find the technician from the first record (simplest way if name matches)
                // Or query users table.
                // Since getServiceRecords joins users, let's see if we have records
                if (!empty($serviceRecords)) {
                    // Check if all records belong to same user (or just take the first one if searching by specific name)
                    // The filter logic in Model uses LIKE.
                    // Ideally, we'd lookup user by name first.
                    $db = \Config\Database::connect('osa'); // Access via OSA DB first to get ID
                    
                    // Actually, let's use the WMA DB users/personal_info standard models
                    // We need to match the name filter to a UUID.
                     $userBuilder = $db->table('users'); // This is OSA db users table? No, users is usually WMA DB?
                     // Wait, ServiceRecordsModel joins 'users' in OSA DB?
                     // Let's check ServiceRecordsModel (Step 595): 
                     // $db = \Config\Database::connect('osa'); 
                     // $builder->join('users', 'users.id = technicians_registry.user_id', 'left');
                     // So 'users' table exists in 'osa' DB (mirrored?).
                     
                     // If we have records, we can grab the user_id from the first record
                     $firstRecord = $serviceRecords[0];
                     if (isset($firstRecord->user_id)) {
                         // Get UUID from user_id in OSA DB
                         $userRec = $db->table('users')->where('id', $firstRecord->user_id)->get()->getRow();
                         if ($userRec) {
                              // Now we need the UUID to fetch Profile from WMA DB models (Practitioner...)
                              // Assuming OSA users table has uuid? 
                              // Check getProfile in TechniciansCustomerRegistryController (Step 544): $userRecord->uuid
                              if (isset($userRec->uuid)) {
                                  $targetUserUuid = $userRec->uuid;
                              }
                         }
                     }
                }
            }
        } 
        else {
            // 2. If user is a Technician (not Admin/Manager), show THEIR profile
            $targetUserUuid = $this->uniqueId;
        }

        if ($targetUserUuid) {
            try {
                $personalInfo = $this->practitionerPersonalInfoModel->where('user_uuid', $targetUserUuid)->first();
                $businessInfo = $this->practitionerBusinessInfoModel->where('user_uuid', $targetUserUuid)->first();
                
                // If we found personal info, we can build the profile
                if ($personalInfo) {
                     $userObj = auth()->getProvider()->findById($personalInfo->user_id ?? 0); // fallback if needed
                     
                     $technicianProfile = [
                        'name' => $personalInfo->first_name . ' ' . $personalInfo->last_name,
                        'phone' => $personalInfo->phone,
                        'company' => ($businessInfo) ? $businessInfo->company_name : 'N/A',
                        'seal_number' => 'N/A' // Placeholder
                    ];
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }
        
        // If still null (Admin viewing all, or technician not found), leave as null.

        $data['page'] = [
            'title' => 'Technicians Registry', // Renamed as requested
            'heading' => 'Technicians Registry',
        ];

        $data['user'] = $this->user;
        $data['serviceRecords'] = $serviceRecords;
        $data['filters'] = $filters;
        $data['availableYears'] = $availableYears;
        $data['technicianProfile'] = $technicianProfile;

        return view('Pages/Osa/ServiceRecords', $data);
    }

    // ---------------------------------------------------------------
    // Private helper: notify officers of the next approval stage
    // after the current stage is approved.
    // Stage map: 1=manager→2=surveillance, 2→3=dts, 3→4=ceo, 4→done
    // ---------------------------------------------------------------
    private function _notifyNextStageOfficers(int $approvedStage, string $applicationId): void
    {
        try {
            // Map approved stage → next stage's group name(s)
            $stageToGroup = [
                1 => ['surveillance'],
                2 => ['dts'],
                3 => ['ceo'],
                4 => ['admin', 'superadmin'],
            ];

            $nextGroups = $stageToGroup[$approvedStage] ?? [];
            if (empty($nextGroups)) {
                return; // Stage 4 approved = fully approved, no next stage
            }

            $stageLabels = [
                1 => 'Regional Manager',
                2 => 'Surveillance Officer',
                3 => 'Technical Director (DTS)',
                4 => 'Commissioner/CEO',
            ];
            $nextStageLabel = $stageLabels[$approvedStage + 1] ?? 'Next Stage';

            // Find users that belong to the next group(s)
            $db = \Config\Database::connect(); // vessel_discharge (default)
            $userIds = [];

            foreach ($nextGroups as $groupName) {
                // auth_groups_users → join auth_groups to get users in this group
                $rows = $db->table('auth_groups_users agu')
                    ->select('agu.user_id')
                    ->join('auth_groups ag', 'ag.id = agu.group_id', 'inner')
                    ->where('ag.name', $groupName)
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    $userIds[] = (int) $row['user_id'];
                }
            }

            $userIds = array_unique($userIds);

            if (empty($userIds)) {
                return;
            }

            $notifModel = new \App\Models\WmaNotificationModel();
            $approverName = trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''));
            if (empty($approverName)) {
                $approverName = $this->user->username ?? 'An officer';
            }

            $notifModel->notifyMany(
                $userIds,
                'Application Ready for Your Review',
                "Application #{$applicationId} has been approved by the {$stageLabels[$approvedStage]} and is now awaiting your review as {$nextStageLabel}.",
                'application_approved',
                $applicationId
            );
        } catch (\Throwable $e) {
            // Log but don't break the main flow
            log_message('error', 'WMA Notification error in _notifyNextStageOfficers: ' . $e->getMessage());
        }
    }

}