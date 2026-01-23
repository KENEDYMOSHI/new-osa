<?php

namespace App\Models;

use CodeIgniter\Model;

class CapacityMeasureInstrumentModel extends Model
{
    protected $table            = 'capacity_measure_instruments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pattern_application_id', 'instrument_type_id', 
        'brand_name', 'manufacturer', 'meter_model', 'quantity', 'serial_numbers',
        'material_construction', 'year_manufacture', 'measurement_unit', 
        'nominal_capacity', 'max_permissible_error', 'temperature_range', 
        'intended_liquid', 'has_seal_arrangement', 'has_adjustment_mechanism', 'has_gauge_glass',
        'other_doc', 'type_approval_doc', 'application_fee', 'pattern_fee'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'pattern_application_id' => 'required|integer',
        'instrument_type_id'     => 'required|integer',
        'brand_name'             => 'required',
        'quantity'               => 'required|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
