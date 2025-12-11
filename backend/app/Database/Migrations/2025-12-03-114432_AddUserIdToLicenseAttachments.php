<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToLicenseAttachments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_application_attachments', [
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true, // Initially null for existing records, or false if we want to enforce it. Let's make it nullable for now or default.
                // Actually, for new uploads it will be set. For existing ones (if any), it might be empty.
                // Given this is dev, let's just add it.
            ],
        ]);

        $this->forge->modifyColumn('license_application_attachments', [
            'application_id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', 'user_id');
        $this->forge->modifyColumn('license_application_attachments', [
            'application_id' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
            ],
        ]);
    }
}
