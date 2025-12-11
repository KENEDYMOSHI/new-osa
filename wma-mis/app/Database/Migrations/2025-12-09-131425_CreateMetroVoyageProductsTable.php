<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetroVoyageProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'voyageProductId' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'voyageId' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'productId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'loadPortDensityAtFifteen' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'loadPortWCFTAtFifteen' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'loadPortDensityAtTwenty' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'loadPortWCFTAtTwenty' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'tbsDensityAtFifteen' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'tbsWCFTAtFifteen' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'tbsDensityAtTwenty' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'tbsWCFTAtTwenty' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
            ],
            'primaryLine' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'secondaryLine' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updatedAt' => [
                'type' => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('voyageProductId');
        $this->forge->addForeignKey('voyageId', 'metro_voyage', 'voyageId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('productId', 'metro_products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('metro_voyageProducts');
    }

    public function down()
    {
        $this->forge->dropTable('metro_voyageProducts');
    }
}
