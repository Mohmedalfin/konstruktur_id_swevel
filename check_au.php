<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query("SELECT id_pekerjaan, count(*) as c FROM ahs_utama GROUP BY id_pekerjaan LIMIT 5");
while($row = $res->fetch_assoc()) { print_r($row); }
