<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSelectedInstrumentsToLicenseApplicationItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_application_items', [
            'selected_instruments' => [
                'type' => 'TEXT', 
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_items', 'selected_instruments');
    }
}
