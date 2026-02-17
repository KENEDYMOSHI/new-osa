<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApproverNameToApplicationReviews extends Migration
{
    public function up()
    {
        $fields = [
            'approver_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'approver_id'
            ],
            'approver_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'approver_name'
            ]
        ];
        
        // approver_id already exists in the model but checking if migration added it?
        // Let's check previous migration "2025-12-08-100300_CreateApplicationReviewsTable.php"
        // If it exists, good. If not, add it.
        // Assuming it exists based on Model definition. 
        // Adding name and title.
        
        $this->forge->addColumn('application_reviews', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('application_reviews', 'approver_name');
        $this->forge->dropColumn('application_reviews', 'approver_title');
    }
}
