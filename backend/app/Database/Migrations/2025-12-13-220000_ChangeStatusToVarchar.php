<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeStatusToVarchar extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'default'    => 'Draft',
            ],
        ];

        $this->forge->modifyColumn('license_applications', $fields);
    }

    public function down()
    {
        // Revert to ENUM (This might be lossy if values outside enum range exist, so handle with care or just leave as varchar)
        // ideally revert to previous state.
        $fields = [
             'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Submitted', 'Approved_DTS', 'Approved_CEO', 'License_Generated', 'Rejected'],
                'default' => 'Draft',
            ],
        ];
        $this->forge->modifyColumn('license_applications', $fields);
    }
}
