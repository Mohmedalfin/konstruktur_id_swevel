<?php
$m = new mysqli('127.0.0.1', 'root', '', 'konstruktor_id_swevel');
$res = $m->query("SELECT id_rap_detail, pekerjaan, sumber FROM rap_detail ORDER BY id_rap_detail DESC LIMIT 5");
if ($res) {
    while($row = $res->fetch_assoc()) { print_r($row); }
} else {
    echo $m->error;
}
