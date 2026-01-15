<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateFeeTypeInOsabill extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Update all records where bill_type = 1 to set fee_type = 'Application Fee'
        $db->table('osabill')
           ->where('bill_type', 1)
           ->update(['fee_type' => 'Application Fee']);
        
        // Update all records where bill_type = 2 to set fee_type = 'License Fee'
        $db->table('osabill')
           ->where('bill_type', 2)
           ->update(['fee_type' => 'License Fee']);
           
        log_message('info', 'Populated fee_type for existing osabill records');
    }

    public function down()
    {
        // Set all fee_type values back to NULL
        $db = \Config\Database::connect();
        $db->table('osabill')->update(['fee_type' => null]);
    }
}
