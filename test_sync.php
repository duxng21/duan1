<?php
require 'commons/env.php';
require 'commons/function.php';
$conn = connectDB();

// Update schedule price to test sync
$newAdultPrice = 16000000;
$newChildPrice = 8000000;

echo "=== UPDATING SCHEDULE #22 ===\n";
echo "Old prices: adult=15000000, child=7000000\n";
echo "New prices: adult=$newAdultPrice, child=$newChildPrice\n";

$stmt = $conn->prepare("UPDATE tour_schedules SET price_adult = ?, price_child = ? WHERE schedule_id = 22");
$stmt->execute([$newAdultPrice, $newChildPrice]);

echo "Updated. Now testing sync...\n\n";

// Now simulate the sync
require 'admin/models/Booking.php';
$bookingModel = new Booking();
$result = $bookingModel->syncPricesBySchedule(22);

echo "Sync result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

// Check updated booking
$stmt = $conn->prepare('SELECT booking_id, num_adults, num_children, num_infants, total_amount FROM bookings WHERE booking_id = 13');
$stmt->execute();
$booking = $stmt->fetch();
echo "=== BOOKING #13 AFTER SYNC ===\n";
var_dump($booking);

// Calculate expected
$expected = (15 * $newAdultPrice) + (10 * $newChildPrice) + (0 * $newChildPrice * 0.1);
echo "\n=== EXPECTED CALCULATION ===\n";
echo "15 × $newAdultPrice = " . (15 * $newAdultPrice) . "\n";
echo "10 × $newChildPrice = " . (10 * $newChildPrice) . "\n";
echo "Expected total: $expected\n";
echo "Current total: " . $booking['total_amount'] . "\n";
echo "Match: " . ($expected == $booking['total_amount'] ? 'YES' : 'NO') . "\n";
