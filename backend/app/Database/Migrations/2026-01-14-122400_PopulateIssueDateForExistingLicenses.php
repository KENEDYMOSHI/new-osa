<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateIssueDateForExistingLicenses extends Migration
{
    public function up()
    {
        // Update existing licenses to set issue_date based on payment_date
        $this->db->query("
            UPDATE licenses 
            SET issue_date = payment_date 
            WHERE issue_date IS NULL 
              AND payment_date IS NOT NULL
        ");

        // For licenses without payment_date, use created_at date
        $this->db->query("
            UPDATE licenses 
            SET issue_date = DATE(created_at)
            WHERE issue_date IS NULL 
              AND created_at IS NOT NULL
        ");

        log_message('info', 'Populated issue_date for existing licenses');
    }

    public function down()
    {
        // Optionally clear the issue_date if rolling back
        // $this->db->query("UPDATE licenses SET issue_date = NULL");
    }
}
