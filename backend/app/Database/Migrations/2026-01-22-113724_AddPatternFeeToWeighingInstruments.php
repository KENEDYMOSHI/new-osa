<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPatternFeeToWeighingInstruments extends Migration
{
    public function up()
    {
        $fields = [
            'pattern_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'application_fee' // Optional: place it after application_fee for readability
            ],
        ];
        $this->forge->addColumn('weighing_instruments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('weighing_instruments', 'pattern_fee');
    }
}
