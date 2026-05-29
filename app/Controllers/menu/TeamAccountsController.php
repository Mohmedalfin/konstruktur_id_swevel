<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\ProfileService;

class TeamAccountsController extends BaseController
{
    protected $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    public function index()
    {
        $id_pengguna = session()->get('id_pengguna') ?? session()->get('id_user');

        if (!$id_pengguna) {
            return view('proyek/menu/kelola-akun');
        }

        $profile = $this->profileService->getDetailProfile((int) $id_pengguna);
        $isSubAccount = is_object($profile) && !empty($profile->parent_id);

        if ($isSubAccount) {
            return redirect()->to(base_url('profile'))->with('error', 'Akun tim tidak memiliki akses untuk menambah akun.');
        }

        return view('proyek/menu/kelola-akun');
    }

    public function getSubAccounts()
    {
        $id_pengguna = session()->get('id_pengguna') ?? session()->get('id_user');

        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $profile = $this->profileService->getDetailProfile((int) $id_pengguna);
        $isSubAccount = is_object($profile) && !empty($profile->parent_id);

        if ($isSubAccount) {
            return $this->response->setJSON(['success' => false, 'message' => 'Forbidden'])->setStatusCode(403);
        }

        $items = $this->profileService->getSubAccounts((int) $id_pengguna);

        return $this->response->setJSON([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function createSubAccount()
    {
        $id_pengguna = session()->get('id_pengguna') ?? session()->get('id_user');

        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $profile = $this->profileService->getDetailProfile((int) $id_pengguna);
        $isSubAccount = is_object($profile) && !empty($profile->parent_id);

        if ($isSubAccount) {
            return $this->response->setJSON(['success' => false, 'message' => 'Forbidden'])->setStatusCode(403);
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

    public function deleteSubAccount(int $subAccountId)
    {
        $id_pengguna = session()->get('id_pengguna') ?? session()->get('id_user');

        if (!$id_pengguna) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $profile = $this->profileService->getDetailProfile((int) $id_pengguna);
        $isSubAccount = is_object($profile) && !empty($profile->parent_id);

        if ($isSubAccount) {
            return $this->response->setJSON(['success' => false, 'message' => 'Forbidden'])->setStatusCode(403);
        }

        $result = $this->profileService->deleteSubAccount((int) $id_pengguna, $subAccountId);

        if ($result['success']) {
            return $this->response->setJSON($result);
        }

        return $this->response->setJSON($result)->setStatusCode(400);
    }
}
