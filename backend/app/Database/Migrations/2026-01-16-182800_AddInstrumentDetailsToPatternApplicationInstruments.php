<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstrumentDetailsToPatternApplicationInstruments extends Migration
{
    public function up()
    {
        $fields = [
            'brand_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'make' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'maximum_capacity' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'manual_calibration_doc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'specification_doc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('pattern_application_instruments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pattern_application_instruments', [
            'brand_name',
            'make',
            'serial_number',
            'maximum_capacity',
            'manual_calibration_doc',
            'specification_doc'
        ]);
    }
}
