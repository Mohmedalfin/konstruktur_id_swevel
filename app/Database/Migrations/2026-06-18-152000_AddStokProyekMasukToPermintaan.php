<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStokProyekMasukToPermintaan extends Migration
{
    public function up()
    {
        // Flag: apakah barang dari permintaan ini sudah masuk ke stok_proyek (lapangan)
        // Diperlukan oleh PermintaanService untuk idempotency saat status = 'selesai'
        $this->forge->addColumn('permintaan', [
            'stok_proyek_masuk' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'stok_terpotong',
                'comment'    => '0 = belum masuk ke stok lapangan proyek, 1 = sudah masuk'
            ]
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('stok_proyek_masuk', 'permintaan')) {
            $this->forge->dropColumn('permintaan', 'stok_proyek_masuk');
        }
    }
}
