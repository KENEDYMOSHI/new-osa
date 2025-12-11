<?php

namespace App\Models;

use CodeIgniter\Model;

class PractitionerModel extends Model
{
    protected $table            = 'practitioners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'nationality', 'identity_number', 'first_name', 'second_name', 'last_name', 'gender', 'dob',
        'region', 'district', 'town', 'street',
        'tin', 'company_name', 'company_email', 'company_phone', 'brela_number',
        'bus_region', 'bus_district', 'bus_town', 'postal_code', 'bus_street'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
