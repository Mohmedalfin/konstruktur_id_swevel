<?php

namespace App\Models;

use CodeIgniter\Model;

class RapModel extends Model
{
    protected $table         = 'rap';
    protected $primaryKey    = 'id_rap';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_proyek', 'id_kategori_pekerjaan', 'id_pekerjaan',
        'nama_pekerjaan', 'satuan', 'volume', 'urutan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
