<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class StatisticsController extends BaseController
{
    use ResponseTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getLicenseStatistics()
    {
        try {
            $builder = $this->db->table('license_application_items');
            $builder->select('license_types.name as license_class, count(license_application_items.id) as applicants');
            $builder->join('license_types', 'license_types.id = license_application_items.license_type', 'left');
            $builder->groupBy('license_application_items.license_type');
            $builder->orderBy('applicants', 'DESC');
            
            $query = $builder->get();
            $results = $query->getResultArray();
            
            $total = 0;
            foreach ($results as $row) {
                $total += $row['applicants'];
            }

            $stats = [];
            foreach ($results as $row) {
                $count = $row['applicants'];
                $name = $row['license_class'] ? $row['license_class'] : 'Unknown Class';
                // Fallback attempt if name is null but type was not null (meaning join failed or type is the name)
                if ($row['license_class'] == null && $row['applicants'] > 0) {
                     // Check if we can get the type from the group by column?
                     // In strict mode we can't select it if not grouped, but we grouped by it.
                     // Let's rely on the left join.
                }

                $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                
                $stats[] = [
                    'license_class' => $name,
                    'applicants' => $count,
                    'popularity' => $percentage
                ];
            }

            return $this->respond($stats);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function getRegionStatistics()
    {
        try {
            $builder = $this->db->table('license_applications');
            $builder->select('users.region as region_name, count(license_applications.id) as count');
            $builder->join('users', 'users.id = license_applications.user_id', 'left');
            $builder->groupBy('users.region');
            $builder->orderBy('count', 'DESC');
            
            $query = $builder->get();
            $results = $query->getResultArray();

            $total = 0;
            foreach ($results as $row) {
                $total += $row['count'];
            }

            $stats = [];
            foreach ($results as $row) {
                $count = $row['count'];
                $region = $row['region_name'] ? $row['region_name'] : 'Unknown Region';
                $percentage = $total > 0 ? round(($count / $total) * 100) : 0;

                $stats[] = [
                    'region' => $region,
                    'count' => $count,
                    'performance' => $percentage
                ];
            }

            return $this->respond($stats);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
