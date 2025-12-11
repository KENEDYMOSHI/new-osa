<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialApplicationsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('initial_applications', true);
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'control_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'application_type' => [
                'type' => 'ENUM',
                'constraint' => ['New', 'Renewal'],
                'default' => 'New',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Approved_Regional', 'Approved_Surveillance', 'Rejected'],
                'default' => 'Draft',
            ],
            'workflow_stage' => [
                'type' => 'INT',
                'default' => 0, // 0: Draft, 1: Regional, 2: Surveillance, 3: Completed
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('initial_applications');
    }

    public function down()
    {
        $this->forge->dropTable('initial_applications');
    }
}
