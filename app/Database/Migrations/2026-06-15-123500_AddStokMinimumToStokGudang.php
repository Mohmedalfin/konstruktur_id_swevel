<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStokMinimumToStokGudang extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stok_gudang', [
            'stok_minimum' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => '0.0000',
                'null'       => false,
                'after'      => 'stok_aktual'
            ]
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('stok_minimum', 'stok_gudang')) {
            $this->forge->dropColumn('stok_gudang', 'stok_minimum');
        }
    }
}
