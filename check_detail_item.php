<?php
// Check if rap_detail_item exists
$hostname = 'mysql-306fa75b-kontraktor-123.e.aivencloud.com';
$port = 14807;
$username = 'avnadmin';
$password = 'REDACTED';
$database = 'defaultdb';

try {
    $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW TABLES LIKE 'rap_detail_item'");
    if ($stmt->rowCount() > 0) {
        echo "Table rap_detail_item exists.\n";
        $stmt = $pdo->query("DESCRIBE rap_detail_item");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo "Table rap_detail_item does NOT exist.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
