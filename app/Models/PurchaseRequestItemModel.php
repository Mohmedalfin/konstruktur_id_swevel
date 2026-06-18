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
        'volume', 
        'status', 
        'po_id',
        'keterangan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    public function getItemsByPR($prId)
    {
        return $this->select('purchase_request_items.*, master_barang.nama_barang as nama_material, master_barang.satuan, master_barang.spesifikasi, purchase_orders.po_number')
                    ->join('master_barang', 'master_barang.id = purchase_request_items.id_barang')
                    ->join('purchase_orders', 'purchase_orders.id = purchase_request_items.po_id', 'left')
                    ->where('pr_id', $prId)
                    ->findAll();
    }
}
