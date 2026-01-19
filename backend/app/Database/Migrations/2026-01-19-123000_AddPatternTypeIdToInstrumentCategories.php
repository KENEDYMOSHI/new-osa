<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPatternTypeIdToInstrumentCategories extends Migration
{
    public function up()
    {
        $fields = [
            'pattern_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Nullable for existing records initially
                'after'      => 'id',
            ],
        ];

        $this->forge->addColumn('instrument_categories', $fields);
        
        // Add Foreign Key
        $this->forge->addForeignKey('pattern_type_id', 'pattern_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->processIndexes('instrument_categories'); // Important for SQLite/others if needed, but good practice
    }

    public function down()
    {
        $this->forge->dropForeignKey('instrument_categories', 'instrument_categories_pattern_type_id_foreign');
        $this->forge->dropColumn('instrument_categories', 'pattern_type_id');
    }
}
