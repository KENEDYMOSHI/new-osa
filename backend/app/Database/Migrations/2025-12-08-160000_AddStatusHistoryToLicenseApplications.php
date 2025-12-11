<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusHistoryToLicenseApplications extends Migration
{
    public function up()
    {
        $fields = [
            'status_stage_1' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'approver_stage_1'],
            'status_stage_2' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'approver_stage_2'],
            'status_stage_3' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'approver_stage_3'],
            'status_stage_4' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'approver_stage_4'],
        ];
        
        foreach ($fields as $field => $attr) {
            if (!$this->db->fieldExists($field, 'license_applications')) {
                $this->forge->addColumn('license_applications', [$field => $attr]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('license_applications', ['status_stage_1', 'status_stage_2', 'status_stage_3', 'status_stage_4']);
    }
}
