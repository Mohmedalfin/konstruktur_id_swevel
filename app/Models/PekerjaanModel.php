<?php

namespace App\Models;

use CodeIgniter\Model;

class PekerjaanModel extends Model
{
    protected $DBGroup          = 'estimator';
    protected $table            = 'pekerjaan_utama';
    protected $primaryKey       = 'id_pekerjaan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'urut',
        'id_pekerjaan',
        'id_kategori_pekerjaan',
        'nama_kategori_pekerjaan',
        'nama_pekerjaan',
        'satuan',
        'sumber',
        'keterangan',
    ];

    protected $useTimestamps = false;
}