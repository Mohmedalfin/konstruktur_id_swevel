<?php

namespace App\Services;

use App\Models\PenggunaModel;

class ProfileService
{
    protected $penggunaModel;
    protected const SUBACCOUNT_PARENT_PREFIX = 'PARENT:';
    protected const ALLOWED_SUBACCOUNT_ROLES = ['Gudang', 'Purchasing'];

    public function __construct()
    {
        $this->penggunaModel = new PenggunaModel();
    }

    public function getDetailProfile(int $id_pengguna)
    {
        return $this->penggunaModel->getProfileData($id_pengguna);
    }

    /**
     * Memperbarui data profil pengguna, termasuk menangani upload foto baru.
     * 
     * @param int $id_pengguna
     * @param array $data Data teks dari form
     * @param mixed $foto File upload object (bisa null)
     * @return array Status sukses atau pesan error
     */
    public function updateProfile(int $id_pengguna, array $data, $foto = null)
    {
        $user = $this->penggunaModel->find($id_pengguna);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan'];
        }

        $data['id_pengguna'] = $id_pengguna;

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $foto->move($uploadPath, $newName);
            
            $data['foto'] = 'uploads/profile/' . $newName;

            if (!empty($user->foto) && file_exists(FCPATH . $user->foto) && strpos($user->foto, 'default-avatar') === false) {
                @unlink(FCPATH . $user->foto);
            }
        }

        if ($this->penggunaModel->update($id_pengguna, $data)) {
            return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
        } else {
            return [
                'success' => false, 
                'message' => 'Gagal memperbarui profil. Pastikan data valid.', 
                'errors' => $this->penggunaModel->errors()
            ];
        }
    }

    /**
     * Mengambil daftar sub-account milik akun utama.
     */
    public function getSubAccounts(int $id_pengguna): array
    {
        $items = $this->penggunaModel
            ->where('parent_id', $id_pengguna)
            ->orderBy('id_pengguna', 'DESC')
            ->findAll();

        $safe = [];
        foreach ($items as $item) {
            if (is_object($item) && property_exists($item, 'password')) {
                unset($item->password);
            }
            $safe[] = $item;
        }

        return $safe;
    }

    /**
     * Membuat sub-account baru untuk Gudang/Purchasing.
     */
    public function createSubAccount(int $parentId, array $payload): array
    {
        $nama = trim((string) ($payload['nama_pengguna'] ?? ''));
        $username = trim((string) ($payload['username'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $password = (string) ($payload['password'] ?? '');
        $role = trim((string) ($payload['kategori_akun'] ?? ''));

        if ($nama === '' || $username === '' || $password === '' || $role === '') {
            return ['success' => false, 'message' => 'Nama, username, password, dan role wajib diisi'];
        }

        if (!in_array($role, self::ALLOWED_SUBACCOUNT_ROLES, true)) {
            return ['success' => false, 'message' => 'Role tidak valid'];
        }

        $parent = $this->penggunaModel->find($parentId);
        if (!$parent) {
            return ['success' => false, 'message' => 'Akun utama tidak ditemukan'];
        }

        $data = [
            'nama_pengguna' => $nama,
            'username'      => $username,
            'email'         => $email === '' ? null : $email,
            'password'      => $password,
            'kategori_akun' => $role,
            'parent_id'     => $parentId,
            'status'        => '1',
            'perusahaan'    => $parent->perusahaan ?? null,
            'profil'        => $parent->profil ?? null,
            'alamat'        => $parent->alamat ?? null,
            'id_wilayah'    => $parent->id_wilayah ?? null,
            'no_telp'       => $parent->no_telp ?? null,
            'no_wa'         => $parent->no_wa ?? null,
            'website'       => $parent->website ?? null,
            'foto'          => $parent->foto ?? null,
            'tgl_daftar'    => date('Y-m-d'),
            'jam_daftar'    => date('H:i:s'),
        ];

        try {
            $insertId = $this->penggunaModel->insert($data, true);

            if (!$insertId) {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat akun',
                    'errors'  => $this->penggunaModel->errors(),
                ];
            }

            return ['success' => true, 'message' => 'Akun berhasil ditambahkan', 'id_pengguna' => $insertId];
        } catch (\Throwable $e) {
            log_message('error', '[ProfileService::createSubAccount] ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan internal pada server'];
        }
    }

    /**
     * Menghapus sub-account milik akun utama.
     */
    public function deleteSubAccount(int $parentId, int $subAccountId): array
    {
        $sub = $this->penggunaModel->find($subAccountId);

        if (!$sub || (int)$sub->parent_id !== $parentId) {
            return ['success' => false, 'message' => 'Akun tim tidak ditemukan atau Anda tidak memiliki akses'];
        }

        if ($this->penggunaModel->delete($subAccountId)) {
            return ['success' => true, 'message' => 'Akun tim berhasil dihapus'];
        }

        return ['success' => false, 'message' => 'Gagal menghapus akun tim'];
    }
}
