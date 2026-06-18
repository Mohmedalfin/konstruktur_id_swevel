<?php

namespace App\Controllers\gudang;

use App\Controllers\BaseController;

class GudangController extends BaseController
{
    private function getIdPerusahaan(): int
    {
        $userId = session()->get('id_pengguna') ?? session()->get('id_user');
        if (!$userId) return 0;
        
        $db = \Config\Database::connect();
        $user = $db->table('pengguna')->where('id_pengguna', $userId)->get()->getRow();
        
        if ($user && !empty($user->parent_id)) {
            return (int)$user->parent_id;
        }
        return (int)$userId;
    }

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

    public function notifikasi()
    {
        return view('gudang/menu-notifikasi', ['activeMenu' => 'notifikasi', 'topbarTitle' => 'Pusat Notifikasi']);
    }

    public function getDashboardData(): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $idPerusahaan = $this->getIdPerusahaan();
            if (!$idPerusahaan) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }
            
            $db = \Config\Database::connect();
            
            // 1. KPI Stats
            $totalBarang = $db->table('master_barang')
                ->where('id_perusahaan', $idPerusahaan)
                ->countAllResults();
            
            $stokKritisCount = $db->table('stok_gudang')
                ->where('id_perusahaan', $idPerusahaan)
                ->where('stok_aktual <= stok_minimum')
                ->countAllResults();

            $permintaanPending = $db->table('permintaan p')
                ->join('pengguna u', 'u.id_pengguna = p.pemohon_id', 'left')
                ->where('p.status', 'pending')
                ->groupStart()
                    ->where('u.parent_id', $idPerusahaan)
                    ->orWhere('u.id_pengguna', $idPerusahaan)
                ->groupEnd()
                ->countAllResults();

            $pengadaanAktif = $db->table('purchase_requests')
                ->where('id_perusahaan', $idPerusahaan)
                ->whereIn('status', ['pending', 'ordered'])
                ->countAllResults();
                
            // 2. Stok Kritis List
            $itemsKritis = $db->table('stok_gudang sg')
                ->select('sg.*, mb.kode_barang, mb.nama_barang, mb.satuan, mb.spesifikasi, mb.merk')
                ->join('master_barang mb', 'mb.id = sg.id_barang', 'inner')
                ->where('sg.id_perusahaan', $idPerusahaan)
                ->where('sg.stok_aktual <= sg.stok_minimum')
                ->orderBy('sg.stok_aktual', 'ASC')
                ->limit(5)
                ->get()
                ->getResultArray();

            // 3. Recent Activities
            $recentPermintaan = $db->query(
                "SELECT p.id, p.nomor_permintaan as no_ref, p.tanggal_permintaan as tanggal, p.status, p.created_at,
                        'permintaan' as tipe, u.nama_pengguna as nama_operator
                 FROM permintaan p
                 LEFT JOIN pengguna u ON u.id_pengguna = p.pemohon_id
                 WHERE (u.parent_id = ? OR u.id_pengguna = ?)
                 ORDER BY p.id DESC LIMIT 5",
                [$idPerusahaan, $idPerusahaan]
            )->getResultArray();

            $recentPengadaan = $db->query(
                "SELECT pr.id, pr.pr_number as no_ref, pr.request_date as tanggal, pr.status, pr.created_at,
                        'pengadaan' as tipe, u.nama_pengguna as nama_operator
                 FROM purchase_requests pr
                 LEFT JOIN pengguna u ON u.id_pengguna = pr.created_by
                 WHERE (u.parent_id = ? OR u.id_pengguna = ?)
                 ORDER BY pr.id DESC LIMIT 5",
                [$idPerusahaan, $idPerusahaan]
            )->getResultArray();
                
            $activities = array_merge($recentPermintaan, $recentPengadaan);
            usort($activities, function($a, $b) {
                return strcmp($b['created_at'] ?? $b['tanggal'], $a['created_at'] ?? $a['tanggal']);
            });
            $activities = array_slice($activities, 0, 7);

            // 4. Stock Health
            $amanCount = $db->table('master_barang mb')
                ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                ->where('mb.id_perusahaan', $idPerusahaan)
                ->where('COALESCE(sg.stok_aktual, 0) > COALESCE(sg.stok_minimum, 0)')
                ->countAllResults();
                
            $kritisCount = $db->table('master_barang mb')
                ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                ->where('mb.id_perusahaan', $idPerusahaan)
                ->where('COALESCE(sg.stok_aktual, 0) > 0')
                ->where('COALESCE(sg.stok_aktual, 0) <= COALESCE(sg.stok_minimum, 0)')
                ->countAllResults();
                
            $kosongCount = $db->table('master_barang mb')
                ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                ->where('mb.id_perusahaan', $idPerusahaan)
                ->where('COALESCE(sg.stok_aktual, 0) <= 0')
                ->countAllResults();

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'stats' => [
                        'total_barang'       => $totalBarang,
                        'stok_kritis'        => $stokKritisCount,
                        'permintaan_pending' => $permintaanPending,
                        'pengadaan_aktif'    => $pengadaanAktif,
                    ],
                    'items_kritis' => $itemsKritis,
                    'activities'   => $activities,
                    'chart_health' => [
                        'aman'   => $amanCount,
                        'kritis' => $kritisCount,
                        'kosong' => $kosongCount,
                    ],
                ]
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', '[GudangController::getDashboardData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil data dashboard gudang.',
            ]);
        }
    }
}
