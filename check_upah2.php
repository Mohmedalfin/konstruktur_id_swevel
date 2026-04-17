<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "SELECT * FROM upah_utama LIMIT 1";
$res = $m->query($query);
while($row = $res->fetch_assoc()) { print_r($row); }
