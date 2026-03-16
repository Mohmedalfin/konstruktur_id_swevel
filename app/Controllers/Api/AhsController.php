<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use Throwable;

class AhsController extends BaseController
{
    /**
     * GET /api/ahs
     * Returns a paginated list of AHS items (bahan, upah, alat) from master tables.
     *
     * Query Params:
     *   q      string  – full-text search on uraian, merk, spesifikasi
     *   tipe   string  – filter by type: 'bahan', 'alat', 'upah', or 'all'
     *   page   int     – page number (default 1)
     */
    public function index(): ResponseInterface
    {
        try {
            $db     = \Config\Database::connect('estimator');
            $search = $this->request->getGet('q');
            $tipe   = $this->request->getGet('tipe');
            $page   = max(1, (int) $this->request->getGet('page'));
            $limit  = 20;
            $offset = ($page - 1) * $limit;

            // ── Build UNION query across three master tables ──────────────────
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

<<<<<<< HEAD
                    SELECT 
                        id_upah AS id, 
                        nama_upah AS uraian, 
                        satuan, 
                        sumber, 
                        spesifikasi, 
                        merk, 
                        'upah' AS tipe,
                        0 AS hargaSatuan
=======
                    SELECT
                        id_upah       AS id,
                        nama_upah     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'upah'        AS tipe,
                        harga_dasar   AS hargaSatuan
>>>>>>> origin/model-rap
                    FROM upah_utama

                    UNION ALL

<<<<<<< HEAD
                    SELECT 
                        id_alat AS id, 
                        nama_alat AS uraian, 
                        satuan, 
                        sumber, 
                        spesifikasi, 
                        merk, 
                        'alat' AS tipe,
                        0 AS hargaSatuan
=======
                    SELECT
                        id_alat       AS id,
                        nama_alat     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'alat'        AS tipe,
                        harga_dasar   AS hargaSatuan
>>>>>>> origin/model-rap
                    FROM alat_utama
                ) AS master_bua
                WHERE 1=1
            ";

            $params = [];

<<<<<<< HEAD
            if (!empty($search)) {
                $sql .= " AND (master_bua.uraian LIKE ? OR master_bua.merk LIKE ? OR master_bua.spesifikasi LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if (!empty($tipe) && $tipe !== 'all') {
                $sql .= " AND master_bua.tipe = ?";
                $params[] = $tipe;
            }

            $sql .= " ORDER BY master_bua.uraian ASC, master_bua.id ASC";

            $page = (int) $this->request->getGet('page');
            if ($page < 1) {
                $page = 1;
            }

            $limit  = 20;
            $offset = ($page - 1) * $limit;

            $sql .= " LIMIT {$limit} OFFSET {$offset}";

            $query = $db->query($sql, $params);
            $data  = $query->getResultArray();

            foreach ($data as &$row) {
                $row['hargaSatuan'] = (float) $row['hargaSatuan'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
=======
            // ── Optional: full-text search ────────────────────────────────────
            if (!empty($search)) {
                $sql .= ' AND (master_bua.uraian LIKE ? OR master_bua.merk LIKE ? OR master_bua.spesifikasi LIKE ?)';
                $term      = "%{$search}%";
                $params[]  = $term;
                $params[]  = $term;
                $params[]  = $term;
            }

            // ── Optional: filter by tipe ──────────────────────────────────────
            if (!empty($tipe) && $tipe !== 'all') {
                $sql     .= ' AND master_bua.tipe = ?';
                $params[] = $tipe;
            }

            $sql .= " ORDER BY master_bua.uraian ASC, master_bua.id ASC
                      LIMIT {$limit} OFFSET {$offset}";

            $data = $db->query($sql, $params)->getResultArray();

            // Cast numeric fields
            foreach ($data as &$row) {
                $row['hargaSatuan'] = (float) $row['hargaSatuan'];
            }
            unset($row);

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'status' => 'success',
                    'page'   => $page,
                    'limit'  => $limit,
                    'data'   => $data,
                ]);

        } catch (\Throwable $e) {
            log_message('error', '[AhsController::index] ' . $e->getMessage());

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_ERROR)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal memuat data AHS. Silakan coba lagi.',
                ]);
>>>>>>> origin/model-rap
        }
    }
}