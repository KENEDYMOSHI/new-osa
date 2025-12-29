<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LicenseSettingSeeder extends Seeder
{
    public function run()
    {
        // -------------------------
        // 1. Seed License Types
        // -------------------------
        $licenseTypes = [
            [
                'name'        => 'Class A License',
                'description' => 'License for large scale operations',
                'fee'         => 150000.00,
                'currency'    => 'TZS',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Class B License',
                'description' => 'License for medium scale operations',
                'fee'         => 100000.00,
                'currency'    => 'TZS',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Class C License',
                'description' => 'License for small scale operations',
                'fee'         => 50000.00,
                'currency'    => 'TZS',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Weighbridge Operator License',
                'description' => 'License to operate a weighbridge',
                'fee'         => 200000.00,
                'currency'    => 'TZS',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Check/Seed license_types table
        if ($this->db->tableExists('license_types')) {
            $builder = $this->db->table('license_types');
            
            foreach ($licenseTypes as $data) {
                // Check if exists to avoid duplicates
                $count = $builder->where('name', $data['name'])->countAllResults();
                
                if ($count === 0) {
                    $builder->insert($data);
                }
            }
            echo "License Types seeded.\n";
        } else {
            echo "Table 'license_types' not found. Please run migrations.\n";
        }


        // -------------------------
        // 2. Seed Application Type Fees
        // -------------------------
        $appFees = [
            [
                'application_type' => 'New License',
                'nationality'      => 'Citizen',
                'amount'           => 10000.00,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'application_type' => 'New License',
                'nationality'      => 'Non-Citizen',
                'amount'           => 50000.00,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'application_type' => 'Renew License',
                'nationality'      => 'Citizen',
                'amount'           => 5000.00,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'application_type' => 'Renew License',
                'nationality'      => 'Non-Citizen',
                'amount'           => 25000.00,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        // Check/Seed application_type_fees table
        if ($this->db->tableExists('application_type_fees')) {
            $builder = $this->db->table('application_type_fees');

            foreach ($appFees as $data) {
                // Check uniqueness based on type + nationality
                $count = $builder->where('application_type', $data['application_type'])
                                 ->where('nationality', $data['nationality'])
                                 ->countAllResults();

                if ($count === 0) {
                    $builder->insert($data);
                }
            }
            echo "Application Type Fees seeded.\n";
        } else {
            echo "Table 'application_type_fees' not found. Please run migrations.\n";
        }
    }
}
