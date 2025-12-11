<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToLicenseAttachments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_application_attachments', [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', ['created_at', 'updated_at']);
    }
}
