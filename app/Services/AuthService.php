<?php

namespace App\Services;

use App\Models\UserModel;
use Exception;

class AuthService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Pindahkan logika Register ke sini
    public function registerUser(array $inputData)
    {
        // Generate kode unik
        $kodeUser = 'USR-' . strtoupper(bin2hex(random_bytes(3))) . date('is');

        // Mapping data
        $data = [
            'kode_user'           => $kodeUser,
            'nama_lengkap'        => $inputData['nama_lengkap'],
            'email'               => $inputData['email'],
            'no_hp'               => $inputData['no_hp'],
            'nama_perusahaan'     => $inputData['nama_perusahaan'],
            'domisili_perusahaan' => $inputData['domisili'],
            'alamat_proyek'       => $inputData['alamat'],
            'posisi_pekerjaan'    => $inputData['posisi'],
            'password'            => $inputData['password'], // Asumsi di-hash di UserModel
        ];

        // Eksekusi insert
        if (!$this->userModel->insert($data)) {
            // Lempar error model agar ditangkap oleh Controller
            throw new Exception(implode(', ', $this->userModel->errors()));
        }

        return true;
    }

    // Pindahkan logika Login ke sini
    public function attemptLogin(string $email, string $password)
    {
        $user = $this->userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Email atau Password yang Anda masukkan salah.');
        }

        // Set session di sini atau return user data ke Controller
        session()->regenerate(); 
        session()->set([
            'isLoggedIn'      => true,
            'id_user'         => $user['id_user'],
            'kode_user'       => $user['kode_user'],
            'nama_lengkap'    => $user['nama_lengkap'],
            'nama_perusahaan' => $user['nama_perusahaan'],
            'posisi_pekerjaan'=> $user['posisi_pekerjaan']
        ]);

        return true;
    }
}