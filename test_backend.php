<?php
// Bootstrap CI4
define('FCPATH', __DIR__ . '/public' . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

$dbEstimator = \Config\Database::connect('estimator');
$masterIds = ["I.1"];
$ahsUtamaRows = $dbEstimator->table('ahs_utama')->whereIn('id_pekerjaan', $masterIds)->get()->getResultArray();
echo "Found rows: " . count($ahsUtamaRows) . "\n";
print_r($ahsUtamaRows);
