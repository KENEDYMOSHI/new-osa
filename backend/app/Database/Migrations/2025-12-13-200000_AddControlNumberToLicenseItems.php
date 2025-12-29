<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddControlNumberToLicenseItems extends Migration
{
    public function up()
    {
        $fields = [
            'control_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'application_fee'
            ],
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Pending',
                'after' => 'control_number'
            ],
        ];
        $this->forge->addColumn('license_application_items', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_items', 'control_number');
        $this->forge->dropColumn('license_application_items', 'payment_status');
    }
}
