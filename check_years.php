<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Daftar Tahun Tersedia di bua_bps_utama per Wilayah ===\n";
$r = $m->query("SELECT id_wilayah, tahun, COUNT(*) as cnt FROM bua_bps_utama GROUP BY id_wilayah, tahun ORDER BY id_wilayah, tahun DESC LIMIT 20");
while ($row = $r->fetch_assoc()) { echo json_encode($row) . "\n"; }
?>
