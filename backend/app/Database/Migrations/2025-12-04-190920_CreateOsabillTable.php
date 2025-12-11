<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOsabillTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'bill_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'control_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'bill_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'payer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'payer_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'bill_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'bill_expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'collection_center' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'VARCHAR', // Using VARCHAR to support both INT and UUID
                'constraint' => 255,
                'null' => true,
            ],
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Pending',
            ],
            'items' => [
                'type' => 'TEXT', // Storing JSON data
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
        $this->forge->createTable('osabill');
    }

    public function down()
    {
        $this->forge->dropTable('osabill');
    }
}
