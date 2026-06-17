<?php

namespace App\Services;

use App\Models\PurchaseRequestModel;
use App\Models\PurchaseRequestItemModel;
use Config\Database;

class PengadaanService
{
    /**
     * Membuat auto-draft pengadaan (PR) berdasarkan kekurangan stok aktual dari sebuah permintaan.
     * Dipanggil ketika Gudang menekan "Auto Procure" dari Permintaan.
     */
    public function createAutoDraftFromPermintaan(int $permintaanId, int $userId): array
    {
        $db = Database::connect();
        $db->transStart();

        $permintaan = $db->table('permintaan')->where('id', $permintaanId)->get()->getRowArray();
        if (!$permintaan) {
            throw new \InvalidArgumentException("Permintaan tidak ditemukan.");
        }

        // Ambil detail permintaan dan join dengan stok_gudang
        $details = $db->table('permintaan_detail pd')
            ->select('pd.*, COALESCE(sg.stok_aktual, 0) as stok_aktual')
            ->join('stok_gudang sg', 'sg.id_barang = pd.id_barang', 'left')
            ->where('pd.id_permintaan', $permintaanId)
            ->get()
            ->getResultArray();

        $itemsKurang = [];
        foreach ($details as $det) {
            $stokAktual = (float)$det['stok_aktual'];
            $jumlahDiminta = (float)$det['jumlah'];
            if ($stokAktual < $jumlahDiminta) {
                $kurang = ceil($jumlahDiminta - $stokAktual);
                if ($kurang > 0) {
                    $itemsKurang[] = [
                        'id_barang'   => $det['id_barang'],
                        'volume'      => $kurang,
                        'keterangan'  => "Kekurangan stok untuk Permintaan: {$permintaan['nomor_permintaan']}"
                    ];
                }
            }
        }

        if (empty($itemsKurang)) {
            $db->transComplete();
            return [
                'status' => 'warning',
                'message' => 'Tidak ada item yang membutuhkan pengadaan (stok mencukupi).'
            ];
        }

        // Ambil id_perusahaan dari pengguna
        $user = $db->table('pengguna')->where('id_pengguna', $userId)->get()->getRowArray();
        $idPerusahaan = $user ? (int)$user['id_perusahaan'] : 0;
        if ($idPerusahaan === 0 && !empty($user['parent_id'])) {
            $idPerusahaan = (int)$user['parent_id'];
        }

        // Generate nomor PR
        $nomorPR = $this->generatePRNumber($idPerusahaan);

        $prModel = new PurchaseRequestModel();
        $prItemModel = new PurchaseRequestItemModel();

        // Insert header PR
        $headerData = [
            'id_perusahaan' => $idPerusahaan,
            'pr_number'     => $nomorPR,
            'request_date'  => date('Y-m-d'),
            'status'        => 'pending',
            'keterangan'    => 'Auto-draft dari Permintaan Nomor: ' . $permintaan['nomor_permintaan'],
            'created_by'    => $userId,
        ];

        $prId = $prModel->insert($headerData);

        // Insert details
        foreach ($itemsKurang as $item) {
            $prItemModel->insert([
                'pr_id'      => $prId,
                'id_barang'  => $item['id_barang'],
                'volume'     => $item['volume'],
                'status'     => 'pending',
                'keterangan' => $item['keterangan']
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Gagal membuat auto-draft pengadaan.");
        }

        return [
            'status' => 'success',
            'message' => 'Draft pengadaan otomatis berhasil dibuat.',
            'pr_number' => $nomorPR
        ];
    }

    /**
     * Membuat PR manual dari input Gudang
     */
    public function createManualPurchaseRequest(int $idPerusahaan, int $userId, array $items, string $keterangan = ''): array
    {
        if (empty($items)) {
            throw new \InvalidArgumentException("Daftar barang tidak boleh kosong.");
        }

        $db = Database::connect();
        $db->transStart();

        $nomorPR = $this->generatePRNumber($idPerusahaan);

        $prModel = new PurchaseRequestModel();
        $prItemModel = new PurchaseRequestItemModel();

        $headerData = [
            'id_perusahaan' => $idPerusahaan,
            'pr_number'     => $nomorPR,
            'request_date'  => date('Y-m-d'),
            'status'        => 'pending',
            'keterangan'    => $keterangan,
            'created_by'    => $userId,
        ];

        $prId = $prModel->insert($headerData);

        foreach ($items as $item) {
            if (empty($item['id_barang']) || empty($item['volume']) || $item['volume'] <= 0) {
                throw new \InvalidArgumentException("Data barang dan volume tidak valid.");
            }

            $prItemModel->insert([
                'pr_id'      => $prId,
                'id_barang'  => $item['id_barang'],
                'volume'     => $item['volume'],
                'status'     => 'pending',
                'keterangan' => $item['keterangan'] ?? ''
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Gagal membuat pengadaan.");
        }

        return [
            'status' => 'success',
            'message' => 'Pengadaan berhasil dibuat.',
            'pr_number' => $nomorPR
        ];
    }

    public function getStats(int $idPerusahaan): array
    {
        $prModel = new PurchaseRequestModel();
        
        $total = $prModel->where('id_perusahaan', $idPerusahaan)->countAllResults();
        $pending = $prModel->where('id_perusahaan', $idPerusahaan)->where('status', 'pending')->countAllResults();
        $processing = $prModel->where('id_perusahaan', $idPerusahaan)->where('status', 'ordered')->countAllResults();
        $completed = $prModel->where('id_perusahaan', $idPerusahaan)->where('status', 'completed')->countAllResults();

        // Kritis count
        $db = Database::connect();
        $kritis = $db->table('stok_gudang')
            ->where('id_perusahaan', $idPerusahaan)
            ->where('stok_aktual <= stok_minimum')
            ->countAllResults();

        return [
            'total'      => $total,
            'pending'    => $pending,
            'processing' => $processing,
            'completed'  => $completed,
            'kritis'     => $kritis,
        ];
    }

    public function getPurchaseRequestList(int $idPerusahaan, string $status = 'all', string $search = ''): array
    {
        $db = Database::connect();
        $builder = $db->table('purchase_requests pr')
            ->select('pr.*, p.nama_pengguna as nama_pembuat, COUNT(pri.id) as total_items')
            ->join('pengguna p', 'p.id_pengguna = pr.created_by', 'left')
            ->join('purchase_request_items pri', 'pri.pr_id = pr.id', 'left')
            ->where('pr.id_perusahaan', $idPerusahaan)
            ->groupBy('pr.id')
            ->orderBy('pr.created_at', 'DESC');

        if ($status !== 'all') {
            $builder->where('pr.status', $status);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('pr.pr_number', $search)
                ->orLike('pr.keterangan', $search)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function getPurchaseRequestDetail(int $prId, int $idPerusahaan): ?array
    {
        $db = Database::connect();
        
        $pr = $db->table('purchase_requests pr')
            ->select('pr.*, p.nama_pengguna as nama_pembuat')
            ->join('pengguna p', 'p.id_pengguna = pr.created_by', 'left')
            ->where('pr.id', $prId)
            ->where('pr.id_perusahaan', $idPerusahaan)
            ->get()
            ->getRowArray();

        if (!$pr) return null;

        $items = $db->table('purchase_request_items pri')
            ->select('pri.*, mb.kode_barang, mb.nama_barang, mb.satuan, mb.spesifikasi, mb.merk')
            ->join('master_barang mb', 'mb.id = pri.id_barang', 'left')
            ->where('pri.pr_id', $prId)
            ->get()
            ->getResultArray();

        $pr['items'] = $items;
        return $pr;
    }

    public function getItemsKritis(int $idPerusahaan): array
    {
        $db = Database::connect();
        return $db->table('stok_gudang sg')
            ->select('sg.*, mb.kode_barang, mb.nama_barang, mb.satuan, mb.spesifikasi, mb.merk')
            ->join('master_barang mb', 'mb.id = sg.id_barang', 'inner')
            ->where('sg.id_perusahaan', $idPerusahaan)
            ->where('sg.stok_aktual <= sg.stok_minimum')
            ->get()
            ->getResultArray();
    }

    public function searchBarang(int $idPerusahaan, string $keyword): array
    {
        $db = Database::connect();
        $builder = $db->table('master_barang mb')
            ->select('mb.id as id_barang, mb.kode_barang, mb.nama_barang, mb.satuan, mb.spesifikasi, COALESCE(sg.stok_aktual, 0) as stok_aktual, COALESCE(sg.stok_minimum, 0) as stok_minimum')
            ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = ' . $db->escape($idPerusahaan), 'left')
            ->where('mb.id_perusahaan', $idPerusahaan);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('mb.nama_barang', $keyword)
                ->orLike('mb.kode_barang', $keyword)
            ->groupEnd();
        }

        return $builder->limit(50)
            ->orderBy('mb.nama_barang', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function deletePurchaseRequest(int $prId, int $idPerusahaan): array
    {
        $db = Database::connect();
        
        $prModel = new PurchaseRequestModel();
        $pr = $prModel->where('id', $prId)->where('id_perusahaan', $idPerusahaan)->first();
        
        if (!$pr) {
            throw new \InvalidArgumentException("Data pengadaan tidak ditemukan.");
        }

        if (!in_array($pr['status'], ['draft', 'pending'])) {
            throw new \InvalidArgumentException("Hanya pengajuan dengan status pending/draft yang dapat dibatalkan.");
        }

        $db->transStart();

        // Delete items
        $db->table('purchase_request_items')->where('pr_id', $prId)->delete();
        // Delete header
        $prModel->delete($prId);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Gagal menghapus pengadaan.");
        }

        return [
            'status' => 'success',
            'message' => 'Pengajuan berhasil dihapus/dibatalkan.'
        ];
    }

    private function generatePRNumber(int $idPerusahaan): string
    {
        $db = Database::connect();
        $dateStr = date('Ymd');
        $prefix = "PR/{$dateStr}/";
        
        $latest = $db->table('purchase_requests')
            ->select('pr_number')
            ->where('id_perusahaan', $idPerusahaan)
            ->like('pr_number', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($latest) {
            // Explode and get sequence
            $parts = explode('/', $latest['pr_number']);
            $numPart = (int) end($parts);
            $nextNum = str_pad($numPart + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }
        
        return $prefix . $nextNum;
    }
}
