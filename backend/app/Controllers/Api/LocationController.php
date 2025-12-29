<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class LocationController extends ResourceController
{
    protected $format = 'json';

    /**
     * Get all unique regions from collectioncenter table
     * GET /api/locations/regions
     */
    public function getRegions()
    {
        $db = \Config\Database::connect();
        
        try {
            $builder = $db->table('district');
            $builder->select('region');
            $builder->distinct();
            $builder->where('region IS NOT NULL');
            $builder->where('region !=', '');
            $builder->orderBy('region', 'ASC');
            
            $query = $builder->get();
            $results = $query->getResult();
            
            // Extract just the region names
            $regions = array_map(function($row) {
                return $row->region;
            }, $results);
            
            // Remove duplicates and re-index
            $regions = array_values(array_unique($regions));
            
            return $this->respond($regions);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch regions: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch regions');
        }
    }

    /**
     * Get districts for a specific region
     * GET /api/locations/districts/:region
     */
    public function getDistricts($region = null)
    {
        if (!$region) {
            return $this->failValidationError('Region parameter is required');
        }

        $db = \Config\Database::connect();
        
        try {
            $builder = $db->table('district');
            $builder->select('id, name');
            $builder->where('region', urldecode($region));
            $builder->orderBy('name', 'ASC');
            
            $query = $builder->get();
            $results = $query->getResult();
            
            return $this->respond($results);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch districts: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch districts');
        }
    }

    /**
     * Get wards for a specific district
     * GET /api/locations/wards/:district
     */
    public function getWards($district = null)
    {
        if (!$district) {
            return $this->failValidationError('District parameter is required');
        }

        $db = \Config\Database::connect();
        
        try {
            $builder = $db->table('ward');
            $builder->select('id, name');
            $builder->where('district', urldecode($district));
            $builder->orderBy('name', 'ASC');
            
            $query = $builder->get();
            $results = $query->getResult();
            
            return $this->respond($results);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch wards: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch wards');
        }
    }

    /**
     * Get postal codes for a specific ward
     * GET /api/locations/postalcodes/:ward
     */
    public function getPostalCodes($ward = null)
    {
        if (!$ward) {
            return $this->failValidationError('Ward parameter is required');
        }

        $db = \Config\Database::connect();
        
        try {
            $builder = $db->table('postcode');
            $builder->select('id, postcode');
            $builder->where('ward', urldecode($ward));
            $builder->orderBy('postcode', 'ASC');
            
            $query = $builder->get();
            $results = $query->getResult();
            
            return $this->respond($results);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch postal codes: ' . $e->getMessage());
            return $this->failServerError('Failed to fetch postal codes');
        }
    }
}
