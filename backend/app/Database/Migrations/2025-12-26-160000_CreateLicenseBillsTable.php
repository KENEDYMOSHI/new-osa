<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicenseBillsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'control_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'license_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'application_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Paid', 'Failed', 'Cancelled'],
                'default' => 'Pending',
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'payment_date' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('license_bills');
    }

    public function down()
    {
        $this->forge->dropTable('license_bills');
    }
}
