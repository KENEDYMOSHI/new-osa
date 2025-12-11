<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetroBerthsTable extends Migration
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
            'berthName' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'portId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addForeignKey('portId', 'metro_port', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('metro_berths');
    }

    public function down()
    {
        $this->forge->dropTable('metro_berths');
    }
}
