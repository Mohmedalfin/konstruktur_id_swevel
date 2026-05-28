<?php

namespace App\Models;

use CodeIgniter\Model;

class RapKategoriModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'rap_kategori';
    protected $primaryKey = 'id_rap_kategori';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;

    protected $allowedFields = [
        'id_rap',
        'id_kategori',
        'nomor_custom',
    ];

    protected $useTimestamps = false;
}