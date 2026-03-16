<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProyekModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MenuRapController extends BaseController
{
    public function index($slug = null)
    {
        $proyekModel = new \App\Models\ProyekModel();

        $project = $proyekModel
            ->where('slug', $slug)
            ->first();

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('proyek/menu/menu-rap', [
            'idProject' => $project['id_project'],
            'slug' => $slug
        ]);
    }

    public function rincianAHS()
    {
        return view('proyek/menu/rincian-ahs');
    }

    public function tambahAHS()
    {
        return view('proyek/menu/tambah-ahs');
    }
    
    public function mainPekerjaan()
    {
        $slug = $this->request->getGet('slug');
        $idProject = $this->request->getGet('id');

        return view('proyek/menu/main-pekerjaan', [
            'slug' => $slug,
            'idProject' => $idProject,
        ]);
    }
    // public function kategoriMaster()
    // {
    //     try {
    //         $rows = $this->kategoriModel
    //             ->orderBy('nama_kategori', 'ASC')
    //             ->findAll();

    //         $data = array_map(function ($row) {
    //             return [
    //                 'id'   => (string) $row['id_kategori_pekerjaan'],
    //                 'nama' => $row['nama_kategori'],
    //             ];
    //         }, $rows);

    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'data'   => $data,
    //         ]);
    //     } catch (\Throwable $e) {
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
    // public function tambahKategori()
    // {
    //     try {
    //         $payload = $this->request->getJSON(true);

    //         $idProject = (int) ($payload['id_project'] ?? 0);
    //         $kategoriList = $payload['kategori'] ?? [];

    //         if ($idProject <= 0) {
    //             return $this->response->setStatusCode(400)->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'id_project wajib diisi',
    //             ]);
    //         }

    //         if (!is_array($kategoriList) || empty($kategoriList)) {
    //             return $this->response->setStatusCode(400)->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'kategori wajib berupa array',
    //             ]);
    //         }

    //         $rap = $this->rapModel->where('id_project', $idProject)->first();

    //         if (!$rap) {
    //             $this->rapModel->insert([
    //                 'id_project'         => $idProject,
    //                 'nama_rap'           => 'RAP Proyek ' . $idProject,
    //                 'subtotal_bahan'     => 0,
    //                 'subtotal_upah'      => 0,
    //                 'subtotal_alat'      => 0,
    //                 'total_keseluruhan'  => 0,
    //                 'status_rap'         => 'draft',
    //                 'keterangan'         => null,
    //             ]);
    //             $rapId = $this->rapModel->getInsertID();
    //         } else {
    //             $rapId = (int) $rap['id_rap'];
    //         }

    //         $saved = [];

    //         foreach ($kategoriList as $item) {
    //             $namaKategori = is_array($item)
    //                 ? trim((string) ($item['nama'] ?? ''))
    //                 : trim((string) $item);

    //             if ($namaKategori === '') {
    //                 continue;
    //             }

    //             $existing = $this->kategoriModel
    //                 ->where('LOWER(nama_kategori)', strtolower($namaKategori))
    //                 ->first();

    //             if (!$existing) {
    //                 $this->kategoriModel->insert([
    //                     'nama_kategori' => $namaKategori,
    //                 ]);
    //                 $kategoriId = $this->kategoriModel->getInsertID();
    //             } else {
    //                 $kategoriId = (int) $existing['id_kategori_pekerjaan'];
    //             }

    //             $saved[] = [
    //                 'id'   => $kategoriId,
    //                 'nama' => $namaKategori,
    //             ];
    //         }

    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Kategori berhasil disimpan',
    //             'data' => [
    //                 'id_rap'    => $rapId,
    //                 'kategori'  => $saved,
    //             ],
    //         ]);
    //     } catch (\Throwable $e) {
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
}