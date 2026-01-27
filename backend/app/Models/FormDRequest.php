<?php

namespace App\Models;

use CodeIgniter\Model;

class FormDRequest extends Model
{
    protected $table            = 'form_d_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'license_number',
        'practitioner_name',
        'practitioner_phone',
        'cert_auth_number',
        'company_name',
        'region',
        'district',
        'ward',
        'street',
        'postal_code',
        'address',
        'certification_action',
        'instrument_name',
        'serial_number',
        'product',
        'sticker_number',
        'seal_number',
        'type_of_instrument',
        'quantity',
        'capacity',
        'status',
        'verification_date',
        'next_verification_date',
        'inspection_report',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'declarant_name',
        'declarant_date',
        'declarant_time',
        'declarant_designation',
        'declarant_phone'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
