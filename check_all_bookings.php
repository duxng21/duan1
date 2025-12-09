<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

$stmt = $conn->prepare('SELECT booking_id, tour_id, tour_date, status FROM bookings WHERE tour_id = 26 AND tour_date = "2025-12-08"');
$stmt->execute();
echo "=== TẤT CẢ BOOKING CHO TOUR 26 NGÀY 2025-12-08 ===\n";
while ($row = $stmt->fetch()) {
    echo "Booking #{$row['booking_id']}: status={$row['status']}\n";
}
