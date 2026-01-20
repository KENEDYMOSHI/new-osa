<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InstrumentDataSeeder extends Seeder
{
    public function run()
    {
        // Insert Instrument Categories
        $categories = [
            [
                'name' => 'Weighing Instrument',
                'code' => 'weighing',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Fuel Pump',
                'code' => 'fuel_pump',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Meter',
                'code' => 'water_meter',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Capacity Measures',
                'code' => 'capacity_measures',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('instrument_categories')->insertBatch($categories);

        // Get category IDs
        $weighingId = $this->db->table('instrument_categories')->where('code', 'weighing')->get()->getRow()->id;
        $fuelPumpId = $this->db->table('instrument_categories')->where('code', 'fuel_pump')->get()->getRow()->id;
        $waterMeterId = $this->db->table('instrument_categories')->where('code', 'water_meter')->get()->getRow()->id;
        $capacityId = $this->db->table('instrument_categories')->where('code', 'capacity_measures')->get()->getRow()->id;

        // Insert Instrument Types
        $instrumentTypes = [
            // Weighing Instruments
            [
                'category_id' => $weighingId,
                'name' => 'Counter Scale',
                'code' => 'CIS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $weighingId,
                'name' => 'Platform Scale',
                'code' => 'P/M',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $weighingId,
                'name' => 'Balance Scale',
                'code' => 'S/B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $weighingId,
                'name' => 'Spring Balance',
                'code' => 'BS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $weighingId,
                'name' => 'Weighbridge',
                'code' => 'W/B',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Placeholder for other categories - to be defined later
            [
                'category_id' => $fuelPumpId,
                'name' => 'Standard Fuel Pump',
                'code' => 'SFP',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $waterMeterId,
                'name' => 'Standard Meter',
                'code' => 'SWM',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $capacityId,
                'name' => 'Standard Capacity Measure',
                'code' => 'SCM',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('instrument_types')->insertBatch($instrumentTypes);

        // Insert Pattern Types (matching instrument categories)
        $patternTypes = [
            [
                'name' => 'Weighing Instrument',
                'description' => 'Pattern approval for weighing instruments including scales, balances, and weighbridges',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Fuel Pump',
                'description' => 'Pattern approval for fuel dispensing pumps and meters',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Meter',
                'description' => 'Pattern approval for metering devices',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Capacity Measures',
                'description' => 'Pattern approval for capacity measurement instruments',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pattern_types')->insertBatch($patternTypes);
    }
}
