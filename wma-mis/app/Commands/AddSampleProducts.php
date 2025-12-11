<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddSampleProducts extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'CodeIgniter';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'add:sample-products';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Add sample products with product types to the database';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'add:sample-products';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Check if products table exists
        if (!$db->tableExists('products')) {
            CLI::error('Products table does not exist. Please run migrations first.');
            return;
        }

        // Check existing products
        $products = $db->table('products')->get()->getResult();
        CLI::write("Found " . count($products) . " existing products:", 'yellow');

        foreach ($products as $product) {
            CLI::write("- {$product->name} (Type: {$product->product_type})");
        }

        // Add sample products if none exist or if forced
        if (count($products) == 0 || CLI::getOption('force')) {
            CLI::write("\nAdding sample products...", 'green');

            $sampleProducts = [
                ['name' => 'Gasoil (AGO) - Diesel', 'product_type' => 'Refined Product'],
                ['name' => 'Gasoline, PMS (MOGAS) - Petrol', 'product_type' => 'Refined Product'],
                ['name' => 'HFO - Heavy Fuel Oil', 'product_type' => 'Refined Product'],
                ['name' => 'Jet A1 & IK', 'product_type' => 'Refined Product'],
                ['name' => 'Baseoil (Lubricants)', 'product_type' => 'Refined Product'],
                ['name' => 'LPG-MIX & BUTTANE', 'product_type' => 'LPG'],
                ['name' => 'VEGOIL (CPO OLEIN, PFAD & RPS)', 'product_type' => 'Other'],
                ['name' => 'Crude Oil - Light', 'product_type' => 'Crude Oil'],
                ['name' => 'Crude Oil - Heavy', 'product_type' => 'Crude Oil'],
                ['name' => 'Chemical Product A', 'product_type' => 'Chemical'],
            ];

            foreach ($sampleProducts as $product) {
                $product['created_at'] = date('Y-m-d H:i:s');
                $product['updated_at'] = date('Y-m-d H:i:s');

                // Check if product already exists
                $existing = $db->table('products')->where('name', $product['name'])->get()->getRow();
                if (!$existing) {
                    $db->table('products')->insert($product);
                    CLI::write("Added: {$product['name']} (Type: {$product['product_type']})", 'green');
                } else {
                    CLI::write("Skipped (exists): {$product['name']}", 'yellow');
                }
            }

            CLI::write("\nSample products processing completed!", 'green');
        } else {
            CLI::write("\nProducts already exist. Use --force to add anyway.", 'yellow');
        }
    }
}
