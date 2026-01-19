<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrumentType extends Model
{
    protected $table            = 'instrument_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['category_id', 'name', 'code', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'category_id' => 'required|integer',
        'name'        => 'required|max_length[255]',
        'code'        => 'required|max_length[50]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getCategory($id)
    {
        return $this->db->table('instrument_categories')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    public function getWithCategory($id)
    {
        return $this->db->table('instrument_types')
            ->select('instrument_types.*, instrument_categories.name as category_name, instrument_categories.code as category_code')
            ->join('instrument_categories', 'instrument_categories.id = instrument_types.category_id')
            ->where('instrument_types.id', $id)
            ->get()
            ->getRowArray();
    }
}
