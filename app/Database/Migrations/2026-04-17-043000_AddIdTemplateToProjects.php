<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdTemplateToProjects extends Migration
{
    public function up()
    {
        $this->forge->addColumn('projects', [
            'id_template' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
                'after'   => 'lokasi_proyek',
                'comment' => 'ID template harga resmi dari DB estimator (tabel template_proyek)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('projects', 'id_template');
    }
}
