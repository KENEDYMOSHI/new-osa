<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUuidToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'after'      => 'username',
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'uuid');
    }
}
