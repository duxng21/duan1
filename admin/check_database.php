<?php
// Script kiểm tra database và liệt kê bảng
require_once __DIR__ . '/../commons/env.php';
require_once __DIR__ . '/../commons/function.php';

try {
    $conn = connectDB();
    echo 'Database connection: OK' . PHP_EOL;
    echo 'Database: ' . DB_NAME . PHP_EOL;
    $tables = $conn->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
    echo 'Tables count: ' . count($tables) . PHP_EOL;
    echo 'Tables:' . PHP_EOL;
    foreach ($tables as $t) {
        $count = $conn->query("SELECT COUNT(*) FROM `{$t[0]}`")->fetchColumn();
        echo '  - ' . $t[0] . ' (' . $count . ' rows)' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
