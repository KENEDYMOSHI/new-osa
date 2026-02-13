<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTechniciansRegistryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'service_date' => [
                'type' => 'DATE',
            ],
            'instrument_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'sticker_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'instrument_issue' => [
                'type' => 'TEXT',
            ],
            'work_performed' => [
                'type' => 'TEXT',
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'district' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'ward' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('technicians_registry');
    }

    public function down()
    {
        $this->forge->dropTable('technicians_registry');
    }
}
