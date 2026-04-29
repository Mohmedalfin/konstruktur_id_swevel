<?php
$pdoEstimator = new PDO('mysql:host=147.93.19.39;dbname=estimator_alpha', 'estimator_alpha', 'mK6si6wYNJypJrfZ');
$stmt = $pdoEstimator->query("SELECT * FROM upah_utama LIMIT 10");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
