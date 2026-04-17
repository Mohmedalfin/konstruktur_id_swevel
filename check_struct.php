<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

// Cek semua tabel di estimator
$r = $m->query("SHOW TABLES");
echo "=== SEMUA TABEL ===\n";
while ($row = $r->fetch_row()) { echo "  {$row[0]}\n"; }

// Cek struktur wilayah
$r2 = $m->query("DESCRIBE wilayah");
echo "\n=== wilayah columns ===\n";
while ($row = $r2->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

// Sample wilayah (untuk tahu nama kolom provinsi dll)
$r3 = $m->query("SELECT * FROM wilayah LIMIT 5");
echo "\n=== sample wilayah ===\n";
while ($row = $r3->fetch_assoc()) { echo "  ".json_encode($row)."\n"; }

// Cari tabel template
$r4 = $m->query("SHOW TABLES LIKE '%template%'");
echo "\n=== Tabel template ===\n";
while ($row = $r4->fetch_row()) { echo "  {$row[0]}\n"; }

// Cek template_proyek
$r5 = $m->query("DESCRIBE template_proyek");
echo "\n=== template_proyek columns ===\n";
if ($r5) while ($row = $r5->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

// Sample template_proyek
$r6 = $m->query("SELECT * FROM template_proyek LIMIT 10");
echo "\n=== sample template_proyek ===\n";
if ($r6) while ($row = $r6->fetch_assoc()) { echo "  ".json_encode($row)."\n"; }

// Cek bua_template_proyek structure
$r7 = $m->query("DESCRIBE bua_template_proyek");
echo "\n=== bua_template_proyek columns ===\n";
if ($r7) while ($row = $r7->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

// Count per id_template
$r8 = $m->query("SELECT id_template, COUNT(*) as c FROM bua_template_proyek GROUP BY id_template ORDER BY id_template LIMIT 20");
echo "\n=== count bua per id_template ===\n";
if ($r8) while ($row = $r8->fetch_assoc()) { echo "  id_template={$row['id_template']}: {$row['c']} bua\n"; }

// Cek ahs_utama
$r9 = $m->query("SELECT COUNT(DISTINCT id_pekerjaan) as pek, COUNT(*) as total FROM ahs_utama");
echo "\n=== ahs_utama stats ===\n";
if ($r9) { $row = $r9->fetch_assoc(); echo "  distinct id_pekerjaan={$row['pek']}, total rows={$row['total']}\n"; }

// Cek apakah id_pekerjaan di ahs_utama sama format dengan yg dikirim frontend
$r10 = $m->query("SELECT DISTINCT id_pekerjaan FROM ahs_utama LIMIT 5");
echo "\n=== sample id_pekerjaan di ahs_utama ===\n";
if ($r10) while ($row = $r10->fetch_assoc()) { echo "  {$row['id_pekerjaan']}\n"; }
