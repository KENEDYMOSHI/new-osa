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
        echo "<p>Tool executed successfully.</p>";
    }
}
