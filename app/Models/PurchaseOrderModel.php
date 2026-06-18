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
        'id_perusahaan',
        'po_number', 
        'supplier_id', 
        'total_nilai', 
        'status', 
        'estimasi_tanggal',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPOsWithSupplier($id_perusahaan = null)
    {
        $builder = $this->select('purchase_orders.*, suppliers.nama_supplier')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id');
                    
        if ($id_perusahaan !== null) {
            $builder->where('purchase_orders.id_perusahaan', $id_perusahaan);
        }
        
        return $builder->orderBy('purchase_orders.created_at', 'DESC')
                    ->findAll();
    }
    
    public function getPOWithDetails($id, $id_perusahaan = null)
    {
        $builder = $this->select('purchase_orders.*, suppliers.nama_supplier')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                   ->where('purchase_orders.id', $id);
                   
        if ($id_perusahaan !== null) {
            $builder->where('purchase_orders.id_perusahaan', $id_perusahaan);
        }
        
        $po = $builder->first();
                   
        if ($po) {
            $itemModel = new PurchaseOrderItemModel();
            $po['items'] = $itemModel->getItemsByPO($id);
        }
        
        return $po;
    }
}
