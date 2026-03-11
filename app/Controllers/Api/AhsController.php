<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use Throwable;

class AhsController extends BaseController
{
    public function index()
    {
        try {
            $db = \Config\Database::connect('estimator');

            $search = $this->request->getGet('q');
            $tipe   = $this->request->getGet('tipe');

            $sql = "
                SELECT * FROM (
                    SELECT 
                        id_bahan AS id, 
                        nama_bahan AS uraian, 
                        satuan, 
                        sumber, 
                        spesifikasi, 
                        merk, 
                        'bahan' AS tipe,
                        0 AS hargaSatuan
                    FROM bahan_utama

                    UNION ALL

                    SELECT 
                        id_upah AS id, 
                        nama_upah AS uraian, 
                        satuan, 
                        sumber, 
                        spesifikasi, 
                        merk, 
                        'upah' AS tipe,
                        0 AS hargaSatuan
                    FROM upah_utama

                    UNION ALL

                    SELECT 
                        id_alat AS id, 
                        nama_alat AS uraian, 
                        satuan, 
                        sumber, 
                        spesifikasi, 
                        merk, 
                        'alat' AS tipe,
                        0 AS hargaSatuan
                    FROM alat_utama
                ) AS master_bua
                WHERE 1=1
            ";

            $params = [];

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
        }
    }
}