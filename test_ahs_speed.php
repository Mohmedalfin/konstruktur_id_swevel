<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$t1 = microtime(true);
$res = $m->query("SELECT * FROM ahs WHERE id_proyek = 1 AND id_pekerjaan IN ('V.4', 'I.1')");
$t2 = microtime(true);
echo "Time: " . ($t2-$t1) . "s\n";
while($row = $res->fetch_assoc()) { 
    echo $row['nama_pekerjaan'] . " - " . $row['nama_kategori'] . "\n";
}
