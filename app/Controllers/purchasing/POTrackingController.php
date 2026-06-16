<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;

class POTrackingController extends BaseController
{
    protected $poModel;
    protected $poItemModel;

    public function __construct()
    {
        $this->poModel = new PurchaseOrderModel();
        $this->poItemModel = new PurchaseOrderItemModel();
    }

    public function index()
    {
        $data = [
            'title' => 'PO Tracking - Kontraktor.id',
            'pos'   => $this->poModel->getPOsWithSupplier()
        ];

        return view('purchasing/po-tracking/index', $data);
    }

    public function getDetail($id)
    {
        $po = $this->poModel->getPOWithDetails($id);
        
        if ($po) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $po
            ]);
        }
        
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Data PO tidak ditemukan'
        ])->setStatusCode(404);
    }

    public function updateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Access Denied');
        }

        $json = $this->request->getJSON();
        $newStatus = $json->status ?? null;

        $validStatuses = ['diproses', 'dalam pengiriman', 'selesai tiba'];
        
        if (!in_array($newStatus, $validStatuses)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Status tidak valid'
            ]);
        }

        $updated = $this->poModel->update($id, ['status' => $newStatus]);

        if ($updated) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Status PO berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal memperbarui status PO'
        ]);
    }
}
