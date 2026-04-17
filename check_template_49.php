<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Data template_proyek untuk id_template = 49 ===\n";
$r = $m->query("SELECT * FROM template_proyek WHERE id_template = 49");
if ($row = $r->fetch_assoc()) { echo json_encode($row) . "\n"; } else { echo "Tidak ditemukan\n"; }

echo "\n=== Data v_wilayah (beberapa baris) ===\n";
$r = $m->query("SELECT * FROM v_wilayah LIMIT 10");
while ($row = $r->fetch_assoc()) { echo json_encode($row) . "\n"; }
?>
