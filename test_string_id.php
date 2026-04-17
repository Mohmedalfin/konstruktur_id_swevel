<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "SELECT count(*) as c FROM ahs_utama WHERE id_pekerjaan = 'I.1'";
$res = $m->query($query);
echo 'Ahs Utama rows for I.1: '. $res->fetch_assoc()['c'] . "\n";
