<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PenggunaModel;

class PenggunaSeeder extends Seeder
{
    public function run()
    {
        $model = new PenggunaModel();

        $data = [
            'nama_pengguna'   => 'Ahmad Hidayat',
            'profil'          => 'Kontraktor profesional dengan pengalaman lebih dari 8 tahun dalam pembangunan perumahan dan renovasi gedung komersial skala menengah.',
            'alamat'          => 'Jl. Kemang Raya No. 12, Mampang Prapatan, Jakarta Selatan',
            'id_wilayah'      => 3174,
            'perusahaan'      => 'PT Sinergi Bangun Semesta',
            'email'           => 'ahmad.hidayat@konstruktor.id',
            'no_telp'         => '081234567890',
            'no_wa'           => '081234567890',
            'website'         => 'https://sinergibangunsemesta.com',
            'username'        => 'ahmadkontraktor',
            'password'        => 'Password123!', // Password plain text, model PenggunaModel akan otomatis melakukan hashing SHA-256
            'foto'            => 'uploads/profiles/default.png',
            'kategori_akun'   => 'Kontraktor',
            'status'          => '1',
            'tgl_daftar'      => date('Y-m-d'),
            'jam_daftar'      => date('H:i:s'),
        ];

        // Lakukan insert data
        if ($model->insert($data)) {
            echo "Seeding sukses! Berhasil menambahkan 1 pengguna random.\n";
            echo "Username: " . $data['username'] . "\n";
            echo "Password: " . $data['password'] . "\n";
        } else {
            echo "Seeding gagal! Detail error:\n";
            print_r($model->errors());
        }
    }
}
