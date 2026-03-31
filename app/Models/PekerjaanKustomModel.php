<?php

namespace App\Models;

use CodeIgniter\Model;

class PekerjaanKustomModel extends Model
{
    protected $DBGroup          = 'default';           // DB lokal (database_konstraktor_id)
    protected $table            = 'pekerjaan';
    protected $primaryKey       = 'id_pekerjaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kategori_pekerjaan',
        'id_proyek',
        'kode_pekerjaan',
        'nama_pekerjaan',
        'satuan',
        'sumber',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama_pekerjaan' => 'required|min_length[2]|max_length[255]',
        'id_proyek'      => 'required|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
