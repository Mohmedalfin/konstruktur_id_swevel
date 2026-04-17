<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$q = "
    SELECT a.keterangan, a.nama_kategori, count(*) as count
    FROM ahs_utama a
    WHERE a.id_pekerjaan = 'V.4'
    GROUP BY a.keterangan, a.nama_kategori
";
$res = $m->query($q);
if ($res) {
    while($row = $res->fetch_assoc()) { 
        echo $row['keterangan'] . ' => ' . $row['nama_kategori'] . ' (' . $row['count'] . ")\n";
    }
} else { echo $m->error; }
