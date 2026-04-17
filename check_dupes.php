<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

// Check duplicates in ahs_utama
$query = "SELECT COUNT(*) as c, id_pekerjaan FROM ahs_utama GROUP BY id_kategori, id_pekerjaan HAVING c > 1 LIMIT 5";
$res = $m->query($query);
echo "Duplicates in ahs_utama (same id_pekerjaan and id_kategori):\n";
while($row = $res->fetch_assoc()) { print_r($row); }
