<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRequestNumberToTables extends Migration
{
    public function up()
    {
        // Add request_number to license_applications
        if (!$this->db->fieldExists('request_number', 'license_applications')) {
            $this->forge->addColumn('license_applications', [
                'request_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'control_number',
                ],
            ]);
        }

        // Add request_number to notifications
        if (!$this->db->fieldExists('request_number', 'notifications')) {
            $this->forge->addColumn('notifications', [
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
        if ($this->db->fieldExists('request_number', 'license_applications')) {
            $this->forge->dropColumn('license_applications', 'request_number');
        }
        if ($this->db->fieldExists('request_number', 'notifications')) {
            $this->forge->dropColumn('notifications', 'request_number');
        }
    }
}
