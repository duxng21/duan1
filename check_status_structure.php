<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== KIỂM TRA CẤU TRÚC BOOKINGS ===\n";
$stmt = $conn->query("DESCRIBE bookings");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
