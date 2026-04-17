<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Harga Mandor di bua_bps untuk Sleman (3404) ===\n";
$r = $m->query("SELECT * FROM bua_bps WHERE id_wilayah = '3404' AND nama_kategori LIKE '%Mandor%' LIMIT 5");
while ($row = $r->fetch_assoc()) { echo json_encode($row) . "\n"; }

echo "\n=== Harga Mandor di bua_bps_utama untuk Sleman (3404) ===\n";
$r = $m->query("SELECT * FROM bua_bps_utama WHERE id_wilayah = '3404' AND nama_kategori LIKE '%Mandor%' LIMIT 5");
while ($row = $r->fetch_assoc()) { echo json_encode($row) . "\n"; }
?>
