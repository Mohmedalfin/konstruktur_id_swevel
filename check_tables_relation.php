<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Kolom template_proyek ===\n";
$r = $m->query("DESCRIBE template_proyek");
while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Mencari tabel relasi wilayah-template ===\n";
$r = $m->query("SHOW TABLES LIKE '%wilayah%'");
while ($row = $r->fetch_row()) {
    $table = $row[0];
    echo "- $table\n";
}

$r = $m->query("SHOW TABLES LIKE '%template%'");
while ($row = $r->fetch_row()) {
    $table = $row[0];
    echo "- $table\n";
}

echo "\n=== Cek isi tabel wilayah ===\n";
$r = $m->query("SELECT * FROM wilayah LIMIT 10");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Cek tabel proyek (lagi) ===\n";
$r = $m->query("SELECT * FROM proyek LIMIT 2");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }
?>
