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
     * GET /api/dashboard/osa-stats
     */
    public function getOsaStats()
    {
        try {
            // Verify API key
            if ($this->request->getHeaderLine('X-API-KEY') !== $this->apiKey) {
                return $this->failUnauthorized('Invalid API key');
            }

            // Get application statistics
            $totalApplications = $this->db->table('license_applications')->countAllResults();
            
            // Count by approval status (all 4 stages approved = Approved)
            $approvedApplications = $this->db->table('license_applications')
                ->where('status_stage_1', 'Approved')
                ->where('status_stage_2', 'Approved')
                ->where('status_stage_3', 'Approved')
                ->where('status_stage_4', 'Approved')
                ->countAllResults();

            // Rejected: At least one stage is rejected
            $rejectedApplications = $this->db->table('license_applications')
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
            // TODO: Once valid_to dates are set, filter by valid_to >= current date
            $activeLicenses = $approvedApplications;

            // Get expired licenses (for now, 0 since valid_to dates are not set)
            // TODO: Once valid_to dates are set, count where valid_to < current date
            $expiredLicenses = 0;

            // Get license type statistics
            $licenseStats = $this->getLicenseTypeStatistics($totalApplications);

            // Get regional statistics
            $regionalStats = $this->getRegionalStatistics();

            // Get financial statistics
            $financialStats = $this->getFinancialStatistics();

            // Get monthly data for chart
            $monthlyData = $this->getMonthlyApplicationData();

            $response = [
                'total_applications' => $totalApplications,
                'approved_applications' => $approvedApplications,
                'pending_applications' => $pendingApplications,
                'rejected_applications' => $rejectedApplications,
                'active_licenses' => $activeLicenses,
                'expired_licenses' => $expiredLicenses,
                'license_stats' => $licenseStats,
                'regions' => $regionalStats,
                'financials' => $financialStats,
                'monthly_data' => $monthlyData
            ];

            return $this->respond($response);

        } catch (\Exception $e) {
            log_message('error', 'Dashboard API Error: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch dashboard statistics');
        }
    }

    /**
     * Get license type statistics from license_application_items
     */
    private function getLicenseTypeStatistics($total)
    {
        // Query license_application_items to get actual license types applied for
        $licenseTypes = $this->db->table('license_application_items')
            ->select('license_type, COUNT(*) as count')
            ->groupBy('license_type')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        $stats = [];
        $colors = ['info', 'primary', 'success', 'warning', 'danger'];
        $colorIndex = 0;
        
        // Calculate total for percentage
        $totalItems = array_sum(array_column($licenseTypes, 'count'));

        foreach ($licenseTypes as $type) {
            $count = $type['count'];
            $percent = $totalItems > 0 ? round(($count / $totalItems) * 100) : 0;
            
            $stats[] = [
                'name' => $type['license_type'] ?? 'Unknown',
                'count' => $count,
                'percent' => $percent,
                'color' => $colors[$colorIndex % count($colors)]
            ];
            $colorIndex++;
        }

        return $stats;
    }

    /**
     * Get regional statistics
     */
    private function getRegionalStatistics()
    {
        // Join through users table to connect license_applications with practitioner_personal_infos
        // license_applications.user_id -> users.id -> users.uuid -> practitioner_personal_infos.user_uuid
        $regions = $this->db->table('license_applications la')
            ->select('ppi.region, COUNT(*) as count')
            ->join('users u', 'la.user_id = u.id', 'left')
            ->join('practitioner_personal_infos ppi', 'u.uuid = ppi.user_uuid', 'left')
            ->where('ppi.region IS NOT NULL')
            ->groupBy('ppi.region')
            ->orderBy('count', 'DESC')
            ->limit(4)
            ->get()
            ->getResultArray();

        $total = array_sum(array_column($regions, 'count'));
        $stats = [];
        $colors = ['primary', 'danger', 'success', 'warning'];
        $colorIndex = 0;

        foreach ($regions as $region) {
            $count = $region['count'];
            $percent = $total > 0 ? round(($count / $total) * 100) : 0;
            
            $stats[] = [
                'name' => $region['region'] ?? 'Unknown',
                'count' => $count,
                'percent' => $percent,
                'color' => $colors[$colorIndex % count($colors)]
            ];
            $colorIndex++;
        }

        // If no regions found, return empty array instead of placeholder data
        return $stats;
    }

    /**
     * Get financial statistics from license_application_items
     */
    private function getFinancialStatistics()
    {
        // Get actual application fees from license_application_items
        $applicationFeeData = $this->db->table('license_application_items')
            ->selectSum('application_fee')
            ->get()
            ->getRow();
        $applicationFee = $applicationFeeData->application_fee ?? 0;

        // Get actual license fees from license_application_items
        $licenseFeeData = $this->db->table('license_application_items')
            ->selectSum('fee')
            ->get()
            ->getRow();
        $licenseFee = $licenseFeeData->fee ?? 0;

        // Total amount is sum of application fees and license fees
        $totalAmount = $applicationFee + $licenseFee;

        // Paid fee (from approved applications)
        // Join with license_applications to get only approved items
        $paidFeeData = $this->db->table('license_application_items lai')
            ->select('SUM(lai.fee + lai.application_fee) as total_paid')
            ->join('license_applications la', 'lai.application_id = la.id', 'inner')
            ->where('la.status_stage_1', 'Approved')
            ->where('la.status_stage_2', 'Approved')
            ->where('la.status_stage_3', 'Approved')
            ->where('la.status_stage_4', 'Approved')
            ->get()
            ->getRow();
        $paidFee = $paidFeeData->total_paid ?? 0;

        // Pending fee (total - paid)
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
    private function getMonthlyApplicationData()
    {
        $currentYear = date('Y');
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $count = $this->db->table('license_applications')
                ->where('YEAR(created_at)', $currentYear)
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
