<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "
    SELECT a.*, 
           COALESCE(b.harga_dasar, u.harga_dasar, al.harga_dasar, 0) as harga_dasar
    FROM ahs_utama a
    LEFT JOIN bahan_utama b ON a.id_kategori = b.id_bahan AND a.kategori = 'A'
    LEFT JOIN upah_utama u ON a.id_kategori = u.id_upah AND a.kategori = 'B'
    LEFT JOIN alat_utama al ON a.id_kategori = al.id_alat AND a.kategori = 'C'
    WHERE a.id_pekerjaan = '2.1.(1)'
    LIMIT 3
";
$res = $m->query($query);
while($row = $res->fetch_assoc()) { print_r($row); }
