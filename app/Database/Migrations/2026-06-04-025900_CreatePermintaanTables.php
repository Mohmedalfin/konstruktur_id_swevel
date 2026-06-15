<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermintaanTables extends Migration
{
    public function up()
    {
        // 1. Table: permintaan (Header)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_permintaan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'tanggal_permintaan' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'pemohon_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending', 'disetujui', 'ditolak', 'selesai'],
                'default'    => 'draft',
                'null'       => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('nomor_permintaan');
        $this->forge->createTable('permintaan');

        // 2. Table: permintaan_detail (Detail)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_permintaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_project' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_rap_detail_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'null'       => false,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('id_permintaan', 'permintaan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('permintaan_detail');
    }

    public function down()
    {
        $this->forge->dropTable('permintaan_detail');
        $this->forge->dropTable('permintaan');
    }
}
