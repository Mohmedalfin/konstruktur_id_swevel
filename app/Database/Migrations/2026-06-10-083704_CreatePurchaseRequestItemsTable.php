<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseRequestItemsTable extends Migration
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
            'pr_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'material_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'volume' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'ordered'],
                'default'    => 'pending',
            ],
            'po_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addForeignKey('pr_id', 'purchase_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('material_id', 'materials', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('purchase_request_items');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_request_items');
    }
}
