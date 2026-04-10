<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentRegistrationModel extends Model
{
    protected $table            = 'equipment_registrations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_uuid',
        'registration_no',
        'service_type_key',
        'service_type_label',
        'category',
        'equipment_data',
        'status',
        'submitted_at',
        'verified_at',
        'verifier_id',
        'verifier_notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateRegistrationNo'];
    
    protected function generateRegistrationNo(array $data)
    {
        if (!isset($data['data']['registration_no'])) {
            $year = date('Y');
            // get the latest id to generate a semi-sequential number
            $last = $this->orderBy('id', 'DESC')->first();
            $nextId = $last ? ($last['id'] + 1) : 1;
            $data['data']['registration_no'] = sprintf("EQR-%s-%05d", $year, $nextId);
        }
        return $data;
    }
}
