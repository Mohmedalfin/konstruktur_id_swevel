<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table            = 'purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_number', 
        'supplier_id', 
        'total_nilai', 
        'status', 
        'estimasi_tanggal'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPOsWithSupplier()
    {
        return $this->select('purchase_orders.*, suppliers.nama_supplier')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->orderBy('purchase_orders.created_at', 'DESC')
                    ->findAll();
    }
    
    public function getPOWithDetails($id)
    {
        $po = $this->select('purchase_orders.*, suppliers.nama_supplier')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                   ->where('purchase_orders.id', $id)
                   ->first();
                   
        if ($po) {
            $itemModel = new PurchaseOrderItemModel();
            $po['items'] = $itemModel->getItemsByPO($id);
        }
        
        return $po;
    }
}
