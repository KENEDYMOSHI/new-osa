<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationTypeFeeModel extends Model
{
    protected $table            = 'application_type_fees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'application_type', 'nationality', 'amount', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    
    protected $beforeInsert = ['validateApplicationTypeFee'];
    protected $beforeUpdate = ['validateApplicationTypeFee'];
    
    protected function validateApplicationTypeFee(array $data)
    {
        $rules = [
            'application_type' => 'required|min_length[3]|max_length[100]',
            'nationality'      => 'required|in_list[Citizen,Non-Citizen]',
            'amount'           => 'required|decimal',
        ];
        
        // Add unique check for combination of application_type and nationality
        if (isset($data['data']['id'])) {
            $rules['application_type'] .= '|is_unique[application_type_fees.application_type,id,' . $data['data']['id'] . ']';
        } else {
            $rules['application_type'] .= '|is_unique[application_type_fees.application_type]';
        }
        
        $this->validationRules = $rules;
        
        return $data;
    }
}
