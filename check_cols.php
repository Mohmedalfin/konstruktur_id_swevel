<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
// bua_template_proyek structure
$r = $m->query("DESCRIBE bua_template_proyek");
echo "=== bua_template_proyek ===\n";
while ($row = $r->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }
// sample bua
$r2 = $m->query("SELECT * FROM bua_template_proyek LIMIT 3");
echo "\n=== bua_template_proyek sample ===\n";
while ($row = $r2->fetch_assoc()) { echo "  ".json_encode($row)."\n"; }

// Check how template_proyek relates to wilayah
// Maybe via proyek_bua or proyek_wilayah?
$r3 = $m->query("SHOW TABLES LIKE '%wilayah%'");
echo "\n=== Tables with wilayah ===\n";
while ($row = $r3->fetch_row()) { echo "  {$row[0]}\n"; }

// Check template_proyek - does it link to tahun?
$r4 = $m->query("SELECT id_template, nama_proyek, tgl_dibuat FROM template_proyek LIMIT 10");
echo "\n=== template_proyek (with dates) ===\n";
while ($row = $r4->fetch_assoc()) { echo "  ".json_encode($row)."\n"; }

// Check bua_template_proyek - which id_template values exist?
$r5 = $m->query("SELECT DISTINCT id_template FROM bua_template_proyek LIMIT 10");
echo "\n=== id_template in bua_template_proyek ===\n";
while ($row = $r5->fetch_assoc()) { echo "  {$row['id_template']}\n"; }

// Check if bua_template_proyek has id_wilayah  
$r6 = $m->query("SELECT * FROM bua_template_proyek WHERE id_template=49 LIMIT 3");
echo "\n=== bua_template_proyek id_template=49 sample ===\n";
while ($row = $r6->fetch_assoc()) { echo "  ".json_encode($row)."\n"; }

// Check proyek (the project table) - does it have id_wilayah?
$r7 = $m->query("DESCRIBE proyek");
echo "\n=== proyek columns ===\n";
if ($r7) while ($row = $r7->fetch_assoc()) { echo "  {$row['Field']} ({$row['Type']})\n"; }

// sample proyek where id_template=49
$r8 = $m->query("SELECT * FROM proyek WHERE id_proyek=1");
echo "\n=== proyek id=1 ===\n";
while ($row = $r8->fetch_assoc()) { echo "  ".json_encode($row)."\n"; }
