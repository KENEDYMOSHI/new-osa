<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LicenseTypeSeeder extends Seeder
{
    public function run()
    {
        // First, clean up potentially bad data if necessary, or just use ignore
        // $this->db->table('license_types')->truncate(); 

        $data = [
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
        ];

        // Using ignore to skip duplicates
        $this->db->table('license_types')->ignore(true)->insertBatch($data);
    }
}
