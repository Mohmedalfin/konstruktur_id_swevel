<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPurchasingTablesForMasterBarang extends Migration
{
    public function up()
    {
        // 1. material_supplier
        if ($this->db->tableExists('material_supplier')) {
            $this->forge->modifyColumn('material_supplier', [
                'material_id' => [
                    'name' => 'id_barang',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
        }

        // 2. purchase_requests
        if ($this->db->tableExists('purchase_requests')) {
            $this->forge->addColumn('purchase_requests', [
                'id_perusahaan' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'after' => 'id'
                ],
                'keterangan' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'status'
                ],
                'created_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'keterangan'
                ],
            ]);
        }

        // 3. purchase_request_items
        if ($this->db->tableExists('purchase_request_items')) {
            $this->forge->modifyColumn('purchase_request_items', [
                'material_id' => [
                    'name' => 'id_barang',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
            $this->forge->addColumn('purchase_request_items', [
                'keterangan' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'status'
                ],
            ]);
        }

        // 4. purchase_orders
        if ($this->db->tableExists('purchase_orders')) {
            $this->forge->addColumn('purchase_orders', [
                'id_perusahaan' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'after' => 'id'
                ],
                'created_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'status'
                ],
            ]);
        }

        // 5. purchase_order_items
        if ($this->db->tableExists('purchase_order_items')) {
            $this->forge->modifyColumn('purchase_order_items', [
                'material_id' => [
                    'name' => 'id_barang',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        // 1. material_supplier
        if ($this->db->tableExists('material_supplier')) {
            $this->forge->modifyColumn('material_supplier', [
                'id_barang' => [
                    'name' => 'material_id',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
        }

        // 2. purchase_requests
        if ($this->db->tableExists('purchase_requests')) {
            $this->forge->dropColumn('purchase_requests', ['id_perusahaan', 'keterangan', 'created_by']);
        }

        // 3. purchase_request_items
        if ($this->db->tableExists('purchase_request_items')) {
            $this->forge->modifyColumn('purchase_request_items', [
                'id_barang' => [
                    'name' => 'material_id',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
            $this->forge->dropColumn('purchase_request_items', 'keterangan');
        }

        // 4. purchase_orders
        if ($this->db->tableExists('purchase_orders')) {
            $this->forge->dropColumn('purchase_orders', ['id_perusahaan', 'created_by']);
        }

        // 5. purchase_order_items
        if ($this->db->tableExists('purchase_order_items')) {
            $this->forge->modifyColumn('purchase_order_items', [
                'id_barang' => [
                    'name' => 'material_id',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
            ]);
        }
    }
}
