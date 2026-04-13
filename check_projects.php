<?php
// Check project sumber_data
$hostname = 'mysql-306fa75b-kontraktor-123.e.aivencloud.com';
$port = 14807;
$username = 'avnadmin';
$password = 'REDACTED';
$database = 'defaultdb';

try {
    $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id_project, nama_proyek, sumber_data FROM projects ORDER BY id_project DESC LIMIT 5");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($results);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
