<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddControlNumberToLicenseApplications extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_applications', [
            'control_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', 'control_number');
    }
}
