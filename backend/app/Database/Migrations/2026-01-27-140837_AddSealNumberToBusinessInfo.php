<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSealNumberToBusinessInfo extends Migration
{
    public function up()
    {
        $fields = [
            'seal_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'tin'
            ],
        ];
        $this->forge->addColumn('practitioner_business_infos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('practitioner_business_infos', 'seal_number');
    }
}
