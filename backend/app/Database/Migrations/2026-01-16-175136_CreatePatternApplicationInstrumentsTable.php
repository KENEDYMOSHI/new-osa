<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatternApplicationInstrumentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pattern_application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'instrument_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pattern_application_id', 'pattern_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_type_id', 'instrument_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pattern_application_instruments');
    }

    public function down()
    {
        $this->forge->dropTable('pattern_application_instruments');
    }
}
