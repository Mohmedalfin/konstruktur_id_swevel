<?php
// Fix ENUM values for project and rap_detail
$hostname = 'mysql-306fa75b-kontraktor-123.e.aivencloud.com';
$port = 14807;
$username = 'avnadmin';
$password = 'REDACTED';
$database = 'defaultdb';

try {
    $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Updating projects.sumber_data enum...\n";
    $pdo->exec("ALTER TABLE projects MODIFY COLUMN sumber_data ENUM('manual', 'estimator', 'boq') DEFAULT 'manual'");
    
    echo "Updating rap_detail.sumber enum...\n";
    $pdo->exec("ALTER TABLE rap_detail MODIFY COLUMN sumber ENUM('manual', 'estimator', 'boq') DEFAULT 'manual'");

    echo "Success!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
