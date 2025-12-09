<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== TEST HỦY BOOKING ===" . PHP_EOL . PHP_EOL;

// Check booking status before
$stmt = $conn->query("SELECT booking_id, status FROM bookings WHERE booking_id IN (1,2,3) LIMIT 3");
echo "Trước khi hủy:" . PHP_EOL;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $b) {
    echo "  Booking #{$b['booking_id']}: '{$b['status']}'" . PHP_EOL;
}

// Try to cancel booking #1
echo PHP_EOL . "Hủy booking #1..." . PHP_EOL;
$sql = "UPDATE bookings SET status = 'Đã hủy' WHERE booking_id = 1";
$result = $conn->exec($sql);
echo "UPDATE result: $result" . PHP_EOL;

// Check after
$stmt = $conn->query("SELECT booking_id, status FROM bookings WHERE booking_id = 1");
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Sau khi hủy:" . PHP_EOL;
echo "  Booking #{$booking['booking_id']}: '{$booking['status']}'" . PHP_EOL;

echo PHP_EOL . "✅ TEST HOÀN THÀNH!" . PHP_EOL;
?>