<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanBillDescriptionInOsabill extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Get all bills
        $bills = $db->table('osabill')->get()->getResult();
        
        foreach ($bills as $bill) {
            $description = $bill->bill_description;
            
            // Remove various fee type patterns from description
            $cleanDescription = $description;
            
            // Remove " - Application Fee", " - License Fee", etc.
            $cleanDescription = preg_replace('/ - (Application Fee|License Fee)$/i', '', $cleanDescription);
            
            // Remove "Application Fee - ", "License Fee - ", etc. at the beginning
            $cleanDescription = preg_replace('/^(Application Fee|License Fee) - /i', '', $cleanDescription);
            
            // Remove standalone "Application Fee" or "License Fee" if that's all there is
            if (preg_match('/^(Application Fee|License Fee)$/i', $cleanDescription)) {
                // If it's ONLY "Application Fee" or "License Fee", we need to find the actual license name
                // This might be in the items JSON or we leave it as is
                // For now, let's check items
                $items = json_decode($bill->items, true);
                if (!empty($items) && isset($items[0]['name'])) {
                    $cleanDescription = $items[0]['name'];
                } elseif (!empty($items) && isset($items[0]['itemName'])) {
                    $cleanDescription = $items[0]['itemName'];
                }
            }
            
            // Update if changed
            if ($cleanDescription !== $description) {
                $db->table('osabill')
                   ->where('id', $bill->id)
                   ->update(['bill_description' => trim($cleanDescription)]);
            }
        }
        
        log_message('info', 'Cleaned bill_description for all osabill records');
    }

    public function down()
    {
        // No rollback - we can't restore the original descriptions
        log_message('info', 'No rollback for bill_description cleanup');
    }
}
