<?php

namespace App\Models;

use CodeIgniter\Model;

class PractitionerPersonalInfoModel extends Model
{
    protected $table            = 'practitioner_personal_infos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // Use object return type
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_uuid',
        'nationality',
        'identity_number',
        'first_name',
        'second_name',
        'last_name',
        'gender',
        'dob',
        'region',
        'district',
        'town',
        'street',
        'phone'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
