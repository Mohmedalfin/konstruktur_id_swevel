<?php
// Quick DB check script
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'konstruktor_id';

// Try to detect DB from CI4 config
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    if (preg_match('/database\.default\.hostname\s*=\s*(.+)/', $env, $m)) $host = trim($m[1]);
    if (preg_match('/database\.default\.username\s*=\s*(.+)/', $env, $m)) $user = trim($m[1]);
    if (preg_match('/database\.default\.password\s*=\s*(.+)/', $env, $m)) $pass = trim($m[1]);
    if (preg_match('/database\.default\.database\s*=\s*(.+)/', $env, $m)) $dbname = trim($m[1]);
}

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
$stmt = $pdo->query("SHOW COLUMNS FROM projects LIKE 'status_proyek'");
$col = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Column: " . $col['Field'] . "\n";
echo "Type:   " . $col['Type'] . "\n";
echo "Null:   " . $col['Null'] . "\n";
echo "Default:" . $col['Default'] . "\n";
