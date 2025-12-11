<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseApplicationModel extends Model
{
    protected $table            = 'license_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'initial_application_id', 'user_id', 'license_number',
        'status', 'workflow_stage', 'valid_from', 'valid_to',
        'application_type', 'total_amount', 'previous_licenses', 'qualifications', 'experiences', 'tools'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id'                     => 'required|max_length[36]',
        'initial_application_id' => 'permit_empty|max_length[36]',
        'user_id'                => 'required|integer',
        'status'                 => 'in_list[Draft,Submitted,Approved_DTS,Approved_CEO,License_Generated,Rejected]',
    ];
}
