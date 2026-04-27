<?php
$db = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

$res = $db->query("SELECT id_pekerjaan FROM pekerjaan_utama WHERE nama_pekerjaan LIKE '%Acian Beton%' LIMIT 1");
$row = $res->fetch_assoc();
$idP = $row['id_pekerjaan'] ?? 'V.4';

$sql = "SELECT id_ahs, k.nama_kategori as nama_kategori, a.koefisien, a.satuan_kategori as satuan_kategori, b.harga_dasar FROM ahs_utama a JOIN bahan_utama b ON a.id_kategori = b.id_bahan WHERE id_pekerjaan = '$idP' AND a.kategori='A'";
$res = $db->query($sql);

if (!$res) die($db->error);

echo "Total rows: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()) {
    echo "{$row['id_ahs']} | {$row['nama_kategori']} | {$row['koefisien']} | {$row['satuan_kategori']} | {$row['harga_dasar']}\n";
}
