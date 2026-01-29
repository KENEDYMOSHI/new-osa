<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\DashboardModel; // We will use the existing OSA DashboardModel (or logic) if available, but wait, DashboardModel was in WMA-MIS.
// I need to check if Backend has a DashboardModel. I created one in the previous session? 
// No, I think I copied the logic to the controller or created a model.
// Let's check my memory/history.
// In the previous session, I created `backend/app/Controllers/Api/DashboardController.php` and it seemingly used `App\Models\DashboardModel`... wait, does existing backend have it?
// I'll create the controller to handle the logic directly or use a model if I created one.
// Actually, looking at the deleted files list: `backend/app/Controllers/Api/DashboardController.php` was deleted.
// I will implement it to query `wma_bill` etc directly using DB builder or existing OsaBillModel.

// Re-reading Step 121 (from previous session summary... wait, I don't have full history).
// But I recall I created `DashboardController` and it had methods `vtv`, `sbl` etc.
// I will recreate it with the logic to query the DB.

class DashboardController extends BaseController
{
    use ResponseTrait;

    private $apiKey = 'osa_approval_api_key_12345';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function validateRequest()
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        if ($requestKey !== $this->apiKey) {
            return false;
        }
        return true;
    }

    // Helper to fetch data
    private function fetchData($table, $params, $activity = null)
    {
         $builder = $this->db->table($table);
         
         // Handle special params logic similar to original model
         // Original used 'wma_bill' mostly.
         
         $builder->select('
            wma_bill.BillId,
            GfsCode as Activity,
            BillItemRef,
            bill_items.Task,
            PaymentStatus,
            PyrName,
            PyrCellNum,
            ItemName,
            BillItemAmt as amount,
            PaidAmount,
            BillAmt as BilledAmount,
            PayCntrNum,
            BillGenBy,
            BillExprDt,
            CollectionCenter,
            wma_bill.CreatedAt
         ');
         
         if ($activity) {
             $builder->like('GfsCode', $activity);
         }

         if (isset($params['CollectionCenter']) && !empty($params['CollectionCenter'])) {
             $builder->where('CollectionCenter', $params['CollectionCenter']);
         }
         
         // Date filters
         foreach ($params as $key => $value) {
             if (strpos($key, 'DATE(') !== false || strpos($key, 'MONTH(') !== false || strpos($key, 'YEAR(') !== false) {
                 // Raw where for functions
                 $builder->where($key, $value);
             } elseif ($key !== 'CollectionCenter' && $key !== 'IsCancelled') { // Handle others standard
                  $builder->where($key, $value);
             }
         }
         
         if (isset($params['IsCancelled'])) {
             $builder->where('IsCancelled', $params['IsCancelled']);
         }

         $builder->join('bill_items', 'wma_bill.BillId = bill_items.BillId', 'left');
         
         return $builder->get()->getResult();
    }

    public function vtv() { return $this->genericStat('VTV'); }
    public function sbl() { return $this->genericStat('SBL'); }
    public function waterMeters() { return $this->genericStat('Water Meter'); }
    public function ppg() { return $this->genericStat('PPG'); }
    
    public function others() { 
         return $this->genericStat(null, true); 
    }
    
    private function genericStat($activity, $isOthers = false) {
        if ($this->request->getHeaderLine('X-API-KEY') !== $this->apiKey) return $this->failUnauthorized();
        $params = $this->request->getJSON(true)['params'] ?? [];
        
        $builder = $this->db->table('wma_bill');
        $builder->select('wma_bill.*, bill_items.*, bill_items.BillItemAmt as amount'); 
        $builder->join('bill_items', 'wma_bill.BillId = bill_items.BillId', 'left');
        
        if ($activity) {
            // Check if params has override
            if ($activity === 'PPG' && isset($params['PrepackageGfsCode'])) $activity = $params['PrepackageGfsCode'];
            $builder->like('GfsCode', $activity);
        }
        
        // Apply params
        foreach ($params as $k => $v) {
            if ($k !== 'PrepackageGfsCode') $builder->where($k, $v);
        }
        
        return $this->respond($builder->get()->getResult());
    }

    /**
     * Get comprehensive OSA dashboard statistics
     * GET /api/dashboard/osa-stats?year=2024
     */
    public function getOsaStats()
    {
        try {
            // Verify API key
            if ($this->request->getHeaderLine('X-API-KEY') !== $this->apiKey) {
                return $this->failUnauthorized('Invalid API key');
            }

            $year = $this->request->getVar('year') ?? date('Y');

            // Connect to OSA database
            $osa = \Config\Database::connect('osa');

            // Get application statistics
            $totalApplications = $osa->table('license_applications')->where('YEAR(created_at)', $year)->countAllResults();
            
            // Count by approval status (all 4 stages approved = Approved)
            $approvedApplications = $osa->table('license_applications')
                ->where('YEAR(created_at)', $year)
                ->where('status_stage_1', 'Approved')
                ->where('status_stage_2', 'Approved')
                ->where('status_stage_3', 'Approved')
                ->where('status_stage_4', 'Approved')
                ->countAllResults();

            // Rejected: At least one stage is rejected
            $rejectedApplications = $osa->table('license_applications')
                ->where('YEAR(created_at)', $year)
                ->groupStart()
                    ->where('status_stage_1', 'Rejected')
                    ->orWhere('status_stage_2', 'Rejected')
                    ->orWhere('status_stage_3', 'Rejected')
                    ->orWhere('status_stage_4', 'Rejected')
                ->groupEnd()
                ->countAllResults();

            // Pending: Applications that are NOT fully approved and NOT rejected
            // This includes applications with NULL stages or partially approved (not all 4 stages approved)
            $pendingApplications = $totalApplications - $approvedApplications - $rejectedApplications;

            // Get active licenses (for now, count all approved applications)
            $activeLicenses = $approvedApplications;

            // Get expired licenses
            $expiredLicenses = 0;

            // Get license type statistics
            $licenseStats = $this->getLicenseTypeStatistics($totalApplications, $year);

            // Get regional statistics
            $regionalStatsData = $this->getRegionalStatistics($year);
            $regionalStats = $regionalStatsData['top']; // Backward compatibility
            $allRegions = $regionalStatsData['all'];

            // Get financial statistics
            $financialStats = $this->getFinancialStatistics($year);

            // Get monthly data for chart (Current Year vs Previous Year)
            $monthlyData = $this->getMonthlyApplicationData($year);
            // Also get previous year for comparison
            $previousYearData = $this->getMonthlyApplicationData($year - 1);

            $response = [
                'total_applications' => $totalApplications,
                'approved_applications' => $approvedApplications,
                'pending_applications' => $pendingApplications,
                'rejected_applications' => $rejectedApplications,
                'active_licenses' => $activeLicenses,
                'expired_licenses' => $expiredLicenses,
                'license_stats' => $licenseStats,
                'regions' => $regionalStats,
                'all_regions' => $allRegions, // New field
                'financials' => $financialStats,
                'monthly_data' => $monthlyData,
                'previous_year_monthly_data' => $previousYearData, // New field
                'selected_year' => $year
            ];

            return $this->respond($response);

        } catch (\Exception $e) {
            log_message('error', 'Dashboard API Error: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch dashboard statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get license type statistics from license_application_items
     */
    private function getLicenseTypeStatistics($total, $year)
    {
        // Connect to OSA database where license data resides
        $osa = \Config\Database::connect('osa');

        // 1. Get ALL License Types
        $allTypes = $osa->table('license_types')->select('id, name')->orderBy('name', 'ASC')->get()->getResultArray();

        // 2. Get Stats for the Year
        $statsRaw = $osa->table('license_application_items lai')
            ->select('lai.license_type, COUNT(*) as count')
            ->join('license_applications la', 'lai.application_id = la.id', 'inner')
            ->where('YEAR(la.created_at)', $year)
            ->groupBy('lai.license_type')
            ->get()
            ->getResultArray();

        // Type Logic: Verify if license_type in items is ID or Name.
        // If it's ID, map by ID. If Name, map by Name.
        // Given previous joins on ID, assume ID.
        $statsMap = [];
        foreach ($statsRaw as $row) {
            $statsMap[$row['license_type']] = $row['count'];
        }

        $stats = [];
        $colors = ['info', 'primary', 'success', 'warning', 'danger'];
        $colorIndex = 0;
        
        $totalItems = $total > 0 ? $total : 1; 

        foreach ($allTypes as $type) {
            $id = $type['id'];
            // Also check if Name works (fallback)
            $count = 0;
            if (isset($statsMap[$id])) {
                $count = $statsMap[$id];
            } elseif (isset($statsMap[$type['name']])) {
                $count = $statsMap[$type['name']];
            }
            
            $percent = $totalItems > 0 ? round(($count / $totalItems) * 100) : 0;
            
            $stats[] = [
                'name' => $type['name'],
                'count' => $count,
                'percent' => $percent,
                'color' => $colors[$colorIndex % count($colors)]
            ];
            $colorIndex++;
        }
        
        // Sort by count DESC
        usort($stats, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $stats;
    }

    /**
     * Get regional statistics
     */
    private function getRegionalStatistics($year)
    {
        // 1. Get ALL Regions (from district table in DEFAULT DB)
        $allRegionsRaw = $this->db->table('district')
            ->select('region')
            ->distinct()
            ->where('region IS NOT NULL')
            ->where('region !=', '')
            ->orderBy('region', 'ASC')
            ->get()
            ->getResultArray();
        
        $allRegionsList = array_column($allRegionsRaw, 'region');

        // 2. Get Application Counts per User from OSA DB
        $osa = \Config\Database::connect('osa');
        $userAppCounts = $osa->table('license_applications')
            ->select('user_id, COUNT(*) as count')
            ->where('YEAR(created_at)', $year)
            ->groupBy('user_id')
            ->get()
            ->getResultArray();

        $statsMap = []; // Region -> Count
        
        if (!empty($userAppCounts)) {
            // Extract User IDs to fetch regions
            $userIds = array_column($userAppCounts, 'user_id');
            
            // 3. Fetch Regions for these Users from DEFAULT DB
            // Join users -> ppi to get region
            $userRegions = $this->db->table('users u')
                ->select('u.id, ppi.region')
                ->join('practitioner_personal_infos ppi', 'u.uuid = ppi.user_uuid', 'left')
                ->whereIn('u.id', $userIds)
                ->where('ppi.region IS NOT NULL')
                ->get()
                ->getResultArray();
            
            // Map UserID -> Region
            $userRegionMap = [];
            foreach ($userRegions as $ur) {
                $userRegionMap[$ur['id']] = strtoupper($ur['region']);
            }
            
            // 4. Aggregate Counts by Region
            foreach ($userAppCounts as $row) {
                $userId = $row['user_id'];
                $count = $row['count'];
                
                if (isset($userRegionMap[$userId])) {
                    $region = $userRegionMap[$userId];
                    if (!isset($statsMap[$region])) {
                        $statsMap[$region] = 0;
                    }
                    $statsMap[$region] += $count;
                }
            }
        }

        $total = 0;
        foreach($statsMap as $c) $total += $c;
        
        $stats = [];
        $colors = ['primary', 'danger', 'success', 'warning'];
        $colorIndex = 0;

        foreach ($allRegionsList as $regionName) {
            $count = isset($statsMap[strtoupper($regionName)]) ? $statsMap[strtoupper($regionName)] : 0;
            $percent = $total > 0 ? round(($count / $total) * 100) : 0;
            
            $stats[] = [
                'name' => $regionName,
                'count' => $count,
                'percent' => $percent,
                'color' => $colors[$colorIndex % count($colors)]
            ];
            $colorIndex++;
        }
        
        // Sort by Count DESC
        usort($stats, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        // Return Top 5 and All
        return [
            'top' => array_slice($stats, 0, 5),
            'all' => $stats
        ];
    }

    /**
     * Get financial statistics from osabill
     */
    private function getFinancialStatistics($year)
    {
        // Connect to OSA database
        $osa = \Config\Database::connect('osa');

        // Fetch all bills for the year
        $bills = $osa->table('osabill')
            ->select('amount, fee_type, payment_status')
            ->where('YEAR(created_at)', $year)
            ->get()
            ->getResultArray();

        $totalAmount = 0;
        $applicationFee = 0;
        $licenseFee = 0;
        $paidFee = 0;

        foreach ($bills as $bill) {
            $amount = (float) $bill['amount'];
            $type = $bill['fee_type'];
            $status = trim($bill['payment_status']); // Trim because sample had "Paid "

            $totalAmount += $amount;

            if (stripos($type, 'Application') !== false) {
                $applicationFee += $amount;
            } elseif (stripos($type, 'License') !== false) {
                $licenseFee += $amount;
            }

            if (stripos($status, 'Paid') !== false) {
                $paidFee += $amount;
            }
        }

        // Pending fee
        $pendingFee = $totalAmount - $paidFee;

        return [
            'total_amount' => $totalAmount,
            'application_fee' => $applicationFee,
            'license_fee' => $licenseFee,
            'paid_fee' => $paidFee,
            'pending_fee' => $pendingFee
        ];
    }

    /**
     * Get monthly application data for chart
     */
    private function getMonthlyApplicationData($year)
    {
        $monthlyData = [];
        $osa = \Config\Database::connect('osa');

        for ($month = 1; $month <= 12; $month++) {
            $count = $osa->table('license_applications')
                ->where('YEAR(created_at)', $year)
                ->where('MONTH(created_at)', $month)
                ->countAllResults();

            $monthlyData[] = [
                'month' => $month,
                'count' => $count
            ];
        }

        return $monthlyData;
    }
    

}
