<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUseToWeighingInstruments extends Migration
{
    public function up()
    {
        $fields = [
            'instrument_use' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('weighing_instruments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('weighing_instruments', 'instrument_use');
    }
}
