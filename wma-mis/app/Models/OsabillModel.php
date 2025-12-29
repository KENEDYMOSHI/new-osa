<?php

namespace App\Models;

use CodeIgniter\Model;

class OsabillModel extends Model
{
    protected $table            = 'osabill';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 
        'bill_id', 
        'control_number', 
        'amount', 
        'bill_type', 
        'payer_name', 
        'payer_phone', 
        'bill_description', 
        'bill_expiry_date', 
        'collection_center', 
        'user_id', 
        'payment_status', 
        'items'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
