<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApplicationFeeToLicenseApplicationItems extends Migration
{
    public function up()
    {
        $fields = [
            'application_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
                'after' => 'fee'
            ],
        ];
        $this->forge->addColumn('license_application_items', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_items', 'application_fee');
    }
}
