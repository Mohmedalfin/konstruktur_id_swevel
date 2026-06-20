<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;
use App\Models\RapDetailItemModel;
use App\Models\RapDetailModel;
use App\Models\RapModel;
use App\Models\RapKategoriModel;
use App\Helpers\InventoryHelper;

class AhsController extends BaseController
{
    public function getRincian($id_rap_detail): ResponseInterface
    {
        try {
            $model = new RapDetailItemModel();
            $data = $model->where('id_rap_detail', $id_rap_detail)
                ->orderBy('jenis_item', 'ASC')
                ->orderBy('id_rap_detail_item', 'ASC')
                ->findAll();

            // Format for frontend
            $formatted = array_map(function ($row) {
                return [
                    'id' => $row['id_rap_detail_item'],
                    'tipe' => $row['jenis_item'], // mapped mapping
                    'uraian' => $row['nama_item'],
                    'merk' => $row['merk'] ?? '',
                    'spesifikasi' => $row['spesifikasi'] ?? '',
                    'koefisien' => (float) $row['koefisien'],
                    'satuan' => $row['satuan'],
                    'hargaSatuan' => (float) $row['harga_satuan'],
                    'sumber' => $row['keterangan'] ?? '',
                ];
            }, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formatted
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveRincian(): ResponseInterface
    {
        try {
            $json = $this->request->getJSON(true);
            $idDetail = $json['id_rap_detail'] ?? null;
            $items = $json['items'] ?? [];

            if (!$idDetail) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id_rap_detail wajib diisi'
                ]);
            }

            $model = new RapDetailItemModel();

            // ── Transaction ──────────────────────────────────────────────────
            $db = \Config\Database::connect();
            $db->transBegin();

            // Get id_project for InventoryHelper
            $rapDetailRow = $db->table('rap_detail rd')
                ->select('r.id_project')
                ->join('rap r', 'r.id_rap = rd.id_rap')
                ->where('rd.id_rap_detail', $idDetail)
                ->get()->getRowArray();
            $idProject = (int) ($rapDetailRow['id_project'] ?? 0);

            // 1. Delete existing
            $model->where('id_rap_detail', $idDetail)->delete();

            // 2. Insert new
            foreach ($items as $index => $item) {
                $jenisItem = $item['tipe'] ?? 'bahan';
                $namaItem = $item['uraian'] ?? '';
                $merk = $item['merk'] ?? '';
                $spesifikasi = $item['spesifikasi'] ?? '';
                $satuan = $item['satuan'] ?? '';

                $idBarang = null;
                if ($idProject > 0) {
                    $idBarang = InventoryHelper::resolveMasterBarang($idProject, $jenisItem, $namaItem, $merk, $spesifikasi, $satuan);
                }

                $inserted = $model->insert([
                    'id_rap_detail' => $idDetail,
                    'id_barang'     => $idBarang,
                    'jenis_item'    => $jenisItem,
                    'nama_item'     => $namaItem,
                    'merk'          => $merk,
                    'spesifikasi'   => $spesifikasi,
                    'koefisien'     => $item['koefisien'] ?? 0,
                    'satuan'        => $satuan,
                    'harga_dasar'   => $item['hargaSatuan'] ?? 0,
                    'harga_satuan'  => $item['hargaSatuan'] ?? 0,
                    'keterangan'    => $item['sumber'] ?? '',
                    'urutan'        => $index + 1,
                ]);
                if (!$inserted) {
                    $db->transRollback();
                    $errors = implode(', ', $model->errors() ?: ['Unknown detail']);
                    throw new \Exception('Insert failed: ' . $errors);
                }
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \Exception('Gagal menyimpan rincian ke database');
            }

            $db->transCommit();

            $rapDetailModel = new RapDetailModel();
            $rapDetail = $rapDetailModel->find($idDetail);

            if ($rapDetail) {
                $totals = ['bahan' => 0.0, 'alat' => 0.0, 'upah' => 0.0];

                $savedItems = $model
                    ->where('id_rap_detail', $idDetail)
                    ->findAll();

                foreach ($savedItems as $si) {
                    $jenis = strtolower($si['jenis_item'] ?? 'bahan');
                    $jumlah = (float) ($si['koefisien'] ?? 0) * (float) ($si['harga_satuan'] ?? 0);
                    if (isset($totals[$jenis])) {
                        $totals[$jenis] += $jumlah;
                    }
                }

                $volume = (float) ($rapDetail['volume'] ?? 1);
                $hargaBahan = $totals['bahan'];
                $hargaAlat = $totals['alat'];
                $hargaUpah = $totals['upah'];
                $subtotalBahan = $volume * $hargaBahan;
                $subtotalAlat = $volume * $hargaAlat;
                $subtotalUpah = $volume * $hargaUpah;
                $totalKeseluruhan = $subtotalBahan + $subtotalAlat + $subtotalUpah;

                $source = $json['source'] ?? null;
                $updateData = [
                    'harga_bahan' => $hargaBahan,
                    'harga_alat' => $hargaAlat,
                    'harga_upah' => $hargaUpah,
                    'subtotal_bahan' => $subtotalBahan,
                    'subtotal_alat' => $subtotalAlat,
                    'subtotal_upah' => $subtotalUpah,
                    'total_keseluruhan' => $totalKeseluruhan,
                ];

                if ($source === 'EMPIRIS') {
                    $updateData['keterangan'] = 'EMPIRIS';
                }

                $rapDetailModel->update($idDetail, $updateData);

                $rapModel = new RapModel();
                $idRap = (int) ($rapDetail['id_rap'] ?? 0);
                if ($idRap > 0) {
                    $allDetails = $rapDetailModel->where('id_rap', $idRap)->findAll();
                    $grandTotal = array_reduce($allDetails, function ($carry, $d) {
                        return $carry + (float) ($d['total_keseluruhan'] ?? 0);
                    }, 0.0);
                    $rapModel->update($idRap, ['total_keseluruhan' => $grandTotal]);
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Rincian AHS berhasil disimpan'
            ]);

        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteItem($id_rap_detail_item): ResponseInterface
    {
        try {
            $model = new RapDetailItemModel();
            if ($model->delete($id_rap_detail_item)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Item berhasil dihapus'
                ]);
            } else {
                throw new \Exception('Gagal menghapus item dari database');
            }
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET /api/ahs/proyek
     * Returns unique bahan/upah/alat items that have been inputted in a given project.
     * Pulls from rap_detail_item → rap_detail → rap → projects chain.
     *
     * Query Params:
     *   id_project    int     – ID of the current project (preferred)
     *   id_rap_detail int     – fallback: derive id_project from this detail ID
     *   q             string  – search keyword on nama_item
     *   tipe          string  – filter by type: 'bahan', 'alat', 'upah', or 'all'
     *   page          int     – page number (default 1)
     */
    public function getProyek(): ResponseInterface
    {
        try {
            $db = \Config\Database::connect();
            $idProject = (int) $this->request->getGet('id_project');
            $idDetail = (int) $this->request->getGet('id_rap_detail');
            $search = $this->request->getGet('q');
            $tipe = $this->request->getGet('tipe');
            $page = max(1, (int) $this->request->getGet('page'));
            $limit = 50;
            $offset = ($page - 1) * $limit;

            // ── Auto-derive id_project from id_rap_detail if not supplied ────
            if ($idProject <= 0 && $idDetail > 0) {
                $row = $db->query(
                    "SELECT r.id_project
                     FROM rap_detail rd
                     INNER JOIN rap r ON r.id_rap = rd.id_rap
                     WHERE rd.id_rap_detail = ?
                     LIMIT 1",
                    [$idDetail]
                )->getRowArray();

                $idProject = (int) ($row['id_project'] ?? 0);
            }

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id_project atau id_rap_detail wajib diisi',
                ]);
            }

            // Join rap_detail_item → rap_detail → rap, filter by id_project
            // Group by nama_item + satuan + jenis_item to get unique rows
            // Use MAX(harga_satuan) as representative price
            $sql = "
                SELECT
                    rdi.jenis_item                         AS tipe,
                    rdi.nama_item                          AS uraian,
                    rdi.satuan,
                    MAX(rdi.harga_satuan)                  AS hargaSatuan,
                    MAX(rdi.merk)                          AS merk,
                    MAX(rdi.spesifikasi)                   AS spesifikasi,
                    MAX(rdi.keterangan)                    AS sumber,
                    COUNT(*)                               AS frekuensi
                FROM rap_detail_item rdi
                INNER JOIN rap_detail rd  ON rd.id_rap_detail = rdi.id_rap_detail
                INNER JOIN rap r          ON r.id_rap         = rd.id_rap
                WHERE r.id_project = ?
            ";

            $params = [$idProject];

            if (!empty($search)) {
                $sql .= ' AND rdi.nama_item LIKE ?';
                $params[] = "%{$search}%";
            }

            if (!empty($tipe) && $tipe !== 'all') {
                $sql .= ' AND rdi.jenis_item = ?';
                $params[] = $tipe;
            }

            $sql .= "
                GROUP BY rdi.jenis_item, rdi.nama_item, rdi.satuan
                ORDER BY rdi.jenis_item ASC, rdi.nama_item ASC
                LIMIT {$limit} OFFSET {$offset}
            ";

            $rows = $db->query($sql, $params)->getResultArray();

            // Assign UID and cast numerics
            foreach ($rows as $i => &$row) {
                $row['hargaSatuan'] = (float) $row['hargaSatuan'];
                $row['id'] = $i + 1 + $offset; // pseudo-id
                $safeUraian = preg_replace('/\W/', '', $row['uraian']);
                $safeUraian = substr($safeUraian, 0, 15);
                $row['_uid'] = $row['tipe'] . '_prj_' . $safeUraian . '_' . $i;
            }
            unset($row);

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'id_project' => $idProject,
                'page' => $page,
                'limit' => $limit,
                'data' => $rows,
            ]);

        } catch (Throwable $e) {
            log_message('error', '[AhsController::getProyek] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memuat data proyek terkini.',
            ]);
        }
    }

    /**
     * GET /api/ahs/shbj
     * Returns unique bahan/upah/alat items sourced from regional government regulations
     * (Kepgub, Pergub, Kepbup, Perbup, Perda, SK, dll).
     * Filters rap_detail_item.keterangan for known regulation keywords.
     *
     * Query Params:
     *   q      string  – search keyword on nama_item
     *   tipe   string  – filter by type: 'bahan', 'alat', 'upah', or 'all'
     *   page   int     – page number (default 1)
     */
    private function getMergedItemsByRegex(string $regexKeywords, string $sourceId): ResponseInterface
    {
        try {
            $dbDefault = \Config\Database::connect();
            $dbEstimator = \Config\Database::connect('estimator');

            $search = $this->request->getGet('q');
            $tipe = $this->request->getGet('tipe');
            $page = max(1, (int) $this->request->getGet('page'));
            $limit = 50;
            $offset = ($page - 1) * $limit;

            // 1. Fetch from Estimator DB (Master)
            $sqlEstimator = "
                SELECT * FROM (
                    SELECT
                        id_bahan      AS id,
                        nama_bahan    AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'bahan'       AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM bahan_utama

                    UNION ALL

                    SELECT
                        id_upah       AS id,
                        nama_upah     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'upah'        AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM upah_utama

                    UNION ALL

                    SELECT
                        id_alat       AS id,
                        nama_alat     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'alat'        AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM alat_utama
                ) AS master_bua
                WHERE master_bua.sumber IS NOT NULL
                  AND master_bua.sumber REGEXP ?
            ";

            $paramsEstimator = [$regexKeywords];

            if (!empty($search)) {
                $sqlEstimator .= ' AND master_bua.uraian LIKE ?';
                $paramsEstimator[] = "%{$search}%";
            }

            if (!empty($tipe) && $tipe !== 'all') {
                $sqlEstimator .= ' AND master_bua.tipe = ?';
                $paramsEstimator[] = $tipe;
            }

            $rowsEstimator = $dbEstimator->query($sqlEstimator, $paramsEstimator)->getResultArray();

            // 2. Fetch from Default DB (Project History)
            $sqlDefault = "
                SELECT
                    rdi.jenis_item                         AS tipe,
                    rdi.nama_item                          AS uraian,
                    rdi.satuan,
                    MAX(rdi.harga_satuan)                  AS hargaSatuan,
                    MAX(rdi.merk)                          AS merk,
                    MAX(rdi.spesifikasi)                   AS spesifikasi,
                    MAX(rdi.keterangan)                    AS sumber,
                    COUNT(*)                               AS frekuensi
                FROM rap_detail_item rdi
                WHERE rdi.keterangan IS NOT NULL
                  AND rdi.keterangan REGEXP ?
            ";

            $paramsDefault = [$regexKeywords];

            if (!empty($search)) {
                $sqlDefault .= ' AND rdi.nama_item LIKE ?';
                $paramsDefault[] = "%{$search}%";
            }

            if (!empty($tipe) && $tipe !== 'all') {
                $sqlDefault .= ' AND rdi.jenis_item = ?';
                $paramsDefault[] = $tipe;
            }

            $sqlDefault .= " GROUP BY rdi.jenis_item, rdi.nama_item, rdi.satuan";

            $rowsDefault = $dbDefault->query($sqlDefault, $paramsDefault)->getResultArray();

            // 3. Merge and Deduplicate
            $merged = [];

            // Process Estimator first (acts as primary master data)
            foreach ($rowsEstimator as $row) {
                $key = strtolower(trim($row['tipe']) . '|' . trim($row['uraian']) . '|' . trim($row['satuan']));
                $row['frekuensi'] = 1; // Default frekuensi for master DB items
                $merged[$key] = $row;
            }

            // Merge Project History
            foreach ($rowsDefault as $row) {
                $key = strtolower(trim($row['tipe']) . '|' . trim($row['uraian']) . '|' . trim($row['satuan']));
                if (!isset($merged[$key])) {
                    $merged[$key] = $row;
                } else {
                    // Update frequency if it already exists from Master
                    $merged[$key]['frekuensi'] += $row['frekuensi'];
                }
            }

            $combinedRows = array_values($merged);

            // Sort by tipe ASC, uraian ASC
            usort($combinedRows, function ($a, $b) {
                $cmpTipe = strcmp($a['tipe'], $b['tipe']);
                if ($cmpTipe !== 0)
                    return $cmpTipe;
                return strcmp($a['uraian'], $b['uraian']);
            });

            // 4. Pagination
            $pagedRows = array_slice($combinedRows, $offset, $limit);

            // Assign UID and cast numerics
            foreach ($pagedRows as $i => &$row) {
                $row['hargaSatuan'] = (float) $row['hargaSatuan'];
                $row['id'] = $i + 1 + $offset;
                $safeUraian = preg_replace('/\W/', '', $row['uraian']);
                $safeUraian = substr($safeUraian, 0, 15);
                $row['_uid'] = $row['tipe'] . '_' . $sourceId . '_' . $safeUraian . '_' . $i;
            }
            unset($row);

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'page' => $page,
                'limit' => $limit,
                'data' => $pagedRows,
            ]);

        } catch (Throwable $e) {
            log_message('error', '[AhsController::getMergedItemsByRegex] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memuat data sumber ' . strtoupper($sourceId) . '.',
            ]);
        }
    }

    /**
     * GET /api/ahs/shbj
     */
    public function getShbj(): ResponseInterface
    {
        return $this->getMergedItemsByRegex('kepgub|pergub|kepbup|perbup|perda|peraturan bupati|peraturan gubernur|keputusan gubernur|keputusan bupati|sk bupati|sk gubernur|surat keputusan|sk walikota|perwal|perwali|instruksi bupati', 'shbj');
    }

    /**
     * GET /api/ahs/survey
     */
    public function getSurvey(): ResponseInterface
    {
        return $this->getMergedItemsByRegex('survey|survei', 'survey');
    }

    /**
     * GET /api/ahs/estimatorid
     */
    public function getEstimatorId(): ResponseInterface
    {
        return $this->getMergedItemsByRegex('estimator\.id', 'estimatorid');
    }

    /**
     * GET /api/ahs
     * Returns a paginated list of AHS items (bahan, upah, alat) from master tables.
     *
     * Query Params:
     *   q      string  – full-text search on uraian, merk, spesifikasi
     *   tipe   string  – filter by type: 'bahan', 'alat', 'upah', or 'all'
     *   page   int     – page number (default 1)
     * 
     */
    public function index(): ResponseInterface
    {
        try {
            $db = \Config\Database::connect('estimator');
            $search = $this->request->getGet('q');
            $tipe = $this->request->getGet('tipe');
            $page = max(1, (int) $this->request->getGet('page'));
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $sql = "
                SELECT * FROM (
                    SELECT
                        id_bahan      AS id,
                        nama_bahan    AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'bahan'       AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM bahan_utama

                    UNION ALL

                    SELECT
                        id_upah       AS id,
                        nama_upah     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'upah'        AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM upah_utama

                    UNION ALL

                    SELECT
                        id_alat       AS id,
                        nama_alat     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'alat'        AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM alat_utama
                ) AS master_bua
                WHERE 1=1
            ";

            $params = [];

            if (!empty($search)) {
                $sql .= ' AND (master_bua.uraian LIKE ? OR master_bua.merk LIKE ? OR master_bua.spesifikasi LIKE ?)';
                $term = "%{$search}%";
                $params[] = $term;
                $params[] = $term;
                $params[] = $term;
            }

            if (!empty($tipe) && $tipe !== 'all') {
                $sql .= ' AND master_bua.tipe = ?';
                $params[] = $tipe;
            }

            $sql .= " ORDER BY master_bua.uraian ASC, master_bua.id ASC
                      LIMIT {$limit} OFFSET {$offset}";

            $data = $db->query($sql, $params)->getResultArray();

            foreach ($data as &$row) {
                $row['hargaSatuan'] = (float) $row['hargaSatuan'];
            }
            unset($row);

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'status' => 'success',
                    'page' => $page,
                    'limit' => $limit,
                    'data' => $data,
                ]);

        } catch (\Throwable $e) {
            log_message('error', '[AhsController::index] ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memuat data AHS. Silakan coba lagi.',
                ]);
        }
    }

    /**
     * GET /api/ahs/search-master-barang
     * Autocomplete endpoint to fetch master_barang across projects
     */
    public function searchMasterBarang(): ResponseInterface
    {
        try {
            $db = \Config\Database::connect();
            $search = $this->request->getGet('q');
            $tipe = $this->request->getGet('tipe'); // bahan, alat, upah
            $limit = 10;

            if (empty($search)) {
                return $this->response->setJSON(['status' => 'success', 'data' => []]);
            }

            $sql = "
                SELECT 
                    id, 
                    nama_barang AS uraian, 
                    satuan, 
                    merk, 
                    spesifikasi, 
                    jenis_item AS tipe 
                FROM master_barang 
                WHERE nama_barang LIKE ?
            ";
            
            $params = ["%{$search}%"];

            if (!empty($tipe) && $tipe !== 'all') {
                $sql .= " AND jenis_item = ?";
                // capitalize first letter to match DB (e.g. 'Bahan')
                $params[] = ucfirst(strtolower($tipe));
            }

            $sql .= " LIMIT {$limit}";

            $data = $db->query($sql, $params)->getResultArray();

            // Format numeric prices if needed (master_barang doesn't have prices usually, fallback to 0)
            foreach ($data as &$row) {
                $row['hargaSatuan'] = 0; // Price needs to be manually entered or fetched from master_data_harga
            }
            unset($row);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[AhsController::searchMasterBarang] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memuat data autocomplete.'
            ]);
        }
    }
}