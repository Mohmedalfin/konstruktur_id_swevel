<?php

namespace App\Services;

use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;
use App\Models\ProyekModel;
use App\Models\PermintaanStatusLogModel;
use App\Models\StokGudangModel;
use App\Helpers\InventoryHelper;
use Config\Database;

class PermintaanService
{
    protected $permintaanModel;
    protected $permintaanDetailModel;
    protected $proyekModel;
    protected $permintaanStatusLogModel;

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->permintaanDetailModel = new PermintaanDetailModel();
        $this->proyekModel = new ProyekModel();
        $this->permintaanStatusLogModel = new PermintaanStatusLogModel();
    }

    /**
     * Get Stats: Total, Menunggu (pending), Diproses (disetujui), Terkirim (selesai)
     */
    public function getStats(?string $month = null): array
    {
        $db = Database::connect();
        
        $getStat = function($status = null) use ($db, $month) {
            $builder = $db->table('permintaan');
            if ($month) {
                $builder->like('tanggal_permintaan', $month . '-', 'after');
            }
            if ($status) {
                $builder->where('status', $status);
            }
            return $builder->countAllResults();
        };

        return [
            'total'     => $getStat(),
            'pending'   => $getStat('pending'),
            'disetujui' => $getStat('disetujui'),
            'diproses'  => $getStat('diproses'),
            'selesai'   => $getStat('selesai'),
            'ditolak'   => $getStat('ditolak'),
        ];
    }

    /**
     * Get Request List (Header info, related project names, total item count)
     */
    public function getList(?string $statusFilter = null, ?string $month = null): array
    {
        $db = Database::connect();
        $builder = $db->table('permintaan p')
            ->select('p.*, u.nama_pengguna as pemohon_nama')
            ->join('pengguna u', 'u.id_pengguna = p.pemohon_id', 'left');

        if ($month) {
            $builder->like('p.tanggal_permintaan', $month . '-', 'after');
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $builder->where('p.status', $statusFilter);
        }

        $requests = $builder->orderBy('p.tanggal_permintaan', 'DESC')
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($requests as $req) {
            $idPermintaan = $req['id'];

            // Fetch unique project names involved in this request
            $projects = $db->table('permintaan_detail pd')
                ->select('pr.id_project, pr.nama_proyek')
                ->join('projects pr', 'pr.id_project = pd.id_project')
                ->where('pd.id_permintaan', $idPermintaan)
                ->distinct()
                ->get()
                ->getResultArray();

            // Count total items
            $itemCount = $db->table('permintaan_detail')
                ->where('id_permintaan', $idPermintaan)
                ->countAllResults();

            $req['projects'] = $projects;
            $req['item_count'] = $itemCount;
            $result[] = $req;
        }

        return $result;
    }

    /**
     * Get request details grouped by project
     */
    public function getDetail(int $id): ?array
    {
        $db = Database::connect();
        $req = $db->table('permintaan p')
            ->select('p.*, u.nama_pengguna as pemohon_nama')
            ->join('pengguna u', 'u.id_pengguna = p.pemohon_id', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

        if (!$req) {
            return null;
        }

        // Fetch detail items
        $details = $db->table('permintaan_detail pd')
            ->select('pd.*, pr.nama_proyek, pr.lokasi_proyek, rdi.spesifikasi, rdi.merk, COALESCE(sg.stok_aktual, 0) as stok_aktual')
            ->join('projects pr', 'pr.id_project = pd.id_project')
            ->join('rap_detail_item rdi', 'rdi.id_rap_detail_item = pd.id_rap_detail_item', 'left')
            ->join('stok_gudang sg', 'sg.id_barang = pd.id_barang', 'left')
            ->where('pd.id_permintaan', $id)
            ->orderBy('pr.nama_proyek', 'ASC')
            ->orderBy('pd.nama_barang', 'ASC')
            ->get()
            ->getResultArray();

        // Fetch status logs
        $statusLogs = $db->table('permintaan_status_log psl')
            ->select('psl.*, u.nama_pengguna as nama_pengubah')
            ->join('pengguna u', 'u.id_pengguna = psl.diubah_oleh', 'left')
            ->where('psl.id_permintaan', $id)
            ->orderBy('psl.created_at', 'ASC')
            ->orderBy('psl.id', 'ASC')
            ->get()
            ->getResultArray();
        
        $req['status_logs'] = $statusLogs;

        // Group by project
        $groupedProjects = [];
        foreach ($details as $det) {
            $idProject = $det['id_project'];
            if (!isset($groupedProjects[$idProject])) {
                $groupedProjects[$idProject] = [
                    'id_project'    => $idProject,
                    'nama_proyek'   => $det['nama_proyek'],
                    'lokasi_proyek' => $det['lokasi_proyek'] ?? '-',
                    'items'         => [],
                ];
            }
            $groupedProjects[$idProject]['items'][] = [
                'id'                 => (int) $det['id'],
                'id_rap_detail_item' => $det['id_rap_detail_item'] ? (int) $det['id_rap_detail_item'] : null,
                'nama_barang'        => $det['nama_barang'],
                'jumlah'             => (float) $det['jumlah'],
                'satuan'             => $det['satuan'],
                'spesifikasi'        => $det['spesifikasi'] ?? '-',
                'merk'               => $det['merk'] ?? '-',
                'kategori'           => $det['jenis_item'] ?? 'Bahan',
                'keterangan'         => $det['keterangan'] ?? '-',
                'stok_aktual'        => (float) ($det['stok_aktual'] ?? 0),
            ];
        }

        $req['projects'] = array_values($groupedProjects);
        return $req;
    }

    /**
     * Store new request with detail items inside transaction
     */
    public function storeRequest(int $pemohonId, array $data): int
    {
        $db = Database::connect();
        $db->transStart();

        // 1. Generate nomor_permintaan
        // Format: REQ/YYYYMMDD/XXXX
        $dateStr = date('Ymd');
        $prefix = "REQ/{$dateStr}/";
        
        $latest = $db->table('permintaan')
            ->select('nomor_permintaan')
            ->like('nomor_permintaan', $prefix, 'after')
            ->orderBy('nomor_permintaan', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($latest) {
            $numPart = (int) substr($latest['nomor_permintaan'], -4);
            $nextNum = str_pad($numPart + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }
        $nomorPermintaan = $prefix . $nextNum;

        // 2. Validate items against RAP sisa_volume and calculate over-limits
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new \InvalidArgumentException("Item permintaan tidak boleh kosong.");
        }

        $projectRapItemsCache = [];
        $cumulativeRequestQty = [];
        $isGlobalOverLimit = 0;
        $processedItems = [];
        
        foreach ($data['items'] as $item) {
            $idProject = (int) $item['id_project'];
            $idRapDetailItem = !empty($item['id_rap_detail_item']) ? (int) $item['id_rap_detail_item'] : null;
            $jumlah = (float) $item['jumlah'];
            $namaBarang = trim($item['nama_barang']);
            
            $isOverLimit = 0;
            $jumlahOverLimit = 0;

            if ($idRapDetailItem) {
                if (!isset($cumulativeRequestQty[$idRapDetailItem])) {
                    $cumulativeRequestQty[$idRapDetailItem] = 0;
                }
                
                $qtyBeforeThis = $cumulativeRequestQty[$idRapDetailItem];
                $cumulativeRequestQty[$idRapDetailItem] += $jumlah;

                if (!isset($projectRapItemsCache[$idProject])) {
                    $projectRapItemsCache[$idProject] = $this->getProjectRapItems($idProject);
                }

                $rapItem = null;
                foreach ($projectRapItemsCache[$idProject] as $ri) {
                    if ((int)$ri['id_rap_detail_item'] === $idRapDetailItem) {
                        $rapItem = $ri;
                        break;
                    }
                }

                if ($rapItem) {
                    $sisaVolume = (float) $rapItem['sisa_volume'];
                    $availableForThisItem = $sisaVolume - $qtyBeforeThis;
                    
                    if ($jumlah > max(0, $availableForThisItem)) {
                        $isOverLimit = 1;
                        $jumlahOverLimit = $jumlah - max(0, $availableForThisItem);
                        $isGlobalOverLimit = 1;
                    }
                }
            }
            
            $processedItems[] = array_merge($item, [
                'is_over_limit' => $isOverLimit,
                'jumlah_over_limit' => $jumlahOverLimit
            ]);
        }
        
        if ($isGlobalOverLimit && empty(trim((string)($data['justifikasi_over_limit'] ?? '')))) {
            throw new \InvalidArgumentException("Justifikasi over-limit wajib diisi untuk permintaan melebihi RAP.");
        }

        // 3. Insert Header
        $headerData = [
            'nomor_permintaan'       => $nomorPermintaan,
            'tanggal_permintaan'     => date('Y-m-d'),
            'pemohon_id'             => $pemohonId,
            'status'                 => 'pending',
            'is_over_limit'          => $isGlobalOverLimit,
            'justifikasi_over_limit' => $isGlobalOverLimit ? trim((string)($data['justifikasi_over_limit'] ?? '')) : null,
            'keterangan'             => $data['keterangan'] ?? null,
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'),
        ];

        $db->table('permintaan')->insert($headerData);
        $permintaanId = $db->insertID();

        // 4. Insert Details
        foreach ($processedItems as $item) {
            $idProject = (int) $item['id_project'];
            $namaBarang = trim($item['nama_barang']);
            $satuan = trim($item['satuan']);
            $jenisItem = !empty($item['kategori']) ? trim($item['kategori']) : (!empty($item['jenis_item']) ? trim($item['jenis_item']) : 'Bahan');
            $keterangan = !empty($item['keterangan']) ? trim($item['keterangan']) : null;

            // Resolve Master Barang (Generate Kode)
            $idBarang = null;
            if ($idProject > 0) {
                $idBarang = InventoryHelper::resolveMasterBarang($idProject, $jenisItem, $namaBarang, null, null, $satuan);
            }

            $detailData = [
                'id_permintaan'      => $permintaanId,
                'id_project'         => $idProject,
                'id_rap_detail_item' => !empty($item['id_rap_detail_item']) ? (int) $item['id_rap_detail_item'] : null,
                'id_barang'          => $idBarang,
                'nama_barang'        => $namaBarang,
                'jumlah'             => (float) $item['jumlah'],
                'satuan'             => $satuan,
                'jenis_item'         => $jenisItem,
                'is_over_limit'      => $item['is_over_limit'],
                'jumlah_over_limit'  => $item['jumlah_over_limit'],
                'keterangan'         => $keterangan,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ];

            $db->table('permintaan_detail')->insert($detailData);
        }

        // 4. Insert Initial Status Log
        $logData = [
            'id_permintaan' => $permintaanId,
            'status'        => 'pending',
            'diubah_oleh'   => $pemohonId,
            'keterangan'    => 'Permintaan dibuat',
            'created_at'    => date('Y-m-d H:i:s'),
        ];
        $db->table('permintaan_status_log')->insert($logData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Gagal menyimpan data permintaan.");
        }

        return $permintaanId;
    }

    /**
     * Update status of request
     */
    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['draft', 'pending', 'disetujui', 'diproses', 'ditolak', 'selesai'])) {
            throw new \InvalidArgumentException("Status tidak valid.");
        }

        $db = Database::connect();
        $db->transStart();

        $req = $this->permintaanModel->find($id);
        if (!$req) {
            throw new \InvalidArgumentException("Permintaan tidak ditemukan.");
        }

        $stokTerpotong = (int) ($req['stok_terpotong'] ?? 0);
        $shouldDeduct = in_array($status, ['diproses', 'selesai']);
        $shouldRestore = in_array($status, ['pending', 'ditolak']);

        if ($shouldDeduct && $stokTerpotong === 0) {
            // Deduct stock
            $details = $db->table('permintaan_detail')->where('id_permintaan', $id)->get()->getResultArray();
            foreach ($details as $det) {
                if ($det['id_barang']) {
                    $db->table('stok_gudang')
                       ->where('id_barang', $det['id_barang'])
                       ->set('stok_aktual', 'stok_aktual - ' . (float)$det['jumlah'], false)
                       ->set('updated_at', date('Y-m-d H:i:s'))
                       ->update();
                }
            }
            $this->permintaanModel->update($id, ['stok_terpotong' => 1]);
        } elseif ($shouldRestore && $stokTerpotong === 1) {
            // Restore stock
            $details = $db->table('permintaan_detail')->where('id_permintaan', $id)->get()->getResultArray();
            foreach ($details as $det) {
                if ($det['id_barang']) {
                    $db->table('stok_gudang')
                       ->where('id_barang', $det['id_barang'])
                       ->set('stok_aktual', 'stok_aktual + ' . (float)$det['jumlah'], false)
                       ->set('updated_at', date('Y-m-d H:i:s'))
                       ->update();
                }
            }
            $this->permintaanModel->update($id, ['stok_terpotong' => 0]);
        }

        $updated = $this->permintaanModel->update($id, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($updated) {
            $pemohonId = session()->get('id_pengguna') ?? session()->get('id_user') ?? null;
            
            $keteranganMap = [
                'pending'   => 'Permintaan dibuat',
                'disetujui' => 'Permintaan diterima oleh gudang',
                'diproses'  => 'Permintaan sedang diproses oleh gudang (Stok dipotong)',
                'selesai'   => 'Permintaan selesai (Telah diterima di lapangan)',
                'ditolak'   => 'Permintaan ditolak',
            ];
            $keterangan = $keteranganMap[$status] ?? ('Status diubah menjadi ' . $status);

            if ($shouldRestore && $stokTerpotong === 1) {
                $keterangan .= ' (Stok dikembalikan)';
            }

            $db->table('permintaan_status_log')->insert([
                'id_permintaan' => $id,
                'status'        => $status,
                'diubah_oleh'   => $pemohonId,
                'keterangan'    => $keterangan,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Get unique materials and tools from a project's RAP
     */
    public function getProjectRapItems(int $projectId): array
    {
        $db = Database::connect();

        $budgetItems = $db->table('rap_detail_item rdi')
            ->select('MIN(rdi.id_rap_detail_item) as id_rap_detail_item, rdi.nama_item as nama, rdi.satuan, rdi.spesifikasi, rdi.merk, rdi.jenis_item as kategori')
            ->select('SUM(rd.volume * rdi.koefisien) as qty_budget', false)
            ->select('COALESCE(MAX(sg.stok_aktual), 0) as stok_aktual', false)
            ->join('rap_detail rd', 'rd.id_rap_detail = rdi.id_rap_detail')
            ->join('rap r', 'r.id_rap = rd.id_rap')
            ->join('projects p', 'p.id_project = r.id_project')
            ->join('master_barang mb', 'mb.nama_barang COLLATE utf8mb4_general_ci = rdi.nama_item COLLATE utf8mb4_general_ci AND mb.merk COLLATE utf8mb4_general_ci = COALESCE(rdi.merk, \'Tanpa Merk\') COLLATE utf8mb4_general_ci AND mb.spesifikasi COLLATE utf8mb4_general_ci = COALESCE(rdi.spesifikasi, \'-\') COLLATE utf8mb4_general_ci AND mb.id_perusahaan = p.id_pengguna', 'left', false)
            ->join('stok_gudang sg', 'sg.id_barang = mb.id', 'left')
            ->where('r.id_project', $projectId)
            ->groupStart()
                ->where('rdi.jenis_item', 'Bahan')
                ->orWhere('rdi.jenis_item', 'Alat')
            ->groupEnd()
            ->groupBy('rdi.nama_item, rdi.satuan, rdi.spesifikasi, rdi.merk, rdi.jenis_item')
            ->orderBy('rdi.nama_item', 'ASC')
            ->get()
            ->getResultArray();

        $usageItems = $db->table('permintaan_detail pd')
            ->select('pd.nama_barang as nama, pd.satuan, rdi.spesifikasi, rdi.merk')
            ->select('SUM(pd.jumlah) as total_usage', false)
            ->join('permintaan p', 'p.id = pd.id_permintaan')
            ->join('rap_detail_item rdi', 'rdi.id_rap_detail_item = pd.id_rap_detail_item', 'left')
            ->where('pd.id_project', $projectId)
            ->whereIn('p.status', ['pending', 'disetujui', 'selesai'])
            ->groupBy('pd.nama_barang, pd.satuan, rdi.spesifikasi, rdi.merk')
            ->get()
            ->getResultArray();

        $usageMap = [];
        foreach ($usageItems as $u) {
            $key = strtolower(trim($u['nama'] . '|' . $u['satuan'] . '|' . $u['spesifikasi'] . '|' . $u['merk']));
            $usageMap[$key] = (float) $u['total_usage'];
        }

        foreach ($budgetItems as &$item) {
            $key = strtolower(trim($item['nama'] . '|' . $item['satuan'] . '|' . $item['spesifikasi'] . '|' . $item['merk']));
            $used = $usageMap[$key] ?? 0;
            
            $item['qty_budget']  = (float) $item['qty_budget'];
            $item['qty_used']    = $used;
            $item['sisa_volume'] = $item['qty_budget'] - $used;
        }

        return $budgetItems;
    }

    /**
     * Delete a pending request
     */
    public function deleteRequest(int $id): bool
    {
        $db = Database::connect();
        $db->transStart();

        $req = $this->permintaanModel->find($id);
        if (!$req) {
            throw new \InvalidArgumentException("Permintaan tidak ditemukan.");
        }

        if ($req['status'] !== 'pending') {
            throw new \InvalidArgumentException("Hanya permintaan dengan status Menunggu (pending) yang dapat dibatalkan/dihapus.");
        }

        $db->table('permintaan_detail')->where('id_permintaan', $id)->delete();
        $this->permintaanModel->delete($id);

        $db->transComplete();
    }

    /**
     * Get Deviasi (Over-limit) Report
     */
    public function getDeviasiReport(?int $idProject = null, ?string $month = null): array
    {
        $db = Database::connect();
        $builder = $db->table('permintaan_detail pd')
            ->select('pd.*, p.nomor_permintaan, p.tanggal_permintaan, p.status, p.justifikasi_over_limit, pr.nama_proyek, rdi.harga_satuan')
            ->join('permintaan p', 'p.id = pd.id_permintaan')
            ->join('projects pr', 'pr.id_project = pd.id_project')
            ->join('rap_detail_item rdi', 'rdi.id_rap_detail_item = pd.id_rap_detail_item', 'left')
            ->where('pd.is_over_limit', 1)
            ->whereIn('p.status', ['pending', 'disetujui', 'selesai']); // active status

        if ($idProject) {
            $builder->where('pd.id_project', $idProject);
        }

        if ($month) {
            $builder->like('p.tanggal_permintaan', $month . '-', 'after');
        }

        $items = $builder->orderBy('p.tanggal_permintaan', 'DESC')->get()->getResultArray();

        $totalKelebihan = 0;
        foreach ($items as &$item) {
            $hargaSatuan = (float)($item['harga_satuan'] ?? 0);
            $jumlahOver = (float)$item['jumlah_over_limit'];
            $kerugian = $hargaSatuan * $jumlahOver;
            $item['kerugian_margin'] = $kerugian;
            $totalKelebihan += $kerugian;
        }

        return [
            'items' => $items,
            'total_kerugian' => $totalKelebihan
        ];
    }
}
