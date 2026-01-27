<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormDRequestsTable extends Migration
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
                'null'       => true, // Make nullable in case of issues, but ideally should be linked
            ],
            'license_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'practitioner_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'practitioner_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'cert_auth_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'region' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'district' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'ward' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'street' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'address' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'certification_action' => [
                'type'       => 'ENUM',
                'constraint' => ['Erected', 'Adjusted', 'Repaired'],
                'default'    => 'Repaired',
            ],
            'instrument_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'product' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'sticker_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'seal_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'type_of_instrument' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'capacity' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Verified', 'Pending Verification', 'Rejected'],
                'default'    => 'Pending Verification',
            ],
            'verification_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'next_verification_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'inspection_report' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'declarant_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'declarant_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'declarant_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'declarant_designation' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'declarant_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
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
        $this->forge->createTable('form_d_requests');
    }

    public function down()
    {
        $this->forge->dropTable('form_d_requests');
    }
}
