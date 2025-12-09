<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== RESET BOOKING STATUS FOR TESTING ===" . PHP_EOL . PHP_EOL;

// Set all bookings to 'Giữ chỗ' (Hold status)
$conn->exec("UPDATE bookings SET status = 'Giữ chỗ'");
echo "✓ Reset all bookings to 'Giữ chỗ'" . PHP_EOL;

// Keep payment_status in sync with booking status (2-in-1)
$conn->exec("UPDATE bookings SET payment_status = status");
echo "✓ Synced payment_status = status" . PHP_EOL;

// Verify
$stmt = $conn->query("SELECT COUNT(*) as cnt, status FROM bookings GROUP BY status");
echo PHP_EOL . "Status distribution:" . PHP_EOL;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "  '" . $row['status'] . "': " . $row['cnt'] . " bookings" . PHP_EOL;
}

// Show sample data
echo PHP_EOL . "Sample bookings:" . PHP_EOL;
$stmt = $conn->query("SELECT booking_id, status, payment_status FROM bookings LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $b) {
    echo "  Booking #{$b['booking_id']}: status='{$b['status']}', payment_status='{$b['payment_status']}'" . PHP_EOL;
}

echo PHP_EOL . "✅ READY FOR TESTING!" . PHP_EOL;
?>