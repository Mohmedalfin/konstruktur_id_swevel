<?php

namespace App\Services;

use Config\Database;

class StokService
{
    /**
     * Get Stats: Total, Aman, Kritis, Kosong
     */
    public function getStats(int $idPerusahaan): array
    {
        $db = Database::connect();
        
        $total = $db->table('master_barang mb')
                    ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                    ->where('mb.id_perusahaan', $idPerusahaan)
                    ->countAllResults();
                    
        $aman = $db->table('master_barang mb')
                   ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                   ->where('mb.id_perusahaan', $idPerusahaan)
                   ->where('COALESCE(sg.stok_aktual, 0) > COALESCE(sg.stok_minimum, 0)')
                   ->countAllResults();
                   
        $kritis = $db->table('master_barang mb')
                     ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                     ->where('mb.id_perusahaan', $idPerusahaan)
                     ->where('COALESCE(sg.stok_aktual, 0) > 0')
                     ->where('COALESCE(sg.stok_aktual, 0) <= COALESCE(sg.stok_minimum, 0)')
                     ->countAllResults();
                     
        $kosong = $db->table('master_barang mb')
                     ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                     ->where('mb.id_perusahaan', $idPerusahaan)
                     ->where('COALESCE(sg.stok_aktual, 0) <= 0')
                     ->countAllResults();

        return [
            'total'  => $total,
            'aman'   => $aman,
            'kritis' => $kritis,
            'kosong' => $kosong
        ];
    }

    /**
     * Get Stock List
     */
    public function getStockList(int $idPerusahaan, ?string $kategori = 'all', ?string $status = 'all', ?string $search = null): array
    {
        $db = Database::connect();
        $builder = $db->table('master_barang mb')
                      ->select('mb.id as id_barang, mb.kode_barang, mb.nama_barang, mb.jenis_item, mb.satuan, COALESCE(sg.stok_aktual, 0) as stok_aktual, COALESCE(sg.stok_minimum, 0) as stok_minimum')
                      ->join('stok_gudang sg', 'sg.id_barang = mb.id AND sg.id_perusahaan = mb.id_perusahaan', 'left')
                      ->where('mb.id_perusahaan', $idPerusahaan);

        if ($kategori && $kategori !== 'all') {
            $jenisItem = ucfirst(strtolower($kategori));
            $builder->where('mb.jenis_item', $jenisItem);
        }

        if ($status && $status !== 'all') {
            if ($status === 'aman') {
                $builder->where('COALESCE(sg.stok_aktual, 0) > COALESCE(sg.stok_minimum, 0)');
            } elseif ($status === 'kritis') {
                $builder->where('COALESCE(sg.stok_aktual, 0) > 0');
                $builder->where('COALESCE(sg.stok_aktual, 0) <= COALESCE(sg.stok_minimum, 0)');
            } elseif ($status === 'kosong') {
                $builder->where('COALESCE(sg.stok_aktual, 0) <= 0');
            }
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('mb.kode_barang', $search)
                    ->orLike('mb.nama_barang', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('mb.kode_barang', 'ASC')->get()->getResultArray();
    }

    /**
     * Update Minimum Stock
     */
    public function updateMinimumStock(int $idPerusahaan, int $idBarang, float $minimumStock): bool
    {
        $db = Database::connect();
        
        // Cek apakah stok_gudang sudah ada
        $stokGudang = $db->table('stok_gudang')
                         ->where('id_perusahaan', $idPerusahaan)
                         ->where('id_barang', $idBarang)
                         ->get()
                         ->getRowArray();
                         
        if ($stokGudang) {
            return $db->table('stok_gudang')
                      ->where('id', $stokGudang['id'])
                      ->update([
                          'stok_minimum' => $minimumStock,
                          'updated_at'   => date('Y-m-d H:i:s')
                      ]);
        }
        
        // Jika belum ada, buat baru (meskipun jarang terjadi karena backfill/pendaftaran otomatis)
        return $db->table('stok_gudang')->insert([
            'id_perusahaan'   => $idPerusahaan,
            'id_barang'       => $idBarang,
            'stok_aktual'     => 0,
            'stok_minimum'    => $minimumStock,
            'harga_rata_rata' => 0,
            'lokasi'          => 'Gudang Utama',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ]);
    }
}
