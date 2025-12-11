<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseApplicationModel extends Model
{
    protected $table            = 'license_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'user_id', 'application_type', 'status', 'total_amount', 
        'previous_licenses', 'qualifications', 'experiences', 'tools',
        'current_stage', 'approver_stage_1', 'approver_stage_2', 'approver_stage_3', 'approver_stage_4'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
