<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetroVoyageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'voyageId' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'vesselId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'vesselExperienceFactor' => [
                'type' => 'DECIMAL',
                'constraint' => '12,6',
                'null' => true,
            ],
            'loadingPort' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'arrivalPort' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'arrivalBerth' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'loadingDate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'arrivalDate' => [
                'type' => 'DATETIME',
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

        $this->forge->addPrimaryKey('voyageId');
        $this->forge->addForeignKey('vesselId', 'metro_vessels', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('loadingPort', 'metro_port', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('arrivalPort', 'metro_port', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('arrivalBerth', 'metro_berths', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('metro_voyage');
    }

    public function down()
    {
        $this->forge->dropTable('metro_voyage');
    }
}
