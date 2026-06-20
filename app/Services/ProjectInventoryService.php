<?php

namespace App\Services;

use App\Models\StokProyekModel;
use App\Models\KartuStokProyekModel;
use Config\Database;

/**
 * ProjectInventoryService
 *
 * Service terpusat untuk mengelola semua operasi persediaan di lapangan proyek:
 * - Penerimaan material dari Gudang Central (Masuk)
 * - Pemakaian material untuk pekerjaan (Keluar)
 * - Retur material sisa ke Gudang Central (Keluar dari Proyek)
 * - Mutasi antar Proyek (Keluar dari Proyek A, Masuk ke Proyek B)
 * - Query stok lapangan per proyek dengan kartu riwayatnya
 */
class ProjectInventoryService
{
    protected $stokProyekModel;
    protected $kartuStokModel;

    public function __construct()
    {
        $this->stokProyekModel = new StokProyekModel();
        $this->kartuStokModel  = new KartuStokProyekModel();
    }

    /**
     * Catat penerimaan material dari Gudang Central ke Lapangan Proyek.
     * Dipanggil otomatis ketika status Permintaan berubah menjadi 'diproses' atau 'selesai'.
     *
     * @param int    $idProject    ID proyek penerima
     * @param int    $idBarang     ID barang dari master_barang
     * @param float  $jumlah       Jumlah yang diterima
     * @param int    $idPermintaan ID permintaan sumber (untuk referensi kartu stok)
     * @param string $nomor        Nomor permintaan (untuk catatan keterangan)
     */
    public function terimaDariCentral(
        int    $idProject,
        int    $idBarang,
        float  $jumlah,
        int    $idPermintaan,
        string $nomor = ''
    ): void {
        if ($jumlah <= 0) return;

        // Tambah stok di lapangan proyek
        $sisaStok = $this->stokProyekModel->tambahStok($idProject, $idBarang, $jumlah);

        // Catat di kartu stok proyek
        $this->kartuStokModel->catat(
            idProject:  $idProject,
            idBarang:   $idBarang,
            tipe:       'masuk',
            jumlah:     $jumlah,
            sisaStok:   $sisaStok,
            sumber:     'permintaan',
            idSumber:   $idPermintaan,
            keterangan: "Penerimaan dari Gudang Central · {$nomor}"
        );
    }

    /**
     * Batalkan penerimaan (ketika status Permintaan dikembalikan dari 'diproses' ke 'pending'/'ditolak').
     * Mengurangi kembali stok lapangan proyek.
     */
    public function batalPenerimaan(
        int    $idProject,
        int    $idBarang,
        float  $jumlah,
        int    $idPermintaan,
        string $nomor = ''
    ): void {
        if ($jumlah <= 0) return;

        $sisaStok = $this->stokProyekModel->kurangiStok($idProject, $idBarang, $jumlah);

        $this->kartuStokModel->catat(
            idProject:  $idProject,
            idBarang:   $idBarang,
            tipe:       'keluar',
            jumlah:     $jumlah,
            sisaStok:   $sisaStok,
            sumber:     'batal_permintaan',
            idSumber:   $idPermintaan,
            keterangan: "Pembatalan penerimaan · {$nomor}"
        );
    }

    /**
     * Catat pemakaian material aktual di lapangan proyek.
     * Dipanggil dari RealisasiService ketika SDM progress disimpan.
     *
     * @param int    $idProject       ID proyek
     * @param int    $idBarang        ID barang dari master_barang
     * @param float  $jumlah          Jumlah yang dipakai
     * @param int    $idRealisasiSdm  ID header realisasi SDM untuk referensi
     * @param string $namaBarang      Nama barang untuk keterangan
     */
    public function catatPemakaian(
        int    $idProject,
        int    $idBarang,
        float  $jumlah,
        int    $idRealisasiSdm,
        string $namaBarang = ''
    ): void {
        if ($jumlah <= 0) return;

        $sisaStok = $this->stokProyekModel->kurangiStok($idProject, $idBarang, $jumlah);

        $this->kartuStokModel->catat(
            idProject:  $idProject,
            idBarang:   $idBarang,
            tipe:       'keluar',
            jumlah:     $jumlah,
            sisaStok:   $sisaStok,
            sumber:     'pemakaian',
            idSumber:   $idRealisasiSdm,
            keterangan: "Pemakaian realisasi · {$namaBarang}"
        );
    }

