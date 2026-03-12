<?php

namespace App\Models;

use CodeIgniter\Model;

class RapDetailItemModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rap_detail_item';
    protected $primaryKey       = 'id_rap_detail_item';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_rap_detail',
        'jenis_item',
        'nama_item',
        'koefisien',
        'satuan',
        'harga_dasar',
        'harga_satuan',
        'merk',
        'spesifikasi',
        'urutan',
        'keterangan',
    ];

    protected $useTimestamps = false;
}