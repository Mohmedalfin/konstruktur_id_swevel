<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');
$query = "SELECT * FROM v_rekap_template_ahs_pekerjaan WHERE id_pekerjaan = 'I.1' LIMIT 5";
$res = $m->query($query);
if ($res) {
    while($row = $res->fetch_assoc()) { print_r($row); }
} else {
    echo $m->error;
}
