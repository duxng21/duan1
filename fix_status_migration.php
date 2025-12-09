<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== KIỂM TRA BOOKING #13 ===\n";
$stmt = $conn->prepare("SELECT booking_id, status, payment_status FROM bookings WHERE booking_id = 13");
$stmt->execute();
$booking = $stmt->fetch();
var_dump($booking);

echo "\n=== KIỂM TRA TẤT CẢ STATUS ===\n";
$stmt = $conn->query("SELECT DISTINCT status FROM bookings");
while ($row = $stmt->fetch()) {
    echo "- {$row['status']}\n";
}

// Manually update the problematic record
echo "\n=== FIX BOOKING #13 ===\n";
$stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = 13");
$stmt->execute(['Đã hủy']);
echo "Updated booking #13 to 'Đã hủy'\n";

// Now try the ALTER again
echo "\n=== RETRY ALTER ===\n";
try {
    $sql = "ALTER TABLE bookings MODIFY COLUMN status ENUM('Giữ chỗ','Đã đặt cọc','Đã thanh toán','Đã hủy','Đã hoàn thành')";
    $conn->exec($sql);
    echo "✓ ALTER thành công\n";
} catch (Exception $e) {
    echo "❌ Lỗi: {$e->getMessage()}\n";
}

echo "\n=== KIỂM TRA KẾT QUẢ CUỐI CÙNG ===\n";
$stmt = $conn->query("SHOW COLUMNS FROM bookings WHERE Field='status' OR Field='payment_status'");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . ": " . $row['Type'] . "\n";
}
