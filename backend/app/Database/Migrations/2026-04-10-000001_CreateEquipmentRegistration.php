<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipmentRegistration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'registration_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'service_type_key' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'service_type_label' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'equipment_data' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'pending', 'verified', 'rejected'],
                'default' => 'draft',
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'verifier_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'verifier_notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('user_uuid');
        $this->forge->addKey('service_type_key');
        $this->forge->addKey('status');
        $this->forge->addKey('category');
        $this->forge->createTable('equipment_registrations', true);

        // Setting default CURRENT_TIMESTAMP manually if CI4 doesn't support it directly in Migrations easily
        $this->db->query('ALTER TABLE `equipment_registrations` CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;');
        $this->db->query('ALTER TABLE `equipment_registrations` CHANGE `updated_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;');
    }

    public function down()
    {
        $this->forge->dropTable('equipment_registrations', true);
    }
}
