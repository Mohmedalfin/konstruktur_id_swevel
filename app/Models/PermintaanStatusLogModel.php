<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanStatusLogModel extends Model
{
    protected $table            = 'permintaan_status_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'id_permintaan',
        'status',
        'diubah_oleh',
        'keterangan',
        'created_at'
    ];

    protected $useTimestamps = false;
}
