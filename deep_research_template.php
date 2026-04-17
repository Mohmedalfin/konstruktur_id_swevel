<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Cek v_wilayah ===\n";
$r = $m->query("DESCRIBE v_wilayah");
if ($r) while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Cek template_harga_satuan ===\n";
$r = $m->query("DESCRIBE template_harga_satuan");
if ($r) while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Cek template_pekerjaan ===\n";
$r = $m->query("DESCRIBE template_pekerjaan");
if ($r) while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Mencari teks 'Sleman' di seluruh tabel template_proyek ===\n";
$r = $m->query("SELECT * FROM template_proyek WHERE nama_proyek LIKE '%Sleman%' LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Mencari id_template 49 di tabel lain ===\n";
$r = $m->query("SHOW TABLES LIKE '%template%'");
while ($row = $r->fetch_row()) {
    $table = $row[0];
    $c = $m->query("SELECT COUNT(*) FROM $table WHERE id_template = 49");
    if ($c) {
        $count = $c->fetch_row()[0];
        if ($count > 0) echo "- Ada di $table ($count rows)\n";
    }
}
?>
