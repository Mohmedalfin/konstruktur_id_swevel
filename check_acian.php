<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

$query = "SELECT id_pekerjaan FROM pekerjaan_utama WHERE nama_pekerjaan LIKE '%Acian Beton%' LIMIT 1";
$res = $m->query($query);
$idP = $res->fetch_assoc()['id_pekerjaan'];
echo "Acian Beton ID: $idP\n\n";

$query = "SELECT * FROM ahs_utama WHERE id_pekerjaan = '$idP'";
$res = $m->query($query);
echo "AHS_UTAMA items:\n";
while($row = $res->fetch_assoc()) { 
    if ($row['nama_kategori'] == 'Semen portland') echo "SP: " . $row['sumber'] . "\n";
}

$query = "SELECT id_proyek, count(*) as c FROM ahs WHERE id_pekerjaan = '$idP' AND nama_kategori LIKE '%Semen portland%' GROUP BY id_proyek ORDER BY c DESC LIMIT 5";
$res = $m->query($query);
echo "\nAHS items per project:\n";
while($row = $res->fetch_assoc()) { 
    echo "P" . $row['id_proyek'] . ": " . $row['c'] . " Semen\n";
}

