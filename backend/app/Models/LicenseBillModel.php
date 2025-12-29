<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseBillModel extends Model
{
    protected $table            = 'license_bills';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'application_id',
        'control_number',
        'license_fee',
        'application_fee',
        'total_amount',
        'payment_status',
        'payment_reference',
        'payment_date',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'application_id' => 'required',
        'total_amount' => 'required|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get bill by application ID
     */
    public function getBillByApplicationId($applicationId)
    {
        return $this->where('application_id', $applicationId)->first();
    }

    /**
     * Check if payment is completed
     */
    public function isPaymentCompleted($applicationId)
    {
        $bill = $this->getBillByApplicationId($applicationId);
        return $bill && $bill['payment_status'] === 'Paid';
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($billId, $status, $reference = null)
    {
        $data = [
            'payment_status' => $status,
        ];

        if ($status === 'Paid') {
            $data['payment_date'] = date('Y-m-d H:i:s');
        }

        if ($reference) {
            $data['payment_reference'] = $reference;
        }

        return $this->update($billId, $data);
    }
}
