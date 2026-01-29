<?php

namespace App\Models;

use CodeIgniter\Model;

class FormDRequestModel extends Model
{
    protected $DBGroup = 'osa';
    protected $table = 'form_d_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'license_number',
        'practitioner_name',
        'practitioner_phone',
        'cert_auth_number',
        'company_name',
        'region',
        'district',
        'ward',
        'street',
        'postal_code',
        'address',
        'certification_action',
        'instrument_name',
        'serial_number',
        'product',
        'sticker_number',
        'seal_number',
        'type_of_instrument',
        'quantity',
        'capacity',
        'capacity_unit',
        'status',
        'inspector_id',
        'assignment_notes',
        'rejection_reason',
        'verification_date',
        'next_verification_date',
        'inspection_report',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'declarant_name',
        'declarant_date',
        'declarant_time',
        'declarant_designation',
        'declarant_phone',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;

    public function getRequests($filters = [])
    {
        $builder = $this->builder();
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        // Date Range Filter (Using created_at)
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $builder->where("DATE(created_at) >=", $filters['start_date'])
                    ->where("DATE(created_at) <=", $filters['end_date']);
        } elseif (!empty($filters['start_date'])) {
             $builder->where("DATE(created_at) >=", $filters['start_date']);
        } elseif (!empty($filters['end_date'])) {
             $builder->where("DATE(created_at) <=", $filters['end_date']);
        }
        
        // Month Filter (YYYY-MM)
        if (!empty($filters['month'])) {
            $date = $filters['month']; 
             $builder->like('created_at', $date, 'after'); 
        }
        
        if (!empty($filters['applicant_name'])) {
            $builder->groupStart()
                ->like('practitioner_name', $filters['applicant_name'])
                ->orLike('declarant_name', $filters['applicant_name'])
                ->groupEnd();
        }

        if (!empty($filters['license_number'])) {
            $builder->like('license_number', $filters['license_number']);
        }

        if (!empty($filters['seal_number'])) {
            $builder->like('seal_number', $filters['seal_number']);
        }
        
        if (!empty($filters['inspector_id'])) {
            $builder->where('inspector_id', $filters['inspector_id']);
        }

        return $builder->orderBy('id', 'DESC')->get()->getResultArray();
    }
}
