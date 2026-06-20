<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteInventoryTables extends Migration
{
    public function up()
    {
        // 1. Table: stok_proyek (Persediaan fisik di lokasi proyek)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_project' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'FK ke tabel projects'
            ],
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'FK ke master_barang'
            ],
            'stok_aktual' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => '0.0000',
                'null'       => false,
                'comment'    => 'Jumlah stok fisik saat ini di lapangan proyek'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_project', 'id_barang']);
        $this->forge->addForeignKey('id_barang', 'master_barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stok_proyek');

        // 2. Table: kartu_stok_proyek (Log kronologis mutasi stok di lapangan proyek)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_project' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'FK ke tabel projects'
            ],
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'FK ke master_barang'
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar'],
                'null'       => false,
                'comment'    => 'masuk = dari gudang central / retur balik proyek lain, keluar = pemakaian / retur ke central'
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'null'       => false,
            ],
            'sisa_stok' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'null'       => false,
                'comment'    => 'Sisa stok setelah transaksi ini (snapshot saldo berjalan)'
            ],
            'sumber' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'comment'    => 'permintaan | pemakaian | retur_ke_central | mutasi_masuk | mutasi_keluar | batal_permintaan'
            ],
            'id_sumber' => [
                'type'    => 'INT',
                'null'    => true,
                'comment' => 'ID dari tabel sumber terkait (id permintaan, id realisasi sdm, dll)'
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_project', 'id_barang']);
        $this->forge->addForeignKey('id_barang', 'master_barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kartu_stok_proyek');
    }

    public function down()
    {
        $this->forge->dropTable('kartu_stok_proyek', true);
        $this->forge->dropTable('stok_proyek', true);
    }
}
