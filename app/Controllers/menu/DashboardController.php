<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        // ── 1. Semua proyek ──────────────────────────────────────────────
        $proyeks = $db->table('projects')
                      ->orderBy('created_at', 'DESC')
                      ->get()
                      ->getResultArray();

        $totalProyek     = count($proyeks);
        $totalProyekAktif = count(array_filter($proyeks, fn($p) => ($p['status_proyek'] ?? '') !== 'done'));

        // ── 2. Total Nilai Kontrak (harga_deal) ──────────────────────────
        $totalNilaiKontrak = array_sum(array_column($proyeks, 'harga_deal'));

        // ── 3. Total RAP (serapan anggaran) ─────────────────────────────
        $totalRap = (float)($db->table('rap')
                              ->selectSum('total_keseluruhan')
                              ->get()
                              ->getRowArray()['total_keseluruhan'] ?? 0);

        $pctSerapan = $totalNilaiKontrak > 0
            ? round(($totalRap / $totalNilaiKontrak) * 100, 1)
            : 0;

        // ── 4. Rata-rata progres (placeholder — pakai status proyek) ─────
        $rataProgres = 0; // akan dikembangkan ketika modul realisasi tersedia

        // ── 5. Project Health (berdasar status_proyek) ───────────────────
        $healthCounts = ['critical' => 0, 'warning' => 0, 'healthy' => 0];
        foreach ($proyeks as $p) {
            $s = $p['status_proyek'] ?? 'draft';
            if ($s === 'done') {
                $healthCounts['healthy']++;
            } elseif ($s === 'draft') {
                $healthCounts['warning']++;
            } else {
                $healthCounts['healthy']++;
            }
        }

        // ── 6. Cash Flow (RAP per bulan, 6 bulan terakhir) ───────────────
        $cashFlow = [];
        for ($i = 5; $i >= 0; $i--) {
            $month     = date('Y-m', strtotime("-$i months"));
            $label     = date('M Y', strtotime("-$i months"));
            $planned   = (float)($db->table('rap')
                                    ->selectSum('total_keseluruhan', 'total')
                                    ->where("DATE_FORMAT(created_at,'%Y-%m')", $month)
                                    ->get()
                                    ->getRowArray()['total'] ?? 0);
            $cashFlow[] = [
                'label'   => $label,
                'planned' => $planned,
                'actual'  => $planned * 0.85, // placeholder sampai modul realisasi siap
            ];
        }

        // ── 7. Daftar proyek untuk tabel ─────────────────────────────────
        $daftarProyek = [];
        foreach ($proyeks as $idx => $p) {
            $status = $p['status_proyek'] ?? 'draft';

            // Schedule status — placeholder logic
            $jadwalStatus = match($status) {
                'done'   => ['label' => 'Early',        'class' => 'badge-early'],
                'aktif'  => ['label' => 'On Time',      'class' => 'badge-ontime'],
                default  => ['label' => 'Slightly Delay','class' => 'badge-slight'],
            };

            // Cost status — placeholder logic
            $rapRow   = $db->table('rap')->where('id_project', $p['id_project'])->get()->getRowArray();
            $rapTotal = (float)($rapRow['total_keseluruhan'] ?? 0);
            $deal     = (float)($p['harga_deal'] ?? 0);

            if ($deal <= 0 || $rapTotal <= 0) {
                $costStatus = ['label' => 'On Budget', 'class' => 'badge-onbudget'];
            } elseif ($rapTotal > $deal) {
                $costStatus = ['label' => 'Overrun', 'class' => 'badge-overrun'];
            } elseif ($rapTotal < $deal * 0.9) {
                $costStatus = ['label' => 'Under Budget', 'class' => 'badge-under'];
            } else {
                $costStatus = ['label' => 'Slightly Over', 'class' => 'badge-slightover'];
            }

            // Overall status
            $overall = match($status) {
                'done'  => ['label' => 'Healthy',  'class' => 'badge-healthy'],
                'aktif' => ['label' => 'Warning',  'class' => 'badge-warning'],
                default => ['label' => 'Warning',  'class' => 'badge-warning'],
            };

            $daftarProyek[] = [
                'no'           => $idx + 1,
                'nama'         => $p['nama_proyek'],
                'lokasi'       => $p['lokasi_proyek'],
                'jadwalStatus' => $jadwalStatus,
                'costStatus'   => $costStatus,
                'overall'      => $overall,
                'slug'         => $p['slug'],
            ];
        }

        return view('proyek/menu/dashboard', [
            'topbarTitle'       => 'Dashboard Proyek',
            'totalProyek'       => $totalProyek,
            'totalProyekAktif'  => $totalProyekAktif,
            'totalNilaiKontrak' => $totalNilaiKontrak,
            'totalRap'          => $totalRap,
            'pctSerapan'        => $pctSerapan,
            'rataProgres'       => $rataProgres,
            'healthCounts'      => $healthCounts,
            'cashFlow'          => $cashFlow,
            'daftarProyek'      => $daftarProyek,
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store() {}
}