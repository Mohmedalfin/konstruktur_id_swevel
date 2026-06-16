<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStokTerpotongToPermintaan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('permintaan', [
            'stok_terpotong' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'status'
            ]
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('stok_terpotong', 'permintaan')) {
            $this->forge->dropColumn('permintaan', 'stok_terpotong');
        }
    }
}
