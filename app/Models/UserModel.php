<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // Sesuai struktur DB yang diberikan user, + nama_lengkap
    protected $allowedFields    = [
        'kode_user', 
        'nama_lengkap',
        'email', 
        'no_hp', 
        'nama_perusahaan', 
        'domisili_perusahaan',
        'alamat_proyek', 
        'posisi_pekerjaan', 
        'password',
    ];

    // Dates
    protected $useTimestamps = false; // Ubah ke true jika punya kolom created_at / updated_at

    // Validation
    protected $validationRules      = [
        'email'            => 'required|valid_email|is_unique[users.email]',
        'nama_lengkap'     => 'required',
        'no_hp'            => 'required|min_length[10]|max_length[15]',
        'nama_perusahaan'  => 'required',
        'domisili_perusahaan' => 'required',
        'alamat_proyek' => 'required',
        'posisi_pekerjaan' => 'required',
        'password'         => 'required|min_length[8]'
    ];
    
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.'
        ],
        'password' => [
            'min_length' => 'Password minimal harus 8 karakter.'
        ]
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }

        return $data;
    }
}
