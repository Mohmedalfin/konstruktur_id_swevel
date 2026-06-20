<?php
define('FCPATH', __DIR__ . '/public/');
require 'public/index.php';
$db = \Config\Database::connect();
$rows = $db->table('master_barang')->like('nama_barang', 'Semen')->get()->getResultArray();
echo json_encode($rows, JSON_PRETTY_PRINT);
