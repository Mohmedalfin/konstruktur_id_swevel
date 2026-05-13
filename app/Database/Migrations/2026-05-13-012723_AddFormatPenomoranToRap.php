<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFormatPenomoranToRap extends Migration
{
    public function up()
    {
        $fields = [
            'format_penomoran' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'keterangan'
            ],
        ];
        $this->forge->addColumn('rap', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('rap', 'format_penomoran');
    }
}
