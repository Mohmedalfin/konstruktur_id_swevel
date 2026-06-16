<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderItemModel extends Model
{
    protected $table            = 'purchase_order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_id', 
        'material_id', 
        'volume', 
        'harga_satuan', 
        'sub_total'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getItemsByPO($poId)
    {
        return $this->select('purchase_order_items.*, materials.nama_material, materials.satuan, materials.spesifikasi')
                    ->join('materials', 'materials.id = purchase_order_items.material_id')
                    ->where('po_id', $poId)
                    ->findAll();
    }
}
