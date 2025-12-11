<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropMetroDeportsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('metro_deports', true);
    }

    public function down()
    {
        // Re-create it if rollback needed, but for now we leave empty or basic reconstruct
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'deportName' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'capacity' => [
                'type' => 'DECIMAL',
                'constraint' => '12,4',
                'null' => true,
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
        $this->forge->createTable('metro_deports');
    }
}
