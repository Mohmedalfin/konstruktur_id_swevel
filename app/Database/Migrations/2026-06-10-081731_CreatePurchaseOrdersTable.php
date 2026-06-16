<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseOrdersTable extends Migration
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
            'po_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'supplier_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_nilai' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diproses', 'dalam pengiriman', 'selesai tiba'],
                'default'    => 'diproses',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'estimasi_tanggal' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_orders');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders');
    }
}
