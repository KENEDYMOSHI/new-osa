<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePractitionersTable extends Migration
{
    public function up()
    {
        // Personal Info Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nationality' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'identity_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'second_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'gender' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'dob' => [
                'type' => 'DATE',
            ],
            'region' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'district' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'town' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'street' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        // $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('practitioner_personal_infos');

        // Business Info Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tin' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'company_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'company_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'brela_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'bus_region' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'bus_district' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'bus_town' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'bus_street' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        // $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('practitioner_business_infos');
    }

    public function down()
    {
        $this->forge->dropTable('practitioner_business_infos');
        $this->forge->dropTable('practitioner_personal_infos');
    }
}
