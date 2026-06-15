<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomorCustomToRap extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('nomor_custom', 'rap_detail')) {
            $this->forge->addColumn('rap_detail', [
                'nomor_custom' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'default' => null,
                ],
            ]);
        }

        if (!$this->db->fieldExists('nomor_custom', 'rap_kategori')) {
            $this->forge->addColumn('rap_kategori', [
                'nomor_custom' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'default' => null,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('nomor_custom', 'rap_detail')) {
            $this->forge->dropColumn('rap_detail', 'nomor_custom');
        }
        if ($this->db->fieldExists('nomor_custom', 'rap_kategori')) {
            $this->forge->dropColumn('rap_kategori', 'nomor_custom');
        }
    }
}
