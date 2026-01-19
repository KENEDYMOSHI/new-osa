<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentCategory extends Model
{
    protected $table            = 'instrument_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'code', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[255]',
        'code' => 'required|max_length[100]|is_unique[instrument_categories.code]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getInstrumentTypes($id)
    {
        return $this->db->table('instrument_types')
            ->where('category_id', $id)
            ->where('is_active', 1)
            ->get()
            ->getResultArray();
    }
}
