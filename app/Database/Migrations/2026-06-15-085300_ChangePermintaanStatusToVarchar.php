<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangePermintaanStatusToVarchar extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'name'       => 'status',
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'draft',
                'null'       => false,
            ]
        ];
        $this->forge->modifyColumn('permintaan', $fields);
    }

    public function down()
    {
        $fields = [
            'status' => [
                'name'       => 'status',
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending', 'disetujui', 'ditolak', 'selesai'],
                'default'    => 'draft',
                'null'       => false,
            ]
        ];
        $this->forge->modifyColumn('permintaan', $fields);
    }
}
