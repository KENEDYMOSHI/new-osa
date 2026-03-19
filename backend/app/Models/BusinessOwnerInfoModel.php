<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessOwnerInfoModel extends Model
{
    protected $table            = 'business_owner_infos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_uuid',
        // Business information
        'company_name',
        'business_type',
        'tin',
        'business_license_number',
        'postal_address',
        'office_phone_number',
        'region',
        'district',
        'ward',
        'postal_code',
        // Ownership details (optional)
        'owner_first_name',
        'owner_second_name',
        'owner_last_name',
        'owner_phone_number',
        'owner_email_address',
        'owner_postal_address',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
