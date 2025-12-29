<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationTypeFeesTable extends Migration
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
            'application_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'New License or Renew License',
            ],
            'nationality' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Citizen or Non-Citizen',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'comment' => 'Fee amount in TZS',
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
