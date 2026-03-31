<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PekerjaanModel;
use App\Models\PekerjaanKustomModel;
use App\Models\ProyekModel;
use CodeIgniter\HTTP\ResponseInterface;

class PekerjaanController extends BaseController
{
    /**
     * GET /api/pekerjaan
     * Returns a merged list of bawaan (estimator DB) + kustom (local DB) pekerjaan.
     *
     * Query Params:
     *   slug    string       – project slug (required for custom data)
     *   q       string       – search on nama_pekerjaan
     *   sumber  string|array – filter by source (SNI, PUPR, Empiris, Estimator.id, Proyek Terkini)
     *   page    int          – page number (default 1)
     *   limit   int          – items per page (default 20)
     */
    public function index(): ResponseInterface
    {
        try {
            $page   = max(1, (int) ($this->request->getGet('page')  ?? 1));
            $limit  = max(1, (int) ($this->request->getGet('limit') ?? 20));
            $search = trim((string) ($this->request->getGet('q')    ?? ''));
            $sumber = $this->request->getGet('sumber');
            $slug   = trim((string) ($this->request->getGet('slug') ?? ''));
            $offset = ($page - 1) * $limit;

            // Resolve sumber filters
            $sumberArr     = [];
            $includeCustom = false;
            if (!empty($sumber)) {
                $sumberArr = is_array($sumber) ? $sumber : explode(',', $sumber);
                $sumberArr = array_map('trim', $sumberArr);
                $includeCustom = in_array('Proyek Terkini', $sumberArr);
            }
            $hasSystemFilter = !empty(array_filter($sumberArr, fn($s) => $s !== 'Proyek Terkini'));

            // ── Query 1: Data Bawaan Sistem (estimator DB) ────────────────────────
            $systemData = [];
            // Only fetch system data if no sumber filter, or if non-custom sumber is requested
            if (empty($sumberArr) || $hasSystemFilter) {
                $model   = new PekerjaanModel();
                $builder = $model->select('id_pekerjaan AS id, nama_pekerjaan AS nama, satuan, keterangan AS sumber, 0 AS harga');

                if ($search !== '') {
                    $builder->groupStart()->like('nama_pekerjaan', $search)->orLike('keterangan', $search)->groupEnd();
                }

                if ($hasSystemFilter) {
                    $keywordMap = ['SNI' => 'SNI', 'PUPR' => 'PUPR', 'Empiris' => 'Empiris', 'Estimator.id' => 'Estimator'];
                    $systemSumber = array_filter($sumberArr, fn($s) => $s !== 'Proyek Terkini');
                    $builder->groupStart();
                    $first = true;
                    foreach ($systemSumber as $s) {
                        $kw = $keywordMap[$s] ?? $s;
                        if ($first) { $builder->like('keterangan', $kw); $first = false; }
                        else        { $builder->orLike('keterangan', $kw); }
                    }
                    $builder->groupEnd();
                }

                $rawSystem  = $builder->orderBy('urut', 'ASC')->orderBy('id_pekerjaan', 'ASC')->findAll();

                // Set PHP booleans explicitly so JSON encodes them correctly (not as string "0")
                foreach ($rawSystem as &$row) {
                    $row['is_custom'] = false;
                    $row['db_id']     = null;
                }
                unset($row);
                $systemData = $rawSystem;
            }

            // ── Query 2: Data Kustom Proyek (local DB) ────────────────────────────
            $customData = [];
            if (empty($sumberArr) || $includeCustom) {
                if (!empty($slug)) {
                    $proyekModel = new ProyekModel();
                    $proyek      = $proyekModel->where('slug', $slug)->first();
                    if ($proyek) {
                        $kustom  = new PekerjaanKustomModel();
                        $qb      = $kustom->select('id_pekerjaan AS db_id, nama_pekerjaan AS nama, satuan, sumber, 0 AS harga, 1 AS is_custom, id_pekerjaan AS id, id_kategori_pekerjaan');
                        $qb->where('id_proyek', $proyek['id_proyek']);
                        if ($search !== '') {
                            $qb->like('nama_pekerjaan', $search);
                        }
                        $rawCustom = $qb->orderBy('id_pekerjaan', 'DESC')->findAll();

                        // Prefix custom IDs to avoid collision with estimator IDs
                        foreach ($rawCustom as &$row) {
                            $row['id']        = 'kustom_' . $row['db_id'];
                            $row['is_custom'] = true;
                            $row['sumber']    = $row['sumber'] ?: 'Proyek Terkini';
                        }
                        unset($row);
                        $customData = $rawCustom;
                    }
                }
            }

            // ── Merge: Custom first, then system ─────────────────────────────────
            $merged = array_merge($customData, $systemData);
            $total  = count($merged);
            $data   = array_slice($merged, $offset, $limit);

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
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['status' => 'error', 'message' => 'Gagal memuat data pekerjaan.']);
        }
    }

    /**
     * POST /api/pekerjaan/kustom
     * Store a new custom pekerjaan for a given project.
     */
    public function store(): ResponseInterface
    {
        try {
            $json = $this->request->getJSON();
            if (!$json || !isset($json->slug) || !isset($json->nama_pekerjaan)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Payload tidak lengkap (slug & nama_pekerjaan wajib)']);
            }

            $proyekModel = new ProyekModel();
            $proyek      = $proyekModel->where('slug', $json->slug)->first();
            if (!$proyek) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Proyek tidak ditemukan']);
            }

            $kodeKustom = 'kustom_' . substr(md5(uniqid(rand(), true)), 0, 8);

            $insertData = [
                'id_proyek'             => $proyek['id_proyek'],
                'id_kategori_pekerjaan' => isset($json->id_kategori_pekerjaan) ? (int) $json->id_kategori_pekerjaan : null,
                'kode_pekerjaan'        => $kodeKustom,
                'nama_pekerjaan'        => $json->nama_pekerjaan,
                'satuan'                => $json->satuan ?? '',
                'sumber'                => 'Proyek Terkini',
            ];

            $kustom  = new PekerjaanKustomModel();
            $kustom->skipValidation(true)->insert($insertData);
            $newId = $kustom->getInsertID();

            return $this->response->setStatusCode(201)->setJSON([
                'status' => 'success',
                'data'   => array_merge($insertData, [
                    'id'        => 'kustom_' . $newId,
                    'db_id'     => $newId,
                    'is_custom' => true,
                    'harga'     => 0,
                ]),
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[PekerjaanController::store] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan pekerjaan kustom.']);
        }
    }

    /**
     * PUT /api/pekerjaan/kustom/{id}
     * Update a custom pekerjaan (nama & satuan only).
     */
    public function update(int $id): ResponseInterface
    {
        try {
            $kustom   = new PekerjaanKustomModel();
            $existing = $kustom->find($id);
            if (!$existing) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pekerjaan tidak ditemukan']);
            }

            $json = $this->request->getJSON();
            if (!$json) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Data kosong']);
            }

            $updateData = [];
            if (isset($json->nama_pekerjaan)) $updateData['nama_pekerjaan'] = $json->nama_pekerjaan;
            if (isset($json->satuan))         $updateData['satuan']         = $json->satuan;

            if (!empty($updateData)) {
                $kustom->update($id, $updateData);
            }

            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Pekerjaan diperbarui']);

        } catch (\Throwable $e) {
            log_message('error', '[PekerjaanController::update] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui pekerjaan.']);
        }
    }

    /**
     * DELETE /api/pekerjaan/kustom/{id}
     * Delete a custom pekerjaan from local DB.
     */
    public function destroy(int $id): ResponseInterface
    {
        try {
            $kustom   = new PekerjaanKustomModel();
            $existing = $kustom->find($id);
            if (!$existing) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Pekerjaan tidak ditemukan']);
            }

            $kustom->delete($id);
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Pekerjaan dihapus']);

        } catch (\Throwable $e) {
            log_message('error', '[PekerjaanController::destroy] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal menghapus pekerjaan.']);
        }
    }
}
