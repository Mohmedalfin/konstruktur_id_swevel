<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Struktur bua_bps ===\n";
$r = $m->query("DESCRIBE bua_bps");
while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Contoh Data bua_bps ===\n";
$r = $m->query("SELECT * FROM bua_bps LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Struktur bua_bps_utama ===\n";
$r = $m->query("DESCRIBE bua_bps_utama");
while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

echo "\n=== Contoh Data bua_bps_utama ===\n";
$r = $m->query("SELECT * FROM bua_bps_utama LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }
