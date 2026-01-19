<?php

namespace App\Models;

use CodeIgniter\Model;

class PatternType extends Model
{
    protected $table            = 'pattern_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getPatternApplications($id)
    {
        return $this->db->table('pattern_applications')
            ->where('pattern_type_id', $id)
            ->get()
            ->getResultArray();
    }
}
