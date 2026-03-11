<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PekerjaanModel;
use Throwable;

class PekerjaanController extends BaseController
{
    public function index()
    {
        try {
            $pekerjaanModel = new PekerjaanModel();

            $page   = (int) ($this->request->getGet('page') ?? 1);
            $limit  = (int) ($this->request->getGet('limit') ?? 20);
            $search = trim((string) ($this->request->getGet('q') ?? ''));
            $sumber = $this->request->getGet('sumber');

            if ($page < 1) {
                $page = 1;
            }

            if ($limit < 1) {
                $limit = 20;
            }

            if ($limit > 100) {
                $limit = 100;
            }

            $offset = ($page - 1) * $limit;

            $builder = $pekerjaanModel->select("
                urut,
                nama_pekerjaan AS nama,
                satuan,
                keterangan AS sumber,
                id_pekerjaan AS id,
                0 AS harga
            ");

            if ($search !== '') {
                $builder->groupStart()
                    ->like('nama_pekerjaan', $search)
                    ->orLike('keterangan', $search)
                    ->groupEnd();
            }

            if (!empty($sumber)) {
                if (is_array($sumber)) {
                    $builder->whereIn('keterangan', $sumber);
                } else {
                    $sumberArray = array_filter(array_map('trim', explode(',', $sumber)));
                    if (!empty($sumberArray)) {
                        $builder->whereIn('keterangan', $sumberArray);
                    }
                }
            }

            $total = $builder->countAllResults(false);

            $data = $builder->orderBy('urut', 'ASC')
                ->orderBy('id_pekerjaan', 'ASC')
                ->findAll($limit, $offset);

            return $this->response->setJSON([
                'status' => 'success',
                'total'  => $total,
                'page'   => $page,
                'limit'  => $limit,
                'data'   => $data,
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}