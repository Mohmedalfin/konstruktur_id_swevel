<?php

namespace App\Controllers\gudang;

use App\Controllers\BaseController;

class GudangController extends BaseController
{
    public function dashboard()
    {
        return view('gudang/dashboard', ['activeMenu' => 'dashboard', 'topbarTitle' => 'Gudang - Dashboard']);
    }

    public function permintaan()
    {
        $userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Gudang';
        return view('gudang/menu-permintaan', [
            'activeMenu' => 'permintaan', 
            'topbarTitle' => 'Gudang - Permintaan',
            'userRole' => $userRole
        ]);
    }

    public function stok()
    {
        return view('gudang/menu-stok', ['activeMenu' => 'stok', 'topbarTitle' => 'Gudang - Stok']);
    }

    public function pengadaan()
    {
        return view('gudang/menu-pengadaan', ['activeMenu' => 'pengadaan', 'topbarTitle' => 'Gudang - Pengadaan']);
    }

    public function riwayat()
    {
        return view('gudang/menu-riwayat', ['activeMenu' => 'riwayat', 'topbarTitle' => 'Gudang - Riwayat']);
    }
}
