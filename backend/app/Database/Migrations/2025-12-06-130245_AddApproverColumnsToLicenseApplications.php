<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApproverColumnsToLicenseApplications extends Migration
{
    public function up()
    {
        $fields = [
            'approver_stage_1' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'current_stage'],
            'approver_stage_2' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'approver_stage_1'],
            'approver_stage_3' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'approver_stage_2'],
            'approver_stage_4' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'approver_stage_3'],
        ];
        $this->forge->addColumn('license_applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', ['approver_stage_1', 'approver_stage_2', 'approver_stage_3', 'approver_stage_4']);
    }
}
