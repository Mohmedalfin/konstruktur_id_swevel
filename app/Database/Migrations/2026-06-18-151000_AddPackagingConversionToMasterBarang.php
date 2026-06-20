<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPackagingConversionToMasterBarang extends Migration
{
    public function up()
    {
        // Tambah kolom konversi satuan kemasan ke master_barang
        // Contoh: satuan = 'kg', satuan_kemasan = 'Sak', konversi_faktor = 50
        // Artinya: 1 Sak = 50 kg
        $this->forge->addColumn('master_barang', [
            'satuan_kemasan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'default'    => null,
                'after'      => 'satuan',
                'comment'    => 'Satuan kemasan pembelian (misal: Sak, Kaleng, Roll). NULL jika tidak ada konversi'
            ],
            'konversi_faktor' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => '1.0000',
                'null'       => false,
                'after'      => 'satuan_kemasan',
                'comment'    => 'Jumlah satuan dasar per 1 satuan kemasan (misal: 1 Sak = 50 kg, maka faktor = 50)'
            ]
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('satuan_kemasan', 'master_barang')) {
            $this->forge->dropColumn('master_barang', 'satuan_kemasan');
        }
        if ($this->db->fieldExists('konversi_faktor', 'master_barang')) {
            $this->forge->dropColumn('master_barang', 'konversi_faktor');
        }
    }
}
