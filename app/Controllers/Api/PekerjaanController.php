<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PekerjaanModel;

class PekerjaanController extends BaseController
{
    public function index()
    {
        $pekerjaanModel = new PekerjaanModel();
        
        $page = $this->request->getGet('page') ?? 1;
        $limit = $this->request->getGet('limit') ?? 20;
        $search = $this->request->getGet('q');
        $sumber = $this->request->getGet('sumber');
        
        $offset = ($page - 1) * $limit;
        
        $builder = $pekerjaanModel->select('urut, nama_pekerjaan as nama, satuan, keterangan as sumber, id_pekerjaan as id, 0 as harga');
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nama_pekerjaan', $search)
                    ->orLike('keterangan', $search)
                    ->groupEnd();
        }
        
        if (!empty($sumber)) {
            if (is_array($sumber)) {
                $builder->whereIn('keterangan', $sumber);
            } else {
                $sumberArray = explode(',', $sumber);
                $builder->whereIn('keterangan', $sumberArray);
            }
        }
        
        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('urut', 'ASC')
                        ->orderBy('id_pekerjaan', 'ASC')
                        ->findAll($limit, $offset);
                        
        return $this->response->setJSON([
            'status' => 'success',
            'total'  => $total,
            'page'   => (int) $page,
            'limit'  => (int) $limit,
            'data'   => $data
        ]);
    }
}
