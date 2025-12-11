<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneToPractitionerPersonalInfos extends Migration
{
    public function up()
    {
        $fields = [
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'street',
            ],
        ];
        $this->forge->addColumn('practitioner_personal_infos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('practitioner_personal_infos', 'phone');
    }
}
