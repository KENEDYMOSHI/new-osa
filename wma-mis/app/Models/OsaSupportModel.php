<?php

namespace App\Models;

use CodeIgniter\Model;

class OsaSupportModel extends Model
{
    protected $DBGroup          = 'osa';
    protected $table            = 'osa_support_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'address', 
        'phone_label_1', 'phone_number_1',
        'phone_label_2', 'phone_number_2',
        'phone_label_3', 'phone_number_3',
        'email_general', 'email_tech', 'website'
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
}
