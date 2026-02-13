<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStickerNumberToRegistryTable extends Migration
{
    public function up()
    {
        $fields = [
            'sticker_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'instrument_type'
            ]
        ];
        $this->forge->addColumn('technicians_customer_registry', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('technicians_customer_registry', 'sticker_number');
    }
}
