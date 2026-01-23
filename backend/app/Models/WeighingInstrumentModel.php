<?php

namespace App\Models;

use CodeIgniter\Model;

class WeighingInstrumentModel extends Model
{
    protected $table            = 'weighing_instruments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pattern_application_id',
        'instrument_type_id',
        'brand_name',
        'make',
        'quantity',
        'serial_numbers',
        'accuracy_class',
        'maximum_capacity',
        'manual_calibration_doc',
        'specification_doc',
        'other_doc',
        'scale_type',
        'instrument_use',
        'value_e',
        'value_d',
        'application_fee',
        'pattern_fee'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
