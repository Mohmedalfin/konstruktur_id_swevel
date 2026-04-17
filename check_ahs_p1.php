<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "SELECT nama_kategori, koefisien, harga_dasar FROM ahs WHERE id_proyek = 1 AND id_pekerjaan = 'V.4'";
$res = $m->query($query);
echo "AHS P1:\n";
while($row = $res->fetch_assoc()) { print_r($row); }
