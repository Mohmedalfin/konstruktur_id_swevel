<?php

namespace App\Models;

use CodeIgniter\Model;

class StokGudangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stok_gudang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_perusahaan',
        'id_barang',
        'stok_aktual',
        'stok_minimum',
        'harga_rata_rata',
        'lokasi',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
