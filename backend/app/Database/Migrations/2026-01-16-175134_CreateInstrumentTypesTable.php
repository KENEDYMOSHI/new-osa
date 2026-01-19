<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstrumentTypesTable extends Migration
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
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addForeignKey('category_id', 'instrument_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('instrument_types');
    }

    public function down()
    {
        $this->forge->dropTable('instrument_types');
    }
}
