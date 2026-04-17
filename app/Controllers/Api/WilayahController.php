<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class WilayahController extends BaseController
{
    /**
     * GET /api/wilayah
     * Ambil semua template harga resmi dari DB estimator.
     * Template adalah AHSP (Analisis Harga Satuan Pekerjaan) standar nasional.
     * Contoh: AHSP PUPR 2016, AHSP SNI 2018, dll.
     */
    public function index(): ResponseInterface
    {
        try {
            $db = \Config\Database::connect('estimator');

            // Ambil semua template yang punya data BUA (harga)
            $rows = $db->query("
                SELECT
                    tp.id_template,
                    tp.nama_proyek AS nama_template,
                    YEAR(tp.tgl_dibuat) AS tahun_input
                FROM template_proyek tp
                INNER JOIN (
                    SELECT DISTINCT id_template FROM bua_template_proyek
                ) btp ON btp.id_template = tp.id_template
                ORDER BY tp.id_template DESC
            ")->getResultArray();

            $data = array_map(fn($r) => [
                'id'    => (int) $r['id_template'],
                'nama'  => $r['nama_template'],
                'tahun' => $r['tahun_input'],
                'label' => $r['nama_template'],
            ], $rows);

            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memuat data template: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /api/wilayah/provinces
     * Ambil daftar provinsi dari database estimator.
     */
    public function provinces(): ResponseInterface
    {
        try {
            $db = \Config\Database::connect('estimator');
            $rows = $db->query("SELECT id_wilayah AS id, wilayah AS nama FROM wilayah WHERE kategori = '1' ORDER BY wilayah ASC")->getResultArray();

            return $this->response->setJSON(['status' => 'success', 'data' => $rows]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memuat data provinsi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /api/wilayah/cities?id_prov=1100
     * Ambil daftar kabupaten/kota berdasarkan ID provinsi.
     */
    public function cities(): ResponseInterface
    {
        try {
            $idProv = $this->request->getGet('id_prov');
            if (!$idProv) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'id_prov wajib diisi']);
            }

            $db = \Config\Database::connect('estimator');
            $rows = $db->query("SELECT id_wilayah AS id, wilayah AS nama FROM wilayah WHERE kategori = '2' AND id_prov = ? ORDER BY wilayah ASC", [$idProv])->getResultArray();

            return $this->response->setJSON(['status' => 'success', 'data' => $rows]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memuat data kota: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /api/wilayah/templates?id_wilayah=3404
     * Ambil daftar tahun/template yang tersedia untuk wilayah tersebut.
     */
    public function templates(): ResponseInterface
    {
        try {
            $idWilayah = $this->request->getGet('id_wilayah');
            $db = \Config\Database::connect('estimator');

            // Cari tahun yang tersedia di bua_bps_utama untuk wilayah ini
            $sql = "SELECT DISTINCT tahun FROM bua_bps_utama WHERE id_wilayah = ? ORDER BY tahun DESC";
            $rows = $db->query($sql, [$idWilayah])->getResultArray();

            // Jika tidak ada data di BPS, mungkin ada di template_proyek (manual mapping?)
            // Sementara kita return tahun sebagai 'id' atau semacamnya
            $data = array_map(fn($r) => [
                'id'    => $r['tahun'], // Kita pakai tahun sebagai referensi harga
                'nama'  => "Harga BPS Tahun " . $r['tahun'],
                'tahun' => $r['tahun']
            ], $rows);

            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memuat template wilayah: ' . $e->getMessage(),
            ]);
        }
    }
}
