<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtherDocToInstrumentTables extends Migration
{
    public function up()
    {
        $fields = [
            'other_doc' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Path to other document',
            ],
        ];

        // Add to Standard Instruments Table
        $this->forge->addColumn('pattern_application_instruments', $fields);

        // Add to Weighing Instruments Table
        $this->forge->addColumn('weighing_instruments', $fields);

        // Add to Capacity Measure Instruments Table
        $this->forge->addColumn('capacity_measure_instruments', $fields);

        // Add to Meter Instruments Table (if it exists/used)
        if ($this->db->tableExists('meter_instruments')) {
            $this->forge->addColumn('meter_instruments', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('pattern_application_instruments', 'other_doc');
        $this->forge->dropColumn('weighing_instruments', 'other_doc');
        $this->forge->dropColumn('capacity_measure_instruments', 'other_doc');
        
        if ($this->db->tableExists('meter_instruments')) {
            $this->forge->dropColumn('meter_instruments', 'other_doc');
        }
    }
}
