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
        $pos = $this->poModel->getPOsWithSupplier(session()->get('id_perusahaan'));
        
        $stats = [
            'total' => count($pos),
            'diproses' => 0,
            'pengiriman' => 0,
            'selesai' => 0
        ];
        
        foreach ($pos as $po) {
            $status = strtolower($po['status']);
            if ($status == 'diproses' || $status == 'proses') $stats['diproses']++;
            elseif ($status == 'pengiriman' || $status == 'dikirim') $stats['pengiriman']++;
            elseif ($status == 'selesai' || $status == 'selesai_tiba') $stats['selesai']++;
            else $stats['diproses']++;
        }

        $data = [
            'title' => 'PO Tracking - Kontraktor.id',
            'pos'   => $pos,
            'stats' => $stats
        ];

        $data['activeNav'] = 'po-tracking';
        return view('purchasing/po-tracking/index', $data);
    }

    public function getDetail($id)
    {
        $po = $this->poModel->getPOWithDetails($id, session()->get('id_perusahaan'));
        
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
            try {
                if ($newStatus === 'dalam pengiriman') {
                    $notifService = new \App\Services\NotificationService();
                    $po = $this->poModel->find($id);
                    if ($po) {
                        $notifService->sendToRole(
                            'gudang',
                            'Barang Dalam Pengiriman 🚚',
                            "Pesanan PO {$po['po_number']} sedang dalam pengiriman oleh supplier.",
                            '/gudang/pengadaan',
                            'fa-solid fa-truck-fast',
                            'blue',
                            'purchasing'
                        );
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Gagal mengirim notifikasi update PO: ' . $e->getMessage());
            }

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
