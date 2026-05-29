<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table            = 'pengguna';
    protected $primaryKey       = 'id_pengguna';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'nama_pengguna', 'profil', 'alamat', 'id_wilayah', 'perusahaan', 
        'email', 'no_telp', 'no_wa', 'website', 'username', 'password', 'foto', 'kategori_akun', 
        'parent_id', 'status', 'kode_verifikasi', 'tgl_daftar', 'jam_daftar'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pengguna' => 'permit_empty',
        'email'    => 'permit_empty|valid_email|is_unique[pengguna.email,id_pengguna,{id_pengguna}]',
        'username' => 'permit_empty|alpha_numeric_space|min_length[3]|is_unique[pengguna.username,id_pengguna,{id_pengguna}]',
        'no_wa'    => 'permit_empty|numeric|min_length[10]|max_length[15]'
    ];
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email ini sudah terdaftar.',
            'valid_email' => 'Format email tidak valid.'
        ],
        'username' => [
            'is_unique' => 'Username ini sudah terdaftar.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    /**
     * Hash password sebelum insert/update
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) return $data;

        // Hash password menggunakan SHA-256 sesuai logic sistem yang ada
        $data['data']['password'] = hash('sha256', $data['data']['password']);
        return $data;
    }

    /**
     * Mengambil data profile pengguna secara aman
     * (Menghapus field sensitif seperti password sebelum di-return)
     * 
     * @param int $id_pengguna
     * @return object|null
     */
    public function getProfileData(int $id_pengguna)
    {
        $profile = $this->where('id_pengguna', $id_pengguna)->first();
        
        if ($profile) {
            // Hapus password agar tidak bocor ke UI/API
            unset($profile->password);
            
            // Format URL foto jika diperlukan
            if (!empty($profile->foto)) {
                $profile->foto_url = base_url($profile->foto);
            } else {
                // Set default avatar jika tidak ada foto
                $profile->foto_url = base_url('assets/images/default-avatar.png');
            }
        }
        
        return $profile;
    }
}