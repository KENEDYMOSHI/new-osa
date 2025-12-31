<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOsaSupportDetailsTable extends Migration
{
    protected $DBGroup = 'osa';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phone_label_1' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_number_1' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_label_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_number_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_label_3' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_number_3' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email_general' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email_tech' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'website' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->createTable('osa_support_details');

        // Insert default data
        $data = [
            'address'        => "Wakala wa Vipimo (WMA)\nVipimo House, Chief Chemist Street\nS.L.P. 2014, Dodoma – Tanzania",
            'phone_label_1'  => 'Office',
            'phone_number_1' => '+255 (26) 22610700',
            'email_general'  => 'info@wma.go.tz',
            'email_tech'     => 'ictsupport@wma.go.tz',
            'website'        => 'www.wma.go.tz',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('osa_support_details')->insert($data);
    }

    public function down()
    {
        $this->forge->dropTable('osa_support_details');
    }
}
