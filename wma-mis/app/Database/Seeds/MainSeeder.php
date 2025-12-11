<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        echo "Running main seeder...\n";

        // Run migrations first if needed
        echo "Checking migrations...\n";

        // Run the Metrological seeder
        echo "Running Metrological seeder...\n";
        $this->call('MetrologicalSeeder');

        // Run Port and Berth seeders
        echo "Running Port seeder...\n";
        $this->call('PortSeeder');

        echo "Running Berth seeder...\n";
        $this->call('BerthSeeder');

        echo "All seed data completed successfully!\n";
    }
}