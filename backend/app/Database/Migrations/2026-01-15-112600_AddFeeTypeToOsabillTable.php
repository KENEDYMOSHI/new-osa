<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFeeTypeToOsabillTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('osabill', [
            'fee_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'bill_type',
                'comment' => 'Indicates whether this is an Application Fee or License Fee'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('osabill', 'fee_type');
    }
}
