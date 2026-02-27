<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFailedLoginAttemptsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'failed_login_attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
        ];

        $this->forge->addColumn('license_users', $fields);
        $this->forge->addColumn('pattern_users', $fields);
        $this->forge->addColumn('customer_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_users', 'failed_login_attempts');
        $this->forge->dropColumn('pattern_users', 'failed_login_attempts');
        $this->forge->dropColumn('customer_users', 'failed_login_attempts');
    }
}
