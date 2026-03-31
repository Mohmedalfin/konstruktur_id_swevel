<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriPekerjaanModel extends Model
{
    protected $table            = 'kategori_pekerjaan';
    protected $primaryKey       = 'id_kategori_pekerjaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'id_proyek',
        'kode_kategori',
        'nama_kategori'
    ];

    // Disable timestamps since there's no evidence of created_at / updated_at in the user's schema query
    protected $useTimestamps = false;
}
