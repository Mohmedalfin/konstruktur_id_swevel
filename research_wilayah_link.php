<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Struktur Wilayah ===\n";
$r = $m->query("DESCRIBE wilayah");
while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Contoh Data Wilayah (Provinsi) ===\n";
$r = $m->query("SELECT * FROM wilayah WHERE kategori = 1 LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Contoh Data Wilayah (Kota) ===\n";
$r = $m->query("SELECT * FROM wilayah WHERE kategori = 2 LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Cek Link Wilayah ke Template ===\n";
// Cari apakah ada tabel yang menghubungkan wilayah ke template
$r = $m->query("SHOW TABLES LIKE '%template%'");
while ($row = $r->fetch_row()) {
    $table = $row[0];
    $cols = $m->query("DESCRIBE $table");
    $hasWilayah = false;
    while($c = $cols->fetch_assoc()) {
        if (stripos($c['Field'], 'wilayah') !== false || stripos($c['Field'], 'prov') !== false) {
            $hasWilayah = true;
            break;
        }
    }
    if ($hasWilayah) {
        echo "Tabel $table memiliki kolom wilayah/prov:\n";
        $cols = $m->query("DESCRIBE $table");
        while($c = $cols->fetch_assoc()) { echo "  {$c['Field']} ({$c['Type']})\n"; }
    }
}

echo "\n=== Cek Tabel template_proyek ===\n";
$r = $m->query("SELECT * FROM template_proyek LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Mencari id_template di tabel proyek estimator ===\n";
$r = $m->query("DESCRIBE proyek");
while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

$r = $m->query("SELECT id_proyek, nama_proyek, id_wilayah, id_template FROM proyek WHERE id_template IS NOT NULL LIMIT 5");
if ($r) {
    while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }
}
