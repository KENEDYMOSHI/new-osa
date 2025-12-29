<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseCompletionModel extends Model
{
    protected $table            = 'license_completions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // UUID
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'application_id', 'user_id', 'license_type',
        'previous_licenses', 'qualifications', 'experiences', 'tools', 
        'declaration', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Callbacks to handle JSON
    protected $beforeInsert = ['encodeJson'];
    protected $beforeUpdate = ['encodeJson'];
    protected $afterFind    = ['decodeJson'];

    protected function encodeJson(array $data)
    {
        $jsonFields = ['previous_licenses', 'qualifications', 'experiences', 'tools'];
        foreach ($jsonFields as $field) {
            if (isset($data['data'][$field]) && is_array($data['data'][$field])) {
                $data['data'][$field] = json_encode($data['data'][$field]);
            }
        }
        return $data;
    }

    protected function decodeJson(array $data)
    {
        // Handle no data found
        if (!isset($data['data'])) {
            return $data;
        }

        // Handle single row find (if it wasn't wrapped in data, but CI4 usually wraps)
        // But here we check if data['data'] itself is the row or array of rows?
        // Actually, for multiple rows, data['data'] is array of arrays.
        // For 'first' or 'find' with single ID, data['data'] is the row array (if found) or null (if not found).

        if ($data['data'] === null) {
            return $data;
        }

        // If it's a single row (associative array)
        if (isset($data['data']['id'])) {
             $data['data'] = $this->decodeRow($data['data']);
             return $data;
        }

        // If it's multiple rows (indexed array)
        if (is_array($data['data'])) {
            foreach ($data['data'] as $key => $row) {
                 if (is_array($row)) {
                    $data['data'][$key] = $this->decodeRow($row);
                 }
            }
        }
        
        return $data;
    }

    private function decodeRow($row)
    {
        $jsonFields = ['previous_licenses', 'qualifications', 'experiences', 'tools'];
        foreach ($jsonFields as $field) {
            if (isset($row[$field]) && is_string($row[$field])) {
                $row[$field] = json_decode($row[$field], true);
            }
        }
        return $row;
    }
}
