<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$res = $m->query("DESCRIBE v_rekap_ahs_pekerjaan");
if ($res) {
    while($row = $res->fetch_assoc()) { print_r($row['Field'] . "\n"); }
} else {
    echo $m->error;
}
