<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

class DbTool extends Controller
{
    public function index()
    {
        $db = Database::connect();
        $forge = Database::forge();

        echo "<h2>Database Update Tool</h2>";

        // Add capacity_unit to form_d_requests
        $table = 'form_d_requests';
        $column = 'capacity_unit';
        
        if ($db->tableExists($table)) {
            $fields = $db->getFieldNames($table);
            if (!in_array($column, $fields)) {
                $sql = "ALTER TABLE `$table` ADD COLUMN `$column` VARCHAR(50) NULL DEFAULT NULL AFTER `capacity`";
                if ($db->query($sql)) {
                    echo "<p style='color:green'>Added column <strong>$column</strong> to <strong>$table</strong>.</p>";
                } else {
                    echo "<p style='color:red'>Failed to add column $column.</p>";
                }
            } else {
                echo "<p style='color:blue'>Column <strong>$column</strong> already exists in <strong>$table</strong>.</p>";
            }
        } else {
            echo "<p style='color:red'>Table $table does not exist.</p>";
        }
    }
}
