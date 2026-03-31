<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RapModel;
use App\Models\RapDetailModel;
use App\Models\ProyekModel;
use CodeIgniter\HTTP\ResponseInterface;

class RapController extends BaseController
{
    private function resolveProyek(string $slug): ?array
    {
        $model = new ProyekModel();
        return $model->where('slug', $slug)->first() ?: null;
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/rap?slug=...
    // Ambil semua baris RAP per proyek, harga dikalkulasi dari rap_detail+ahs
    // ──────────────────────────────────────────────────────────────────────
    public function index(): ResponseInterface
    {
        try {
            $slug   = $this->request->getGet('slug');
            if (!$slug) return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'slug diperlukan']);

            $proyek = $this->resolveProyek($slug);
            if (!$proyek) return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Proyek tidak ditemukan']);

            $idProyek = $proyek['id_proyek'];
            $db       = \Config\Database::connect('default');

            // Ambil semua baris RAP + kalkulasi harga dari rap_detail + ahs
            $sql = "
                SELECT
                    r.id_rap,
                    r.id_kategori_pekerjaan,
                    r.id_pekerjaan,
                    r.nama_pekerjaan,
                    r.satuan,
                    r.volume,
                    r.urutan,
                    kp.nama_kategori,
                    COALESCE(SUM(CASE WHEN a.tipe_ahs = 'Bahan' THEN rd.koefesien * rd.harga_satuan ELSE 0 END), 0) AS harga_bahan,
                    COALESCE(SUM(CASE WHEN a.tipe_ahs = 'Alat'  THEN rd.koefesien * rd.harga_satuan ELSE 0 END), 0) AS harga_alat,
                    COALESCE(SUM(CASE WHEN a.tipe_ahs = 'Upah'  THEN rd.koefesien * rd.harga_satuan ELSE 0 END), 0) AS harga_upah
                FROM rap r
                LEFT JOIN kategori_pekerjaan kp ON kp.id_kategori_pekerjaan = r.id_kategori_pekerjaan
                LEFT JOIN rap_detail rd           ON rd.id_rap = r.id_rap
                LEFT JOIN ahs a                   ON a.id_ahs  = rd.id_ahs
                WHERE r.id_proyek = ?
                GROUP BY r.id_rap
                ORDER BY r.id_kategori_pekerjaan ASC, r.urutan ASC, r.id_rap ASC
            ";

            $rows = $db->query($sql, [$idProyek])->getResultArray();

            // Cast numerics
            foreach ($rows as &$row) {
                $row['volume']      = (float) $row['volume'];
                $row['urutan']      = (int)   $row['urutan'];
                $row['harga_bahan'] = (float) $row['harga_bahan'];
                $row['harga_alat']  = (float) $row['harga_alat'];
                $row['harga_upah']  = (float) $row['harga_upah'];
            }
            unset($row);

            // Kelompokkan per kategori
            $grouped = [];
            foreach ($rows as $row) {
                $catId   = $row['id_kategori_pekerjaan'] ?? 0;
                $catName = $row['nama_kategori'] ?? 'Tanpa Kategori';

                if (!isset($grouped[$catId])) {
                    $grouped[$catId] = [
                        'id_kategori'   => $catId,
                        'nama_kategori' => $catName,
                        'items'         => [],
                    ];
                }
                $grouped[$catId]['items'][] = $row;
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => array_values($grouped),
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[RapController::index] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal memuat data RAP']);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /api/rap
    // Simpan batch pekerjaan baru ke tabel rap
    // Payload: { slug, items: [{ id_pekerjaan, id_kategori, sumber, nama, satuan }] }
    // ──────────────────────────────────────────────────────────────────────
    public function store(): ResponseInterface
    {
        try {
            $json = $this->request->getJSON();

            if (!$json || empty($json->slug) || empty($json->items)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Payload tidak lengkap']);
            }
            
            log_message('error', '[RapController::store] Payload: ' . json_encode($json));

            $proyek = $this->resolveProyek($json->slug);
            if (!$proyek) return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Proyek tidak ditemukan']);

            $idProyek = $proyek['id_proyek'];
            $rapModel = new RapModel();
            $inserted = [];

            foreach ($json->items as $item) {
                // Urutan berikutnya dalam kategori ini
                $db      = \Config\Database::connect('default');
                $idKat   = isset($item->id_kategori) && (int) $item->id_kategori > 0 ? (int) $item->id_kategori : null;
                $lastRow = $db->table('rap')
                              ->where('id_proyek', $idProyek)
                              ->where('id_kategori_pekerjaan', $idKat)
                              ->orderBy('urutan', 'DESC')
                              ->limit(1)
                              ->get()->getRowArray();

                $urutan = $lastRow ? ((int) $lastRow['urutan'] + 1) : 1;

                // id_pekerjaan:
                // - kustom  : gunakan db_id (integer dari tabel pekerjaan lokal)
                // - estimator: gunakan 0 (tidak ada FK, kode estimator disimpan di nama_pekerjaan)
                $sumber      = $item->sumber      ?? 'estimator';
                $idPekerjaan = 0;
                if ($sumber === 'kustom' && !empty($item->db_id)) {
                    $idPekerjaan = (int) $item->db_id;
                }

                $id = $rapModel->insert([
                    'id_proyek'            => $idProyek,
                    'id_kategori_pekerjaan'=> $idKat,
                    'id_pekerjaan'         => $idPekerjaan,
                    'nama_pekerjaan'       => $item->nama   ?? '',
                    'satuan'               => $item->satuan ?? '',
                    'volume'               => 0,
                    'urutan'               => $urutan,
                ]);

                $inserted[] = ['id_rap' => $id, 'nama' => $item->nama ?? ''];
            }

            return $this->response->setStatusCode(201)->setJSON([
                'status'   => 'success',
                'message'  => count($inserted) . ' pekerjaan berhasil ditambahkan ke RAP',
                'inserted' => $inserted,
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[RapController::store] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan RAP: ' . $e->getMessage()]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // PUT /api/rap/{id}
    // Update volume (atau urutan) satu baris
    // ──────────────────────────────────────────────────────────────────────
    public function update($id): ResponseInterface
    {
        try {
            $json = $this->request->getJSON();
            if (!$json) return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Payload kosong']);

            $rapModel = new RapModel();
            $rap      = $rapModel->find($id);
            if (!$rap) return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Item RAP tidak ditemukan']);

            $updateData = [];
            if (isset($json->volume)) $updateData['volume'] = (float) $json->volume;
            if (isset($json->urutan)) $updateData['urutan'] = (int)   $json->urutan;

            if (empty($updateData)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Tidak ada data yang diupdate']);
            }

            $rapModel->update($id, $updateData);

            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'RAP diperbarui']);

        } catch (\Throwable $e) {
            log_message('error', '[RapController::update] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui RAP']);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // DELETE /api/rap/{id}
    // Hapus satu baris RAP beserta seluruh rap_detail-nya
    // ──────────────────────────────────────────────────────────────────────
    public function destroy($id): ResponseInterface
    {
        try {
            $rapModel    = new RapModel();
            $detailModel = new RapDetailModel();

            $rap = $rapModel->find($id);
            if (!$rap) return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Item RAP tidak ditemukan']);

            // Hapus detail RAP dulu, lalu header
            $detailModel->where('id_rap', $id)->delete();
            $rapModel->delete($id);

            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Pekerjaan dihapus dari RAP']);

        } catch (\Throwable $e) {
            log_message('error', '[RapController::destroy] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menghapus RAP']);
        }
    }
}
