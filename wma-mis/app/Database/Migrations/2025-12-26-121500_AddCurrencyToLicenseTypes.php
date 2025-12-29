<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCurrencyToLicenseTypes extends Migration
{
    public function up()
    {
        $fields = [
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'TZS',
                'after'      => 'fee'
            ],
        ];

        $this->forge->addColumn('license_types', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_types', 'currency');
    }
}
