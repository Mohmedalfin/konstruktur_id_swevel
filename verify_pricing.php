<?php

// Load CodeIgniter environment
require 'vendor/autoload.php';
$app = require_once 'app/Config/Bootstrap.php';

$db = \Config\Database::connect();

// 1. Manually update Proyek ID 1 to Sleman 2021
$db->table('projects')->update([
    'id_wilayah' => '3404', 
    'id_template' => '2021',
    'lokasi_proyek' => 'Kab Sleman'
], ['id_project' => 1]);

echo "Updated Project 1 to Sleman 2021\n";

// 2. Fetch the project to verify
$p = $db->table('projects')->where('id_project', 1)->get()->getRowArray();
echo "Project Data: " . json_encode($p) . "\n";

// 3. Test price retrieval logic (Manual check of logic inside RapController)
$dbEstimator = \Config\Database::connect('estimator');
$idWilayah = $p['id_wilayah'];
$tahun = $p['id_template'];
$masterIdPekerjaan = 'A.2.2.1.1.'; // Pembuatan pagar sementara (contoh dari Turn 11)

$yearFilter = !empty($tahun) ? "AND btp.tahun = '{$tahun}'" : "";

$sql = "
    SELECT 
        au.nama_kategori,
        au.koefisien,
        COALESCE(btp.harga_dasar, 0) AS master_harga_dasar,
        (au.koefisien * COALESCE(btp.harga_dasar, 0)) AS subtotal
    FROM ahs_utama au
    LEFT JOIN bua_bps_utama btp 
        ON btp.id_wilayah = '{$idWilayah}'
        AND btp.id_kategori = au.id_kategori
        AND btp.kategori = au.kategori
        {$yearFilter}
        AND (btp.utama = '1' OR btp.utama IS NULL OR btp.utama = '')
    WHERE au.id_pekerjaan = ?
    GROUP BY au.id_ahs
";

$results = $dbEstimator->query($sql, [$masterIdPekerjaan])->getResultArray();

echo "\nResult for Pekerjaan A.2.2.1.1. in Sleman 2021:\n";
foreach ($results as $row) {
    echo "- {$row['nama_kategori']}: Koef={$row['koefisien']}, Price={$row['master_harga_dasar']}, Subtotal={$row['subtotal']}\n";
    if (stripos($row['nama_kategori'], 'Mandor') !== false) {
        if ((int)$row['master_harga_dasar'] == 130000) {
            echo "✅ Mandor price is correct (130k)\n";
        } else {
            echo "❌ Mandor price is INCORRECT: " . $row['master_harga_dasar'] . "\n";
        }
    }
}
