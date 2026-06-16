<?php

namespace App\Controllers\purchasing;

use App\Controllers\BaseController;
use App\Models\PurchaseRequestModel;
use App\Models\PurchaseOrderModel;
use App\Models\SupplierModel;
use App\Models\MaterialModel;
use App\Models\MaterialSupplierModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $prModel = new PurchaseRequestModel();
        $poModel = new PurchaseOrderModel();
        $supplierModel = new SupplierModel();
        $materialModel = new MaterialModel();
        $materialSupplierModel = new MaterialSupplierModel();

        // PR Stats
        $prs = $prModel->findAll();
        $totalPr = count($prs);
        $prMenunggu = 0;
        $prDiproses = 0;
        $prParsial = 0;
        $prSelesai = 0;

        foreach ($prs as $pr) {
            $status = strtolower($pr['status']);
            if ($status == 'menunggu' || $status == 'pending') $prMenunggu++;
            elseif ($status == 'diproses') $prDiproses++;
            elseif ($status == 'parsial') $prParsial++;
            elseif ($status == 'selesai') $prSelesai++;
            else $prMenunggu++; // fallback
        }

        // PR Terbaru (Latest 5)
        $prTerbaru = $prModel->orderBy('created_at', 'DESC')->limit(5)->find();

        // PO Stats
        $pos = $poModel->findAll();
        $totalPo = count($pos);
        $poDiproses = 0;
        $poPengiriman = 0;
        $poSelesaiTiba = 0;

        foreach ($pos as $po) {
            $status = strtolower($po['status']);
            if ($status == 'diproses' || $status == 'proses') $poDiproses++;
            elseif ($status == 'pengiriman' || $status == 'dikirim') $poPengiriman++;
            elseif ($status == 'selesai_tiba' || $status == 'selesai tiba' || $status == 'selesai') $poSelesaiTiba++;
            else $poDiproses++; // fallback
        }

        // PO Values
        $currentMonth = date('m');
        $currentYear = date('Y');
        $lastMonth = date('m', strtotime('-1 month'));
        $lastMonthYear = date('Y', strtotime('-1 month'));

        $nilaiPoBulanIni = 0;
        $nilaiPoBulanLalu = 0;

        foreach ($pos as $po) {
            $poMonth = date('m', strtotime($po['created_at']));
            $poYear = date('Y', strtotime($po['created_at']));
            
            if ($poMonth == $currentMonth && $poYear == $currentYear) {
                $nilaiPoBulanIni += $po['total_nilai'];
            } elseif ($poMonth == $lastMonth && $poYear == $lastMonthYear) {
                $nilaiPoBulanLalu += $po['total_nilai'];
            }
        }

        $persentasePo = 0;
        if ($nilaiPoBulanLalu > 0) {
            $persentasePo = (($nilaiPoBulanIni - $nilaiPoBulanLalu) / $nilaiPoBulanLalu) * 100;
        } elseif ($nilaiPoBulanIni > 0) {
            $persentasePo = 100; // If last month was 0 but this month has value
        }

        // Master Data Stats
        $totalSupplier = $supplierModel->countAllResults();
        $totalMaterial = $materialModel->countAllResults();
        $totalHarga = $materialSupplierModel->countAllResults();

        $data = [
            'title' => 'Purchasing Dashboard - Kontraktor.id',
            'pr' => [
                'total' => $totalPr,
                'menunggu' => $prMenunggu,
                'diproses' => $prDiproses,
                'parsial' => $prParsial,
                'selesai' => $prSelesai,
                'terbaru' => $prTerbaru
            ],
            'po' => [
                'total' => $totalPo,
                'diproses' => $poDiproses,
                'pengiriman' => $poPengiriman,
                'selesai_tiba' => $poSelesaiTiba,
                'nilai_bulan_ini' => $nilaiPoBulanIni,
                'persentase_kenaikan' => round($persentasePo)
            ],
            'master_data' => [
                'supplier' => $totalSupplier,
                'material' => $totalMaterial,
                'harga' => $totalHarga
            ]
        ];

        return view('purchasing/dashboard', $data);
    }
}
