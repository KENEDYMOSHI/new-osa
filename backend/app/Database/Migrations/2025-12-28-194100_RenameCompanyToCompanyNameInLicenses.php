<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameCompanyToCompanyNameInLicenses extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('licenses', [
            'company' => [
                'name' => 'company_name',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('licenses', [
            'company_name' => [
                'name' => 'company',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }
}
