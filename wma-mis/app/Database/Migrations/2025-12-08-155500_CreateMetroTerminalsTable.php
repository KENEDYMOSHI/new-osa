<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetroTerminalsTable extends Migration
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
            'terminalName' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'postalAddress' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'phoneNumber' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'physicalAddress' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'createdAt' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updatedAt' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('metro_terminals');
    }

    public function down()
    {
        $this->forge->dropTable('metro_terminals');
    }
}
