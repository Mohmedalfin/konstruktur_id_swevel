<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AhsController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $search = $this->request->getGet('q');
        $tipe = $this->request->getGet('tipe'); // bahan, alat, upah atau all
        
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
        
        // --- Pagination Logic ---
        $page = (int) $this->request->getGet('page');
        if ($page < 1) $page = 1;
        
        $limit = 20; // 20 item per loading
        $offset = ($page - 1) * $limit;
        
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        // ------------------------
        
        $query = $db->query($sql, $params);
        $data = $query->getResultArray();
        
        // Convert hargaSatuan to float just to be safe
        foreach ($data as &$row) {
            $row['hargaSatuan'] = (float) $row['hargaSatuan'];
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }
}
