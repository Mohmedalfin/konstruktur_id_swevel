<?php
// Migration script for Aiven DB

$hostname = 'mysql-306fa75b-kontraktor-123.e.aivencloud.com';
$port = 14807;
$username = 'avnadmin';
$password = 'REDACTED'; // Use environment variable instead
$database = 'defaultdb';

try {
    $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if column exists first
    $stmt = $pdo->query("SHOW COLUMNS FROM rap_detail LIKE 'sumber'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE rap_detail ADD COLUMN sumber ENUM('manual', 'boq') DEFAULT 'manual' AFTER id_parent");
        echo "Successfully added 'sumber' column to 'rap_detail'.\n";
    } else {
        echo "Column 'sumber' already exists in 'rap_detail'.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
