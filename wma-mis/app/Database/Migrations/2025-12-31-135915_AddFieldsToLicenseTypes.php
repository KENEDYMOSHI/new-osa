<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToLicenseTypes extends Migration
{
    protected $DBGroup = 'osa';

    public function up()
    {
        $fields = [
            'selected_instruments' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'currency'
            ],
            'criteria' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'selected_instruments'
            ],
        ];

        $this->forge->addColumn('license_types', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_types', 'selected_instruments');
        $this->forge->dropColumn('license_types', 'criteria');
    }
}
