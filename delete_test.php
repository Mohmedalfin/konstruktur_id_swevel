<?php
require 'public/index.php';
$db = \Config\Database::connect();
$db->query("DELETE FROM rap_detail_item WHERE nama_item = 'Custom Bahan Test'");
echo "Test data deleted.";
