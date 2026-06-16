<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pr_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'request_date' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diproses', 'parsial', 'selesai'],
                'default'    => 'diproses',
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
        $this->forge->createTable('purchase_requests');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_requests');
    }
}
