<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedOtherPatternInstrument extends Migration
{
    public function up()
    {
        // 1. Insert 'Other Pattern Instrument' into pattern_types
        $this->db->table('pattern_types')->insert([
            'name'       => 'Other Pattern Instrument',
            'description'=> 'For instruments not listed in other categories',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $patternTypeId = $this->db->insertID();

        // 2. Insert 'Other Instruments' into instrument_categories
        $this->db->table('instrument_categories')->insert([
            'pattern_type_id' => $patternTypeId,
            'name'            => 'Other Instruments',
            'code'            => 'OTHER',
            'is_active'       => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        
        $categoryId = $this->db->insertID();

        // 3. Insert a generic 'Other Instrument' type so the category isn't empty
        $this->db->table('instrument_types')->insert([
            'category_id' => $categoryId,
            'name'        => 'Other Instrument',
            'code'        => 'OTH',
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        // Clean up (Reverse order)
        // Note: Using LIKE to be safe, though IDs would be better if we tracked them.
        
        // 1. Get ID for Other Pattern Instrument
        $builder = $this->db->table('pattern_types');
        $row = $builder->where('name', 'Other Pattern Instrument')->get()->getRow();
        
        if ($row) {
             // Deletion will cascade to categories and types due to FKs if set up that way, 
             // but let's be explicit or rely on FKs. 
             // Previous migration added FK with CASCADE, so deleting the pattern type should suffice.
             $builder->where('id', $row->id)->delete();
        }
    }
}
