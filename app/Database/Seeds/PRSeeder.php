<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PRSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Ensure Materials Exist
        $materials = [
            ['nama_material' => 'Semen Portland (50Kg)', 'kategori' => 'Struktur', 'satuan' => 'Sak', 'spesifikasi' => 'Standard'],
            ['nama_material' => 'Besi Beton Polos (8mm)', 'kategori' => 'Struktur', 'satuan' => 'Ton', 'spesifikasi' => 'SNI'],
            ['nama_material' => 'Pasir Pasang', 'kategori' => 'Struktur', 'satuan' => 'm3', 'spesifikasi' => 'Standard'],
            ['nama_material' => 'Bata Merah', 'kategori' => 'Arsitektur', 'satuan' => 'Pcs', 'spesifikasi' => 'Standard'],
            ['nama_material' => 'Paku Bangunan', 'kategori' => 'Struktur', 'satuan' => 'Kg', 'spesifikasi' => '3 inch'],
            ['nama_material' => 'Keramik Lantai 40x40', 'kategori' => 'Arsitektur', 'satuan' => 'Box', 'spesifikasi' => 'Standard'],
        ];

        $materialMap = [];
        foreach ($materials as $m) {
            $existing = $db->table('materials')->where('nama_material', $m['nama_material'])->get()->getRow();
            if (!$existing) {
                $db->table('materials')->insert($m);
                $materialMap[$m['nama_material']] = $db->insertID();
            } else {
                $materialMap[$m['nama_material']] = $existing->id;
            }
        }

        // 2. Ensure some prices in material_supplier
        // Get all suppliers
        $suppliers = $db->table('suppliers')->get()->getResult();
        if (count($suppliers) > 0) {
            $prices = [58000, 44000, 38000, 2800, 13000, 23500]; // Dummy prices
            $idx = 0;
            foreach ($materialMap as $name => $mId) {
                foreach ($suppliers as $sup) {
                    $existingPrice = $db->table('material_supplier')
                                        ->where(['material_id' => $mId, 'supplier_id' => $sup->id])
                                        ->get()->getRow();
                    if (!$existingPrice) {
                        $db->table('material_supplier')->insert([
                            'material_id' => $mId,
                            'supplier_id' => $sup->id,
                            'harga' => $prices[$idx % count($prices)] + rand(1000, 5000), // randomize slightly
                        ]);
                    }
                }
                $idx++;
            }
        }

        // 3. Create PRs
        $prs = [
            ['pr_number' => 'PR-2026-05-102', 'request_date' => '2026-05-10', 'status' => 'parsial'],
            ['pr_number' => 'PR-2026-05-103', 'request_date' => '2026-05-08', 'status' => 'diproses'],
            ['pr_number' => 'PR-2026-05-104', 'request_date' => '2026-05-06', 'status' => 'selesai'],
        ];

        foreach ($prs as $pr) {
            $existingPr = $db->table('purchase_requests')->where('pr_number', $pr['pr_number'])->get()->getRow();
            if (!$existingPr) {
                $db->table('purchase_requests')->insert($pr);
                $prId = $db->insertID();

                // Create items for PR-102
                if ($pr['pr_number'] == 'PR-2026-05-102') {
                    $items = [
                        ['material_id' => $materialMap['Semen Portland (50Kg)'], 'volume' => 100, 'status' => 'ordered', 'po_id' => 1],
                        ['material_id' => $materialMap['Besi Beton Polos (8mm)'], 'volume' => 5, 'status' => 'pending', 'po_id' => null],
                        ['material_id' => $materialMap['Pasir Pasang'], 'volume' => 20, 'status' => 'ordered', 'po_id' => 1],
                        ['material_id' => $materialMap['Bata Merah'], 'volume' => 10000, 'status' => 'ordered', 'po_id' => 1],
                        ['material_id' => $materialMap['Paku Bangunan'], 'volume' => 50, 'status' => 'pending', 'po_id' => null],
                        ['material_id' => $materialMap['Keramik Lantai 40x40'], 'volume' => 150, 'status' => 'pending', 'po_id' => null],
                    ];
                    
                    foreach ($items as $item) {
                        $item['pr_id'] = $prId;
                        $db->table('purchase_request_items')->insert($item);
                    }
                } else if ($pr['pr_number'] == 'PR-2026-05-103') {
                    // All pending
                    $db->table('purchase_request_items')->insert([
                        'pr_id' => $prId, 'material_id' => $materialMap['Besi Beton Polos (8mm)'], 'volume' => 10, 'status' => 'pending', 'po_id' => null
                    ]);
                } else {
                    // All ordered
                    $db->table('purchase_request_items')->insert([
                        'pr_id' => $prId, 'material_id' => $materialMap['Semen Portland (50Kg)'], 'volume' => 50, 'status' => 'ordered', 'po_id' => 2
                    ]);
                }
            }
        }
    }
}
