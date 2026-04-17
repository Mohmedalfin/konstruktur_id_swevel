<?php
$mconn = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $mconn->query("SELECT id_proyek, count(id_ahs) as c FROM ahs GROUP BY id_proyek LIMIT 10");
while($row = $res->fetch_assoc()) { print_r($row); }
