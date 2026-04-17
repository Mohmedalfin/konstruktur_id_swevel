<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query("SELECT * FROM ahs_utama WHERE id_pekerjaan = 'I.1'");
while($row = $res->fetch_assoc()) { print_r($row); }
