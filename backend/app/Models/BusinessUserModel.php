<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessUserModel extends Model
{
    protected $table            = 'business_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'username',
        'email',
        'password_hash',
        'user_type',
        'business_type',
        'phone_number',
        'region',
        'active',
        'status',
        'last_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
