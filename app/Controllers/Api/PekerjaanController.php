<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PekerjaanModel;

class PekerjaanController extends BaseController
{
    public function index()
    {
        $pekerjaanModel = new PekerjaanModel();

        $page   = (int) ($this->request->getGet('page')  ?? 1);
        $limit  = (int) ($this->request->getGet('limit') ?? 20);
        $search = $this->request->getGet('q');
        $sumber = $this->request->getGet('sumber');   // array dari checkbox
        $offset = ($page - 1) * $limit;

        $builder = $pekerjaanModel->select(
            'urut, nama_pekerjaan AS nama, satuan, keterangan AS sumber, id_pekerjaan AS id, 0 AS harga'
        );

        // ── Filter: nama pekerjaan ────────────────────────────────────────────
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nama_pekerjaan', $search)
                    ->orLike('keterangan', $search)
                    ->groupEnd();
        }

        // ── Filter: sumber (LIKE keyword pada keterangan) ─────────────────────
        if (!empty($sumber)) {
            $sumberArr = is_array($sumber) ? $sumber : explode(',', $sumber);

            // Keyword mapping: nilai checkbox → keyword yang dicari di keterangan
            $keywordMap = [
                'SNI'          => 'SNI',
                'PUPR'         => 'PUPR',
                'Empiris'      => 'Empiris',
                'Estimator.id' => 'Estimator',
            ];

            $patterns      = [];
            $includeCustom = false;

            foreach ($sumberArr as $s) {
                $s = trim($s);
                if ($s === 'Proyek Terkini') {
                    $includeCustom = true;
                } elseif (isset($keywordMap[$s])) {
                    $patterns[] = $keywordMap[$s];
                }
            }

            if (!empty($patterns) || $includeCustom) {
                $builder->groupStart();

                $addedOr = false;
                foreach ($patterns as $kw) {
                    if (!$addedOr) {
                        $builder->like('keterangan', $kw);
                        $addedOr = true;
                    } else {
                        $builder->orLike('keterangan', $kw);
                    }
                }

                // Proyek Terkini = entri yang tidak mengandung keyword apapun
                if ($includeCustom) {
                    $builder->orGroupStart();
                    foreach ($keywordMap as $kw) {
                        $builder->notLike('keterangan', $kw);
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

        return $this->response->setJSON([
            'status' => 'success',
            'total'  => $total,
            'page'   => $page,
            'limit'  => $limit,
            'data'   => $data,
        ]);
    }
}
