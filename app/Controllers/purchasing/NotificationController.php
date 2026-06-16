<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;

class NotificationController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Pusat Notifikasi Purchasing',
            'topbarTitle' => 'Pusat Notifikasi',
            'notifikasi' => [
                [
                    'id' => 1,
                    'kategori' => 'Purchase Request',
                    'judul' => 'Pengajuan PR Baru',
                    'pesan' => 'Proyek Gedung A mengajukan Purchase Request untuk Semen Tiga Roda.',
                    'waktu' => '10 menit yang lalu',
                    'is_read' => false,
                    'ikon' => 'fa-solid fa-file-invoice-dollar',
                    'warna' => 'blue'
                ],
                [
                    'id' => 2,
                    'kategori' => 'Purchase Order',
                    'judul' => 'Status PO Diperbarui',
                    'pesan' => 'PO-2023-001 telah disetujui oleh Direktur dan siap diproses.',
                    'waktu' => '1 jam yang lalu',
                    'is_read' => false,
                    'ikon' => 'fa-solid fa-file-signature',
                    'warna' => 'purple'
                ],
                [
                    'id' => 3,
                    'kategori' => 'Purchase Order',
                    'judul' => 'PO Ditolak',
                    'pesan' => 'PO-2023-005 ditolak karena melebihi pagu anggaran.',
                    'waktu' => 'Kemarin',
                    'is_read' => true,
                    'ikon' => 'fa-solid fa-xmark',
                    'warna' => 'red'
                ],
                [
                    'id' => 4,
                    'kategori' => 'Gudang',
                    'judul' => 'Penerimaan Material',
                    'pesan' => 'Material Besi Beton 10mm untuk PO-2023-002 telah diterima oleh Gudang Utama.',
                    'waktu' => '2 hari yang lalu',
                    'is_read' => true,
                    'ikon' => 'fa-solid fa-boxes-stacked',
                    'warna' => 'blue'
                ],
                [
                    'id' => 5,
                    'kategori' => 'Sistem',
                    'judul' => 'Pemeliharaan Sistem',
                    'pesan' => 'Sistem purchasing akan mengalami pemeliharaan nanti malam.',
                    'waktu' => '1 minggu yang lalu',
                    'is_read' => true,
                    'ikon' => 'fa-solid fa-server',
                    'warna' => 'gray'
                ]
            ]
        ];

        return view('purchasing/notification/index', $data);
    }
}
