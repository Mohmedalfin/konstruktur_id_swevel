<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class POSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Ensure Suppliers exist
        $suppliers = [
            ['nama_supplier' => 'Toko Makmur Jaya', 'alamat' => 'Jakarta', 'telepon' => '08123'],
            ['nama_supplier' => 'Tunas Baja Utama', 'alamat' => 'Bandung', 'telepon' => '08124'],
            ['nama_supplier' => 'Depo Bangunan', 'alamat' => 'Surabaya', 'telepon' => '08125'],
            ['nama_supplier' => 'Mitra Abadi Beton', 'alamat' => 'Bekasi', 'telepon' => '08126'],
            ['nama_supplier' => 'Sinar Bangunan', 'alamat' => 'Bogor', 'telepon' => '08127'],
            ['nama_supplier' => 'Baja Nusantara', 'alamat' => 'Tangerang', 'telepon' => '08128'],
        ];

        foreach ($suppliers as $s) {
            $existing = $db->table('suppliers')->where('nama_supplier', $s['nama_supplier'])->get()->getRow();
            if (!$existing) {
                $db->table('suppliers')->insert($s);
            }
        }

        // Ensure Material exists
        $materialData = [
            'nama_material' => 'Semen Portland (50 Kg)',
            'kategori'      => 'Struktur',
            'satuan'        => 'Sak',
            'spesifikasi'   => 'SNI'
        ];
        $materialExisting = $db->table('materials')->where('nama_material', 'Semen Portland (50 Kg)')->get()->getRow();
        if (!$materialExisting) {
            $db->table('materials')->insert($materialData);
            $materialId = $db->insertID();
        } else {
            $materialId = $materialExisting->id;
        }

        // Get suppliers mapped by name
        $suppMap = [];
        $allSuppliers = $db->table('suppliers')->get()->getResult();
        foreach ($allSuppliers as $sup) {
            $suppMap[$sup->nama_supplier] = $sup->id;
        }

        // Insert POs
        $pos = [
            [
                'po_number'   => 'PO-2026-05-001',
                'supplier_id' => $suppMap['Toko Makmur Jaya'] ?? 1,
                'total_nilai' => 5800000,
                'status'      => 'diproses',
                'created_at'  => '2026-05-25 10:00:00',
                'estimasi_tanggal' => '2026-05-27 10:00:00',
            ],
            [
                'po_number'   => 'PO-2026-05-002',
                'supplier_id' => $suppMap['Tunas Baja Utama'] ?? 2,
                'total_nilai' => 45000000,
                'status'      => 'diproses',
                'created_at'  => '2026-05-26 11:00:00',
                'estimasi_tanggal' => '2026-05-28 11:00:00',
            ],
            [
                'po_number'   => 'PO-2026-05-003',
                'supplier_id' => $suppMap['Depo Bangunan'] ?? 3,
                'total_nilai' => 840000,
                'status'      => 'diproses',
                'created_at'  => '2026-05-27 09:00:00',
                'estimasi_tanggal' => '2026-05-29 09:00:00',
            ],
            [
                'po_number'   => 'PO-2026-05-004',
                'supplier_id' => $suppMap['Mitra Abadi Beton'] ?? 4,
                'total_nilai' => 12500000,
                'status'      => 'diproses',
                'created_at'  => '2026-05-28 14:00:00',
                'estimasi_tanggal' => '2026-05-30 14:00:00',
            ],
            [
                'po_number'   => 'PO-2026-04-981',
                'supplier_id' => $suppMap['Sinar Bangunan'] ?? 5,
                'total_nilai' => 3200000,
                'status'      => 'dalam pengiriman',
                'created_at'  => '2026-04-20 08:00:00',
                'estimasi_tanggal' => '2026-04-22 08:00:00',
            ],
            [
                'po_number'   => 'PO-2026-04-982',
                'supplier_id' => $suppMap['Baja Nusantara'] ?? 6,
                'total_nilai' => 8300000,
                'status'      => 'selesai tiba',
                'created_at'  => '2026-04-21 15:00:00',
                'estimasi_tanggal' => '2026-04-23 15:00:00',
            ],
        ];

        // Ensure we don't insert duplicate POs
        foreach ($pos as $po) {
            $existingPo = $db->table('purchase_orders')->where('po_number', $po['po_number'])->get()->getRow();
            if (!$existingPo) {
                $db->table('purchase_orders')->insert($po);
                $poId = $db->insertID();

                // Insert Item
                $db->table('purchase_order_items')->insert([
                    'po_id' => $poId,
                    'material_id' => $materialId,
                    'volume' => 100,
                    'harga_satuan' => ($po['total_nilai'] / 100),
                    'sub_total' => $po['total_nilai'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
