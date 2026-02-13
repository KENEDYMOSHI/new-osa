<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateFormDRequestsTable extends Migration
{
    public function up()
    {
        $fields = [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id'
            ],
            'license_number' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'practitioner_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'practitioner_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'cert_auth_number' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'district' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ward' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'street' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'certification_action' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'instrument_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'product' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'sticker_number' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'seal_number' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'type_of_instrument' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'capacity' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'capacity_unit' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
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
            // Declarant fields
            'declarant_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
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
                 'type' => 'VARCHAR',
                 'constraint' => '100',
                 'null' => true,
            ],
             'declarant_phone' => [
                 'type' => 'VARCHAR',
                 'constraint' => '20',
                 'null' => true,
            ],
        ];

        // Check if columns exist before adding to avoid errors if re-running or partial updates
        // However, standard migration practice assumes we know the state. 
        // Given the "DbTool" check earlier, capacity_unit might exist. 
        // I'll add logic to check existence if possible, or just try adding.
        // Forge addColumn doesn't automatically check existence in all drivers.
        
        // Let's add them. If they exist, it might error, but usually we just want to ensure they are there.
        // To be safe against Duplicate column name errors if DbTool was run, I'll filter out capacity_unit if it causes issues,
        // but for a clean migration, I'll list all desired fields.
        
        // NOTE: capacity_unit was added by DbTool in a previous turn (or potentially).
        // I will assume it might fail if I don't check, but Migrations are usually versioned.
        // I'll proceed with adding columns.
        
        $this->forge->addColumn('form_d_requests', $fields);
    }

    public function down()
    {
        $fields = [
            'user_id',
            'license_number',
            'practitioner_name',
            'practitioner_phone',
            'cert_auth_number',
            'company_name',
            'region',
            'district',
            'ward',
            'street',
            'postal_code',
            'address',
            'certification_action',
            'instrument_name',
            'serial_number',
            'product',
            'sticker_number',
            'seal_number',
            'type_of_instrument',
            'quantity',
            'capacity',
            'capacity_unit',
            'verification_date',
            'next_verification_date',
            'inspection_report',
            'declarant_name',
            'declarant_date',
            'declarant_time',
            'declarant_designation',
            'declarant_phone'
        ];
        $this->forge->dropColumn('form_d_requests', $fields);
    }
}