    /**
     * Retur material sisa dari Lapangan Proyek kembali ke Gudang Central.
     * Mengurangi stok lapangan dan menambah stok central.
     *
     * @param int    $idProject    ID proyek pengirim
     * @param int    $idBarang     ID barang
     * @param int    $idPerusahaan ID perusahaan untuk stok_gudang central
     * @param float  $jumlah       Jumlah yang diretur
     * @param string $keterangan   Alasan retur
     */
    public function returKeCentral(
        int    $idProject,
        int    $idBarang,
        int    $idPerusahaan,
        float  $jumlah,
        string $keterangan = 'Retur material sisa ke gudang'
    ): void {
        if ($jumlah <= 0) return;

        $db = Database::connect();
        $db->transStart();

        // 1. Kurangi stok lapangan proyek
        $sisaStok = $this->stokProyekModel->kurangiStok($idProject, $idBarang, $jumlah);
        $this->kartuStokModel->catat(
            idProject:  $idProject,
            idBarang:   $idBarang,
            tipe:       'keluar',
            jumlah:     $jumlah,
            sisaStok:   $sisaStok,
            sumber:     'retur_ke_central',
            idSumber:   null,
            keterangan: $keterangan
        );

        // 2. Tambah stok ke Gudang Central
        $stokGudang = $db->table('stok_gudang')
            ->where('id_perusahaan', $idPerusahaan)
            ->where('id_barang', $idBarang)
            ->get()->getRowArray();

        if ($stokGudang) {
            $db->table('stok_gudang')
               ->where('id', $stokGudang['id'])
               ->set('stok_aktual', 'stok_aktual + ' . (float)$jumlah, false)
               ->set('updated_at', date('Y-m-d H:i:s'))
               ->update();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Gagal memproses retur material ke gudang central.");
        }
    }

    /**
     * Mutasi material dari Proyek A ke Proyek B.
     * Mengurangi stok Proyek A dan menambah stok Proyek B.
     *
     * @param int    $idProjectAsal   ID proyek pengirim
     * @param int    $idProjectTujuan ID proyek penerima
     * @param int    $idBarang        ID barang
     * @param float  $jumlah          Jumlah yang dimutasi
     * @param string $keterangan      Catatan mutasi
     */
    public function mutasiAntar(
        int    $idProjectAsal,
        int    $idProjectTujuan,
        int    $idBarang,
        float  $jumlah,
        string $keterangan = ''
    ): void {
        if ($jumlah <= 0) return;

        $db = \Config\Database::connect();
        $namaProyekAsal = $db->table('projects')->select('nama_proyek')->where('id_project', $idProjectAsal)->get()->getRow()->nama_proyek ?? "ID {$idProjectAsal}";
        $namaProyekTujuan = $db->table('projects')->select('nama_proyek')->where('id_project', $idProjectTujuan)->get()->getRow()->nama_proyek ?? "ID {$idProjectTujuan}";

        // Kurangi dari proyek asal
        $sisaAsal = $this->stokProyekModel->kurangiStok($idProjectAsal, $idBarang, $jumlah);
        $this->kartuStokModel->catat(
            idProject:  $idProjectAsal,
            idBarang:   $idBarang,
            tipe:       'keluar',
            jumlah:     $jumlah,
            sisaStok:   $sisaAsal,
            sumber:     'mutasi_keluar',
            idSumber:   $idProjectTujuan,
            keterangan: "Mutasi ke {$namaProyekTujuan} · {$keterangan}"
        );

        // Tambahkan ke proyek tujuan
        $sisaTujuan = $this->stokProyekModel->tambahStok($idProjectTujuan, $idBarang, $jumlah);
        $this->kartuStokModel->catat(
            idProject:  $idProjectTujuan,
            idBarang:   $idBarang,
            tipe:       'masuk',
            jumlah:     $jumlah,
            sisaStok:   $sisaTujuan,
            sumber:     'mutasi_masuk',
            idSumber:   $idProjectAsal,
            keterangan: "Mutasi dari {$namaProyekAsal} · {$keterangan}"
        );
    }

    /**
     * Dapatkan daftar stok lapangan proyek beserta info barang
     */
    public function getStokProyek(int $idProject): array
    {
        return $this->stokProyekModel->getByProject($idProject);
    }

    /**
     * Dapatkan stok satu barang spesifik di satu proyek
     */
    public function getStokBarang(int $idProject, int $idBarang): float
    {
        $row = $this->stokProyekModel->getStok($idProject, $idBarang);
        return $row ? (float)$row['stok_aktual'] : 0.0;
    }

    /**
     * Dapatkan kartu riwayat mutasi barang di lapangan proyek
     */
    public function getKartuStok(int $idProject, int $limit = 100): array
    {
        return $this->kartuStokModel->getByProject($idProject, $limit);
    }

    /**
     * Dapatkan kartu riwayat mutasi spesifik per barang di proyek
     */
    public function getKartuByBarang(int $idProject, int $idBarang): array
    {
        return $this->kartuStokModel->getKartuByBarang($idProject, $idBarang);
    }

    /**
     * Catat pembuangan/waste material sisa dari lapangan proyek.
     * Mengurangi stok lapangan hingga habis.
     *
     * @param int    $idProject    ID proyek asal
     * @param int    $idBarang     ID barang
     * @param float  $jumlah       Jumlah yang di-waste
     * @param string $keterangan   Catatan penyusutan
     */
    public function catatWaste(
        int    $idProject,
        int    $idBarang,
        float  $jumlah,
        string $keterangan = 'Waste / Penyusutan sisa proyek'
    ): void {
        if ($jumlah <= 0) return;

        $sisaStok = $this->stokProyekModel->kurangiStok($idProject, $idBarang, $jumlah);

        $this->kartuStokModel->catat(
            idProject:  $idProject,
            idBarang:   $idBarang,
            tipe:       'keluar',
            jumlah:     $jumlah,
            sisaStok:   $sisaStok,
            sumber:     'waste_penyusutan',
            idSumber:   null,
            keterangan: $keterangan
        );
    }
}
