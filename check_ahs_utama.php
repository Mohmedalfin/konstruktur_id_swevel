<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query('DESCRIBE ahs_utama');
while($r = $res->fetch_array()) echo $r[0]."\n";
