<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorPractitionersToUuid extends Migration
{
    public function up()
    {
        // Drop existing tables
        $this->forge->dropTable('practitioner_business_infos', true);
        $this->forge->dropTable('practitioner_personal_infos', true);

        // Recreate Personal Info Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
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
        // Add index for user_uuid
        $this->forge->addKey('user_uuid');
        // Note: CodeIgniter 4 migration foreign key support for non-primary keys can be tricky depending on DB driver.
        // We will add a logical foreign key constraint if possible, but for now, we rely on the application logic and index.
        // Ideally: $this->forge->addForeignKey('user_uuid', 'users', 'uuid', 'CASCADE', 'CASCADE');
        // But 'uuid' in 'users' must be indexed/unique. It is unique per our logic but let's ensure it.
        
        $this->forge->createTable('practitioner_personal_infos');

        // Recreate Business Info Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
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
        $this->forge->addKey('user_uuid');
        $this->forge->createTable('practitioner_business_infos');
    }

    public function down()
    {
        $this->forge->dropTable('practitioner_business_infos');
        $this->forge->dropTable('practitioner_personal_infos');
    }
}
