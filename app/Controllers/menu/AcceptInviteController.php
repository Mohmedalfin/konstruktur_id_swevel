<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Models\UndanganPenggunaModel;
use App\Models\PenggunaModel;

class AcceptInviteController extends BaseController
{
    /**
     * Tampilan form setup akun dari link undangan
     */
    public function index()
    {
        $token = $this->request->getGet('token');

        if (empty($token)) {
            return view('errors/html/error_404', [
                'message' => 'Token undangan tidak ditemukan.'
            ]);
        }

        $undanganModel = new UndanganPenggunaModel();
        $invitation = $undanganModel->getActiveInvitation($token);

        if (!$invitation) {
            // Tampilan error premium / kedaluwarsa
            return view('auth/accept_invite_expired', [
                'token' => $token
            ]);
        }

        // Ambil data admin pengundang untuk mempercantik UI
        $penggunaModel = new PenggunaModel();
        $admin = $penggunaModel->find($invitation->parent_id);

        return view('auth/accept_invite', [
            'invitation' => $invitation,
            'admin'      => $admin,
            'token'      => $token
        ]);
    }

    /**
     * Proses pendaftaran/setup akun anggota tim baru
     */
    public function submit()
    {
        $token = $this->request->getPost('token');

        if (empty($token)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token tidak valid'
            ])->setStatusCode(400);
        }

        $undanganModel = new UndanganPenggunaModel();
        $invitation = $undanganModel->getActiveInvitation($token);

        if (!$invitation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Undangan tidak ditemukan, sudah kedaluwarsa, atau sudah digunakan.'
            ])->setStatusCode(400);
        }

        $nama = trim((string) $this->request->getPost('nama_pengguna'));
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($nama === '' || $username === '' || $password === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama, username, dan password wajib diisi.'
            ])->setStatusCode(400);
        }

        $penggunaModel = new PenggunaModel();

        // Validasi keunikan username
        $existingUsername = $penggunaModel->where('username', $username)->first();
        if ($existingUsername) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Username ini sudah terdaftar. Silakan pilih username lain.'
            ])->setStatusCode(400);
        }

        // Ambil data admin pengundang untuk mewarisi identitas perusahaan
        $admin = $penggunaModel->find($invitation->parent_id);

        $userData = [
            'nama_pengguna' => $nama,
            'username'      => $username,
            'email'         => $invitation->email,
            'password'      => $password, // Akan di-hash otomatis SHA-256 oleh PenggunaModel::hashPassword
            'kategori_akun' => strtolower($invitation->kategori_akun), // Gudang / Purchasing
            'parent_id'     => $invitation->parent_id,
            'status'        => '1',
            'perusahaan'    => $admin->perusahaan ?? null,
            'profil'        => $admin->profil ?? null,
            'alamat'        => $admin->alamat ?? null,
            'id_wilayah'    => $admin->id_wilayah ?? null,
            'no_telp'       => $admin->no_telp ?? null,
            'no_wa'         => $admin->no_wa ?? null,
            'website'       => $admin->website ?? null,
            'foto'          => $admin->foto ?? null,
            'tgl_daftar'    => date('Y-m-d'),
            'jam_daftar'    => date('H:i:s'),
        ];

        try {
            // Transaksi database agar aman
            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Simpan user baru ke tabel pengguna
            $userId = $penggunaModel->insert($userData, true);

            if (!$userId) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat akun.',
                    'errors'  => $penggunaModel->errors()
                ])->setStatusCode(400);
            }

            // 2. Tandai undangan sudah accepted
            $undanganModel->update($invitation->id, [
                'status' => 'accepted'
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses database.'
                ])->setStatusCode(500);
            }

            // 3. Kembalikan response sukses tanpa login otomatis agar tidak menimpa session admin penguji
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Akun tim berhasil diaktifkan!',
                'role'    => esc(ucfirst($invitation->kategori_akun)),
                'email'   => esc($invitation->email)
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[AcceptInviteController::submit] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan internal pada server.'
            ])->setStatusCode(500);
        }
    }
}
