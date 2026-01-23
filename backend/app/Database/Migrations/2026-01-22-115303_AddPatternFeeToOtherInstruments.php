<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPatternFeeToOtherInstruments extends Migration
{
    public function up()
    {
        $fields = [
            'pattern_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'application_fee'
            ],
        ];
        
        // Add to Capacity Measure Instruments
        $this->forge->addColumn('capacity_measure_instruments', $fields);

        // Add to Meter Instruments
        $this->forge->addColumn('meter_instruments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('capacity_measure_instruments', 'pattern_fee');
        $this->forge->dropColumn('meter_instruments', 'pattern_fee');
    }
}
