<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFileContentToAttachments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_application_attachments', [
            'file_content' => [
                'type' => 'LONGBLOB',
                'null' => true,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
        ]);
        
        // Make file_path nullable as we might not use it anymore
        $this->forge->modifyColumn('license_application_attachments', [
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', ['file_content', 'mime_type']);
        $this->forge->modifyColumn('license_application_attachments', [
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
        ]);
    }
}
