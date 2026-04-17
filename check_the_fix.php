<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$q = "
    SELECT a.*, COALESCE(b.harga_dasar, u.harga_dasar, al.harga_dasar, 0) as master_harga_dasar
    FROM ahs a
    LEFT JOIN bahan_utama b ON a.id_kategori = b.id_bahan AND a.kategori = 'A'
    LEFT JOIN upah_utama u ON a.id_kategori = u.id_upah AND a.kategori = 'B'
    LEFT JOIN alat_utama al ON a.id_kategori = al.id_alat AND a.kategori = 'C'
    WHERE a.id_proyek = 1 AND a.id_pekerjaan IN ('V.4')
";
$res = $m->query($q);
if ($res) {
    while($row = $res->fetch_assoc()) { 
        echo $row['nama_pekerjaan'] . ' - ' . $row['nama_kategori'] . ' - ' . $row['master_harga_dasar'] . "\n";
    }
} else { echo $m->error; }
