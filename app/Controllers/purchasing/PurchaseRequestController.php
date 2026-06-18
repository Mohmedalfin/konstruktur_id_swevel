<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\PurchaseRequestModel;
use App\Models\PurchaseRequestItemModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\MaterialSupplierModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PurchaseRequestController extends BaseController
{
    protected $prModel;
    protected $prItemModel;
    protected $poModel;
    protected $poItemModel;

    public function __construct()
    {
        $this->prModel = new PurchaseRequestModel();
        $this->prItemModel = new PurchaseRequestItemModel();
        $this->poModel = new PurchaseOrderModel();
        $this->poItemModel = new PurchaseOrderItemModel();
    }

    public function index()
    {
        $prs = $this->prModel->getPRsWithItemCount(session()->get('id_perusahaan'));
        
        $stats = [
            'total' => count($prs),
            'menunggu' => 0,
            'diproses' => 0,
            'parsial' => 0,
            'selesai' => 0
        ];
        
        foreach ($prs as $pr) {
            $status = strtolower($pr['status']);
            if ($status == 'pending' || $status == 'draft' || $status == 'menunggu') $stats['menunggu']++;
            elseif ($status == 'diproses' || $status == 'ordered') $stats['diproses']++;
            elseif ($status == 'parsial') $stats['parsial']++;
            elseif ($status == 'selesai') $stats['selesai']++;
            else $stats['menunggu']++;
        }

        $data = [
            'title' => 'Purchase Request - Kontraktor.id',
            'prs'   => $prs,
            'stats' => $stats
        ];

        $data['activeNav'] = 'purchase-request';
        return view('purchasing/purchase-request/index', $data);
    }

    public function getDetail($id)
    {
        $pr = $this->prModel->where('id_perusahaan', session()->get('id_perusahaan'))->find($id);
        if (!$pr) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'PR tidak ditemukan'])->setStatusCode(404);
        }

        $items = $this->prItemModel->getItemsByPR($id);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'pr' => $pr,
                'items' => $items
            ]
        ]);
    }

    public function getPendingItems($id)
    {
        $pr = $this->prModel->where('id_perusahaan', session()->get('id_perusahaan'))->find($id);
        if (!$pr) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'PR tidak ditemukan'])->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        
        // Get all pending items for this PR
        $items = $db->table('purchase_request_items')
            ->select('purchase_request_items.*, master_barang.nama_barang as nama_material, master_barang.satuan, master_barang.spesifikasi')
            ->join('master_barang', 'master_barang.id = purchase_request_items.id_barang')
            ->where('pr_id', $id)
            ->where('status', 'pending')
            ->get()
            ->getResultArray();

        // For each item, fetch available suppliers
        foreach ($items as &$item) {
            $suppliers = $db->table('material_supplier')
                ->select('material_supplier.harga, suppliers.id as supplier_id, suppliers.nama_supplier')
                ->join('suppliers', 'suppliers.id = material_supplier.supplier_id')
                ->where('id_barang', $item['id_barang'])
                ->get()
                ->getResultArray();
            $item['available_suppliers'] = $suppliers;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'pr' => $pr,
                'items' => $items
            ]
        ]);
    }

    public function generatePO()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Access Denied');
        }

        $json = $this->request->getJSON();
        $prId = $json->pr_id ?? null;
        $selections = $json->selections ?? [];

        if (!$prId || empty($selections)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Group selections by supplier
            $supplierGroups = [];
            foreach ($selections as $sel) {
                $supId = $sel->supplier_id;
                if (!isset($supplierGroups[$supId])) {
                    $supplierGroups[$supId] = [];
                }
                $supplierGroups[$supId][] = $sel;
            }

            $createdPOs = [];

            foreach ($supplierGroups as $supplierId => $items) {
                // Calculate total nilai
                $totalNilai = 0;
                foreach ($items as $item) {
                    $totalNilai += ($item->volume * $item->harga);
                }

                // Generate PO Number
                $poNumber = 'PO-' . date('Y-m') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

                // Create PO
                $poData = [
                    'id_perusahaan' => session()->get('id_perusahaan'),
                    'created_by' => session()->get('id_pengguna') ?? session()->get('id_user') ?? session()->get('id'),
                    'po_number' => $poNumber,
                    'supplier_id' => $supplierId,
                    'total_nilai' => $totalNilai,
                    'status' => 'diproses',
                    'created_at' => date('Y-m-d H:i:s'),
                    'estimasi_tanggal' => date('Y-m-d H:i:s', strtotime('+3 days')),
                ];
                $this->poModel->insert($poData);
                $poId = $this->poModel->getInsertID();

                // Get supplier name for response
                $supplier = $db->table('suppliers')->where('id', $supplierId)->get()->getRow();

                $poSummary = [
                    'po_number' => $poNumber,
                    'supplier_name' => $supplier->nama_supplier,
                    'items_desc' => []
                ];

                // Create PO Items & Update PR Items
                foreach ($items as $item) {
                    // Insert PO Item
                    $this->poItemModel->insert([
                        'po_id' => $poId,
                        'id_barang' => $item->material_id ?? $item->id_barang,
                        'volume' => $item->volume,
                        'harga_satuan' => $item->harga,
                        'sub_total' => $item->volume * $item->harga,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    // Update PR Item
                    $this->prItemModel->update($item->pr_item_id, [
                        'status' => 'ordered',
                        'po_id' => $poId
                    ]);

                    $mat = $db->table('master_barang')->where('id', $item->material_id ?? $item->id_barang)->get()->getRow();
                    $poSummary['items_desc'][] = $mat->nama_barang . ' ' . $mat->spesifikasi . ' ' . $item->volume . ' ' . $mat->satuan;
                }

                $poSummary['items_desc'] = implode(', ', $poSummary['items_desc']);
                $createdPOs[] = $poSummary;
            }

            // Check if all PR items are ordered
            $pendingCount = $db->table('purchase_request_items')
                               ->where('pr_id', $prId)
                               ->where('status', 'pending')
                               ->countAllResults();
            
            if ($pendingCount == 0) {
                $this->prModel->update($prId, ['status' => 'ordered']);
            } else {
                $this->prModel->update($prId, ['status' => 'parsial']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new DatabaseException();
            }

            try {
                $notifService = new \App\Services\NotificationService();
                foreach ($createdPOs as $poSum) {
                    $notifService->sendToRole(
                        'gudang',
                        'PO Baru Diterbitkan 📋',
                        "Purchasing telah menerbitkan {$poSum['po_number']} ke supplier {$poSum['supplier_name']}.",
                        '/gudang/pengadaan',
                        'fa-solid fa-file-invoice-dollar',
                        'purple',
                        'purchasing'
                    );
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Gagal mengirim notifikasi PO: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'PO berhasil dibuat',
                'created_pos' => $createdPOs
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membuat PO: ' . $e->getMessage()
            ]);
        }
    }
}
