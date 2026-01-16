<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeBusinessFieldsNullable extends Migration
{
    public function up()
    {
        // Make brela_number, company_name, company_email, and company_phone nullable
        // to support Pattern Approval registrations where these fields are optional
        
        $fields = [
            'brela_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'company_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'company_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('practitioner_business_infos', $fields);
    }

    public function down()
    {
        // Revert to NOT NULL (original state)
        $fields = [
            'brela_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'company_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'company_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('practitioner_business_infos', $fields);
    }
}
