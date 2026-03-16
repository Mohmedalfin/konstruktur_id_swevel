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
            $cards[] = [
                'id'     => $p['id_project'],
                'title'  => $p['nama_proyek'],
                'lokasi' => $p['lokasi_proyek'],
                'nilai'  => $p['harga_deal'] > 0 ? 'Rp ' . number_format($p['harga_deal'], 0, ',', '.') : null,
                'pct'    => '0%', // Temporary placeholder until RAB/Realization logic is implemented
                'tgl'    => $p['tanggal_mulai'] ?? date('Y-m-d', strtotime($p['created_at'])),
                'href'   => base_url('proyek/' . $p['slug']),
                'foto'   => $p['foto_proyek'] // Passing the photo for the view
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
            'kode_proyek'      => $kodeProyek,
            'nama_proyek'      => $namaProyek,
            'slug'             => $slug,
            'lokasi_proyek'    => $this->request->getPost('lokasi_proyek'),
            'jenis_proyek'     => $this->request->getPost('jenis_proyek'),
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('tanggal_mulai')))) : null,
            'estimasi_selesai' => $this->request->getPost('estimasi_selesai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('estimasi_selesai')))) : null,
            'nama_owner'       => $this->request->getPost('nama_owner'),
            'nama_perusahaan'  => $this->request->getPost('perusahaan'),
            'nomor_kontrak'    => $this->request->getPost('nomor_kontrak'),
            'keterangan'       => $this->request->getPost('keterangan_lain'),
            'status_proyek'    => 'draft',
            'sumber_data'      => 'manual',
            'harga_deal'       => $hargaDeal,
            'foto_proyek'      => null,
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

        return view('proyek/edit', ['proyek' => $proyek]);
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
            'nama_proyek'      => $namaProyek,
            'slug'             => $slug,
            'lokasi_proyek'    => $this->request->getPost('lokasi_proyek'),
            'jenis_proyek'     => $this->request->getPost('jenis_proyek'),
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('tanggal_mulai')))) : null,
            'estimasi_selesai' => $this->request->getPost('estimasi_selesai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('estimasi_selesai')))) : null,
            'nama_owner'       => $this->request->getPost('nama_owner'),
            'nama_perusahaan'  => $this->request->getPost('perusahaan'),
            'nomor_kontrak'    => $this->request->getPost('nomor_kontrak'),
            'keterangan'       => $this->request->getPost('keterangan_lain'),
            'harga_deal'       => $hargaDeal,
        ];

        // Process Upload Foto Proyek (Only overwrite if a new one is uploaded)
        $foto = $this->request->getFile('foto_proyek');
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/proyek', $newName);
            $data['foto_proyek'] = 'uploads/proyek/' . $newName;

            // Optional: You could delete the old image here to save space
            if ($existingProyek['foto_proyek'] && file_exists(FCPATH . $existingProyek['foto_proyek'])) {
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
}
