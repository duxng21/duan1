<?php
require_once 'commons/env.php';
require_once 'commons/function.php';

$conn = connectDB();

// Kiểm tra bảng guest_list
echo "=== GUEST_LIST ===\n";
$stmt = $conn->query('DESCRIBE guest_list');
while ($row = $stmt->fetch()) {
    if (stripos($row['Field'], 'check') !== false || stripos($row['Field'], 'status') !== false) {
        echo $row['Field'] . " | Type: " . $row['Type'] . " | Null: " . $row['Null'] . "\n";
    }
}

// Kiểm tra dữ liệu hiện tại
echo "\n=== DỮ LIỆU HIỆN TẠI ===\n";
$stmt2 = $conn->query('SELECT guest_id, full_name, check_in_status FROM guest_list LIMIT 3');
while ($row = $stmt2->fetch()) {
    echo "ID: " . $row['guest_id'] . " | Name: " . $row['full_name'] . " | Status: '" . $row['check_in_status'] . "' (Length: " . strlen($row['check_in_status']) . ")\n";
}
