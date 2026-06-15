<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePermintaanTables extends Migration
{
    public function up()
    {
        // 1. Add jenis_item to permintaan_detail
        $fields = [
            'jenis_item' => [
                'type'       => 'ENUM',
                'constraint' => ['Bahan', 'Alat', 'Upah'],
                'default'    => 'Bahan',
                'null'       => false,
                'after'      => 'nama_barang',
            ]
        ];
        $this->forge->addColumn('permintaan_detail', $fields);

        // 2. Create permintaan_status_log table
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
                'unsigned'   => true, // must match permintaan.id (not unsigned in original migration, wait)
                'null'       => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'diubah_oleh' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        // Wait, check the original CreatePermintaanTables.php.
        // In 2026-06-04-025900_CreatePermintaanTables.php, permintaan.id was:
        // 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true
        // So unsigned is true. That's correct.
        
        // Add foreign key
        $this->forge->addForeignKey('id_permintaan', 'permintaan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('permintaan_status_log');
    }

    public function down()
    {
        $this->forge->dropTable('permintaan_status_log');
        $this->forge->dropColumn('permintaan_detail', 'jenis_item');
    }
}
