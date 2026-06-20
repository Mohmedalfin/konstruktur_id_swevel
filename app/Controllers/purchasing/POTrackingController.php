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
            'title' => 'PO Tracking',
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
                } elseif ($newStatus === 'selesai tiba') {
                    $db = \Config\Database::connect();
                    $db->transStart();

                    $poItems = $this->poItemModel->where('po_id', $id)->findAll();
                    $prItemModel = new \App\Models\PurchaseRequestItemModel();
                    $prModel = new \App\Models\PurchaseRequestModel();
                    
                    $prIds = [];
                    foreach ($poItems as $item) {
                        $idBarang = $item['id_barang'];
                        $volume = (float)$item['volume'];
                        
                        // 1. Update stok_gudang
                        $stokGudang = $db->table('stok_gudang')
                            ->where('id_perusahaan', session()->get('id_perusahaan'))
                            ->where('id_barang', $idBarang)
                            ->get()->getRowArray();
                            
                        if ($stokGudang) {
                            $db->table('stok_gudang')
                               ->where('id', $stokGudang['id'])
                               ->set('stok_aktual', 'stok_aktual + ' . $volume, false)
                               ->set('updated_at', date('Y-m-d H:i:s'))
                               ->update();
                        } else {
                            $db->table('stok_gudang')->insert([
                                'id_perusahaan' => session()->get('id_perusahaan'),
                                'id_barang' => $idBarang,
                                'stok_aktual' => $volume,
                                'stok_minimum' => 0,
                                'harga_rata_rata' => $item['harga_satuan'],
                                'lokasi' => 'Gudang Utama',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }

                        // 2. Update PR items status
                        $prItemsToUpdate = $prItemModel->where('po_id', $id)->where('id_barang', $idBarang)->findAll();
                        foreach ($prItemsToUpdate as $prI) {
                            $prItemModel->update($prI['id'], ['status' => 'received']);
                            $prIds[] = $prI['pr_id'];
                        }
                    }

                    // 3. Update parent PR status if fully received
                    $prIds = array_unique($prIds);
                    foreach ($prIds as $prId) {
                        $totalItems = $prItemModel->where('pr_id', $prId)->countAllResults();
                        $receivedItems = $prItemModel->where('pr_id', $prId)->where('status', 'received')->countAllResults();
                        
                        if ($totalItems > 0 && $totalItems === $receivedItems) {
                            $prModel->update($prId, ['status' => 'completed']);
                        }
                    }

                    $db->transComplete();
                    
                    if ($db->transStatus() !== false) {
                        $notifService = new \App\Services\NotificationService();
                        $po = $this->poModel->find($id);
                        if ($po) {
                            $notifService->sendToRole(
                                'gudang',
                                'Barang Telah Tiba ✅',
                                "Pesanan PO {$po['po_number']} telah tiba. Stok gudang berhasil diperbarui.",
                                '/gudang/stok',
                                'fa-solid fa-box-open',
                                'green',
                                'gudang'
                            );
                        }
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
