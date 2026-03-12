<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyIdToAttachments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('license_application_attachments', [
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'application_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', 'company_id');
    }
}
