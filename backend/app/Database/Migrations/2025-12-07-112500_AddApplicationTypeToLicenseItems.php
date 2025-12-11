<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApplicationTypeToLicenseItems extends Migration
{
    public function up()
    {
        $fields = [
            'application_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'New',
                'after'      => 'fee'
            ],
        ];
        $this->forge->addColumn('license_application_items', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_items', 'application_type');
    }
}
