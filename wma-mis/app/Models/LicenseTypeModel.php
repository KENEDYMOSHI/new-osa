<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseTypeModel extends Model
{
    protected $DBGroup          = 'osa';
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
    
    protected $beforeInsert = ['validateLicenseType', 'generateID'];
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

    protected function generateID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->uuid();
        }
        return $data;
    }

    private function uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
