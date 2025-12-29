<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryToLicenseAttachments extends Migration
{
    public function up()
    {
        $fields = [
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'document_type' // Place it after document_type for logical ordering
            ],
        ];

        // Check if column exists first to prevent errors if run multiple times manually
        if (!$this->db->fieldExists('category', 'license_application_attachments')) {
            $this->forge->addColumn('license_application_attachments', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('category', 'license_application_attachments')) {
            $this->forge->dropColumn('license_application_attachments', 'category');
        }
    }
}
