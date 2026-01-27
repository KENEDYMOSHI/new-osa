<?php

namespace App\Models;

use CodeIgniter\Model;

class PractitionerBusinessInfoModel extends Model
{
    protected $table            = 'practitioner_business_infos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // Use object return type
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_uuid',
        'tin',
        'company_name',
        'company_email',
        'company_phone',
        'brela_number',
        'bus_region',
        'bus_district',
        'bus_ward',
        'postal_code',
        'bus_street',
        'seal_number'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
