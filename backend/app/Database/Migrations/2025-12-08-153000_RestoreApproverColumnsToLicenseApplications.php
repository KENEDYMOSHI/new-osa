<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RestoreApproverColumnsToLicenseApplications extends Migration
{
    public function up()
    {
        // Re-add potentially missing columns if they don't exist
        $fields = [
            'approval_stage' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'status'],
            'current_stage' => ['type' => 'INT', 'constraint' => 11, 'default' => 1, 'after' => 'approval_stage'],
            'approver_stage_1' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'current_stage'],
            'approver_stage_2' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'approver_stage_1'],
            'approver_stage_3' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'approver_stage_2'],
            'approver_stage_4' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'approver_stage_3'],
        ];
        
        foreach ($fields as $field => $attr) {
            if (!$this->db->fieldExists($field, 'license_applications')) {
                $this->forge->addColumn('license_applications', [$field => $attr]);
            }
        }
    }

    public function down()
    {
        // No down needed as we don't want to lose data
    }
}
