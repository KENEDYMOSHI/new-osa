<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicenseApplicationsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('license_applications', true);
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'initial_application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'license_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Approved_DTS', 'Approved_CEO', 'License_Generated', 'Rejected'],
                'default' => 'Draft',
            ],
            'workflow_stage' => [
                'type' => 'INT',
                'default' => 0, // 0: Draft, 1: DTS, 2: CEO, 3: Completed
            ],
            'valid_from' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'valid_to' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('initial_application_id', 'initial_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('license_applications');
    }

    public function down()
    {
        $this->forge->dropTable('license_applications');
    }
}
