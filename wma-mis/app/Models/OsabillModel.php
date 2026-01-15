<?php

namespace App\Models;

use CodeIgniter\Model;

class OsabillModel extends Model
{
    protected $DBGroup          = 'osa';
    protected $table            = 'osabill';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 
        'bill_id', 
        'control_number', 
        'amount', 
        'bill_type', 
        'fee_type', // Added fee_type
        'payer_name', 
        'payer_phone', 
        'bill_description', 
        'bill_expiry_date', 
        'collection_center', 
        'user_id', 
        'payment_status', 
        'items'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get bills by control numbers
     *
     * @param array $controlNumbers
     * @return array
     */
    public function getBillsByControlNumbers(array $controlNumbers)
    {
        if (empty($controlNumbers)) {
            return [];
        }

        return $this->select('control_number, amount, payment_status, created_at, bill_type, fee_type, bill_description, payer_name')
                    ->whereIn('control_number', $controlNumbers)
                    ->findAll();
    }
    public function getBillsWithFilters(array $filters = [])
    {
        $builder = $this->select('control_number, amount, payment_status, created_at, bill_type, fee_type, bill_description, payer_name');

        if (!empty($filters['name'])) {
            $builder->like('payer_name', $filters['name']);
        }

        if (!empty($filters['license_type'])) {
            $builder->like('bill_description', $filters['license_type']);
        }

        if (!empty($filters['control_number'])) {
            $builder->like('control_number', $filters['control_number']);
        }
        
        if (!empty($filters['dateRange'])) {
            $dates = explode(' - ', $filters['dateRange']);
            if (count($dates) == 2) {
                 $builder->where('created_at >=', $dates[0] . ' 00:00:00');
                 $builder->where('created_at <=', $dates[1] . ' 23:59:59');
            }
        }
        
        if (!empty($filters['year'])) {
             $builder->like('created_at', $filters['year']);
        }

        if (!empty($filters['fee_type'])) {
            $builder->like('fee_type', $filters['fee_type']);
        }

        if (!empty($filters['payment_status'])) {
            $builder->where('payment_status', $filters['payment_status']);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}
