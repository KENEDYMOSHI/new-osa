<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicensesTable extends Migration
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
                'null' => false,
            ],
            'bill_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'control_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'applicant_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'applicant_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'application_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'license_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'license_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'license_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'payment_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date when payment was completed',
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
        $this->forge->addUniqueKey('license_number');
        $this->forge->addKey('applicant_id');
        $this->forge->addKey('application_id');
        
        // Add foreign key constraint
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('licenses');
    }

    public function down()
    {
        $this->forge->dropTable('licenses');
    }
}
