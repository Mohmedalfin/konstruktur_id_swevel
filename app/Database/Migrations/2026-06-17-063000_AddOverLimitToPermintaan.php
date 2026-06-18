<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOverLimitToPermintaan extends Migration
{
    public function up()
    {
        // Add columns to permintaan table
        $this->forge->addColumn('permintaan', [
            'is_over_limit' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'status'
            ],
            'justifikasi_over_limit' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'is_over_limit'
            ]
        ]);

        // Add columns to permintaan_detail table
        $this->forge->addColumn('permintaan_detail', [
            'is_over_limit' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'satuan'
            ],
            'jumlah_over_limit' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,4',
                'default'    => '0.0000',
                'null'       => false,
                'after'      => 'is_over_limit'
            ]
        ]);
    }

    public function down()
    {
        // Remove columns from permintaan table
        if ($this->db->fieldExists('justifikasi_over_limit', 'permintaan')) {
            $this->forge->dropColumn('permintaan', 'justifikasi_over_limit');
        }
        if ($this->db->fieldExists('is_over_limit', 'permintaan')) {
            $this->forge->dropColumn('permintaan', 'is_over_limit');
        }

        // Remove columns from permintaan_detail table
        if ($this->db->fieldExists('jumlah_over_limit', 'permintaan_detail')) {
            $this->forge->dropColumn('permintaan_detail', 'jumlah_over_limit');
        }
        if ($this->db->fieldExists('is_over_limit', 'permintaan_detail')) {
            $this->forge->dropColumn('permintaan_detail', 'is_over_limit');
        }
    }
}
