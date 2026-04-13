<?php
require 'vendor/autoload.php';
$db = \CodeIgniter\Database\Config::connect();
$query = $db->query("SHOW COLUMNS FROM rap_detail");
print_r($query->getResultArray());
