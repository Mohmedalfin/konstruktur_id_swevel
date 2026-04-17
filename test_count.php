<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query("SELECT count(*) as c FROM ahs WHERE id_pekerjaan = 3318");
echo 'Total: '. $res->fetch_assoc()['c'] . "\n";
$res = $m->query("SELECT count(*) as c FROM ahs WHERE id_pekerjaan = 3318 AND id_proyek = 1");
echo 'Proj1: '. $res->fetch_assoc()['c'] . "\n";
