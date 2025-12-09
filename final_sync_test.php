<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

echo "=== BOOKING #14 TRƯỚC SYNC ===\n";
$stmt = $conn->query('SELECT num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id = 14');
$before = $stmt->fetch();
print_r($before);

// Restore booking #13 status for testing
echo "\n=== KHÔI PHỤC BOOKING #13 ===\n";
$conn->query("UPDATE bookings SET status = 'Chờ xác nhận' WHERE booking_id = 13");
echo "Booking #13 status changed to 'Chờ xác nhận'\n";

// Now run sync
require_once 'admin/models/Booking.php';
$bookingModel = new Booking();
$result = $bookingModel->syncPricesBySchedule(22);
echo "\nSync result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

echo "\n=== SAU SYNC ===\n";
$stmt = $conn->query('SELECT booking_id, num_adults, num_children, num_infants, total_amount, contact_name FROM bookings WHERE booking_id IN (13, 14) ORDER BY booking_id');
while ($row = $stmt->fetch()) {
    echo "Booking #{$row['booking_id']}: adults={$row['num_adults']}, children={$row['num_children']}, infants={$row['num_infants']}, total={$row['total_amount']}, contact={$row['contact_name']}\n";
}

echo "\n=== DỮ LIỆU LỊCH #22 (KỲ VỌNG) ===\n";
$stmt = $conn->query('SELECT num_adults, num_children, num_infants, price_adult, price_child, customer_name FROM tour_schedules WHERE schedule_id = 22');
$schedule = $stmt->fetch();
$expectedTotal = ($schedule['num_adults'] * $schedule['price_adult']) + ($schedule['num_children'] * $schedule['price_child']) + ($schedule['num_infants'] * $schedule['price_child'] * 0.1);
echo "adults={$schedule['num_adults']}, children={$schedule['num_children']}, infants={$schedule['num_infants']}, total=$expectedTotal, contact={$schedule['customer_name']}\n";
