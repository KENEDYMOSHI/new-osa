<?php

namespace App\Models;

use CodeIgniter\Model;

class InitialApplicationModel extends Model
{
    protected $table            = 'initial_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'user_id', 'control_number', 'application_type', 
        'status', 'workflow_stage'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id'               => 'required|max_length[36]',
        'user_id'          => 'required|integer',
        'application_type' => 'required|in_list[New,Renewal]',
        'status'           => 'in_list[Draft,Submitted,Approved_Regional,Approved_Surveillance,Rejected]',
    ];
}
