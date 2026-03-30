<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PekerjaanModel;
use CodeIgniter\HTTP\ResponseInterface;

class PekerjaanController extends BaseController
{
    /**
     * GET /api/pekerjaan
     * Returns a paginated list of pekerjaan utama filtered by search and sumber.
     *
     * Query Params:
     *   q       string       – full-text search on nama_pekerjaan, keterangan
     *   sumber  string|array – filter by source category (SNI, PUPR, Empiris, Estimator.id, Proyek Terkini)
     *   page    int          – page number (default 1)
     *   limit   int          – items per page (default 20)
     */
    public function index(): ResponseInterface
    {
        try {
            $masterModel = new PekerjaanModel();      // Estimator DB
            $localModel  = new \App\Models\LocalPekerjaanModel(); // Default DB

            $page   = max(1, (int) ($this->request->getGet('page')  ?? 1));
            $limit  = max(1, (int) ($this->request->getGet('limit') ?? 20));
            $search = trim((string) ($this->request->getGet('q')      ?? ''));
            $sumber = $this->request->getGet('sumber');
            $offset = ($page - 1) * $limit;

            // ── Collect local items if needed ─────────────────────────────────
            $localItems = [];
            $includeLocal = false;

            if (empty($sumber)) {
                $includeLocal = true; // All sources
            } else {
                $sumberArr = is_array($sumber) ? $sumber : explode(',', $sumber);
                if (in_array('Proyek Terkini', $sumberArr)) {
                    $includeLocal = true;
                }
            }

            if ($includeLocal) {
                // Use single quotes for string literals and disable escaping for this custom select
                $localModel->select('0 AS urut, nama_pekerjaan AS nama, satuan, \'Proyek Terkini\' AS sumber, id_pekerjaan AS id, 0 AS harga', false);
                
                $idProject = $this->request->getGet('id_project') ?: $this->request->getGet('id');
                if ($idProject) {
                    $localModel->where('id_project', $idProject);
                }

                if ($search !== '') {
                    $localModel->like('nama_pekerjaan', $search);
                }
                $localItems = $localModel->findAll();
            }

            // ── Fetch Master Items (with Pagination) ─────────────────────────
            $masterBuilder = $masterModel->select(
                'urut, nama_pekerjaan AS nama, satuan, keterangan AS sumber, id_pekerjaan AS id, 0 AS harga'
            );

            if ($search !== '') {
                $masterBuilder->groupStart()
                        ->like('nama_pekerjaan', $search)
                        ->orLike('keterangan',   $search)
                        ->groupEnd();
            }

            $includeMaster = true;
            if (!empty($sumber)) {
                $sumberArr = is_array($sumber) ? $sumber : explode(',', $sumber);
                $sumberArr = array_map('trim', $sumberArr);

                $keywordMap = ['SNI' => 'SNI', 'PUPR' => 'PUPR', 'Empiris' => 'Empiris', 'Estimator.id' => 'Estimator'];
                $patterns = [];
                $onlyLocal = true;
                foreach ($sumberArr as $s) {
                    if (isset($keywordMap[$s])) {
                        $patterns[] = $keywordMap[$s];
                        $onlyLocal = false;
                    }
                }

                if ($onlyLocal && in_array('Proyek Terkini', $sumberArr)) {
                    $includeMaster = false;
                } else if (!empty($patterns)) {
                    $masterBuilder->groupStart();
                    foreach ($patterns as $index => $keyword) {
                        if ($index === 0) $masterBuilder->like('keterangan', $keyword);
                        else $masterBuilder->orLike('keterangan', $keyword);
                    }
                    $masterBuilder->groupEnd();
                }
            }

            $localTotal  = count($localItems);
            $masterTotal = $includeMaster ? $masterBuilder->countAllResults(false) : 0;
            $total       = $localTotal + $masterTotal;

            // ── Pagination Calculation ───────────────────────────────────────
            // If local items fill the first pages, we need to adjust master offset
            $masterData = [];
            if ($includeMaster) {
                $masterOffset = ($page === 1) ? 0 : max(0, $offset - $localTotal);
                $masterLimit  = ($page === 1) ? max(0, $limit - $localTotal) : $limit;
                
                if ($masterLimit > 0) {
                    $masterData = $masterBuilder->orderBy('urut', 'ASC')->findAll($masterLimit, $masterOffset);
                }
            }

            // ── Final Merging ──────────────────────────────────────────────────
            $data = [];
            if ($page === 1) {
                // Page 1: [Local Items] + [Head of Master Data]
                $data = array_merge($localItems, $masterData);
            } else {
                // Page > 1: Just the calculated Master Data slice
                $data = $masterData;
            }

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'status' => 'success',
                    'total'  => $total,
                    'page'   => $page,
                    'limit'  => $limit,
                    'data'   => array_slice($data, 0, $limit),
                ]);

        } catch (\Throwable $e) {
            log_message('error', '[PekerjaanController::index] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * POST /api/pekerjaan/custom
     * Saves a new custom pekerjaan to the local DB.
     */
    public function storeCustom(): ResponseInterface
    {
        try {
            $json   = $this->request->getJSON(true);
            $nama   = trim($json['nama']   ?? '');
            $satuan = trim($json['satuan'] ?? 'm2');
            $idProject = $json['id_project'] ?? null;

            if (empty($nama)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Nama pekerjaan wajib diisi',
                ]);
            }

            $model = new \App\Models\LocalPekerjaanModel();
            $data = [
                'nama_pekerjaan' => $nama,
                'satuan'         => $satuan,
                'keterangan'     => 'Proyek Terkini',
                'id_project'     => $idProject,
            ];

            if ($model->insert($data)) {
                return $this->response->setStatusCode(201)->setJSON([
                    'status'  => 'success',
                    'message' => 'Pekerjaan kustom berhasil disimpan ke database',
                    'data'    => array_merge($data, ['id' => $model->getInsertID()]),
                ]);
            }

            throw new \Exception('Gagal menyimpan ke database');

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}