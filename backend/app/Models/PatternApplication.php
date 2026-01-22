<?php

namespace App\Models;

use CodeIgniter\Model;

class PatternApplication extends Model
{
    protected $table            = 'pattern_applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'pattern_type_id', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'user_id'         => 'required|integer',
        'pattern_type_id' => 'required|integer',
        'status'          => 'required|in_list[draft,submitted,approved,rejected]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getWithDetails($id)
    {
        return $this->db->table('pattern_applications')
            ->select('pattern_applications.*, pattern_types.name as pattern_type_name, users.username')
            ->join('pattern_types', 'pattern_types.id = pattern_applications.pattern_type_id')
            ->join('users', 'users.id = pattern_applications.user_id')
            ->where('pattern_applications.id', $id)
            ->get()
            ->getRowArray();
    }

    public function getSelectedInstruments($applicationId)
    {
        // Get standard instruments
        $standardInstruments = $this->db->table('pattern_application_instruments')
            ->select('pattern_application_instruments.*, instrument_types.name as instrument_type_name, instrument_types.code as instrument_type_code, instrument_categories.name as category_name')
            ->join('instrument_types', 'instrument_types.id = pattern_application_instruments.instrument_type_id')
            ->join('instrument_categories', 'instrument_categories.id = instrument_types.category_id')
            ->where('pattern_application_instruments.pattern_application_id', $applicationId)
            ->get()
            ->getResultArray();

        // Get weighing instruments
        $weighingInstruments = $this->db->table('weighing_instruments')
            ->select('weighing_instruments.*, instrument_types.name as instrument_type_name, instrument_types.code as instrument_type_code, instrument_categories.name as category_name')
            ->join('instrument_types', 'instrument_types.id = weighing_instruments.instrument_type_id')
            ->join('instrument_categories', 'instrument_categories.id = instrument_types.category_id')
            ->where('weighing_instruments.pattern_application_id', $applicationId)
            ->get()
            ->getResultArray();

        // Process serial numbers for weighing instruments
        foreach ($weighingInstruments as &$instrument) {
            if (isset($instrument['serial_numbers'])) {
                $instrument['serial_numbers'] = explode(',', $instrument['serial_numbers']);
            }
        }

        // Get capacity measure instruments
        $capacityMeasureInstruments = $this->db->table('capacity_measure_instruments')
            ->select('capacity_measure_instruments.*, instrument_types.name as instrument_type_name, instrument_types.code as instrument_type_code, instrument_categories.name as category_name')
            ->join('instrument_types', 'instrument_types.id = capacity_measure_instruments.instrument_type_id')
            ->join('instrument_categories', 'instrument_categories.id = instrument_types.category_id')
            ->where('capacity_measure_instruments.pattern_application_id', $applicationId)
            ->get()
            ->getResultArray();

        // Process serial numbers for capacity measure instruments
        foreach ($capacityMeasureInstruments as &$instrument) {
            if (isset($instrument['serial_numbers'])) {
                 $instrument['serial_numbers'] = explode(',', $instrument['serial_numbers']);
            }
        }

        return array_merge($standardInstruments, $weighingInstruments, $capacityMeasureInstruments);
    }

    public function addInstrument($applicationId, $instrumentTypeId)
    {
        return $this->db->table('pattern_application_instruments')->insert([
            'pattern_application_id' => $applicationId,
            'instrument_type_id'     => $instrumentTypeId,
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);
    }

    public function removeInstrument($applicationId, $instrumentTypeId)
    {
        return $this->db->table('pattern_application_instruments')
            ->where('pattern_application_id', $applicationId)
            ->where('instrument_type_id', $instrumentTypeId)
            ->delete();
    }
}
