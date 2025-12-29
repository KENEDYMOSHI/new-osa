<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTownToWard extends Migration
{
    public function up()
    {
        // Rename 'town' to 'ward' in practitioner_personal_infos table
        $this->forge->modifyColumn('practitioner_personal_infos', [
            'town' => [
                'name' => 'ward',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
        ]);

        // Rename 'bus_town' to 'bus_ward' in practitioner_business_infos table
        $this->forge->modifyColumn('practitioner_business_infos', [
            'bus_town' => [
                'name' => 'bus_ward',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        // Revert 'ward' back to 'town' in practitioner_personal_infos table
        $this->forge->modifyColumn('practitioner_personal_infos', [
            'ward' => [
                'name' => 'town',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
        ]);

        // Revert 'bus_ward' back to 'bus_town' in practitioner_business_infos table
        $this->forge->modifyColumn('practitioner_business_infos', [
            'bus_ward' => [
                'name' => 'bus_town',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
        ]);
    }
}
