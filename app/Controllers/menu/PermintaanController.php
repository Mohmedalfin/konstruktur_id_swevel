<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\PermintaanService;
use App\Services\NotificationService;
use CodeIgniter\HTTP\ResponseInterface;

class PermintaanController extends BaseController
{
    protected $permintaanService;

    public function __construct()
    {
        $this->permintaanService = new PermintaanService();
    }

    /**
     * GET /permintaan
     * Renders the Monitoring Page (Halaman Utama)
     */
    public function index()
    {
        $userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Kontraktor';
        
        $proyekModel = new \App\Models\ProyekModel();
        // Load active projects for dropdown
        $projects = $proyekModel->orderBy('nama_proyek', 'ASC')->findAll();

        return view('proyek/menu/menu-monitoring', [
            'title'       => 'Permintaan',
            'userRole'    => $userRole,
            'topbarTitle' => 'PERMINTAAN',
            'projects'    => $projects
        ]);
    }

    /**
     * GET /permintaan/create
     * Redirects to monitoring page and opens create modal
     */
    public function create()
    {
        return redirect()->to(base_url('permintaan'))->with('open_create_modal', true);
    }

    /**
     * GET /api/permintaan/stats
     * Return stats JSON
     */
    public function getStats(): ResponseInterface
    {
        try {
            $month = $this->request->getGet('month');
            $stats = $this->permintaanService->getStats($month);
            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $stats,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::getStats] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data statistik.',
            ]);
        }
    }

    /**
     * GET /api/permintaan/data
     * Return request list JSON with filter
     */
    public function getData(): ResponseInterface
    {
        try {
            $status = $this->request->getGet('status');
            $month = $this->request->getGet('month');
            $data = $this->permintaanService->getList($status, $month);

            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data permintaan.',
            ]);
        }
    }

    /**
     * GET /api/permintaan/detail/(:num)
     * Return single request detail with items grouped by project
     */
    public function getDetail(int $id): ResponseInterface
    {
        try {
            $data = $this->permintaanService->getDetail($id);
            if (!$data) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data permintaan tidak ditemukan.',
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::getDetail] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil detail permintaan.',
            ]);
        }
    }

    /**
     * POST /api/permintaan/store
     * Handles creating a new request
     */
    public function store(): ResponseInterface
    {
        try {
            $pemohonId = session()->get('id_pengguna') ?? session()->get('id_user');
            if (!$pemohonId) {
                // Fallback for development/testing if session is not active
                $pemohonId = 1;
            }

            $payload = $this->request->getJSON(true);
            if (empty($payload) || empty($payload['items'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data permintaan tidak lengkap.',
                ]);
            }

            $requestId = $this->permintaanService->storeRequest((int)$pemohonId, $payload);

            // === TRIGGER NOTIFIKASI: Beritahu divisi Gudang ada permintaan baru ===
            try {
                // Ambil info permintaan yang baru dibuat untuk detail notif
                $db = \Config\Database::connect();
                $permintaan = $db->table('permintaan')->where('id', $requestId)->get()->getRowArray();
                $pemohonNama = session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Tim Proyek';

                $jumlahItem = count($payload['items']);
                $nomorPermintaan = $permintaan['nomor_permintaan'] ?? 'REQ-' . $requestId;

                $notifService = new NotificationService();
                $notifService->sendToRole(
                    'gudang',
                    'Permintaan Material Baru',
                    "{$pemohonNama} mengajukan permintaan {$nomorPermintaan} dengan {$jumlahItem} item material.",
                    '/gudang/permintaan',
                    'fa-solid fa-box-open',
                    'blue',
                    'proyek'
                );
            } catch (\Throwable $notifEx) {
                log_message('warning', '[PermintaanController::store] Gagal kirim notifikasi: ' . $notifEx->getMessage());
            }
            // === END TRIGGER NOTIFIKASI ===

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Permintaan item berhasil dibuat.',
                'data'    => ['id' => $requestId]
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::store] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat menyimpan permintaan.',
            ]);
        }
    }

    /**
     * POST /api/permintaan/status/(:num)
     * Handles status changes (disetujui, ditolak, selesai)
     */
    public function updateStatus(int $id): ResponseInterface
    {
        try {
            // Check roles
            $userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Kontraktor';
            if (!in_array(strtolower($userRole), ['gudang', 'admin', 'purchasing'])) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk memproses permintaan ini.',
                ]);
            }

            $payload = $this->request->getJSON(true);
            $newStatus = $payload['status'] ?? '';

            if (empty($newStatus)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Status baru wajib ditentukan.',
                ]);
            }

            // Ambil info permintaan SEBELUM update untuk notifikasi ke pemohon
            $db = \Config\Database::connect();
            $permintaan = $db->table('permintaan p')
                ->select('p.*, u.nama_pengguna as pemohon_nama')
                ->join('pengguna u', 'u.id_pengguna = p.pemohon_id', 'left')
                ->where('p.id', $id)
                ->get()->getRowArray();

            $success = $this->permintaanService->updateStatus($id, $newStatus);

            if ($success) {
                // === TRIGGER NOTIFIKASI: Kirim update status ke pemohon ===
                try {
                    $nomorPermintaan = $permintaan['nomor_permintaan'] ?? 'REQ-' . $id;
                    $pemohonId       = $permintaan['pemohon_id'] ?? null;
                    $prosesOleh      = session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Tim Gudang';

                    $statusPesan = [
                        'disetujui' => ['judul' => 'Permintaan Disetujui ✅',    'pesan' => "{$nomorPermintaan} telah disetujui oleh {$prosesOleh}. Material sedang disiapkan.",         'ikon' => 'fa-solid fa-circle-check',  'warna' => 'green'],
                        'diproses'  => ['judul' => 'Permintaan Sedang Diproses 🔄', 'pesan' => "{$nomorPermintaan} sedang diproses oleh gudang. Stok material telah dipotong.",       'ikon' => 'fa-solid fa-rotate',        'warna' => 'orange'],
                        'ditolak'   => ['judul' => 'Permintaan Ditolak ❌',       'pesan' => "{$nomorPermintaan} ditolak oleh {$prosesOleh}. Silakan hubungi tim gudang untuk info lebih lanjut.", 'ikon' => 'fa-solid fa-circle-xmark',  'warna' => 'red'],
                        'selesai'   => ['judul' => 'Permintaan Selesai 🎉',       'pesan' => "{$nomorPermintaan} telah selesai dan material sudah diterima di lapangan.",             'ikon' => 'fa-solid fa-flag-checkered','warna' => 'green'],
                    ];

                    if (isset($statusPesan[$newStatus]) && $pemohonId) {
                        $info = $statusPesan[$newStatus];
                        $notifService = new NotificationService();
                        $notifService->sendToUser(
                            (int)$pemohonId,
                            $info['judul'],
                            $info['pesan'],
                            '/permintaan',
                            $info['ikon'],
                            $info['warna'],
                            'gudang'
                        );
                    }
                } catch (\Throwable $notifEx) {
                    log_message('warning', '[PermintaanController::updateStatus] Gagal kirim notifikasi: ' . $notifEx->getMessage());
                }
                // === END TRIGGER NOTIFIKASI ===

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Status permintaan berhasil diperbarui.',
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status permintaan.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::updateStatus] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat memperbarui status.',
            ]);
        }
    }

    /**
     * GET /api/permintaan/projects
     * API to fetch projects list
     */
    public function getProjects(): ResponseInterface
    {
        try {
            $proyekModel = new \App\Models\ProyekModel();
            $projects = $proyekModel->orderBy('nama_proyek', 'ASC')->findAll();

            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $projects,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::getProjects] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil daftar proyek.',
            ]);
        }
    }

    /**
     * GET /api/permintaan/rap-items/(:num)
     * API to fetch RAP items for project
     */
    public function getRapItems(int $projectId): ResponseInterface
    {
        try {
            $items = $this->permintaanService->getProjectRapItems($projectId);
            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $items,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::getRapItems] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil item anggaran proyek.',
            ]);
        }
    }

    /**
     * POST /api/permintaan/auto-procure/(:num)
     * Handles creating auto draft pengadaan for insufficient stock
     */
    public function autoProcure(int $id): ResponseInterface
    {
        try {
            $userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Gudang';
            if (!in_array(strtolower($userRole), ['gudang', 'admin', 'purchasing'])) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk memproses ini.',
                ]);
            }

            $userId = session()->get('id_pengguna') ?? session()->get('id_user') ?? 1;
            $pengadaanService = new \App\Services\PengadaanService();
            
            $result = $pengadaanService->createAutoDraftFromPermintaan($id, (int)$userId);

            // === TRIGGER NOTIFIKASI: Beritahu purchasing ada PR auto-draft baru ===
            if (($result['status'] ?? '') === 'success') {
                try {
                    $db = \Config\Database::connect();
                    $permintaan = $db->table('permintaan')->where('id', $id)->get()->getRowArray();
                    $nomorPermintaan = $permintaan['nomor_permintaan'] ?? 'REQ-' . $id;
                    $nomorPR = $result['pr_number'] ?? '-';
                    $prosesOleh = session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Tim Gudang';

                    $notifService = new NotificationService();
                    $notifService->sendToRole(
                        'purchasing',
                        'Purchase Request Baru (Auto) 📋',
                        "{$prosesOleh} membuat PR otomatis {$nomorPR} dari kekurangan stok permintaan {$nomorPermintaan}.",
                        '/gudang/pengadaan',
                        'fa-solid fa-cart-plus',
                        'purple',
                        'gudang'
                    );
                } catch (\Throwable $notifEx) {
                    log_message('warning', '[PermintaanController::autoProcure] Gagal kirim notifikasi: ' . $notifEx->getMessage());
                }
            }
            // === END TRIGGER NOTIFIKASI ===

            return $this->response->setJSON($result);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::autoProcure] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat memproses pengadaan otomatis.',
            ]);
        }
    }

    /**
     * DELETE /api/permintaan/delete/(:num)
     * Handles deleting a pending request
     */
    public function destroy(int $id): ResponseInterface
    {
        try {
            $success = $this->permintaanService->deleteRequest($id);

            if ($success) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Permintaan berhasil dibatalkan/dihapus.',
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus permintaan.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::destroy] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat menghapus permintaan.',
            ]);
        }
    }
    /**
     * GET /permintaan/deviasi
     * Renders the Deviasi Report Page
     */
    public function deviasi()
    {
        $userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Kontraktor';
        
        $proyekModel = new \App\Models\ProyekModel();
        $projects = $proyekModel->orderBy('nama_proyek', 'ASC')->findAll();

        return view('proyek/menu/deviasi', [
            'title'       => 'Laporan Deviasi & Margin',
            'userRole'    => $userRole,
            'topbarTitle' => 'Laporan Deviasi & Margin',
            'projects'    => $projects
        ]);
    }

    /**
     * GET /api/permintaan/deviasi
     * Return deviasi data JSON
     */
    public function getDeviasiData(): ResponseInterface
    {
        try {
            $idProject = $this->request->getGet('id_project');
            $month = $this->request->getGet('month');
            $data = $this->permintaanService->getDeviasiReport($idProject ? (int)$idProject : null, $month);

            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PermintaanController::getDeviasiData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data laporan deviasi.',
            ]);
        }
    }
}
