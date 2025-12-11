<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBillOfLadingToMetroVoyageProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('metro_voyageProducts', [
            'billOfLading' => [
                'type' => 'DECIMAL',
                'constraint' => '12,5',
                'null' => true,
                'after' => 'secondaryLine'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('metro_voyageProducts', 'billOfLading');
    }
}
