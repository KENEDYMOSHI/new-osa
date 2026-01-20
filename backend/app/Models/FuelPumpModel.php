<?php

namespace App\Models;

use CodeIgniter\Model;

class FuelPumpModel extends Model
{
    protected $table            = 'fuel_pump_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'application_number',
        // Step 1: Manufacturer Details
        'manufacturer_name',
        'country_of_manufacture',
        // Step 2: Fuel Pump Identification
        'make',
        'model',
        'quantity_of_pumps',
        'manufacturing_year',
        'number_of_nozzles',
        'dispenser_type',
        // Step 3: Metrological Characteristics
        'measured_quantity',
        'fuel_type',
        'other_fuel_type',
        'min_flow_rate',
        'max_flow_rate',
        'min_measured_volume',
        'operating_temp_min',
        'operating_temp_max',
        // Step 4: Accuracy & Performance
        'declared_accuracy_class',
        'max_permissible_error',
        // Step 5: Indicating & Power System
        'volume_indicator_type',
        'price_display',
        'display_location',
        'power_supply',
        // Step 6: Software Information
        'software_version',
        'software_legally_relevant',
        'software_protection_method',
        'event_log_available',
        // Step 7: Sealing & Security
        'adjustment_points',
        'seal_type',
        'seal_locations',
        // Step 8: Installation Information
        'intended_installation',
        'intended_country_of_use',
        'installation_manual_available',
        // Step 9: Supporting Documents
        'calibration_manual',
        'user_manual',
        'pump_exterior_photo',
        'nameplate_photo',
        'display_photo',
        'sealing_points_photo',
        'type_examination_cert',
        'software_documentation',
        // Application Status
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewer_id',
        'review_notes',
        'approval_certificate_path',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'manufacturer_name' => 'required|max_length[255]',
        'country_of_manufacture' => 'required|max_length[100]',
        'make' => 'required|max_length[255]',
        'model' => 'required|max_length[255]',
        'quantity_of_pumps' => 'required|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
