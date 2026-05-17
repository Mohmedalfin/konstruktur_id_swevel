<?php

namespace App\Models;

use CodeIgniter\Model;

class RealisasiPekerjaanModel extends Model
{
    protected $table            = 'realisasi_pekerjaan';
    protected $primaryKey       = 'id_realisasi_pekerjaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_rap_detail',
        'tanggal',
        'volume_tercapai',
        'keterangan',
        'foto',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
