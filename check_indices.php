<?php
$mconn = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $mconn->query("SHOW INDEXES FROM ahs");
while($row = $res->fetch_assoc()) { print_r($row); }
