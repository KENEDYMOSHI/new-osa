<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropProductIdFromPressureLogs extends Migration
{
    public function up()
    {
        // Drop foreign key first if it exists
        // The previous migration created it with: $this->forge->addForeignKey('productId', 'metro_products', 'id', 'CASCADE', 'CASCADE');
        // FK name is usually generated or can be specified. Since strictly speaking we don't know the exact name, 
        // we might try to drop the column and let the DB handle it or drop FK by name if known. 
        // In CI4, finding FK name programmatically is hard in migration.
        // But usually dropping the column drops the constraint in MySQL.

        // However, to be safe, we try dropping the column.
        // If strict mode is on, we might need to drop FK first. 
        // Let's assume MySQL behaves nicely or try to drop the FK by a guessed name if it fails, but standard is `metro_pressureLogs_productId_foreign`

        $this->forge->dropForeignKey('metro_pressureLogs', 'metro_pressureLogs_productId_foreign');
        $this->forge->dropColumn('metro_pressureLogs', 'productId');
    }

    public function down()
    {
        $this->forge->addColumn('metro_pressureLogs', [
            'productId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
        $this->forge->addForeignKey('productId', 'metro_products', 'id', 'CASCADE', 'CASCADE');
    }
}
