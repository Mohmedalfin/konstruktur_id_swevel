<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialSupplierModel extends Model
{
    protected $table            = 'material_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_barang', 'supplier_id', 'harga'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getHargaWithDetails($id_perusahaan = null)
    {
        $builder = $this->select('material_supplier.id, material_supplier.harga, master_barang.nama_barang as nama_material, master_barang.jenis_item as kategori, master_barang.satuan, master_barang.satuan_kemasan, master_barang.spesifikasi, suppliers.nama_supplier, master_barang.id as id_barang, suppliers.id as supplier_id')
                    ->join('master_barang', 'master_barang.id = material_supplier.id_barang')
                    ->join('suppliers', 'suppliers.id = material_supplier.supplier_id');
                    
        if ($id_perusahaan !== null) {
            $builder->where('master_barang.id_perusahaan', $id_perusahaan);
        }
        
        return $builder->orderBy('material_supplier.id', 'DESC')
                    ->findAll();
    }
}
