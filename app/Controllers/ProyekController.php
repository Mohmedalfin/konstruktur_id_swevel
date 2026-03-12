<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProyekController extends BaseController
{
    public function index()
    {
        return view('proyek/index');
    }
    public function create()
    {
        return view('proyek/create');
    }
    public function store()
    {
        $proyekModel = new \App\Models\ProyekModel();

        // Generate simple Kode Proyek
        $kodeProyek = 'PRJ-' . date('YmdHis') . '-' . rand(100, 999);

        // Parse harga deal from formatted Rupiah to integer
        $hargaDealRaw = $this->request->getPost('harga_deal') ?? '';
        $hargaDeal = (int) preg_replace('/[^0-9]/', '', $hargaDealRaw);

        // Map form data to database fields
        $data = [
            'kode_proyek'      => $kodeProyek,
            'nama_proyek'      => $this->request->getPost('nama_proyek'),
            'lokasi_proyek'    => $this->request->getPost('lokasi_proyek'),
            'jenis_proyek'     => $this->request->getPost('jenis_proyek'),
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('tanggal_mulai')))) : null,
            'estimasi_selesai' => $this->request->getPost('estimasi_selesai') ? date('Y-m-d', strtotime(str_replace('.', '-', $this->request->getPost('estimasi_selesai')))) : null,
            'nama_owner'       => $this->request->getPost('nama_owner'),
            'nama_perusahaan'  => $this->request->getPost('perusahaan'), // in schema it's nama_perusahaan, in form it's perusahaan
            'nomor_kontrak'    => $this->request->getPost('nomor_kontrak'),
            'keterangan'       => $this->request->getPost('keterangan_lain'),
            'status_proyek'    => 'draft',
            'sumber_data'      => 'manual',
            'harga_deal'       => $hargaDeal,
            'foto_proyek'      => null, // Save as null if no photo is uploaded to save database space
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
}
