<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomorCustomToRap extends Migration
{
    public function up()
    {
        $this->forge->addColumn('rap_detail', [
            'nomor_custom' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addColumn('rap_kategori', [
            'nomor_custom' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('rap_detail', 'nomor_custom');
        $this->forge->dropColumn('rap_kategori', 'nomor_custom');
    }
}
