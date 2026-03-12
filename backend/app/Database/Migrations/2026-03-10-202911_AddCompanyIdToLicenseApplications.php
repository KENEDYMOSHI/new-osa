<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyIdToLicenseApplications extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_applications', [
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', 'company_id');
    }
}
