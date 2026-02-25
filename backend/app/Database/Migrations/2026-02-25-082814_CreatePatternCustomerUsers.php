<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePatternCustomerUsers extends Migration
{
    public function up()
    {
        // 1. Create Pattern Users Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'uuid'           => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'username'       => ['type' => 'varchar', 'constraint' => 30, 'null' => true, 'unique' => true],
            'email'          => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'unique' => true],
            'password_hash'  => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'user_type'      => ['type' => 'varchar', 'constraint' => 50, 'default' => 'pattern'],
            'registration_type' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'pattern'],
            'status'         => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status_message' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active'         => ['type' => 'tinyint', 'constraint' => 1, 'null' => false, 'default' => 0],
            'last_active'    => ['type' => 'datetime', 'null' => true],
            'created_at'     => ['type' => 'datetime', 'null' => true],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pattern_users');

        // 2. Create Customer Users Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'uuid'           => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'username'       => ['type' => 'varchar', 'constraint' => 30, 'null' => true, 'unique' => true],
            'email'          => ['type' => 'varchar', 'constraint' => 255, 'null' => true, 'unique' => true],
            'password_hash'  => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'user_type'      => ['type' => 'varchar', 'constraint' => 50, 'default' => 'customer'],
            'registration_type' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'customer'],
            'status'         => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status_message' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active'         => ['type' => 'tinyint', 'constraint' => 1, 'null' => false, 'default' => 0],
            'last_active'    => ['type' => 'datetime', 'null' => true],
            'created_at'     => ['type' => 'datetime', 'null' => true],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customer_users');
    }

    public function down()
    {
        $this->forge->dropTable('pattern_users', true);
        $this->forge->dropTable('customer_users', true);
    }
}
