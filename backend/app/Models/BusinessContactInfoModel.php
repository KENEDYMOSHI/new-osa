<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessContactInfoModel extends Model
{
    protected $table            = 'business_contact_infos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_uuid',
        'first_name',
        'second_name',
        'last_name',
        'designation',
        'phone_number',
        'alternative_phone_number',
        'email_address',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
