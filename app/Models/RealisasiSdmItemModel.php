<?php

namespace App\Models;

use CodeIgniter\Model;

class RealisasiSdmItemModel extends Model
{
    protected $table            = 'realisasi_sdm_item';
    protected $primaryKey       = 'id_realisasi_sdm_item';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_realisasi_sdm',
        'kategori',
        'nama_item',
        'qty',
        'harga_satuan',
        'satuan',
        'spesifikasi',
        'merk',
        'keterangan',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_realisasi_sdm' => 'required|is_natural_no_zero',
        'kategori'         => 'required|in_list[Bahan,Alat,Tenaga Kerja]',
        'nama_item'        => 'required|min_length[2]',
        'qty'              => 'required|numeric|greater_than[0]',
        'satuan'           => 'required',
    ];

    protected $validationMessages = [
        'id_realisasi_sdm' => [
            'required'           => 'ID Realisasi SDM wajib diisi.',
            'is_natural_no_zero' => 'ID Realisasi SDM tidak valid.'
        ],
        'kategori' => [
            'required' => 'Kategori wajib diisi.',
            'in_list'  => 'Kategori harus berupa Bahan, Alat, atau Tenaga Kerja.'
        ],
        'nama_item' => [
            'required'   => 'Nama item wajib diisi.',
            'min_length' => 'Nama item terlalu pendek.'
        ],
        'qty' => [
            'required'     => 'Kuantitas wajib diisi.',
            'numeric'      => 'Kuantitas harus berupa angka.',
            'greater_than' => 'Kuantitas harus lebih dari 0.'
        ],
        'satuan' => [
            'required' => 'Satuan wajib diisi.'
        ]
    ];
}
