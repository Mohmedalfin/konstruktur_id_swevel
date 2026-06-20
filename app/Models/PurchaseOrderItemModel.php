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
        'id_barang', 
        'volume', 
        'harga_satuan', 
        'sub_total'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getItemsByPO($poId)
    {
        return $this->select('purchase_order_items.*, master_barang.nama_barang as nama_material, master_barang.satuan, master_barang.satuan_kemasan, master_barang.spesifikasi, master_barang.konversi_faktor')
                    ->join('master_barang', 'master_barang.id = purchase_order_items.id_barang')
                    ->where('po_id', $poId)
                    ->findAll();
    }
}
