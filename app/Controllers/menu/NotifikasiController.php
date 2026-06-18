<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
class NotifikasiController extends BaseController
{
    public function index()
    {
        $userRole = strtolower(session()->get('kategori_akun') ?? session()->get('role') ?? 'kontraktor');
        $layout = 'layouts/app';
        
        if ($userRole === 'gudang') {
            $layout = 'gudang/layouts/main';
        } elseif ($userRole === 'purchasing') {
            $layout = 'purchasing/layouts/main';
        }

        $data = [
            'title' => 'Pusat Notifikasi',
            'topbarTitle' => 'Pusat Notifikasi',
            'layout' => $layout
        ];

        return view('proyek/menu/menu-notifikasi', $data);
    }
}
