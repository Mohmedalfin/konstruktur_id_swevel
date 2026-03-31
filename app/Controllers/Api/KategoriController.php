<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\KategoriPekerjaanModel;

class KategoriController extends ResourceController
{
    protected $modelName = 'App\Models\KategoriPekerjaanModel';
    protected $format    = 'json';

    public function index()
    {
        $slug = $this->request->getGet('slug');
        if (!$slug) {
            return $this->fail('slug diperlukan', 400);
        }

        $proyekModel = new \App\Models\ProyekModel();
        $proyek = $proyekModel->where('slug', $slug)->first();
        
        if (!$proyek) {
            return $this->failNotFound('Proyek tidak ditemukan');
        }

        $idProyek = $proyek['id_proyek'];

        // Ambil kategori dari proyek ini, atau dari data bawaan sistem (id_proyek IS NULL)
        $kategori = $this->model
            ->groupStart()
                ->where('id_proyek', $idProyek)
                ->orWhere('id_proyek', null)
                ->orWhere('id_proyek', 0)
            ->groupEnd()
            ->orderBy('id_proyek', 'DESC')
            ->orderBy('id_kategori_pekerjaan', 'DESC')
            ->findAll();

        // Cek kategori yang sudah ada di tabel rap
        $db = \Config\Database::connect();
        $usedCats = $db->table('rap')
            ->select('id_kategori_pekerjaan')
            ->where('id_proyek', $idProyek)
            ->where('id_kategori_pekerjaan IS NOT NULL')
            ->groupBy('id_kategori_pekerjaan')
            ->get()
            ->getResultArray();
            
        $usedCatIds = array_column($usedCats, 'id_kategori_pekerjaan');

        foreach ($kategori as &$kat) {
            $kat['sudah_digunakan'] = in_array($kat['id_kategori_pekerjaan'], $usedCatIds);
        }

        return $this->respond($kategori);
    }

    public function create()
    {
        $json = $this->request->getJSON();
        if (!$json || !isset($json->slug) || !isset($json->nama_kategori)) {
            return $this->fail('Payload tidak lengkap', 400);
        }

        $proyekModel = new \App\Models\ProyekModel();
        $proyek = $proyekModel->where('slug', $json->slug)->first();
        
        if (!$proyek) {
            return $this->failNotFound('Proyek tidak ditemukan');
        }

        $idProyek = $proyek['id_proyek'];

        // Generate a random unique kode for custom category (e.g. "kustom_12345")
        $kodeKategori = 'kustom_' . substr(md5(uniqid(rand(), true)), 0, 8);

        $data = [
            'id_proyek'     => $idProyek,
            'kode_kategori' => $kodeKategori,
            'nama_kategori' => $json->nama_kategori
        ];

        // Insert to DB
        $this->model->insert($data); 
        $data['id_kategori_pekerjaan'] = $this->model->getInsertID();

        return $this->respondCreated([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function update($id = null)
    {
        $json = $this->request->getJSON();
        if (!$json || !isset($json->nama_kategori)) {
            return $this->fail('Data tidak dikirim', 400);
        }

        $existing = $this->model->find($id);
        if (!$existing) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }

        $updateData = [
            'nama_kategori' => $json->nama_kategori
        ];

        $this->model->update($id, $updateData);

        return $this->respond([
            'status' => 'success',
            'message' => 'Berhasil diperbarui'
        ]);
    }

    public function delete($id = null)
    {
        $existing = $this->model->find($id);
        if (!$existing) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Kategori dihapus'
        ]);
    }
}
