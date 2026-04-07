<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToRapDetail extends Migration
{
    public function up()
    {
        $fields = [
            'id_parent' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_kategori',
            ],
        ];
        $this->forge->addColumn('rap_detail', $fields);

        // Add index for performance
        $this->db->query("CREATE INDEX idx_rap_detail_parent ON rap_detail (id_parent)");
    }

    public function down()
    {
        $this->forge->dropColumn('rap_detail', 'id_parent');
    }
}
