<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePatternTypeDescriptions extends Migration
{
    public function up()
    {
        $data = [
            'Weighing Instrument' => 'Select this option to apply for pattern approval of an instrument designed to measure weight or mass in accordance with approved metrological standards.',
            'Fuel Pump' => 'Select this option to apply for pattern approval of an instrument intended for measuring and dispensing liquid fuel for commercial transactions.',
            'Water Meter' => 'Select this option to apply for pattern approval of an instrument used to measure the volume of water consumption for billing or regulatory purposes.',
            'Capacity Measures' => 'Select this option to apply for pattern approval of an instrument used to measure volume or capacity in a fixed and standardized manner.',
            'Other Pattern Instrument' => 'Select this option to apply for pattern approval of any other measuring instrument not covered under the categories listed above.',
        ];

        foreach ($data as $name => $description) {
            $this->db->table('pattern_types')
                ->where('name', $name)
                ->update(['description' => $description]);
        }
    }

    public function down()
    {
        // No need to revert descriptions realistically, but we could set them to NULL
        $this->db->table('pattern_types')->update(['description' => null]);
    }
}
