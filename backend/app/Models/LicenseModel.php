<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseModel extends Model
{
    protected $table = 'licenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id',
        'application_id',
        'bill_id',
        'region',
        'control_number',
        'applicant_id',
        'applicant_name',
        'address',
        'company_name',
        'application_number',
        'license_type',
        'expiry_date',
        'license_number',
        'license_token',
        'payment_date',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Generate unique license number in format: WL-YYYY-NNNN
     * Example: WL-2026-0001
     */
    public function generateLicenseNumber()
    {
        $year = date('Y');
        $prefix = 'WL-' . $year . '-';
        
        // Get the last license number for this year
        $lastLicense = $this->db->table('licenses')
            ->select('license_number')
            ->like('license_number', $prefix, 'after')
            ->orderBy('license_number', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        if ($lastLicense) {
            // Extract sequence number and increment
            $lastNumber = (int)substr($lastLicense->license_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            // First license of the year
            $newNumber = 1;
        }
        
        // Format: WL-2026-0001
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create license from approved application
     * 
     * @param string $applicationId
     * @param string|null $paymentDate Date when payment was completed (Y-m-d format)
     * @return array|false License data or false on failure
     */
    public function createLicense($applicationId, $paymentDate = null)
    {
        // Get application details
        $application = $this->db->table('license_applications la')
            ->select('la.*, u.uuid as user_uuid')
            ->join('users u', 'la.user_id = u.id', 'left')
            ->where('la.id', $applicationId)
            ->get()
            ->getRow();

        if (!$application) {
            log_message('error', 'License creation failed: Application not found - ' . $applicationId);
            return false;
        }

        // Verify application is fully approved
        // We check strict 'Approved' status or 'Approved_CEO' which signifies final stage
        if ($application->status !== 'Approved' && $application->status !== 'Approved_CEO') {
            log_message('error', 'License creation failed: Application not fully approved - ' . $applicationId . ' Status: ' . $application->status);
            return false;
        }

        // Get applicant details
        $applicant = $this->db->table('practitioner_personal_infos')
            ->where('user_uuid', $application->user_uuid)
            ->get()
            ->getRow();

        // Get business info (company name)
        $businessInfo = $this->db->table('practitioner_business_infos')
            ->where('user_uuid', $application->user_uuid)
            ->get()
            ->getRow();

        // Get license type from application items
        $licenseItem = $this->db->table('license_application_items')
            ->where('application_id', $applicationId)
            ->get()
            ->getRow();

        // Check if license already exists for this application
        $existingLicense = $this->where('application_id', $applicationId)->first();
        if ($existingLicense) {
            log_message('info', 'License already exists for application: ' . $applicationId);
            return $existingLicense;
        }

        // Generate license number
        // Use the existing license number if it was already generated in the application table
        if (!empty($application->license_number)) {
            $licenseNumber = $application->license_number;
        } else {
            $licenseNumber = $this->generateLicenseNumber();
        }

        // Set payment date (use provided date or current date)
        $paymentDate = $paymentDate ?? date('Y-m-d');

        // Calculate expiry date (1 year from payment date)
        $expiryDate = date('Y-m-d', strtotime($paymentDate . ' +1 year'));

        // Prepare license data
        helper('text');
        $licenseData = [
            'id' => $this->generateUuid(), // Use a clean UUID generator if available, or helper
            'application_id' => $applicationId,
            'bill_id' => null, // Can be updated later if needed
            'region' => $applicant->region ?? null,
            'control_number' => $application->control_number ?? null,
            'applicant_id' => $application->user_uuid,
            'applicant_name' => trim(($applicant->first_name ?? '') . ' ' . ($applicant->second_name ?? '') . ' ' . ($applicant->last_name ?? '')),
            'address' => ($applicant->region ?? '') . ', ' . ($applicant->district ?? '') . ', ' . ($applicant->ward ?? ''),
            'company_name' => $businessInfo->company_name ?? null,
            'application_number' => null, // Add if you have application number
            'license_type' => $licenseItem->license_type ?? 'Unknown',
            'expiry_date' => $expiryDate,
            'license_number' => $licenseNumber,
            'license_token' => bin2hex(random_bytes(16)), // Generate random token
            'payment_date' => $paymentDate,
        ];

        // Insert license
        if ($this->insert($licenseData)) {
            // Update license_number in license_applications table
            $this->db->table('license_applications')
                ->where('id', $applicationId)
                ->update(['license_number' => $licenseNumber]);

            log_message('info', 'License created successfully: ' . $licenseNumber . ' for application: ' . $applicationId);
            return $licenseData;
        }

        log_message('error', 'License creation failed for application: ' . $applicationId);
        return false;
    }

    /**
     * Get license by license number
     */
    public function getLicenseByNumber($licenseNumber)
    {
        return $this->where('license_number', $licenseNumber)->first();
    }

    /**
     * Get all licenses for an applicant
     */
    public function getLicensesByApplicant($applicantId)
    {
        return $this->where('applicant_id', $applicantId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all active licenses (not expired)
     */
    public function getActiveLicenses()
    {
        return $this->where('expiry_date >=', date('Y-m-d'))
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all expired licenses
     */
    public function getExpiredLicenses()
    {
        return $this->where('expiry_date <', date('Y-m-d'))
            ->orderBy('expiry_date', 'DESC')
            ->findAll();
    }

    /**
     * Check if license is expired
     */
    public function isExpired($licenseId)
    {
        $license = $this->find($licenseId);
        if (!$license) {
            return null;
        }
        
        return strtotime($license['expiry_date']) < strtotime(date('Y-m-d'));
    }

    /**
     * Generate UUID v4
     */
    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
