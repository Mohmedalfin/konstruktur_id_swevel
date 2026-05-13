<?php

namespace App\Models;

use CodeIgniter\Model;

class RapModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rap';
    protected $primaryKey       = 'id_rap';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_project',
        'nama_rap',
        'subtotal_bahan',
        'subtotal_upah',
        'subtotal_alat',
        'total_keseluruhan',
        'status_rap',
        'keterangan',
        'format_penomoran',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}