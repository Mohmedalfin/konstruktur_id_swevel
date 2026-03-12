<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyekModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'projects';
    protected $primaryKey       = 'id_project';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_proyek',
        'nama_proyek',
        'lokasi_proyek',
        'tanggal_mulai',
        'estimasi_selesai',
        'nama_owner',
        'nama_perusahaan',
        'nomor_kontrak',
        'keterangan',
        'foto_proyek',
        'harga_deal',
        'jenis_proyek',
        'sumber_data',
        'id_ref_sumber',
        'status_proyek',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
