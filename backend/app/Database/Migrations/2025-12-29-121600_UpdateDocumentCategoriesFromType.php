<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateDocumentCategoriesFromType extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Define qualification document types
        $qualificationTypes = [
            'psle', 'csee', 'acsee', 'veta', 'nta4', 'nta5', 'nta6', 
            'specialized', 'bachelor', 'diploma', 'degree', 'master', 'phd',
            'cv', 'certificate', 'professional_certificate'
        ];
        
        // Update qualification documents
        foreach ($qualificationTypes as $type) {
            $sql = "UPDATE license_application_attachments 
                    SET category = 'qualification' 
                    WHERE LOWER(document_type) = ? 
                    OR LOWER(document_type) LIKE ?";
            $db->query($sql, [$type, "%{$type}%"]);
        }
        
        // Update all remaining documents to 'attachment' (required documents)
        $sql = "UPDATE license_application_attachments 
                SET category = 'attachment' 
                WHERE category IS NULL OR category = ''";
        $db->query($sql);
        
        echo "Document categories updated successfully.\n";
    }

    public function down()
    {
        // Reset all categories to null
        $db = \Config\Database::connect();
        $db->table('license_application_attachments')->update(['category' => null]);
    }
}
