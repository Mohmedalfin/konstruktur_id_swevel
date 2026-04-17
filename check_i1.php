<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "SELECT count(*) as c FROM ahs WHERE id_pekerjaan = 'I.1' AND id_proyek = 1";
$res = $m->query($query);
echo 'Count I.1 in ahs with proyek=1: '. $res->fetch_assoc()['c'] . "\n";
