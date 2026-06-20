<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'master_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_perusahaan',
        'kode_barang',
        'nama_barang',
        'merk',
        'spesifikasi',
        'satuan',
        'satuan_kemasan',
        'konversi_faktor',
        'jenis_item',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
