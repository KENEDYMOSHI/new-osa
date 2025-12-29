<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorLicenseCompletionTables extends Migration
{
    public function up()
    {
        // 1. Drop the fragmented tables if they exist
        $this->forge->dropTable('license_tools', true);
        $this->forge->dropTable('license_experiences', true);
        $this->forge->dropTable('license_qualifications', true);
        $this->forge->dropTable('license_previous_licenses', true);

        // 2. Create the Single Combined Table
        // "make one containing everything"
        // "in a column create a place where approved licenses will be" -> application_id foreign key
        
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'application_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            
            // Completion Details (JSON storage for lists)
            'previous_licenses' => ['type' => 'JSON', 'null' => true],
            'qualifications'    => ['type' => 'JSON', 'null' => true],
            'experiences'       => ['type' => 'JSON', 'null' => true],
            'tools'             => ['type' => 'JSON', 'null' => true],
            
            'declaration' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        
        $this->forge->addKey('id', true);
        // Link to the approved application
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE', 'fk_completion_app_id');
        
        $this->forge->createTable('license_completions');
    }

    public function down()
    {
        $this->forge->dropTable('license_completions');
        // We do not restore the fragmneted tables as they are deprecated
    }
}
