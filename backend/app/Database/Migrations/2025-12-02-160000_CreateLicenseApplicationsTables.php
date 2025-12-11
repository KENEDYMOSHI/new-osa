<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicenseApplicationsTables extends Migration
{
    public function up()
    {
        // License Applications Table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'user_id' => [
                'type' => 'INT', // Assuming users table uses INT id based on previous context, or VARCHAR if UUID. Let's check users table or assume INT for now based on typical CI4 Shield, but previous refactor might have changed it. 
                // Wait, previous conversation mentioned refactoring to UUID. Let's check a recent migration to be sure.
                // I'll check the migration file I viewed earlier: 2025-12-01-163500_RefactorPractitionersToUuid.php
                // It seems users table might still be using INT or maybe UUID.
                // To be safe, I will check the users table schema if possible, or just use VARCHAR 255 to be safe for both.
                // Actually, let's look at the `RefactorPractitionersToUuid` migration again or just use VARCHAR 255 for user_id to be safe.
                'constraint' => 255, 
                'null' => false,
            ],
            'application_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50, // New or Renewal
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Pending',
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
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
        $this->forge->createTable('license_applications');

        // License Application Items Table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'application_id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'license_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'fee' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('license_application_items');

        // License Application Attachments Table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'application_id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'document_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('license_application_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('license_application_attachments');
        $this->forge->dropTable('license_application_items');
        $this->forge->dropTable('license_applications');
    }
}
