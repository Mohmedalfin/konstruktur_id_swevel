<?php
$m = new mysqli('147.93.19.39', 'estimator_alpha', 'mK6si6wYNJypJrfZ', 'estimator_alpha');

echo "=== Struktur template_ahs ===\n";
$r = $m->query("DESCRIBE template_ahs");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }

echo "\n=== Data template_ahs id_template=49 ===\n";
$r = $m->query("SELECT * FROM template_ahs WHERE id_template = 49 LIMIT 5");
while ($row = $r->fetch_assoc()) { echo "  " . json_encode($row) . "\n"; }
?>
