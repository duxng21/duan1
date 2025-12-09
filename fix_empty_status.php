<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== FIX EMPTY STATUS VALUES ===\n\n";

// Set default status for empty values
$sql = "UPDATE bookings SET status = 'Giữ chỗ' WHERE status = '' OR status IS NULL";
$conn->exec($sql);
echo "✓ Set default status to 'Giữ chỗ' for empty records\n";

// Verify
$stmt = $conn->query("SELECT COUNT(booking_id) as total, COUNT(CASE WHEN status = '' THEN 1 END) as empty_count FROM bookings");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total bookings: " . $result['total'] . "\n";
echo "Empty status: " . $result['empty_count'] . "\n";

// Show samples
echo "\nSample bookings:\n";
$stmt = $conn->query("SELECT booking_id, status, payment_status FROM bookings LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $booking) {
    echo "  Booking #" . $booking['booking_id'] . ": status='" . $booking['status'] . "', payment_status='" . $booking['payment_status'] . "'\n";
}

echo "\n✅ FIXED!\n";
?>