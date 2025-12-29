<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLicenseApplicationItemsTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('license_application_items', true);
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36, // MD5 is 32, UUID is 36. Using 36 for safety.
            ],
            'application_id' => [
                'type' => 'CHAR', // Matching license_applications.id
                'constraint' => 36,
            ],
            'license_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'fee' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'application_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50, // New or Renewal
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
        $this->forge->addForeignKey('application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE', 'license_application_items_app_id_fk_new');
        $this->forge->createTable('license_application_items');
    }

    public function down()
    {
        $this->forge->dropTable('license_application_items');
    }
}
