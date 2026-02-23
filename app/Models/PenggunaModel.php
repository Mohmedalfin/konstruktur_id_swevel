<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table            = 'pengguna';
    protected $primaryKey       = 'id_pengguna';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'nama_pengguna', 'profil', 'alamat', 'id_wilayah', 'perusahaan', 
        'email', 'no_telp', 'no_wa', 'website', 'harga_min', 'harga_max', 
        'nego', 'username', 'password', 'foto', 'kategori_akun', 
        'status', 'kode_verifikasi', 'status_verifikasi', 'tgl_daftar', 'jam_daftar'
    ];

    // Fungsi untuk memproses data sebelum masuk ke database
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) return $data;

        // Hash password menggunakan SHA-256 sesuai logic sistem kamu
        $data['data']['password'] = hash('sha256', $data['data']['password']);
        return $data;
    }
}