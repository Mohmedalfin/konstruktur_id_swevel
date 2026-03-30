<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProyekController extends BaseController
{
    public function index()
    {
        $proyekModel = new \App\Models\ProyekModel();
        // 1. Ambil data proyek (urutkan dari terbaru)
        $data['proyeks'] = $proyekModel->orderBy('id_proyek', 'DESC')->findAll();

        // 2. Ambil data Provinsi & Kabupaten dari JSON lokal statis yang telah digenerate (Super Cepat!)
        $wilayahJson = FCPATH . 'assets/json/wilayah.json';
        if (file_exists($wilayahJson)) {
            $data['wilayah'] = json_decode(file_get_contents($wilayahJson), true);
        } else {
            $data['wilayah'] = [];
        }

        return view('proyek/index', $data);
    }

    public function create()
    {
        return view('proyek/create');
    }

    public function store() {
        // 1. Validasi Sederhana
        $validationRules = [
            'nama_proyek'   => 'required',
            'lokasi_proyek' => 'required',
        ];

        if (!$this->validate($validationRules)) {
            // Bisa menggunakan withInput() untuk mengembalikan nilai ke form, di sini asumsikan error sederhana
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $session = session();
        $idUser = $session->get('id_user') ?? 1; // Fallback jika belum terintegrasi auth login

        $proyekModel = new \App\Models\ProyekModel();
        
        // 2. Generate Kode Proyek (Format: PRJ-YYYYMM-XXXX)
        $ym = date('Ym');
        $lastProyek = $proyekModel->withDeleted()
                                  ->like('kode_proyek', 'PRJ-'.$ym, 'after')
                                  ->orderBy('id_proyek', 'DESC')
                                  ->first();
        if ($lastProyek && !empty($lastProyek['kode_proyek'])) {
            $lastNo = (int) substr($lastProyek['kode_proyek'], -4);
            $newNo = str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNo = '0001';
        }
        $kodeProyek = 'PRJ-'.$ym.'-'.$newNo;

        // 3. Upload Foto Project
        $fotoName = null;
        $foto = $this->request->getFile('foto_proyek');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/proyek', $fotoName);
        }

        // 4. Upload File Pendukung (Multiple)
        $filesName = [];
        if ($files = $this->request->getFiles()) {
            // "dokumen" adalah name="dokumen[]" di form HTML
            if (isset($files['dokumen'])) {
                foreach ($files['dokumen'] as $doc) {
                    if ($doc->isValid() && !$doc->hasMoved()) {
                        $newName = $doc->getRandomName();
                        $doc->move(FCPATH . 'uploads/proyek/dokumen', $newName);
                        $filesName[] = $newName;
                    }
                }
            }
        }

        helper(['url', 'text']);

        // 5. Struktur Data Array yang Dimasukkan ke Tabel
        $namaProyek = $this->request->getPost('nama_proyek');
        
        $slug = url_title($namaProyek, '-', true) . '-' . strtolower(random_string('alnum', 6));

        $data = [
            'id_user'          => $idUser,
            'kode_proyek'      => $kodeProyek,
            'slug'             => $slug,
            'nama_proyek'      => $namaProyek,
            'lokasi_proyek'    => $this->request->getPost('lokasi_proyek'),
            'jenis_proyek'     => $this->request->getPost('jenis_proyek'),
            'tgl_mulai'        => $this->request->getPost('tanggal_mulai') ?: null,
            'tgl_selesai'      => $this->request->getPost('estimasi_selesai') ?: null,
            'nama_owner_klien' => $this->request->getPost('nama_owner'),
            'perusahaan'       => $this->request->getPost('perusahaan'),
            'nomor_kontrak'    => $this->request->getPost('nomor_kontrak'),
            'keterangan_lain'  => $this->request->getPost('keterangan_lain'),
            'foto_project'     => $fotoName,
            'file_pendukung'   => !empty($filesName) ? json_encode($filesName) : null
        ];

        $proyekModel->insert($data);

        return redirect()->to('/proyek')->with('success', 'Data proyek berhasil ditambahkan.');
    }

    public function complete($id)
    {
        $proyekModel = new \App\Models\ProyekModel();
        $proyek = $proyekModel->find($id);
        if (!$proyek) {
            return redirect()->to('/proyek')->with('error', 'Proyek tidak ditemukan.');
        }

        $proyekModel->update($id, ['status' => 'Selesai']);

        return redirect()->to('/proyek')->with('success', 'Status proyek berhasil diubah menjadi Selesai.');
    }

    public function delete($id)
    {
        $proyekModel = new \App\Models\ProyekModel();
        
        $proyek = $proyekModel->find($id);
        if (!$proyek) {
            return redirect()->to('/proyek')->with('error', 'Proyek tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->table('proyek')->where('id_proyek', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('/proyek')->with('success', 'Proyek berhasil dihapus.');
    }
}
