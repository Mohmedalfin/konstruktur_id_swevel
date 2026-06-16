<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\RealisasiService;
use CodeIgniter\Exceptions\PageNotFoundException;

class RealisasiController extends BaseController
{
    protected $realisasiService;

    public function __construct()
    {
        $this->realisasiService = new RealisasiService();
    }

    public function index($slug = null)
    {
        try {
            $idProject = null;
            $progressData = [];
            $categories = [];

            if ($slug) {
                $proyekModel = new \App\Models\ProyekModel();
                $project = $proyekModel->where('slug', $slug)->first();

                if (!$project) {
                    throw PageNotFoundException::forPageNotFound();
                }

                $idProject    = $project['id_project'];
                $progressData = $this->realisasiService->getPekerjaanProgressData($idProject);

                $kategoriModel = new \App\Models\KategoriPekerjaanModel();
                $categories = $kategoriModel
                    ->groupStart()
                        ->where('jenis_kategori', 'sistem')
                        ->orGroupStart()
                            ->where('jenis_kategori', 'custom')
                            ->where('id_project', $idProject)
                        ->groupEnd()
                    ->groupEnd()
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
            }

            return view('proyek/menu/menu-realisasi', [
                'idProject'    => $idProject,
                'slug'         => $slug,
                'progressData' => $progressData,
                'categories'   => $categories
            ]);
        } catch (PageNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::index] ' . $e->getMessage());
            throw $e;
        }
    }

    public function getData()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $data = $this->realisasiService->getPekerjaanProgressData($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Project tidak ditemukan',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }

    public function store(string $slug)
    {
        try {
            $proyekModel = new \App\Models\ProyekModel();
            $project     = $proyekModel->where('slug', $slug)->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan.',
                ]);
            }

            $tanggal   = $this->request->getPost('tanggal');
            $itemsJson = $this->request->getPost('items');
            $items     = json_decode($itemsJson, true);

            if (empty($tanggal) || empty($items) || !is_array($items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data tidak valid. Pastikan tanggal dan item progress terisi.',
                ]);
            }

            $uploadedFiles = $this->request->getFiles()['foto'] ?? [];

            $this->realisasiService->saveProgressBatch(
                (int) $project['id_project'],
                $tanggal,
                $items,
                $uploadedFiles
            );

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Progress berhasil disimpan.',
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::store] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server.',
            ]);
        }
    }

    public function deleteLog(int $id)
    {
        try {
            $this->realisasiService->deleteLog($id);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Log progress berhasil dihapus.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::deleteLog] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server.',
            ]);
        }
    }

    public function deleteSdmItem(int $idItem)
    {
        try {
            $this->realisasiService->deleteSdmItem($idItem);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data penggunaan berhasil dihapus.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::deleteSdmItem] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server.',
            ]);
        }
    }
    public function getSdmResources()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $data = $this->realisasiService->getSdmResources($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::getSdmResources] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }

    public function getSdmData()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $data = $this->realisasiService->getSdmHistory($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::getSdmData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }

    public function storeSdm(string $slug)
    {
        try {
            $proyekModel = new \App\Models\ProyekModel();
            $project     = $proyekModel->where('slug', $slug)->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan.',
                ]);
            }

            $tanggal   = $this->request->getPost('tanggal');
            $itemsJson = $this->request->getPost('items');
            $items     = json_decode($itemsJson, true);

            if (empty($tanggal) || empty($items) || !is_array($items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data tidak valid. Pastikan tanggal dan item terisi.',
                ]);
            }

            $uploadedFiles = $this->request->getFiles()['foto'] ?? [];

            $this->realisasiService->saveSdmProgress(
                (int) $project['id_project'],
                $tanggal,
                $items,
                $this->request->getPost('keterangan'),
                $uploadedFiles
            );

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Realisasi SDM berhasil disimpan.',
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[RealisasiController::storeSdm] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server.',
            ]);
        }
    }
}
