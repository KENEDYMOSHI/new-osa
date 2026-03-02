<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWmaNotificationsTable extends Migration
{
    public function up()
    {
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
                'null'       => true,
                'comment'    => 'vessel_discharge users.id of the officer to notify',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['document_reuploaded', 'application_approved', 'info'],
                'default'    => 'info',
            ],
            'related_entity_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'UUID of the related license application',
            ],
            'is_read' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('is_read');
        $this->forge->createTable('wma_notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('wma_notifications', true);
    }
}
