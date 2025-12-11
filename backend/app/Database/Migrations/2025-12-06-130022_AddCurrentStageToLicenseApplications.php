<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCurrentStageToLicenseApplications extends Migration
{
    public function up()
    {
        $fields = [
            'current_stage' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1, // 1: Manager, 2: Surveillance, 3: DTS, 4: CEO
                'after'      => 'status'
            ],
        ];
        $this->forge->addColumn('license_applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', 'current_stage');
    }
}
