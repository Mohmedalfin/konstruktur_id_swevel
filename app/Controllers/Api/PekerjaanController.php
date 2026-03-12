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
            $model  = new PekerjaanModel();
            $page   = max(1, (int) ($this->request->getGet('page')  ?? 1));
            $limit  = max(1, (int) ($this->request->getGet('limit') ?? 20));
            $search = trim((string) ($this->request->getGet('q')      ?? ''));
            $sumber = $this->request->getGet('sumber');
            $offset = ($page - 1) * $limit;

            $builder = $model->select(
                'urut, nama_pekerjaan AS nama, satuan, keterangan AS sumber, id_pekerjaan AS id, 0 AS harga'
            );

            // ── Filter: full-text search ──────────────────────────────────────
            if ($search !== '') {
                $builder->groupStart()
                        ->like('nama_pekerjaan', $search)
                        ->orLike('keterangan',   $search)
                        ->groupEnd();
            }

            // ── Filter: sumber via LIKE keyword on keterangan ─────────────────
            if (!empty($sumber)) {
                $sumberArr = is_array($sumber) ? $sumber : explode(',', $sumber);
                $sumberArr = array_map('trim', $sumberArr);

                // Maps checkbox value → keyword that appears in keterangan
                $keywordMap = [
                    'SNI'          => 'SNI',
                    'PUPR'         => 'PUPR',
                    'Empiris'      => 'Empiris',
                    'Estimator.id' => 'Estimator',
                ];

                $patterns      = [];
                $includeCustom = false;

                foreach ($sumberArr as $s) {
                    if ($s === 'Proyek Terkini') {
                        $includeCustom = true;
                    } elseif (isset($keywordMap[$s])) {
                        $patterns[] = $keywordMap[$s];
                    }
                }

                if (!empty($patterns) || $includeCustom) {
                    $builder->groupStart();

                    $firstPattern = true;
                    foreach ($patterns as $keyword) {
                        if ($firstPattern) {
                            $builder->like('keterangan', $keyword);
                            $firstPattern = false;
                        } else {
                            $builder->orLike('keterangan', $keyword);
                        }
                    }

                    // "Proyek Terkini" = rows that contain none of the known keywords
                    if ($includeCustom) {
                        $builder->orGroupStart();
                        foreach ($keywordMap as $keyword) {
                            $builder->notLike('keterangan', $keyword);
                        }
                        $builder->groupEnd();
                    }

                    $builder->groupEnd();
                }
            }

            $total = $builder->countAllResults(false);
            $data  = $builder->orderBy('urut', 'ASC')
                             ->orderBy('id_pekerjaan', 'ASC')
                             ->findAll($limit, $offset);

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'status' => 'success',
                    'total'  => $total,
                    'page'   => $page,
                    'limit'  => $limit,
                    'data'   => $data,
                ]);

        } catch (\Throwable $e) {
            log_message('error', '[PekerjaanController::index] ' . $e->getMessage());

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_ERROR)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal memuat data pekerjaan. Silakan coba lagi.',
                ]);
        }
    }
}
