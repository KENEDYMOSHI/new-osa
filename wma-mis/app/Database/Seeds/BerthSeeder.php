<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BerthSeeder extends Seeder
{
    public function run()
    {
        // First get all port IDs from the database
        $ports = $this->db->table('ports')->select('id, name')->get()->getResult();
        $portMap = [];

        foreach ($ports as $port) {
            $portMap[$port->name] = $port->id;
        }

        // Berth data
        $data = [
            // Dar es Salaam Port berths
            [
                'name' => 'Berth 1',
                'port_id' => $portMap['Dar es Salaam Port'] ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 2',
                'port_id' => $portMap['Dar es Salaam Port'] ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 3',
                'port_id' => $portMap['Dar es Salaam Port'] ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 4',
                'port_id' => $portMap['Dar es Salaam Port'] ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Tanga Port berths
            [
                'name' => 'Berth 1',
                'port_id' => $portMap['Tanga Port'] ?? 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 2',
                'port_id' => $portMap['Tanga Port'] ?? 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Mtwara Port berths
            [
                'name' => 'Berth 1',
                'port_id' => $portMap['Mtwara Port'] ?? 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Zanzibar Port berths
            [
                'name' => 'Berth 1',
                'port_id' => $portMap['Zanzibar Port'] ?? 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 2',
                'port_id' => $portMap['Zanzibar Port'] ?? 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Mombasa Port berths
            [
                'name' => 'Berth 1',
                'port_id' => $portMap['Mombasa Port'] ?? 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 2',
                'port_id' => $portMap['Mombasa Port'] ?? 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Berth 3',
                'port_id' => $portMap['Mombasa Port'] ?? 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Other port berths with descriptive names
            [
                'name' => 'North Terminal',
                'port_id' => $portMap['Rotterdam Port'] ?? 10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'South Terminal',
                'port_id' => $portMap['Rotterdam Port'] ?? 10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Container Terminal',
                'port_id' => $portMap['Durban Port'] ?? 6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bulk Terminal',
                'port_id' => $portMap['Durban Port'] ?? 6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'East Quay',
                'port_id' => $portMap['Cape Town Port'] ?? 7,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'West Quay',
                'port_id' => $portMap['Cape Town Port'] ?? 7,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data to table
        $this->db->table('berths')->insertBatch($data);
    }
}