<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "SELECT count(DISTINCT id_pekerjaan) as c1 FROM ahs WHERE id_proyek = 1";
$res1 = $m->query($query);
echo "AHS (proyek 1) distinct jobs: " . $res1->fetch_assoc()['c1'] . "\n";

$query = "SELECT count(DISTINCT id_pekerjaan) as c2 FROM ahs_utama";
$res2 = $m->query($query);
echo "AHS UTAMA distinct jobs: " . $res2->fetch_assoc()['c2'] . "\n";

$query = "SELECT count(DISTINCT id_pekerjaan) as c3 FROM ahs WHERE id_proyek = 1 AND id_pekerjaan NOT IN (SELECT id_pekerjaan FROM ahs_utama)";
$res3 = $m->query($query);
if ($res3) echo "In AHS P1 but NOT in AHS UTAMA: " . $res3->fetch_assoc()['c3'] . "\n";
else echo $m->error;
