<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnforceCategoryNotNull extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // First, ensure all existing records have a category
        $db->query("
            UPDATE license_application_attachments 
            SET category = CASE 
                WHEN LOWER(document_type) IN ('psle', 'csee', 'acsee', 'veta', 'nta4', 'nta5', 'nta6', 
                                               'specialized', 'bachelor', 'diploma', 'degree', 'master', 'phd',
                                               'cv', 'certificate', 'professional_certificate') 
                THEN 'qualification'
                ELSE 'attachment'
            END
            WHERE category IS NULL OR category = ''
        ");
        
        // Now modify the column to be NOT NULL with a default value
        $fields = [
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'default' => 'attachment'
            ]
        ];
        
        $this->forge->modifyColumn('license_application_attachments', $fields);
        
        echo "Category column is now NOT NULL with default 'attachment'\n";
    }

    public function down()
    {
        // Revert to nullable
        $fields = [
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ]
        ];
        
        $this->forge->modifyColumn('license_application_attachments', $fields);
    }
}
