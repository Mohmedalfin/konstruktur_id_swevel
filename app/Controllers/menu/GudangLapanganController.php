<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Models\ProyekModel;
use App\Services\ProjectInventoryService;


class GudangLapanganController extends BaseController
{
    protected $proyekModel;
    protected $inventoryService;

    public function __construct()
    {
        $this->proyekModel      = new ProyekModel();
        $this->inventoryService = new ProjectInventoryService();
    }

    public function index(string $slug)
    {
        $project = $this->proyekModel->where('slug', $slug)->first();
        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Proyek tidak ditemukan.');
        }

        $idProject = (int) $project['id_project'];

        // Simpan slug ke session agar navbar bisa menampilkan link Gudang Lapangan
        session()->set('current_project_slug', $slug);

        return view('proyek/menu/menu-gudang-lapangan', [
            'slug'      => $slug,
            'idProject' => $idProject,
            'proyek'    => $project,
        ]);
    }

    public function getStok()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);
            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi'
                ]);
            }

            $stok = $this->inventoryService->getStokProyek($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $stok
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[GudangLapanganController::getStok] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server'
            ]);
        }
    }

    public function getSisaStok($idProject)
    {
        try {
            $idProject = (int)$idProject;
            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project tidak valid'
                ]);
            }

            // Dapatkan stok proyek
            $stok = $this->inventoryService->getStokProyek($idProject);

            // Filter hanya yang memiliki stok_aktual > 0
            $sisaStok = array_filter($stok, function($item) {
                return (float)$item['stok_aktual'] > 0;
            });

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => array_values($sisaStok)
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[GudangLapanganController::getSisaStok] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server'
            ]);
        }
    }



    public function getKartu()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);
            $idBarang  = (int) ($this->request->getGet('id_barang') ?? 0);
            $limit     = min((int) ($this->request->getGet('limit') ?? 100), 500);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi'
                ]);
            }

            if ($idBarang > 0) {
                $data = $this->inventoryService->getKartuByBarang($idProject, $idBarang);
            } else {
                $data = $this->inventoryService->getKartuStok($idProject, $limit);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[GudangLapanganController::getKartu] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server'
            ]);
        }
    }

    public function retur()
    {
        try {
            $body      = $this->request->getJSON(true) ?? $this->request->getPost();
            $idProject = (int) ($body['id_project'] ?? 0);
            $idBarang  = (int) ($body['id_barang']  ?? 0);
            $jumlah    = (float) ($body['jumlah']   ?? 0);
            $keterangan = trim($body['keterangan']  ?? 'Retur material sisa ke gudang');

            if ($idProject <= 0 || $idBarang <= 0 || $jumlah <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project, id_barang, dan jumlah wajib diisi dengan nilai valid'
                ]);
            }

            // Cek stok tersedia
            $stokSekarang = $this->inventoryService->getStokBarang($idProject, $idBarang);
            if ($jumlah > $stokSekarang) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => 'error',
                    'message' => "Jumlah retur ({$jumlah}) melebihi stok lapangan yang tersedia ({$stokSekarang})"
                ]);
            }

            // Dapatkan id_perusahaan dari proyek
            $db = \Config\Database::connect();
            $project = $db->table('projects')->where('id_project', $idProject)->get()->getRowArray();
            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Proyek tidak ditemukan'
                ]);
            }
            $idPerusahaan = (int) $project['id_pengguna'];

            $this->inventoryService->returKeCentral(
                idProject:    $idProject,
                idBarang:     $idBarang,
                idPerusahaan: $idPerusahaan,
                jumlah:       $jumlah,
                keterangan:   $keterangan
            );

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Retur {$jumlah} unit berhasil dikembalikan ke Gudang Central"
            ]);

        } catch (\RuntimeException $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[GudangLapanganController::retur] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server'
            ]);
        }
    }
}
