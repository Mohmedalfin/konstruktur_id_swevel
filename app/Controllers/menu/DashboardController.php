<?php

namespace App\Controllers\menu;

use App\Controllers\BaseController;
use App\Services\DashboardService;
use CodeIgniter\Exceptions\PageNotFoundException;

class DashboardController extends BaseController
{
    protected $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index($slug = null)
    {
        if (!$slug) {
            // Global Dashboard
            $db = db_connect();

            // ── 1. Semua proyek ──────────────────────────────────────────────
            $proyeks = $db->table('projects')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getResultArray();

            $totalProyek = count($proyeks);
            $totalProyekAktif = count(array_filter($proyeks, fn($p) => ($p['status_proyek'] ?? '') !== 'done'));

            // ── 2. Total Nilai Kontrak (harga_deal) ──────────────────────────
            $totalNilaiKontrak = array_sum(array_column($proyeks, 'harga_deal'));

            // ── 3. Total RAP & 4. Rata-rata progres & 5. Project Health & 7. Daftar Proyek ──
            $healthCounts = ['critical' => 0, 'warning' => 0, 'healthy' => 0];
            $scheduleCounts = ['ontime' => 0, 'delayed' => 0];
            $costCounts = ['onbudget' => 0, 'overrun' => 0];
            $daftarProyek = [];
            
            $totalEv = 0;
            $totalBac = 0;
            $totalAc = 0;

            foreach ($proyeks as $idx => $p) {
                $status = $p['status_proyek'] ?? 'draft';

                // Ambil metrik aktual dari EVM
                $metrics = $this->dashboardService->getProjectMetrics($p['id_project']);
                
                $totalEv += $metrics['ev_value'] ?? 0;
                $totalBac += $metrics['bac_value'] ?? 0;
                $totalAc += $metrics['ac_value'] ?? 0;

                // Schedule status
                $schedLabel = $metrics['schedule_status'] ?? 'On Time';
                $schedClass = match ($schedLabel) {
                    'Early' => 'badge-early',
                    'Slightly Delay' => 'badge-slight',
                    'Delayed' => 'badge-delayed',
                    default => 'badge-ontime',
                };
                $jadwalStatus = ['label' => $schedLabel, 'class' => $schedClass];

                // Cost status
                $costLabel = $metrics['cost_status'] ?? 'On Budget';
                $costClass = match ($costLabel) {
                    'Under Budget' => 'badge-under',
                    'Slightly Over' => 'badge-slightover',
                    'Overrun' => 'badge-overrun',
                    default => 'badge-onbudget',
                };
                $costStatus = ['label' => $costLabel, 'class' => $costClass];

                // Schedule Counts
                if (in_array($schedLabel, ['Delayed', 'Slightly Delay'])) {
                    $scheduleCounts['delayed']++;
                } else {
                    $scheduleCounts['ontime']++;
                }

                // Cost Counts
                if (in_array($costLabel, ['Overrun', 'Slightly Over'])) {
                    $costCounts['overrun']++;
                } else {
                    $costCounts['onbudget']++;
                }

                // Overall status & Health Counts
                if ($status === 'done') {
                    $overall = ['label' => 'Healthy', 'class' => 'badge-healthy'];
                    $healthCounts['healthy']++;
                } elseif ($schedLabel === 'Delayed' || $costLabel === 'Overrun') {
                    $overall = ['label' => 'Critical', 'class' => 'badge-critical'];
                    $healthCounts['critical']++;
                } elseif ($schedLabel === 'Slightly Delay' || $costLabel === 'Slightly Over') {
                    $overall = ['label' => 'Warning', 'class' => 'badge-warning'];
                    $healthCounts['warning']++;
                } else {
                    $overall = ['label' => 'Healthy', 'class' => 'badge-healthy'];
                    $healthCounts['healthy']++;
                }

                $daftarProyek[] = [
                    'no' => $idx + 1,
                    'nama' => $p['nama_proyek'],
                    'lokasi' => $p['lokasi_proyek'],
                    'jadwalStatus' => $jadwalStatus,
                    'costStatus' => $costStatus,
                    'overall' => $overall,
                    'slug' => $p['slug'],
                ];
            }

            $rataProgres = $totalBac > 0 ? round(($totalEv / $totalBac) * 100, 1) : 0;
            
            $totalRap = $totalAc;
            $pctSerapan = $totalNilaiKontrak > 0
                ? round(($totalRap / $totalNilaiKontrak) * 100, 1)
                : 0;

            // ── 6. Cash Flow (RAP per bulan, 6 bulan terakhir) ───────────────
            $cashFlow = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $label = date('M Y', strtotime("-$i months"));
                $planned = (float) ($db->table('rap')
                    ->selectSum('total_keseluruhan', 'total')
                    ->where("DATE_FORMAT(created_at,'%Y-%m')", $month)
                    ->get()
                    ->getRowArray()['total'] ?? 0);
                $cashFlow[] = [
                    'label' => $label,
                    'planned' => $planned,
                    'actual' => $planned * 0.85, // placeholder sampai modul realisasi siap
                ];
            }

            return view('proyek/menu/dashboard', [
                'topbarTitle' => 'Dashboard',
                'totalProyek' => $totalProyek,
                'totalProyekAktif' => $totalProyekAktif,
                'totalNilaiKontrak' => $totalNilaiKontrak,
                'totalRap' => $totalRap,
                'pctSerapan' => $pctSerapan,
                'rataProgres' => $rataProgres,
                'healthCounts' => $healthCounts,
                'scheduleCounts' => $scheduleCounts,
                'costCounts' => $costCounts,
                'cashFlow' => $cashFlow,
                'daftarProyek' => $daftarProyek,
            ]);
        }

        // Specific Project Dashboard
        try {
            $idProject = null;

            $proyekModel = new \App\Models\ProyekModel();
            $project = $proyekModel->where('slug', $slug)->first();

            if (!$project) {
                throw PageNotFoundException::forPageNotFound('Proyek tidak ditemukan.');
            }

            $idProject = $project['id_project'];

            return view('proyek/menu/menu-dashboard', [
                'idProject' => $idProject,
                'slug' => $slug,
            ]);
        } catch (PageNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::index] ' . $e->getMessage());
            throw $e;
        }
    }
    public function getData()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $overview = $this->dashboardService->getOverviewData($idProject);
            $summary = $this->dashboardService->getRingkasanPekerjaan($idProject);
            $chart = $this->dashboardService->getChartData($idProject);
            $projectMetrics = $this->dashboardService->getProjectMetrics($idProject, $chart);
            $costChart = $this->dashboardService->getCostChartData($idProject);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'overview' => array_merge($overview, $projectMetrics),
                    'summary' => $summary,
                    'chart' => $chart,
                    'cost_chart' => $costChart,
                ],
            ]);
        } catch (PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Project tidak ditemukan',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::getData] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }

    public function getCategoryDetail($idKategori)
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);
            $categoryId = (int) $idKategori;

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            if ($categoryId <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id_kategori wajib diisi',
                ]);
            }

            $categoryMetrics = $this->dashboardService->getCategoryMetrics($idProject, $categoryId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id_project' => $idProject,
                    'category_detail' => $categoryMetrics,
                ],
            ]);
        } catch (PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Project tidak ditemukan',
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::getCategoryDetail] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
            ]);
        }
    }
}
