<?php

namespace App\Models;

use CodeIgniter\Model;

class RealisasiSdmModel extends Model
{
    protected $table            = 'realisasi_sdm';
    protected $primaryKey       = 'id_realisasi_sdm';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_project',
        'tanggal',
        'keterangan',
        'dokumentasi',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_project' => 'required|is_natural_no_zero',
        'tanggal'    => 'required|valid_date',
    ];

    protected $validationMessages = [
        'id_project' => [
            'required'           => 'ID Proyek wajib diisi.',
            'is_natural_no_zero' => 'ID Proyek tidak valid.'
        ],
        'tanggal' => [
            'required'   => 'Tanggal wajib diisi.',
            'valid_date' => 'Format tanggal tidak valid.'
        ]
    ];
}
