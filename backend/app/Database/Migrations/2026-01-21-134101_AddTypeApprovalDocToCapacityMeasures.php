<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeApprovalDocToCapacityMeasures extends Migration
{
    public function up()
    {
        $this->forge->addColumn('capacity_measure_instruments', [
            'type_approval_doc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('capacity_measure_instruments', 'type_approval_doc');
    }
}
