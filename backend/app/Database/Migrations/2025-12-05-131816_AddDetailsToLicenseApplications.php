<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailsToLicenseApplications extends Migration
{
    public function up()
    {
        $fields = [
            'previous_licenses' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'qualifications' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'experiences' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tools' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('license_applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', ['previous_licenses', 'qualifications', 'experiences', 'tools']);
    }
}
