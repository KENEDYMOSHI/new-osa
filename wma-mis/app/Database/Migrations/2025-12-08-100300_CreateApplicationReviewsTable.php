<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationReviewsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('application_reviews', true);
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'application_type' => [
                'type' => 'ENUM',
                'constraint' => ['Initial', 'License'],
            ],
            'approver_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true, // System generated checks might not have a specific user
            ],
            'stage' => [
                'type' => 'VARCHAR',
                'constraint' => 50, // e.g., 'Regional', 'Surveillance', 'DTS', 'CEO'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Approved', 'Rejected', 'Pending'],
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        // We can't strictly foreign key application_id because it can be from either table.
        // We could index it though.
        $this->forge->addKey('application_id');
        $this->forge->createTable('application_reviews');
    }

    public function down()
    {
        $this->forge->dropTable('application_reviews');
    }
}
