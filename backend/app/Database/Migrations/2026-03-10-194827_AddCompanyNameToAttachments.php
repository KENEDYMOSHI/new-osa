<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyNameToAttachments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_application_attachments', [
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', 'company_name');
    }
}
