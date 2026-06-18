<?php

namespace App\Controllers\gudang;

use App\Controllers\BaseController;
use App\Services\PengadaanService;
use App\Services\NotificationService;
use CodeIgniter\HTTP\ResponseInterface;

class PengadaanController extends BaseController
{
    protected $pengadaanService;

    public function __construct()
    {
        $this->pengadaanService = new PengadaanService();
    }

    private function getIdPerusahaan(): int
    {
        $userId = session()->get('id_pengguna') ?? session()->get('id_user');
        if (!$userId) return 0;
        
        $db = \Config\Database::connect();
        $user = $db->table('pengguna')->where('id_pengguna', $userId)->get()->getRow();
        
        if ($user && !empty($user->parent_id)) {
            return (int)$user->parent_id;
        }
        return (int)$userId;
    }

    public function getStats(): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }
            
            $stats = $this->pengadaanService->getStats($idPerusahaan);
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $stats,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PengadaanController::getStats] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data statistik pengadaan.',
            ]);
        }
    }

    public function getData(): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }
            
            $status = $this->request->getGet('status') ?? 'all';
            $search = $this->request->getGet('search') ?? '';
            
            $data = $this->pengadaanService->getPurchaseRequestList($idPerusahaan, $status, $search);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PengadaanController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data pengadaan.',
            ]);
        }
    }

    public function getDetail(int $id): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }

            $detail = $this->pengadaanService->getPurchaseRequestDetail($id, $idPerusahaan);

            if (!$detail) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data pengadaan tidak ditemukan.',
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $detail,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PengadaanController::getDetail] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil detail pengadaan.',
            ]);
        }
    }

    public function getItemsKritis(): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }

            $data = $this->pengadaanService->getItemsKritis($idPerusahaan);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PengadaanController::getItemsKritis] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data barang kritis.',
            ]);
        }
    }

    public function searchBarang(): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }

            $keyword = $this->request->getGet('q') ?? '';
            $data = $this->pengadaanService->searchBarang($idPerusahaan, $keyword);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PengadaanController::searchBarang] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mencari barang.',
            ]);
        }
    }

    public function store(): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            $userId = session()->get('id_pengguna') ?? session()->get('id_user');

            if (!$idPerusahaan || !$userId) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }

            $payload = $this->request->getJSON(true);
            $items = $payload['items'] ?? [];
            $keterangan = $payload['keterangan'] ?? '';

            if (empty($items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Daftar barang tidak boleh kosong.',
                ]);
            }

            $result = $this->pengadaanService->createManualPurchaseRequest($idPerusahaan, $userId, $items, $keterangan);

            // === TRIGGER NOTIFIKASI: Beritahu purchasing ada PR manual baru dari Gudang ===
            if (($result['status'] ?? '') === 'success') {
                try {
                    $nomorPR   = $result['pr_number'] ?? '-';
                    $buatOleh  = session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Tim Gudang';
                    $jumlahItem = count($items);

                    $notifService = new NotificationService();
                    $notifService->sendToRole(
                        'purchasing',
                        'Purchase Request Baru 📋',
                        "{$buatOleh} mengajukan PR {$nomorPR} dengan {$jumlahItem} item barang yang perlu diproses.",
                        '/gudang/pengadaan',
                        'fa-solid fa-file-invoice',
                        'blue',
                        'gudang'
                    );
                } catch (\Throwable $notifEx) {
                    log_message('warning', '[PengadaanController::store] Gagal kirim notifikasi: ' . $notifEx->getMessage());
                }
            }
            // === END TRIGGER NOTIFIKASI ===

            return $this->response->setJSON($result);
            
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[PengadaanController::store] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat membuat pengadaan.',
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }

            $result = $this->pengadaanService->deletePurchaseRequest($id, $idPerusahaan);
            return $this->response->setJSON($result);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            log_message('error', '[PengadaanController::delete] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus pengadaan.'
            ]);
        }
    }
}
