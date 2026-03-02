<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRequestNumberToWmaNotifications extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('request_number', 'wma_notifications')) {
            $this->forge->addColumn('wma_notifications', [
                'request_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'related_entity_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('request_number', 'wma_notifications')) {
            $this->forge->dropColumn('wma_notifications', 'request_number');
        }
    }
}
