<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyLoadingPortToVarchar extends Migration
{
    public function up()
    {
        // 1. Drop Foreign Key
        $this->forge->dropForeignKey('metro_voyage', 'metro_voyage_loadingPort_foreign');

        // 2. Modify Column
        $this->forge->modifyColumn('metro_voyage', [
            'loadingPort' => [
                'name' => 'loadingPort',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Revert to INT
        $this->forge->modifyColumn('metro_voyage', [
            'loadingPort' => [
                'name' => 'loadingPort',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        // Add FK back
        $this->forge->addForeignKey('loadingPort', 'metro_port', 'id', 'SET NULL', 'CASCADE', 'metro_voyage_loadingPort_foreign');
    }
}
