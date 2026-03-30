<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalPekerjaanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pekerjaan_utama';
    protected $primaryKey       = 'id_pekerjaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'nama_pekerjaan',
        'satuan',
        'keterangan',
        'id_project',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field for now
}
