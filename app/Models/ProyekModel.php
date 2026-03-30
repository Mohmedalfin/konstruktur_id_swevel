<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyekModel extends Model
{
    protected $table            = 'proyek';
    protected $primaryKey       = 'id_proyek';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'id_user', 'kode_proyek', 'slug', 'nama_proyek', 'lokasi_proyek', 
        'jenis_proyek', 'tgl_mulai', 'tgl_selesai', 'nama_owner_klien', 
        'perusahaan', 'nomor_kontrak', 'keterangan_lain', 
        'foto_project', 'file_pendukung', 'status'
    ];

    // Dates
    protected $useTimestamps = false; // Setel ke true jika tabel memiliki kolom created_at dan updated_at
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
