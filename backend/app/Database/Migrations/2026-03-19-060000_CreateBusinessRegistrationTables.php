<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBusinessRegistrationTables extends Migration
{
    public function up()
    {
        # 1. Create Business Users Table (auth/login)
        $this->forge->addField([
            'id'                => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'uuid'              => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'username'          => ['type' => 'varchar', 'constraint' => 30, 'null' => true, 'unique' => true],
            'email'             => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'unique' => true],
            'password_hash'     => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'user_type'         => ['type' => 'varchar', 'constraint' => 50, 'default' => 'business_owner'],
            'registration_type' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'business_owner'],
            'business_type'     => ['type' => 'varchar', 'constraint' => 50, 'null' => true],
            'phone_number'      => ['type' => 'varchar', 'constraint' => 20, 'null' => true],
            'region'            => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'status'            => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status_message'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active'            => ['type' => 'tinyint', 'constraint' => 1, 'null' => false, 'default' => 0],
            'last_active'       => ['type' => 'datetime', 'null' => true],
            'created_at'        => ['type' => 'datetime', 'null' => true],
            'updated_at'        => ['type' => 'datetime', 'null' => true],
            'deleted_at'        => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('business_users');

        # 2. Create Business Owner Infos Table (business + ownership data)
        $this->forge->addField([
            'id'                     => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_uuid'              => ['type' => 'varchar', 'constraint' => 32],
            'company_name'           => ['type' => 'varchar', 'constraint' => 255],
            'business_type'          => ['type' => 'varchar', 'constraint' => 50],
            'tin'                    => ['type' => 'varchar', 'constraint' => 20],
            'business_license_number' => ['type' => 'varchar', 'constraint' => 50],
            'postal_address'         => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'office_phone_number'    => ['type' => 'varchar', 'constraint' => 20, 'null' => true],
            'region'                 => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'district'               => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'ward'                   => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'postal_code'            => ['type' => 'varchar', 'constraint' => 20, 'null' => true],
            'owner_first_name'       => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'owner_second_name'      => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'owner_last_name'        => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'owner_phone_number'     => ['type' => 'varchar', 'constraint' => 20, 'null' => true],
            'owner_email_address'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'owner_postal_address'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at'             => ['type' => 'datetime', 'null' => true],
            'updated_at'             => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_uuid');
        $this->forge->createTable('business_owner_infos');

        # 3. Create Business Contact Infos Table (contact person)
        $this->forge->addField([
            'id'                       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_uuid'                => ['type' => 'varchar', 'constraint' => 32],
            'first_name'               => ['type' => 'varchar', 'constraint' => 100],
            'second_name'              => ['type' => 'varchar', 'constraint' => 100],
            'last_name'                => ['type' => 'varchar', 'constraint' => 100],
            'designation'              => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'phone_number'             => ['type' => 'varchar', 'constraint' => 20],
            'alternative_phone_number' => ['type' => 'varchar', 'constraint' => 20, 'null' => true],
            'email_address'            => ['type' => 'varchar', 'constraint' => 255],
            'created_at'               => ['type' => 'datetime', 'null' => true],
            'updated_at'               => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_uuid');
        $this->forge->createTable('business_contact_infos');
    }

    public function down()
    {
        $this->forge->dropTable('business_contact_infos', true);
        $this->forge->dropTable('business_owner_infos', true);
        $this->forge->dropTable('business_users', true);
    }
}
