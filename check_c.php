<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query("SELECT count(*) as c FROM ahs_utama");
echo $res->fetch_assoc()['c'];
