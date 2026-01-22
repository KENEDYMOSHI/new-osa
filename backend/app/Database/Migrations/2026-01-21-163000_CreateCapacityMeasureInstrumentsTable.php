<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCapacityMeasureInstrumentsTable extends Migration
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
            
            // Common Fields
            'brand_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'manufacturer' => [ // Make
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'meter_model' => [ // Model Type
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'serial_numbers' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            
            // Capacity Measure Specific Fields
            'material_construction' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'year_manufacture' => [
                'type'       => 'VARCHAR',
                'constraint' => '20', // e.g. 2024
                'null'       => true,
            ],
            'measurement_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // Litre or Cubic Metre
                'null'       => true,
            ],
            'nominal_capacity' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'max_permissible_error' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'temperature_range' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'intended_liquid' => [
                'type'       => 'TEXT', // Can contain multiple values or long string
                'null'       => true,
            ],
            
            // Boolean Flags (Yes/No)
            'has_seal_arrangement' => [
                'type'       => 'VARCHAR', // Yes/No
                'constraint' => '10',
                'null'       => true,
            ],
            'has_adjustment_mechanism' => [
                'type'       => 'VARCHAR', // Yes/No
                'constraint' => '10',
                'null'       => true,
            ],
            'has_gauge_glass' => [
                'type'       => 'VARCHAR', // Yes/No
                'constraint' => '10',
                'null'       => true,
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
        $this->forge->createTable('capacity_measure_instruments');
    }

    public function down()
    {
        $this->forge->dropTable('capacity_measure_instruments');
    }
}
