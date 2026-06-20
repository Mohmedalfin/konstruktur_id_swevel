<?php

namespace App\Controllers\gudang;

use App\Controllers\BaseController;
use App\Services\StokService;
use CodeIgniter\HTTP\ResponseInterface;

class StokController extends BaseController
{
    protected $stokService;

    public function __construct()
    {
        $this->stokService = new StokService();
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
            
            $stats = $this->stokService->getStats($idPerusahaan);
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $stats,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[StokController::getStats] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data statistik stok.',
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
            
            $kategori = $this->request->getGet('kategori') ?? 'all';
            $status   = $this->request->getGet('status') ?? 'all';
            $search   = $this->request->getGet('search') ?? '';
            
            $data = $this->stokService->getStockList($idPerusahaan, $kategori, $status, $search);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[StokController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data stok.',
            ]);
        }
    }

    public function updateMinimum(): ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }

            $json = $this->request->getJSON(true);
            $idBarang = isset($json['id_barang']) ? (int) $json['id_barang'] : 0;
            $stokMinimum = isset($json['stok_minimum']) ? (float) $json['stok_minimum'] : -1;
            $satuan = isset($json['satuan']) ? trim($json['satuan']) : '';
            $satuanKemasan = isset($json['satuan_kemasan']) ? trim($json['satuan_kemasan']) : null;
            $konversiFaktor = isset($json['konversi_faktor']) && $json['konversi_faktor'] !== '' ? (float) $json['konversi_faktor'] : 1.0000;

            if ($idBarang <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data id_barang tidak valid.'
                ]);
            }

            $updated = $this->stokService->updateMinimumStock($idPerusahaan, $idBarang, $stokMinimum, $satuan, $satuanKemasan, $konversiFaktor);

            if ($updated) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Detail barang berhasil diperbarui.'
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memperbarui batas minimum stok.'
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[StokController::updateMinimum] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server saat memperbarui batas minimum.'
            ]);
        }
    }
}
