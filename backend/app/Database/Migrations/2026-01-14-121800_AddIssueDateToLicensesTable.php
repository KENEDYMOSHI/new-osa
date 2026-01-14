<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIssueDateToLicensesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('licenses', [
            'issue_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'license_type',
                'comment' => 'Date when the license was issued'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('licenses', 'issue_date');
    }
}
