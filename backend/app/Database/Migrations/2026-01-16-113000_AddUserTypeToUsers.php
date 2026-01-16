<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserTypeToUsers extends Migration
{
    public function up()
    {
        // Add user_type column to users table
        $fields = [
            'user_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'practitioner',
                'after'      => 'uuid',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // Remove user_type column
        $this->forge->dropColumn('users', 'user_type');
    }
}
