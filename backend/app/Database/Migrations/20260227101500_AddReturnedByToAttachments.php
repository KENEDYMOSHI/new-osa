<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReturnedByToAttachments extends Migration
{
    public function up()
    {
        // Add returned_by column to track which officer returned the document
        $this->forge->addColumn('license_application_attachments', [
            'returned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'rejection_reason',
            ]
        ]);
        
        // Optional: add foreign key if users table is guaranteed to exist and match
        // $this->forge->addForeignKey('returned_by', 'license_users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropColumn('license_application_attachments', 'returned_by');
    }
}
