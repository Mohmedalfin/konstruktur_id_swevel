<?php

namespace App\Services;

use App\Models\ProyekModel;
use App\Models\RapModel;
use App\Models\RapDetailModel;
use App\Models\RapKategoriModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Exceptions\PageNotFoundException;

class DashboardService
{
    protected $db;
    protected $proyekModel;
    protected $rapModel;

    public function __construct()
    {
        $this->db          = \Config\Database::connect();
        $this->proyekModel = new ProyekModel();
        $this->rapModel    = new RapModel();
    }

    /**
     * Get overall project statistics for the dashboard
     */
    public function getOverviewData(int $idProject): array
    {
        $project = $this->proyekModel->where('id_project', $idProject)->first();
        if (!$project) {
            throw PageNotFoundException::forPageNotFound('Proyek tidak ditemukan.');
        }

        $rap = $this->rapModel->where('id_project', $idProject)->first();
        
        $nilaiKontrak = (float) ($project['harga_deal'] ?? 0);
        $nilaiRap     = $rap ? (float) $rap['total_keseluruhan'] : 0;
        
        $marginPct = 0;
        if ($nilaiKontrak > 0) {
            $marginPct = round((($nilaiKontrak - $nilaiRap) / $nilaiKontrak) * 100, 2);
        }

        $realisasiBiaya = $this->getActualCostValue($idProject);

        $serapanBiayaPct = 0;
        if ($nilaiRap > 0) {
            $serapanBiayaPct = round(($realisasiBiaya / $nilaiRap) * 100, 2);
        }

        $targetSelesai = $project['estimasi_selesai'] ?? null;
        $hariLagi = null;
        if ($targetSelesai) {
            $targetDate = new \DateTime($targetSelesai);
            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            
            if ($targetDate >= $today) {
                $interval = $today->diff($targetDate);
                $hariLagi = $interval->days;
            } else {
                $interval = $targetDate->diff($today);
                $hariLagi = -$interval->days; // Overdue
            }
        }

        return [
            'project_name'  => $project['nama_proyek'],
            'nilai_kontrak' => $nilaiKontrak,
            'nilai_rap'     => $nilaiRap,
            'margin_pct'    => $marginPct,
            'realisasi'     => $realisasiBiaya,
            'serapan_pct'   => $serapanBiayaPct,
            'target_date'   => $targetSelesai,
            'hari_lagi'     => $hariLagi,
        ];
    }

    public function getRingkasanPekerjaan(int $idProject): array
    {
        $rap = $this->rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            return [];
        }

        $rapId = (int) $rap['id_rap'];
        $totalRapValue = (float) $rap['total_keseluruhan'];

        if ($totalRapValue <= 0) {
            return [];
        }

        $kategoriRows = $this->db->table('rap_kategori rk')
            ->select('rk.id_kategori, kp.nama_kategori')
            ->join('kategori_pekerjaan kp', 'kp.id_kategori_pekerjaan = rk.id_kategori', 'left')
            ->where('rk.id_rap', $rapId)
            ->orderBy('kp.nama_kategori', 'ASC')
            ->get()
            ->getResultArray();

        $detailRows = $this->db->table('rap_detail')
            ->select('id_rap_detail, id_kategori, volume, total_keseluruhan, start_date, finish_date')
            ->select('(SELECT COALESCE(SUM(volume_tercapai), 0) FROM realisasi_pekerjaan WHERE id_rap_detail = rap_detail.id_rap_detail) as total_tercapai')
            ->where('id_rap', $rapId)
            ->where('pekerjaan IS NOT NULL', null, false)
            ->where('pekerjaan !=', '')
            ->get()
            ->getResultArray();

        $groupedDetails = [];
        foreach ($detailRows as $row) {
            $catId = (string) ($row['id_kategori'] ?? '0');
            if (!isset($groupedDetails[$catId])) {
                $groupedDetails[$catId] = [];
            }
            $groupedDetails[$catId][] = $row;
        }

        $summary = [];

