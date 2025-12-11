<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToLicenseAttachments extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Returned', 'Resubmitted', 'Approved'],
                'default'    => 'Draft',
                'after'      => 'mime_type' // Place after mime_type
            ],
            'rejection_reason' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'after'      => 'status'
            ],
        ];

        $this->forge->addColumn('license_application_attachments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', ['status', 'rejection_reason']);
    }
}
