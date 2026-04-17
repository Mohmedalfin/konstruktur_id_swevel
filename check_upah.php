<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query("SELECT harga_dasar FROM upah_utama WHERE id_upah = '1647484672567'");
if($res && $row = $res->fetch_assoc()) { print_r($row); } else { echo "No upah found."; }