        foreach ($kategoriRows as $cat) {
            $catId = (string) $cat['id_kategori'];
            $items = $groupedDetails[$catId] ?? [];
            
            $totalBobotValue = 0;
            $actualWeightedProgress = 0; 
            $plannedWeightedProgress = 0;
            
            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            $todayTs = $today->getTimestamp();

            foreach ($items as $item) {
                $itemValue = (float) $item['total_keseluruhan'];
                $itemVolumeTarget = (float) $item['volume'];
                $itemVolumeTercapai = (float) $item['total_tercapai'];
                
                $totalBobotValue += $itemValue;
                
                // --- Calculate Actual Progress ---
                $itemActualPct = 0;
                if ($itemVolumeTarget > 0) {
                    $itemActualPct = min(1, $itemVolumeTercapai / $itemVolumeTarget);
                }
                $actualWeightedProgress += ($itemActualPct * $itemValue);

                // --- Calculate Planned Progress ---
                $itemPlannedPct = 0;
                $startDate = !empty($item['start_date']) ? new \DateTime($item['start_date']) : null;
                $finishDate = !empty($item['finish_date']) ? new \DateTime($item['finish_date']) : null;

                if ($startDate && $finishDate) {
                    $startDate->setTime(0, 0, 0);
                    $finishDate->setTime(0, 0, 0);
                    
                    $startTs = $startDate->getTimestamp();
                    $finishTs = $finishDate->getTimestamp();

                    if ($todayTs >= $finishTs) {
                        $itemPlannedPct = 1; 
                    } elseif ($todayTs >= $startTs) {
                        $totalDays = ($finishTs - $startTs) / 86400;
                        if ($totalDays > 0) {
                            $elapsedDays = ($todayTs - $startTs) / 86400;
                            $itemPlannedPct = min(1, $elapsedDays / $totalDays);
                        } else {
                            $itemPlannedPct = 1; 
                        }
                    }
                }
                $plannedWeightedProgress += ($itemPlannedPct * $itemValue);
            }

            $bobotKategoriPct = ($totalBobotValue / $totalRapValue) * 100;
            
            $actualKategoriPct = 0;
            $plannedKategoriPct = 0;
            if ($totalBobotValue > 0) {
                $actualKategoriPct = ($actualWeightedProgress / $totalBobotValue) * 100;
                $plannedKategoriPct = ($plannedWeightedProgress / $totalBobotValue) * 100;
            }

            $summary[] = [
                'id_kategori'   => $catId,
                'nama_kategori' => $cat['nama_kategori'] ?? 'Tanpa Kategori',
                'bobot_pct'     => round($bobotKategoriPct, 2),
                'planned_pct'   => round($plannedKategoriPct, 2),
                'actual_pct'    => round($actualKategoriPct, 2),
            ];
        }

