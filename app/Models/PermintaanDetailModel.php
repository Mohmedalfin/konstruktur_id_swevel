<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanDetailModel extends Model
{
    protected $table            = 'permintaan_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_permintaan',
        'id_project',
        'id_rap_detail_item',
        'id_barang',
        'nama_barang',
        'jumlah',
        'satuan',
        'is_over_limit',
        'jumlah_over_limit',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
