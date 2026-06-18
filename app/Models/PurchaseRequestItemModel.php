<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseRequestItemModel extends Model
{
    protected $table            = 'purchase_request_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pr_id',
        'id_barang',
        'material_id',
        'volume',
        'status',
        'keterangan',
        'po_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    public function getItemsByPR($prId)
    {
        return $this->select('purchase_request_items.*, materials.nama_material, materials.satuan, materials.spesifikasi, purchase_orders.po_number')
                    ->join('materials', 'materials.id = purchase_request_items.material_id')
                    ->join('purchase_orders', 'purchase_orders.id = purchase_request_items.po_id', 'left')
                    ->where('pr_id', $prId)
                    ->findAll();
    }
}
