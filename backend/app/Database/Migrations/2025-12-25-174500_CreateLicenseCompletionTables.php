<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicenseCompletionTables extends Migration
{
    public function up()
    {
        // 1. license_previous_licenses
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'application_id' => ['type' => 'CHAR', 'constraint' => 36],
            'license_number' => ['type' => 'VARCHAR', 'constraint' => 255],
            'date_issued' => ['type' => 'DATE'],
            'class' => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        // Using a unique name for foreign key to avoid conflicts
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE', 'fk_prev_lic_app_id');
        $this->forge->createTable('license_previous_licenses');

        // 2. license_qualifications
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'application_id' => ['type' => 'CHAR', 'constraint' => 36],
            'institution' => ['type' => 'VARCHAR', 'constraint' => 255],
            'award' => ['type' => 'VARCHAR', 'constraint' => 255],
            'year' => ['type' => 'VARCHAR', 'constraint' => 4],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE', 'fk_qual_app_id');
        $this->forge->createTable('license_qualifications');

        // 3. license_experiences
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'application_id' => ['type' => 'CHAR', 'constraint' => 36],
            'company' => ['type' => 'VARCHAR', 'constraint' => 255],
            'position' => ['type' => 'VARCHAR', 'constraint' => 255],
            'years' => ['type' => 'VARCHAR', 'constraint' => 10], 
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE', 'fk_exp_app_id');
        $this->forge->createTable('license_experiences');

        // 4. license_tools
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'application_id' => ['type' => 'CHAR', 'constraint' => 36],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'serial_number' => ['type' => 'VARCHAR', 'constraint' => 255],
            'capacity' => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE', 'fk_tools_app_id');
        $this->forge->createTable('license_tools');
    }

    public function down()
    {
        $this->forge->dropTable('license_tools');
        $this->forge->dropTable('license_experiences');
        $this->forge->dropTable('license_qualifications');
        $this->forge->dropTable('license_previous_licenses');
    }
}
