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
        'id', 'initial_application_id', 'user_id', 'license_number', 'control_number',
        'status', 'workflow_stage', 'valid_from', 'valid_to',
        'application_type', 'total_amount', 'previous_licenses', 'qualifications', 'experiences', 'tools',
        'approval_stage', 'current_stage',
        'approver_stage_1', 'status_stage_1',
        'approver_stage_2', 'status_stage_2',
        'approver_stage_3', 'status_stage_3',
        'approver_stage_4', 'status_stage_4',
        'comment_stage_1', 'comment_stage_2', 'comment_stage_3', 'comment_stage_4'
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
        'status'                 => 'permit_empty|max_length[50]',
    ];
}
