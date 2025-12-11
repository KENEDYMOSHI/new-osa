<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelephoneToMetroTerminals extends Migration
{
    public function up()
    {
        $fields = [
            'telephone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'after'      => 'phoneNumber',
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('metro_terminals', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('metro_terminals', 'telephone');
    }
}
