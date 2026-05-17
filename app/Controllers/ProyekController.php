<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProyekController extends BaseController
{
    public function index()
    {
        $proyekModel = new \App\Models\ProyekModel();
        $proyeks = $proyekModel->orderBy('created_at', 'DESC')->findAll();

        $cards = [];
        foreach ($proyeks as $p) {
            $slug = $p['slug'];

            // Auto-generate missing slug for old projects
            if (empty($slug)) {
                $slug = $proyekModel->generateUniqueSlug($p['nama_proyek'] ?? 'proyek', $p['id_project']);
                $proyekModel->update($p['id_project'], ['slug' => $slug]);
            }

            $cards[] = [
                'id' => $p['id_project'],
                'title' => $p['nama_proyek'],
                'lokasi' => $p['lokasi_proyek'],
                'nilai' => $p['harga_deal'] > 0 ? 'Rp ' . number_format($p['harga_deal'], 0, ',', '.') : null,
                'pct' => '0%', // Temporary placeholder until RAB/Realization logic is implemented
                'tgl' => $p['tanggal_mulai'] ?? date('Y-m-d', strtotime($p['created_at'])),
                'href' => base_url('proyek/' . $slug),
                'foto' => $p['foto_proyek'], // Passing the photo for the view
                'status' => $p['status_proyek'] ?? 'draft',
            ];
        }

        return view('proyek/index', ['cards' => $cards]);
    }
    public function show($slug)
    {
        $proyekModel = new \App\Models\ProyekModel();
        $proyek = $proyekModel->where('slug', $slug)->first();

        if (!$proyek) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Proyek tidak ditemukan.');
        }

        return view('proyek/show', ['proyek' => $proyek]);
    }
    public function create()
    {
        return view('proyek/create');
    }
    public function store()
    {
        $proyekModel = new \App\Models\ProyekModel();
        $namaProyek = trim($this->request->getPost('nama_proyek') ?? '');
        $slug = $proyekModel->generateUniqueSlug($namaProyek);
        // Generate simple Kode Proyek
        $kodeProyek = 'PRJ-' . date('YmdHis') . '-' . rand(100, 999);

        // Parse harga deal from formatted Rupiah to integer
        $hargaDealRaw = $this->request->getPost('harga_deal') ?? '';
        $hargaDeal = (int) preg_replace('/[^0-9]/', '', $hargaDealRaw);

        // Map form data to database fields
        $data = [
            'kode_proyek'     => $kodeProyek,
            'nama_proyek'     => $namaProyek,
            'slug'            => $slug,
            'lokasi_proyek'   => $this->request->getPost('lokasi_proyek'),
            'id_template'     => ($this->request->getPost('id_template') !== null && $this->request->getPost('id_template') !== '')
                                     ? (int) $this->request->getPost('id_template')
                                     : null,
            'jenis_proyek'    => $this->request->getPost('jenis_proyek'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('tanggal_mulai')))) : null,
            'estimasi_selesai'=> $this->request->getPost('estimasi_selesai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('estimasi_selesai')))) : null,
            'nama_owner'      => $this->request->getPost('nama_owner'),
            'nama_perusahaan' => $this->request->getPost('perusahaan'),
            'nomor_kontrak'   => $this->request->getPost('nomor_kontrak'),
            'keterangan'      => $this->request->getPost('keterangan_lain'),
            'status_proyek'   => 'draft',
            'sumber_data'     => 'manual',
            'harga_deal'      => $hargaDeal,
            'id_wilayah'      => $this->request->getPost('id_wilayah'),
            'foto_proyek'     => null,
        ];

        // Process Upload Foto Proyek
        $foto = $this->request->getFile('foto_proyek');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/proyek', $newName);
            $data['foto_proyek'] = 'uploads/proyek/' . $newName;
        }

        // Insert into `projects` table
        $proyekModel->insert($data);

        // Process Upload Dokumen (Multiple Documents)
        $dokumenFiles = $this->request->getFileMultiple('dokumen');
        if ($dokumenFiles) {
            foreach ($dokumenFiles as $doc) {
                if ($doc->isValid() && !$doc->hasMoved()) {
                    $docNewName = $doc->getRandomName();
                    // Save document to generic folder for now as there's no project_document table mentioned
                    $doc->move(FCPATH . 'uploads/dokumen', $docNewName);
                }
            }
        }

        return redirect()->to(base_url('proyek'))->with('success', 'Proyek berhasil dibuat.');
    }

    public function edit($id)
    {
        $proyekModel = new \App\Models\ProyekModel();
        $proyek = $proyekModel->find($id);

        if (!$proyek) {
            return redirect()->to(base_url('proyek'))->with('error', 'Proyek tidak ditemukan.');
        }

        // Cari id_prov untuk pre-fill dropdown wilayah di frontend
        $idProv = null;
        if (!empty($proyek['id_wilayah'])) {
            try {
                $dbEst = \Config\Database::connect('estimator');
                $w = $dbEst->query("SELECT id_prov FROM wilayah WHERE id_wilayah = ?", [$proyek['id_wilayah']])->getRowArray();
                $idProv = $w['id_prov'] ?? null;
            } catch (\Throwable $e) {}
        }

        return view('proyek/edit', [
            'proyek' => $proyek,
            'id_prov' => $idProv
        ]);
    }

    public function update($id)
    {
        $proyekModel = new \App\Models\ProyekModel();
        $existingProyek = $proyekModel->find($id);

        if (!$existingProyek) {
            return redirect()->to(base_url('proyek'))->with('error', 'Proyek tidak ditemukan.');
        }

        // Parse harga deal from formatted Rupiah to integer
        $hargaDealRaw = $this->request->getPost('harga_deal') ?? '';
        $hargaDeal = (int) preg_replace('/[^0-9]/', '', $hargaDealRaw);

        $namaProyek = trim($this->request->getPost('nama_proyek') ?? '');
        $slug = $proyekModel->generateUniqueSlug($namaProyek, (int) $id);

        $data = [
            'nama_proyek'     => $namaProyek,
            'slug'            => $slug,
            'lokasi_proyek'   => $this->request->getPost('lokasi_proyek'),
            'id_template'     => ($this->request->getPost('id_template') !== null && $this->request->getPost('id_template') !== '')
                                     ? (int) $this->request->getPost('id_template')
                                     : null,
            'id_wilayah'      => $this->request->getPost('id_wilayah'),
            'jenis_proyek'    => $this->request->getPost('jenis_proyek'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('tanggal_mulai')))) : null,
            'estimasi_selesai'=> $this->request->getPost('estimasi_selesai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('estimasi_selesai')))) : null,
            'nama_owner'      => $this->request->getPost('nama_owner'),
            'nama_perusahaan' => $this->request->getPost('perusahaan'),
            'nomor_kontrak'   => $this->request->getPost('nomor_kontrak'),
            'keterangan'      => $this->request->getPost('keterangan_lain'),
            'harga_deal'      => $hargaDeal,
        ];

        // Process Upload Foto Proyek (Only overwrite if a new one is uploaded)
        $foto = $this->request->getFile('foto_proyek');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/proyek', $newName);
            $data['foto_proyek'] = 'uploads/proyek/' . $newName;

            // Only delete the old image if it's in the uploads folder (prevent deleting default assets)
            if ($existingProyek['foto_proyek'] && strpos($existingProyek['foto_proyek'], 'uploads/') === 0 && file_exists(FCPATH . $existingProyek['foto_proyek'])) {
                @unlink(FCPATH . $existingProyek['foto_proyek']);
            }
        }

        // Update the database
        $proyekModel->update($id, $data);

        // Process Upload Dokumen (Multiple Documents) - Optional Add-on capability
        $dokumenFiles = $this->request->getFileMultiple('dokumen');
        if ($dokumenFiles) {
            foreach ($dokumenFiles as $doc) {
                if ($doc->isValid() && !$doc->hasMoved()) {
                    $docNewName = $doc->getRandomName();
                    $doc->move(FCPATH . 'uploads/dokumen', $docNewName);
                }
            }
        }

        return redirect()->to(base_url('proyek'))->with('success', 'Proyek berhasil diperbarui.');
    }

    /**
     * POST /proyek/selesai/:id
     * Marks a project as done and returns JSON.
     */
    public function selesai($id): ResponseInterface
    {
        $idProject = (int) $id;

        if ($idProject <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'ID proyek tidak valid',
            ]);
        }

        $db    = db_connect();
        $exist = $db->table('projects')->where('id_project', $idProject)->get()->getRowArray();

        if (!$exist) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Proyek tidak ditemukan',
            ]);
        }

        $db->table('projects')
           ->where('id_project', $idProject)
           ->update(['status_proyek' => 'done']);

        if ($db->affectedRows() === 0) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan status (tidak ada baris yang diubah)',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Proyek berhasil ditandai selesai',
        ]);
    }

    /**
     * DELETE /proyek/delete/:id
     * Cascading delete for project and all related RAP/AHS data.
     */
    public function destroy($id): ResponseInterface
    {
        $idProject = (int) $id;
        $db = db_connect();

        try {
            $db->transStart();

            // 1. Get project details for asset cleanup
            $proyek = $db->table('projects')->where('id_project', $idProject)->get()->getRowArray();
            if (!$proyek) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Proyek tidak ditemukan'
                ]);
            }

            // 2. Cascade delete RAP data
            $rap = $db->table('rap')->where('id_project', $idProject)->get()->getRowArray();
            if ($rap) {
                $rapId = $rap['id_rap'];

                // Delete AHS items via rap_detail
                $details = $db->table('rap_detail')->where('id_rap', $rapId)->get()->getResultArray();
                foreach ($details as $detail) {
                    $db->table('rap_detail_item')->where('id_rap_detail', $detail['id_rap_detail'])->delete();
                }

                // Delete rap_detail, rap_kategori, and rap
                $db->table('rap_detail')->where('id_rap', $rapId)->delete();
                $db->table('rap_kategori')->where('id_rap', $rapId)->delete();
                $db->table('rap')->where('id_rap', $rapId)->delete();
            }

            // 3. Delete custom categories
            $db->table('kategori_pekerjaan')
               ->where('id_project', $idProject)
               ->where('jenis_kategori', 'custom')
               ->delete();

            // 4. Delete project photo (only if it's an uploaded file)
            if (!empty($proyek['foto_proyek']) && strpos($proyek['foto_proyek'], 'uploads/') === 0 && file_exists(FCPATH . $proyek['foto_proyek'])) {
                @unlink(FCPATH . $proyek['foto_proyek']);
            }

            // 5. Delete the main project record
            $db->table('projects')->where('id_project', $idProject)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data di database');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Proyek berhasil dihapus'
            ]);

        } catch (\Throwable $e) {
            if ($db->transStatus() === true) { 
                $db->transRollback();
            }
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
