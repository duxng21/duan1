<?php
require_once 'commons/env.php';
require_once 'commons/function.php';

$conn = connectDB();
$stmt = $conn->query('DESCRIBE staff');

echo "=== CẤU TRÚC BẢNG STAFF ===\n";
while ($row = $stmt->fetch()) {
    echo $row['Field'] . "\n";
}
