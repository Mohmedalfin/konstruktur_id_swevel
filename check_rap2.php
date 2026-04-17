<?php
define('FCPATH', __DIR__ . '/public' . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

$db = \Config\Database::connect();
$rows = $db->query("SELECT id_rap_detail, pekerjaan, sumber FROM rap_detail ORDER BY id_rap_detail DESC LIMIT 5")->getResultArray();
echo "Rap Detail Rows:\n";
print_r($rows);

$items = $db->query("SELECT id_rap_detail_item, id_rap_detail, nama_item FROM rap_detail_item ORDER BY id_rap_detail_item DESC LIMIT 5")->getResultArray();
echo "Rap Detail Item Rows:\n";
print_r($items);