        return $summary;
    }

    public function getChartData(int $idProject): array
    {
        $rap = $this->rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            return ['labels' => [], 'planned' => [], 'actual' => []];
        }

        $rapId = (int) $rap['id_rap'];
        $totalRapValue = (float) $rap['total_keseluruhan'];
        if ($totalRapValue <= 0) {
            return ['labels' => [], 'planned' => [], 'actual' => []];
        }

        $dates = $this->db->table('rap_detail')
            ->select('MIN(start_date) as start, MAX(finish_date) as finish')
            ->where('id_rap', $rapId)
            ->where('start_date IS NOT NULL')
            ->where('finish_date IS NOT NULL')
            ->get()->getRowArray();

        if (empty($dates['start']) || empty($dates['finish'])) {
            return ['labels' => [], 'planned' => [], 'actual' => []];
        }

        $startDate = new \DateTime($dates['start']);
        $finishDate = new \DateTime($dates['finish']);
        
        $startDate->modify('monday this week');
        $finishDate->modify('sunday this week');

        $weeks = [];
        $current = clone $startDate;
        while ($current <= $finishDate) {
            $sunday = (clone $current)->modify('sunday this week');
            $weeks[] = $sunday->format('Y-m-d');
            $current->modify('+7 days');
        }

        $items = $this->db->table('rap_detail')
            ->select('id_rap_detail, total_keseluruhan, start_date, finish_date')
            ->where('id_rap', $rapId)
            ->where('pekerjaan IS NOT NULL')
            ->where('start_date IS NOT NULL')
            ->where('finish_date IS NOT NULL')
            ->get()->getResultArray();

        $realizations = $this->db->table('realisasi_pekerjaan rp')
            ->select('rp.tanggal, rp.volume_tercapai, rd.total_keseluruhan, rd.volume as volume_target')
            ->join('rap_detail rd', 'rd.id_rap_detail = rp.id_rap_detail')
            ->where('rd.id_rap', $rapId)
            ->get()->getResultArray();

        $plannedSeries = [];
        $actualSeries = [];
        $labels = [];
        
        $todayTs = strtotime(date('Y-m-d') . ' 23:59:59');

        foreach ($weeks as $index => $weekEnd) {
            $weekEndTs   = strtotime($weekEnd . ' 23:59:59');
            // Awal minggu = 6 hari sebelum akhir minggu (Senin)
            $weekStartTs = $weekEndTs - (6 * 86400);
            $labels[]    = "W" . ($index + 1);

            $cumPlannedValue = 0;
            foreach ($items as $item) {
                $startTs  = strtotime($item['start_date']);
                $finishTs = strtotime($item['finish_date']);
                $val      = (float) $item['total_keseluruhan'];

                if ($weekEndTs >= $finishTs) {
                    $cumPlannedValue += $val;
                } elseif ($weekEndTs >= $startTs) {
                    $totalDays = ($finishTs - $startTs) / 86400;
                    if ($totalDays > 0) {
                        $elapsed = ($weekEndTs - $startTs) / 86400;
                        $cumPlannedValue += ($val * ($elapsed / $totalDays));
                    }
                }
            }
            $plannedSeries[] = round(($cumPlannedValue / $totalRapValue) * 100, 2);

            // Sembunyikan actual untuk minggu yang belum terjadi
            if ($weekStartTs > $todayTs) {
                $actualSeries[] = null;
            } else {
                $cumActualValue = 0;
                foreach ($realizations as $r) {
                    $rTs = strtotime($r['tanggal']);
                    if ($rTs <= $weekEndTs) {
                        $volTarget = (float) $r['volume_target'];
                        if ($volTarget > 0) {
                            $progress = (float) $r['volume_tercapai'] / $volTarget;
                            $cumActualValue += ($progress * (float) $r['total_keseluruhan']);
                        }
                    }
                }
                $actualSeries[] = round(($cumActualValue / $totalRapValue) * 100, 2);
            }
        }

        return [
            'labels'  => $labels,
            'planned' => $plannedSeries,
            'actual'  => $actualSeries
        ];
    }

    public function getScheduleStatusFromChart(array $chart): array
    {
        $plannedSeries = $chart['planned'] ?? [];
        $actualSeries = $chart['actual'] ?? [];
        $latestActualIndex = $this->findLatestActualIndex($actualSeries);

        if ($latestActualIndex === null) {
            return [
                'spi_value'       => 1.00,
                'schedule_status' => 'On Time',
            ];
        }

        $evPct = (float) ($actualSeries[$latestActualIndex] ?? 0);
        $pvPct = (float) ($plannedSeries[$latestActualIndex] ?? 0);
        $spi = $pvPct > 0 ? ($evPct / $pvPct) : 1.0;

        return [
            'spi_value'       => round($spi, 2),
            'schedule_status' => $this->classifyScheduleStatus($spi),
        ];
    }

    public function getProjectMetrics(int $idProject, ?array $chart = null): array
    {
        $rap = $this->rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            return $this->defaultProjectMetrics();
        }

        $totalBudget = (float) ($rap['total_keseluruhan'] ?? 0);
        if ($totalBudget <= 0) {
            return $this->defaultProjectMetrics();
        }

        $chartData = $chart ?? $this->getChartData($idProject);
        $actualCost = $this->getActualCostValue($idProject);

        return $this->getProjectMetricsFromChart($chartData, $totalBudget, $actualCost);
    }

    public function getCategoryMetrics(int $idProject, int $categoryId): array
    {
        $rap = $this->rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            throw PageNotFoundException::forPageNotFound('Proyek tidak ditemukan.');
        }

        $rapId = (int) $rap['id_rap'];

        $items = $this->db->table('rap_detail')
            ->select('id_rap_detail, pekerjaan, volume, satuan, total_keseluruhan, start_date, finish_date')
            ->select('(SELECT COALESCE(SUM(volume_tercapai), 0) FROM realisasi_pekerjaan WHERE id_rap_detail = rap_detail.id_rap_detail) as total_tercapai')
            ->where('id_rap', $rapId)
            ->where('id_kategori', $categoryId)
            ->where('pekerjaan IS NOT NULL', null, false)
            ->where('pekerjaan !=', '')
            ->get()
            ->getResultArray();

        if (empty($items)) {
            return [
                'id_kategori'      => $categoryId,
                'bac_value'        => 0.0,
                'ev_value'         => 0.0,
                'pv_value'         => 0.0,
                'ac_value'         => 0.0,
                'actual_pct'       => 0.0,
                'planned_pct'      => 0.0,
                'spi_value'        => 1.0,
                'cpi_value'        => 1.0,
                'schedule_status'  => 'On Time',
                'cost_status'      => 'On Budget',
                'items'            => [],
            ];
        }

        $categoryBudget = 0.0;
        foreach ($items as $item) {
            $categoryBudget += (float) ($item['total_keseluruhan'] ?? 0);
        }

        $todayTs = strtotime(date('Y-m-d') . ' 23:59:59');
        $bac = $categoryBudget;
        $ev = 0.0;
        $pv = 0.0;
        $processedItems = [];
        $catStartTs = null;
        $catFinishTs = null;

        foreach ($items as $item) {
            $itemBudget = (float) ($item['total_keseluruhan'] ?? 0);
            $targetVol = (float) ($item['volume'] ?? 0);
            $actualVol = (float) ($item['total_tercapai'] ?? 0);

            $actualPct = 0.0;
            if ($targetVol > 0) {
                $actualPct = min(1, $actualVol / $targetVol);
            }
            $ev += ($actualPct * $itemBudget);

            $plannedPct = 0.0;
            $startTs = !empty($item['start_date']) ? strtotime($item['start_date'] . ' 00:00:00') : null;
            $finishTs = !empty($item['finish_date']) ? strtotime($item['finish_date'] . ' 23:59:59') : null;

            if ($startTs !== null && $finishTs !== null) {
                if ($todayTs >= $finishTs) {
                    $plannedPct = 1.0;
                } elseif ($todayTs >= $startTs) {
                    $durationDays = ($finishTs - $startTs) / 86400;
                    if ($durationDays > 0) {
                        $elapsedDays = ($todayTs - $startTs) / 86400;
                        $plannedPct = min(1, max(0, $elapsedDays / $durationDays));
                    } else {
                        $plannedPct = 1.0;
                    }
                }
            }

            $pv += ($plannedPct * $itemBudget);

            if ($startTs !== null) {
                if ($catStartTs === null || $startTs < $catStartTs) {
                    $catStartTs = $startTs;
                }
            }
            if ($finishTs !== null) {
                if ($catFinishTs === null || $finishTs > $catFinishTs) {
                    $catFinishTs = $finishTs;
                }
            }

            $status = 'Belum Mulai';
            if ($actualPct >= 1) {
                $status = 'Selesai';
            } elseif ($actualPct > 0) {
                if ($actualPct < $plannedPct - 0.05) { 
                    $status = 'Terlambat';
                } else {
                    $status = 'Berjalan';
                }
            } elseif ($plannedPct > 0) {
                $status = 'Terlambat'; 
            }

            $processedItems[] = [
                'id_rap_detail'  => $item['id_rap_detail'],
                'pekerjaan'      => $item['pekerjaan'],
                'volume'         => $targetVol,
                'satuan'         => $item['satuan'],
                'total_tercapai' => $actualVol,
                'budget'         => $itemBudget,
                'bobot_pct'      => $categoryBudget > 0 ? ($itemBudget / $categoryBudget) * 100 : 0,
                'actual_pct'     => $actualPct * 100,
                'planned_pct'    => $plannedPct * 100,
                'status'         => $status
            ];
        }

        $ac = $this->getActualCostValue($idProject, $categoryId);
        $spi = $pv > 0 ? ($ev / $pv) : 1.0;
        $cpi = $ac > 0 ? ($ev / $ac) : 1.0;

        return [
            'id_kategori'      => $categoryId,
            'bac_value'        => round($bac, 2),
            'ev_value'         => round($ev, 2),
            'pv_value'         => round($pv, 2),
            'ac_value'         => round($ac, 2),
            'actual_pct'       => $bac > 0 ? round(($ev / $bac) * 100, 2) : 0.0,
            'planned_pct'      => $bac > 0 ? round(($pv / $bac) * 100, 2) : 0.0,
            'spi_value'        => round($spi, 2),
            'cpi_value'        => round($cpi, 2),
            'schedule_status'  => $this->classifyScheduleStatus($spi),
            'cost_status'      => $this->classifyCostStatus($cpi),
            'start_date'       => $catStartTs ? date('Y-m-d', $catStartTs) : null,
            'finish_date'      => $catFinishTs ? date('Y-m-d', $catFinishTs) : null,
            'items'            => $processedItems,
        ];
    }

    public function getCostChartData(int $idProject): array
    {
        $rap = $this->rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            return ['labels' => [], 'planned' => [], 'actual' => []];
        }

        $rapId = (int) $rap['id_rap'];

        $dates = $this->db->table('rap_detail')
            ->select('MIN(start_date) as start, MAX(finish_date) as finish')
            ->where('id_rap', $rapId)
            ->where('start_date IS NOT NULL')
            ->where('finish_date IS NOT NULL')
            ->get()->getRowArray();

        if (empty($dates['start']) || empty($dates['finish'])) {
            return ['labels' => [], 'planned' => [], 'actual' => []];
        }

        $startDate = new \DateTime($dates['start']);
        $finishDate = new \DateTime($dates['finish']);
        $startDate->modify('monday this week');
        $finishDate->modify('sunday this week');

        $weeks = [];
        $current = clone $startDate;
        while ($current <= $finishDate) {
            $weekStart = clone $current;
            $weekEnd = (clone $current)->modify('sunday this week');

            $weeks[] = [
                'start' => $weekStart->format('Y-m-d'),
                'end'   => $weekEnd->format('Y-m-d'),
            ];

            $current->modify('+7 days');
        }

        $items = $this->db->table('rap_detail')
            ->select('id_rap_detail, volume, total_keseluruhan, start_date, finish_date')
            ->where('id_rap', $rapId)
            ->where('pekerjaan IS NOT NULL')
            ->where('pekerjaan !=', '')
            ->where('start_date IS NOT NULL')
            ->where('finish_date IS NOT NULL')
            ->get()->getResultArray();

        if (empty($items) || empty($weeks)) {
            return ['labels' => [], 'planned' => [], 'actual' => []];
        }

        $itemMeta = [];
        foreach ($items as $item) {
            $itemMeta[(int) $item['id_rap_detail']] = [
                'total'     => (float) $item['total_keseluruhan'],
                'volume'    => (float) $item['volume'],
                'start_ts'  => strtotime($item['start_date'] . ' 00:00:00'),
                'finish_ts' => strtotime($item['finish_date'] . ' 23:59:59'),
            ];
        }

        // Ambil seluruh pengeluaran SDM diurutkan per tanggal
        $costRows = $this->db->table('realisasi_sdm_item rsi')
            ->select('rs.tanggal, COALESCE(SUM(rsi.total_harga), 0) as daily_cost', false)
            ->join('realisasi_sdm rs', 'rs.id_realisasi_sdm = rsi.id_realisasi_sdm')
            ->where('rs.id_project', $idProject)
            ->where('rsi.total_harga >', 0)
            ->where('rs.tanggal IS NOT NULL')
            ->groupBy('rs.tanggal')
            ->orderBy('rs.tanggal', 'ASC')
            ->get()
            ->getResultArray();

        // Index biaya per tanggal
        $costByDate = [];
        foreach ($costRows as $row) {
            $ts = strtotime($row['tanggal'] . ' 23:59:59');
            $costByDate[$ts] = (float) $row['daily_cost'];
        }
        ksort($costByDate);
        $costDates = array_keys($costByDate);

        $labels = [];
        $plannedSeries = [];
        $actualSeries = [];

        $todayEndTs = strtotime(date('Y-m-d') . ' 23:59:59');
        $costIndex = 0;
        $totalCostDates = count($costDates);
        $actualCumValue = 0.0;

        foreach ($weeks as $index => $week) {
            $weekStartTs = strtotime($week['start'] . ' 00:00:00');
            $weekEndTs = strtotime($week['end'] . ' 23:59:59');
            $labels[] = 'W' . ($index + 1);

            $plannedCumValue = 0.0;
            foreach ($itemMeta as $meta) {
                if ($weekEndTs >= $meta['finish_ts']) {
                    $plannedCumValue += $meta['total'];
                    continue;
                }

                if ($weekEndTs < $meta['start_ts']) {
                    continue;
                }

                $totalDuration = ($meta['finish_ts'] - $meta['start_ts']) / 86400;
                if ($totalDuration <= 0) {
                    $plannedCumValue += $meta['total'];
                    continue;
                }

                $elapsedDuration = ($weekEndTs - $meta['start_ts']) / 86400;
                $plannedCumValue += ($meta['total'] * min(1, max(0, $elapsedDuration / $totalDuration)));
            }
            $plannedSeries[] = round($plannedCumValue, 2);

            if ($weekStartTs > $todayEndTs) {
                $actualSeries[] = null;
                continue;
            }

            // Akumulasi biaya SDM yang sudah diinput s.d. akhir minggu ini
            while ($costIndex < $totalCostDates && $costDates[$costIndex] <= $weekEndTs) {
                $actualCumValue += $costByDate[$costDates[$costIndex]];
                $costIndex++;
            }

            $actualSeries[] = round($actualCumValue, 2);
        }

        return [
            'labels'  => $labels,
            'planned' => $plannedSeries,
            'actual'  => $actualSeries,
        ];
    }

    private function defaultProjectMetrics(): array
    {
        return [
            'bac_value'       => 0.0,
            'ev_value'        => 0.0,
            'pv_value'        => 0.0,
            'ac_value'        => 0.0,
            'spi_value'       => 1.0,
            'cpi_value'       => 1.0,
            'schedule_status' => 'On Time',
            'cost_status'     => 'On Budget',
        ];
    }

    private function getProjectMetricsFromChart(array $chart, float $totalBudget, float $actualCost): array
    {
        $plannedSeries = $chart['planned'] ?? [];
        $actualSeries = $chart['actual'] ?? [];

        $latestActualIndex = $this->findLatestActualIndex($actualSeries);
        if ($latestActualIndex === null) {
            return [
                'bac_value'       => round($totalBudget, 2),
                'ev_value'        => 0.0,
                'pv_value'        => 0.0,
                'ac_value'        => round($actualCost, 2),
                'spi_value'       => 1.0,
                'cpi_value'       => 1.0,
                'schedule_status' => 'On Time',
                'cost_status'     => 'On Budget',
            ];
        }

        $actualPct = (float) ($actualSeries[$latestActualIndex] ?? 0);
        $plannedPct = (float) ($plannedSeries[$latestActualIndex] ?? 0);

        $ev = ($actualPct / 100) * $totalBudget;
        $pv = ($plannedPct / 100) * $totalBudget;

        $spi = $pv > 0 ? ($ev / $pv) : 1.0;
        $cpi = $actualCost > 0 ? ($ev / $actualCost) : 1.0;

        return [
            'bac_value'       => round($totalBudget, 2),
            'ev_value'        => round($ev, 2),
            'pv_value'        => round($pv, 2),
            'ac_value'        => round($actualCost, 2),
            'spi_value'       => round($spi, 2),
            'cpi_value'       => round($cpi, 2),
            'schedule_status' => $this->classifyScheduleStatus($spi),
            'cost_status'     => $this->classifyCostStatus($cpi),
        ];
    }

    private function findLatestActualIndex(array $actualSeries): ?int
    {
        for ($i = count($actualSeries) - 1; $i >= 0; $i--) {
            if ($actualSeries[$i] !== null) {
                return $i;
            }
        }

        return null;
    }

    private function classifyScheduleStatus(float $spi): string
    {
        if ($spi > 1.05) {
            return 'Early';
        }
        if ($spi >= 0.95) {
            return 'On Time';
        }
        if ($spi >= 0.85) {
            return 'Slightly Delay';
        }

        return 'Delayed';
    }

    private function classifyCostStatus(float $cpi): string
    {
        if ($cpi > 1.05) {
            return 'Under Budget';
        }
        if ($cpi >= 0.95) {
            return 'On Budget';
        }
        if ($cpi >= 0.85) {
            return 'Slightly Over';
        }

        return 'Overrun';
    }

    private function getActualCostValue(int $idProject, ?int $categoryId = null): float
    {
        $builder = $this->db->table('realisasi_sdm_item rsi')
            ->select('COALESCE(SUM(rsi.total_harga), 0) as total_ac', false)
            ->join('realisasi_sdm rs', 'rs.id_realisasi_sdm = rsi.id_realisasi_sdm')
            ->where('rs.id_project', $idProject)
            ->where('rsi.qty >', 0);

        $row = $builder->get()->getRowArray();
        return (float) ($row['total_ac'] ?? 0);
    }


    private function buildItemPriceMap(int $idProject, ?int $categoryId = null): array
    {
        $builder = $this->db->table('rap_detail_item rdi')
            ->select('rdi.jenis_item, rdi.nama_item, rdi.satuan, rdi.harga_satuan')
            ->join('rap_detail rd', 'rd.id_rap_detail = rdi.id_rap_detail')
            ->join('rap r', 'r.id_rap = rd.id_rap')
            ->where('r.id_project', $idProject)
            ->where('rdi.harga_satuan >', 0);

        if ($categoryId !== null) {
            $builder->where('rd.id_kategori', $categoryId);
        }

        $rows = $builder->get()->getResultArray();

        $map    = [];
        $counts = [];
        foreach ($rows as $row) {
            $price = (float) ($row['harga_satuan'] ?? 0);
            if ($price <= 0) {
                continue;
            }

            $key = $this->buildItemKey(
                (string) ($row['jenis_item'] ?? ''),
                (string) ($row['nama_item'] ?? ''),
                (string) ($row['satuan'] ?? '')
            );

            if (!isset($map[$key])) {
                $map[$key]    = $price;
                $counts[$key] = 1;
            } else {
                $map[$key]   += $price;
                $counts[$key]++;
            }
        }

        foreach ($map as $key => $total) {
            $map[$key] = $total / $counts[$key];
        }

        return $map;
    }


    private function buildItemKey(string $kategori, string $namaItem, string $satuan): string
    {
        $cat = strtolower(trim($kategori));

        if (in_array($cat, ['tenaga kerja', 'tenaga', 'tenaga kerja harian'], true)) {
            $cat = 'upah';
        }

        return implode('|', [
            $cat,
            strtolower(trim($namaItem)),
            strtolower(trim($satuan)),
        ]);
    }
}
