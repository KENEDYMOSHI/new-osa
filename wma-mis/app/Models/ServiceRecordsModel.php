<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceRecordsModel extends Model
{
    protected $table = 'technicians_registry';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    /**
     * Get service records with filters
     */
    public function getServiceRecords($filters = [], $userRole = 'other', $userRegion = null)
    {
        $db = \Config\Database::connect('osa');
        $builder = $db->table('technicians_registry');
        
        // Join with users table to get technician username
        $builder->select('technicians_registry.*, license_users.username, license_users.uuid,
                          technicians_registry.instrument_type as instrument, 
                          technicians_registry.instrument_issue as issue_problem,
                          license_users.username as technician_name');
        $builder->join('license_users', 'license_users.id = technicians_registry.user_id', 'left');

        // Apply filters
        if (!empty($filters['technician_name'])) {
            $builder->like('license_users.username', $filters['technician_name']);
        }
        
        if (!empty($filters['customer_name'])) {
            $builder->like('customer_name', $filters['customer_name']);
        }

        if (!empty($filters['instrument'])) {
            $builder->like('technicians_registry.instrument_type', $filters['instrument']);
        }

        if (!empty($filters['sticker_number'])) {
            $builder->like('technicians_registry.sticker_number', $filters['sticker_number']);
        }
        
        if (!empty($filters['license_number'])) {
            $builder->like('license_number', $filters['license_number']);
        }
        
        if (!empty($filters['service_date'])) {
            $builder->where('service_date', $filters['service_date']);
        }
        
        if (!empty($filters['year']) && $filters['year'] !== 'all') {
            $builder->where('YEAR(service_date)', $filters['year']);
        }
        
        if (!empty($filters['region']) && $filters['region'] !== 'all') {
            $builder->where('region', $filters['region']);
        }
        
        // Date range filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $builder->where('service_date >=', $filters['date_from']);
            $builder->where('service_date <=', $filters['date_to']);
        }
        
        // Role-based filtering
        if ($userRole === 'manager' && $userRegion) {
            $builder->where('region', $userRegion);
        }
        
        // Order by service date descending
        $builder->orderBy('service_date', 'DESC');
        
        $query = $builder->get();
        return $query->getResult();
    }
    
    /**
     * Get available years from service records
     */
    public function getAvailableYears()
    {
        $db = \Config\Database::connect('osa');
        $builder = $db->table('technicians_registry');
        $builder->select('DISTINCT YEAR(service_date) as year');
        $builder->orderBy('year', 'DESC');
        
        $query = $builder->get();
        $years = [];
        
        foreach ($query->getResult() as $row) {
            if ($row->year) {
                $years[] = $row->year;
            }
        }
        
        return $years;
    }
}
