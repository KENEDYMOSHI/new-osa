<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseTypeModel extends Model
{
    protected $table            = 'license_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'name', 'description', 'fee', 'currency', 'created_at', 'updated_at', 'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    
    protected $beforeInsert = ['validateLicenseType'];
    protected $beforeUpdate = ['validateLicenseType'];
    
    protected function validateLicenseType(array $data)
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'fee'  => 'required|decimal',
        ];
        
        // Add unique check for name, excluding current ID on update
        if (isset($data['data']['id'])) {
            $rules['name'] .= '|is_unique[license_types.name,id,' . $data['data']['id'] . ']';
        } else {
            $rules['name'] .= '|is_unique[license_types.name]';
        }
        
        $this->validationRules = $rules;
        
        return $data;
    }
}
