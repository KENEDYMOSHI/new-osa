<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToLicenseApplicationItems extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Submitted',
                'after'      => 'fee'
            ],
            // Tracking "Level" or "Stage" might belong to the item if they move independently
            // e.g. "Manager", "Surveillance", "DTS", "CEO"
            // Let's call it "approval_stage"
            'approval_stage' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Manager',
                'after'      => 'status'
            ]
        ];
        $this->forge->addColumn('license_application_items', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_items', ['status', 'approval_stage']);
    }
}
