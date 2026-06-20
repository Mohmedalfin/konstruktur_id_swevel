<?php

namespace App\Models;

use CodeIgniter\Model;

class StokProyekModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stok_proyek';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_project',
        'id_barang',
        'stok_aktual',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Dapatkan stok proyek untuk satu barang spesifik
     */
    public function getStok(int $idProject, int $idBarang): ?array
    {
        return $this->where('id_project', $idProject)
                    ->where('id_barang', $idBarang)
                    ->first();
    }

    /**
     * Tambah stok masuk ke lapangan proyek (dari gudang central atau mutasi)
     * Return sisa stok setelah operasi
     */
    public function tambahStok(int $idProject, int $idBarang, float $jumlah): float
    {
        $existing = $this->getStok($idProject, $idBarang);

        if ($existing) {
            $newStok = (float)$existing['stok_aktual'] + $jumlah;
            $this->update($existing['id'], [
                'stok_aktual' => $newStok,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            return $newStok;
        } else {
            $this->insert([
                'id_project'  => $idProject,
                'id_barang'   => $idBarang,
                'stok_aktual' => $jumlah,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            return $jumlah;
        }
    }

    /**
     * Kurangi stok dari lapangan proyek (pemakaian atau retur ke central)
     * Tidak akan menjadi negatif (dilindungi oleh cek di service)
     * Return sisa stok setelah operasi
     */
    public function kurangiStok(int $idProject, int $idBarang, float $jumlah): float
    {
        $existing = $this->getStok($idProject, $idBarang);

        if ($existing) {
            $newStok = (float)$existing['stok_aktual'] - $jumlah;
            $this->update($existing['id'], [
                'stok_aktual' => $newStok,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            return $newStok;
        }

        // Jika belum ada record, buat dengan nilai negatif (pencatatan backlog)
        $this->insert([
            'id_project'  => $idProject,
            'id_barang'   => $idBarang,
            'stok_aktual' => -$jumlah,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        return -$jumlah;
    }

    /**
     * Dapatkan seluruh stok lapangan untuk satu proyek,
     * di-join ke master_barang untuk data lengkap
     */
    public function getByProject(int $idProject): array
    {
        $db = \Config\Database::connect();
        return $db->table('stok_proyek sp')
            ->select('sp.*, mb.kode_barang, mb.nama_barang, mb.satuan, mb.satuan_kemasan, mb.konversi_faktor, mb.jenis_item, mb.merk, mb.spesifikasi')
            ->join('master_barang mb', 'mb.id = sp.id_barang')
            ->where('sp.id_project', $idProject)
            ->orderBy('mb.nama_barang', 'ASC')
            ->get()
            ->getResultArray();
    }
}
