<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLicenseApplicationsTable extends Migration
{
    public function up()
    {
        // Modify initial_application_id to be nullable
        $this->forge->modifyColumn('license_applications', [
            'initial_application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
        ]);

        // Add missing columns
        $this->forge->addColumn('license_applications', [
            'application_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'previous_licenses' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'qualifications' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'experiences' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'tools' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', [
            'application_type', 'total_amount', 'previous_licenses', 'qualifications', 'experiences', 'tools'
        ]);
        
        // Reverting modification is tricky without knowing exact previous state, assuming strict
        // $this->forge->modifyColumn('license_applications', [ ... ]);
    }
}
