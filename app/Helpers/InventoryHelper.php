<?php

namespace App\Helpers;

class InventoryHelper
{
    /**
     * Generate Kode Barang Otomatis
     * Format: [JENIS]-[INISIAL_NAMA]-[INISIAL_MERK]-[NOMOR_URUT]
     *
     * @param int $idPerusahaan
     * @param string $jenisItem 'Bahan' atau 'Alat'
     * @param string $namaBarang
     * @param string|null $merk
     * @return string
     */
    public static function generateKodeBarang(int $idPerusahaan, string $jenisItem, string $namaBarang, ?string $merk = null): string
    {
        // 1. Jenis Item (MAT / ALT)
        $jenisPrefix = ($jenisItem === 'Alat') ? 'ALT' : 'MAT';

        // 2. Inisial Nama (3 Huruf)
        $cleanNama = preg_replace('/[^A-Z0-9]/', '', strtoupper($namaBarang));
        if (strlen($cleanNama) < 3) {
            $initialNama = str_pad($cleanNama, 3, 'X');
        } else {
            $initialNama = substr($cleanNama, 0, 3);
        }

        // 3. Inisial Merk (3 Huruf)
        $merk = $merk ?? '';
        $cleanMerk = preg_replace('/[^A-Z0-9]/', '', strtoupper($merk));
        
        if (empty($cleanMerk) || $cleanMerk === 'TANPAMERK') {
            $initialMerk = 'TAN';
        } else {
            if (strlen($cleanMerk) < 3) {
                $initialMerk = str_pad($cleanMerk, 3, 'X');
            } else {
                $initialMerk = substr($cleanMerk, 0, 3);
            }
        }

        // 4. Prefix Kode Utama
        $prefix = "{$jenisPrefix}-{$initialNama}-{$initialMerk}-";

        // 5. Query nomor urut terakhir
        $db = \Config\Database::connect();
        $row = $db->table('master_barang')
                  ->select('kode_barang')
                  ->where('id_perusahaan', $idPerusahaan)
                  ->like('kode_barang', $prefix, 'after')
                  ->orderBy('kode_barang', 'DESC')
                  ->limit(1)
                  ->get()
                  ->getRowArray();

        $nextNum = 1;
        if ($row && isset($row['kode_barang'])) {
            $parts = explode('-', $row['kode_barang']);
            if (count($parts) === 4) {
                $lastNum = (int) $parts[3];
                $nextNum = $lastNum + 1;
            }
        }

        $sequence = str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);

        return $prefix . $sequence;
    }

    /**
     * Cari atau buat master barang baru (auto-generate kode) berdasarkan input dari Permintaan/RAB
     */
    public static function resolveMasterBarang(int $idProject, string $jenisItem, string $namaBarang, ?string $merk = null, ?string $spesifikasi = null, ?string $satuan = null, ?string $satuanKemasan = null, ?float $konversiFaktor = null): ?int
    {
        $db = \Config\Database::connect();
        
        // 1. Dapatkan id_perusahaan (id_pengguna dari projects)
        $project = $db->table('projects')
            ->select('id_pengguna')
            ->where('id_project', $idProject)
            ->get()->getRowArray();
        
        if (!$project) {
            return null; // Project tidak valid
        }

        $idPerusahaan = (int) ($project['id_pengguna'] ?? 1);
        
        $namaBarang = trim($namaBarang);
        $merk = trim((string) $merk);
        if ($merk === '' || $merk === '-') {
            $merk = 'Tanpa Merk';
        }
        $spesifikasi = trim((string) $spesifikasi);
        if ($spesifikasi === '') {
            $spesifikasi = '-';
        }
        $satuan = trim((string) $satuan) ?: '-';
        
        // Normalisasi jenis_item ke title case (Bahan, Alat, Upah)
        $jenisItem = ucfirst(strtolower(trim($jenisItem))); 

        // Upah tidak masuk ke master_barang inventaris
        if ($jenisItem === 'Upah') {
            return null;
        }

        // Jika bukan Bahan/Alat, fallback ke Bahan
        if (!in_array($jenisItem, ['Bahan', 'Alat'])) {
            $jenisItem = 'Bahan';
        }

        // 2. Cari barang existing di master_barang
        $existing = $db->table('master_barang')
            ->where('id_perusahaan', $idPerusahaan)
            ->where('nama_barang', $namaBarang)
            ->where('merk', $merk)
            ->where('spesifikasi', $spesifikasi)
            ->where('jenis_item', $jenisItem)
            ->get()->getRowArray();

        if ($existing) {
            return (int) $existing['id'];
        }

        // 3. Jika tidak ada, generate kode dan buat baru
        $kodeBarang = self::generateKodeBarang($idPerusahaan, $jenisItem, $namaBarang, $merk);
        
        $dataMaster = [
            'id_perusahaan' => $idPerusahaan,
            'kode_barang'   => $kodeBarang,
            'nama_barang'   => $namaBarang,
            'merk'          => $merk,
            'spesifikasi'   => $spesifikasi,
            'satuan'        => $satuan,
            'satuan_kemasan'=> $satuanKemasan,
            'konversi_faktor'=> $konversiFaktor ?? 1,
            'jenis_item'    => $jenisItem,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $db->table('master_barang')->insert($dataMaster);
        $idBarang = $db->insertID();

        // 4. Buat stok_gudang awal (0)
        $db->table('stok_gudang')->insert([
            'id_perusahaan' => $idPerusahaan,
            'id_barang'     => $idBarang,
            'stok_aktual'   => 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        return $idBarang;
    }
}
