<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class AuthController extends BaseController
{
    public function loginProcess()
    {
        $session = session();
        $model = new PenggunaModel();

        $loginIdentifier = $this->request->getPost('username'); // bisa username atau email
        $password = $this->request->getPost('password');

        if (empty($loginIdentifier) || empty($password)) {
            $session->setFlashdata('error', 'Username/Email dan password harus diisi.');
            return redirect()->back()->withInput();
        }

        // Cek user berdasarkan username atau email
        $user = $model->where('username', $loginIdentifier)
                      ->orWhere('email', $loginIdentifier)
                      ->first();

        if ($user) {
            // Hash input password menggunakan logic yang sama dengan hashPassword di model
            $hashedPassword = hash('sha256', $password);

            if ($user->password === $hashedPassword) {
                // Tentukan id_perusahaan berdasarkan kategori akun
                // Jika kontraktor (owner), maka id_perusahaan = id_pengguna itu sendiri
                // Jika sub-akun (gudang/purchasing), id_perusahaan = parent_id
                $id_perusahaan = (strtolower((string)$user->kategori_akun) === 'kontraktor' || empty($user->parent_id)) 
                    ? $user->id_pengguna 
                    : $user->parent_id;

                // Set session data
                $ses_data = [
                    'id_pengguna'   => $user->id_pengguna,
                    'id_user'       => $user->id_pengguna,
                    'id_perusahaan' => $id_perusahaan,
                    'nama_pengguna' => $user->nama_pengguna,
                    'username'      => $user->username,
                    'kategori_akun' => strtolower((string)$user->kategori_akun),
                    'perusahaan'    => $user->perusahaan,
                    'logged_in'     => true,
                ];
                $session->set($ses_data);

                // Redirect sesuai kategori_akun
                $kategori = strtolower((string)$user->kategori_akun);
                if ($kategori === 'gudang') {
                    return redirect()->to(base_url('gudang/dashboard'));
                } elseif ($kategori === 'purchasing') {
                    return redirect()->to(base_url('purchasing/dashboard'));
                } else {
                    return redirect()->to(base_url('dashboard'));
                }
            } else {
                $session->setFlashdata('error', 'Password salah.');
                return redirect()->back()->withInput();
            }
        } else {
            $session->setFlashdata('error', 'Username atau Email tidak ditemukan.');
            return redirect()->back()->withInput();
        }
    }

    public function registerProcess()
    {
        $session = session();
        $model = new PenggunaModel();

        $rules = [
            'nama_pengguna' => 'required',
            'username'      => 'required|min_length[3]|is_unique[pengguna.username]',
            'email'         => 'required|valid_email|is_unique[pengguna.email]',
            'password'      => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('validation_errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $data = [
            'nama_pengguna' => $this->request->getPost('nama_pengguna'),
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password'      => $this->request->getPost('password'), // Di-hash otomatis di PenggunaModel::hashPassword sebelum insert
            'perusahaan'    => $this->request->getPost('nama_perusahaan'),
            'id_wilayah'    => $this->request->getPost('id_wilayah'),
            'no_wa'         => $this->request->getPost('no_hp'),
            'kategori_akun' => 'kontraktor', // Default untuk registrasi mandiri
            'status'        => '1', // Langsung aktif
            'tgl_daftar'    => date('Y-m-d'),
            'jam_daftar'    => date('H:i:s'),
        ];

        if ($model->insert($data)) {
            $session->setFlashdata('success', 'Registrasi berhasil! Silakan login.');
            return redirect()->to(base_url('login'));
        } else {
            $session->setFlashdata('error', 'Terjadi kesalahan saat menyimpan data.');
            return redirect()->back()->withInput();
        }
    }
}
