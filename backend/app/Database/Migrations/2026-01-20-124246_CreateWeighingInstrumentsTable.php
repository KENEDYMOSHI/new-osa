<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWeighingInstrumentsTable extends Migration
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
            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'instrument_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'brand_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'make' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'serial_numbers' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Comma-separated serial numbers',
            ],
            'accuracy_class' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'maximum_capacity' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'manual_calibration_doc' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Path to manual calibration document',
            ],
            'specification_doc' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Path to specification document',
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
        $this->forge->addForeignKey('application_id', 'pattern_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instrument_type_id', 'instrument_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('weighing_instruments');
    }

    public function down()
    {
        $this->forge->dropTable('weighing_instruments');
    }
}
