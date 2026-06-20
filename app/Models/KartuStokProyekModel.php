<?php

namespace App\Models;

use CodeIgniter\Model;

class KartuStokProyekModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'kartu_stok_proyek';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_project',
        'id_barang',
        'tipe',
        'jumlah',
        'sisa_stok',
        'sumber',
        'id_sumber',
        'keterangan',
        'created_at',
    ];

    // Tidak pakai timestamps otomatis karena hanya punya created_at
    protected $useTimestamps = false;

    /**
     * Catat transaksi stok (masuk atau keluar)
     */
    public function catat(
        int    $idProject,
        int    $idBarang,
        string $tipe,        // 'masuk' atau 'keluar'
        float  $jumlah,
        float  $sisaStok,
        string $sumber,      // 'permintaan', 'pemakaian', 'retur_ke_central', dll
        ?int   $idSumber = null,
        ?string $keterangan = null
    ): bool {
        return $this->insert([
            'id_project' => $idProject,
            'id_barang'  => $idBarang,
            'tipe'       => $tipe,
            'jumlah'     => $jumlah,
            'sisa_stok'  => $sisaStok,
            'sumber'     => $sumber,
            'id_sumber'  => $idSumber,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ambil kartu stok suatu barang di proyek tertentu
     */
    public function getKartuByBarang(int $idProject, int $idBarang): array
    {
        return $this->where('id_project', $idProject)
                    ->where('id_barang', $idBarang)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil seluruh riwayat transaksi lapangan di satu proyek
     * (join ke master_barang untuk tampil di view)
     */
    public function getByProject(int $idProject, int $limit = 100): array
    {
        $db = \Config\Database::connect();
        return $db->table('kartu_stok_proyek ksp')
            ->select('ksp.*, mb.nama_barang, mb.satuan, mb.kode_barang')
            ->join('master_barang mb', 'mb.id = ksp.id_barang')
            ->where('ksp.id_project', $idProject)
            ->orderBy('ksp.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
