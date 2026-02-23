<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Registrasi extends BaseController
{
    public function index()
    {
        return view('tambah_pengguna');
    }

    public function simpan()
    {
        $model = new PenggunaModel();

        $data = [
            'nama_pengguna'     => $this->request->getPost('nama_pengguna'),
            'profil'            => $this->request->getPost('profil'),
            'id_wilayah'        => $this->request->getPost('id_wilayah'),
            'no_wa'             => $this->request->getPost('no_wa'),
            'website'           => $this->request->getPost('website') ?? '-',
            'harga_min'         => $this->request->getPost('harga_min') ?? 0,
            'harga_max'         => $this->request->getPost('harga_max') ?? 0,
            'nego'              => $this->request->getPost('nego'),
            'username'          => $this->request->getPost('username'),
            'password'          => $this->request->getPost('password'), // Di-hash otomatis oleh Model
            'kategori_akun'     => $this->request->getPost('kategori_akun'),
            'status'            => '1',
            'status_verifikasi' => '1',
            'tgl_daftar'        => date('Y-m-d'),
            'jam_daftar'        => date('H:i:s'),
        ];

        if ($model->insert($data)) {
            return redirect()->to('/registrasi')->with('msg', 'Data Berhasil Masuk!');
        } else {
            return redirect()->back()->with('msg', 'Gagal Simpan Data.');
        }
    }
}