<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\ProfileService;

class ProfileController extends BaseController
{
    protected $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    /**
     * Halaman View Utama untuk Profile
     */
    public function index()
    {
        return view('proyek/menu/profile-perusahaan');
    }

    /**
     * API Endpoint: Mendapatkan data profile JSON
     */
    public function getData()
    {
        $id_pengguna = session()->get('id_pengguna') ?? session()->get('id_user');
        
        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $profile = $this->profileService->getDetailProfile($id_pengguna);

        if ($profile) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $profile
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan'])->setStatusCode(404);
    }

    /**
     * API Endpoint: Update data profile
     */
    public function update()
    {
        $id_pengguna = session()->get('id_pengguna') ?? session()->get('id_user');
        
        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $data = $this->request->getPost();
        
        $foto = $this->request->getFile('foto');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $result = $this->profileService->updateProfile($id_pengguna, $data, $foto);

        if ($result['success']) {
            return $this->response->setJSON($result);
        } else {
            return $this->response->setJSON($result)->setStatusCode(400);
        }
    }

    /**
     * API Endpoint: List sub-account (Gudang/Purchasing) milik akun utama
     */
    public function getSubAccounts()
    {
        $id_pengguna = session()->get('id_pengguna');

        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $items = $this->profileService->getSubAccounts((int) $id_pengguna);

        return $this->response->setJSON([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * API Endpoint: Buat sub-account untuk Gudang/Purchasing
     */
    public function createSubAccount()
    {
        $id_pengguna = session()->get('id_pengguna');

        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $payload = [
            'nama_pengguna' => trim((string) $this->request->getPost('nama_pengguna')),
            'username'      => trim((string) $this->request->getPost('username')),
            'email'         => trim((string) $this->request->getPost('email')),
            'password'      => (string) $this->request->getPost('password'),
            'kategori_akun' => trim((string) $this->request->getPost('kategori_akun')),
        ];

        $result = $this->profileService->createSubAccount((int) $id_pengguna, $payload);

        if ($result['success']) {
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON($result)->setStatusCode(400);
    }
}
