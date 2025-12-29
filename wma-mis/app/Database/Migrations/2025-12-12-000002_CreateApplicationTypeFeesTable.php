<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationTypeFeesTable extends Migration
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
            'application_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nationality' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
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
        $this->forge->createTable('application_type_fees');
    }

    public function down()
    {
        $this->forge->dropTable('application_type_fees');
    }
}
