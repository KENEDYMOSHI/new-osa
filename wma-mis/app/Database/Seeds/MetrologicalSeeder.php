<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MetrologicalSeeder extends Seeder
{
    public function run()
    {
        // Seed products data
        $productsData = [
            [
                'name' => 'Gasoline',
                'product_type' => 'Refined Product',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Diesel',
                'product_type' => 'Refined Product',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Jet A-1',
                'product_type' => 'Refined Product',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kerosene',
                'product_type' => 'Refined Product',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Crude Oil - Brent',
                'product_type' => 'Crude Oil',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Crude Oil - WTI',
                'product_type' => 'Crude Oil',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'LPG',
                'product_type' => 'LPG',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Check if the table exists and has data
        if ($this->db->tableExists('products')) {
            $existing = $this->db->table('products')->countAllResults();
            if ($existing == 0) {
                $this->db->table('products')->insertBatch($productsData);
                echo "Products data seeded successfully!\n";
            } else {
                echo "Products table already has data. Skipping seeding.\n";
            }
        } else {
            echo "Products table does not exist. Run migrations first.\n";
        }

        // Seed terminals data
        $terminalsData = [
            [
                'name' => 'Dar es Salaam Terminal',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Tanga Terminal',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Mtwara Terminal',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Zanzibar Terminal',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kurasini Oil Jetty',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Check if the table exists and has data
        if ($this->db->tableExists('terminals')) {
            $existing = $this->db->table('terminals')->countAllResults();
            if ($existing == 0) {
                $this->db->table('terminals')->insertBatch($terminalsData);
                echo "Terminals data seeded successfully!\n";
            } else {
                echo "Terminals table already has data. Skipping seeding.\n";
            }
        } else {
            echo "Terminals table does not exist. Run migrations first.\n";
        }

        // Seed ships data
        $shipsData = [
            [
                'name' => 'TORM HELENE',
                'imo_number' => 'IMO-9308195',
                'flag' => 'Panama',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'TORM DIANA',
                'imo_number' => 'IMO-9308756',
                'flag' => 'Singapore',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'MT PANTHER',
                'imo_number' => 'IMO-9235082',
                'flag' => 'Liberia',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'ROYAL TIDE',
                'imo_number' => 'IMO-9102497',
                'flag' => 'Marshall Islands',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'CHEM RANGER',
                'imo_number' => 'IMO-9411941',
                'flag' => 'Singapore',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Check if the table exists and has data
        if ($this->db->tableExists('ships')) {
            $existing = $this->db->table('ships')->countAllResults();
            if ($existing == 0) {
                $this->db->table('ships')->insertBatch($shipsData);
                echo "Ships data seeded successfully!\n";
            } else {
                echo "Ships table already has data. Skipping seeding.\n";
            }
        } else {
            echo "Ships table does not exist. Run migrations first.\n";
        }

        // Seed ports data - using the actual column names from the database
        $portsData = [
            [
                'region' => 'Dar es Salaam',
                'port_name' => 'Dar es Salaam Port',
                'terminal' => 'Kurasini Oil Jetty',
                'phone_number' => '255-22-2110401',
                'email' => 'info@ports.go.tz',
                'postal_address' => 'P.O. Box 9184, Dar es Salaam',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'region' => 'Tanga',
                'port_name' => 'Tanga Port',
                'terminal' => 'Tanga Oil Terminal',
                'phone_number' => '255-27-2646208',
                'email' => 'tanga@ports.go.tz',
                'postal_address' => 'P.O. Box 5077, Tanga',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'region' => 'Mtwara',
                'port_name' => 'Mtwara Port',
                'terminal' => 'Mtwara Oil Terminal',
                'phone_number' => '255-23-2333578',
                'email' => 'mtwara@ports.go.tz',
                'postal_address' => 'P.O. Box 529, Mtwara',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'region' => 'Zanzibar',
                'port_name' => 'Zanzibar Port',
                'terminal' => 'Malindi Terminal',
                'phone_number' => '255-24-2230814',
                'email' => 'zanzibar@ports.go.tz',
                'postal_address' => 'P.O. Box 695, Zanzibar',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Check if the table exists and has data
        if ($this->db->tableExists('ports')) {
            $existing = $this->db->table('ports')->countAllResults();
            if ($existing == 0) {
                $this->db->table('ports')->insertBatch($portsData);
                echo "Ports data seeded successfully!\n";
            } else {
                echo "Ports table already has data. Skipping seeding.\n";
            }
        } else {
            echo "Ports table does not exist. Run migrations first.\n";
        }

        // Seed example tanks for first ship (TORM HELENE)
        if ($this->db->tableExists('ships') && $this->db->tableExists('tanks')) {
            $ship = $this->db->table('ships')->where('name', 'TORM HELENE')->get()->getRow();
            
            if ($ship) {
                $tanksData = [
                    [
                        'ship_id' => $ship->id,
                        'name' => '1P',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => '1S',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => '2P',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => '2S',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => '3P',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => '3S',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => 'SLOP P',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'ship_id' => $ship->id,
                        'name' => 'SLOP S',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                ];
                
                $existing = $this->db->table('tanks')->where('ship_id', $ship->id)->countAllResults();
                if ($existing == 0) {
                    $this->db->table('tanks')->insertBatch($tanksData);
                    echo "Tanks data for TORM HELENE seeded successfully!\n";
                } else {
                    echo "Tanks data for TORM HELENE already exists. Skipping seeding.\n";
                }
            } else {
                echo "Ship TORM HELENE not found. Cannot seed tanks.\n";
            }
        } else {
            echo "Ships or Tanks table does not exist. Run migrations first.\n";
        }

        echo "Seeding completed successfully!\n";
    }
}