<?php

namespace App\Services;

use App\Models\PermintaanModel;
use App\Models\PermintaanDetailModel;
use Config\Database;

class PengadaanService
{
    /**
     * Membuat auto-draft pengadaan berdasarkan kekurangan stok aktual dari sebuah permintaan
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
                $kurang = $jumlahDiminta - $stokAktual;
                $itemsKurang[] = [
                    'id_barang'   => $det['id_barang'],
                    'nama_barang' => $det['nama_barang'],
                    'jumlah'      => $kurang,
                    'satuan'      => $det['satuan'],
                    'spesifikasi' => $det['spesifikasi'] ?? null,
                    'merk'        => $det['merk'] ?? null,
                ];
            }
        }

        if (empty($itemsKurang)) {
            $db->transComplete();
            return [
                'status' => 'warning',
                'message' => 'Tidak ada item yang membutuhkan pengadaan (stok mencukupi).'
            ];
        }

        // Generate nomor pengadaan (PRC/YYYYMMDD/XXXX)
        $dateStr = date('Ymd');
        $prefix = "PRC/{$dateStr}/";
        $latest = $db->table('pengadaan')
            ->select('nomor_pengadaan')
            ->like('nomor_pengadaan', $prefix, 'after')
            ->orderBy('nomor_pengadaan', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($latest) {
            $numPart = (int) substr($latest['nomor_pengadaan'], -4);
            $nextNum = str_pad($numPart + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }
        $nomorPengadaan = $prefix . $nextNum;

        // Ambil id_perusahaan dari pengguna
        $user = $db->table('pengguna')->where('id_pengguna', $userId)->get()->getRowArray();
        $idPerusahaan = $user ? (int)$user['id_perusahaan'] : 1;

        // Insert header pengadaan
        $headerData = [
            'id_perusahaan'     => $idPerusahaan,
            'nomor_pengadaan'   => $nomorPengadaan,
            'tanggal_pengadaan' => date('Y-m-d'),
            'status'            => 'draft',
            'keterangan'        => 'Auto-draft dari Permintaan Nomor: ' . $permintaan['nomor_permintaan'],
            'created_by'        => $userId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $db->table('pengadaan')->insert($headerData);
        $pengadaanId = $db->insertID();

        // Insert details
        foreach ($itemsKurang as $item) {
            $db->table('pengadaan_detail')->insert([
                'id_pengadaan' => $pengadaanId,
                'id_barang'    => $item['id_barang'],
                'nama_barang'  => $item['nama_barang'],
                'jumlah'       => $item['jumlah'],
                'satuan'       => $item['satuan'],
                'spesifikasi'  => $item['spesifikasi'],
                'merk'         => $item['merk'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Gagal membuat auto-draft pengadaan.");
        }

        return [
            'status' => 'success',
            'message' => 'Draft pengadaan otomatis berhasil dibuat.',
            'nomor_pengadaan' => $nomorPengadaan
        ];
    }
}
