<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVoyageNumberToMetroVoyage extends Migration
{
    public function up()
    {
        $this->forge->addColumn('metro_voyage', [
            'voyageNumber' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'voyageId',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('metro_voyage', 'voyageNumber');
    }
}
