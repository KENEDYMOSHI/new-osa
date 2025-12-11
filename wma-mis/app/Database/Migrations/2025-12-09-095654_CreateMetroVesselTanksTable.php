<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetroVesselTanksTable extends Migration
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
            'vesselId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tankName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'createdAt' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updatedAt' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('vesselId', 'metro_vessels', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('metro_vesselTanks');
    }

    public function down()
    {
        $this->forge->dropTable('metro_vesselTanks');
    }
}
