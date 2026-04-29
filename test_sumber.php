<?php
require 'system/bootstrap.php';
$db = \Config\Database::connect();
$dbEst = \Config\Database::connect('estimator');

echo "--- Default DB ---\n";
$q = $db->query("SELECT DISTINCT keterangan FROM rap_detail_item WHERE keterangan IS NOT NULL LIMIT 20");
print_r($q->getResultArray());

echo "\n--- Estimator DB ---\n";
$q2 = $dbEst->query("SELECT DISTINCT keterangan FROM bahan_utama WHERE keterangan IS NOT NULL LIMIT 20");
print_r($q2->getResultArray());
