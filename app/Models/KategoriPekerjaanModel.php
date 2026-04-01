<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriPekerjaanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'kategori_pekerjaan';
    protected $primaryKey       = 'id_kategori_pekerjaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'nama_kategori',
        'slug',
        'id_project',
        'id_user',
        'jenis_kategori',
    ];

    protected $useTimestamps = false;
}