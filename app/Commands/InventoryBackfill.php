<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Helpers\InventoryHelper;

class InventoryBackfill extends BaseCommand
{
    protected $group       = 'Inventory';
    protected $name        = 'inventory:backfill';
    protected $description = 'Melakukan backfill data lama (RAP dan Permintaan) ke master_barang.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Memulai proses backfill sinkronisasi data master_barang...', 'green');
        
        // 1. Ambil data RAP Detail Item (Bahan & Alat) yang belum punya id_barang
        $rapItems = $db->table('rap_detail_item rdi')
            ->select('rdi.id_rap_detail_item, rdi.nama_item, rdi.merk, rdi.spesifikasi, rdi.satuan, rdi.jenis_item, p.id_pengguna as id_perusahaan')
            ->join('rap_detail rd', 'rd.id_rap_detail = rdi.id_rap_detail')
            ->join('rap r', 'r.id_rap = rd.id_rap')
            ->join('projects p', 'p.id_project = r.id_project')
            ->where('rdi.id_barang', null)
            ->groupStart()
                ->where('rdi.jenis_item', 'Bahan')
                ->orWhere('rdi.jenis_item', 'Alat')
            ->groupEnd()
            ->get()
            ->getResultObject();

        $countRap = count($rapItems);
        CLI::write("Ditemukan {$countRap} data RAP Detail Item yang belum terhubung.", 'yellow');

        $processedCount = 0;
        $db->transStart();

        foreach ($rapItems as $item) {
            $idPerusahaan = (int) $item->id_perusahaan;
            $namaBarang = trim($item->nama_item);
            $merk = trim((string)$item->merk);
            $spesifikasi = trim((string)$item->spesifikasi);
            if (empty($spesifikasi)) $spesifikasi = '-';
            $satuan = trim($item->satuan);
            $jenisItem = $item->jenis_item;

            // Cek apakah kombinasi sudah ada di master_barang
            $existing = $db->table('master_barang')
                ->where('id_perusahaan', $idPerusahaan)
                ->where('nama_barang', $namaBarang)
                ->where('merk', empty($merk) ? 'Tanpa Merk' : $merk)
                ->where('spesifikasi', $spesifikasi)
                ->where('satuan', $satuan)
                ->where('jenis_item', $jenisItem)
                ->get()
                ->getRowObject();

            if ($existing) {
                $idBarang = $existing->id;
            } else {
                // Generate kode baru
                $kodeBarang = InventoryHelper::generateKodeBarang($idPerusahaan, $jenisItem, $namaBarang, $merk);
                
                $dataMaster = [
                    'id_perusahaan' => $idPerusahaan,
                    'kode_barang'   => $kodeBarang,
                    'nama_barang'   => $namaBarang,
                    'merk'          => empty($merk) ? 'Tanpa Merk' : $merk,
                    'spesifikasi'   => $spesifikasi,
                    'satuan'        => $satuan,
                    'jenis_item'    => $jenisItem,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
                $db->table('master_barang')->insert($dataMaster);
                $idBarang = $db->insertID();

                // Buat stok gudang awal (0)
                $dataStok = [
                    'id_perusahaan'   => $idPerusahaan,
                    'id_barang'       => $idBarang,
                    'stok_aktual'     => 0,
                    'harga_rata_rata' => 0,
                    'lokasi'          => 'Gudang Utama',
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                $db->table('stok_gudang')->insert($dataStok);
            }

            // Update rap_detail_item
            $db->table('rap_detail_item')
               ->where('id_rap_detail_item', $item->id_rap_detail_item)
               ->update(['id_barang' => $idBarang]);

            $processedCount++;
        }

        // 2. Ambil data Permintaan Detail yang belum punya id_barang
        $reqItems = $db->table('permintaan_detail pd')
            ->select('pd.id, pd.id_rap_detail_item, pd.nama_barang, pd.satuan, pd.jenis_item, perm.pemohon_id')
            ->join('permintaan perm', 'perm.id = pd.id_permintaan')
            ->where('pd.id_barang', null)
            ->groupStart()
                ->where('pd.jenis_item', 'Bahan')
                ->orWhere('pd.jenis_item', 'Alat')
            ->groupEnd()
            ->get()
            ->getResultObject();

        $countReq = count($reqItems);
        CLI::write("Ditemukan {$countReq} data Permintaan Detail yang belum terhubung.", 'yellow');

        foreach ($reqItems as $item) {
            $idBarang = null;

            // Jika dia terhubung ke RAP Detail Item, ambil id_barang dari sana
            if (!empty($item->id_rap_detail_item)) {
                $rdi = $db->table('rap_detail_item')
                          ->select('id_barang')
                          ->where('id_rap_detail_item', $item->id_rap_detail_item)
                          ->get()
                          ->getRowObject();
                if ($rdi && $rdi->id_barang) {
                    $idBarang = $rdi->id_barang;
                }
            }

            // Jika masih null (karena tidak terhubung ke RAP atau RAP belum punya barang)
            if (!$idBarang) {
                // Cari id_perusahaan dari pemohon
                $pemohon = $db->table('pengguna')
                              ->select('id_pengguna, parent_id')
                              ->where('id_pengguna', $item->pemohon_id)
                              ->get()
                              ->getRowObject();
                
                if ($pemohon) {
                    $idPerusahaan = !empty($pemohon->parent_id) ? (int)$pemohon->parent_id : (int)$pemohon->id_pengguna;
                    $namaBarang = trim($item->nama_barang);
                    $satuan = trim($item->satuan);
                    $jenisItem = $item->jenis_item;
                    
                    // Coba cari di master_barang (asumsi merk Tanpa Merk dan spesifikasi -)
                    $existing = $db->table('master_barang')
                        ->where('id_perusahaan', $idPerusahaan)
                        ->where('nama_barang', $namaBarang)
                        ->where('satuan', $satuan)
                        ->where('jenis_item', $jenisItem)
                        ->get()
                        ->getRowObject();

                    if ($existing) {
                        $idBarang = $existing->id;
                    } else {
                        // Insert master baru
                        $kodeBarang = InventoryHelper::generateKodeBarang($idPerusahaan, $jenisItem, $namaBarang, 'Tanpa Merk');
                        $dataMaster = [
                            'id_perusahaan' => $idPerusahaan,
                            'kode_barang'   => $kodeBarang,
                            'nama_barang'   => $namaBarang,
                            'merk'          => 'Tanpa Merk',
                            'spesifikasi'   => '-',
                            'satuan'        => $satuan,
                            'jenis_item'    => $jenisItem,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                        ];
                        $db->table('master_barang')->insert($dataMaster);
                        $idBarang = $db->insertID();

                        $dataStok = [
                            'id_perusahaan'   => $idPerusahaan,
                            'id_barang'       => $idBarang,
                            'stok_aktual'     => 0,
                            'harga_rata_rata' => 0,
                            'lokasi'          => 'Gudang Utama',
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ];
                        $db->table('stok_gudang')->insert($dataStok);
                    }
                }
            }

            if ($idBarang) {
                $db->table('permintaan_detail')
                   ->where('id', $item->id)
                   ->update(['id_barang' => $idBarang]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            CLI::error('Terjadi kesalahan saat melakukan backfill data.');
        } else {
            CLI::write("Proses backfill selesai. Berhasil memproses {$processedCount} item RAP dan data Permintaan terkait.", 'green');
        }
    }
}
