<?php

namespace App\Controllers;

class NotifikasiController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Pusat Notifikasi',
            'topbarTitle' => 'Pusat Notifikasi',
            'notifikasi' => [
                [
                    'id' => 1,
                    'kategori' => 'Gudang',
                    'judul' => 'Permintaan Gudang Baru',
                    'pesan' => 'Proyek Gedung A meminta pengiriman 100 sak Semen Tiga Roda secepatnya.',
                    'waktu' => '10 menit yang lalu',
                    'is_read' => false,
                    'ikon' => 'fa-solid fa-box',
                    'warna' => 'blue'
                ],
                [
                    'id' => 2,
                    'kategori' => 'Purchasing',
                    'judul' => 'Pengajuan Purchasing',
                    'pesan' => 'PO-2023-001 telah disetujui oleh Direktur dan siap diproses.',
                    'waktu' => '1 jam yang lalu',
                    'is_read' => false,
                    'ikon' => 'fa-solid fa-cart-shopping',
                    'warna' => 'purple'
                ],
                [
                    'id' => 3,
                    'kategori' => 'Jadwal',
                    'judul' => 'Peringatan Jadwal',
                    'pesan' => 'Pekerjaan Galian Tanah (Proyek B) mengalami keterlambatan 2 hari dari jadwal.',
                    'waktu' => 'Kemarin',
                    'is_read' => false,
                    'ikon' => 'fa-solid fa-triangle-exclamation',
                    'warna' => 'amber'
                ],
                [
                    'id' => 4,
                    'kategori' => 'Gudang',
                    'judul' => 'Stok Menipis',
                    'pesan' => 'Stok Besi Beton 10mm di Gudang Utama tersisa 50 batang.',
                    'waktu' => '2 hari yang lalu',
                    'is_read' => true,
                    'ikon' => 'fa-solid fa-boxes-stacked',
                    'warna' => 'blue'
                ],
                [
                    'id' => 5,
                    'kategori' => 'Sistem',
                    'judul' => 'Update Sistem Berhasil',
                    'pesan' => 'Pemeliharaan server telah selesai. Sistem berjalan normal.',
                    'waktu' => '1 minggu yang lalu',
                    'is_read' => true,
                    'ikon' => 'fa-solid fa-server',
                    'warna' => 'gray'
                ]
            ]
        ];

        return view('notifikasi/index', $data);
    }
}
