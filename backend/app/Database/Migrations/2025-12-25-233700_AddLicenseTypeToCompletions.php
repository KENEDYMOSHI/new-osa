<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLicenseTypeToCompletions extends Migration
{
    public function up()
    {
        $fields = [
            'license_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'user_id'
            ]
        ];
        
        $this->forge->addColumn('license_completions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_completions', 'license_type');
    }
}
