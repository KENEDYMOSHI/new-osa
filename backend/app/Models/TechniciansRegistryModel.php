<?php

namespace App\Models;

use CodeIgniter\Model;

class TechniciansRegistryModel extends Model
{
    protected $table = 'technicians_registry';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'customer_name',
        'service_date',
        'instrument_type',
        'sticker_number',
        'instrument_issue',
        'work_performed',
        'region',
        'district',
        'ward'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'customer_name' => 'required|min_length[3]|max_length[255]',
        'service_date' => 'required|valid_date',
        'instrument_type' => 'required|max_length[255]',
        'instrument_issue' => 'required',
        'work_performed' => 'required',
    ];

    protected $validationMessages = [
        'customer_name' => [
            'required' => 'Customer name is required',
            'min_length' => 'Customer name must be at least 3 characters',
        ],
        'service_date' => [
            'required' => 'Service date is required',
            'valid_date' => 'Please provide a valid date',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all registry records for a specific user
     */
    public function getRegistryByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('service_date', 'DESC')
                    ->findAll();
    }

    /**
     * Create a new registry record
     */
    public function createRegistry(array $data): bool|int
    {
        return $this->insert($data);
    }

    /**
     * Update a registry record
     */
    public function updateRegistry(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a registry record
     */
    public function deleteRegistry(int $id): bool
    {
        return $this->delete($id);
    }
}
