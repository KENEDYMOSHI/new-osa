<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreateLicenseAttachmentsTable extends Migration
{
    public function up()
    {
        // Drop if exists to be safe
        $this->forge->dropTable('license_application_attachments', true);

        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36, // Increased to 36 for UUID compatibility
            ],
            'user_id' => [
                'type' => 'INT', // Matched to new schema user_id (INT UNSIGNED)
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'application_id' => [
                'type' => 'VARCHAR', // Can hold UUID (36) or old ID (32)
                'constraint' => 36,
                'null' => true,
            ],
            'document_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'file_content' => [
                'type' => 'LONGBLOB', // Ensure huge files fit, though MEDIUMBLOB usually enough
                'null' => true,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('application_id');
        $this->forge->createTable('license_application_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('license_application_attachments');
    }
}
