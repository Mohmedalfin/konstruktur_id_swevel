<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdWilayahToProjects extends Migration
{
    public function up()
    {
        $this->forge->addColumn('projects', [
            'id_wilayah' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'null'       => true,
                'after'      => 'id_template',
                'comment'    => 'ID Wilayah dari database estimator',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('projects', 'id_wilayah');
    }
}
