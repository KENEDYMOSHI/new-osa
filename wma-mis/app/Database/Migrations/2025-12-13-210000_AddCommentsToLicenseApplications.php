<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommentsToLicenseApplications extends Migration
{
    public function up()
    {
        $fields = [
            'comment_stage_1' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status_stage_1',
            ],
            'comment_stage_2' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status_stage_2',
            ],
            'comment_stage_3' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status_stage_3',
            ],
            'comment_stage_4' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status_stage_4',
            ],
        ];

        $this->forge->addColumn('license_applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', [
            'comment_stage_1', 
            'comment_stage_2', 
            'comment_stage_3', 
            'comment_stage_4'
        ]);
    }
}
