<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewAssessmentTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('interview_assessments', true);
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'result' => [
                'type' => 'ENUM',
                'constraint' => ['PASS', 'FAIL'],
            ],
            'scores' => [
                'type' => 'TEXT', // JSON array of scores
                'null' => true,
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'interview_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'panel_names' => [
                'type' => 'TEXT', // JSON array or comma separated
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('interview_assessments');
    }

    public function down()
    {
        $this->forge->dropTable('interview_assessments');
    }
}
