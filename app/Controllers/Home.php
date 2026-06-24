<?php

namespace App\Controllers;

use App\Models\RoleModel; // Import modelnya

class Home extends BaseController
{
    
    
    public function index()
    {
        return view('landing/index');
    }

    public function login()
    {
        return view('auth/loginUser');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }

    public function bypassAdmin()
    {
        $db = \Config\Database::connect();
        
        // Cari user admin pertama (id_pengguna = 1)
        $admin = $db->table('pengguna')->where('id_pengguna', 1)->get()->getRow();

        if ($admin) {
            $id_perusahaan = (strtolower($admin->kategori_akun) === 'kontraktor' || empty($admin->parent_id)) 
                ? $admin->id_pengguna 
                : $admin->parent_id;

            session()->set([
                'id_pengguna'   => $admin->id_pengguna,
                'id_user'       => $admin->id_pengguna,
                'id_perusahaan' => $id_perusahaan,
                'nama_pengguna' => $admin->nama_pengguna,
                'username'      => $admin->username,
                'kategori_akun' => strtolower($admin->kategori_akun), // e.g. kontraktor
                'logged_in'     => true,
            ]);
            
            return redirect()->to(base_url('dashboard'))->with('msg', 'Bypass login berhasil! Masuk sebagai Admin.');
        }

        return 'Gagal bypass: User Admin dengan ID 1 tidak ditemukan di database. Silakan jalankan seeder terlebih dahulu.';
    }
    
    private function getAllCities()
    {
        try {
            $db = \Config\Database::connect('estimator');
            return $db->query("SELECT id_wilayah AS id, wilayah AS nama FROM wilayah WHERE kategori = '2' ORDER BY wilayah ASC")->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function register()
    {
        return view('auth/registerUser', ['cities' => $this->getAllCities()]);
    }
}