<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimeAndPressureLogs extends Migration
{
    public function up()
    {
        // --- Table: metro_timeLogs ---
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
            'voyageId' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'logDate' => [
                'type' => 'DATE',
            ],
            'logTime' => [
                'type' => 'TIME',
            ],
            'eventDescription' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addForeignKey('vesselId', 'metro_vessels', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('voyageId', 'metro_voyage', 'voyageId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('metro_timeLogs');


        // --- Table: metro_pressureLogs ---
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'productId' => [
                'type' => 'INT', // Assuming metro_products uses INT id
                'constraint' => 11,
                'unsigned' => true,
            ],
            'vesselId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'voyageId' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'logDate' => [
                'type' => 'DATE',
            ],
            'logTime' => [
                'type' => 'TIME',
            ],
            'pressure' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
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
        $this->forge->addForeignKey('productId', 'metro_products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('vesselId', 'metro_vessels', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('voyageId', 'metro_voyage', 'voyageId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('metro_pressureLogs');
    }

    public function down()
    {
        $this->forge->dropTable('metro_timeLogs');
        $this->forge->dropTable('metro_pressureLogs');
    }
}
