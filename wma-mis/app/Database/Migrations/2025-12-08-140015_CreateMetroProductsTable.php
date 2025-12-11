<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetroProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'productName' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'createdAt' => [
                'type'    => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updatedAt' => [
                'type'    => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('metro_products');
    }

    public function down()
    {
        $this->forge->dropTable('metro_products');
    }
}
