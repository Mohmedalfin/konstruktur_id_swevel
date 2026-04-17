<?php
$mconn = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$t1 = microtime(true);
$res = $mconn->query("EXPLAIN SELECT * FROM ahs WHERE id_pekerjaan = '3318'");
while($row = $res->fetch_assoc()) { print_r($row); }
$t2 = microtime(true);
echo "Time: " . ($t2 - $t1) . " seconds\n";
