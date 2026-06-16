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
    protected $allowedFields    = ['material_id', 'supplier_id', 'harga'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getHargaWithDetails()
    {
        return $this->select('material_supplier.id, material_supplier.harga, materials.nama_material, materials.kategori, materials.satuan, materials.spesifikasi, suppliers.nama_supplier, materials.id as material_id, suppliers.id as supplier_id')
                    ->join('materials', 'materials.id = material_supplier.material_id')
                    ->join('suppliers', 'suppliers.id = material_supplier.supplier_id')
                    ->orderBy('material_supplier.id', 'DESC')
                    ->findAll();
    }
}
