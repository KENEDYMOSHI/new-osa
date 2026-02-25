<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPatternApplicationsFk extends Migration
{
    public function up()
    {
        // Drop the foreign key constraint that requires user_id to exist in the main users table
        $this->forge->dropForeignKey('pattern_applications', 'pattern_applications_user_id_foreign');
    }

    public function down()
    {
        // Re-add the foreign key
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        // Note: Codeigniter Forge allows processIndexes to recreate constraints
        $this->forge->processIndexes('pattern_applications');
    }
}
