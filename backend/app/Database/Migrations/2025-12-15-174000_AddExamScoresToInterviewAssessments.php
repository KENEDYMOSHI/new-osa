<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExamScoresToInterviewAssessments extends Migration
{
    public function up()
    {
        $fields = [
            'theory_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'result',
            ],
            'practical_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'theory_score',
            ],
            'total_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'practical_score',
            ],
        ];
        
        $this->forge->addColumn('interview_assessments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('interview_assessments', ['theory_score', 'practical_score', 'total_score']);
    }
}
