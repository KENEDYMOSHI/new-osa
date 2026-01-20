<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateWaterMeterToMeter extends Migration
{
    public function up()
    {
        // Update pattern_types table
        $this->db->table('pattern_types')
            ->where('name', 'Water Meter')
            ->update([
                'name' => 'Meter',
                'description' => 'Pattern approval for metering devices',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // Update instrument_categories table
        $this->db->table('instrument_categories')
            ->where('name', 'Water Meter')
            ->update([
                'name' => 'Meter',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // Update instrument_types table
        $this->db->table('instrument_types')
            ->where('name', 'Standard Water Meter')
            ->update([
                'name' => 'Standard Meter',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down()
    {
        // Revert pattern_types table
        $this->db->table('pattern_types')
            ->where('name', 'Meter')
            ->update([
                'name' => 'Water Meter',
                'description' => 'Pattern approval for water metering devices',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // Revert instrument_categories table
        $this->db->table('instrument_categories')
            ->where('name', 'Meter')
            ->update([
                'name' => 'Water Meter',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // Revert instrument_types table
        $this->db->table('instrument_types')
            ->where('name', 'Standard Meter')
            ->update([
                'name' => 'Standard Water Meter',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
