<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePatternTypesData extends Migration
{
    public function up()
    {
        // Update existing pattern types
        $this->db->table('pattern_types')->where('id', 1)->update([
            'name' => 'Weighing Instrument',
            'description' => 'Pattern approval for weighing instruments including scales, balances, and weighbridges',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('pattern_types')->where('id', 2)->update([
            'name' => 'Fuel Pump',
            'description' => 'Pattern approval for fuel dispensing pumps and meters',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Check if Water Meter and Capacity Measures already exist
        $waterMeter = $this->db->table('pattern_types')->where('name', 'Water Meter')->get()->getRow();
        if (!$waterMeter) {
            $this->db->table('pattern_types')->insert([
                'name' => 'Water Meter',
                'description' => 'Pattern approval for water metering devices',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $capacityMeasures = $this->db->table('pattern_types')->where('name', 'Capacity Measures')->get()->getRow();
        if (!$capacityMeasures) {
            $this->db->table('pattern_types')->insert([
                'name' => 'Capacity Measures',
                'description' => 'Pattern approval for capacity measurement instruments',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        // Revert to original pattern types
        $this->db->table('pattern_types')->where('id', 1)->update([
            'name' => 'Pattern Type A',
            'description' => 'Standard pattern type for general instruments',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('pattern_types')->where('id', 2)->update([
            'name' => 'Pattern Type B',
            'description' => 'Advanced pattern type for specialized instruments',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Delete Water Meter and Capacity Measures if they exist
        $this->db->table('pattern_types')->where('name', 'Water Meter')->delete();
        $this->db->table('pattern_types')->where('name', 'Capacity Measures')->delete();
    }
}
