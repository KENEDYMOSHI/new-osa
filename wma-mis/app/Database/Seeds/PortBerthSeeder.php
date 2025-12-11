<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Combined seeder for Ports and Berths
 */
class PortBerthSeeder extends Seeder
{
    public function run()
    {
        echo "Running Port and Berth seeders...\n";

        // Run Port seeder first
        echo "Running Port seeder...\n";
        $this->call('PortSeeder');

        // Then run Berth seeder
        echo "Running Berth seeder...\n";
        $this->call('BerthSeeder');

        echo "Port and Berth seed data completed successfully!\n";
    }
}