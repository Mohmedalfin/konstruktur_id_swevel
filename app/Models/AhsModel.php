<?php

namespace App\Models;

use CodeIgniter\Model;

class AhsModel extends Model
{
    protected $DBGroup = 'estimator';
    protected $table = 'ahs';
    protected $primaryKey = 'id_ahs';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;

    protected $allowedFields = [
        'id_ahs',
        'id_proyek',
        'id_pelaksana',
        'id_pekerjaan',
        'id_kategori',
        'id_kategori_pekerjaan',
        'id_pekerjaan_duplikat',
        'nama_kategori_pekerjaan',
        'nama_pekerjaan',
        'satuan_pekerjaan',
        'kategori',
        'koefisien',
        'nama_kategori',
        'satuan_kategori',
        'spesifikasi',
        'merk',
        'tahun_kategori',
        'sumber_kategori',
        'keterangan_kategori',
        'harga_dasar',
        'tahun',
        'sumber',
        'keterangan',
        'tgl_dibuat',
        'jam_dibuat',
    ];

    protected $useTimestamps = false;
}