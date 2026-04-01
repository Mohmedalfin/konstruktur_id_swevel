<?php
$db = new PDO('mysql:host=localhost;dbname=kontraktor_alpha', 'root', '');
$stmt = $db->query('SHOW TABLES');
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
file_put_contents('tables.json', json_encode($tables));
echo "Done";
