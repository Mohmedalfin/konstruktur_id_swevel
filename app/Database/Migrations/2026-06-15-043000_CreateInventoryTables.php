<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTables extends Migration
{
    public function up()
    {
        // 1. Table: master_barang
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_perusahaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => false,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => false,
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'Tanpa Merk',
                'null'       => false,
            ],
            'spesifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'default'    => '-',
                'null'       => false,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'jenis_item' => [
                'type'       => 'ENUM',
                'constraint' => ['Bahan', 'Alat'],
                'null'       => false,
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
        $this->forge->addUniqueKey(['id_perusahaan', 'kode_barang']);
        $this->forge->createTable('master_barang');

        // 2. Table: stok_gudang
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_perusahaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'stok_aktual' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => '0.0000',
                'null'       => false,
            ],
            'harga_rata_rata' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => '0.0000',
                'null'       => false,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'Gudang Utama',
                'null'       => false,
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
        $this->forge->addForeignKey('id_barang', 'master_barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stok_gudang');

        // 3. Add column id_barang to rap_detail_item
        $this->forge->addColumn('rap_detail_item', [
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_rap_detail'
            ]
        ]);

        // 4. Add column id_barang to permintaan_detail
        $this->forge->addColumn('permintaan_detail', [
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_rap_detail_item'
            ]
        ]);
    }

    public function down()
    {
        // Drop column from permintaan_detail
        if ($this->db->fieldExists('id_barang', 'permintaan_detail')) {
            $this->forge->dropColumn('permintaan_detail', 'id_barang');
        }

        // Drop column from rap_detail_item
        if ($this->db->fieldExists('id_barang', 'rap_detail_item')) {
            $this->forge->dropColumn('rap_detail_item', 'id_barang');
        }

        // Drop tables
        $this->forge->dropTable('stok_gudang', true);
        $this->forge->dropTable('master_barang', true);
    }
}
